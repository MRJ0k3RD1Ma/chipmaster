<?php

namespace backend\controllers;

use common\models\File;
use common\models\LoginForm;
use Yii;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\rest\Controller;
use yii\web\Response;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // CORS
        $behaviors['cors'] = [
            'class' => \yii\filters\Cors::class,
        ];

        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'actions' => ['login', 'error', 'options', 'file'],
                    'allow' => true,
                ],
                [
                    'actions' => ['logout', 'index'],
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ];

        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'logout' => ['post'],
            ],
        ];

        return $behaviors;
    }

    // OPTIONS preflight handler
    public function actionOptions()
    {
        Yii::$app->response->statusCode = 204;
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => \yii\web\ErrorAction::class,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return string|Response
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $this->layout = 'blank';

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Faylni yuklab olish yoki ko'rish
     *
     * @param string $slug Fayl slug
     * @param string $mode inline|download
     * @param string $size sm|md|lg (faqat rasmlar uchun)
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionFile($slug, $mode = 'inline', $size = 'lg')
    {
        $file = File::find()
            ->where(['slug' => $slug, 'status' => File::STATUS_ACTIVE])
            ->one();

        if (!$file) {
            throw new NotFoundHttpException('Fayl topilmadi');
        }

        // Rasm o'lchami bo'yicha URL olish
        switch ($size) {
            case 'sm':
                $fileUrl = $file->getSmallUrl();
                break;
            case 'md':
                $fileUrl = $file->getMediumUrl();
                break;
            default:
                $fileUrl = $file->getOriginalUrl();
        }

        $filePath = Yii::getAlias('@frontend/web') . $fileUrl;

        if (!file_exists($filePath)) {
            throw new NotFoundHttpException('Fayl serverda topilmadi');
        }

        $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';
        $fileName = $file->name . '.' . $file->exts;

        return Yii::$app->response->sendFile($filePath, $fileName, [
            'mimeType' => $mimeType,
            'inline' => ($mode !== 'download'),
        ]);
    }
}
