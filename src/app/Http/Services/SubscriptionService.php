<?php

namespace App\Http\Services;

use App\Models\Subscription\SubPlan;
use App\Models\Subscription\SubUser;
use App\Models\User\User;

class SubscriptionService
{
    public function getUserSubscription(User $user, array $with = [])
    {
        return $user->subscription()->with($with)->first();
    }

    public function createUpdateUserSubscription(User $user, SubPlan $plan)
    {
        $subscription = $this->getUserSubscription($user);
        $currentDate = date("Y-m-d");

        $previousSubscriptionDate = $subscription->active_until ?? $currentDate;

        $baseDate = $previousSubscriptionDate < $currentDate ? $currentDate : $previousSubscriptionDate;

        $subscriptionDate = date("Y-m-d", strtotime($baseDate . " + " . $plan->plan_dur_day . "days"));

        $data = [
            "plan_id" => $plan->id,
            "is_paid" => false,
            "note" => "system",
            "active_until" => $subscriptionDate
        ];

        if (is_null($subscription)) {
            $subscription = $this->createSubscription($user, $data);
        } else {
            $this->updateSubscription($subscription, $data);
        }

        return $subscription;
    }

    public function getSubscriptionPlans(array $filters = [])
    {
        $filterModel = [
            "is_subscription" => [ "where", "is_subscription" ],
            "is_paid" => [ "where", "is_paid" ],
        ];

        return filter_query_with_model(SubPlan::query(), $filterModel, $filters)->get();
    }

    public function getSubscriptionPlanById(int $id)
    {
        return SubPlan::find($id);
    }

    public function createSubscription(User $user, array $data)
    {
        return $user->subscription()->create($data);
    }

    public function updateSubscription(SubUser $subscription, array $data)
    {
        return $subscription->update($data);
    }
}
