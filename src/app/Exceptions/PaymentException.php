<?php

namespace App\Exceptions;

use App\Models\Payment\PaymentInvoice;
use Exception;

class PaymentException extends Exception
{
    public function __construct(private PaymentInvoice $invoice, private array $body)
    {
        parent::__construct("Payment request failed!");
    }

    public function getInvoice()
    {
        return $this->invoice;
    }

    public function getBody()
    {
        return $this->body;
    }
}
