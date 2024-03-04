<?php

namespace App\Http\Resources\Web\Payment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentInvoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "identifier" => $this->identifier,
            "user_id" => $this->user_id,
            "total_amount" => $this->total_amount,
            "total_pending_amount" => $this->total_pending_amount,
            "total_paid_amount" => $this->total_paid_amount,
            "merchant_invoice_id" => $this->merchant_invoice_id,
            "merchant_payment_id" => $this->merchant_payment_id,
            "status" => $this->status,
            "order" => new PaymentOrderResource($this->whenLoaded("paymentOrder"))
        ];
    }
}
