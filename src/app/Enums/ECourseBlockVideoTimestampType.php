<?php

namespace App\Enums;

use App\Traits\EnumWithValues;

enum ECourseBlockVideoTimestampType: string
{
    use EnumWithValues;

    case EXAM = "EXAM";
    case INPUT = "INPUT";
    case TEXT = "TEXT";
}
