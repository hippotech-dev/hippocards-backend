<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Model;

class Paragraph extends Model
{
    public $timestamps = false;

    protected $fillable = [
        "object_id",
        "object_type",
        "value",
        "translation",
        "type",
        "order",
        "is_highlighted",
    ];

    protected $casts = [
        "is_highlighted" => "boolean",
    ];

    public function object()
    {
        return $this->morphTo("object", "object_type", "object_id");
    }
}
