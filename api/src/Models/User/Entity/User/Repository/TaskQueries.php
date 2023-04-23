<?php

declare(strict_types=1);

namespace App\Models\User\Entity\User\Repository;

use App\Models\User\Entity\User\Task\Task;
use App\Models\User\Entity\User\Task\Types as TaskTypes;
use App\Models\User\Entity\User\Types as UserTypes;

interface TaskQueries
{
    public function getTask(UserTypes\Id $userId, TaskTypes\Id $id): Task;
}
