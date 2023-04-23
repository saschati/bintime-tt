<?php

declare(strict_types=1);

namespace App\Http\Response;

interface Responder
{
    public function transform($data): object|array;
}
