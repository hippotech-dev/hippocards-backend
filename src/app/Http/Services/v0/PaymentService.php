<?php

namespace App\Http\Services\v0;

use App\Enums\EStatus;
use App\Models\v0\QpayInvoice;

class PaymentService
{
    public function __construct()
    {
    }

    public static function getInvoiceStatus(mixed $invoice)
    {
        $check = $invoice->result_code === "0"
            && $invoice->status === 1
            && $invoice->sub_check === 1;

        return $check ? EStatus::SUCCESS : EStatus::PENDING;
    }

    public function getInvoicesWithPage(array $filters = [], array $with = [])
    {
        return filter_query_with_model(QpayInvoice::query(), $this->getFilterModels($filters), $filters)->with($with)->orderBy("id", "desc")->paginate(page_size());
    }

    public function getInvoiceById(int $id, array $with = [])
    {
        return QpayInvoice::with($with)->find($id);
    }

    protected function getFilterModels(array $filters)
    {
        return [
            "start_date" => [ "moreEqualThan", "created_at" ],
            "end_date" => [ "lessEqualThan", "created_at" ],
            "user_id" => [ "where", "user_id" ],
            "object_id" => [ "where", "object_id" ],
            "id" => [ "where", "id" ],
            "status" => [
                [ "where" ],
                [
                    [
                        "name" => null,
                        "value" => function ($query) use ($filters) {
                            $status = $filters["status"] ?? -1;
                            switch ($status) {
                                case EStatus::PENDING->value:
                                    return $query->pending();
                                case EStatus::SUCCESS->value:
                                    return $query->paid();
                                default:
                                    return $query;
                            }
                        }
                    ]
                ]
            ],
            "filter" => [
                [ "where" ],
                [
                    [
                        "name" => null,
                        "value" => function ($query) use ($filters) {
                            return $query
                                ->where("id", $filters["filter"])
                                ->orWhere("user_id", $filters["filter"]);
                        }
                    ]
                ]
            ]
        ];
    }
}
