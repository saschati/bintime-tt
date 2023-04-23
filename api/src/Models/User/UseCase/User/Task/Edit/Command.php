<?php

declare(strict_types=1);

namespace App\Models\User\UseCase\User\Task\Edit;

class Command
{
    public string $title = '';
    public string $description = '';
    public string $status = '';
    public string $startedAt = '';

    public function __construct(
        public string $id,
        public string $taskId
    ) {
    }
}
