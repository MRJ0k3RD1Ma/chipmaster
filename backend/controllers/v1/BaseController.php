<?php

namespace backend\controllers\v1;

use common\models\Admin;
use yii\rest\Controller;
use yii\filters\Cors;
use yii\web\UnauthorizedHttpException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Yii;

class BaseController extends Controller
{
    public $enableCsrfValidation = false;

    protected $admin = null;

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        unset($behaviors['authenticator']);

        $behaviors['cors'] = [
            'class' => Cors::class,
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Allow-Credentials' => true,
            ],
        ];

        return $behaviors;
    }

    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        $this->admin = $this->authenticate();
        return true;
    }

    protected function authenticate()
    {
        $authHeader = Yii::$app->request->headers->get('Authorization');

        if (!$authHeader || !preg_match('/^Bearer\s+(.*)$/', $authHeader, $matches)) {
            throw new UnauthorizedHttpException("Token topilmadi");
        }

        $token = $matches[1];

        try {
            $jwt = Yii::$app->params['jwt'];
            $decoded = JWT::decode($token, new Key($jwt['secret'], $jwt['algorithm']));

            if ($decoded->type !== 'access') {
                throw new UnauthorizedHttpException("Token turi noto'g'ri");
            }

            $admin = Admin::find()
                ->where(['id' => $decoded->admin_id, 'status' => Admin::STATUS_ACTIVE])
                ->with('role')
                ->one();

            if (!$admin) {
                throw new UnauthorizedHttpException("Admin topilmadi");
            }

            return $admin;
        } catch (\Firebase\JWT\ExpiredException $e) {
            throw new UnauthorizedHttpException("Token muddati tugagan");
        } catch (\Exception $e) {
            throw new UnauthorizedHttpException("Token yaroqsiz");
        }
    }
}
