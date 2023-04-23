<?php

declare(strict_types=1);

namespace App\Core\Db;

use Exception;
use yii\db\Connection;

class TransactionManager
{
    public function __construct(private Connection $connection)
    {
    }

    /**
     * @throws Exception
     */
    public function wrap(callable $function): void
    {
        $transaction = $this->connection->beginTransaction();
        try {
            $function();

            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();

            throw $e;
        }
    }
}
