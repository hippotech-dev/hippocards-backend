<?php

namespace App\Enums;

use App\Traits\EnumWithValues;

enum ECodeChallengeMethod: string
{
    use EnumWithValues;

    case PLAIN = "PLAIN";
    case S256 = "S256";
}
