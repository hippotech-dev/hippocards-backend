<?php

namespace App\Http\Resources\System\Content;

use App\Enums\EWordImageType;
use App\Enums\EWordSimilarType;
use App\Http\Resources\Utility\SentenceResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WordResource extends JsonResource
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
            "word" => $this->word,
            "package_id" => $this->when(!is_null($this->package_id), $this->package_id),
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "definition_sentences" => SentenceResource::collection($this->whenLoaded("definitionSentences")),
            "imagination_sentences" => SentenceResource::collection($this->whenLoaded("imaginationSentences")),
            "translation" => $this->whenLoaded("mainDetail", function () {
                return $this->mainDetail->translation ?? null;
            }),
            "pronunciation" => $this->whenLoaded("mainDetail", function () {
                return $this->mainDetail->pronunciation ?? null;
            }),
            "hiragana" => $this->whenLoaded("mainDetail", function () {
                return $this->mainDetail->hiragana ?? null;
            }),
            "keyword" => $this->whenLoaded("mainDetail", function () {
                return $this->mainDetail->keyword ?? null;
            }),
            "pos" => $this->whenLoaded("mainDetail", function () {
                return $this->mainDetail->part_of_speech ?? null;
            }),
            "image" => $this->whenLoaded("images", function () {
                return append_s3_path(($this->images->where("type", EWordImageType::PRIMARY)->first())->path ?? null);
            }),
            "thumbnail_image" => $this->whenLoaded("images", function () {
                return append_s3_path(($this->images->where("type", EWordImageType::PRIMARY)->first())->path ?? null);
            }),
            "imagination_image" => $this->whenLoaded("images", function () {
                return append_s3_path(($this->images->where("type", EWordImageType::IMAGINATION)->first())->path ?? null);
            }),
            "definition_image" => $this->whenLoaded("images", function () {
                return append_s3_path(($this->images->where("type", EWordImageType::DEFINITION)->first())->path ?? null);
            }),
            "sort" => $this->when(!is_null($this->sort), $this->sort),
            "synonyms" => $this->whenLoaded("synonyms", function () {
                return $this->synonyms->where("type", EWordSimilarType::SYNONYM)->sortBy("id")->toArray();
            }),
            "antonyms" => $this->whenLoaded("synonyms", function () {
                return $this->synonyms->where("type", EWordSimilarType::ANTONYM)->sortBy("id")->toArray();
            }),
            "similars" => $this->whenLoaded("synonyms", function () {
                return $this->synonyms->where("type", EWordSimilarType::SIMILAR)->sortBy("id")->toArray();
            }),
            "audio" => "https://cdn.hippo.cards/storage/power-vocabs/sound/" . $this->id . ".m4a",
        ];
    }
}
