<?php

namespace App\Http\Resources\Web\Academy;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseExamInstanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    public $preserveKeys = true;

    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "type" => $this->type,
            "questions" => $this->questions ?? [],
            "answers" => $this->answers ?? [],
            "v3_course_block_id" => $this->v3_course_block_id,
            "v3_course_group_id" => $this->v3_course_group_id,
            "v3_course_id" => $this->v3_course_id,
            "user_id" => $this->user_id,
            "start_time" => $this->start_time,
            "end_time" => $this->end_time,
            "current_question_number" => $this->current_question_number ?? 0,
            "total_questions" => $this->total_questions,
        ];
    }
}
