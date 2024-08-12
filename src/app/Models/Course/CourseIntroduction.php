<?php

namespace App\Models\Course;

use App\Models\Utility\Asset;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseIntroduction extends Model
{
    use HasFactory;

    public $table = "v3_course_introductions";

    protected $fillable = [
        "v3_course_id",
        "v3_video_asset_id",
        "video_asset_path",
        "content"
    ];

    protected $casts = [
        "content" => "array"
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, "v3_course");
    }

    public function videoAsset()
    {
        return $this->belongsTo(Asset::class, "v3_video_asset_id");
    }
}
