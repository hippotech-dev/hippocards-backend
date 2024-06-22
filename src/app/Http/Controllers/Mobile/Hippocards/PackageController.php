<?php

namespace App\Http\Controllers\Mobile\Hippocards;

use App\Http\Controllers\Controller;
use App\Http\Resources\Mobile\Hippocards\WordSortResource;
use App\Http\Services\PackageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class PackageController extends Controller
{
    public function __construct(private PackageService $service)
    {
    }

    /**
     * Search word
     */
    public function searchWords(Request $request)
    {
        $filters = Validator::make(
            $request->only("search"),
            [
                "search" => "required|string|max:256",
            ]
        )
            ->validate();

        $sorts = Cache::remember(
            cache_key("search-words", array_merge($filters, [ $request->get("page", 1) ])),
            60 * 5,
            fn () => $this->service->searchWords($filters)
        );

        return WordSortResource::collection($sorts);
    }
}
