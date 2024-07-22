<?php

namespace App\Models\Package;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPackageProgress extends Model
{
    use HasFactory;

    public $table = "v3_user_package_progresses";

    protected $fillable = [
        "user_id",
        "package_id",
        "progress",
        "package_word_count",
        "total_exam_count",
        "total_final_exam_count"
    ];

    /**
     * Relations
     */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(Baseklass::class);
    }
}
