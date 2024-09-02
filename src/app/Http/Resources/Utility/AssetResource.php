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
            "name" => $this->name,
            "path" => append_cdn_path($this->path),
            "s3_path" => append_s3_path($this->path),
            "size" => $this->size,
            "mime_type" => $this->mime_type,
            "metadata" => [
                "transcoded_url" => array_key_exists("transcoded_url", $this->metadata ?? []) ? append_s3_path($this->metadata["transcoded_url"] ?? null) : null,
            ],
            "vdo_drm_video_id" => $this->vdo_drm_video_id,
            "vdo_drm_video_status" => $this->vdo_drm_video_status,
            "upload_type" => $this->upload_type,
        ];
    }
}
