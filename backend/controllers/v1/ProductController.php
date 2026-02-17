<?php

namespace backend\controllers\v1;

use common\models\Product;
use common\models\ProductGuide;
use common\models\ProductImage;
use common\models\ProductSoft;
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
        $with = ['brand','category','image'];

        if ($expand = $request->get('expand')) {

            $expandFields = array_map('trim', explode(',', $expand));

            if (in_array('images', $expandFields)) {
                $with[] = 'images';
            }
            if (in_array('guides', $expandFields)) {
                $with[] = 'guides';
            }
            if (in_array('softs', $expandFields)) {
                $with[] = 'softs';
            }
        }
        $query->with($with);

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


        $model = Product::find()
            ->where(['id' => $id])
            ->with(['brand', 'category', 'image', 'images', 'guides', 'softs'])
            ->one();

        if ($model === null) {
            throw new NotFoundHttpException("Mahsulot topilmadi: $id");
        }

        return $model->toArray([], ['images', 'guides', 'softs']);
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

    // POST /v1/product/create-fully
    public function actionCreateFully()
    {
        $request = Yii::$app->request->getBodyParams();

      /*  // DEBUG: Requestni ko'rish
        return [
            'debug' => true,
            'request_keys' => array_keys($request),
            'has_guides' => isset($request['guides']),
            'has_images' => isset($request['images']),
            'has_softs' => isset($request['softs']),
            'guides_count' => count($request['guides'] ?? []),
            'images_count' => count($request['images'] ?? []),
            'softs_count' => count($request['softs'] ?? []),
            'raw_request' => $request,
        ];*/

        $transaction = Yii::$app->db->beginTransaction();

        try {
            // Product yaratish
            $product = new Product();
            $product->load($request, '');

            if (!$product->save()) {
                throw new \yii\base\UserException(json_encode($product->errors));
            }

            // Guides yaratish
            $guides = $request['guides'] ?? [];
            $savedGuides = [];
            foreach ($guides as $index => $guideData) {
                $guide = new ProductGuide();
                $guide->load($guideData, '');
                $guide->product_id = $product->id;

                if (!$guide->save()) {
                    throw new \yii\base\UserException(json_encode([
                        'guides' => [$index => $guide->errors]
                    ]));
                }
                $savedGuides[] = $guide;
            }

            // Images yaratish
            $images = $request['images'] ?? [];
            $savedImages = [];
            foreach ($images as $index => $imageData) {
                $image = new ProductImage();
                $image->load($imageData, '');
                $image->product_id = $product->id;

                if (!$image->save()) {
                    throw new \yii\base\UserException(json_encode([
                        'images' => [$index => $image->errors]
                    ]));
                }
                $savedImages[] = $image;
            }

            // Softs yaratish
            $softs = $request['softs'] ?? [];
            $savedSofts = [];
            foreach ($softs as $index => $softData) {
                $soft = new ProductSoft();
                $soft->load($softData, '');
                $soft->product_id = $product->id;

                if (!$soft->save()) {
                    throw new \yii\base\UserException(json_encode([
                        'softs' => [$index => $soft->errors]
                    ]));
                }
                $savedSofts[] = $soft;
            }

            $transaction->commit();

            Yii::$app->response->statusCode = 201;
            $product->refresh();

            return [
                'product' => $product,
                'guides' => $savedGuides,
                'images' => $savedImages,
                'softs' => $savedSofts,
            ];

        } catch (\yii\base\UserException $e) {
            $transaction->rollBack();
            Yii::$app->response->statusCode = 400;
            return [
                'success' => false,
                'errors' => json_decode($e->getMessage(), true),
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->response->statusCode = 500;
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    // PUT /v1/product/{id}/update-fully
    public function actionUpdateFully($id)
    {
        $product = $this->findModel($id);
        $request = Yii::$app->request->getBodyParams();
        $transaction = Yii::$app->db->beginTransaction();

        try {
            // Product yangilash
            $product->load($request, '');

            if (!$product->save()) {
                throw new \yii\base\UserException(json_encode($product->errors));
            }

            // Guides yangilash
            if (isset($request['guides'])) {
                $guides = $request['guides'];
                $existingGuideIds = [];

                foreach ($guides as $index => $guideData) {
                    if (!empty($guideData['id'])) {
                        // Mavjud guide ni yangilash
                        $guide = ProductGuide::findOne([
                            'id' => $guideData['id'],
                            'product_id' => $product->id
                        ]);

                        if (!$guide) {
                            throw new \yii\base\UserException(json_encode([
                                'guides' => [$index => ["Qo'llanma topilmadi: {$guideData['id']}"]]
                            ]));
                        }

                        $guide->load($guideData, '');
                    } else {
                        // Yangi guide yaratish
                        $guide = new ProductGuide();
                        $guide->load($guideData, '');
                        $guide->product_id = $product->id;
                    }

                    if (!$guide->save()) {
                        throw new \yii\base\UserException(json_encode([
                            'guides' => [$index => $guide->errors]
                        ]));
                    }

                    $existingGuideIds[] = $guide->id;
                }

                // Request da kelmaganlarni o'chirish (soft delete)
                ProductGuide::updateAll(
                    ['status' => ProductGuide::STATUS_INACTIVE],
                    ['and',
                        ['product_id' => $product->id],
                        ['not in', 'id', $existingGuideIds],
                        ['status' => ProductGuide::STATUS_ACTIVE]
                    ]
                );
            }

            // Images yangilash
            if (isset($request['images'])) {
                $images = $request['images'];
                $existingImageIds = [];

                foreach ($images as $index => $imageData) {
                    if (!empty($imageData['id'])) {
                        // Mavjud image ni yangilash
                        $image = ProductImage::findOne([
                            'id' => $imageData['id'],
                            'product_id' => $product->id
                        ]);

                        if (!$image) {
                            throw new \yii\base\UserException(json_encode([
                                'images' => [$index => ["Rasm topilmadi: {$imageData['id']}"]]
                            ]));
                        }

                        $image->load($imageData, '');
                    } else {
                        // Yangi image yaratish
                        $image = new ProductImage();
                        $image->load($imageData, '');
                        $image->product_id = $product->id;
                    }

                    if (!$image->save()) {
                        throw new \yii\base\UserException(json_encode([
                            'images' => [$index => $image->errors]
                        ]));
                    }

                    $existingImageIds[] = $image->id;
                }

                // Request da kelmaganlarni o'chirish (soft delete)
                ProductImage::updateAll(
                    ['status' => ProductImage::STATUS_INACTIVE],
                    ['and',
                        ['product_id' => $product->id],
                        ['not in', 'id', $existingImageIds],
                        ['status' => ProductImage::STATUS_ACTIVE]
                    ]
                );
            }

            // Softs yangilash
            if (isset($request['softs'])) {
                $softs = $request['softs'];
                $existingSoftIds = [];

                foreach ($softs as $index => $softData) {
                    if (!empty($softData['id'])) {
                        // Mavjud soft ni yangilash
                        $soft = ProductSoft::findOne([
                            'id' => $softData['id'],
                            'product_id' => $product->id
                        ]);

                        if (!$soft) {
                            throw new \yii\base\UserException(json_encode([
                                'softs' => [$index => ["Dastur topilmadi: {$softData['id']}"]]
                            ]));
                        }

                        $soft->load($softData, '');
                    } else {
                        // Yangi soft yaratish
                        $soft = new ProductSoft();
                        $soft->load($softData, '');
                        $soft->product_id = $product->id;
                    }

                    if (!$soft->save()) {
                        throw new \yii\base\UserException(json_encode([
                            'softs' => [$index => $soft->errors]
                        ]));
                    }

                    $existingSoftIds[] = $soft->id;
                }

                // Request da kelmaganlarni o'chirish (soft delete)
                ProductSoft::updateAll(
                    ['status' => ProductSoft::STATUS_INACTIVE],
                    ['and',
                        ['product_id' => $product->id],
                        ['not in', 'id', $existingSoftIds],
                        ['status' => ProductSoft::STATUS_ACTIVE]
                    ]
                );
            }

            $transaction->commit();

            $product->refresh();

            return [
                'product' => $product,
                'guides' => $product->getGuides()->where(['status' => ProductGuide::STATUS_ACTIVE])->all(),
                'images' => $product->getImages()->where(['status' => ProductImage::STATUS_ACTIVE])->all(),
                'softs' => $product->getSofts()->where(['status' => ProductSoft::STATUS_ACTIVE])->all(),
            ];

        } catch (\yii\base\UserException $e) {
            $transaction->rollBack();
            Yii::$app->response->statusCode = 400;
            return [
                'success' => false,
                'errors' => json_decode($e->getMessage(), true),
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->response->statusCode = 500;
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    protected function findModel($id)
    {
        $model = Product::findOne($id);

        if ($model === null) {
            throw new NotFoundHttpException("Mahsulot topilmadi: $id");
        }

        return $model;
    }

    // ==================== PRODUCT GUIDE ====================

    // GET /v1/product/{product_id}/guides
    public function actionGuides($product_id)
    {
        $this->findModel($product_id);

        $request = Yii::$app->request;
        $perPage = (int)$request->get('per_page', 20);

        $query = ProductGuide::find()->where(['product_id' => $product_id]);

        if (($status = $request->get('status')) !== null) {
            $query->andFilterWhere(['status' => $status]);
        }

        if (($hasVideo = $request->get('has_video')) !== null) {
            $query->andFilterWhere(['has_video' => $hasVideo]);
        }

        if ($search = $request->get('search')) {
            $query->andWhere([
                'or',
                ['like', 'title_uz', $search],
                ['like', 'title_ru', $search],
            ]);
        }

        if ($expand = $request->get('expand')) {
            $expandFields = array_map('trim', explode(',', $expand));
            $with = [];
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

    // GET /v1/product/{product_id}/guides/{id}
    public function actionGuideView($product_id, $id)
    {
        $this->findModel($product_id);
        return $this->findGuide($id, $product_id);
    }

    // POST /v1/product/{product_id}/guides
    public function actionGuideCreate($product_id)
    {
        $this->findModel($product_id);

        $model = new ProductGuide();
        $model->load(Yii::$app->request->post(), '');
        $model->product_id = $product_id;

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

    // PUT /v1/product/{product_id}/guides/{id}
    public function actionGuideUpdate($product_id, $id)
    {
        $this->findModel($product_id);
        $model = $this->findGuide($id, $product_id);
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

    // DELETE /v1/product/{product_id}/guides/{id}
    public function actionGuideDelete($product_id, $id)
    {
        $this->findModel($product_id);
        $model = $this->findGuide($id, $product_id);
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

    protected function findGuide($id, $product_id)
    {
        $model = ProductGuide::findOne(['id' => $id, 'product_id' => $product_id]);

        if ($model === null) {
            throw new NotFoundHttpException("Qo'llanma topilmadi: $id");
        }

        return $model;
    }

    // ==================== PRODUCT IMAGE ====================

    // GET /v1/product/{product_id}/images
    public function actionImages($product_id)
    {
        $this->findModel($product_id);

        $request = Yii::$app->request;
        $perPage = (int)$request->get('per_page', 20);

        $query = ProductImage::find()->where(['product_id' => $product_id]);

        if (($status = $request->get('status')) !== null) {
            $query->andFilterWhere(['status' => $status]);
        }

        if (($isPrimary = $request->get('is_primary')) !== null) {
            $query->andFilterWhere(['is_primary' => $isPrimary]);
        }

        if ($expand = $request->get('expand')) {
            $expandFields = array_map('trim', explode(',', $expand));
            $with = [];
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

    // GET /v1/product/{product_id}/images/{id}
    public function actionImageView($product_id, $id)
    {
        $this->findModel($product_id);
        return $this->findImage($id, $product_id);
    }

    // POST /v1/product/{product_id}/images
    public function actionImageCreate($product_id)
    {
        $this->findModel($product_id);

        $model = new ProductImage();
        $model->load(Yii::$app->request->post(), '');
        $model->product_id = $product_id;

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

    // PUT /v1/product/{product_id}/images/{id}
    public function actionImageUpdate($product_id, $id)
    {
        $this->findModel($product_id);
        $model = $this->findImage($id, $product_id);
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

    // PUT /v1/product/{product_id}/images/{id}/set-primary
    public function actionImageSetPrimary($product_id, $id)
    {
        $this->findModel($product_id);
        $model = $this->findImage($id, $product_id);
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

    // DELETE /v1/product/{product_id}/images/{id}
    public function actionImageDelete($product_id, $id)
    {
        $this->findModel($product_id);
        $model = $this->findImage($id, $product_id);
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

    protected function findImage($id, $product_id)
    {
        $model = ProductImage::findOne(['id' => $id, 'product_id' => $product_id]);

        if ($model === null) {
            throw new NotFoundHttpException("Rasm topilmadi: $id");
        }

        return $model;
    }

    // ==================== PRODUCT SOFT ====================

    // GET /v1/product/{product_id}/softs
    public function actionSofts($product_id)
    {
        $this->findModel($product_id);

        $request = Yii::$app->request;
        $perPage = (int)$request->get('per_page', 20);

        $query = ProductSoft::find()->where(['product_id' => $product_id]);

        if (($status = $request->get('status')) !== null) {
            $query->andFilterWhere(['status' => $status]);
        }

        if ($search = $request->get('search')) {
            $query->andWhere(['like', 'name', $search]);
        }

        if ($expand = $request->get('expand')) {
            $expandFields = array_map('trim', explode(',', $expand));
            $with = [];
            if (in_array('file', $expandFields)) {
                $with[] = 'file';
            }
            if (!empty($with)) {
                $query->with($with);
            }
        }

        $provider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
                'attributes' => ['id', 'name', 'created'],
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

    // GET /v1/product/{product_id}/softs/{id}
    public function actionSoftView($product_id, $id)
    {
        $this->findModel($product_id);
        return $this->findSoft($id, $product_id);
    }

    // POST /v1/product/{product_id}/softs
    public function actionSoftCreate($product_id)
    {
        $this->findModel($product_id);

        $model = new ProductSoft();
        $model->load(Yii::$app->request->post(), '');
        $model->product_id = $product_id;

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

    // PUT /v1/product/{product_id}/softs/{id}
    public function actionSoftUpdate($product_id, $id)
    {
        $this->findModel($product_id);
        $model = $this->findSoft($id, $product_id);
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

    // DELETE /v1/product/{product_id}/softs/{id}
    public function actionSoftDelete($product_id, $id)
    {
        $this->findModel($product_id);
        $model = $this->findSoft($id, $product_id);
        $model->status = ProductSoft::STATUS_INACTIVE;

        if ($model->save(false)) {
            Yii::$app->response->statusCode = 204;
            return null;
        }

        return [
            'success' => false,
            'message' => "O'chirib bo'lmadi",
        ];
    }

    protected function findSoft($id, $product_id)
    {
        $model = ProductSoft::findOne(['id' => $id, 'product_id' => $product_id]);

        if ($model === null) {
            throw new NotFoundHttpException("Dastur topilmadi: $id");
        }

        return $model;
    }
}
