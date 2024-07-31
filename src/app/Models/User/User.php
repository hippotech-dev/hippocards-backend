<?php

namespace App\Models\User;

use App\Enums\EUserLoginType;
use App\Enums\EUserRole;
use App\Models\Course\CourseCertificate;
use App\Models\Package\UserCustomWordDetail;
use App\Models\Payment\PaymentInvoice;
use App\Models\Payment\PaymentOrder;
use App\Models\SSO\OAuthAuthenticationAttempt;
use App\Models\Subscription\SubUser;
use App\Models\Utility\Favorite;
use App\Models\Utility\UserActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use HasFactory;
    use HasApiTokens;

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
        "apl_tkn",
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public $casts = [
        "login_type" => EUserLoginType::class,
        "role_id" => EUserRole::class,
        "new_role" => EUserRole::class,
        "is_guest" => "boolean"
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

    /**
     * Relations
     */

    public function authenticationAttempts()
    {
        return $this->hasMany(OAuthAuthenticationAttempt::class);
    }

    public function invoices()
    {
        return $this->hasMany(PaymentInvoice::class);
    }

    public function paymentOrders()
    {
        return $this->hasMany(PaymentOrder::class);
    }

    public function courseCertificates()
    {
        return $this->hasMany(CourseCertificate::class);
    }

    public function subscription()
    {
        return $this->hasOne(SubUser::class, "user_id");
    }

    public function activities()
    {
        return $this->hasMany(UserActivity::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function customWordDetails()
    {
        return $this->hasMany(UserCustomWordDetail::class, "user_id");
    }

    public function preferences()
    {
        return $this->hasMany(UserPreference::class, "user_id");
    }

    /**
     * Scopes
     */
}
