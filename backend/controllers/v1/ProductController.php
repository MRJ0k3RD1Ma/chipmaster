<?php

namespace backend\controllers\v1;

use common\models\Product;
use yii\web\NotFoundHttpException;
use Yii;

class ProductController extends BaseController
{
    // GET /v1/product
    public function actionIndex()
    {
        $request = Yii::$app->request;
        $perPage = (int)$request->get('per_page', 20);

        $query = Product::find();

        // Filter by category
        if ($categoryId = $request->get('category_id')) {
            $query->andFilterWhere(['category_id' => $categoryId]);
        }

        // Filter by brand
        if ($brandId = $request->get('brand_id')) {
            $query->andFilterWhere(['brand_id' => $brandId]);
        }

        // Filter by status
        if (($status = $request->get('status')) !== null) {
            $query->andFilterWhere(['status' => $status]);
        }

        // Filter by featured
        if (($featured = $request->get('featured')) !== null) {
            $query->andFilterWhere(['featured' => $featured]);
        }

        // Filter by is_device
        if (($isDevice = $request->get('is_device')) !== null) {
            $query->andFilterWhere(['is_device' => $isDevice]);
        }

        // Filter by price range
        if ($minPrice = $request->get('min_price')) {
            $query->andWhere(['>=', 'price', (int)$minPrice]);
        }
        if ($maxPrice = $request->get('max_price')) {
            $query->andWhere(['<=', 'price', (int)$maxPrice]);
        }

        // Filter in stock
        if ($request->get('in_stock')) {
            $query->andWhere(['>', 'stock_quantity', 0]);
        }

        // Global search
        if ($search = $request->get('search')) {
            $query->andWhere([
                'or',
                ['like', 'name_uz', $search],
                ['like', 'name_ru', $search],
                ['like', 'sku', $search],
            ]);
        }

        // Expand relations
        if ($expand = $request->get('expand')) {
            $expandFields = array_map('trim', explode(',', $expand));
            $with = [];
            if (in_array('category', $expandFields)) {
                $with[] = 'category';
            }
            if (in_array('brand', $expandFields)) {
                $with[] = 'brand';
            }
            if (in_array('image', $expandFields)) {
                $with[] = 'image';
            }
            if (in_array('images', $expandFields)) {
                $with[] = 'images';
            }
            if (in_array('guides', $expandFields)) {
                $with[] = 'guides';
            }
            if (!empty($with)) {
                $query->with($with);
            }
        }

        $provider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
                'attributes' => ['id', 'name_uz', 'price', 'stock_quantity', 'created'],
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

    // GET /v1/product/{id}
    public function actionView($id)
    {
        return $this->findModel($id);
    }

    // POST /v1/product
    public function actionCreate()
    {
        $model = new Product();
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

    // PUT/POST /v1/product/{id}
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

    // DELETE /v1/product/{id}
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->status = Product::STATUS_INACTIVE;

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
        $model = Product::findOne($id);

        if ($model === null) {
            throw new NotFoundHttpException("Mahsulot topilmadi: $id");
        }

        return $model;
    }
}
