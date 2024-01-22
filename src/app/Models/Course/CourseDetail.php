<?php

namespace App\Models\Course;

use Illuminate\Database\Eloquent\Model;

class CourseDetail extends Model
{
    public $table = "v3_course_details";
    public $timestamps = false;

    protected $fillable = [
        "course_id",
        "content"
    ];

    public $casts = [
        "content" => "array"
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
