<?php

namespace App\Http\Resources\System\Academy;

use App\Http\Resources\Utility\AssetResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseIntroductionResource extends JsonResource
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
            "content" => $this->content ?? [],
            "video_asset_path" => append_s3_path($this->video_asset_path),
            "video_asset" => new AssetResource($this->whenLoaded("videoAsset")),
        ];
    }
}
