<?php

namespace App\Http\Resources\Mobile\Hippocards;

use App\Enums\EFavoriteType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FavoriteResource extends JsonResource
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
            "object_id" => $this->object_id,
            "type" => $this->type,
            "language_id" => $this->language_id,
            "package" => new PackageResource($this->when($this->type === EFavoriteType::PACKAGE, $this->whenLoaded("object"))),
            "sort" => new WordSortResource($this->when($this->type === EFavoriteType::WORD, $this->whenLoaded("object"))),
            "article" => new ArticleResource($this->when($this->type === EFavoriteType::ARTICLE, $this->whenLoaded("object"))),
        ];
    }
}
