<?php

namespace App\Enums;

use App\Traits\EnumWithValues;

enum EUserLoginType: int
{
    use EnumWithValues;

    case LOGIN_UNKNOWN = 0;
    case LOGIN_NORMAL = 1;
    case LOGIN_FACEBOOK = 2;
    case LOGIN_GMAIL = 3;
    case LOGIN_APPLE = 4;
    case LOGIN_EMAIL = 5;
}
