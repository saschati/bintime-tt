<?php

declare(strict_types=1);

namespace App\Core\Db;

use yii\db\ActiveQuery as YiiActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Connection;

abstract class ActiveQuery extends YiiActiveQuery
{
    use QueryTrait;

    /**
     * @throws ModelNotFoundException
     */
    final public function firstOrFail(string $message = null, null|Connection $db = null): ActiveRecord|array
    {
        $model = static::one($db);

        if ($model === null || $model === []) {
            throw new ModelNotFoundException($message ?: 'No query results for this model');
        }

        return $model;
    }
}
