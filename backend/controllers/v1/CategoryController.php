<?php

namespace backend\controllers\v1;

use common\models\Category;
use yii\web\NotFoundHttpException;
use Yii;

class CategoryController extends BaseController
{
    // GET /v1/category
    public function actionIndex()
    {
        $request = Yii::$app->request;
        $perPage = (int)$request->get('per_page', 20);

        $query = Category::find()->andWhere(['status'=>1]);

        // Search by name
        if ($name = $request->get('name')) {
            $query->andFilterWhere(['like', 'name', $name]);
        }

        // Filter by parent_id
        if (($parentId = $request->get('parent_id')) !== null) {
            $query->andFilterWhere(['parent_id' => $parentId]);
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

    // PUT/POST /v1/category/{id}
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
