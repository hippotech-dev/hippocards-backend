<?php

namespace App\Enums;

use App\Traits\EnumWithValues;

enum EUserRole: int
{
    use EnumWithValues;

    case USER = 0;
    case SUPERADMIN = 1;
    case UNKNOWN = 2;
    case OLD_CONTENT_MANAGER = 3;
    case OLD_CONTENT_CREATOR = 4;
    case CONTENT_CREATOR = 6;
    case CONTENT_MANAGER = 7;
}
