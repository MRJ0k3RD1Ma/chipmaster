<?php

namespace backend\controllers\v1;

use common\models\Article;
use yii\web\NotFoundHttpException;
use Yii;

class ArticleController extends BaseController
{
    // GET /v1/article
    public function actionIndex()
    {
        $request = Yii::$app->request;
        $perPage = (int)$request->get('per_page', 20);

        $query = Article::find()->andWhere(['status' => 1]);

        // Filter by navigation_id
        if (($navigationId = $request->get('navigation_id')) !== null) {
            $query->andFilterWhere(['navigation_id' => $navigationId]);
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
                ['like', 'description_uz', $search],
                ['like', 'description_ru', $search],
            ]);
        }

        // Expand relations
        if ($expand = $request->get('expand')) {
            $query->with(explode(',', $expand));
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

    // GET /v1/article/{id}
    public function actionView($id)
    {
        return $this->findModel($id);
    }

    // POST /v1/article
    public function actionCreate()
    {
        $model = new Article();
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

    // PUT/POST /v1/article/{id}
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

    // DELETE /v1/article/{id}
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->status = Article::STATUS_INACTIVE;

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
        $model = Article::findOne($id);

        if ($model === null) {
            throw new NotFoundHttpException("Maqola topilmadi: $id");
        }

        return $model;
    }
}
