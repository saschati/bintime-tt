<?php

declare(strict_types=1);

namespace App\Models\User\UseCase\User\DeleteProfile;

class Command
{
    public function __construct(public string $id)
    {
    }
}
