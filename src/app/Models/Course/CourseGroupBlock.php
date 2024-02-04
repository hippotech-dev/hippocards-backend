<?php

namespace App\Models\Course;

use App\Enums\ECourseBlockType;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Collection\Sort;

class CourseGroupBlock extends Model
{
    public $table = "v3_course_group_block";
    public $timestamps = false;

    protected $fillable = [
        "v3_course_id",
        "v3_course_group_id",
        "sort_id",
        "name",
        "type",
        "order"
    ];

    public $casts = [
        "type" => ECourseBlockType::class
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, "v3_course_id");
    }

    public function group()
    {
        return $this->belongsTo(CourseGroup::class, "v3_course_group_id");
    }

    public function wordSort()
    {
        return $this->belongsTo(Sort::class);
    }

    public function videos()
    {
        return $this->hasMany(CourseBlockVideo::class);
    }
}
