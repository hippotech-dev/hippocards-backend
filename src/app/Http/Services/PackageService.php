<?php

namespace App\Http\Services;

use App\Models\Package\Baseklass;
use App\Models\Package\Sort;
use Illuminate\Support\Collection;

class PackageService
{
    public function getPackages(array $filter)
    {
        $filterModel = [
            "id_in" => [ "whereIn", "id" ]
        ];

        return filter_query_with_model(Baseklass::query(), $filterModel, $filter)->get();
    }

    public function searchPackages(array $filter)
    {
        $filterModel = [
            "name_like" => [ "whereLike", "name" ],
            "language_id" => [ "where", "language_id" ]
        ];

        return filter_query_with_model(Baseklass::query(), $filterModel, $filter)->paginate($_GET["limit"] ?? null)->withQueryString();
    }

    public function getSortById(int $id, $with = [ "word" ])
    {
        return Sort::with($with)->find($id);
    }

    public function getSortByIdLoaded(int $id)
    {
        $sort = $this->getSortById($id);

        if (is_null($sort)) {
            return null;
        }

        return Sort::with([
            "word.translation",
            "word.pronunciation",
            "word.wordImaginations" => function ($query) use ($sort) {
                $query->where("baseklass_id", $sort->baseklass_id)
                    ->where("language_id", $sort->language_id);
            },
            "word.wordImaginations.imagination",
            "word.exampleSentences" => function ($query) use ($sort) {
                $query->where("baseklass_id", $sort->baseklass_id)
                    ->where("language_id", $sort->language_id);
            },
            "word.exampleSentences.example",
            "word.wordKeyword" => function ($query) use ($sort) {
                $query->where("baseklass_id", $sort->baseklass_id)
                    ->where("language_id", $sort->language_id);
            },
            "word.wordKeyword.keyword",
            "word.wordImage" => function ($query) use ($sort) {
                $query->where("baseklass_id", $sort->baseklass_id)
                    ->where("language_id", $sort->language_id);
            },
            "word.wordImage.image",
            "word.pos",
            "word.synonyms",
        ])
            ->find($id);
    }

    public function getPackagesSorts(Collection|array $packages)
    {
        if ($packages instanceof Collection) {
            $ids = $packages->pluck("id")->toArray();
        } else {
            $ids = $packages;
        }
        return Sort::with("word")->whereIn("baseklass_id", $ids)->get();
    }
}
