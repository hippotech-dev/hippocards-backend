<?php

use App\Http\Controllers\SSO\OAuthClientController;
use App\Http\Controllers\SSO\SSOController;
use Illuminate\Http\Request;
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
    "oauth-client" => OAuthClientController::class
]);

Route::post("authorize", [ SSOController::class, "authorizeUser" ]);
Route::post("token", [ SSOController::class, "getAuthenticationToken" ]);
