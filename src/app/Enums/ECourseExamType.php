<?php

namespace App\Enums;

use App\Traits\EnumWithValues;

enum ECourseExamType: string
{
    use EnumWithValues;

    case CHOOSE = "CHOOSE";
}
