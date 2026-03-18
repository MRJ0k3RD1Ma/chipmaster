<?php

namespace backend\controllers\v1;

use common\models\Navigation;
use yii\web\NotFoundHttpException;
use Yii;

class NavigationController extends BaseController
{
    // GET /v1/navigation
    public function actionIndex()
    {
        $request = Yii::$app->request;
        $perPage = (int)$request->get('per_page', 20);

        $query = Navigation::find()->andWhere(['status' => 1]);

        // Search by name
        if ($name = $request->get('name')) {
            $query->andFilterWhere(['like', 'name_uz', $name]);
        }

        // Filter by parent_id
        if (($parentId = $request->get('parent_id')) !== null) {
            $query->andFilterWhere(['parent_id' => $parentId]);
        }

        // Filter by template
        if ($template = $request->get('template')) {
            $query->andFilterWhere(['template' => $template]);
        }

        // Filter by status
        if (($status = $request->get('status')) !== null) {
            $query->andFilterWhere(['status' => $status]);
        }

        // Global search
        if ($search = $request->get('search')) {
            $query->andWhere([
                'or',
                ['like', 'name_uz', $search],
                ['like', 'name_ru', $search],
            ]);
        }

        $provider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['sort_order' => SORT_ASC, 'id' => SORT_DESC],
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

    // GET /v1/navigation/tree - daraxt ko'rinishda
    public function actionTree()
    {
        $navigations = Navigation::find()
            ->where(['status' => Navigation::STATUS_ACTIVE])
            ->orderBy(['sort_order' => SORT_ASC])
            ->asArray()
            ->all();

        return $this->buildTree($navigations);
    }

    private function buildTree(array $navigations, $parentId = null)
    {
        $tree = [];
        foreach ($navigations as $navigation) {
            if ($navigation['parent_id'] == $parentId) {
                $children = $this->buildTree($navigations, $navigation['id']);
                if ($children) {
                    $navigation['children'] = $children;
                }
                $tree[] = $navigation;
            }
        }
        return $tree;
    }

    // GET /v1/navigation/{id}
    public function actionView($id)
    {
        return $this->findModel($id);
    }

    // POST /v1/navigation
    public function actionCreate()
    {
        $model = new Navigation();
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

    // PUT/POST /v1/navigation/{id}
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

    // DELETE /v1/navigation/{id}
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->status = Navigation::STATUS_INACTIVE;

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
        $model = Navigation::findOne($id);

        if ($model === null) {
            throw new NotFoundHttpException("Navigation topilmadi: $id");
        }

        return $model;
    }
}
