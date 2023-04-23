<?php

declare(strict_types=1);

namespace App\Utils\ValueObject;

use RuntimeException;
use Saschati\ValueObject\Types\Specials\Interfaces\SpecialInterface;
use yii\db\Expression;

class JsonType implements SpecialInterface
{
    public static function convertToPhpValue(mixed $value): array
    {
        $value = \is_array($value) ? $value : json_decode($value, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException(json_last_error_msg());
        }

        return $value;
    }

    public static function convertToDatabaseValue(mixed $value): Expression|null
    {
        if ($value === null) {
            return null;
        }

        $encoded = json_encode($value, JSON_UNESCAPED_UNICODE);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException(json_last_error_msg());
        }

        return new Expression("'{$encoded}'");
    }
}
