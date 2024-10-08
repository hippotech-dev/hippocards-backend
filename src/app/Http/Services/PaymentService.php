<?php

namespace App\Http\Services;

use App\Enums\EPaymentMethodType;
use App\Enums\EStatus;
use App\Exceptions\PaymentException;
use App\Jobs\PaymentOrderProcessingJob;
use App\Models\Payment\PaymentInvoice;
use App\Models\User\User;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function __construct(private QPayService $qpayService, private PaymentOrderService $orderService, private PromoService $promoService)
    {
    }

    public function getInvoiceById(int $id, array $with = [])
    {
        return PaymentInvoice::with($with)->find($id);
    }

    public function createInvoice(User $user, array $data)
    {
        return DB::transaction(function () use ($user, $data) {
            $identifier = uniqid(date("Y"));
            $promo = array_key_exists("v3_promo_code_id", $data)
                ? $this->promoService->checkAndGetPromo($data["v3_promo_code_id"])
                : null;
            $order = $this->orderService->createOrder($user, $data, $promo);
            $invoice = $user->invoices()->create([
                "identifier" => $identifier,
                "v3_payment_order_id" => $order->id,
                "v3_promo_code_id" => $promo->id ?? null,
                "v3_promo_code_value" => $promo->code ?? null,
                "total_amount" => $order->total_amount,
                "total_pending_amount" => $order->total_amount,
                "total_discount_amount" => $order->total_discount_amount,
                "total_paid_amount" => 0,
                "redirect_url" => $data["redirect_uri"] ?? null,
            ]);
            $invoice->setRelation("paymentOrder", $order);
            return $invoice;
        });
    }

    public function createQpayInvoice(PaymentInvoice $invoice)
    {
        $result = $this->qpayService->createInvoice($invoice);
        $this->setInvoicePaymentMethod($invoice, EPaymentMethodType::QPAY);
        if (array_key_exists("invoice_id", $result)) {
            $this->setInvoiceMerchandInvoice($invoice, $result["invoice_id"]);
        }

        return $result;
    }

    public function handleQPayCallback(PaymentInvoice $invoice)
    {
        DB::transaction(function () use ($invoice) {
            $user = $invoice->user()->first();

            [ "data" => $data, "paid_amount" => $paidAmount ] = $this->qpayService->checkInvoiceStatus($invoice);

            $this->handleCheckInvoice($invoice, $paidAmount, $data);
            $this->handleSuccessfulInvoice($invoice);
            $this->handlePaymentPromo($user, $invoice);
        });
    }

    public function handlePaymentPromo(User $user, PaymentInvoice $invoice)
    {
        $promo = $invoice->promoCode()->first();
        if (is_null($promo)) {
            return;
        }
        $this->promoService->usePromo($user, $promo);
    }

    public function handleCheckInvoice(PaymentInvoice $invoice, int $paidAmount, array $data)
    {
        $this->setInvoicePaidAmount($invoice, $paidAmount);
        $this->createInvoiceResponse($invoice, $data);
        $this->setInvoiceStatus($invoice);

        // if (!$this->isInvoicePaid($invoice)) {
        //     throw new PaymentException($invoice, [ "status" => "Payment not paid!" ]);
        // }
    }

    public function handleSuccessfulInvoice(PaymentInvoice $invoice)
    {
        dispatch(new PaymentOrderProcessingJob($invoice));
    }

    public function isInvoicePaid(PaymentInvoice $invoice)
    {
        return $invoice->total_pending_amount === 0 && $invoice->total_paid_amount === $invoice->total_amount;
    }

    private function createInvoiceResponse(PaymentInvoice $invoice, array $data)
    {
        return $invoice->responses()->create([
            "identifier" => $invoice->identifier,
            "payment_method" => $invoice->payment_method,
            "content" => $data,
            "status_code" => 0,
            "v3_payment_order_id" => $invoice->v3_payment_order_id
        ]);
    }

    private function setInvoiceMerchandInvoice(PaymentInvoice $invoice, string $id)
    {
        $invoice->merchant_invoice_id = $id;
        return $invoice->save();
    }

    private function setInvoiceStatus(PaymentInvoice $invoice)
    {
        $check = $this->isInvoicePaid($invoice);
        if ($check) {
            $invoice->status = EStatus::SUCCESS;
        } else {
            $invoice->status = EStatus::PENDING;
        }
        return $invoice->save();
    }

    private function setInvoicePaymentMethod(PaymentInvoice $invoice, EPaymentMethodType $method)
    {
        $invoice->payment_method = $method;
        return $invoice->save();
    }

    private function setInvoicePaidAmount(PaymentInvoice $invoice, int $paidAmount)
    {
        $invoice->total_pending_amount = $invoice->total_amount - $paidAmount;
        $invoice->total_paid_amount = $paidAmount;
        return $invoice->save();
    }
}
