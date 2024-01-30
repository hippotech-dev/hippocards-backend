<?php

namespace App\Models\Course;

use App\Enums\ELanguageLevel;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    public $table = "v3_courses";

    protected $fillable = [
        "name",
        "description",
        "thumbnail",
        "v3_thumbnail_asset_id",
        "language_id",
        "author_id",
        "additional",
        "level"
    ];

    protected $casts = [
        "level" => ELanguageLevel::class,
        "additional" => "array"
    ];

    public function detail()
    {
        return $this->hasOne(CourseDetail::class, "v3_course_id");
    }

    public function groups()
    {
        return $this->hasMany(CourseGroup::class, "v3_course_id");
    }

    public function blocks()
    {
        return $this->hasMany(CourseGroupBlock::class, "v3_course_id");
    }
}
