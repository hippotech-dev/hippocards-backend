<?php

namespace App\Enums;

use App\Traits\EnumWithValues;

enum ELanguageLevel: string
{
    use EnumWithValues;

    case BEGINNER = "BEGINNER";
    case INTERMIDIATE = "INTERMIDIATE";
    case ADVANCED = "ADVANCED";
}
