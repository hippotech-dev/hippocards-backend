<?php

use App\Http\Controllers\System\Content\SubscriptionController;
use App\Http\Controllers\System\Content\UserController;
use App\Http\Controllers\System\Content\PackageController;
use App\Http\Controllers\System\Content\PromoController;
use App\Http\Controllers\System\Content\WordDetailController;
use App\Http\Controllers\System\Content\WordImageController;
use App\Http\Controllers\System\Content\WordSortController;
use App\Http\Controllers\System\Content\WordSynonymController;
use App\Http\Controllers\System\Utility\CategoryController;
use App\Http\Controllers\System\Utility\LanguageController;
use App\Http\Controllers\System\Utility\SubscriptionPlanController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::apiResources([
    "promo" => PromoController::class,
    "package" => PackageController::class,
    "sort" => WordSortController::class,
    "word.detail" => WordDetailController::class,
    "word.image" => WordImageController::class,
    "word/synonym" => WordSynonymController::class,
    "account/user" => UserController::class,
    "utility/subscription-plan" => SubscriptionPlanController::class,
    "utility/language" => LanguageController::class,
    "utility/category" => CategoryController::class
]);

Route::prefix("account")->group(function () {
    Route::prefix("subscription")->group(function () {
        Route::get("{user}", [ SubscriptionController::class, "index" ]);
        Route::patch("{user}", [ SubscriptionController::class, "store" ]);
    });
    Route::post("user/{user}/set-default-password", [ UserController::class, "setDefaultPasswordForUser" ]);
    Route::post("delete-user", [ UserController::class, "deleteAccountRequest" ]);
});

Route::prefix("package")->group(function () {
    Route::get("{package}/sort", [ PackageController::class, "getPackageSorts" ]);
    Route::prefix("action")->group(function () {
        Route::get("search", [ PackageController::class, "search" ]);
    });
});

Route::prefix("word")->group(function () {
    Route::prefix("action")->group(function () {
        Route::get("search", [ WordSortController::class, "index" ]);
    });
});
