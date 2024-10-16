<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSession extends Model
{
    use HasFactory;

    public $table = "v3_user_sessions";

    protected $fillable = [
        "user_id",
        "origin",
        "access_token",
        "v3_web_browser_id",
        "last_access_at",
    ];
}
