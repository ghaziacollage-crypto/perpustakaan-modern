<?php

declare(strict_types=1);

namespace App\Enums;

enum BorrowingStatus: string
{
    case Pending = 'pending';
    case Active = 'active';
    case Returned = 'returned';
    case Late = 'late';
}
