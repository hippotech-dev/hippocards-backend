<?php

namespace App\Enums;

use App\Traits\EnumWithValues;

enum EPackageExamType: string
{
    case EXAM = "EXAM";
    case FINAL_EXAM = "FINAL_EXAM";
}
