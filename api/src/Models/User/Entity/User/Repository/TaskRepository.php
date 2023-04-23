<?php

declare(strict_types=1);

namespace App\Models\User\Entity\User\Repository;

use App\Models\User\Entity\User\Task\Task;

interface TaskRepository
{
    public function addTask(Task $task): void;

    public function removeTask(Task $task): void;
}
