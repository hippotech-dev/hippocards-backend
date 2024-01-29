<?php

namespace App\Models\Utility;

use App\Enums\EAssetType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;
    public $table = "v3_assets";
    public $timestamps = false;

    protected $fillable = [
        "path",
        "size",
        "mime_type",
        "metadata"
    ];

    public $casts = [
        "metadata" => "array"
    ];
}
