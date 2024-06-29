<?php

namespace App\Enums;

use App\Traits\EnumWithValues;

enum EPackageType: int
{
    case DEFAULT = 0;
    case ARTICLE = 1;
    case BOOK = 2;
}
