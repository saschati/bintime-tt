<?php

declare(strict_types=1);

namespace App\Models\User\Entity\User\Types;

use App\Utils\ValueObject\IsEqual;
use Saschati\ValueObject\Types\ValueObjects\Id as Uuid;

class Id extends Uuid
{
    use IsEqual;
}
