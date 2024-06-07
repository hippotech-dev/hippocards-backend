<?php

namespace App\Http\Services\v0;

use App\Models\v1\Payment\QpayInvoice;

class PaymentService
{
    public function __construct()
    {
    }

    public function getInvoicesWithPage(array $filters = [], array $with = [])
    {
        return filter_query_with_model(QpayInvoice::query(), $this->getFilterModels($filters), $filters)->with($with)->where("is_guest", false)->orderBy("id", "desc")->simplePaginate(page_size());
    }

    public function getInvoiceById(int $id, array $with = [])
    {
        return QpayInvoice::with($with)->find($id);
    }

    protected function getFilterModels(array $filters)
    {
        return [
            "start_date" => [ "moreEqualThan", "created_at" ],
            "end_date" => [ "lessThan", "created_at" ],
            "user_id" => [ "where", "user_id" ],
            "object_id" => [ "where", "object_id" ]
        ];
    }
}
