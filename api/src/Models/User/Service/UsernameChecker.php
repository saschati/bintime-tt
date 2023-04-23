<?php

declare(strict_types=1);

namespace App\Models\User\Service;

use App\Models\User\Entity\User\Repository\UserQueries;
use App\Models\User\Entity\User\Types\Username;
use App\Models\User\Exception\DomainException;

class UsernameChecker
{
    public function __construct(private UserQueries $queries)
    {
    }

    public function check(Username $newUsername, ?Username $oldUsername = null): void
    {
        if ($oldUsername !== null && $newUsername->isEqual($oldUsername) === true) {
            return;
        }

        if ($this->queries->hasUsername($newUsername) === true) {
            throw new DomainException('User with this login is already exist!');
        }
    }
}
