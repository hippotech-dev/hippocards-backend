<?php

namespace App\Enums;

use App\Traits\EnumWithValues;

enum EUserRole: int
{
    use EnumWithValues;

    case UNKNOWN = 0;
    case SUPERADMIN = 1;
    case USER = 5;
}
