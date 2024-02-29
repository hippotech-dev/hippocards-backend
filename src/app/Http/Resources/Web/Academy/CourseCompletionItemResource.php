<?php

namespace App\Http\Resources\Web\Academy;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseCompletionItemResource extends JsonResource
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
            "v3_course_completion_id" => $this->v3_course_completion_id,
            "v3_course_group_id" => $this->v3_course_group_id,
            "v3_course_block_id" => $this->v3_course_block_id,
            "status" => $this->status,
        ];
    }
}
