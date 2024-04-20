<?php

namespace App\Http\Resources\Web\Academy;

use App\Http\Resources\System\Academy\BlockVideoResource;
use App\Http\Resources\System\Academy\WordSortResource;
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
            "order" => $this->order,
            "v3_course_group_id" => $this->v3_course_group_id,
            "word_sort" => new WordSortResource($this->whenLoaded("wordSort")),
            "videos" => BlockVideoResource::collection($this->whenLoaded("videos"))
        ];
    }
}
