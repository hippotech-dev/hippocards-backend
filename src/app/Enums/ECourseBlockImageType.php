<?php

namespace App\Enums;

use App\Traits\EnumWithValues;

enum ECourseBlockImageType: string
{
    use EnumWithValues;

    case DEFAULT = "DEFAULT";
}
