<?php

declare(strict_types=1);

namespace App\Models\User\Entity\User\Types;

use App\Models\User\Exception\InvalidArgumentException;
use App\Utils\ValueObject\IsEqual;
use Saschati\ValueObject\Types\ValueObjects\Abstracts\NativeType;

class Username extends NativeType
{
    use IsEqual;

    public function __construct(string $value)
    {
        if (mb_strlen($value) < 4) {
            throw new InvalidArgumentException('Login cannot be less than 4 characters.');
        }

        parent::__construct($value);
    }
}
