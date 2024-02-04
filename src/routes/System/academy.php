<?php

use App\Http\Controllers\System\Academy\CourseController;
use App\Http\Controllers\System\Academy\CourseDetailController;
use App\Http\Controllers\System\Academy\CourseGroupController;
use App\Http\Controllers\System\Academy\GroupBlockController;
use App\Http\Controllers\System\AuthController;
use Illuminate\Http\Request;
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
    "course" => CourseController::class,
    "course.detail" => CourseDetailController::class,
    "course.group" => CourseGroupController::class,
    "group.block" => GroupBlockController::class,
]);

Route::prefix("course")->group(function () {
    Route::prefix("{course}")->group(function () {
        Route::post("group/{id}/shift", [ CourseGroupController::class, "shiftGroups" ]);
    });
});
