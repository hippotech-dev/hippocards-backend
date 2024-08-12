<?php

namespace App\Http\Resources\System\Academy;

use App\Enums\EStatus;
use App\Http\Resources\Utility\LanguageResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
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
            "description" => $this->description,
            "thumbnail" => append_s3_path($this->thumbnail),
            "level" => $this->level,
            "additional" => $this->additional,
            "status" => $this->status ?? EStatus::PENDING,
            "total_block" => $this->blocks_count,
            "detail" => new CourseDetailResource($this->whenLoaded("detail")),
            "introduction" => new CourseIntroductionResource($this->whenLoaded("introduction")),
            "language" => new LanguageResource($this->whenLoaded("language")),
            "groups" => CourseGroupResource::collection($this->whenLoaded("groups")),
            "packages" => PackageResource::collection($this->whenLoaded("packages"))
        ];
    }
}
