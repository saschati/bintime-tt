<?php

declare(strict_types=1);

namespace App\Models\User\Entity\User\Types;

use App\Utils\ValueObject\IsEqual;
use Saschati\ValueObject\Types\ValueObjects\EmailAddress;

class Email extends EmailAddress
{
    use IsEqual;
}
