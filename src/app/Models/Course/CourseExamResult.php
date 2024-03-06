<?php

namespace App\Models\Course;

use App\Enums\ECourseBlockType;
use App\Enums\EStatus;
use Illuminate\Database\Eloquent\Model;

class CourseExamResult extends Model
{
    public $table = "v3_course_exam_results";

    protected $fillable = [
        "type",
        "status",
        "total_points",
        "total_received_points",
        "v3_course_block_id",
        "v3_course_group_id",
        "v3_course_id",
        "user_id",
        "v3_course_exam_instance_id",
    ];

    public $casts = [
        "type" => ECourseBlockType::class,
        "status" => EStatus::class
    ];

    public function courseBlock()
    {
        return $this->belongsTo(CourseGroupBlock::class);
    }

    public function course()
    {
        return $this->belongsTo(CourseGroupBlock::class);
    }
}
