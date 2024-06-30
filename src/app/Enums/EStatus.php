<?php

namespace App\Enums;

use App\Traits\EnumWithValues;

enum EStatus: int
{
    use EnumWithValues;

    case PENDING = 0;
    case SUCCESS = 1;
    case FAILURE = 2;
    case PUBLISHED = 3;
    case PACKAGE_PUBLISHED = 4;
}
