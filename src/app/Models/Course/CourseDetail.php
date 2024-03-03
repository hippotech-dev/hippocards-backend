<?php

namespace App\Models\Course;

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
    ];

    public $casts = [
        "content" => "array"
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
