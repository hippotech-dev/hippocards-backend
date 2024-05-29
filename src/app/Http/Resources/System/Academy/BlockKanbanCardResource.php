<?php

namespace App\Http\Resources\System\Academy;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlockKanbanCardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $metadata = $this->metadata ?? [];
        return [
            "id" => $this->id,
            "name" => $this->name,
            "type" => $this->type,
            "v3_course_group_id" => $this->v3_course_group_id,
            "metadata" => [
                "upload_imagination" => $metadata["upload_imagination"] ?? false,
                "upload_definition" => $metadata["upload_definition"] ?? false,
                "detail_sentences_counter" => $metadata["detail_sentences_counter"] ?? 0,
                "detail_keyword_counter" => $metadata["detail_keyword_counter"] ?? 0,
                "upload_image_counter" => $metadata["upload_image_counter"] ?? 0,
            ]
        ];
    }
}
