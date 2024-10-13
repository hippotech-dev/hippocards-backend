<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserWebBrowser extends Model
{
    use HasFactory;

    public $table = "v3_web_browsers";

    protected $fillables = [
        "user_id",
        "device_id",
        "user_agent",
        "screen_width",
        "screen_height",
        "language",
    ];
}
