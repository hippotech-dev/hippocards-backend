<?php

namespace App\Models\Course;

use Illuminate\Database\Eloquent\Model;

class CourseGroup extends Model
{
    public $table = "v3_course_groups";
    public $timestamps = false;

    protected $fillable = [
        "v3_course_id",
        "name",
        "type",
        "order"
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, "v3_course_id");
    }

    public function blocks()
    {
        return $this->hasMany(CourseGroupBlock::class, "v3_course_group_id");
    }
}
