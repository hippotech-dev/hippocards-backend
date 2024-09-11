<?php

namespace App\Models\Payment;

use App\Enums\EPaymentOrderType;
use App\Enums\EStatus;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentOrder extends Model
{
    use HasFactory;
    public $table = "v3_payment_orders";

    protected $fillable = [
        "user_id",
        "status",
        "type",
        "total_amount",
        "total_discount_amount",
        "total_items",
        "number",
        "v3_promo_code_id"
    ];

    protected $casts = [
        "status" => EStatus::class,
        "type" => EPaymentOrderType::class,
    ];

    public function invoice()
    {
        return $this->hasOne(PaymentInvoice::class, "v3_payment_order_id");
    }

    public function items()
    {
        return $this->hasMany(PaymentOrderItem::class, "v3_payment_order_id");
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
