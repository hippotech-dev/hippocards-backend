<?php

namespace App\Http\Resources\Mobile\Hippocards;

use App\Http\Resources\Utility\LanguageResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Config;

class PackageResource extends JsonResource
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
            "foreign_name" => $this->foreign_name,
            "description" => $this->description,
            "word_count" => is_null($this->word_sorts_count) ? 0 : $this->word_sorts_count,
            "language_id" => $this->language_id,
            "is_free" => boolval($this->is_free),
            "preview" => $this->prepare_see,
            "is_book" => boolval($this->is_book),
            "thumbnail_path" => append_s3_path($this->thumbnail_path),
            "language" => new LanguageResource($this->whenLoaded("language")),
        ];
    }
}
