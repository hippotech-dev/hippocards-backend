<?php

namespace App\Models\Course;

use Illuminate\Database\Eloquent\Model;

class CourseExamResult extends Model
{
    public $table = "v3_course_exam_results";

    protected $fillable = [
        "answers",
        "v3_course_exam_instance_id",
        "user_id"
    ];

    public $casts = [
        "answers" => "array"
    ];

    public function courseExamInstance()
    {
        return $this->belongsTo(CourseExamInstance::class);
    }
}
