<?php

namespace App\Http\Controllers\Mobile\Hippocards;

use App\Http\Controllers\Controller;
use App\Http\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(private UserService $service)
    {
        $this->middleware("jwt.auth");
    }

    /**
     * Delete user data
     */
    public function deleteUserData()
    {
        $requestUser = auth()->user();

        return response()->success();
    }
}
