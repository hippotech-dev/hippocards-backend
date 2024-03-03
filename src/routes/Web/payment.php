<?php

use App\Http\Controllers\Web\Academy\CourseBlockController;
use App\Http\Controllers\Web\Academy\CourseCompletionController;
use App\Http\Controllers\Web\Academy\CourseController;
use App\Http\Controllers\Web\Academy\CourseGroupController;
use App\Http\Controllers\Web\Payment\PaymentController;
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

Route::post("invoice", [ PaymentController::class, "createInvoice" ]);

Route::post("qpay/invoice/{invoice}/", [ PaymentController::class, "createQpayInvoice" ]);
Route::post("qpay/callback/{invoice}/", [ PaymentController::class, "qpayCallback" ]);
