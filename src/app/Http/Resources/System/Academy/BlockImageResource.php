<?php

namespace App\Http\Resources\System\Academy;

use App\Http\Resources\Utility\AssetResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlockImageResource extends JsonResource
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
            "v3_asset_id" => $this->v3_asset_id,
            "v3_course_group_block_id" => $this->v3_course_group_block_id,
            "type" => $this->type,
            "path" => append_s3_path($this->path),
            "asset" => new AssetResource($this->whenLoaded("asset")),
        ];
    }
}
