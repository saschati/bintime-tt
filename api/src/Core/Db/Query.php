<?php

declare(strict_types=1);

namespace App\Core\Db;

use yii\db\Query as BaseQuery;

class Query extends BaseQuery
{
    use QueryTrait;
}
