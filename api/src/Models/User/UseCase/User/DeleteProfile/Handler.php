<?php

declare(strict_types=1);

namespace App\Models\User\UseCase\User\DeleteProfile;

use App\Core\Flusher\Flusher;
use App\Models\User\Entity;
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

        $this->repository->remove($user);
        $this->flusher->flush();
    }
}
