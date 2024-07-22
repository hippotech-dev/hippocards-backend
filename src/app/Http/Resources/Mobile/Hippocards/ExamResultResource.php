<?php

namespace App\Http\Resources\Mobile\Hippocards;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamResultResource extends JsonResource
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
            "user_id" => $this->user_id,
            "package_id" => $this->baseklass_id,
            "word" => new WordResource($this->whenLoaded("word"))
        ];
    }
}
