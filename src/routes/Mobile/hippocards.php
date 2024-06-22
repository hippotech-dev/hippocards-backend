<?php

use App\Http\Controllers\Mobile\Hippocards\PackageController;
use Illuminate\Support\Facades\Route;

Route::prefix("words")->group(function () {
    Route::get("action/search", [ PackageController::class, "searchWords" ]);
});
