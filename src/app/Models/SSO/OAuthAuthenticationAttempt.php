<?php

namespace App\Models\SSO;

use App\Enums\ECodeChallengeMethod;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OAuthAuthenticationAttempt extends Model
{
    use HasFactory;

    public $table = "v3_oauth_authentication_attempts";

    protected $fillable = [
        "code",
        "redirect_uri",
        "state",
        "challenge",
        "challenge_method",
        "scopes",
        "user_id",
        "v3_oauth_client_id"
    ];

    public $casts = [
        "scopes" => "array",
        "challenge_method" => ECodeChallengeMethod::class
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(OAuthClient::class);
    }
}
