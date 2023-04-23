<?php

declare(strict_types=1);

namespace App\Models\User\Infrastructure\Password;

interface PasswordValidator
{
    public function isValid(string $password, string $hash): void;
}
