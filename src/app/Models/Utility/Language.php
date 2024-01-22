<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $table = "language";
    protected $fillable = [
        "name",
        "color",
        "image",
        "is_active",
        "sort",
        "azure",
        "neural_name",
        "neural_name_female",
        "neural_name_male",
        "rate",
        "has_placement",
        "placement_time",
        "is_hiragana"
    ];
}
