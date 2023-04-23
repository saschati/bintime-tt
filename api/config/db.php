<?php

declare(strict_types=1);

use App\Constants\Time;
use yii\db\Connection;

return [
    'class' => Connection::class,
    'dsn' =>  'pgsql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE'),
    'username' => getenv('DB_USER'),
    'password' => getenv('DB_PASSWORD'),
    'charset' => 'utf8',
    'tablePrefix' => getenv('DB_TABLE_PREFIX'),

    // Schema cache options (for production environment)
    'enableSchemaCache'=> true,
    'schemaCacheDuration' => Time::secondInDay->value,
    'schemaCache' => 'cache',
];
