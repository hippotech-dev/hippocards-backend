<?php

use App\Http\Controllers\Mobile\Hippocards\FavoriteController;
use App\Http\Controllers\Mobile\Hippocards\PackageController;
use App\Http\Controllers\Mobile\Hippocards\UserController;
use App\Http\Controllers\Mobile\Hippocards\UserPreferenceController;
use App\Http\Controllers\Mobile\Hippocards\WordSortController;
use Illuminate\Support\Facades\Route;

Route::apiResources([
    "sort" => WordSortController::class,
    "package" => PackageController::class
]);

Route::prefix("package")->group(function () {
    Route::get("{package}/sort", [ PackageController::class, "getPackageSorts" ]);
    Route::post("{package}/progress", [ PackageController::class, "submitPackageProgress" ]);

    Route::prefix("action")->group(function () {
        Route::get("recent", [ PackageController::class, "getRecentLearningPackages" ]);

    });
});

Route::prefix("favorite")->group(function () {
    Route::prefix("action")->group(function () {
        Route::get("package", [ FavoriteController::class, "getFavoritePackages" ]);
        Route::get("sort", [ FavoriteController::class, "getFavoriteSorts" ]);
        Route::get("article", [ FavoriteController::class, "getFavoriteArticles" ]);

        Route::post("package", [ FavoriteController::class, "createOrDeleteFavoritePackage" ]);
        Route::post("sort", [ FavoriteController::class, "createOrDeleteFavoriteSort" ]);
        Route::post("article", [ FavoriteController::class, "createOrDeleteFavoriteArticle" ]);

        Route::delete("delete", [ FavoriteController::class, "deleteFavorites" ]);
    });
});

Route::prefix("sort")->group(function () {
    Route::post("{sort}/custom-detail", [ WordSortController::class, "createCustomKeywordForSort" ]);

    Route::prefix("action")->group(function () {
        Route::get("memorized-words", [ WordSortController::class, "getMemorizedSorts" ]);
    });
});

Route::prefix("word")->group(function () {
    Route::get("action/recent", [ WordSortController::class, "getRecentLearningWords" ]);
});

Route::prefix("account")->group(function () {
    Route::delete("delete-user", [ UserController::class, "deleteUserData" ]);
    Route::post("preference", [ UserPreferenceController::class, "store" ]);
});


Route::prefix("utility")->group(function () {
    Route::get("onboarding-preferences", [ UserPreferenceController::class, "getOnboardingPreferencesData" ]);
});
