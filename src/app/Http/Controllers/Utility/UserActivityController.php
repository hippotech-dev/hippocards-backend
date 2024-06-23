<?php

namespace App\Http\Controllers\Utility;

use App\Enums\EUserActivityAction;
use App\Enums\EUserActivityType;
use App\Http\Controllers\Controller;
use App\Http\Services\UserActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserActivityController extends Controller
{
    public function __construct(private UserActivityService $service)
    {
        $this->middleware("jwt.auth");
    }

    /**
     * Create or update user activity instance
     */
    public function store(Request $request)
    {
        $validatedData = Validator::make(
            $request->only(
                "object_id",
                "type",
                "action",
            ),
            [
                "object_id" => "required|integer",
                "type" => [
                    "required",
                    Rule::in(
                        EUserActivityType::COURSE->value,
                        EUserActivityType::WORD->value,
                    )
                ],
                "action" => [
                    "required",
                    Rule::in(
                        EUserActivityAction::MEMORIZE->value,
                        EUserActivityAction::READ->value,
                        EUserActivityAction::FINISH->value,
                    )
                ],
            ]
        )
            ->validate();


        $requestUser = auth()->user();
        $this->service->createUpdateUserActivity(
            $requestUser,
            EUserActivityType::tryFrom($validatedData["type"]),
            $validatedData["object_id"],
            EUserActivityAction::tryFrom($validatedData["action"]),
        );

        return response()->success();
    }
}
