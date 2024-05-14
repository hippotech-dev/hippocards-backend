<?php

use App\Enums\EUserRole;
use App\Http\Controllers\System\AuthController;
use App\Http\Controllers\Web\AuthController as WebAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get("content/identity", [ AuthController::class, "getContentIdentity" ])->middleware(["jwt.auth", "role:" . EUserRole::SUPERADMIN->value]);
Route::get("content/identity", [ AuthController::class, "getContentIdentity" ])->middleware(["jwt.auth"]);
Route::get("web/academy/identity", [ WebAuthController::class, "getAcademyIdentity" ])->middleware("jwt.auth");
Route::get("sso/url", [ AuthController::class, "getSSOUrl" ]);
Route::post("token", [ AuthController::class, "getSSOToken" ]);
