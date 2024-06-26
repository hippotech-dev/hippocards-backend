<?php

namespace App\Http\Resources\System\Academy;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WordSortResource extends JsonResource
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
            "word_id" => $this->word_id,
            "package_id" => $this->baseklass_id,
            "word" => new WordResource($this->whenLoaded("word"))
        ];
    }
}
