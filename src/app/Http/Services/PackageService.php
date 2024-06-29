<?php

namespace App\Http\Services;

use App\Enums\EPartOfSpeech;
use App\Enums\ESentenceType;
use App\Enums\EUserActivityType;
use App\Enums\EWordImageType;
use App\Models\Package\WordDetail;
use App\Models\Package\WordImage;
use App\Models\Package\Baseklass;
use App\Models\Package\Sort;
use App\Models\Package\Word\Word;
use App\Models\Package\Word\WordExample;
use App\Models\User\User;
use App\Models\Utility\Sentence;
use App\Models\Utility\UserActivity;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PackageService
{
    public function __construct(private UserActivityService $userActivityService, private AssetService $assetService)
    {
    }

    protected function getFilterModel(array $filters)
    {
        return [
            "name_like" => [ "whereLike", "name" ],
            "language_id" => [ "where", "language_id" ],
            "id_in" => [ "whereIn", "id" ]
        ];
    }

    public function getPackages(array $filters)
    {
        return filter_query_with_model(Baseklass::query(), $this->getFilterModel($filters), $filters)->get();
    }

    public function getPackagesWithPage(array $filters)
    {
        return filter_query_with_model(Baseklass::query(), $this->getFilterModel($filters), $filters)->paginate($_GET["limit"] ?? null)->withQueryString();
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

        return filter_query_with_model(Sort::with("word.mainDetail", "package")->active(), $filterModel, $filters)->orderBy("id", "desc")->simplePaginate(page_size());
    }

    public function getMemorizedWords(User $user)
    {
        $activitiesWithSorts = $this->userActivityService->getUserActivitiesByTypeWithPage($user, EUserActivityType::WORD, [ "object.word.mainDetail", "object.package" ]);

        $activitiesWithSortsCollection = $activitiesWithSorts->getCollection();

        $activitiesWithSorts->setCollection($activitiesWithSortsCollection->pluck("object"));

        return $activitiesWithSorts;
    }
}
