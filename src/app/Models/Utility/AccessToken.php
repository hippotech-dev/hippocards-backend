<?php

namespace App\Models\Utility;

use App\Enums\EAccessTokenType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessToken extends Model
{
    use HasFactory;
    public $table = "v3_access_tokens";

    protected $fillable = [
        "access_token",
        "refresh_token",
        "access_expire",
        "refresh_expire",
        "type"
    ];

    protected $casts = [
        "access_expire" => "datetime",
        "refresh_expire" => "datetime",
        "type" => EAccessTokenType::class
    ];
}
