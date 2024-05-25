<?php

namespace App\Http\Services;

use App\Enums\ECodeChallengeMethod;
use App\Enums\EConfirmationType;
use App\Enums\EUserLoginType;
use App\Exceptions\AppException;
use App\Exceptions\UnauthorizedException;
use App\Models\SSO\OAuthAuthenticationAttempt;
use App\Models\SSO\OAuthClient;
use App\Models\User\User;
use App\Models\Utility\EmailConfirmation;
use Exception;
use Google\Service\Oauth2\Userinfo;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class SSOService
{
    public function __construct(private UserService $userService)
    {
    }

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

    public function deleteAuthenticationAttempt(int $id)
    {
        return OAuthAuthenticationAttempt::where("id", $id)->delete();
    }

    public function checkClientData(
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

    public function checkAuthorizeRequest($validatedData)
    {
        $client = $this->getClientByClientId($validatedData["client_id"]);

        if (is_null($client)) {
            throw new AppException("Invalid client!");
        }

        $check = $this->checkClientData(
            $client,
            [
                "scopes" => $validatedData["scopes"],
                "state" => $validatedData["state"],
                "redirect_uri" => $validatedData["redirect_uri"],
                "response_type" => $validatedData["response_type"],
            ],
        );

        if (!$check) {
            throw new UnauthorizedException("Authorization request failed!");
        }
    }

    public function getAuthURL($validatedData)
    {
        return Config::get("constants.SSO_ENDPOINT", "") . "/login?" . http_build_query($validatedData);
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
            $user = $this->checkUserValue($credentials["value"], "login", $credentials["password"]);
        }
        $attempt = $this->createAuthenticationAttempt($user, $oauthOptions);
        return [
            "code" => $attempt->code,
            "redirect_uri" =>
                $attempt->redirect_uri
                . ($attempt->redirect_uri[strlen($attempt->redirect_uri) - 1] === "&" ? "" : "?")
                . http_build_query([ "code" => $attempt->code, "state", $attempt->state ]),
            "state" => $attempt->state
        ];
    }

    public function getToken(OAuthAuthenticationAttempt $attempt)
    {
        $user = $attempt->user()->first();
        $accessToken = auth()->login($user);
        return [
            "access_token" => $accessToken,
            "expires_in" => Config::get("jwt.ttl", 3600),
            "openid" => $user->id
        ];
    }

    public function getAuthenticationToken(array $validatedData)
    {
        $client = $this->getClientByClientId($validatedData["client_id"]);
        $attemt = $this->getAuthenticationAttemptByCode($validatedData["code"]);

        if (is_null($client) || $client->client_secret !== $validatedData["client_secret"]) {
            throw new AppException("Invalid request!");
        }

        if (is_null($attemt) || $attemt->code !== $validatedData["code"] || $attemt->v3_oauth_client_id !== $client->id) {
            throw new AppException("Invalid request!");
        }

        return DB::transaction(function () use ($attemt) {
            $this->deleteAuthenticationAttempt($attemt->id);
            return $this->getToken($attemt);
        });
    }

    public function registerUser(EmailConfirmation $confirmation, array $userData)
    {
        $this->checkUserValue($confirmation->email, "register");

        return $this->userService->createNormalUser(array_merge($userData, $this->getUserCredentialFromValue($confirmation->email)));
    }

    public function forgotPassword(EmailConfirmation $confirmation, string $password)
    {
        $checkUser = $this->checkUserValue($confirmation->email, "forgot");

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
            "email" => $googleUserData->getEmail(),
            "fid" => $googleUserData->getId(),
            "image" => $googleUserData->getPicture()
        ];

        $checkUser = $this->userService->getUser([
            "email" => $userData["email"]
        ]);

        if (is_null($checkUser)) {
            $checkUser = $this->userService->createNormalUser($userData);
        }

        return $checkUser;
    }

    public function checkUserValue(string $value, string $type, string $password = null)
    {
        $credentials = $this->getUserCredentialFromValue($value);

        $user =  $this->userService->getUser($credentials);

        !is_null($password) && $credentials["password"] = $password;

        switch ($type) {
            case "register":
                if (!is_null($user)) {
                    throw new UnauthorizedException("Энэхүү имэйл эсвэл утас аль хэдийн бүртгэл үүсгэсэн байна!");
                }
                break;
            case "forgot":
                if (is_null($user)) {
                    throw new UnauthorizedException("Энэхүү имэйл эсвэл утас бүртгэл үүсгээгүй байна!");
                }
                break;
            case "login":
                if (is_null($user)) {
                    throw new UnauthorizedException("Энэхүү имэйл эсвэл утас бүртгэл үүсгээгүй байна!");
                }

                // if (array_key_exists("email", $credentials) && $user->login_type !== EUserLoginType::LOGIN_NORMAL) {
                //     throw new UnauthorizedException("This account is signed up using Gmail!");
                // }

                if (!auth()->validate($credentials)) {
                    throw new UnauthorizedException("Нууц үг буруу байна!");
                }
                break;
        }

        return $user;
    }

    public function getUserCredentialFromValue(string $value)
    {
        $credentials = array();
        check_email($value)
            && $credentials["email"] = $value;

        !check_email($value)
            && $credentials["phone"] = $value;

        return $credentials;
    }
}
