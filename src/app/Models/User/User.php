<?php

namespace App\Models\User;

use App\Enums\EUserLoginType;
use App\Enums\EUserRole;
use App\Models\SSO\OAuthAuthenticationAttempt;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'name',
        'image',
        'email',
        'password',
        'role_id',
        'phone',
        'sex',
        'birth_year',
        'device_name',
        'model',
        'device_id',
        'fid',
        'ftoken',
        'logged_in',
        'is_authorized',
        'country_id',
        'is_guest',
        'login_type',
        'new_role',
        "code_push",
        "verify",
        "apl_tkn"
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public $casts = [
        "login_type" => EUserLoginType::class,
        "role_id" => EUserRole::class,
        "new_role" => EUserRole::class,
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function authenticationAttempts()
    {
        return $this->hasMany(OAuthAuthenticationAttempt::class);
    }
}
