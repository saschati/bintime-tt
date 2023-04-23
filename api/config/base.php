<?php

declare(strict_types=1);

use App\Models\User\Entity\User\Types\Role;
use App\Models\User\Entity\User\User;
use App\Provider\AppProvider;
use elisdn\hybrid\AuthManager;
use yii\caching\FileCache;
use yii\log\DbTarget;

$params  = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'name' => env('APP_NAME', 'YII APP'),
    'basePath' => dirname(__DIR__),
    'timeZone' => 'Europe/Kiev',
    'language' => 'uk',
    'bootstrap' => [
        'log',
        AppProvider::class,
    ],
    'components' => [
        'cache' => ['class' => FileCache::class],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => DbTarget::class,
                    'levels' => explode(',', env('YII_LOG_LEVELS', 'error')),
                    'logTable' => '{{%system_log}}',
                    'except' => [
                        'yii\web\HttpException:*',
                        'yii\web\NotFoundHttpException:*',
                        'yii\base\InvalidRouteException:*',
                    ],
                ],
            ],
        ],
        'db' => $db,
        'authManager' => [
            'class' => AuthManager::class,
            'modelClass' => User::class,
            'defaultRoles' => [Role::Common],
        ],
    ],
    'params' => $params,
];

return $config;
