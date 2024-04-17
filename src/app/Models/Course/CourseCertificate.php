<?php

namespace App\Models\Course;

use App\Models\Utility\Asset;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseCertificate extends Model
{
    use HasFactory;

    public $table = "v3_course_certificates";

    protected $fillable = [
        "issue_date",
        "user_id",
        "v3_course_id",
        "v3_course_exam_instance_id",
        "v3_asset_id",
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, "v3_course_id");
    }

    public function courseExamInstance()
    {
        return $this->belongsTo(Course::class, "v3_course_exam_instance_id");
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class, "v3_asset_id");
    }
}
