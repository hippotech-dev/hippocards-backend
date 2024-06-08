<?php

namespace App\Models\v0;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QpayInvoice extends Model
{
    use HasFactory;

    public $table = "qpay_invoice";

    protected $fillable = [
        "user_id",
        "object_id",
        "price",
        "result_code",
        "result_msg",
        "seen",
        "type",
        "is_promo",
        "is_candy",
        "is_gift",
        "status",
        "object_type",
        "discount_type",
        "merchant_code"
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopePending($query)
    {
        return $query
            ->where("status", 1)
            ->where("result_code", 1);
    }

    public function scopePaid($query)
    {
        return $query
            ->where("status", 1)
            ->where("result_code", 0);
    }
}
