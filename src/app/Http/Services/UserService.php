<?php

namespace App\Http\Services;

use App\Enums\EUserLoginType;
use App\Enums\EUserRole;
use App\Models\User\User;

class UserService
{
    public function getUserById(int $id, array $with = [])
    {
        return User::with($with)->find($id);
    }

    public function getUsers(array $filters, array $with = [])
    {

        return filter_query_with_model(User::query(), $this->getFilterModels($filters), $filters)->with($with)->where("is_guest", false)->get();
    }

    public function getUsersWithPage(array $filters, array $with = [])
    {
        return filter_query_with_model(User::query(), $this->getFilterModels($filters), $filters)->with($with)->where("is_guest", false)->orderBy("id", "desc")->simplePaginate(page_size());
    }

    public function getUser(array $filters, array $with = [])
    {
        return filter_query_with_model(User::query(), $this->getFilterModels($filters), $filters)->with($with)->where("is_guest", false)->first();
    }

    public function createNormalUser(array $userData)
    {
        $userData["role_id"] = EUserRole::USER;
        $userData["new_role"] = EUserRole::USER;
        $userData["password"] = $userData["password"] ? $this->hashPassword($userData["password"]) : "none";
        $userData["login_type"] = $userData["login_type"] ?? EUserLoginType::LOGIN_NORMAL;
        $userData["area_code"] = "976";
        $userData["image"] = $userData["image"] ?? "";
        $userData["phone"] = $userData["phone"] ?? 0;
        $userData["fid"] = $userData["fid"] ?? "";
        $userData["ftoken"] = "";
        $userData["logged_in"] = false;
        $userData["model"] = "";
        $userData["code_push"] = 0;
        return User::create($userData);
    }

    public function updateUser(int $id, array $userData)
    {
        if (array_key_exists("password", $userData)) {
            $userData["password"] = $this->hashPassword($userData["password"]);
        }
        return User::where("id", $id)->update($userData);
    }

    public function hashPassword(string $password)
    {
        return bcrypt($password);
    }

    protected function getFilterModels(array $filters)
    {
        return [
            "email" => [ "where", "email" ],
            "phone" => [ "where", "phone" ],
            "filter" => [
                [ "where" ],
                [
                    [
                        "name" => null,
                        "value" => function ($query) use ($filters) {
                            return $query
                                ->whereLike("name", $filters["filter"])
                                ->orWhereLike("email", $filters["filter"])
                                ->orWhereLike("phone", $filters["filter"]);
                        }
                    ]
                ]
            ]
        ];
    }
}
