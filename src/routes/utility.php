<?php

use App\Http\Controllers\Utilities\UploadController;
use Illuminate\Support\Facades\Route;

Route::post("upload/file", [ UploadController::class, "uploadFile" ]);
