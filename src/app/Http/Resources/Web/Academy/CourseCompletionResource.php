<?php

namespace App\Http\Resources\Web\Academy;

use App\Models\Course\CourseCompletionItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseCompletionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "v3_user_course_id" => $this->v3_user_course_id,
            "v3_course_id" => $this->v3_course_id,
            "current_group_id" => $this->current_group_id,
            "current_block_id" => $this->current_block_id,
            "progress" => $this->progress,
            "is_final_exam_finished" => $this->is_final_exam_finished,
            "items" => CourseCompletionItemResource::collection($this->whenLoaded("items"))
        ];
    }
}
