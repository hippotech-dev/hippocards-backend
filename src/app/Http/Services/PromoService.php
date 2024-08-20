<?php

namespace App\Http\Services;

use App\Enums\EPromoType;
use App\Enums\EPromoUsageType;
use App\Enums\EStatus;
use App\Exceptions\AppException;
use App\Models\Subscription\SubPlan;
use App\Models\Utility\PromoCode;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PromoService
{
    public function getFilterModel()
    {
        return [
            "code" => [ "where", "code" ],
            "type" => [ "where", "type" ],
            "usage_type" => [ "where", "usage_type" ],
            "total_quantity" => [ "where", "total_quantity" ],
            "status" => [ "where", "status" ],
            "description" => [ "where", "description" ],
            "amount" => [ "where", "amount" ],
            "amount_type" => [ "where", "amount_type" ],
            "context_type" => [ "where", "context_type" ],
        ];
    }

    public function getPromos(array $filters, array $with = [])
    {
        return filter_query_with_model(PromoCode::query(), $this->getFilterModel($filters), $filters)->with($with)->get();
    }

    public function getPromo(array $filters, array $with = [])
    {
        return filter_query_with_model(PromoCode::query(), $this->getFilterModel($filters), $filters)->with($with)->first();
    }

    public function getPromoById(int $id, array $with = [])
    {
        return PromoCode::with($with)->find($id);
    }

    public function getPromoByIdThrow(int $id, array $with = [])
    {
        $promoCode =  PromoCode::with($with)->find($id);

        if (is_null($promoCode)) {
            throw new NotFoundHttpException("Promo code not found!");

        }

        return $promoCode;
    }

    public function getPromosWithPage(array $filters, array $with = [], $orderBy = [ "field" => "id", "value" => "desc" ])
    {
        $orderBy = get_sort_info($orderBy);
        return filter_query_with_model(PromoCode::query(), $this->getFilterModel($filters), $filters)->orderBy($orderBy["field"], $orderBy["value"])->with($with)->paginate($_GET["limit"] ?? null)->withQueryString();
    }

    public function createPromo(array $data)
    {
        switch ($data["type"]) {
            case EPromoType::SUBSCIPRIPTION->value:
                $object = get_class_map_object($data["object_id"], "plan");
                break;
            case EPromoType::ACADEMY_COURSE->value:

                $object = get_class_map_object($data["object_id"], "course");
                break;
            default:
                throw new AppException("Invalid type!");
        }

        if (is_null($object)) {
            throw new AppException("Invalid object!");
        }

        if (!array_key_exists("code", $data) || is_null($data["code"])) {
            $data["code"] = $this->generateRandomPromocode();
        }

        if ($data["usage_type"] == EPromoUsageType::SINGLE->value) {
            $data["total_quantity"] = 0;
        }

        $data["object_id"] = $object->id;
        $data["object_type"] = get_class($object);
        $data["status"] = EStatus::PENDING;

        return PromoCode::create($data);
    }

    public function createBatchPromo(array $batchData, array $data)
    {
        $totalPromoCount = $batchData["batch_quantity"] ?? 1;
        $batchPromoArray = array();
        $data["context_type"] = $batchData["context_type"];
        $data["created_at"] = date("Y-m-d H:i:s");
        $data["updated_at"] = date("Y-m-d H:i:s");
        $data["status"] = EStatus::SUCCESS;
        for ($i = 0; $i < $totalPromoCount; $i++) {
            $data["code"] = $this->generateRandomPromocode();

            array_push(
                $batchPromoArray,
                $data
            );
        }

        PromoCode::insert($batchPromoArray);
    }

    public function generateRandomPromocode()
    {
        do {
            $code = substr(bin2hex(random_bytes(4)), 0, 8);
            $promo = $this->getPromo([ "code" => $code ]);
        } while (!is_null($promo));

        return strtoupper($code);
    }

    public function updatePromo(PromoCode $promo, array $data)
    {
        if ($promo->usage_type == EPromoUsageType::SINGLE) {
            $data["total_quantity"] = 0;
        }

        return $promo->update($data);
    }

    public function deletePromo(PromoCode $promo)
    {
        return $promo->delete();
    }
}
