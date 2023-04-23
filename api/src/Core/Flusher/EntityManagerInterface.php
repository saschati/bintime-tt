<?php

declare(strict_types=1);
/**
 * PHP version 7.4.
 */

namespace App\Core\Flusher;

use yii\db\ActiveRecordInterface;

/**
 * Interface EntityManagerInterface.
 */
interface EntityManagerInterface
{
    public function persist(ActiveRecordInterface $record): void;

    public function remove(ActiveRecordInterface $record): void;

    public function flush(): void;
}
