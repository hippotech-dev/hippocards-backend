<?php

namespace App\Http\Controllers\SSO;

use App\Enums\ECodeChallengeMethod;
use App\Enums\EConfirmationType;
use App\Http\Controllers\Controller;
use App\Http\Services\ConfirmationService;
use App\Http\Services\GoogleService;
use App\Http\Services\SSOService;
use App\Http\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class SSOController extends Controller
{
    public function __construct(private SSOService $service, private ConfirmationService $confirmationService) {}

    /**
     * Authorize SSO request
     *
     * @param  \Illumiante\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkAuthorizeRequest(Request $request)
    {
        $validatedData = Validator::make(
            $request->only(
                "response_type",
                "client_id",
                "redirect_uri",
                "scopes",
                "state",
                "challenge",
                "challenge_method"
            ),
            [
                "response_type" => "required|string|max:24",
                "client_id" => "required|string|max:128",
                "redirect_uri" => "required|string|max:256",
                "scopes" => "required|array",
                "state" => "required|string|max:128",
                "challenge" => "required|string|max:128",
                "challenge_method" => "required|in:" . ECodeChallengeMethod::PLAIN->value . "," . ECodeChallengeMethod::S256->value . "|max:12"
            ]
        )
            ->validate();

        $this->service->checkAuthorizeRequest($validatedData);

        $URL = $this->service->getAuthURL($validatedData);

        return response()->success($URL);
    }

    /**
     * Authorize user
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function authorizeUser(Request $request)
    {
        $validatedData = Validator::make(
            $request->only(
                "oauth",
                "credentials"
            ),
            [
                "oauth" => "required",
                "credentials" => "required",
                "oauth.response_type" => "required|string|max:24",
                "oauth.client_id" => "required|string|max:128",
                "oauth.redirect_uri" => "required|string|max:256",
                "oauth.scopes" => "required|array",
                "oauth.state" => "required|string|max:128",
                "oauth.challenge" => "required|string|max:128",
                "oauth.challenge_method" => [ "required", Rule::in(ECodeChallengeMethod::PLAIN->value, ECodeChallengeMethod::S256->value)],
                "credentials.value" => "required|string|max:64",
                "credentials.password" => "required|string|max:32"
            ]
        )
            ->validate();

        $oauth = $validatedData["oauth"];
        $credentials = $validatedData["credentials"];

        $client = $this->service->getClientByClientId($oauth["client_id"]);

        if (is_null($client)) {
            return response()->notFound();
        }

        $result = $this->service->authorizeUser($client, $credentials, $oauth);

        return response()->success($result);
    }

    /**
     * Get token
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAuthenticationToken(Request $request)
    {
        $validatedData = Validator::make(
            $request->only(
                "client_id",
                "client_secret",
                "code_verifier",
                "code",
            ),
            [
                "client_id" => "required|string|max:128",
                "client_secret" => "required|string|max:128",
                "code_verifier" => "required|string|max:128",
                "code" => "required|string|max:32",
            ]
        )
            ->validate();

        $result = $this->service->getAuthenticationToken($validatedData);

        return response()->success($result);
    }

    /**
     * Verify credential
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyCredential(Request $request)
    {
        $validatedData = Validator::make(
            $request->only(
                "confirmation_id",
                "value",
                "type"
            ),
            [
                "confirmation_id" => "nullable|integer|exists:email_comfirmation,id",
                "value" => "required|string|max:128",
                "type" => [ "required", new Enum(EConfirmationType::class) ],
            ]
        )
            ->validate();

        if ($this->confirmationService->checkConfirmationFrequency($validatedData["value"])) {
            return response()->fail("Please resend after some time!");
        }

        $confirmation = $this->confirmationService->createConfirmation(EConfirmationType::from($validatedData["type"]), $validatedData["value"], $validatedData["confirmation_id"] ?? null);
        return response()->success([
            "confirmation_id" => $confirmation->id,
            "value" => $confirmation->email,
        ]);
    }

    /**
     * Approve comfirmation
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function approveConfirmation(Request $request)
    {
        $validatedData = Validator::make(
            $request->only(
                "confirmation_id",
                "code"
            ),
            [
                "confirmation_id" => "required|integer",
                "code" => "required|string|max:6"
            ]
        )
            ->validate();

        $this->confirmationService->approveConfirmation($validatedData["confirmation_id"], $validatedData["code"]);

        return response()->success();
    }

    /**
     * Registration
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function registerUser(Request $request)
    {
        $validatedData = Validator::make(
            $request->only(
                "confirmation_id",
                "name",
                "password"
            ),
            [
                "confirmation_id" => "required|integer",
                "name" => "required|string|max:128",
                "password" => "required|string|max:32"
            ]
        )
            ->validate();

        $confirmation = $this->confirmationService->checkConfirmationValidity($validatedData["confirmation_id"]);

        if (is_null($confirmation)) {
            return response()->fail("Confirmation is expired!");
        }

        $this->service->registerUser($confirmation, [
            "name" => $validatedData["name"],
            "password" => $validatedData["password"]
        ]);

        return response()->success();
    }

    /**
     * Forgot password
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgotPassword(Request $request)
    {
        $validatedData = Validator::make(
            $request->only(
                "confirmation_id",
                "password"
            ),
            [
                "confirmation_id" => "required|integer",
                "password" => "required|string|max:32"
            ]
        )
            ->validate();

        $confirmation = $this->confirmationService->checkConfirmationValidity($validatedData["confirmation_id"]);

        if (is_null($confirmation)) {
            return response()->fail("Confirmation is expired!");
        }

        $this->service->forgotPassword($confirmation, $validatedData["password"]);

        return response()->success();
    }

    /**
     * Gmail Authentication
     *
     * @param  \App\Http\Services\GoogleService $googleService
     * @return \Illuminate\Http\JsonResponse
     */
    public function authorizeGmail(Request $request, GoogleService $googleService)
    {
        $validatedData = Validator::make(
            $request->only(
                "response_type",
                "client_id",
                "redirect_uri",
                "scopes",
                "state",
                "challenge",
                "challenge_method",
            ),
            [
                "response_type" => "required|string|max:24",
                "client_id" => "required|string|max:128",
                "redirect_uri" => "required|string|max:256",
                "scopes" => "required|array",
                "state" => "required|string|max:128",
                "challenge" => "required|string|max:128",
                "challenge_method" => [ "required", Rule::in(ECodeChallengeMethod::PLAIN->value, ECodeChallengeMethod::S256->value)],
            ]
        )
            ->validate();

        $googleService->setState($validatedData);
        $authURL = $googleService->createAuthUrl();

        return response()->success($authURL);
    }

    /**
     * Gmail Callback
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function callbackGmail(Request $request, GoogleService $googleService)
    {
        $data = json_decode($request->get("state", "[]"), true);
        $validatedData = Validator::make(
            $data,
            [
                "response_type" => "required|string|max:24",
                "client_id" => "required|string|max:128",
                "redirect_uri" => "required|string|max:256",
                "scopes" => "required|array",
                "state" => "required|string|max:128",
                "challenge" => "required|string|max:128",
                "challenge_method" => [ "required", Rule::in(ECodeChallengeMethod::PLAIN->value, ECodeChallengeMethod::S256->value)],
            ]
        )
            ->validate();

        $authorizationCode = $request->get("code", null);
        $authorizationError = $request->get("error", null);

        if (!is_null($authorizationError)) {
            return response()->fail("Google OAuth2 Error!");
        }

        $userData = $googleService->getUserData($authorizationCode);
        $user = $this->service->getGoogleUser($userData);

        $client = $this->service->getClientByClientId($validatedData["client_id"]);

        if (is_null($client)) {
            return response()->notFound();
        }

        $result = $this->service->authorizeUser($client, $user, $validatedData);

        return response()->redirectTo($result["redirect_uri"] . "?" . http_build_query($result));
    }

    /**
     * Facebook Authentication
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function authorizeFacebook(Request $request) {}

    /**
     * Gmail Callback
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function callbackFacebook(Request $request) {}

    /**
     * Check user credential
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkUserCredential(Request $request)
    {
        $validatedData = Validator::make(
            $request->only(
                "value",
                "type",
            ),
            [
                "value" => "required|string",
                "type" => "required|string|in:forgot,register"
            ]
        )
            ->validate();

        $this->service->checkUserValue($validatedData["value"], $validatedData["type"]);

        return response()->success();
    }
}
