<?php

namespace backend\controllers\v1;

use common\models\ProductGuide;
use yii\web\NotFoundHttpException;
use Yii;

class ProductGuideController extends BaseController
{
    // GET /v1/product-guide
    public function actionIndex()
    {
        $request = Yii::$app->request;
        $perPage = (int)$request->get('per_page', 20);

        $query = ProductGuide::find();

        // Filter by product
        if ($productId = $request->get('product_id')) {
            $query->andFilterWhere(['product_id' => $productId]);
        }

        // Filter by status
        if (($status = $request->get('status')) !== null) {
            $query->andFilterWhere(['status' => $status]);
        }

        // Filter by has_video
        if (($hasVideo = $request->get('has_video')) !== null) {
            $query->andFilterWhere(['has_video' => $hasVideo]);
        }

        // Global search
        if ($search = $request->get('search')) {
            $query->andWhere([
                'or',
                ['like', 'title_uz', $search],
                ['like', 'title_ru', $search],
            ]);
        }

        // Expand relations
        if ($expand = $request->get('expand')) {
            $expandFields = array_map('trim', explode(',', $expand));
            $with = [];
            if (in_array('product', $expandFields)) {
                $with[] = 'product';
            }
            if (in_array('video', $expandFields)) {
                $with[] = 'video';
            }
            if (!empty($with)) {
                $query->with($with);
            }
        }

        $provider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['sort_order' => SORT_ASC],
                'attributes' => ['id', 'title_uz', 'sort_order', 'created'],
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

    // GET /v1/product-guide/{id}
    public function actionView($id)
    {
        return $this->findModel($id);
    }

    // POST /v1/product-guide
    public function actionCreate()
    {
        $model = new ProductGuide();
        $model->load(Yii::$app->request->post(), '');

        if ($model->save()) {
            Yii::$app->response->statusCode = 201;
            return $model;
        }

        Yii::$app->response->statusCode = 400;
        return [
            'success' => false,
            'errors' => $model->errors,
        ];
    }

    // PUT/POST /v1/product-guide/{id}
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->load(Yii::$app->request->post(), '');

        if ($model->save()) {
            return $model;
        }

        Yii::$app->response->statusCode = 400;
        return [
            'success' => false,
            'errors' => $model->errors,
        ];
    }

    // DELETE /v1/product-guide/{id}
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->status = ProductGuide::STATUS_INACTIVE;

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
        $model = ProductGuide::findOne($id);

        if ($model === null) {
            throw new NotFoundHttpException("Qo'llanma topilmadi: $id");
        }

        return $model;
    }
}
