<?php

declare(strict_types=1);

namespace App\Enums;

enum BookCondition: string
{
    case Normal = 'normal';
    case Rusak = 'rusak';
    case Hilang = 'hilang';
}
