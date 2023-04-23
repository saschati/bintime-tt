<?php

declare(strict_types=1);

namespace App\Models\User\Entity\User\Types;

use App\Models\User\Exception\InvalidArgumentException;
use RuntimeException;
use Saschati\ValueObject\Types\ValueObjects\Interfaces\ValueObjectInterface;
use yii\db\Expression;

class Name implements ValueObjectInterface
{
    private string $firstName;
    private string $lastName;

    public function __construct(string $firstName, string $lastName)
    {
        if (empty($firstName) === true) {
            throw new InvalidArgumentException('First name cannot be empty.');
        }

        if (empty($lastName) === true) {
            throw new InvalidArgumentException('Last name cannot be empty.');
        }

        $this->firstName = ucfirst($firstName);
        $this->lastName  = ucfirst($lastName);
    }

    public function __toString(): string
    {
        return $this->fullName();
    }

    public function fullName(): string
    {
        return sprintf('%s %s', $this->firstName, $this->lastName);
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public static function convertToObjectValue(mixed $value): static
    {
        $value = \is_array($value) ? $value : json_decode($value, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException(json_last_error_msg());
        }

        return new self($value['firstName'], $value['lastName']);
    }

    public function convertToDatabaseValue(): Expression
    {
        $encoded = json_encode([
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
        ], JSON_UNESCAPED_UNICODE);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException(json_last_error_msg());
        }

        return new Expression("'{$encoded}'");
    }
}
