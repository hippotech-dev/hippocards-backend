<?php

namespace App\Enums;

use App\Traits\EnumWithValues;

enum EUserActivityAction: int
{
    use EnumWithValues;

    case MEMORIZE = 0;
    case READ = 1;
    case FINISH = 2;
    case CREATE = 3;
    case UPDATE = 4;
    case DELETE = 5;
}
