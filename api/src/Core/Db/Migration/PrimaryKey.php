<?php

declare(strict_types=1);

namespace App\Core\Db\Migration;

use yii\db\ColumnSchemaBuilder;

class PrimaryKey
{
    public function __construct(private ColumnSchemaBuilder $schema)
    {
    }

    public function getSchema(): ColumnSchemaBuilder
    {
        return clone $this->schema;
    }
}
