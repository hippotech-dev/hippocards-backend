<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;
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

    protected $casts = [
        "is_hiragana" => "boolean",
        "is_active" => "boolean"
    ];
}
