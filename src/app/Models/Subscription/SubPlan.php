<?php

namespace App\Models\Subscription;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubPlan extends Model
{
    use HasFactory;

    public $table = "sub_plan";
    public $timestamps = false;

    protected $fillable = [
        "name",
        "name_mn",
        "price",
        "monthly_price",
        "plan_dur_day",
        "image",
        "image_mobile",
        "color",
        "note",
        "is_paid",
        "is_subscription",
        "featured"
    ];

    public $casts = [
        "featured" => "boolean",
        "is_paid" => "boolean",
        "is_subscription" => "boolean"
    ];
}
