<?php

namespace backend\controllers\v1;

use common\models\ProductImage;
use yii\web\NotFoundHttpException;
use Yii;

class ProductImageController extends BaseController
{
    // GET /v1/product-image
    public function actionIndex()
    {
        $request = Yii::$app->request;
        $perPage = (int)$request->get('per_page', 20);

        $query = ProductImage::find();

        // Filter by product
        if ($productId = $request->get('product_id')) {
            $query->andFilterWhere(['product_id' => $productId]);
        }

        // Filter by status
        if (($status = $request->get('status')) !== null) {
            $query->andFilterWhere(['status' => $status]);
        }

        // Filter by is_primary
        if (($isPrimary = $request->get('is_primary')) !== null) {
            $query->andFilterWhere(['is_primary' => $isPrimary]);
        }

        // Expand relations
        if ($expand = $request->get('expand')) {
            $expandFields = array_map('trim', explode(',', $expand));
            $with = [];
            if (in_array('product', $expandFields)) {
                $with[] = 'product';
            }
            if (in_array('image', $expandFields)) {
                $with[] = 'image';
            }
            if (!empty($with)) {
                $query->with($with);
            }
        }

        $provider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['sort_order' => SORT_ASC],
                'attributes' => ['id', 'sort_order', 'is_primary', 'created'],
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

    // GET /v1/product-image/{id}
    public function actionView($id)
    {
        return $this->findModel($id);
    }

    // POST /v1/product-image
    public function actionCreate()
    {
        $model = new ProductImage();
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

    // PUT/POST /v1/product-image/{id}
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

    // PUT /v1/product-image/{id}/set-primary
    public function actionSetPrimary($id)
    {
        $model = $this->findModel($id);
        $model->is_primary = ProductImage::IS_PRIMARY_YES;

        if ($model->save()) {
            return $model;
        }

        Yii::$app->response->statusCode = 400;
        return [
            'success' => false,
            'errors' => $model->errors,
        ];
    }

    // DELETE /v1/product-image/{id}
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->status = ProductImage::STATUS_INACTIVE;

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
        $model = ProductImage::findOne($id);

        if ($model === null) {
            throw new NotFoundHttpException("Rasm topilmadi: $id");
        }

        return $model;
    }
}
