<?php

declare(strict_types=1);

namespace App\Utils;

class Interpolator
{
    public function __construct(private string $format = '%s')
    {
    }

    public function __invoke(mixed $value): string
    {
        return sprintf($this->format, $value);
    }
}
