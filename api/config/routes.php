<?php

declare(strict_types=1);

use yii\web\GroupUrlRule;

return [
    'class' => 'yii\web\UrlManager',
    'enablePrettyUrl' => true,
    'showScriptName' => false,
    'rules' => [
        [
            'class' => GroupUrlRule::class,
            'prefix' => 'api',
            'routePrefix' => 'Api',
            'rules' => require_once 'routes/api.php',
        ],
    ],
];
