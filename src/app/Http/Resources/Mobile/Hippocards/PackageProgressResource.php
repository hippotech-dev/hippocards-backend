<?php

namespace App\Http\Resources\Mobile\Hippocards;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageProgressResource extends JsonResource
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
            "user_id" => $this->id,
            "progress" => $this->progress,
            "package_word_count" => $this->package_word_count,
            "total_exam_count" => $this->total_exam_count,
            "total_final_exam_count" => $this->total_final_exam_count,
            "package" => new PackageResource($this->whenLoaded("package")),
        ];
    }
}
