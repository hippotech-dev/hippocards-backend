<?php

namespace App\Models\Course;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseCompletionItem extends Model
{
    use HasFactory;
    public $table = "v3_course_completion_items";

    protected $fillable = [
        "v3_user_course_id",
        "v3_course_completion_id",
        "v3_course_group_id",
        "v3_course_block_id",
        "status"
    ];
}
