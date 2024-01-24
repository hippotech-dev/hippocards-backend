<?php

namespace App\Http\Services;

use App\Exceptions\AppException;
use Google\Service\Oauth2;
use Google_Client;
use Illuminate\Support\Facades\Config;

class GoogleService
{
    private Google_Client $client;

    private string $authUrl;

    private string $redirectUri = 'http://localhost:8000/sso/social/google/callback';

    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setAuthConfig("client_secret.json");
        $this->client->setScopes([ Oauth2::USERINFO_EMAIL, Oauth2::USERINFO_PROFILE ]);
        $this->client->setRedirectUri($this->redirectUri);
        $this->client->setAccessType('offline');
        $this->client->setPrompt('consent');
        $this->client->setIncludeGrantedScopes(true);
        $this->createAuthUrl();
    }

    public function createAuthUrl()
    {
        $this->authUrl = $this->client->createAuthUrl();
    }

    public function setState(array $params)
    {
        $this->client->setState(json_encode($params));
        $this->createAuthUrl();
    }

    public function getAuthUrl()
    {
        return $this->authUrl;
    }

    public function verifyIdToken(string $token)
    {
        $payload = $this->client->verifyIdToken($token);

        if (!$payload) {
            return null;
        }

        return $payload;
    }

    public function getUserData(string $authorizationCode)
    {
        $tokens = $this->client->fetchAccessTokenWithAuthCode($authorizationCode);

        if (array_key_exists("error", $tokens)) {
            throw new AppException("Invalid google access token!");
        }

        $this->client->setAccessToken($tokens["access_token"]);

        $oauth = new Oauth2($this->client);

        return $oauth->userinfo->get();
    }
}
