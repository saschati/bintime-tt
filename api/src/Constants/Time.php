<?php

declare(strict_types=1);

namespace App\Constants;

enum Time: int
{
    case secondInMinute = 60;
    case secondInHour = 3600;
    case secondInDay = 86400;
    case secondInWeek = 604800;
    case secondInMonth = 2592000;
    case secondInYear = 31536000;
}
