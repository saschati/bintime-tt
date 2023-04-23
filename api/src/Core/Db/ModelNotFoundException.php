<?php

declare(strict_types=1);

namespace App\Core\Db;

use App\Models\EntityException;
use yii\web\NotFoundHttpException;

class ModelNotFoundException extends NotFoundHttpException implements EntityException
{
}
