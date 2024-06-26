<?php

use App\Http\Controllers\Mobile\Hippocards\PackageController;
use App\Http\Controllers\Mobile\Hippocards\WordController;
use Illuminate\Support\Facades\Route;

Route::apiResources([
    "word" => WordController::class
]);

Route::prefix("word")->group(function () {
    Route::get("action/search", [ PackageController::class, "searchWords" ]);
    Route::get("action/memorized-words", [ PackageController::class, "getMemorizedWords" ]);
});
