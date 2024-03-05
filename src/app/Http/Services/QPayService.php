<?php

namespace App\Http\Services;

use App\Enums\EAccessTokenType;
use App\Exceptions\AppException;
use App\Exceptions\PaymentException;
use App\Models\Payment\PaymentInvoice;
use App\Models\User\User;
use App\Models\Utility\AccessToken;
use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class QPayService
{
    private string $username = "";
    private string $password = "";

    public function __construct(private AccessTokenService $accessTokenService)
    {
        $this->username = Config::get("credentials.qpay.USERNAME");
        $this->password = Config::get("credentials.qpay.PASSWORD");
    }

    public function checkInvoiceStatus(PaymentInvoice $invoice)
    {
        $qpayToken = $this->accessTokenService->getLatestTokenByType(EAccessTokenType::QPAY);
        $checkData =  $this->fetchInvoiceStatus($invoice, $qpayToken);

        $paidAmount = $checkData["paid_amount"] ?? 0;

        return [
            "data" => $checkData,
            "is_paid_full" => $paidAmount === $invoice->total_pending_amount,
            "paid_amount" => $paidAmount
        ];
    }

    public function getAccessToken()
    {
        $qpayToken = $this->accessTokenService->getLatestTokenByType(EAccessTokenType::QPAY);

        if (is_null($qpayToken) || $this->accessTokenService->isAccessTokenExpired($qpayToken)) {
            $qpayToken = $this->fetchAccessToken();
        }

        return $qpayToken;
    }

    public function createInvoice(PaymentInvoice $invoice)
    {
        $qpayToken = $this->getAccessToken();

        return $this->fetchQpayInvoice($invoice, $qpayToken);
    }

    private function fetchQpayInvoice(
        PaymentInvoice $invoice,
        AccessToken $token
    ) {
        $response = Http::contentType("application/json")
            ->withHeader(
                "Authorization",
                "Bearer " . $token->access_token
            )
            ->post(
                "https://merchant.qpay.mn/v2/invoice",
                [
                    "invoice_code" => Config::get("credentials.qpay.INVOICE_CODE"),
                    "sender_invoice_no" => $invoice->identifier . "",
                    "invoice_receiver_code" => $invoice->user_id . "",
                    "invoice_description" => "Нэхэмжлэл",
                    "sender_branch_code" => "HIPPOCARDS_QPAY01",
                    "amount" => $invoice->total_pending_amount,
                    "callback_url" => url("/v1/web/payment/qpay/callback/" . $invoice->id)
                ]
            );

        $body = $response->json();

        if ($response->failed()) {
            throw new PaymentException($invoice, $body);
        }

        return $body;
    }

    private function fetchInvoiceStatus(PaymentInvoice $invoice, AccessToken $token)
    {
        $response = Http::contentType('application/json')
            ->withHeader(
                "Authorization",
                "Bearer " . $token->access_token
            )
            ->post(
                "https://merchant.qpay.mn/v2/payment/check",
                [
                    "object_type" => "INVOICE",
                    "object_id" => $invoice->merchant_invoice_id
                ]
            );

        $body = $response->json();

        if ($response->failed()) {
            throw new PaymentException($invoice, $body);
        }

        return $body;
    }

    private function fetchAccessToken()
    {
        $response = Http::contentType('Content-type: application/json')
            ->withBasicAuth($this->username, $this->password)
            ->post(
                "https://merchant.qpay.mn/v2/auth/token",
                []
            );

        if ($response->failed()) {
            throw new AppException($response->body());
        }

        $body = $response->json();

        return $this->accessTokenService->createToken(
            EAccessTokenType::QPAY,
            $body["access_token"],
            $body["expires_in"],
            [
                'refresh_token' => $body["refresh_token"],
                "refresh_expires_in" => $body["refresh_expires_in"],
            ]
        );
    }
}
