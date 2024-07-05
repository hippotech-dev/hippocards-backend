<?php

namespace App\Http\Services;

use App\Enums\EPackageType;
use App\Enums\EStatus;
use App\Enums\EUserActivityAction;
use App\Enums\EUserActivityType;
use App\Models\Package\Baseklass;
use App\Models\Package\Sort;
use App\Models\Package\Word\Word;
use App\Models\User\User;
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
            "language" => [ "where", "language_id" ],
            "type" => [ "where", "type" ],
            "status" => [ "where", "status" ],
            "for_kids" => [ "where", "for_kids" ],
            "created_by" => [ "where", "created_by" ],
            "id_in" => [ "whereIn", "id" ],
            "filter" => [
                [ "where" ],
                [
                    [
                        "name" => null,
                        "value" => fn ($query) => $query->whereLike("name", $filters["filter"])->orWhereLike("foreign_name", $filters["filter"])
                    ]
                ]
            ]
        ];
    }

    protected function createSystemPackageActivity(User $user, mixed $object, EUserActivityAction $action)
    {
        return $this->userActivityService->createObjectActivity($user, $object, EUserActivityType::SYSTEM_PACKAGE, $action);
    }

    public function getPackages(array $filters)
    {
        return filter_query_with_model(Baseklass::query(), $this->getFilterModel($filters), $filters)->get();
    }

    public function getPackagesWithPage(array $filters, array $with = [], $orderBy = [ "field" => "id", "value" => "desc" ])
    {
        $orderBy = get_sort_info($orderBy);
        return filter_query_with_model(Baseklass::query(), $this->getFilterModel($filters), $filters)->orderBy($orderBy["field"], $orderBy["value"])->with($with)->paginate($_GET["limit"] ?? null)->withQueryString();
    }

    public function getPackageById(int $id, array $with = [])
    {
        return Baseklass::with($with)->find($id);
    }

    public function getPackage(array $filters, array $with = [])
    {
        return filter_query_with_model(Baseklass::query(), $this->getFilterModel($filters), $filters)->with($with)->first();
    }

    public function createPackage(User $user, array $data)
    {
        return DB::transaction(function () use ($user, $data) {
            if (array_key_exists("v3_thumbnail_asset_id", $data)) {
                $data["thumbnail_path"] = $this->assetService->getAssetPath($data["v3_thumbnail_asset_id"]);
            }

            $package = Baseklass::create(array_merge(
                $data,
                [
                    "created_by" => $user->id,
                    "status" => EStatus::PENDING,
                    "word_count" => 0,
                    "article_id" => 0
                ]
            ));

            $this->createSystemPackageActivity($user, $package, EUserActivityAction::CREATE);

            return $package;
        });
    }

    public function updatePackage(User $user, Baseklass $package, array $data)
    {
        return DB::transaction(function () use ($user, $package, $data) {
            $this->createSystemPackageActivity($user, $package, EUserActivityAction::UPDATE);
            return $package->update(array_merge(
                $data
            ));
        });
    }

    public function deletePackage(User $user, Baseklass $package)
    {
        return DB::transaction(function () use ($user, $package) {
            $this->createSystemPackageActivity($user, $package, EUserActivityAction::DELETE);
            return $package->delete();
        });
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

    public function getSortsWithSimplePage(array $filters)
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
            "package" => [ "where", "baseklass_id" ],
            "id_in" => [ "whereIn",  ]
        ];

        return filter_query_with_model(Sort::with("word.mainDetail", "package")->byType(EPackageType::DEFAULT)->active(), $filterModel, $filters)->orderBy("id", "desc")->simplePaginate(page_size());
    }

    public function getMemorizedWords(User $user)
    {
        $activitiesWithSorts = $this->userActivityService->getUserActivitiesByTypeWithPage($user, EUserActivityType::USER_WORD, [ "object.word.mainDetail", "object.package" ]);

        $activitiesWithSortsCollection = $activitiesWithSorts->getCollection();

        $activitiesWithSorts->setCollection($activitiesWithSortsCollection->pluck("object"));

        return $activitiesWithSorts;
    }
}
