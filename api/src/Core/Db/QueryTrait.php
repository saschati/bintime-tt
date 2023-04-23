<?php

declare(strict_types=1);

namespace App\Core\Db;

use Closure;
use DateTime;
use yii\db\Expression;
use yii\db\ExpressionInterface;

trait QueryTrait
{
    public function andWhereDate(string $operator, string $dateColumn, DateTime $dateComparison): static
    {
        return $this->andWhere(
            [
                $operator,
                "UNIX_TIMESTAMP(DATE_FORMAT({$dateColumn}, \"%Y-%m-%d\"))",
                new Expression("UNIX_TIMESTAMP(\"{$dateComparison->format('Y-m-d')}\")"),
            ]
        );
    }

    public function andFilterWhereDate(string $column, string $format = 'd.m.Y', ?string $from = null, ?string $to = null): static
    {
        if (empty($from) === false && empty($to) === false) {
            $this->andWhereDate('>=', $column, DateTime::createFromFormat($format, $from));
            $this->andWhereDate('<=', $column, DateTime::createFromFormat($format, $to));
        } elseif (empty($from) === false) {
            $this->andWhereDate('>=', $column, DateTime::createFromFormat($format, $from));
        } elseif (empty($to) === false) {
            $this->andWhereDate('<=', $column, DateTime::createFromFormat($format, $to));
        }// end if

        return $this;
    }

    public function leftJoinSub(self $query, string $alias, string $on, array $params = []): static
    {
        return $this->leftJoin(sprintf('(%s) %s', $query->createCommand()->rawSql, $alias), $on, $params);
    }

    public function rightJoinSub(self $query, string $alias, string $on, array $params = []): static
    {
        return $this->rightJoin(sprintf('(%s) %s', $query->createCommand()->rawSql, $alias), $on, $params);
    }

    public function whereIf(bool $condition, Closure $closure): static
    {
        return $condition === true ? $closure->call($this) : $this;
    }

    public function getColumnWithSelect(string $index, string $value): array
    {
        return $this->select([$value])->indexBy($index)->column();
    }

    public function getColumn(string $value): array
    {
        return $this->select([$value])->column();
    }

    /**
     * @param array|ExpressionInterface|string $condition
     * @param array                            $params
     */
    public function where($condition, $params = []): static
    {
        return $this->andWhere($condition, $params);
    }
}
