<?php

namespace App\Http\Controllers\Web\Payment;

use App\Enums\EPaymentOrderItemType;
use App\Enums\EPaymentOrderType;
use App\Http\Controllers\Controller;
use App\Http\Resources\Web\Payment\PaymentInvoiceResource;
use App\Http\Services\PaymentService;
use App\Models\Payment\PaymentInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $service)
    {
        $this->middleware("jwt.auth", [
            "except" => "qpayCallback"
        ]);
    }

    /**
     * Get invoice
     */
    public function getInvoice(int $invoice)
    {
        $requestUser = auth()->user();
        $invoice = $this->service->getInvoiceById($invoice, ["paymentOrder"]);
        if (is_null($invoice) || $requestUser->id !== $invoice->user_id) {
            throw new NotFoundHttpException("Invoice not found!");
        }
        return new PaymentInvoiceResource($invoice);
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
                "redirect_uri",
                "promo_code_id"
            ),
            [
                "type" => ["required", Rule::in(EPaymentOrderType::cases())],
                "redirect_uri" => "sometimes|string",
                "items" => "required|array",
                "items.*.object_id" => "required|integer",
                "items.*.object_type" => ["required", Rule::in(EPaymentOrderItemType::cases())],
                "promo_code_id" => "sometimes|integer"
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
        $this->service->handleQPayCallback($invoice);

        return response()->success();
    }
}
