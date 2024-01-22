<?php

namespace App\Http\Services;

use App\Enums\ECodeChallengeMethod;
use App\Enums\EUserLoginType;
use App\Exceptions\UnauthorizedException;
use App\Models\SSO\OAuthAuthenticationAttempt;
use App\Models\SSO\OAuthClient;
use App\Models\User\User;
use Exception;
use Illuminate\Support\Facades\Config;
use Tymon\JWTAuth\Facades\JWTAuth;

class SSOService
{
    public function __construct(private UserService $userService) {}

    public const SSO_SCOPES = [
        "openid",
        "email",
        "phone",
    ];

    public function getClients()
    {
        return OAuthClient::all();
    }

    public function getClientByClientId(string $id)
    {
        return OAuthClient::where("client_id", $id)->first();
    }

    public function createClient(array $validatedData)
    {
        $validatedData["client_id"] = bin2hex(random_bytes(16));
        $validatedData["client_secret"] = bin2hex(random_bytes(32));
        return OAuthClient::create($validatedData);
    }

    public function getAuthenticationAttemptByCode(string $code)
    {
        return OAuthAuthenticationAttempt::where("code", $code)->first();
    }

    public function createAuthenticationAttempt(User $user, array $validatedData)
    {
        $validatedData["code"] = bin2hex(random_bytes(16));
        return $user->authenticationAttempts()->create($validatedData);
    }

    public function checkAuthorizeRequest(
        OAuthClient $client,
        array $options,
    ) {
        try {
            if (!$client->is_active) {
                return false;
            }
            if ($options["response_type"] !== "code") {
                return false;
            }
            return true;
        } catch (Exception $err) {
            return false;
        }
    }

    public function checkUserCredentials(string $value, string $password)
    {
        $credentials = [];
        check_email($value)
            && $credentials["email"] = $value;

        !check_email($value)
            && $credentials["phone"] = $value;

        $user = $this->userService->getUserByFilter($credentials);
        $credentials["password"] = $password;

        if (is_null($user)) {
            throw new UnauthorizedException("Email or phone number does not exists!");
        }

        if ($user->login_type !== EUserLoginType::LOGIN_NORMAL) {
            throw new UnauthorizedException("This account is signed up using Gmail!");
        }

        if (!auth()->validate($credentials)) {
            throw new UnauthorizedException("Incorrect password!");
        }

        return $user;
    }

    public function validateCodeChallange(OAuthAuthenticationAttempt $attempt, string $codeVerifier)
    {
        switch ($attempt->challenge_method) {
            case ECodeChallengeMethod::PLAIN:
                return $attempt->challenge === $codeVerifier;
            case ECodeChallengeMethod::S256:
                return $attempt->challenge === hash("sha256", $codeVerifier);
        }

        return false;
    }

    public function authorize(OAuthClient $client, array $credentials, array $oauthOptions)
    {
        $oauthOptions["v3_oauth_client_id"] = $client->id;
        $user = $this->checkUserCredentials($credentials["value"], $credentials["password"]);
        $attempt = $this->createAuthenticationAttempt($user, $oauthOptions);
        return [
            "code" => $attempt->code,
            "redirect_uri" => $attempt->redirect_uri,
            "state" => $attempt->state
        ];
    }

    public function getAuthenticationToken(OAuthAuthenticationAttempt $attempt)
    {
        $user = $attempt->user()->first();
        $accessToken = auth()->login($user);
        return [
            "access_token" => $accessToken,
            "expires_in" => Config::get("jwt.ttl", 3600),
            "openid" => $user->id
        ];
    }
}
