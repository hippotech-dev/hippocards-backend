<?php

namespace App\Http\Services;

use App\Enums\EUserLoginType;
use App\Enums\EUserRole;
use App\Models\User\User;

class UserService
{
    public function getUserByFilter($filters)
    {
        $filterModel = [
            "email" => [ "where", "email" ],
            "phone" => [ "where", "phone" ],
        ];

        return filter_query_with_model(User::query(), $filterModel, $filters)->first();
    }

    public function createNormalUser($userData)
    {
        $userData["role_id"] = EUserRole::USER;
        $userData["new_role"] = EUserRole::USER;
        $userData["password"] = bcrypt($userData["password"]);
        $userData["login_type"] = EUserLoginType::LOGIN_NORMAL;
        $userData["area_code"] = "976";
        $userData["image"] = "";
        $userData["phone"] = 0;
        $userData["fid"] = "";
        $userData["ftoken"] = "";
        $userData["logged_in"] = false;
        $userData["model"] = "";
        $userData["code_push"] = 0;
        return User::create($userData);
    }
}
