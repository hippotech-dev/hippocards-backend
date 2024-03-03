<?php

namespace App\Enums;

enum EPaymentOrderItemType: string
{
    case ACADEMY_COURSE = "ACADEMY_COURSE";
    case APP_SUBSCRIPTION_PLAN = "APP_SUBSCRIPTION_PLAN";
}
