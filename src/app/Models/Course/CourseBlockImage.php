<?php

namespace App\Models\Course;

use App\Enums\ECourseBlockImageType;
use App\Models\Utility\Asset;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseBlockImage extends Model
{
    use HasFactory;
    public $table = "v3_course_block_images";

    protected $fillable = [
        "type",
        "v3_course_group_block_id",
        "path",
        "v3_asset_id"
    ];

    protected $casts = [
        "type" => ECourseBlockImageType::class
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class, "v3_asset_id");
    }

    public function courseGroupBlock()
    {
        return $this->belongsTo(CourseGroupBlock::class);
    }
}
