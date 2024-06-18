<?php

namespace App\Http\Resources\Web\Academy;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserCourseResource extends JsonResource
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
            "start" => $this->start,
            "end" => $this->end,
            "v3_course_id" => $this->v3_course_id,
            "course" => new CourseResource($this->whenLoaded("course")),
        ];
    }
}
