<?php

declare(strict_types=1);

namespace App\Utils\ValueObject;

trait IsEqual
{
    public function isEqual(self $other): bool
    {
        return $this->getValue() === $other->getValue();
    }
}
