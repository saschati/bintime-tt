<?php

declare(strict_types=1);

namespace App\Core\Db;

use yii\db\Expression;

class SybQueryExpression extends Expression
{
    public function __construct(Query $query, string $pattern = '%s', array $params = [], array $config = [])
    {
        parent::__construct(sprintf($pattern, $query->createCommand()->rawSql), $params, $config);
    }
}
