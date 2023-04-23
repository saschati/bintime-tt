<?php

declare(strict_types=1);

namespace App\Models;

interface EntityException
{
    public function getCode();

    public function getMessage(): string;
}
