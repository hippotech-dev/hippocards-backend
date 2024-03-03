<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentOrderItem extends Model
{
    use HasFactory;
    public $table = "v3_payment_order_items";

    protected $fillable = [
        "user_id",
        "v3_payment_order_id",
        "amount",
        "object_type",
        "object_id",
    ];

    public function paymentOrder()
    {
        return $this->belongsTo(PaymentOrder::class, "v3_payment_order_id");
    }

    public function payable()
    {
        return $this->morphTo("object", "object_type", "object_id");
    }
}
