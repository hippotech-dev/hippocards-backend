<?php

namespace App\Http\Resources\Mobile\Hippocards;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $imagePath = str_contains($this->image, "upload/avatar")
            ? $this->image
            : "upload/avatar/" . $this->image;
        return [
            "id" => $this->id,
            "name" => $this->name,
            "email" => $this->email,
            "phone" => $this->phone,
            "login_type" => $this->login_type,
            "logged_in" => boolval($this->logged_in),
            "image" => !is_null($this->image) && strlen($this->image) > 0
                ? append_cdn_path($imagePath)
                : "https://api.dicebear.com/5.x/initials/png?seed=" . $this->name,
            "s3_image" => !is_null($this->image) && strlen($this->image) > 0
                ? append_s3_path($imagePath)
                : "https://api.dicebear.com/5.x/initials/png?seed=" . $this->name,
            "role_id" => $this->new_role,
            "birth_year" => $this->birth_year,
            "sex" => $this->sex,
            "member" => boolval($this->member),
            "is_guest" => boolval($this->is_guest),
            "is_influencer" => boolval($this->is_influencer),
            "verify" => $this->verify ?? false,
            "device_name" => $this->device_name,
            "model" => $this->model,
            "device_id" => $this->device_id,
            "updated_at" => $this->updated_at,
            "created_at" => $this->created_at,
        ];
    }
}
