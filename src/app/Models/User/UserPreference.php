<?php

namespace App\Models\User;

use App\Enums\EUserPreferenceType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    use HasFactory;

    public $table = "v3_user_preferences";

    protected $fillable = [
        "user_id",
        "type",
        "value"
    ];

    protected $casts = [
        "type" => EUserPreferenceType::class,
        "value" => "array",
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
