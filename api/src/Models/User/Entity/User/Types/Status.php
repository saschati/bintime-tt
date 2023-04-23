<?php

declare(strict_types=1);

namespace App\Models\User\Entity\User\Types;

use App\Utils\ValueObject\EnumType;
use Saschati\ValueObject\Types\Specials\Interfaces\SpecialInterface;

enum Status: string implements SpecialInterface
{
    use EnumType;

    case Active = 'active';
    case Wait = 'wait';

    public function isActive(): bool
    {
        return $this === self::Active;
    }

    public function isWait(): bool
    {
        return $this === self::Wait;
    }
}
