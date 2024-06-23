<?php

namespace App\Models\Utility;

use App\Enums\EUserActivityAction;
use App\Enums\EUserActivityType;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    use HasFactory;

    public $table = "v3_user_activities";

    protected $fillable = [
        "user_id",
        "object_id",
        "object_type",
        "action",
        "type",
        "metadata",
        "updated_at"
    ];

    protected $casts = [
        "action" => EUserActivityAction::class,
        "type" => EUserActivityType::class,
    ];

    /**
     * Relations
     */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function object()
    {
        return $this->morphTo("object", "object_type", "object_id");
    }
}
