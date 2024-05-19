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
        "name",
        "size",
        "mime_type",
        "metadata",
        "transcoder_job_id",
    ];

    public $casts = [
        "metadata" => "array"
    ];
}
