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

Route::post("validate", [ SSOController::class, "checkAuthorizeRequest" ])->name("sso-validate");
Route::post("authorize", [ SSOController::class, "authorizeUser" ])->name("sso-authorize");
Route::post("register", [ SSOController::class, "registerUser" ]);
Route::post("token", [ SSOController::class, "getAuthenticationToken" ])->name("sso-token");
Route::post("forgot/password", [ SSOController::class, "forgotPassword" ]);

Route::post("confirmation/verify", [ SSOController::class, "verifyCredential" ]);
Route::post("confirmation/approve", [ SSOController::class, "approveConfirmation" ]);

Route::post("/account/email", [ SSOController::class, "updateUserEmail" ]);
Route::post("/account/phone", [ SSOController::class, "updateUserPhone" ]);

Route::post("check/value", [ SSOController::class, "checkUserCredential" ]);

Route::prefix("social")->group(function () {
    Route::post("google/auth", [ SSOController::class, "authorizeGmail" ]);
    Route::get("google/callback", [ SSOController::class, "callbackGmail" ]);
    Route::post("facebook/auth", [ SSOController::class, "authorizeGacebook" ]);
    Route::get("facebook/callback", [ SSOController::class, "callbackFacebook" ]);
});
