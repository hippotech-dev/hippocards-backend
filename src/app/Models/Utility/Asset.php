<?php

namespace App\Models\Utility;

use App\Enums\EAssetType;
use App\Enums\EAssetUploadType;
use App\Enums\EStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;
    public $table = "v3_assets";
    public $timestamps = false;

    protected $fillable = [
        "path",
        "thumbnail_path",
        "name",
        "size",
        "mime_type",
        "metadata",
        "transcoder_job_id",
        'vdo_drm_video_id',
        "vdo_drm_video_status",
        "upload_type"
    ];

    public $casts = [
        "metadata" => "array",
        "upload_type" => EAssetUploadType::class,
        "vdo_drm_video_status" => EStatus::class,
    ];
}
