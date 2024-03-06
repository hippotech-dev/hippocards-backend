<?php

namespace App\Http\Resources\Web\Academy;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseExamResultResource extends JsonResource
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
            "type" => $this->type,
            "v3_course_block_id" => $this->v3_course_block_id,
            "v3_course_group_id" => $this->v3_course_group_id,
            "v3_course_id" => $this->v3_course_id,
            "v3_course_exam_instance_id" => $this->v3_course_exam_instance_id,
            "user_id" => $this->user_id,
            "total_points" => $this->total_points,
            "total_received_points" => $this->total_received_points,
            "status" => $this->status
        ];
    }
}
