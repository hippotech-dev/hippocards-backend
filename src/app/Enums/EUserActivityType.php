<?php

namespace App\Enums;

use App\Traits\EnumWithValues;

enum EUserActivityType: int
{
    use EnumWithValues;

    case DEFAULT = 0;
    case USER_WORD = 1;
    case SYSTEM_WORD = 2;
    case USER_COURSE = 3;
    case SYSTEM_COURSE = 4;
    case USER_ARTICLE = 5;
    case SYSTEM_ARTICLE = 6;
    case USER_PACKAGE = 7;
    case SYSTEM_PACKAGE = 8;
}
