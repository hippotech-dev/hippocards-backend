<?php

namespace App\Models\Course;

use App\Enums\ELanguageLevel;
use App\Models\Package\Baseklass;
use App\Models\Utility\Language;
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
        "level",
        "status"
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

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function packages()
    {
        return $this->belongsToMany(
            Baseklass::class,
            "v3_course_packages",
            "v3_course_id",
            "package_id"
        );
    }

    public function packagePivots()
    {
        return $this->hasMany(CoursePackage::class, "v3_course_id");
    }

    public function completions()
    {
        return $this->hasMany(CourseCompletion::class, "v3_course_id");
    }

    public function introduction()
    {
        return $this->hasOne(CourseIntroduction::class, "v3_course_id");
    }
}
