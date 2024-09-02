<?php

use App\Http\Controllers\Utility\AudioController;
use App\Http\Controllers\Utility\SentenceController;
use App\Http\Controllers\Utility\UploadController;
use App\Http\Controllers\Utility\UserActivityController;
use App\Http\Controllers\Utility\UtilityController;
use Illuminate\Support\Facades\Route;

Route::apiResources([
    "user-activity" => UserActivityController::class,
    "sentence" => SentenceController::class
]);

Route::prefix("upload")->group(function () {
    Route::post("file", [ UploadController::class, "uploadFile" ]);
    Route::post("url", [ UploadController::class, "uploadFileWithURL" ]);
    Route::post("unsplash", [ UploadController::class, "uploadUnsplashUrls" ]);
    Route::post("video/url", [ UploadController::class, "getVideoSignedUrl" ]);
    Route::post("video/job/submit", [ UploadController::class, "setTranscoderJob" ]);
    Route::post("video/job/complete", [ UploadController::class, "completeTranscoderJob" ]);
    Route::post("video/{asset}/otp", [ UploadController::class, "getVideoPlaybackAndOTPInfo" ]);
});

Route::prefix("audio")->group(function () {
    Route::post("generate", [ AudioController::class, "generateAudio" ]);
});

Route::post("webhook/drm-video-ready", [ UploadController::class, "webhookVDOVideoSuccess" ]);

Route::get("version", [ UtilityController::class, "getCodeVersion" ]);
