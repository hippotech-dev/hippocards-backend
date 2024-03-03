<?php

namespace App\Models\Payment;

use App\Enums\EPaymentMethodType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentInvoiceResponse extends Model
{
    use HasFactory;
    public $table = "v3_payment_invoice_responses";

    protected $fillable = [
        "identifier",
        "payment_method",
        "content",
        "status_code",
        "v3_payment_invoice_id",
        "v3_payment_order_id",
    ];

    protected $casts = [
        "content" => "array",
        "payment_method" => EPaymentMethodType::class,
    ];

    public function paymentOrder()
    {
        return $this->belongsTo(PaymentOrder::class, "v3_payment_order_id");
    }

    public function invoice()
    {
        return $this->belongsTo(PaymentInvoice::class, "v3_payment_invoice_id");
    }
}
