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

class PaymentOrderService
{
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

    public function createOrder(User $user, array $data)
    {
        $itemsData = $this->generateOrderItemData($user, $data);
        $totalAmount = array_sum(array_column($itemsData, "amount"));
        $order = $user->paymentOrders()->create([
            "total_amount" => $totalAmount,
            "total_items" => count($itemsData),
            "type" => EPaymentOrderType::from($data["type"]),
            "status" => EStatus::PENDING,
            "number" => gen_uuid()
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
                return UserCourse::create([
                    "start" => date("Y-m-d 00:00:00"),
                    "end" => date("Y-m-d 00:00:00", strtotime("+1 month")),
                    "user_id" => $user->id,
                    "v3_course_id" => $item->object_id
                ]);
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
