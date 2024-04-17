<?php

namespace App\Http\Resources\Web\Academy;

use App\Http\Resources\Utility\AssetResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CertificateResource extends JsonResource
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
            "course" => new CourseResource($this->whenLoaded("course")),
            "asset" => new AssetResource($this->whenLoaded("asset")),
            "created_at" => $this->created_at
        ];
    }
}
