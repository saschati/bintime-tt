<?php

declare(strict_types=1);

namespace App\Core\Db\Migration;

use yii\base\NotSupportedException;
use yii\db\ColumnSchemaBuilder;
use yii\db\Migration;

class AbstractMigration extends Migration
{
    protected const TYPE_UUID = 'uuid';
    protected const TYPE_JSONB = 'jsonb';

    protected string $tableOptions = '';

    /**
     * @throws NotSupportedException
     */
    public function uuid(): ColumnSchemaBuilder
    {
        return $this->getDb()
            ->getSchema()
            ->createColumnSchemaBuilder(self::TYPE_UUID, (self::TYPE_UUID === 'string') ? 36 : null);
    }

    /**
     * @throws NotSupportedException
     */
    public function jsonb(): ColumnSchemaBuilder
    {
        return $this->getDb()->getSchema()->createColumnSchemaBuilder(self::TYPE_JSONB);
    }

    public function pk(ColumnSchemaBuilder $schema): PrimaryKey
    {
        return new PrimaryKey($schema->notNull());
    }

    public function fk(
        ColumnSchemaBuilder $schema,
        string|array $refTables,
        string|array $refColumns,
        bool $onlyFk = false
    ): ForeignKey {
        return new ForeignKey($schema, $refTables, $refColumns, $onlyFk);
    }

    /**
     * @param string $table
     * @param array  $columns
     * @param string $options
     */
    public function createTable($table, $columns, $options = null): void
    {
        $primaries = [];
        $indexes = [];
        $foreigns = [];
        foreach ($columns as $column => $type) {
            if ($type instanceof PrimaryKey) {
                $primaries[] = $column;
                $columns[$column] = $type->getSchema();
            }

            if ($type instanceof ForeignKey) {
                if ($type->isOnlyFk() === false) {
                    $indexes[] = $column;
                }
                $foreigns[$column] = $type;
                $columns[$column] = $type->getSchema();
            }
        }

        parent::createTable($table, $columns, $options ?? $this->tableOptions);

        if ($primaries !== []) {
            $this->addPrimaryKey(
                sprintf('pk_%s_%s', $this->trimTable($table), implode('_', $primaries)),
                $table,
                $primaries
            );
        }

        foreach ($indexes as $column) {
            $this->createIndex(sprintf('inx_%s_%s', $this->trimTable($table), $column), $table, $column);
        }

        foreach ($foreigns as $column => $type) {
            $this->addForeignKey(
                sprintf('fk_%s_%s_%s', $this->trimTable($table), $this->trimTable($type->getRefTables()), $column),
                $table,
                $column,
                $type->getRefTables(),
                $type->getRefColumns()
            );
        }
    }

    public function addColumn($table, $column, $type): void
    {
        if ($type instanceof ForeignKey) {
            parent::addColumn($table, $column, $type->getSchema());

            if ($type->isOnlyFk() === false) {
                $this->createIndex(sprintf('inx_%s_%s', $this->trimTable($table), $column), $table, $column);
            }

            $this->addForeignKey(
                sprintf('fk_%s_%s_%s', $this->trimTable($table), $this->trimTable($type->getRefTables()), $column),
                $table,
                $column,
                $type->getRefTables(),
                $type->getRefColumns()
            );
        } else {
            parent::addColumn($table, $column, $type);
        }
    }

    public function dropFk(string $column, string $table, string $refTables, bool $withIndex = true): void
    {
        $this->dropForeignKey(
            sprintf('fk_%s_%s_%s', $this->trimTable($table), $this->trimTable($refTable), $column),
            $table,
        );

        if ($withIndex) {
            $this->dropIndex(sprintf('inx_%s_%s', $this->trimTable($table), $column), $table);
        }
    }

    public function autoIncrement(?int $length = null): ColumnSchemaBuilder
    {
        return $this->integer($length)->unique()->notNull()->append(' AUTO_INCREMENT');
    }

    public function createdAtTimestamp(): ColumnSchemaBuilder
    {
        return $this->timestamp()->notNull()->append(' DEFAULT CURRENT_TIMESTAMP');
    }

    public function updatedAtTimestamp(): ColumnSchemaBuilder
    {
        return $this->timestamp()->notNull()->append(' DEFAULT NOW() ON UPDATE NOW()');
    }

    private function trimTable(string $table): string
    {
        preg_match('/([\w\_]+)/', $table, $matches);

        [, $trimTable] = $matches;

        return $trimTable;
    }
}
