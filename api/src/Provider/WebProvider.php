<?php

declare(strict_types=1);

namespace App\Provider;

use Yii;
use yii\base\Application;
use yii\base\BootstrapInterface;

class WebProvider implements BootstrapInterface
{
    /**
     * @param Application|mixed $app
     */
    public function bootstrap($app): void
    {
        $container = Yii::$container;
    }
}
