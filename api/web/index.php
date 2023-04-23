<?php

declare(strict_types=1);

use yii\helpers\ArrayHelper;
use yii\web\Application;

require __DIR__ . '/../vendor/autoload.php';

defined('YII_DEBUG') || define('YII_DEBUG', env('YII_DEBUG', true));
defined('YII_ENV') || define('YII_ENV', env('YII_ENV', 'dev'));

require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$config = ArrayHelper::merge(
    require __DIR__ . '/../config/base.php',
    require __DIR__ . '/../config/web.php'
);

(new Application($config))->run();
