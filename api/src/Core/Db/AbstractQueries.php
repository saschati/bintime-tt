<?php

declare(strict_types=1);

namespace App\Core\Db;

use yii\base\Model;
use yii\db\ActiveRecord;
use yii\db\BatchQueryResult;
use yii\db\Expression;
use yii\db\Query;

abstract class AbstractQueries
{
    /**
     * @param mixed $id
     * @throws ModelNotFoundException
     */
    final public function getById($id): ActiveRecord|null
    {
        $model = $this->model()::findOne($id);

        if ($model === null) {
            throw new ModelNotFoundException('Nothing was found for this ID.');
        }

        return $model;
    }

    /**
     * @return ActiveRecord[]
     */
    final public function getAll(array|Expression|string $condition = null): array
    {
        return $this->model()::findAll($condition);
    }

    final public function eachAll(array|Expression|string $condition = null): BatchQueryResult
    {
        return $this->query($condition)->each();
    }

    final public function count(array|Expression|string $condition = null): int
    {
        return $this->query($condition)->count();
    }

    final public function findById(mixed $id): ?Model
    {
        return $this->findBy('id', $id);
    }

    final public function findBy(string $column, mixed $value): ?Model
    {
        return $this->query()->where([$column => $value])->one();
    }

    final public function hasBy(string $column, mixed $value): bool
    {
        return $this->query()->where([$column => $value])->exists();
    }

    /**
     * @return ActiveRecord
     */
    abstract protected function model();

    protected function query(array|Expression|string $condition = null): ActiveQuery|Query
    {
        if ($condition !== null) {
            return $this->model()::find()->where($condition);
        }

        return $this->model()::find();
    }

    protected function where(array|Expression|string $condition): ActiveQuery|Query
    {
        return $this->query($condition);
    }
}
