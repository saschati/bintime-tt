<?php

declare(strict_types=1);

namespace App\Models\User\Service;

use App\Models\User\Entity\User\Repository\UserQueries;
use App\Models\User\Entity\User\Types\Email;
use App\Models\User\Exception\DomainException;

class EmailChecker
{
    public function __construct(private UserQueries $queries)
    {
    }

    public function check(Email $newEmail, ?Email $oldEmail = null): void
    {
        if ($oldEmail !== null && $newEmail->isEqual($oldEmail) === true) {
            return;
        }

        if ($this->queries->hasEmail($newEmail) === true) {
            throw new DomainException('User with this email is already exist!');
        }
    }
}
