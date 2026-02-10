<?php

namespace backend\controllers\v1;

use common\models\Admin;
use yii\rest\Controller;
use yii\filters\Cors;
use yii\web\UnauthorizedHttpException;
use yii\web\BadRequestHttpException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Yii;

class AdminAuthController extends Controller
{
    public $enableCsrfValidation = false;

    private function getJwtConfig($key)
    {
        return Yii::$app->params['jwt'][$key];
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        unset($behaviors['authenticator']);

        $behaviors['cors'] = [
            'class' => Cors::class,
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Allow-Credentials' => true,
            ],
        ];

        return $behaviors;
    }

    // POST /v1/admin-auth/login
    public function actionLogin()
    {
        $request = Yii::$app->request;
        $username = $request->post('username');
        $password = $request->post('password');

        if (empty($username) || empty($password)) {
            throw new BadRequestHttpException("Username va password kiritilishi shart");
        }

        $admin = Admin::find()
            ->where(['username' => $username, 'status' => Admin::STATUS_ACTIVE])
            ->with('role')
            ->one();

        if (!$admin || !$admin->validatePassword($password)) {
            throw new UnauthorizedHttpException("Login yoki parol noto'g'ri");
        }

        $tokens = $this->generateTokens($admin);

        return [
            'success' => true,
            'data' => [
                'admin' => $admin,
                'access_token' => $tokens['access_token'],
                'refresh_token' => $tokens['refresh_token'],
                'expires_in' => $this->getJwtConfig('accessTokenExpire'),
            ],
        ];
    }

    // POST /v1/admin-auth/refresh
    public function actionRefresh()
    {
        $refreshToken = Yii::$app->request->post('refresh_token');

        if (empty($refreshToken)) {
            throw new BadRequestHttpException("Refresh token kiritilishi shart");
        }

        try {
            $decoded = JWT::decode($refreshToken, new Key($this->getJwtConfig('secret'), $this->getJwtConfig('algorithm')));

            if ($decoded->type !== 'refresh') {
                throw new UnauthorizedHttpException("Token turi noto'g'ri");
            }

            $admin = Admin::find()
                ->where(['id' => $decoded->admin_id, 'status' => Admin::STATUS_ACTIVE])
                ->with('role')
                ->one();

            if (!$admin) {
                throw new UnauthorizedHttpException("Admin topilmadi");
            }

            $tokens = $this->generateTokens($admin);

            return [
                'success' => true,
                'data' => [
                    'access_token' => $tokens['access_token'],
                    'refresh_token' => $tokens['refresh_token'],
                    'expires_in' => $this->getJwtConfig('accessTokenExpire'),
                ],
            ];
        } catch (\Exception $e) {
            throw new UnauthorizedHttpException("Refresh token yaroqsiz");
        }
    }

    // GET /v1/admin-auth/me
    public function actionMe()
    {
        $admin = $this->getAuthenticatedAdmin();

        return [
            'success' => true,
            'data' => $admin,
        ];
    }

    // POST /v1/admin-auth/logout
    public function actionLogout()
    {
        // JWT stateless, server tomonda hech narsa qilish shart emas
        // Client tomonida tokenlarni o'chirish kerak
        return [
            'success' => true,
            'message' => 'Tizimdan chiqdingiz',
        ];
    }

    private function generateTokens(Admin $admin)
    {
        $now = time();
        $secret = $this->getJwtConfig('secret');
        $algorithm = $this->getJwtConfig('algorithm');

        // Access token
        $accessPayload = [
            'iss' => Yii::$app->request->hostInfo,
            'iat' => $now,
            'exp' => $now + $this->getJwtConfig('accessTokenExpire'),
            'type' => 'access',
            'admin_id' => $admin->id,
            'username' => $admin->username,
            'role_id' => $admin->role_id,
        ];

        // Refresh token
        $refreshPayload = [
            'iss' => Yii::$app->request->hostInfo,
            'iat' => $now,
            'exp' => $now + $this->getJwtConfig('refreshTokenExpire'),
            'type' => 'refresh',
            'admin_id' => $admin->id,
        ];

        return [
            'access_token' => JWT::encode($accessPayload, $secret, $algorithm),
            'refresh_token' => JWT::encode($refreshPayload, $secret, $algorithm),
        ];
    }

    public function getAuthenticatedAdmin()
    {
        $authHeader = Yii::$app->request->headers->get('Authorization');

        if (!$authHeader || !preg_match('/^Bearer\s+(.*)$/', $authHeader, $matches)) {
            throw new UnauthorizedHttpException("Token topilmadi");
        }

        $token = $matches[1];

        try {
            $decoded = JWT::decode($token, new Key($this->getJwtConfig('secret'), $this->getJwtConfig('algorithm')));

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
