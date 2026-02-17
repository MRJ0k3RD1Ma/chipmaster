<?php

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
            'baseUrl'=>'/api'
        ],
        'response' => [
            'formatters' => [
                \yii\web\Response::FORMAT_JSON => [
                    'class' => 'yii\web\JsonResponseFormatter',
                    'prettyPrint' => YII_DEBUG,
                    'encodeOptions' => JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
                ],
            ],
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                // OPTIONS preflight uchun
                'OPTIONS <url:.*>' => 'site/options',
                ''=>'site/index',
                'GET v1/getfile/<slug:[\w-]+>' => 'site/file',
                'POST v1/admin-auth/login' => 'v1/admin-auth/login',
                'POST v1/admin-auth/refresh' => 'v1/admin-auth/refresh',
                'POST v1/admin-auth/logout' => 'v1/admin-auth/logout',
                'GET v1/admin-auth/me' => 'v1/admin-auth/me',
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => ['v1/admin-role', 'v1/admin', 'v1/brand'],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => ['v1/category'],
                    'extraPatterns' => [
                        'GET tree' => 'tree',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => ['v1/product'],
                    'extraPatterns' => [
                        // Fully create/update
                        'POST create-fully' => 'create-fully',
                        'PUT <id:\d+>/update-fully' => 'update-fully',
                        // Product Guides
                        'GET <product_id:\d+>/guides' => 'guides',
                        'GET <product_id:\d+>/guides/<id:\d+>' => 'guide-view',
                        'POST <product_id:\d+>/guides' => 'guide-create',
                        'PUT <product_id:\d+>/guides/<id:\d+>' => 'guide-update',
                        'DELETE <product_id:\d+>/guides/<id:\d+>' => 'guide-delete',
                        // Product Images
                        'GET <product_id:\d+>/images' => 'images',
                        'GET <product_id:\d+>/images/<id:\d+>' => 'image-view',
                        'POST <product_id:\d+>/images' => 'image-create',
                        'PUT <product_id:\d+>/images/<id:\d+>' => 'image-update',
                        'PUT <product_id:\d+>/images/<id:\d+>/set-primary' => 'image-set-primary',
                        'DELETE <product_id:\d+>/images/<id:\d+>' => 'image-delete',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => ['v1/file'],
                    'extraPatterns' => [
                        'POST upload' => 'upload',
                        'POST cancel' => 'cancel',
                    ],
                ],
            ],
        ],
    ],
    'params' => $params,
];
