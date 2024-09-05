<?php

namespace App\Models\Utility;

use App\Enums\EPromoAmountType;
use App\Enums\EPromoContextType;
use App\Enums\EPromoType;
use App\Enums\EPromoUsageType;
use App\Enums\EStatus;
use App\Models\Payment\PaymentInvoice;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    use HasFactory;

    public $table = "v3_promo_codes";

    protected $fillable = [
        "object_id",
        "object_type",
        "code",
        "type",
        "usage_type",
        "total_quantity",
        "total_used",
        "status",
        "description",
        "amount",
        "amount_type",
        "context_type",
        "expires_at"
    ];

    protected $casts = [
        "amount_type" => EPromoAmountType::class,
        "type" => EPromoType::class,
        "usage_type" => EPromoUsageType::class,
        "status" => EStatus::class,
        "context_type" => EPromoContextType::class,
    ];

    public function usages()
    {
        return $this->hasMany(PromoUsage::class, "v3_promo_code_id");
    }

    public function object()
    {
        return $this->morphTo("object", "object_type", "object_id");
    }

    public function invoices()
    {
        return $this->hasMany(PaymentInvoice::class, "v3_promo_code_id");
    }
}
