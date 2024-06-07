<?php

namespace App\Http\Controllers\System\Content\v0;

use App\Http\Controllers\Controller;
use App\Http\Services\OldPaymentService;
use App\Http\Services\v0\PaymentService;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function __construct(private PaymentService $service)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->only(
            "start_date",
            "end_date",
            "user_id",
            "object_id"
        );

        $invoices = $this->service->getInvoicesWithPage($filters, [ "user" ]);

        return response()->success($invoices);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
