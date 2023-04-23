<?php

declare(strict_types=1);

use App\Models\User\Entity\User\User;
use App\Provider\WebProvider;
use yii\web\JsonParser;
use yii\web\Request;

$aliases = require __DIR__ . '/aliases.php';

$config = [
    'id' => 'basic',
    'bootstrap' => [
        WebProvider::class,
    ],
    'aliases' => $aliases,
    'controllerNamespace' => 'App\Http\Controllers',
    'components' => [
        'request' => ['cookieValidationKey' => env('COOKIE_KEY')],
        'api' => [
            'class' => Request::class,
            'parsers' => ['application/json' => JsonParser::class],
        ],
        'response' => [
            'format' => yii\web\Response::FORMAT_JSON,
            'charset' => 'UTF-8',
        ],
        'user' => [
            'identityClass' => User::class,
            'enableAutoLogin' => true,
        ],
        'urlManager' => require __DIR__ . DIRECTORY_SEPARATOR . 'routes.php',
    ],
];

if (env('YII_ENV') === 'dev') {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['*'],
        'dataPath' => '@runtime' . DIRECTORY_SEPARATOR . PHP_SAPI . DIRECTORY_SEPARATOR . 'debug',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class'      => 'yii\gii\Module',
        'allowedIPs' => ['*'],
    ];
}

return $config;
