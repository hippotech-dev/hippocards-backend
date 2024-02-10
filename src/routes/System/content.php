<?php

use App\Http\Controllers\System\Content\PackageController;
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
]);

Route::prefix("package")->group(function () {
    Route::prefix("action")->group(function () {
        Route::get("search", [ PackageController::class, "search" ]);
    });
});
