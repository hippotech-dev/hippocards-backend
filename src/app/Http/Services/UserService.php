<?php

namespace App\Http\Services;

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
}
