<?php

namespace App\Http\Resources\Utility;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SentenceResource extends JsonResource
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
            "value" => $this->value,
            "translation" => $this->translation,
            "pronunciation" => $this->pronunciation,
            "latin" => $this->latin,
            "type" => $this->type,
            "order" => $this->order,
            "v3_audio_asset_id" => $this->v3_audio_asset_id,
            "language_id" => $this->language_id,
            "audio" => new AssetResource($this->whenLoaded("audioAsset"))
        ];
    }
}
