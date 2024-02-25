<?php

namespace App\Models\Course;

use App\Enums\EStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseCompletion extends Model
{
    use HasFactory;
    public $table = "v3_course_completions";

    protected $fillable = [
        "v3_user_course_id",
        "v3_course_id",
        "current_group_id",
        "current_block_id",
        "progress",
        "is_final_exam_finished"
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
