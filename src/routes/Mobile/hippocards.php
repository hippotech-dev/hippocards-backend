<?php

use App\Http\Controllers\Mobile\Hippocards\PackageController;
use App\Http\Controllers\Mobile\Hippocards\UserController;
use App\Http\Controllers\Mobile\Hippocards\WordSortController;
use Illuminate\Support\Facades\Route;

Route::apiResources([
    "word" => WordSortController::class,
    "package" => PackageController::class
]);

Route::prefix("word")->group(function () {
    Route::get("action/memorized-words", [ PackageController::class, "getMemorizedWords" ]);
});

Route::prefix("account")->group(function () {
    Route::delete("delete-user", [ UserController::class, "deleteUserData" ]);
});
