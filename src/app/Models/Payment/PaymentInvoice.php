<?php

namespace App\Models\Payment;

use App\Enums\EPaymentMethodType;
use App\Enums\EStatus;
use App\Models\User\User;
use App\Models\Utility\PromoCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentInvoice extends Model
{
    use HasFactory;
    public $table = "v3_payment_invoices";

    protected $fillable = [
        "identifier",
        "total_amount",
        "total_pending_amount",
        "total_paid_amount",
        "total_discount_amount",
        "payment_method",
        "status",
        "user_id",
        "merchant_payment_id",
        "merchant_invoice_id",
        "redirect_uri",
        "v3_payment_order_id",
        "v3_promo_code_id",
        "v3_promo_code_value"
    ];

    protected $casts = [
        "payment_method" => EPaymentMethodType::class,
        "status" => EStatus::class,
    ];

    public function paymentOrder()
    {
        return $this->belongsTo(PaymentOrder::class, "v3_payment_order_id");
    }

    public function responses()
    {
        return $this->hasMany(PaymentInvoiceResponse::class, "v3_payment_invoice_id");
    }

    public function promoCode()
    {
        return $this->belongsTo(PromoCode::class, "v3_promo_code_id");
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
