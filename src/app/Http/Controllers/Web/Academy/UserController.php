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
    public function store(Request $request)
    {
        $validatedData = Validator::make(
            $request->only(
                "name",
                "email",
                "phone",
                "sex",
                "birth_year",
                "v3_asset_id"
            ),
            [
                "name" => "sometimes|string|max:128",
                "email" => "sometimes|string|max:128",
                "phone" => "sometimes|string|max:32",
                "sex" => "sometimes|integer",
                "birth_year" => "sometimes|date",
                "v3_asset_id" => "sometimes|exists:v3_assets,id"
            ]
        )
            ->validate();

        $requestUser = auth()->user();

        $this->service->updateUser($requestUser, $validatedData);

        return response()->success();
    }
}
