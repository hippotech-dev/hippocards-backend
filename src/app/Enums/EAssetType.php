<?php

namespace App\Enums;

use App\Traits\EnumWithValues;

enum EAssetType: string
{
    use EnumWithValues;

    case UNKNOWN = "UNKNOWN";
    case PDF = "PDF";
    case IMAGE = "IMAGE";
    case VIDEO = "VIDEO";
    case AUDIO = "AUDIO";
}
