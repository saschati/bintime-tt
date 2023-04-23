<?php

declare(strict_types=1);

namespace App\Core\Db\DataProvider;

use yii\data\Pagination;
use yii\data\Sort;
use yii\data\SqlDataProvider as BaseSqlDataProvider;
use yii\db\Query;

class SqlDataProvider extends BaseSqlDataProvider
{
    public static function fromQuery(
        Query $query,
        bool|array|Pagination $pagination = false,
        bool|array|Sort $sort = false,
        mixed ...$params
    ): static {
        return new self(
            [
                'sql'        => $query->createCommand()->rawSql,
                'totalCount' => (int)$query->count(),
                'pagination' => $pagination,
                'sort'       => $sort,
                ...$params,
            ]
        );
    }
}
