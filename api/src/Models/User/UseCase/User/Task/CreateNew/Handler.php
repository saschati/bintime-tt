<?php

declare(strict_types=1);

namespace App\Models\User\UseCase\User\Task\CreateNew;

use App\Core\Flusher\Flusher;
use App\Models\User\Entity;
use App\Models\User\Entity\User\Task\Types;
use App\Models\User\Exception\DomainException;
use DateTime;
use Exception;

class Handler
{
    public function __construct(
        private Entity\User\Repository\UserQueries $queries,
        private Entity\User\Repository\UserRepository $repository,
        private Flusher $flusher
    ) {
    }

    /**
     * @throws Exception
     */
    public function handle(Command $command): void
    {
        /** @var Entity\User\User $user */
        $user = $this->queries->getById($command->id);

        $user->addTask(
            new Types\Id($command->taskId),
            $command->title,
            $command->description,
            DateTime::createFromFormat(
                '!Y-m-d H:i',
                $command->startedAt
            ) ?: throw new DomainException('Invalid start date.'),
            $this->repository
        );

        $this->repository->persist($user);
        $this->flusher->flush();
    }
}
