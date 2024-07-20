<?php

namespace App\Http\Resources\Mobile\Hippocards;

use App\Http\Resources\Utility\CategoryResource;
use App\Http\Resources\Utility\LanguageResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
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
            "title" => $this->title,
            "image" => cdn_path($this->image),
            "time" => $this->time,
            'audio' => $this->mp3 !== null ? cdn_path($this->mp3) : $this->mp3,
            "description" => $this->description,
            "level" => check_const_level($this->level_id),
            "is_review" => $this->is_review,
            "published" => !$this->is_review,
            "package_id" => $this->package_id,
            "is_featured" => $this->is_featured,
            "questions_count" => $this->morph_questions_count,
            "favorite" => !is_null($this->favorites_count) && $this->favorites_count > 0,
            "package_word_count" => $this->relationLoaded("package") && !is_null($this->package) ? $this->package->word_sorts_count : 0,
            "category" => new CategoryResource($this->whenLoaded("category")),
            "author" => new UserResource($this->whenLoaded('user')),
            "language" => new LanguageResource($this->whenLoaded("language")),
            "paragraphs" => ParagraphResource::collection($this->whenLoaded('paragraphs')),
            "total_paragraph_words" => $this->whenLoaded("paragraphs", function () {
                $total = 0;
                foreach ($this->paragraphs as $paragraph) {
                    !is_null($paragraph->value) && $total += count(explode(" ", $paragraph->value));
                }
                return $total;
            }),
            "favorite_id" => $this->favorite_id
        ];
    }
}
