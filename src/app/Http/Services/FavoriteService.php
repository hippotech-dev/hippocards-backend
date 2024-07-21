<?php

namespace App\Http\Services;

use App\Enums\EFavoriteType;
use App\Models\Article\Article;
use App\Models\Package\Baseklass;
use App\Models\Package\Sort;
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
            "language" => [ "where", "language_id" ],
            "type" => [ "where", "type" ],
            "object_id" => [ "where", "object_id" ],
        ];
    }

    public function getFavoriteById(int $id, array $with = [])
    {
        return Favorite::with($with)->find($id);
    }

    public function getUserFavorite(User $user, array $filters, array $with = [])
    {
        return filter_query_with_model($user->favorites()->with($with), $this->getFilterModel($filters), $filters)->first();
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

    public function createFavoriteSort(User $user, Sort $sort, bool $value)
    {
        $favorite = $this->getUserFavorite($user, [ "object_id" => $sort->id, "type" => EFavoriteType::WORD ]);
        if (!$value && !is_null($favorite)) {
            $favorite->delete();
        }
        if ($value && is_null($favorite)) {
            $favorite = $this->createFavorite(
                $user,
                [ "object_id" => $sort->id, "object_type" => Sort::class, "language_id" => $sort->language_id, "type" => EFavoriteType::WORD ]
            );
        }

        return $favorite;
    }

    public function createFavoritePackage(User $user, Baseklass $package, bool $value)
    {
        $favorite = $this->getUserFavorite($user, [ "object_id" => $package->id, "type" => EFavoriteType::PACKAGE ]);
        if (!$value && !is_null($favorite)) {
            $favorite->delete();
        }
        if ($value && is_null($favorite)) {
            $favorite = $this->createFavorite(
                $user,
                [ "object_id" => $package->id, "object_type" => Baseklass::class, "language_id" => $package->language_id, "type" => EFavoriteType::PACKAGE ]
            );
        }

        return $favorite;
    }

    public function createFavoriteArticle(User $user, Article $article, bool $value)
    {
        $favorite = $this->getUserFavorite($user, [ "object_id" => $article->id, "type" => EFavoriteType::ARTICLE ]);
        if (!$value && !is_null($favorite)) {
            $favorite->delete();
        }
        if ($value && is_null($favorite)) {
            $favorite = $this->createFavorite(
                $user,
                [ "object_id" => $article->id, "object_type" => Article::class, "language_id" => $article->language_id ?? 1, "type" => EFavoriteType::ARTICLE ]
            );
        }

        return $favorite;
    }

    public function createOrDeleteFavoriteByType(User $user, EFavoriteType $type, mixed $object, bool $value)
    {
        switch ($type) {
            case EFavoriteType::PACKAGE:
                $favorite = $this->createFavoritePackage($user, $object, $value);
                break;
            case EFavoriteType::WORD:
                $favorite = $this->createFavoriteSort($user, $object, $value);
                break;
            case EFavoriteType::ARTICLE:
                $favorite = $this->createFavoriteArticle($user, $object, $value);
                break;
        }

        return $favorite;
    }

    public function createFavorite(User $user, array $data)
    {
        return $user->favorites()->create($data);
    }

    public function deleteFavorites(User $user, array $favorites)
    {
        return $user->favorites()->whereIn("id", $favorites)->delete();
    }
}
