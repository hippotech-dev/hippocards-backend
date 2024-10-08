<?php

namespace App\Http\Controllers\Mobile\Hippocards;

use App\Enums\EPackageExamType;
use App\Enums\EPackageType;
use App\Http\Controllers\Controller;
use App\Http\Resources\Mobile\Hippocards\PackageProgressResource;
use App\Http\Resources\Mobile\Hippocards\PackageResource;
use App\Http\Resources\Mobile\Hippocards\WordSortResource;
use App\Http\Services\PackageService;
use App\Http\Services\WordSortService;
use App\Models\Package\Baseklass;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PackageController extends Controller
{
    public function __construct(private PackageService $service, private WordSortService $wordSortService)
    {
        $this->middleware("jwt.auth");
    }

    /**
     * Get package sorts
     */
    public function getPackageSorts(Baseklass $package)
    {
        $user = auth()->user();

        $sorts = Cache::remember(
            cache_key("package-sort", [ $package->id ]),
            3600,
            fn () => $this->wordSortService->getPackageSorts($package, [], [ "word", "word.mainDetail",  "word.images" ], [ "field" => "sort_word", "value" => "asc" ])
        );

        $packageProgress = $this->service->getPackageWordsProgress($user, $package);

        return resource_append_additional(WordSortResource::collection($sorts), [ "word_progresses" => $packageProgress ]);
    }

    /**
     * Submit package progress
     */
    public function submitPackageProgress(Request $request, Baseklass $package)
    {
        $validatedData = Validator::make(
            $request->only(
                "type"
            ),
            [
                "type" => [
                    "required",
                    "string",
                    Rule::in([ EPackageExamType::EXAM->value, EPackageExamType::FINAL_EXAM->value ])
                ]
            ]
        )
            ->validate();

        $requestUser = auth()->user();

        $this->service->submitPackageProgress($requestUser, $package, EPackageExamType::tryFrom($validatedData["type"]));

        return response()->success();
    }

    /**
     * Get Recent learning pacakges
     */
    public function getRecentLearningPackages(Request $request)
    {
        $filters = $request->only("language");

        $requestUser = auth()->user();

        [ "results" => $packages, "total" => $total ] = $this->service->getRecentLearningPackagesWithCursor($requestUser, $filters);

        return resource_append_additional(PackageProgressResource::collection($packages), [ "total_count" => $total ]);
    }
}
