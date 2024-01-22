<?php

namespace App\Enums;

use App\Traits\EnumWithValues;

enum EStatus: string
{
    use EnumWithValues;

    case PENDING = "PENDING";
    case SUCCESS = "SUCCESS";
    case FAILURE = "FAILURE";
}
