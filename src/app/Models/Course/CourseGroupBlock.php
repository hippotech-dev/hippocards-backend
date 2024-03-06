<?php

namespace App\Models\Course;

use App\Enums\ECourseBlockType;
use App\Models\Package\Sort;
use Illuminate\Database\Eloquent\Model;

class CourseGroupBlock extends Model
{
    public $table = "v3_course_group_blocks";
    public $timestamps = false;

    protected $fillable = [
        "v3_course_id",
        "v3_course_group_id",
        "sort_id",
        "word_id",
        "package_id",
        "name",
        "type",
        "order",
        "metadata"
    ];

    public $casts = [
        "type" => ECourseBlockType::class,
        "metadata" => "array"
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
        return $this->belongsTo(Sort::class, "sort_id");
    }

    public function videos()
    {
        return $this->hasMany(CourseBlockVideo::class, "v3_course_group_block_id");
    }
}
