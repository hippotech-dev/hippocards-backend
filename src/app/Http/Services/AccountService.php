<?php

namespace App\Http\Services;

use App\Exceptions\AppException;
use Exception;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Config;

class AccountService
{
    public function __construct(private UserService $userService)
    {
    }

    public function getUsersWithPage(array $filter)
    {
        return $this->userService->getUsersWithPage($filter);
    }
}
