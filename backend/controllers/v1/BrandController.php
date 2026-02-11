<?php

namespace backend\controllers\v1;

use common\models\Brand;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use Yii;

class BrandController extends BaseController
{
    // GET /v1/brand
    public function actionIndex()
    {
        return Brand::find()->all();
    }

    // GET /v1/brand/{id}
    public function actionView($id)
    {
        return $this->findModel($id);
    }

    // POST /v1/brand
    public function actionCreate()
    {
        $model = new Brand();
        $model->load(Yii::$app->request->post(), '');
        $model->logoFile = UploadedFile::getInstanceByName('logoFile');

        if (!$model->validate()) {
            Yii::$app->response->statusCode = 400;
            return [
                'success' => false,
                'errors' => $model->errors,
            ];
        }

        if (!$model->uploadLogo()) {
            Yii::$app->response->statusCode = 400;
            return [
                'success' => false,
                'message' => "Rasm yuklab bo'lmadi",
            ];
        }

        if ($model->save(false)) {
            Yii::$app->response->statusCode = 201;
            return $model;
        }

        return [
            'success' => false,
            'errors' => $model->errors,
        ];
    }

    // PUT/POST /v1/brand/{id}
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->load(Yii::$app->request->post(), '');
        $model->logoFile = UploadedFile::getInstanceByName('logoFile');

        if (!$model->validate()) {
            Yii::$app->response->statusCode = 400;
            return [
                'success' => false,
                'errors' => $model->errors,
            ];
        }

        if (!$model->uploadLogo()) {
            Yii::$app->response->statusCode = 400;
            return [
                'success' => false,
                'message' => "Rasm yuklab bo'lmadi",
            ];
        }

        if ($model->save(false)) {
            return $model;
        }

        return [
            'success' => false,
            'errors' => $model->errors,
        ];
    }

    // DELETE /v1/brand/{id}
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->status = 0;
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
        $model = Brand::findOne($id);

        if ($model === null) {
            throw new NotFoundHttpException("Brand topilmadi: $id");
        }

        return $model;
    }
}
