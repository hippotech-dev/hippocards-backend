<?php

namespace App\Http\Resources\Utility;

use App\Enums\EPromoType;
use App\Http\Resources\System\Academy\CourseResource;
use App\Http\Resources\System\Utility\SubscriptionPlanResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PromoResource extends JsonResource
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
            "object_id" => $this->object_id,
            "object_type" => $this->object_type,
            "code" => $this->code,
            "type" => $this->type,
            "usage_type" => $this->usage_type,
            "total_quantity" => $this->total_quantity,
            "status" => $this->status,
            "description" => $this->description,
            "amount" => $this->amount,
            "amount_type" => $this->amount_type,
            "context_type" => $this->context_type,
            "object" => $this->whenLoaded("object", function () {
                if (is_null($this->object)) {
                    return null;
                }
                switch ($this->type) {
                    case EPromoType::ACADEMY_COURSE:

                        return new CourseResource($this->object);
                    case EPromoType::SUBSCIPRIPTION:
                        return new SubscriptionPlanResource($this->object);
                    default:
                        return null;
                }
            })
        ];
    }
}
