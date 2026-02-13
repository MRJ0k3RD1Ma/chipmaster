<?php

namespace backend\controllers\v1;

use common\models\Category;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use Yii;

class CategoryController extends BaseController
{
    // GET /v1/category
    public function actionIndex()
    {
        return Category::find()
            ->where(['status' => Category::STATUS_ACTIVE])
            ->orderBy(['sort_order' => SORT_ASC])
            ->all();
    }

    // GET /v1/category/tree - daraxt ko'rinishda
    public function actionTree()
    {
        $categories = Category::find()
            ->where(['status' => Category::STATUS_ACTIVE])
            ->orderBy(['sort_order' => SORT_ASC])
            ->asArray()
            ->all();

        return $this->buildTree($categories);
    }

    private function buildTree(array $categories, $parentId = null)
    {
        $tree = [];
        foreach ($categories as $category) {
            if ($category['parent_id'] == $parentId) {
                $children = $this->buildTree($categories, $category['id']);
                if ($children) {
                    $category['children'] = $children;
                }
                $tree[] = $category;
            }
        }
        return $tree;
    }

    // GET /v1/category/{id}
    public function actionView($id)
    {
        return $this->findModel($id);
    }

    // POST /v1/category
    public function actionCreate()
    {
        $model = new Category();
        $model->load(Yii::$app->request->post(), '');
        $model->imageFile = UploadedFile::getInstanceByName('imageFile');

        if (!$model->validate()) {
            Yii::$app->response->statusCode = 400;
            return [
                'success' => false,
                'errors' => $model->errors,
            ];
        }

        if (!$model->uploadImage()) {
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

    // PUT/POST /v1/category/{id}
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->load(Yii::$app->request->post(), '');
        $model->imageFile = UploadedFile::getInstanceByName('imageFile');

        if (!$model->validate()) {
            Yii::$app->response->statusCode = 400;
            return [
                'success' => false,
                'errors' => $model->errors,
            ];
        }

        if (!$model->uploadImage()) {
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

    // DELETE /v1/category/{id}
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->status = Category::STATUS_INACTIVE;

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
        $model = Category::findOne($id);

        if ($model === null) {
            throw new NotFoundHttpException("Kategoriya topilmadi: $id");
        }

        return $model;
    }
}
