<?php

namespace App\Enums;

use App\Traits\EnumWithValues;

enum EUserRole: int
{
    use EnumWithValues;

    case SUPERADMIN = 1;
    case USER = 5;
}
