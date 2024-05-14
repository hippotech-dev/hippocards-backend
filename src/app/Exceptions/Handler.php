<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {

        });

        $this->reportable(function (PaymentException $e) {
            $invoice = $e->getInvoice();
            Log::channel("payment")->error(print_r($e->getBody(), true), [ $invoice->id, $invoice->user_id ]);
        })->stop();
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof AppException) {
            return response()->fail($exception->getMessage());
        }

        if ($exception instanceof UnauthorizedException) {
            return response()->fail($exception->getMessage(), 401);
        }

        if ($exception instanceof PaymentException) {
            return response()->fail($exception->getBody());
        }

        return parent::render($request, $exception);
    }


}
