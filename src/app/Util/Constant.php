<?php

namespace App\Util;

use App\Enums\EPartOfSpeech;
use App\Enums\EPermissionScope;
use App\Enums\EPromoType;
use App\Enums\EUserRole;
use App\Models\Course\Course;
use App\Models\Package\Baseklass;
use App\Models\Package\Word\Word;
use App\Models\Subscription\SubPlan;

class Constant
{
    public const DEFAULT_PAGINATION = 20;

    public const ROLE_SCOPES = [
        EUserRole::SUPERADMIN->value => [
            EPermissionScope::READ_PACKAGE,
            EPermissionScope::CREATE_PACKAGE,
            EPermissionScope::UPDATE_PACKAGE,
            EPermissionScope::DELETE_PACKAGE,
            EPermissionScope::READ_WORD,
            EPermissionScope::CREATE_WORD,
            EPermissionScope::UPDATE_WORD,
            EPermissionScope::DELETE_WORD,
            EPermissionScope::READ_INVOICE,
            EPermissionScope::READ_USERS,
            EPermissionScope::UPDATE_USER,
            EPermissionScope::UPDATE_USER_SUBSCRIPTION,
            EPermissionScope::CREATE_COURSE,
            EPermissionScope::UPDATE_COURSE,
            EPermissionScope::DELETE_COURSE,
            EPermissionScope::ADD_BLOCK_COURSE,
            EPermissionScope::MOVE_BLOCK_COURSE,
            EPermissionScope::REMOVE_BLOCK_COURSE,
            EPermissionScope::UPDATE_BLOCK_COURSE,
            EPermissionScope::MANAGE_IMAGE_BLOCK_COURSE,
            EPermissionScope::MANAGE_SENTENCE_BLOCK_COURSE,
            EPermissionScope::MANAGE_VIDEO_BLOCK_COURSE,
        ],
        EUserRole::CONTENT_CREATOR->value => [
            EPermissionScope::READ_PACKAGE,
            EPermissionScope::CREATE_PACKAGE,
            EPermissionScope::UPDATE_PACKAGE,
            EPermissionScope::READ_WORD,
            EPermissionScope::CREATE_WORD,
            EPermissionScope::UPDATE_WORD,
        ],
        EUserRole::CONTENT_MANAGER->value => [
            EPermissionScope::READ_PACKAGE,
            EPermissionScope::CREATE_PACKAGE,
            EPermissionScope::UPDATE_PACKAGE,
            EPermissionScope::DELETE_PACKAGE,
            EPermissionScope::READ_WORD,
            EPermissionScope::CREATE_WORD,
            EPermissionScope::UPDATE_WORD,
            EPermissionScope::DELETE_WORD,
        ],
    ];

    public const CLASS_MAP = [
        "baseklass" => Baseklass::class,
        "word" => Word::class,
        "plan" => SubPlan::class,
        "course" => Course::class,
    ];
}
