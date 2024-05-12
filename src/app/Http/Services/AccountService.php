<?php

namespace App\Http\Services;

use App\Exceptions\AppException;
use App\Models\User\User;
use Exception;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Config;

class AccountService
{
    public function __construct(private UserService $userService, private AssetService $assetService)
    {
    }

    public function getUsersWithPage(array $filter)
    {
        return $this->userService->getUsersWithPage($filter, []);
    }

    public function updateUser(User $user, array $data)
    {
        if (array_key_exists("v3_asset_id", $data)) {
            $assetPath = $this->assetService->getAssetPath($data["v3_asset_id"]);
            if (!is_null($assetPath)) {
                $data["image"] = $assetPath;
            }
        }

        if (array_key_exists("email", $data) && $user->email !== $data["email"]) {
            $checkUser = $this->userService->getUser([ "email" => $data["email"] ]);
            if (!is_null($checkUser)) {
                throw new AppException("Email is already registered!");
            }
        }

        if (array_key_exists("phone", $data) && ($user->phone . "") !== $data["phone"]) {
            $checkUser = $this->userService->getUser([ "phone" => $data["phone"] ]);
            if (!is_null($checkUser)) {
                throw new AppException("Phone number is already registered!");
            }
        }

        return $this->userService->updateUser($user->id, $data);
    }

    public function getUserById(int $id)
    {
        return $this->userService->getUserById($id, []);
    }
}
