<?php

namespace App\Http\Controllers\SSO;

use App\Enums\ECodeChallengeMethod;
use App\Http\Controllers\Controller;
use App\Http\Services\SSOService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SSOController extends Controller
{
    public function __construct(private SSOService $service) {}

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
                "code_challange",
                "code_challange_method"
            ),
            [
                "response_type" => "required|string|max:24",
                "client_id" => "required|string|max:128",
                "redirect_uri" => "required|string|max:256",
                "scopes" => "required|array",
                "state" => "required|string|max:128",
                "code_challange" => "required|string|max:128",
                "code_challange_method" => "required|in:" . ECodeChallengeMethod::PLAIN->value . "," . ECodeChallengeMethod::S256->value . "|max:12"
            ]
        )
            ->validate();

        $client = $this->service->getClientByClientId($validatedData["clinet_id"]);

        if (is_null($client)) {
            return response()->notFound();
        }

        $check = $this->service->checkAuthorizeRequest(
            $client,
            [
                "scopes" => $validatedData["scopes"],
                "state" => $validatedData["state"],
                "redirect_uri" => $validatedData["redirect_uri"],
                "response_type" => $validatedData["response_type"],
            ],
        );

        if (!$check) {
            return response()->fail("Authorization request failed!");
        }

        return response()->success();
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
                "oauth.challenge_method" => "required|in:" . ECodeChallengeMethod::PLAIN->value . "," . ECodeChallengeMethod::S256->value . "|max:12",
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

        $result = $this->service->authorize($client, $credentials, $oauth);

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

        $client = $this->service->getClientByClientId($validatedData["client_id"]);
        $attemt = $this->service->getAuthenticationAttemptByCode($validatedData["code"]);

        if (is_null($client) || $client->client_secret !== $validatedData["client_secret"]) {
            return response()->notFound();
        }

        if (is_null($attemt) || $attemt->code !== $validatedData["code"] || $attemt->v3_oauth_client_id !== $client->id) {
            return response()->notFound();
        }

        $result = $this->service->getAuthenticationToken($attemt);

        return response()->success($result);
    }
}
