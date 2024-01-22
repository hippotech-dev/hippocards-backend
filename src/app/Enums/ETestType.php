<?php

namespace App\Enums;

use App\Traits\EnumWithValues;

enum ETestType: string
{
    use EnumWithValues;

    case IMAGE = "IMAGE";
    case SINGLE = "SINGLE";
    case CONNECT = "CONNECT";
    case VOICE = "VOICE";
    case FILL = "FILL";
}
