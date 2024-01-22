<?php

namespace App\Models\Utility;

use App\Enums\EAssetType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;
    public $table = "v3_assets";

    protected $fillable = [
        "path",
        "size",
        "type",
        "metadata"
    ];

    public $casts = [
        "type" => EAssetType::class,
        "metadata" => "array"
    ];
}
