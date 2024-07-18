<?php

namespace App\Http\Controllers\Web\Academy;

use App\Http\Controllers\Controller;
use App\Http\Services\AccountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function __construct(private AccountService $service)
    {
        $this->middleware("jwt.auth", [
            "except" => [
                "index",
                "show",
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $validatedData = Validator::make(
            $request->only(
                "name",
                "sex",
                "password",
                "birth_year",
                "v3_asset_id",
                "phone_verification_id",
                "email_verification_id"
            ),
            [
                "name" => "sometimes|string|max:128",
                "sex" => "sometimes|integer",
                "password",
                "birth_year" => "sometimes|date",
                "v3_asset_id" => "sometimes|exists:v3_assets,id",
                "phone_verification_id" => "sometimes|integer",
                "email_verification_id" => "sometimes|integer",
            ]
        )
            ->validate();

        $requestUser = auth()->user();

        $this->service->updateUser($requestUser, $validatedData);

        return response()->success();
    }
}
