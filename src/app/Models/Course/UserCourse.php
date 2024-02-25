<?php

namespace App\Models\Course;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCourse extends Model
{
    use HasFactory;

    public $table = "v3_user_courses";

    protected $fillable = [
        "start",
        "end",
        "user_id",
        "v3_course_id"
    ];
}
