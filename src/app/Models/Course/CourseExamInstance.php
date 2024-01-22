<?php

namespace App\Models\Course;

use Illuminate\Database\Eloquent\Model;

class CourseExamInstance extends Model
{
    public $table = "v3_course_exam_instances";

    protected $fillable = [
        "type",
        "questions",
        "v3_course_group_block_id"
    ];

    public $casts = [
        "questions" => "array"
    ];

    public function courseGroupBlock()
    {
        return $this->belongsTo(CourseGroupBlock::class);
    }
}
