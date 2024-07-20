<?php

use App\Http\Controllers\Mobile\Hippocards\FavoriteController;
use App\Http\Controllers\Mobile\Hippocards\PackageController;
use App\Http\Controllers\Mobile\Hippocards\UserController;
use App\Http\Controllers\Mobile\Hippocards\WordSortController;
use Illuminate\Support\Facades\Route;

Route::apiResources([
    "sort" => WordSortController::class,
    "package" => PackageController::class
]);

Route::prefix("package")->group(function () {
    Route::get("{package}/sort", [ PackageController::class, "getPackageSorts" ]);
});

Route::prefix("favorite")->group(function () {
    Route::get("action/package", [ FavoriteController::class, "getFavoritePackages" ]);
    Route::get("action/sort", [ FavoriteController::class, "getFavoriteSorts" ]);
    Route::get("action/article", [ FavoriteController::class, "getFavoriteArticles" ]);
});

Route::prefix("sort")->group(function () {
    Route::get("action/memorized-words", [ WordSortController::class, "getMemorizedSorts" ]);
});

Route::prefix("account")->group(function () {
    Route::delete("delete-user", [ UserController::class, "deleteUserData" ]);
});
