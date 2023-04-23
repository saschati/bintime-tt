<?php

declare(strict_types=1);

namespace App\Models\User\Entity\User\Task\Types;

use App\Utils\ValueObject\EnumType;
use Saschati\ValueObject\Types\Specials\Interfaces\SpecialInterface;

enum Status: string implements SpecialInterface
{
    use EnumType;

    case New = 'new';
    case InProgress = 'in_progress';
    case Failed = 'failed';
    case Finished = 'finished';

    public function isNew(): bool
    {
        return $this === self::New;
    }

    public function isInProgress(): bool
    {
        return $this === self::InProgress;
    }

    public function isFailed(): bool
    {
        return $this === self::Failed;
    }

    public function isFinished(): bool
    {
        return $this === self::Finished;
    }
}
