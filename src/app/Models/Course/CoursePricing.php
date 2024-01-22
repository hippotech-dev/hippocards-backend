<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoursePricing extends Model
{
    protected $fillable = [
        "course_id",
        "price",
        "price_string"
    ];

    public function course() {
        return $this->belongsTo(Course::class);
    }
}
