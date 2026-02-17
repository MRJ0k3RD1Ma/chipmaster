<?php

namespace backend\controllers\v1;

use common\models\AdminRole;
use yii\web\NotFoundHttpException;
use Yii;

class AdminRoleController extends BaseController
{

    // GET /v1/admin-role
    public function actionIndex()
    {
        $request = Yii::$app->request;
        $perPage = (int)$request->get('per_page', 20);

        $query = AdminRole::find()->andWhere(['status'=>1]);

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

    // GET /v1/admin-role/{id}
    public function actionView($id)
    {
        return $this->findModel($id);
    }

    // POST /v1/admin-role
    public function actionCreate()
    {
        $model = new AdminRole();
        if($model->load(Yii::$app->request->post(), '')){
            if ($model->save()) {
                Yii::$app->response->statusCode = 201;
                return $model;
            }else{
                return [
                    'success' => false,
                    'errors' => $model->errors,
                ];
            }
        }else{
            Yii::$app->response->statusCode = 400;
            return [
                'success' => false,
            ];
        }

    }

    // PUT /v1/admin-role/{id}
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->load(Yii::$app->request->post(), '');

        if ($model->save()) {
            return $model;
        }

        return [
            'success' => false,
            'errors' => $model->errors,
        ];
    }

    // DELETE /v1/admin-role/{id}
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->status = 0;
        if ($model->save()) {
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
        $model = AdminRole::findOne($id);

        if ($model === null) {
            throw new NotFoundHttpException("Admin role topilmadi: $id");
        }

        return $model;
    }
}
