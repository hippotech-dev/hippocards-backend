<?php

namespace App\Http\Resources\System\Academy;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseGroupResource extends JsonResource
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
            "name" => $this->name,
            "order" => $this->order,
            "type" => $this->type,
            "v3_course_id" => $this->v3_course_id,
            "blocks" => GroupBlockResource::collection($this->whenLoaded("blocks")),
            "cardIds" => $this->when(!is_null($this->cardIds), $this->cardIds)
        ];
    }
}
