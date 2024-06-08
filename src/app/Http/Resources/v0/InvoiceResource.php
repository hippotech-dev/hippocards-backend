<?php

namespace App\Http\Resources\v0;

use App\Http\Resources\System\Content\UserResource;
use App\Http\Services\v0\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
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
            "user_id" => $this->user_id,
            "type" => $this->type,
            "object_id" => $this->object_id,
            "price" => $this->price,
            "promo" => $this->is_promo,
            "result_code" => $this->result_code,
            "merchant_code" => $this->merchant_code,
            "object_type" => $this->object_type,
            "method" => $this->method,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "status" => PaymentService::getInvoiceStatus($this),
            "user" => new UserResource($this->whenLoaded("user"))
        ];
    }
}
