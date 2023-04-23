<?php

declare(strict_types=1);

namespace App\Models\User\UseCase\User\EditProfile;

use App\Models\User\UseCase\User\JoinByEmail\Request\Chunk\Name;

class Command
{
    public Name $name;
    public string $username = '';
    public string $email = '';
    public string $password = '';

    public function __construct(public string $id)
    {
    }
}
