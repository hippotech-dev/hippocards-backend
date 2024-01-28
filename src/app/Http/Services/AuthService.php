<?php

namespace App\Http\Services;

use App\Exceptions\AppException;
use Exception;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Config;

class AuthService
{
    public function __construct(private SSOService $ssoService) {}

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
            "scopes" => [ "openid" ],
            "state" => "openid"
        ]);
    }
}
