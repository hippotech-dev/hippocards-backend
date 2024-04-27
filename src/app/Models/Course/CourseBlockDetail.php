<?php

namespace App\Models\Course;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseBlockDetail extends Model
{
    use HasFactory;

    public $table = "v3_course_block_details";

    protected $fillable = [
        "sentences",
        "keywords",
        "v3_course_id",
        "v3_coure_block_id"
    ];

    protected $casts = [
        "sentences" => "array",
        "keywords" => "array",
    ];
}
