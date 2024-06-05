<?php

namespace App\Http\Controllers\System\Content\Account;

use App\Http\Controllers\Controller;
use App\Http\Resources\System\Utility\SubscriptionResource;
use App\Http\Services\SubscriptionService;
use App\Models\User\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SubscriptionController extends Controller
{
    public function __construct(private SubscriptionService $service)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(User $user)
    {
        $subscription = $this->service->getUserSubscription($user);

        if (is_null($subscription)) {
            return response()->success();
        }

        return new SubscriptionResource($subscription);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, User $user)
    {
        $validatedData = Validator::make(
            $request->only(
                "subplan_id",
            ),
            [
                "subplan_id" => "required|integer"
            ]
        )
            ->validate();

        $plan = $this->service->getSubscriptionPlanById($validatedData["subplan_id"]);

        if (is_null($plan)) {
            throw new NotFoundHttpException("Subscription plan is not found!");
        }

        $subscription = $this->service->createUpdateUserSubscription($user, $plan);

        return new SubscriptionResource($subscription);
    }
}
