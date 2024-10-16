<?php

namespace App\Http\Services;

use App\Enums\EUserLoginType;
use App\Enums\EUserRole;
use App\Models\User\User;
use App\Models\User\UserSession;
use App\Models\User\UserWebBrowser;

class UserService
{
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
        return filter_query_with_model(User::query(), $this->getFilterModels($filters), $filters)->with($with)->where("is_guest", false)->orderBy("id", "desc")->paginate(page_size());
    }

    public function getUser(array $filters, array $with = [])
    {
        return filter_query_with_model(User::query(), $this->getFilterModels($filters), $filters)->with($with)->first();
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

    public function deleteUser(User $user)
    {
        return $user->delete();
    }

    public function hashPassword(string $password)
    {
        return bcrypt($password);
    }

    public function createUserWebBrowser(User $user, array $data)
    {
        return $user->webBrowsers()->create($data);
    }

    public function createUserSession(User $user, array $data)
    {
        return $user->sessions()->create($data);
    }

    public function getUserWebBrowser(User $user, array $filters)
    {
        return filter_query_with_model($user->webBrowsers(), [ "user_id" => [ "where", "user_id" ], "device_id" => [ "where", "device_id" ] ], $filters)->first();
    }

    public function getUserSession(User $user, array $filters)
    {
        return filter_query_with_model($user->sessions(), [ "user_id" => [ "where", "user_id" ], "access_token" => [ "where", "access_token" ] ], $filters)->first();
    }

    public function deleteUserSession(int $id)
    {
        return UserSession::where("id", $id)->delete();
    }

    public function deleteWebBrowser(int $id)
    {
        return UserWebBrowser::where("id", $id)->delete();
    }

    public function getOrCreateUserBrowser(User $user, array $data)
    {
        $browser = $this->getUserWebBrowser($user, [ "device_id" => $data["device_id"] ]);
        if (is_null($browser)) {
            $browser = $this->createUserWebBrowser($user, $data);
        }

        return $browser;
    }

    public function deleteOldBrowsers(User $user)
    {
        $browsers = $user->webBrowsers()->oldest()->get();

        if (count($browsers) === 1) {
            return;
        }

        $browsers = $browsers->slice(0, count($browsers) - 1);

        foreach ($browsers as $browser) {
            $this->deleteWebBrowser($browser->id);
        }
    }

    public function deleteOldSessions(User $user)
    {
        $sessions = $user->sessions()->oldest()->get();

        if (count($sessions) === 1) {
            return;
        }

        $sessions = $sessions->slice(0, count($sessions) - 1);

        foreach ($sessions as $session) {
            $this->deleteUserSession($session->id);
        }
    }

    public function verifyUserSession(User $user, array $device)
    {

    }

    public function verifyBrowser(User $user, array $device)
    {

    }
}
