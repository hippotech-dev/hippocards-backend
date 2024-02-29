<?php

use App\Http\Controllers\Web\Academy\CourseBlockController;
use App\Http\Controllers\Web\Academy\CourseCompletionController;
use App\Http\Controllers\Web\Academy\CourseController;
use App\Http\Controllers\Web\Academy\CourseGroupController;
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
    "course.group" => CourseGroupController::class,
    "course.block" => CourseBlockController::class,
    "course.completion" => CourseCompletionController::class,
]);

Route::prefix("course")->group(function () {
    Route::prefix("{course}")->group(function () {
        Route::get("learn", [ CourseController::class, "getLearnData" ]);
    });
    Route::get("exam/{block}", [ CourseBlockController::class, "getCourseExamData" ]);
    Route::post("exam/{block}/submit", [ CourseBlockController::class, "submitExamAnswers" ]);
});
