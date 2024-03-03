<?php

namespace App\Http\Services;

use App\Enums\EAccessTokenType;
use App\Models\Utility\AccessToken;

class AccessTokenService
{
    public function getLatestTokenByType(EAccessTokenType $type)
    {
        return AccessToken::where("type", $type)->orderBy("access_expire")->first();
    }

    public function isAccessTokenExpired(AccessToken $accessToken)
    {
        return $accessToken->access_expire < date("Y-m-d H:i:s");
    }

    public function isRefreshTokenExpired(AccessToken $accessToken)
    {
        return is_null($accessToken->refresh_token) || is_null($accessToken->refresh_expire) || $accessToken->refresh_expire < date("Y-m-d H:i:s");
    }

    public function createToken(
        $type,
        $accessToken,
        $accessExpire,
        $additional = []
    ) {
        return AccessToken::create(array_merge(
            [
                "type" => $type,
                "access_token" => $accessToken,
                "access_expire" => $accessExpire
            ],
            $additional
        ));
    }
}
