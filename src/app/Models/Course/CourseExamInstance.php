<?php

namespace App\Models\Course;

use App\Enums\ECourseBlockType;
use Illuminate\Database\Eloquent\Model;

class CourseExamInstance extends Model
{
    public $table = "v3_course_exam_instances";

    protected $fillable = [
        "type",
        "questions",
        "answers",
        "v3_course_block_id",
        "v3_course_group_id",
        "v3_course_id",
        "user_id",
        "start_time",
        "end_time",
        "current_question_number",
        "total_questions"
    ];

    public $casts = [
        "type" => ECourseBlockType::class,
        "questions" => "array",
        "answers" => "array",
        "start_time" => "datetime",
        "end_time" => "datetime"
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
