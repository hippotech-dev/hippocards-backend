<?php

namespace App\Http\Controllers\Web\Academy;

use App\Http\Controllers\Controller;
use App\Http\Resources\System\Auth\UserResource;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Get web academy identity
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAcademyIdentity()
    {
        $requestUser = auth()->user();
        return response()->success([
            "user" => new UserResource($requestUser)
        ]);
    }
}
