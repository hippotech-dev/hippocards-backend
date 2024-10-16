<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserWebBrowser extends Model
{
    use HasFactory;

    public $table = "v3_user_web_browsers";

    protected $fillable = [
        "user_id",
        "device_id",
        "origin",
        "user_agent",
        "screen_width",
        "screen_height",
        "language",
        "platform",
        "timezone"
    ];
}
