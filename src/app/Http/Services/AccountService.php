<?php

namespace App\Http\Services;

use App\Enums\EUserPreferenceType;
use App\Exceptions\AppException;
use App\Models\User\User;
use App\Models\User\UserPreference;
use Exception;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Config;

class AccountService
{
    public function __construct(private UserService $userService, private AssetService $assetService, private ConfirmationService $confirmationService)
    {
    }

    public function getUsersWithPage(array $filter)
    {
        return $this->userService->getUsersWithPage($filter, []);
    }

    public function getUserById(int $id)
    {
        return $this->userService->getUserById($id, []);
    }

    public function deleteUser(User $user)
    {
        return $this->userService->deleteUser($user);
    }

    public function updateUser(User $user, array $data)
    {
        if (array_key_exists("v3_asset_id", $data)) {
            $assetPath = $this->assetService->getAssetPath($data["v3_asset_id"]);
            if (!is_null($assetPath)) {
                $data["image"] = $assetPath;
                unset($data["v3_asset_id"]);
            }
        }

        if (array_key_exists("phone_confirmation_id", $data)) {
            $confirmation = $this->confirmationService->checkConfirmationValidity($data["phone_confirmation_id"]);
            if (!is_null($confirmation)) {
                $data["phone"] = $confirmation->email;
            }

            unset($data["phone_confirmation_id"]);
        }

        if (array_key_exists("email_confirmation_id", $data)) {
            $confirmation = $this->confirmationService->checkConfirmationValidity($data["email_confirmation_id"]);
            if (!is_null($confirmation)) {
                $data["email"] = $confirmation->email;
            }

            unset($data["email_confirmation_id"]);
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

        if (array_key_exists("password", $data)) {
            $data["password"] = bcrypt($data["password"]);
        }

        return $this->userService->updateUser($user->id, $data);
    }

    public function setDefaultPasswordForUser(User $user)
    {
        return $this->userService->updateUser(
            $user->id,
            [
                "password" => "12345678"
            ]
        );
    }

    public function changeUserPassword(User $user, string $currentPassword, string $newPassword)
    {
        if (!auth()->attempt([
            "id" => $user->id,
            "password" => $currentPassword
        ])) {
            throw new AppException("Invalid password!");
        }

        return $this->updateUser($user, [ "password" => $newPassword ]);
    }

    public function createUpdateUserPreference(User $user, EUserPreferenceType|null $type, mixed $value)
    {
        if (is_null($type)) {
            return null;
        }

        return $user->preferences()->updateOrCreate(
            [
                "type" => $type,
            ],
            [

                "value" => $value
            ]
        );
    }

    public function getUserPreferences(User $user)
    {
        return $user->preferences()->get();
    }

    public function createUpdateUserPreferences(User $user, array $preferences)
    {
        foreach ($preferences as $preference) {
            $this->createUpdateUserPreference($user, EUserPreferenceType::tryFrom($preference["type"]), $preference["value"]);
        }
    }
}
