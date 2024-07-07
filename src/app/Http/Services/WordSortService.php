<?php

namespace App\Http\Services;

use App\Enums\EPackageType;
use App\Enums\EUserActivityType;
use App\Models\Package\Sort;
use App\Models\Package\Word\Word;
use App\Models\User\User;
use Illuminate\Support\Collection;

class WordSortService
{
    public function __construct(private UserActivityService $userActivityService, private AssetService $assetService)
    {
    }

    protected function getFilterModel(array $filters)
    {
        return [
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
            "package" => [ "where", "baseklass_id" ],
            "id_in" => [ "whereIn",  ]
        ];
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
            "mainDetail",
            "images",
            "synonyms",
            "definitionSentences",
            "imaginationSentences"
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
            array_push($with, "images");
        }

        if (in_array("mainDetail", $include)) {
            array_push($with, "mainDetail");
        }

        if (in_array("synonyms", $include)) {
            array_push($with, "synonyms");
        }

        if (in_array("imaginations", $include)) {
            array_push($with, "imaginationSentences");
        }

        if (in_array("examples", $include)) {
            array_push($with, "definitionSentences");
        }

        $word = Word::with($with)->find($sort->word_id);

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

    public function getSortsWithSimplePage(array $filters, array $with = [], $orderBy = [ "field" => "id", "value" => "desc" ])
    {
        $orderBy = get_sort_info($orderBy);
        return filter_query_with_model(Sort::with($with), $this->getFilterModel($filters), $filters)->orderBy($orderBy["field"], $orderBy["value"])->simplePaginate(page_size());
    }

    public function getSortsWithPage(array $filters, array $with, $orderBy = [ "field" => "id", "value" => "desc" ])
    {
        $orderBy = get_sort_info($orderBy);
        return filter_query_with_model(Sort::with($with), $this->getFilterModel($filters), $filters)->orderBy($orderBy["field"], $orderBy["value"])->paginate(page_size());
    }

    public function getMemorizedWords(User $user)
    {
        $activitiesWithSorts = $this->userActivityService->getUserActivitiesByTypeWithPage($user, EUserActivityType::USER_WORD, [ "object.word.mainDetail", "object.package" ]);

        $activitiesWithSortsCollection = $activitiesWithSorts->getCollection();

        $activitiesWithSorts->setCollection($activitiesWithSortsCollection->pluck("object"));

        return $activitiesWithSorts;
    }
}
