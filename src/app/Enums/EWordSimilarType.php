<?php

namespace App\Enums;

enum EWordSimilarType: int
{
    case SYNONYM = 0;
    case ANTONYM = 1;
    case SIMILAR = 2;
}
