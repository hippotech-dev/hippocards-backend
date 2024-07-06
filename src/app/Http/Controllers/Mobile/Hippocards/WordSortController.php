<?php

namespace App\Http\Controllers\Mobile\Hippocards;

use App\Http\Controllers\Controller;
use App\Http\Resources\Mobile\Hippocards\WordResource;
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
            cache_key("search-words", array_merge($filters, [ $request->get("page", 1) ])),
            60 * 5,
            fn () => WordSortResource::collection($this->service->getSortsWithSimplePage($filters))
        );

        return $resource;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $sort = Cache::remember(
            cache_key("get-word-overview", [ $id, 123 ]),
            3600,
            function () use ($id) {
                $sort = $this->service->getSortByIdLoaded($id);

                if (is_null($sort)) {
                    throw new NotFoundHttpException("Sort not found!");
                }

                return (new WordResource($sort->word))->toArray(request());
            }
        );

        return $sort;
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
            300,
            fn () => WordSortResource::collection($this->service->getMemorizedWords($requestUser))
        );

        return $resource;
    }
}
