<?php

namespace App\Models\Course;

use App\Models\Utility\Asset;
use Illuminate\Database\Eloquent\Model;

class CourseDetail extends Model
{
    public $table = "v3_course_details";
    public $timestamps = false;

    protected $fillable = [
        "v3_course_id",
        "content",
        "price",
        "price_string",
        "duration_days",
        "total_blocks",
        "about_video_path",
        "v3_about_video_asset_id",
    ];

    public $casts = [
        "content" => "array"
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function aboutVideoAsset()
    {
        return $this->belongsTo(Asset::class, "v3_about_video_asset_id");
    }
}
