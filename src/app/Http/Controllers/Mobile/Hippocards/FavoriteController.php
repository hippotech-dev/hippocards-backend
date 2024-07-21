<?php

namespace App\Http\Controllers\Mobile\Hippocards;

use App\Enums\EFavoriteType;
use App\Http\Controllers\Controller;
use App\Http\Resources\Mobile\Hippocards\FavoriteResource;
use App\Http\Services\FavoriteService;
use App\Http\Services\PackageService;
use App\Http\Services\WordSortService;
use App\Models\Article\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FavoriteController extends Controller
{
    public function __construct(private FavoriteService $service, private PackageService $packageService, private WordSortService $wordSortService)
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
            600,
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
            600,
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
            600,
            fn () => $this->service->getFavoriteArticles($requestUser, $filters)
        );

        return FavoriteResource::collection($favorites);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function createOrDeleteFavoritePackage(Request $request)
    {
        $validatedData = Validator::make(
            $request->only(
                "package_id",
                "favorite"
            ),
            [
                "package_id" => "required|integer",
                "favorite" => "required|boolean"
            ]
        )
            ->validate();


        $requestUser = auth()->user();
        $package = $this->packageService->getPackageById($validatedData["package_id"]);
        if (is_null($package)) {
            throw new NotFoundHttpException("Package not found!");
        }
        $this->service->createOrDeleteFavoriteByType($requestUser, EFavoriteType::PACKAGE, $package, $validatedData["favorite"]);
        Cache::forget(cache_key("list-favorite-packages", [ $requestUser->id, $package->language_id, 0 ]));

        return response()->success();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function createOrDeleteFavoriteSort(Request $request)
    {
        $validatedData = Validator::make(
            $request->only(
                "sort_id",
                "favorite"
            ),
            [
                "sort_id" => "required|integer",
                "favorite" => "required|boolean"
            ]
        )
            ->validate();


        $requestUser = auth()->user();
        $sort = $this->wordSortService->getSortById($validatedData["sort_id"]);
        if (is_null($sort)) {
            throw new NotFoundHttpException("Sort not found!");
        }
        $this->service->createOrDeleteFavoriteByType($requestUser, EFavoriteType::WORD, $sort, $validatedData["favorite"]);
        Cache::forget(cache_key("list-favorite-sorts", [ $requestUser->id, $sort->language_id, 0 ]));

        return response()->success();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function createOrDeleteFavoriteArticle(Request $request)
    {
        $validatedData = Validator::make(
            $request->only(
                "article_id",
                "favorite"
            ),
            [
                "article_id" => "required|integer",
                "favorite" => "required|boolean"
            ]
        )
            ->validate();


        $requestUser = auth()->user();
        $article = Article::find($validatedData["article_id"]);
        if (is_null($article)) {
            throw new NotFoundHttpException("Article not found!");
        }
        $this->service->createOrDeleteFavoriteByType($requestUser, EFavoriteType::ARTICLE, $article, $validatedData["favorite"]);
        Cache::forget(cache_key("list-favorite-articles", [ $requestUser->id, $article->language_id, 0 ]));

        return response()->success();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function deleteFavorites(Request $request)
    {
        $validatedData = Validator::make(
            $request->only(
                "favorites"
            ),
            [
                "favorites" => "required|min:1|array"
            ]
        )
            ->validate();

        $requestUser = auth()->user();
        $initialFavorite = $this->service->getFavoriteById($validatedData["favorites"][0] ?? 0);

        if (is_null($initialFavorite)) {
            throw new NotFoundHttpException("Invalid favorite in array!");
        }

        $this->service->deleteFavorites($requestUser, $validatedData["favorites"]);

        $initialFavorite->type === EFavoriteType::ARTICLE && Cache::forget(cache_key("list-favorite-articles", [ $requestUser->id, $initialFavorite->language_id, 0 ]));
        $initialFavorite->type === EFavoriteType::PACKAGE && Cache::forget(cache_key("list-favorite-packages", [ $requestUser->id, $initialFavorite->language_id, 0 ]));
        $initialFavorite->type === EFavoriteType::WORD && Cache::forget(cache_key("list-favorite-sorts", [ $requestUser->id, $initialFavorite->language_id, 0 ]));

        return response()->success();
    }
}
