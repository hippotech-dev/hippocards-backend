<?php

use App\Http\Controllers\System\AuthController;
use App\Http\Controllers\Web\AuthController as WebAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get("academy/identity", [ AuthController::class, "getAcademyIdentity" ])->middleware("jwt.auth");
Route::get("web/academy/identity", [ WebAuthController::class, "getAcademyIdentity" ])->middleware("jwt.auth");
Route::get("sso/url", [ AuthController::class, "getSSOUrl" ]);
Route::post("token", [ AuthController::class, "getSSOToken" ]);
