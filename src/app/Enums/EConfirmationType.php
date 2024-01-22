<?php

namespace App\Enums;

use App\Traits\EnumWithValues;

enum EConfirmationType: int
{
    use EnumWithValues;

    case PHONE = 0;
    case EMAIL = 1;
}
