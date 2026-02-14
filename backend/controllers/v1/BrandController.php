<?php

namespace backend\controllers\v1;

use common\models\Brand;
use yii\web\NotFoundHttpException;
use Yii;

class BrandController extends BaseController
{
    // GET /v1/brand
    public function actionIndex()
    {
        $request = Yii::$app->request;
        $perPage = (int)$request->get('per_page', 20);

        $query = Brand::find()->with('logo');

        // Search by name
        if ($name = $request->get('name')) {
            $query->andFilterWhere(['like', 'name', $name]);
        }

        // Filter by status
        if (($status = $request->get('status')) !== null) {
            $query->andFilterWhere(['status' => $status]);
        }

        // Global search
        if ($search = $request->get('search')) {
            $query->andWhere(['like', 'name', $search]);
        }

        $provider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
            ],
            'pagination' => [
                'pageSize' => $perPage,
                'pageParam' => 'page',
                'pageSizeParam' => 'per_page',
            ],
        ]);

        $pagination = $provider->pagination;
        $totalItems = $provider->totalCount;
        $totalPages = ceil($totalItems / $perPage);
        $currentPage = $pagination->page + 1;

        return [
            'data' => $provider->getModels(),
            'pagination' => [
                'current_page' => $currentPage,
                'per_page' => $perPage,
                'total_items' => $totalItems,
                'total_pages' => $totalPages,
                'has_next' => $currentPage < $totalPages,
                'has_prev' => $currentPage > 1,
            ],
        ];
    }

    // GET /v1/brand/{id}
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $model->populateRelation('logo', $model->logo);
        return $model;
    }

    // POST /v1/brand
    public function actionCreate()
    {
        $model = new Brand();
        $model->load(Yii::$app->request->post(), '');

        if ($model->save()) {
            Yii::$app->response->statusCode = 201;
            $model->populateRelation('logo', $model->logo);
            return $model;
        }

        Yii::$app->response->statusCode = 400;
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

        if ($model->save()) {
            $model->populateRelation('logo', $model->logo);
            return $model;
        }

        Yii::$app->response->statusCode = 400;
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
