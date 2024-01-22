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
        "course_id",
        "course_group_id",
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
        return $this->belongsTo(Course::class);
    }

    public function group()
    {
        return $this->belongsTo(CourseGroup::class);
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
