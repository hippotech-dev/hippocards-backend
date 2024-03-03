<?php

namespace App\Jobs;

use App\Http\Services\PaymentOrderService;
use App\Models\Payment\PaymentInvoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PaymentOrderProcessingJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private PaymentInvoice $invoice)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(PaymentOrderService $paymentOrderService): void
    {
        $order = $paymentOrderService->getPaymentOrderFromInvoice($this->invoice);
        $paymentOrderService->createSuccessfullOrderObjects($order);
    }
}
