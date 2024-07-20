<?php

namespace App\Http\Controllers\Mobile\Hippocards;

use App\Http\Controllers\Controller;
use App\Http\Resources\Mobile\Hippocards\WordSortResource;
use App\Http\Services\PackageService;
use App\Http\Services\WordSortService;
use App\Models\Package\Baseklass;
use Illuminate\Support\Facades\Cache;

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
        $sorts = Cache::remember(
            cache_key("package-sort", [ $package->id ]),
            3600,
            fn () => $this->wordSortService->getPackageSorts($package, [], [ "word", "word.images" ], [ "field" => "sort_word", "value" => "asc" ])
        );

        return WordSortResource::collection($sorts);
    }
}
