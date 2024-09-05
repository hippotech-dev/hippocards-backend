<?php

namespace App\Http\Services;

use App\Enums\EPaymentOrderItemType;
use App\Enums\EPaymentOrderType;
use App\Enums\EStatus;
use App\Exceptions\AppException;
use App\Models\Course\Course;
use App\Models\Course\UserCourse;
use App\Models\Payment\PaymentInvoice;
use App\Models\Payment\PaymentOrder;
use App\Models\Payment\PaymentOrderItem;
use App\Models\User\User;
use App\Models\Utility\PromoCode;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PaymentOrderService
{
    public function __construct(private CourseService $courseService, private PromoService $promoService)
    {
    }

    public function getPaymentOrderFromInvoice(PaymentInvoice $invoice)
    {
        return $invoice->paymentOrder()->first();
    }

    public function getPaymentItemObject($objectId, $objectType)
    {
        switch ($objectType) {
            case EPaymentOrderItemType::ACADEMY_COURSE->value:
                return Course::find($objectId);
            default:
                throw new AppException("Invalid order item!");
        }
    }

    public function getPaymentItemObjectAmount(mixed $object)
    {
        return 100;
        switch (get_class($object)) {
            case Course::class:
                $detail = $object->detail()->first();
                if (is_null($detail)) {
                    throw new AppException("Course is not available for purchase!");
                }
                return $detail->price;
            default:
                throw new AppException("Invalid order item!");
        }
    }

    public function generateOrderItemData(User $user, array &$data)
    {
        $items = $data["items"];
        $generatedItemData = array();
        foreach ($items as $item) {
            $object = $this->getPaymentItemObject($item["object_id"], $item["object_type"]);

            if (is_null($object)) {
                throw new NotFoundHttpException("Object not found!");
            }

            $amount = $this->getPaymentItemObjectAmount($object);
            array_push(
                $generatedItemData,
                [
                    "object_id" => $item["object_id"],
                    "object_type" => get_class($object),
                    "amount" => $amount,
                    "user_id" => $user->id
                ]
            );
        }

        return $generatedItemData;
    }

    public function createOrder(User $user, array $data, PromoCode|null $promo)
    {
        $itemsData = $this->generateOrderItemData($user, $data);
        $totalAmount = array_sum(array_column($itemsData, "amount"));
        $discountAmount = 0;
        if (!is_null($promo)) {
            $discountAmount = $this->promoService->getDiscountPrice($promo, $totalAmount);
        }
        $order = $user->paymentOrders()->create([
            "total_amount" => $totalAmount - $discountAmount,
            "discount_amount" => $discountAmount,
            "total_items" => count($itemsData),
            "type" => EPaymentOrderType::from($data["type"]),
            "status" => EStatus::PENDING,
            "number" => gen_uuid(),
            "v3_promo_code_id" => $promo->id ?? null
        ]);

        $order->items()->createMany($itemsData);

        return $order;
    }

    public function createSuccessfullOrderObjects(PaymentOrder $order)
    {
        $user = $order->user()->first();
        $items = $order->items()->get();

        foreach ($items as $item) {
            $this->createOrderItemObject($user, $item);
        }

        $this->setOrderStatus($order, EStatus::SUCCESS);
    }

    public function createOrderItemObject(User $user, PaymentOrderItem $item)
    {
        switch ($item->object_type) {
            case Course::class:
                $this->courseService->createUserCourse($user, [ "v3_course_id" => $item->object_id ]);
                break;
            default:
                throw new AppException("Invalid order item type!");
        }
    }

    public function setOrderStatus(PaymentOrder $order, EStatus $status)
    {
        $order->status = $status;
        return $order->save();
    }
}
