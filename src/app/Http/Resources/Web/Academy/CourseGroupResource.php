<?php

namespace App\Http\Resources\Web\Academy;

use App\Enums\ECourseBlockType;
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
        if (!is_null($this->blocks)) {
            $index = 0;
            foreach ($this->blocks as &$block) {
                if ($block->type !== ECourseBlockType::LESSON) {
                    continue;
                }
                $block->order = $index;
                $index++;
            }
        }
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
