<?php

namespace App\Enums;

use App\Traits\EnumWithValues;

enum EUserActivityType: int
{
    use EnumWithValues;

    case DEFAULT = 0;
    case WORD = 1;
    case COURSE = 2;
    case ARTICLE = 3;
    case PACKAGE = 4;
}
