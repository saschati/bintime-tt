<?php

declare(strict_types=1);

namespace App\Models\User\Service;

use App\Models\User\Exception\DomainException;
use App\Models\User\Infrastructure\Password\PasswordHasher as PasswordHasherInterface;
use App\Models\User\Infrastructure\Password\PasswordValidator;
use yii\base\Exception;
use yii\base\Security;

class PasswordHasher implements PasswordHasherInterface, PasswordValidator
{
    public function __construct(private Security $security)
    {
    }

    /**
     * @throws Exception
     */
    public function hash(string $password): string
    {
        return $this->security->generatePasswordHash($password);
    }

    /**
     * @throws DomainException
     */
    public function isValid(string $password, string $hash): void
    {
        if ($this->security->validatePassword($password, $hash) === false) {
            throw new DomainException('Login or password is incorrect.');
        }
    }
}
