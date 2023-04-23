<?php

declare(strict_types=1);

namespace App\Models\User\Service;

use App\Models\User\Infrastructure\AuthKeyGenerator as AuthKeyGeneratorInterface;
use yii\base\Exception;
use yii\base\Security;

class AuthKeyGenerator implements AuthKeyGeneratorInterface
{
    public function __construct(private Security $security)
    {
    }

    /**
     * @throws Exception
     */
    public function generate(): string
    {
        return $this->security->generateRandomString();
    }
}
