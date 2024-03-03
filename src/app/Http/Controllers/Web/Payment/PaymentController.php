<?php

namespace App\Http\Controllers\Web\Payment;

use App\Enums\EPaymentOrderItemType;
use App\Enums\EPaymentOrderType;
use App\Http\Controllers\Controller;
use App\Http\Resources\Web\Payment\PaymentInvoiceResource;
use App\Http\Services\PaymentService;
use App\Http\Services\QPayService;
use App\Models\Payment\PaymentInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $service)
    {
        $this->middleware("jwt.auth");
    }

    /**
     * Create invoice
     */
    public function createInvoice(Request $request)
    {
        $validatedData = Validator::make(
            $request->only(
                "type",
                "items",
                "redirect_uri"
            ),
            [
                "type" => ["required", Rule::in(EPaymentOrderType::cases())],
                "redirect_uri" => "sometimes|string",
                "items" => "required|array",
                "items.*.object_id" => "required|integer",
                "items.*.object_type" => ["required", Rule::in(EPaymentOrderItemType::cases())]
            ]
        )
            ->validate();

        $requestUser = auth()->user();

        $invoice = $this->service->createInvoice($requestUser, $validatedData);

        return new PaymentInvoiceResource($invoice);
    }

    /**
     * Create QPay invoice
     */
    public function createQpayInvoice(PaymentInvoice $invoice)
    {
        $result = $this->service->createQpayInvoice($invoice);

        return response()->success([
            "invoice" => new PaymentInvoiceResource($invoice),
            "qpay" => $result
        ]);
    }

    /**
     * QPay callback
     */

    public function qpayCallback(Request $request, PaymentInvoice $invoice)
    {
        $result = $this->service->handleQPayCallback($invoice);

        return response()->success();
    }
}
