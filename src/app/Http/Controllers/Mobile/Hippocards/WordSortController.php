<?php

namespace App\Http\Controllers\Mobile\Hippocards;

use App\Http\Controllers\Controller;
use App\Http\Resources\Mobile\Hippocards\ExamResultResource;
use App\Http\Resources\Mobile\Hippocards\WordSortResource;
use App\Http\Services\WordSortService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class WordSortController extends Controller
{
    public function __construct(private WordSortService $service)
    {
        $this->middleware("jwt.auth", [ "only" => "getRecentLearningWords" ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
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
            cache_key("search-words", array_merge($filters, [ $request->get("cursor", 0) ])),
            300,
            fn () => $this->service->getSortsWithCursor($filters, [ "word.mainDetail", "package" ])
        );

        return WordSortResource::collection($resource);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $requestUser = auth()->user();

        $sort = Cache::remember(
            cache_key("get-word-overview", [ $id ]),
            3600,
            fn () => $this->service->getSortByIdLoaded($id)
        );

        if (is_null($sort)) {
            throw new NotFoundHttpException("Sort not found!");
        }

        $sortFavorite = $this->service->getSortFavorite($requestUser, $sort);

        return resource_append_additional(new WordSortResource($sort), [ "favorite" => !is_null($sortFavorite) ]);
    }

    /**
     * Get recent learning words
     */
    public function getRecentLearningWords(Request $request)
    {
        $filters = $request->only("language");

        $user = auth()->user();

        [ "results" => $words, "total" => $total ] = $this->service->getRecentLearningWords($user, $filters);

        return resource_append_additional(ExamResultResource::collection($words), [ "total_count" => $total ]);
    }
}
