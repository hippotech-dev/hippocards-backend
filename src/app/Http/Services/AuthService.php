<?php

namespace App\Http\Services;

use App\Exceptions\AppException;
use App\Models\User\User;
use App\Models\User\UserSession;
use App\Models\User\UserWebBrowser;
use Exception;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Config;

class AuthService
{
    public function __construct(private SSOService $ssoService)
    {
    }

    public function getTokenFromSSO(string $code)
    {
        return $this->ssoService->getAuthenticationToken([
            "client_id" => Config::get("credentials.hippo.CLIENT_ID"),
            "client_secret" => Config::get("credentials.hippo.CLIENT_SECRET"),
            "code_verifier" => Config::get("credentials.hippo.VERIFIER"),
            "code" => $code,
        ]);
    }

    public function getSSOUrl(string $redirectURI)
    {
        return $this->ssoService->getAuthURL([
            "client_id" => Config::get("credentials.hippo.CLIENT_ID"),
            "challenge" => Config::get("credentials.hippo.VERIFIER"),
            "challenge_method" => "PLAIN",
            "response_type" => "code",
            "redirect_uri" => $redirectURI,
            "scopes" => "openid",
            "state" => "openid"
        ]);
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
}
