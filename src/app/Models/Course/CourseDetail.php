<?php

namespace App\Models;

use App\Casts\Json;
use Illuminate\Database\Eloquent\Model;

class CourseDetail extends Model
{
    protected $fillable = [
        "course_id",
        "content"
    ];

    public $casts = [
        "content" => "array"
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
