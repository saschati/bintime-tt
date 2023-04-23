<?php

declare(strict_types=1);

namespace App\Models\User\UseCase\User\EditProfile;

use App\Core\Flusher\Flusher;
use App\Models\User\Entity;
use App\Models\User\Entity\User\Types;
use App\Models\User\Service;
use Exception;

class Handler
{
    public function __construct(
        private Entity\User\Repository\UserQueries $queries,
        private Entity\User\Repository\UserRepository $repository,
        private Service\UsernameChecker $usernameChecker,
        private Service\EmailChecker $emailChecker,
        private Service\PasswordHasher $passwordHasher,
        private Service\AuthKeyGenerator $authKeyGenerator,
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

        $this->usernameChecker->check($username = new Types\Username($command->username), $user->username);
        $this->emailChecker->check($email = new Types\Email($command->email), $user->email);

        $user->editProfile(
            $email,
            $username,
            new Entity\User\Types\Name(
                $command->name->firstName,
                $command->name->lastName
            ),
            $command->password,
            $this->passwordHasher,
            $this->authKeyGenerator
        );

        $this->repository->persist($user);
        $this->flusher->flush();
    }
}
