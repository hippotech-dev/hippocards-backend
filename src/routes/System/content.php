<?php

use App\Http\Controllers\System\Content\AccountManagementController;
use App\Http\Controllers\System\Content\PackageController;
use App\Http\Controllers\System\Content\WordController;
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
    "package" => PackageController::class,
    "account/user" => AccountManagementController::class,
]);

Route::prefix("package")->group(function () {
    Route::prefix("action")->group(function () {
        Route::get("search", [ PackageController::class, "search" ]);
    });
});

Route::prefix("word")->group(function () {
    Route::prefix("action")->group(function () {
        Route::get("search", [ WordController::class, "search" ]);
    });
});
