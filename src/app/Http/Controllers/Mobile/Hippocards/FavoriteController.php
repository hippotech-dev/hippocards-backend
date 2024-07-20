<?php

namespace App\Http\Controllers\Mobile\Hippocards;

use App\Http\Controllers\Controller;
use App\Http\Resources\Mobile\Hippocards\FavoriteResource;
use App\Http\Services\FavoriteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class FavoriteController extends Controller
{
    public function __construct(private FavoriteService $service)
    {
        $this->middleware("jwt.auth");
    }
    /**
     * Display a listing of the resource.
     */
    public function getFavoritePackages(Request $request)
    {
        $filters = $request->only("language");

        $requestUser = auth()->user();

        $favorites = Cache::remember(
            cache_key("list-favorite-packages", [ $requestUser->id, $filters["language"] ?? 0, $request->get("cursor", 0) ]),
            3600,
            fn () => $this->service->getFavoritePackages($requestUser, $filters)
        );

        return FavoriteResource::collection($favorites);
    }

    /**
     * Display a listing of the resource.
     */
    public function getFavoriteSorts(Request $request)
    {
        $filters = $request->only("language");

        $requestUser = auth()->user();

        $favorites = Cache::remember(
            cache_key("list-favorite-sorts", [ $requestUser->id, $filters["language"] ?? 0, $request->get("cursor", 0) ]),
            3600,
            fn () => $this->service->getFavoriteSorts($requestUser, $filters)
        );

        return FavoriteResource::collection($favorites);
    }

    /**
     * Display a listing of the resource.
     */
    public function getFavoriteArticles(Request $request)
    {
        $filters = $request->only("language");

        $requestUser = auth()->user();

        $favorites = Cache::remember(
            cache_key("list-favorite-articles", [ $requestUser->id, $filters["language"] ?? 0, $request->get("cursor", 0) ]),
            3600,
            fn () => $this->service->getFavoriteArticles($requestUser, $filters)
        );

        return FavoriteResource::collection($favorites);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
