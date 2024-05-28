<?php

use App\Http\Controllers\System\Academy\BlockImageController;
use App\Http\Controllers\System\Academy\BlockVideoController;
use App\Http\Controllers\System\Academy\CourseController;
use App\Http\Controllers\System\Academy\CourseDetailController;
use App\Http\Controllers\System\Academy\CourseGroupController;
use App\Http\Controllers\System\Academy\CoursePackageController;
use App\Http\Controllers\System\Academy\GroupBlockController;
use App\Http\Controllers\System\Academy\VideoTimestampController;
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
    "block.video" => BlockVideoController::class,
    "block.image" => BlockImageController::class,
    "video.timestamp" => VideoTimestampController::class
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

    Route::prefix("block")->group(function () {
        Route::post("{block}/detail", [ GroupBlockController::class, "createUpdateBlockDetail" ]);
        Route::post("{block}/import-word-image", [ GroupBlockController::class, "importWordImagesToBlock" ]);
    });
});

Route::get("test", [ CourseController::class, "test" ]);
