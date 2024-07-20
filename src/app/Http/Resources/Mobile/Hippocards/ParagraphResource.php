<?php

namespace App\Http\Resources\Mobile\Hippocards;

use Illuminate\Http\Resources\Json\JsonResource;

class ParagraphResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "type" => $this->type,
            "order" => $this->order,
            "is_highlighted" => $this->is_highlighted,
            'value' => $this->when($this->type === 0, function () {
                return $this->value;
            }),
            'translation' => $this->when($this->type === 0, function () {
                return $this->translation;
            }),
            'image' => $this->when($this->type === 1, function () {
                return cdn_path($this->value);
            }),
        ];
    }
}
