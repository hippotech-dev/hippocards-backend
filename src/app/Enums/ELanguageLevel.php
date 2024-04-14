<?php

namespace App\Enums;

use App\Traits\EnumWithValues;

enum ELanguageLevel: string
{
    use EnumWithValues;

    case BEGINNER = "BEGINNER";
    case UPPER_BEGINNER = "UPPER_BEGINNER";
    case INTERMIDIATE = "INTERMIDIATE";
    case UPPER_INTERMIDIATE = "UPPER_INTERMIDIATE";
    case ADVANCED = "ADVANCED";
}
