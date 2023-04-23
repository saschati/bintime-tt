<?php

declare(strict_types=1);

namespace App\Models\User\Exception;

use App\Models\EntityException;

class DomainException extends \DomainException implements EntityException
{
}
