<?php

namespace App\Models\Course;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseBlockResponse extends Model
{
    use HasFactory;

    public $table = "v3_course_block_responses";

    protected $fillable = [
        "user_id",
        "v3_course_id",
        "v3_course_completion_id",
        "v3_course_block_id",
        "type",
        "keyword",
        "sentence_hint",
        "sentence_translation",
        "sentence",
    ];
}
