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
        return AdminRole::find()->all();
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
        $model = AdminRole::findOne($id);

        if ($model === null) {
            throw new NotFoundHttpException("Admin role topilmadi: $id");
        }

        return $model;
    }
}
