<?php

declare(strict_types=1);

use yii\base\Application;
use yii\BaseYii;
use yii\web\Request;

/**
 * Yii bootstrap file.
 * Used for enhanced IDE code autocompletion.
 * Note: To avoid "Multiple Implementations" PHPStorm warning and make autocomplete faster
 * exclude or "Mark as Plain Text" vendor/yiisoft/yii2/Yii.php file.
 */
class Yii extends BaseYii
{
    /**
     * @var BaseApplication
     */
    public static $app;
}

/**
 * Class BaseApplication
 * Used for properties that are identical for both WebApplication and ConsoleApplication.
 *
 * @property Request $api
 */
abstract class BaseApplication extends Application
{
}
