<?php

use App\Http\Controllers\Utilities\UploadController;
use App\Http\Controllers\Web\Academy\CertificateController;
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
    "user/certificate" => CertificateController::class
]);

Route::prefix("course")->group(function () {
    Route::prefix("{course}")->group(function () {
        Route::get("learn", [ CourseController::class, "getLearnData" ]);
        Route::get("final-exam", [ CourseController::class, "getFinalExamData" ]);
        Route::get("final-exam/{examInstance}/answer", [ CourseController::class, "getFinalExamCorrectAnswers" ]);
        Route::post("final-exam/{examInstance}/submit", [ CourseController::class, "submitFinalExamData" ]);
        Route::post("final-exam/{examInstance}/finish", [ CourseController::class, "finishFinalExamData" ]);
        Route::post("block/{block}/progress", [ CourseBlockController::class, "setCourseCompletion" ]);
    });

    Route::prefix("block")->group(function () {
        Route::post("{block}/sentence-keyword", [ CourseBlockController::class, "submitSentenceKeywordResponse" ]);
        Route::get("{block}/sentence-keyword", [ CourseBlockController::class, "getSentenceKeywordsResponses" ]);
    });

    Route::prefix("exam")->group(function () {
        Route::get("{block}", [ CourseBlockController::class, "getCourseExamData" ]);
        Route::post("{block}/submit", [ CourseBlockController::class, "submitExamAnswers" ]);
    });
});
