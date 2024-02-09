<?php

namespace App\Http\Resources\System\Academy;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseDetailResource extends JsonResource
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
            "price" => $this->price,
            "duration_days" => $this->duration_days,
            "content" => $this->content ?? []
        ];
    }
}
