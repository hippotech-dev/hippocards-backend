<?php

namespace App\Http\Resources\Utility;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssetResource extends JsonResource
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
            "path" => append_cdn_path($this->path),
            "s3_path" => append_s3_path($this->path),
            "size" => $this->size,
            "mime_type" => $this->mime_type,
            "metadata" => $this->metadata ?? [],
        ];
    }
}
