<?php

namespace App\Models\Course;

use App\Enums\EStatus;
use Illuminate\Database\Eloquent\Model;

class CourseCompletion extends Model
{
    public $table = "v3_course_completions";

    protected $fillable = [
        "status",
        "user_id",
        "v3_course_id",
        "v3_course_group_block_id"
    ];

    protected $casts = [
        "status" => EStatus::class,
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function courseGroupBlocks()
    {
        return $this->belongsTo(CourseGroupBlock::class);
    }
}
