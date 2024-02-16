<?php

namespace App\Models\Course;

use App\Models\Utility\Asset;
use Illuminate\Database\Eloquent\Model;

class CourseBlockVideo extends Model
{
    public $table = "v3_course_block_videos";
    public $timestamps = false;

    protected $fillable = [
        "type",
        "duration",
        "v3_course_group_block_id",
        "v3_asset_id"
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class, "v3_asset_id");
    }

    public function courseGroupBlock()
    {
        return $this->belongsTo(CourseGroupBlock::class);
    }

    public function videoTimestamps()
    {
        return $this->hasMany(CourseBlockVideoTimestamp::class, "v3_course_video_id");
    }
}
