<?php

declare(strict_types=1);

namespace App\Utils\ValueObject;

trait EnumType
{
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function array(): array
    {
        return array_combine(self::values(), self::names());
    }

    public static function convertToPhpValue(mixed $value): static
    {
        return self::tryFrom($value);
    }

    public static function convertToDatabaseValue(mixed $value): string|null
    {
        if ($value === null) {
            return null;
        }

        return (string)$value->value;
    }
}
