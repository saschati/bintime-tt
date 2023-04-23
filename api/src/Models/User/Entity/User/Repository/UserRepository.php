<?php

declare(strict_types=1);

namespace App\Models\User\Entity\User\Repository;

use App\Core\Flusher\AbstractRepository;
use App\Models\User\Entity\User\Task\Task;

class UserRepository extends AbstractRepository implements TaskRepository
{
    public function addTask(Task $task): void
    {
        $this->persist($task);
    }

    public function removeTask(Task $task): void
    {
        $this->remove($task);
    }
}
