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
            "about_video_path" => append_s3_path($this->about_video_path),
            "about_video_asset" => new AssetResource($this->whenLoaded("aboutVideoAsset")),
        ];
    }
}
