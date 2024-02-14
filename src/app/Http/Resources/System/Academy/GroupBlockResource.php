<?php

namespace App\Http\Resources\System\Academy;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupBlockResource extends JsonResource
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
            "type" => $this->type,
            "v3_course_group_id" => $this->v3_course_group_id,
            "word_sort" => new WordSortResource($this->whenLoaded("wordSort")),
        ];
    }
}
