<?php

declare(strict_types=1);

namespace App\Core\Db\Migration;

use yii\db\ColumnSchemaBuilder;

class ForeignKey
{
    public function __construct(
        private ColumnSchemaBuilder $schema,
        private string|array $refTables,
        private string|array $refColumns,
        private bool $onlyFk = false
    ) {
    }

    public function getSchema(): ColumnSchemaBuilder
    {
        return clone $this->schema;
    }

    public function getRefTables(): string|array
    {
        return $this->refTables;
    }

    public function getRefColumns(): string|array
    {
        return $this->refColumns;
    }

    public function isOnlyFk(): bool
    {
        return $this->onlyFk === true;
    }
}
