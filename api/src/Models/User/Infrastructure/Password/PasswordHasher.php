<?php

declare(strict_types=1);

namespace App\Models\User\Infrastructure\Password;

interface PasswordHasher
{
    public function hash(string $password): string;
}
