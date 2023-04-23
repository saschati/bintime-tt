<?php

declare(strict_types=1);

namespace App\Models\User\Entity\User\Types;

use App\Utils\ValueObject\EnumType;
use Saschati\ValueObject\Types\Specials\Interfaces\SpecialInterface;

enum Role: string implements SpecialInterface
{
    use EnumType;

    case Admin = 'admin';
    case User = 'user';
    case Common = 'common';

    public function getName(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::User => 'User',
            self::Common => 'Common'
        };
    }

    public function isAdmin(): bool
    {
        return $this === Role::Admin;
    }

    public function isUser(): bool
    {
        return $this === Role::User;
    }
}
