<?php

declare(strict_types=1);

use yii\console\controllers\MigrateController;

$db = require __DIR__ . '/db.php';
$aliases = require __DIR__ . '/aliases.php';

$config = [
    'id' => 'basic-console',
    'controllerMap' => [
        'migrate' => [
            'class' => MigrateController::class,
            'templateFile' => '@data/database/template.php',
            'migrationPath' => '@data/database/migrations',
        ],
    ],
    'bootstrap' => [],
    'controllerNamespace' => 'App\Commands',
    'aliases' => array_merge(
        $aliases,
        [
            '@webroot' => '@app/web',
            '@web' => '',
        ]
    ),
    'components' => [
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'baseUrl' => '/',
            'hostInfo' => env('APP_URL'),
            'rules' => require_once 'routes/api.php',
        ],
    ],
];

if (env('YII_ENV') === 'dev') {
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => yii\gii\Module::class,
    ];
}

return $config;
