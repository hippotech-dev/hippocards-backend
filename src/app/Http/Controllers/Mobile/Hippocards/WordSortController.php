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
            cache_key("search-words", array_merge($filters, [ $request->get("cursor", 1) ])),
            300,
            fn () => WordSortResource::collection($this->service->getSortsWithCursor($filters, [ "word", "package" ]))
        );

        return $resource;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $sort = $this->service->getSortByIdLoaded($id);

        if (is_null($sort)) {
            throw new NotFoundHttpException("Sort not found!");
        }

        $sort = Cache::remember(
            cache_key("get-word-overview", [ $id ]),
            3600,
            fn () => new WordSortResource($sort)
        );

        return $sort;
    }
}
