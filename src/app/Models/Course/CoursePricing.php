<?php

namespace App\Models\Course;

use Illuminate\Database\Eloquent\Model;

class CoursePricing extends Model
{
    public $table = "v3_course_pricings";
    public $timestamps = false;

    protected $fillable = [
        "course_id",
        "price",
        "price_string",
        "duration_days"
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
