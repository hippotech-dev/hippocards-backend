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
        $this->middleware("jwt.auth");
    }

    /**
     * Search word
     */
    public function searchWords(Request $request)
    {
        $filters = Validator::make(
            $request->only(
                "search",
                "language"
            ),
            [
                "search" => "required|string|max:256",
                "language" => "sometimes|integer",
            ]
        )
            ->validate();

        $resource = Cache::remember(
            cache_key("search-words", array_merge($filters, [ $request->get("page", 1) ])),
            60 * 5,
            fn () => WordSortResource::collection($this->service->searchWords($filters))
        );

        return $resource;
    }

    /**
     * Get memorized words
     */
    public function getMemorizedWords(Request $request)
    {
        $requestUser = auth()->user();
        $page = $request->get("page", 1);
        $resource = Cache::remember(
            cache_key("memorized-words", [ $requestUser->id, $page, 1 ]),
            60,
            fn () => WordSortResource::collection($this->service->getMemorizedWords($requestUser))
        );

        return $resource;
    }
}
