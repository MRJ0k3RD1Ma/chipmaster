<?php

namespace backend\controllers\v1;

use common\models\Admin;
use yii\web\NotFoundHttpException;
use Yii;

class AdminController extends BaseController
{

    // GET /v1/admin
    public function actionIndex()
    {
        $request = Yii::$app->request;
        $perPage = (int)$request->get('per_page', 20);

        $query = Admin::find()->with('role');

        // Search by name
        if ($name = $request->get('name')) {
            $query->andFilterWhere(['like', 'name', $name]);
        }

        // Search by username
        if ($username = $request->get('username')) {
            $query->andFilterWhere(['like', 'username', $username]);
        }

        // Search by phone
        if ($phone = $request->get('phone')) {
            $query->andFilterWhere(['like', 'phone', $phone]);
        }

        // Filter by role_id
        if ($roleId = $request->get('role_id')) {
            $query->andFilterWhere(['role_id' => $roleId]);
        }

        // Filter by status
        if (($status = $request->get('status')) !== null) {
            $query->andFilterWhere(['status' => $status]);
        }

        // Global search
        if ($search = $request->get('search')) {
            $query->andWhere([
                'or',
                ['like', 'name', $search],
                ['like', 'username', $search],
                ['like', 'phone', $search],
            ]);
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
                'has_next' => $currentPage < $totalPages ,
                'has_prev' => $currentPage > 1,
            ],
        ];
    }

    // GET /v1/admin/{id}
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $model->populateRelation('role', $model->role);
        return $model;
    }

    // POST /v1/admin
    public function actionCreate()
    {
        $model = new Admin();
        $data = Yii::$app->request->post();

        if ($model->load($data, '')) {
            // Parolni hash qilish
            if (!empty($data['password'])) {
                $model->setPassword($data['password']);
            }

            if ($model->save()) {
                Yii::$app->response->statusCode = 201;
                return $model;
            } else {
                return [
                    'success' => false,
                    'errors' => $model->errors,
                ];
            }
        } else {
            Yii::$app->response->statusCode = 400;
            return [
                'success' => false,
                'message' => "Ma'lumot yuklanmadi",
            ];
        }
    }

    // PUT /v1/admin/{id}
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $data = Yii::$app->request->post();

        if ($model->load($data, '')) {
            // Parol yangilansa hash qilish
            if (!empty($data['password'])) {
                $model->setPassword($data['password']);
            }

            if ($model->save()) {
                return $model;
            } else {
                return [
                    'success' => false,
                    'errors' => $model->errors,
                ];
            }
        } else {
            Yii::$app->response->statusCode = 400;
            return [
                'success' => false,
                'message' => "Ma'lumot yuklanmadi",
            ];
        }
    }

    // DELETE /v1/admin/{id}
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->delete()) {
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
        $model = Admin::findOne($id);

        if ($model === null) {
            throw new NotFoundHttpException("Admin topilmadi: $id");
        }

        return $model;
    }
}
