<?php

namespace App\Enums;

use App\Traits\EnumWithValues;

enum ECourseBlockVideoType: string
{
    use EnumWithValues;

    case TRANSLATION = "TRANSLATION";
    case IMAGINATION = "IMAGINATION";
}
