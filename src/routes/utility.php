<?php

use App\Http\Controllers\Utilities\UploadController;
use App\Http\Controllers\Utilities\VideoController;
use Illuminate\Support\Facades\Route;

Route::post("upload/file", [ UploadController::class, "uploadFile" ]);
Route::post("upload/video/url", [ UploadController::class, "getVideoSignedUrl" ]);
