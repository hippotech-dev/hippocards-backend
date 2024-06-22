<?php

namespace App\Http\Resources\Mobile\Hippocards;

use App\Enums\EWordImageType;
use App\Enums\EWordSimilarType;
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
            "translation" => $this->whenLoaded("translation", function () {
                if (is_null($this->translation)) {
                    return null;
                }
                return $this->translation->name;
            }),
            "sentence_audio" => $this->whenLoaded("exampleSentences", function () {
                if (is_null($this->exampleSentences)) {
                    return null;
                }
                $sentence = !is_null($this->baseklass_id)
                    ? $this->exampleSentences->where("type", 1)->where("baseklass_id", $this->baseklass_id)->first()
                    : $this->exampleSentences->where("type", 1)->first();
                if (is_null($sentence)) {
                    return null;
                }
                return cdn_path($sentence->audio);
            }),
            "sentence" => $this->whenLoaded("exampleSentences", function () {
                if (is_null($this->exampleSentences)) {
                    return null;
                }
                $sentence = !is_null($this->baseklass_id)
                    ? $this->exampleSentences->where("type", 1)->where("baseklass_id", $this->baseklass_id)->first()
                    : $this->exampleSentences->where("type", 1)->first();
                if (is_null($sentence) || is_null($sentence->example)) {
                    return null;
                }
                return $sentence->example->name;
            }),
            "sentence_translation" => $this->whenLoaded("exampleSentences", function () {
                if (is_null($this->exampleSentences)) {
                    return null;
                }
                $sentence = !is_null($this->baseklass_id)
                    ? $this->exampleSentences->where("type", 2)->where("baseklass_id", $this->baseklass_id)->first()
                    : $this->exampleSentences->where("type", 2)->first();
                if (is_null($sentence) || is_null($sentence->example)) {
                    return null;
                }
                return $sentence->example->name;
            }),
            "sentence_latin" => $this->whenLoaded("exampleSentences", function () {
                if (is_null($this->exampleSentences)) {
                    return null;
                }
                $sentence = !is_null($this->baseklass_id)
                    ? $this->exampleSentences->where("type", 3)->where("baseklass_id", $this->baseklass_id)->first()
                    : $this->exampleSentences->where("type", 3)->first();
                if (is_null($sentence) || is_null($sentence->example)) {
                    return null;
                }
                return $sentence->example->name;
            }),
            "sentence2" => $this->whenLoaded("exampleSentences", function () {
                if (is_null($this->exampleSentences)) {
                    return null;
                }
                $sentence = !is_null($this->baseklass_id)
                    ? $this->exampleSentences->where("type", 4)->where("baseklass_id", $this->baseklass_id)->first()
                    : $this->exampleSentences->where("type", 4)->first();
                if (is_null($sentence) || is_null($sentence->example)) {
                    return null;
                }
                return $sentence->example->name;
            }),
            "sentence2_translation" => $this->whenLoaded("exampleSentences", function () {
                if (is_null($this->exampleSentences)) {
                    return null;
                }
                $sentence = !is_null($this->baseklass_id)
                    ? $this->exampleSentences->where("type", 5)->where("baseklass_id", $this->baseklass_id)->first()
                    : $this->exampleSentences->where("type", 5)->first();
                if (is_null($sentence) || is_null($sentence->example)) {
                    return null;
                }
                return $sentence->example->name;
            }),
            "sentence2_latin" => $this->whenLoaded("exampleSentences", function () {
                if (is_null($this->exampleSentences)) {
                    return null;
                }
                $sentence = !is_null($this->baseklass_id)
                    ? $this->exampleSentences->where("type", 6)->where("baseklass_id", $this->baseklass_id)->first()
                    : $this->exampleSentences->where("type", 6)->first();
                if (is_null($sentence) || is_null($sentence->example)) {
                    return null;
                }
                return $sentence->example->name;
            }),
            "pronunciation" => $this->whenLoaded("pronunciation", function () {
                if (is_null($this->pronunciation)) {
                    return null;
                }
                return preg_replace('/<sub>(.*?)<\/sub><span>(.*?)<\/span>/', '$2', $this->pronunciation->name);
            }),
            "hiragana" => $this->whenLoaded("pronunciation", function () {
                if (is_null($this->pronunciation)) {
                    return null;
                }
                if (!is_null($this->language) && $this->language->is_hiragana) {
                    return preg_replace('/<sub>(.*?)<\/sub><span>(.*?)<\/span>/', '$1', $this->pronunciation->name);
                }
                return null;
            }),
            "imagination" => $this->whenLoaded("wordImaginations", function () {
                if (is_null($this->wordImaginations) || count($this->wordImaginations) == 0) {
                    return null;
                }
                $imagination = !is_null($this->baseklass_id)
                    ? $this->wordImaginations->where("baseklass_id", $this->baseklass_id)->first()
                    : $this->wordImaginations->first();
                if (is_null($imagination->imagination)) {
                    return null;
                }
                return $imagination->imagination->name;
            }),
            "keyword" => $this->whenLoaded("wordKeyword", function () {
                if (is_null($this->wordKeyword)) {
                    return null;
                }
                $wordKeyword = !is_null($this->baseklass_id)
                    ? $this->wordKeyword->where("baseklass_id", $this->baseklass_id)->first()
                    : $this->wordKeyword->first();
                if (is_null($wordKeyword) || is_null($wordKeyword->keyword)) {
                    return null;
                }
                return $wordKeyword->keyword->name;
            }),
            "image" => $this->whenLoaded("wordImages", function () {
                if (is_null($this->wordImages)) {
                    return null;
                }
                $baseImage = $this->wordImages->whereIn("tag", [ null, EWordImageType::PRIMARY ]);
                $wordImages = !is_null($this->baseklass_id)
                    ? $baseImage->where("baseklass_id", $this->baseklass_id)->first()
                    : $baseImage->first();
                if (is_null($wordImages) || is_null($wordImages->image)) {
                    return null;
                }
                return "https://cdn.hippo.cards/storage/" . $wordImages->image->image;
            }),
            "thumbnail_image" => $this->whenLoaded("wordImages", function () {
                if (is_null($this->wordImages)) {
                    return null;
                }
                $wordImages = !is_null($this->baseklass_id)
                    ? $this->wordImages->where("baseklass_id", $this->baseklass_id)->first()
                    : $this->wordImages->first();
                if (is_null($wordImages) || is_null($wordImages->image)) {
                    return null;
                }
                return "https://cdn.hippo.cards/storage/" . $wordImages->image->tumbnail_img;
            }),
            "imagination_image" => $this->whenLoaded("wordImages", function () {
                if (is_null($this->wordImages)) {
                    return null;
                }
                $baseImage = $this->wordImages->where("tag", EWordImageType::IMAGINATION);
                $wordImages = !is_null($this->baseklass_id)
                    ? $baseImage->where("baseklass_id", $this->baseklass_id)->first()
                    : $baseImage->first();
                if (is_null($wordImages) || is_null($wordImages->image)) {
                    return null;
                }
                return "https://cdn.hippo.cards/storage/" . $wordImages->image->image;
            }),
            "definition_image" => $this->whenLoaded("wordImages", function () {
                if (is_null($this->wordImages)) {
                    return null;
                }
                $baseImage = $this->wordImages->where("tag", EWordImageType::DEFINITION);
                $wordImages = !is_null($this->baseklass_id)
                    ? $baseImage->where("baseklass_id", $this->baseklass_id)->first()
                    : $baseImage->first();
                if (is_null($wordImages) || is_null($wordImages->image)) {
                    return null;
                }
                return "https://cdn.hippo.cards/storage/" . $wordImages->image->image;
            }),
            "sort" => $this->when(!is_null($this->sort), $this->sort),
            "pos" => $this->whenLoaded("pos", function () {
                if (is_null($this->pos)) {
                    return null;
                }
                return $this->pos->pos_id;
            }),
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
