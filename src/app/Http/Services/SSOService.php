<?php

namespace App\Http\Services;

use App\Enums\ECodeChallengeMethod;
use App\Enums\EConfirmationType;
use App\Enums\EUserLoginType;
use App\Exceptions\UnauthorizedException;
use App\Models\SSO\OAuthAuthenticationAttempt;
use App\Models\SSO\OAuthClient;
use App\Models\User\User;
use App\Models\Utility\EmailConfirmation;
use Exception;
use Google\Service\Oauth2\Userinfo;
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

    public function authorizeUser(OAuthClient $client, array|User $credentials, array $oauthOptions)
    {
        $oauthOptions["v3_oauth_client_id"] = $client->id;
        if ($credentials instanceof User) {
            $user = $credentials;
        } else {
            $user = $this->checkUserCredentials($credentials["value"], $credentials["password"]);
        }
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

    public function registerUser(EmailConfirmation $confirmation, array $userData)
    {
        $credentials = [];
        switch ($confirmation->type) {
            case EConfirmationType::EMAIL:
                $credentials["email"] = $confirmation->email;
                break;
            case EConfirmationType::PHONE:
                $credentials["phone"] = $confirmation->email;
                break;
        }

        $checkUser = $this->userService->getUserByFilter($credentials);

        if (!is_null($checkUser)) {
            throw new UnauthorizedException("User with such email or phone number is already registered!");
        }

        return $this->userService->createNormalUser(array_merge(
            $credentials,
            $userData
        ));
    }

    public function forgotPassword(EmailConfirmation $confirmation, string $password)
    {
        $credentials = [];
        switch ($confirmation->type) {
            case EConfirmationType::EMAIL:
                $credentials["email"] = $confirmation->email;
                break;
            case EConfirmationType::PHONE:
                $credentials["phone"] = $confirmation->email;
                break;
        }

        $checkUser = $this->userService->getUserByFilter($credentials);

        if (is_null($checkUser)) {
            throw new UnauthorizedException("User with such email or phone number does not exist!");
        }

        return $this->userService->updateUser($checkUser->id, [
            "password" => $password
        ]);
    }

    public function getGoogleUser(Userinfo $googleUserData)
    {
        // +email: "batsoyombo.kh@gmail.com"
        // +familyName: "Khishigbaatar"
        // +gender: null
        // +givenName: "Batsoyombo"
        // +hd: null
        // +id: "113771276259537088557"
        // +link: null
        // +locale: "en"
        // +name: "Batsoyombo Khishigbaatar"
        // +picture: "https://lh3.googleusercontent.com/a/ACg8ocK-5X9xC9CB7fWr4ZcT-sMvzNsH7DOKzSNsd2wLlWm8=s96-c"
        // +verifiedEmail: true
        $userData = [
            "login_type" => EUserLoginType::LOGIN_GMAIL,
            "name" => $googleUserData->getFamilyName() . " " . $googleUserData->getGivenName(),
            "email" => $googleUserData->getVerifiedEmail(),
            "fid" => $googleUserData->getId(),
            "image" => $googleUserData->getPicture()
        ];
        $checkUser = $this->userService->getUserByFilter([
            "email" => $userData["email"]
        ]);

        if (is_null($checkUser)) {
            $checkUser = $this->userService->createNormalUser($userData);
        }

        return $checkUser;
    }
}
