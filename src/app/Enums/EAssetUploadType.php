<?php

namespace App\Enums;

use App\Traits\EnumWithValues;

enum EAssetUploadType: int
{
    case UPLOAD = 0;
    case UNSPLASH = 1;
    case URL = 2;
}
