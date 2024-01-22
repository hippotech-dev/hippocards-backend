<?php

namespace App\Models\SSO;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OAuthClient extends Model
{
    use HasFactory;

    public $table = "v3_oauth_clients";

    protected $fillable = [
        "name",
        "client_id",
        "client_secret",
        "is_active"
    ];


}
