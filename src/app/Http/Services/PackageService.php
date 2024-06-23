<?php

namespace App\Http\Services;

use App\Enums\EUserActivityType;
use App\Models\Package\Baseklass;
use App\Models\Package\Sort;
use App\Models\Package\Word\Word;
use App\Models\User\User;
use App\Models\Utility\UserActivity;
use Illuminate\Support\Collection;

class PackageService
{
    public function __construct(private UserActivityService $userActivityService)
    {
    }

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

        $word = Word::with([
            "translation",
            "pronunciation",
            "wordImaginations" => function ($query) use ($sort) {
                $query->where("baseklass_id", $sort->baseklass_id)
                    ->where("language_id", $sort->language_id);
            },
            "wordImaginations.imagination",
            "exampleSentences" => function ($query) use ($sort) {
                $query->where("baseklass_id", $sort->baseklass_id)
                    ->where("language_id", $sort->language_id);
            },
            "exampleSentences.example",
            "wordKeyword" => function ($query) use ($sort) {
                $query->where("baseklass_id", $sort->baseklass_id)
                    ->where("language_id", $sort->language_id);
            },
            "wordKeyword.keyword",
            "wordImages" => function ($query) use ($sort) {
                $query->where("baseklass_id", $sort->baseklass_id)
                    ->where("language_id", $sort->language_id);
            },
            "wordImages.image",
            "pos",
            "synonyms",
        ])
            ->find($sort->word_id);

        $sort->setRelation("word", $word);

        return $sort;
    }

    public function getSortByIdInclude(int $id, array $include = [])
    {
        $sort = $this->getSortById($id);

        if (is_null($sort)) {
            return null;
        }

        $with = [];

        if (in_array("images", $include)) {
            array_merge($with, [
                "wordImages" => function ($query) use ($sort) {
                    $query->where("baseklass_id", $sort->baseklass_id)
                        ->where("language_id", $sort->language_id);
                },
            ]);
            array_push($with, "wordImages.image");
        }

        if (in_array("translation", $include)) {
            array_push($with, "translation");
        }

        if (in_array("pronunciation", $include)) {
            array_push($with, "pronunciation");
        }

        if (in_array("pos", $include)) {
            array_push($with, "pos");
        }

        if (in_array("synonyms", $include)) {
            array_push($with, "synonyms");
        }

        if (in_array("imaginations", $include)) {
            array_merge($with, [
                "word.wordImaginations" => function ($query) use ($sort) {
                    $query->where("baseklass_id", $sort->baseklass_id)
                        ->where("language_id", $sort->language_id);
                },
            ]);
            array_push($with, "word.wordImaginations.imagination");
        }

        if (in_array("keywords", $include)) {
            array_merge($with, [
                "word.wordKeyword" => function ($query) use ($sort) {
                    $query->where("baseklass_id", $sort->baseklass_id)
                        ->where("language_id", $sort->language_id);
                },
            ]);
            array_push($with, "word.wordKeyword.keyword");
        }
        $word = Word::with($with)
            ->find($sort->word_id);

        $sort->setRelation("word", $word);

        return $sort;
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

    public function searchWords(array $filters)
    {
        $filterModel = [
            "search" => [
                [ "whereHas" ],
                [
                    [
                        "name" => "word",
                        "value" => fn ($query) => $query->whereLike("word", $filters["search"])
                    ]
                ],
            ],
            "language" => [ "where", "language_id" ],
            "id_in" => [ "whereIn",  ]
        ];

        return filter_query_with_model(Sort::with("word.translation", "package")->active(), $filterModel, $filters)->orderBy("id", "desc")->simplePaginate(page_size());
    }

    public function getMemorizedWords(User $user)
    {
        $activitiesWithSorts = $this->userActivityService->getUserActivitiesByTypeWithPage($user, EUserActivityType::WORD, [ "object.word.translation", "object.package" ]);

        $activitiesWithSortsCollection = $activitiesWithSorts->getCollection();

        $activitiesWithSorts->setCollection($activitiesWithSortsCollection->pluck("object"));

        return $activitiesWithSorts;
    }
}
