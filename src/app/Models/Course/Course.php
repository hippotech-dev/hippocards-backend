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
        "thumbnail_asset_id",
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
        return $this->hasOne(CourseDetail::class);
    }

    public function pricing()
    {
        return $this->hasOne(CoursePricing::class);
    }

    public function groups()
    {
        return $this->hasMany(CourseGroup::class);
    }

    public function blocks()
    {
        return $this->hasMany(CourseGroupBlock::class);
    }
}
