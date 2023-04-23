<?php

declare(strict_types=1);

namespace App\Core\Flusher;

use yii\db\ActiveRecordInterface;

abstract class AbstractRepository
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    final public function persist(ActiveRecordInterface ...$records): void
    {
        foreach ($records as $record) {
            $this->em->persist($record);
        }
    }

    final public function remove(ActiveRecordInterface ...$records): void
    {
        foreach ($records as $record) {
            $this->em->remove($record);
        }
    }
}
