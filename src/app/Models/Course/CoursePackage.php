<?php

namespace App\Models\Course;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoursePackage extends Model
{
    use HasFactory;
    public $table = "v3_course_packages";
    public $timestamps = false;

    protected $fillable = [
        "v3_course_id",
        "package_id",
        "order"
    ];
}
