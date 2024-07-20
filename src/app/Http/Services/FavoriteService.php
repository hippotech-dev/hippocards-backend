<?php

namespace App\Http\Services;

use App\Enums\EFavoriteType;
use App\Models\Package\Baseklass;
use App\Models\User\User;
use App\Models\Utility\Favorite;

class FavoriteService
{
    public function __construct(private PackageService $packageService)
    {
    }

    protected function getFilterModel($filters)
    {
        return [
            "language" => [ "where", "language_id" ]
        ];
    }

    public function getFavoritePackages(User $user, array $filters = [])
    {
        return filter_query_with_model(Favorite::package(), $this->getFilterModel($filters), $filters)
            ->where("type", EFavoriteType::PACKAGE)
            ->where("user_id", $user->id)
            ->whereHas("object")
            ->orderBy("id", "desc")
            ->cursorPaginate(page_size())
            ->withQueryString();
    }

    public function getFavoriteSorts(User $user, array $filters = [])
    {
        return filter_query_with_model(Favorite::sort(), $this->getFilterModel($filters), $filters)
            ->where("type", EFavoriteType::WORD)
            ->where("user_id", $user->id)
            ->whereHas("object")
            ->orderBy("id", "desc")
            ->cursorPaginate(page_size())
            ->withQueryString();
    }

    public function getFavoriteArticles(User $user, array $filters = [])
    {
        return filter_query_with_model(Favorite::article(), $this->getFilterModel($filters), $filters)
            ->where("type", EFavoriteType::ARTICLE)
            ->where("user_id", $user->id)
            ->whereHas("object")
            ->orderBy("id", "desc")
            ->cursorPaginate(page_size())
            ->withQueryString();
    }

    public function createFavoriteSort()
    {

    }

    public function createFavorite(User $user, EFavoriteType $type, int $objectId, bool $value)
    {
        // switch ($type) {
        //     case EFavoriteType::
        // }
    }
}
