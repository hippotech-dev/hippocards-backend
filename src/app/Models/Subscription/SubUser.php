<?php

namespace App\Models\Subscription;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubUser extends Model
{
    use HasFactory;

    public $table = "sub_user";

    protected $fillable = [
        "user_id",
        "plan_id",
        "active_until",
        "is_paid",
        "note",
        "sale_percent"
    ];
}
