<?php

declare(strict_types=1);

namespace App\Models\User\Infrastructure;

interface AuthKeyGenerator
{
    public function generate(): string;
}
