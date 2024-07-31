<?php

namespace App\Enums;

use App\Traits\EnumWithValues;

enum EUserPreferenceType: string
{
    case LEARNING_PURPOSE = 'LEARNING_PURPOSE';
    case HOW_HIPPO_WILL_HELP = 'HOW_HIPPO_WILL_HELP';
    case CATEGORIES = 'CATEGORIES';

    case STUDY_TIME = 'STUDY_TIME';
    case STUDY_REPETITION = 'STUDY_REPETITION';
    case LANGUAGE_LEVEL = 'LANGUAGE_LEVEL';
}
