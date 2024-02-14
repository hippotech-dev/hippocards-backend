<?php

use App\Http\Controllers\System\Academy\CourseController;
use App\Http\Controllers\System\Academy\CourseDetailController;
use App\Http\Controllers\System\Academy\CourseGroupController;
use App\Http\Controllers\System\Academy\CoursePackageController;
use App\Http\Controllers\System\Academy\GroupBlockController;
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
    "course.package" => CoursePackageController::class,
    "course.group" => CourseGroupController::class,
    "group.block" => GroupBlockController::class,
]);

Route::prefix("course")->group(function () {
    Route::prefix("{course}")->group(function () {
        Route::prefix("action")->group(function () {
            Route::get("kanban", [ CourseController::class, "getCourseKanbanData" ]);
            Route::post("import", [ CourseController::class, "automatedGroupsAndBlockCreate" ]);
        });
        Route::post("group/{group}/shift", [ CourseGroupController::class, "shiftGroup" ]);
    });
});

Route::prefix("group")->group(function () {
    Route::prefix("{group}")->group(function () {
        Route::post("block/{block}/shift", [ GroupBlockController::class, "shiftBlock" ]);
    });
});
