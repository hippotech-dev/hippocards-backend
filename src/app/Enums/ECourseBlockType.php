<?php

namespace App\Enums;

use App\Traits\EnumWithValues;

enum ECourseBlockType: string
{
    use EnumWithValues;

    case EXAM = "EXAM";
    case FINAL_EXAM = "FINAL_EXAM";
    case LESSON = "LESSON";
}
