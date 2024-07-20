<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    public $timestamps = false;

    protected $fillable = [
        "object_id",
        "object_type",
        "category_id",
        "value",
        "difficulty",
        "image",
        "audio",
        "duration",
        "type"
    ];
}
