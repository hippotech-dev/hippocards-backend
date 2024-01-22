<?php

namespace App\Models\Utility;

use App\Enums\EConfirmationType;
use App\Enums\EStatus;
use Illuminate\Database\Eloquent\Model;

class EmailConfirmation extends Model
{
    protected $table = 'email_comfirmation';

    protected $fillable = [
        "email",
        "code",
        "status",
        "user_id",
        "email_token",
        "body",
        "type"
    ];

    public $casts = [
        "status" => EStatus::class,
        "type" => EConfirmationType::class
    ];
}
