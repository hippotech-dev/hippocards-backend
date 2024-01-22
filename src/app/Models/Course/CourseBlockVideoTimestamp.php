<?php

namespace App\Models\Course;

use App\Enums\ECourseBlockVideoTimestampType;
use Illuminate\Database\Eloquent\Model;

class CourseBlockVideoTimestamp extends Model
{
    public $table = "v3_course_video_timestamps";
    public $timestamps = false;

    protected $fillables = [
        "type",
        "content",
        "start",
        "end",
        "v3_course_video_id",
    ];

    public $casts = [
        "content" => "array",
        "type" => ECourseBlockVideoTimestampType::class
    ];

    public function courseGroupBlock()
    {
        return $this->belongsTo(CourseBlockVideo::class);
    }
}
