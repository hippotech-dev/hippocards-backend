<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoUsage extends Model
{
    use HasFactory;

    public $table = "v3_promo_usages";

    protected $fillable = [
        "user_id",
        "v3_promo_code_id",
        "object_id",
        "object_type",
    ];

    public function object()
    {
        return $this->morphTo("object", "object_type", "object_id");
    }
}
