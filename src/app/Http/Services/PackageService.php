<?php

namespace App\Http\Services;

use App\Enums\EPackageExamType;
use App\Enums\EPackageType;
use App\Enums\EStatus;
use App\Enums\EUserActivityAction;
use App\Enums\EUserActivityType;
use App\Models\Package\Baseklass;
use App\Models\Package\ExamResult;
use App\Models\Package\Sort;
use App\Models\Package\UserPackageProgress;
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

    public function getPackages(array $filters, $orderBy = [ "field" => "id", "value" => "desc" ])
    {
        return filter_query_with_model(Baseklass::query(), $this->getFilterModel($filters), $filters)->withCount("wordSorts")->orderBy($orderBy["field"], $orderBy["value"])->get();
    }

    public function getPackagesWithPage(array $filters, array $with = [], $orderBy = [ "field" => "id", "value" => "desc" ])
    {
        $orderBy = get_sort_info($orderBy);
        return filter_query_with_model(Baseklass::query(), $this->getFilterModel($filters), $filters)->withCount("wordSorts")->orderBy($orderBy["field"], $orderBy["value"])->with($with)->paginate($_GET["limit"] ?? null)->withQueryString();
    }

    public function getPackageById(int $id, array $with = [])
    {
        return Baseklass::with($with)->withCount("wordSorts")->find($id);
    }

    public function getPackage(array $filters, array $with = [])
    {
        return filter_query_with_model(Baseklass::query(), $this->getFilterModel($filters), $filters)->with($with)->withCount("wordSorts")->first();
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
            if (array_key_exists("v3_thumbnail_asset_id", $data)) {
                $data["thumbnail_path"] = $this->assetService->getAssetPath($data["v3_thumbnail_asset_id"]);
            }

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

    public function resetPackageWordCount(Baseklass $package)
    {
        return $package->update([
            "word_count" => $package->wordSorts()->count()
        ]);
    }

    public function getUserPackageProgress(User $user, Baseklass $package)
    {
        return UserPackageProgress::where("user_id", $user->id)->where("package_id", $package->id)->first();
    }

    public function createOrUpdateUserProgress(User $user, Baseklass $package, array $data)
    {
        return UserPackageProgress::updateOrCreate([
            "user_id" => $user->id,
            "package_id" => $package->id,
        ], $data);
    }

    public function submitPackageProgress(User $user, Baseklass $package, EPackageExamType $type)
    {
        $correctExamCount = ExamResult::where("user_id", $user->id)->where("baseklass_id", $package->id)->where("status", EStatus::SUCCESS)->count();

        $userProgress = $this->getUserPackageProgress($user, $package);

        $totalExamCount = $userProgress->total_exam_count ?? 0;
        $totalFinalExamCount = $userProgress->total_final_exam_count ?? 0;

        $this->createOrUpdateUserProgress(
            $user,
            $package,
            [
                "progress" => $correctExamCount,
                "package_word_count" => $package->word_count,
                "language_id" => $package->language_id,
                "total_exam_count" => $type === EPackageExamType::EXAM && $correctExamCount > $totalExamCount
                    ? $totalExamCount + 1
                    : $totalExamCount,
                "total_final_exam_count" => $type === EPackageExamType::FINAL_EXAM && $correctExamCount > $totalFinalExamCount
                    ? $totalFinalExamCount + 1
                    : $totalFinalExamCount,
            ]
        );
    }

    public function getRecentLearningPackagesWithCursor(User $user, array $filters = [])
    {
        $query = filter_query_with_model(UserPackageProgress::with("package.category"), [ "language" => [ "where", "language_id" ] ], $filters)
            ->where("user_id", $user->id)
            ->whereHas("package", fn ($query) => $query->active())
            ->whereColumn("progress", "<", "package_word_count")->orderBy("id", "desc");

        return [
            "results" => $query->cursorPaginate(page_size()),
            "total" => $query->count()
        ];
    }

    public function getPackageWordsProgress(User $user, Baseklass $package)
    {
        $examResults = ExamResult::where("user_id", $user->id)->where("baseklass_id", $package->id)->get();

        return array_column($examResults->toArray(), "status", "word_id");
    }
}
