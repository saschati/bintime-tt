<?php

declare(strict_types=1);

namespace App\Http\Forms;

interface CompositeForm
{
    public function forms(): array;
}
