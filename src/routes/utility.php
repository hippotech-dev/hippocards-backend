<?php

use App\Http\Controllers\Utilities\UploadController;
use App\Http\Controllers\Utilities\VideoController;
use Illuminate\Support\Facades\Route;

Route::prefix("upload")->group(function () {
    Route::post("file", [ UploadController::class, "uploadFile" ]);
    Route::post("video/url", [ UploadController::class, "getVideoSignedUrl" ]);
    Route::post("video/job/submit", [ UploadController::class, "setTranscoderJob" ]);
    Route::post("video/job/complete", [ UploadController::class, "completeTranscoderJob" ]);
    Route::post("video/{asset}/otp", [ UploadController::class, "getVideoPlaybackAndOTPInfo" ]);
});
