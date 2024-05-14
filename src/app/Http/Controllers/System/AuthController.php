<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Http\Resources\System\Auth\UserResource;
use App\Http\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct(private AuthService $service)
    {
    }

    /**
     * Get academy identity
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSSOToken(Request $request)
    {
        $validatedData = Validator::make(
            $request->only(
                "code",
            ),
            [
                "code" => "required|string|max:256"
            ]
        )
            ->validate();

        $token = $this->service->getTokenFromSSO($validatedData["code"]);

        return response()->success($token);
    }

    /**
     * Get academy identity
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getContentIdentity()
    {
        $requestUser = auth()->user();
        return response()->success([
            "user" => new UserResource($requestUser)
        ]);
    }

    /**
     * Get SSO url
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSSOUrl(Request $request)
    {
        $redirectUri = $request->get("redirect_uri", "");
        $URL = $this->service->getSSOUrl($redirectUri);
        return response()->success($URL);
    }
}
