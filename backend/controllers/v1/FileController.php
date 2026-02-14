<?php

namespace backend\controllers\v1;

use common\models\File;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use Yii;

class FileController extends BaseController
{
    private $uploadPath;
    private $tempPath;

    // Rasm o'lchamlari
    private $imageSizes = [
        'small' => 150,   // 150px width
        'medium' => 500,  // 500px width
    ];

    // Rasm formatlar
    private $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    public function init()
    {
        parent::init();
        $this->uploadPath = Yii::getAlias('@frontend/web/upload/files');
        $this->tempPath = Yii::getAlias('@frontend/web/upload/temp');

        if (!is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0755, true);
        }
        if (!is_dir($this->tempPath)) {
            mkdir($this->tempPath, 0755, true);
        }
    }

    // GET /v1/file
    public function actionIndex()
    {
        return File::find()
            ->where(['status' => File::STATUS_ACTIVE])
            ->orderBy(['id' => SORT_DESC])
            ->all();
    }

    // GET /v1/file/{id}
    public function actionView($id)
    {
        return $this->findModel($id);
    }

    // POST /v1/file/upload - Chunked upload
    public function actionUpload()
    {
        $request = Yii::$app->request;

        // Chunk ma'lumotlari
        $chunkIndex = (int) $request->post('chunkIndex', 0);
        $totalChunks = (int) $request->post('totalChunks', 1);
        $fileId = $request->post('fileId');
        $fileName = $request->post('fileName');

        if (!$fileId || !$fileName) {
            Yii::$app->response->statusCode = 400;
            return [
                'success' => false,
                'message' => "fileId va fileName majburiy",
                'received' => [
                    'fileId' => $fileId,
                    'fileName' => $fileName,
                    'chunkIndex' => $chunkIndex,
                    'totalChunks' => $totalChunks,
                    'post' => $request->post(),
                    'files' => array_keys($_FILES),
                ],
            ];
        }

        // Chunk faylni olish
        $chunk = $_FILES['chunk'] ?? null;
        if (!$chunk) {
            Yii::$app->response->statusCode = 400;
            return [
                'success' => false,
                'message' => "Chunk fayl topilmadi",
                'files' => $_FILES,
            ];
        }

        if ($chunk['error'] !== UPLOAD_ERR_OK) {
            Yii::$app->response->statusCode = 400;
            return [
                'success' => false,
                'message' => "Chunk yuklashda xato: " . $chunk['error'],
                'error_code' => $chunk['error'],
            ];
        }

        // Temporary papkada chunk saqlash
        $chunkPath = $this->tempPath . '/' . $fileId;
        if (!is_dir($chunkPath)) {
            mkdir($chunkPath, 0755, true);
        }

        $chunkFile = $chunkPath . '/' . $chunkIndex;
        if (!move_uploaded_file($chunk['tmp_name'], $chunkFile)) {
            throw new BadRequestHttpException("Chunk saqlab bo'lmadi");
        }

        // Hamma chunklar kelganmi tekshirish
        $uploadedChunks = count(glob($chunkPath . '/*'));

        if ($uploadedChunks < $totalChunks) {
            return [
                'success' => true,
                'message' => "Chunk $chunkIndex qabul qilindi",
                'uploadedChunks' => $uploadedChunks,
                'totalChunks' => $totalChunks,
            ];
        }

        // Barcha chunklar kelgan - faylni birlashtirish
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $baseName = pathinfo($fileName, PATHINFO_FILENAME);
        $uniqueName = time() . '_' . Yii::$app->security->generateRandomString(8);
        $newFileName = $uniqueName . '.' . $extension;
        $finalPath = $this->uploadPath . '/' . $newFileName;

        $finalFile = fopen($finalPath, 'wb');
        if (!$finalFile) {
            throw new BadRequestHttpException("Fayl yaratib bo'lmadi");
        }

        for ($i = 0; $i < $totalChunks; $i++) {
            $chunkFilePath = $chunkPath . '/' . $i;
            if (!file_exists($chunkFilePath)) {
                fclose($finalFile);
                throw new BadRequestHttpException("Chunk $i topilmadi");
            }
            $chunkContent = file_get_contents($chunkFilePath);
            fwrite($finalFile, $chunkContent);
        }

        fclose($finalFile);

        // Temp chunklarni tozalash
        $this->cleanupChunks($chunkPath);

        // URL larni tayyorlash
        $urls = [];

        // Agar rasm bo'lsa, resize qilish
        if ($this->isImage($extension)) {
            $urls = $this->processImage($finalPath, $uniqueName, $extension);
        } else {
            // Oddiy fayl
            $urls = ['original' => '/upload/files/' . $newFileName];
        }

        // Bazaga yozish
        $file = new File();
        $file->name = $baseName;
        $file->exts = $extension;
        $file->url = json_encode($urls, JSON_UNESCAPED_SLASHES);

        if (!$file->save()) {
            // Fayllarni o'chirish
            $this->deleteFiles($urls);
            Yii::$app->response->statusCode = 400;
            return [
                'success' => false,
                'errors' => $file->errors,
            ];
        }

        Yii::$app->response->statusCode = 201;
        return [
            'success' => true,
            'id' => $file->id,
            'url' => $file->getUrls(),
        ];
    }

    // POST /v1/file/cancel - Yuklashni bekor qilish va temp fayllarni tozalash
    public function actionCancel()
    {
        $fileId = Yii::$app->request->post('fileId');

        if (!$fileId) {
            Yii::$app->response->statusCode = 400;
            return [
                'success' => false,
                'message' => "fileId majburiy",
            ];
        }

        $chunkPath = $this->tempPath . '/' . $fileId;

        if (is_dir($chunkPath)) {
            $this->cleanupChunks($chunkPath);
            return [
                'success' => true,
                'message' => "Temp fayllar tozalandi",
            ];
        }

        return [
            'success' => true,
            'message' => "Temp papka topilmadi (allaqachon tozalangan)",
        ];
    }

    // DELETE /v1/file/{id}
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->status = File::STATUS_INACTIVE;

        if ($model->save(false)) {
            Yii::$app->response->statusCode = 204;
            return null;
        }

        return [
            'success' => false,
            'message' => "O'chirib bo'lmadi",
        ];
    }

    protected function findModel($id)
    {
        $model = File::findOne($id);

        if ($model === null) {
            throw new NotFoundHttpException("Fayl topilmadi: $id");
        }

        return $model;
    }

    private function cleanupChunks($chunkPath)
    {
        $files = glob($chunkPath . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        rmdir($chunkPath);
    }

    private function isImage($extension)
    {
        return in_array($extension, $this->imageExtensions);
    }

    private function processImage($originalPath, $uniqueName, $extension)
    {
        $urls = [];

        // Original rasmni yuklash
        $sourceImage = $this->createImageFromFile($originalPath, $extension);
        if (!$sourceImage) {
            return ['original' => '/upload/files/' . $uniqueName . '.' . $extension];
        }

        $originalWidth = imagesx($sourceImage);
        $originalHeight = imagesy($sourceImage);

        // Original URL
        $urls['original'] = '/upload/files/' . $uniqueName . '.' . $extension;

        // Small va Medium versiyalarini yaratish
        foreach ($this->imageSizes as $sizeName => $maxWidth) {
            // Agar original kichikroq bo'lsa, resize qilmaslik
            if ($originalWidth <= $maxWidth) {
                $urls[$sizeName] = $urls['original'];
                continue;
            }

            $newWidth = $maxWidth;
            $newHeight = (int) ($originalHeight * ($maxWidth / $originalWidth));

            $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

            // PNG va GIF uchun transparency saqlash
            if ($extension === 'png' || $extension === 'gif') {
                imagealphablending($resizedImage, false);
                imagesavealpha($resizedImage, true);
                $transparent = imagecolorallocatealpha($resizedImage, 0, 0, 0, 127);
                imagefill($resizedImage, 0, 0, $transparent);
            }

            // Resize qilish
            imagecopyresampled(
                $resizedImage,
                $sourceImage,
                0, 0, 0, 0,
                $newWidth, $newHeight,
                $originalWidth, $originalHeight
            );

            // Yangi fayl nomini yaratish
            $newFileName = $uniqueName . '_' . $sizeName . '.' . $extension;
            $newPath = $this->uploadPath . '/' . $newFileName;

            // Faylga saqlash
            $this->saveImage($resizedImage, $newPath, $extension);
            imagedestroy($resizedImage);

            $urls[$sizeName] = '/upload/files/' . $newFileName;
        }

        imagedestroy($sourceImage);

        return $urls;
    }

    private function createImageFromFile($path, $extension)
    {
        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                return imagecreatefromjpeg($path);
            case 'png':
                return imagecreatefrompng($path);
            case 'gif':
                return imagecreatefromgif($path);
            case 'webp':
                return imagecreatefromwebp($path);
            default:
                return null;
        }
    }

    private function saveImage($image, $path, $extension, $quality = 85)
    {
        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                imagejpeg($image, $path, $quality);
                break;
            case 'png':
                imagepng($image, $path, 9);
                break;
            case 'gif':
                imagegif($image, $path);
                break;
            case 'webp':
                imagewebp($image, $path, $quality);
                break;
        }
    }

    private function deleteFiles($urls)
    {
        $basePath = Yii::getAlias('@frontend/web');
        foreach ($urls as $url) {
            $filePath = $basePath . $url;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
    }
}
