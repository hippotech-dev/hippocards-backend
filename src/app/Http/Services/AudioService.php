<?php

namespace App\Http\Services;

use App\Enums\EAccessTokenType;
use App\Exceptions\AppException;
use App\Util\AudioConfig;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class AudioService
{
    public function __construct(private AccessTokenService $accessTokenService, private AssetService $assetService)
    {
    }

    public function getAccessToken()
    {
        $accessToken = $this->accessTokenService->getLatestTokenByType(EAccessTokenType::AZURE_COGNITIVE);
        if (is_null($accessToken)) {
            $accessToken = $this->fetchAccessToken();
        }

        if ($this->accessTokenService->isAccessTokenExpired($accessToken)) {
            $accessToken->delete();
            $accessToken = $this->fetchAccessToken();
        }

        return $accessToken;
    }

    private function fetchAccessToken()
    {
        $response = Http::contentType('Content-type: application/x-www-form-urlencoded')
            ->withHeaders([
                'Ocp-Apim-Subscription-Key' => Config::get("credentials.azure.API_KEY")
            ])
            ->post(
                "https://eastus.api.cognitive.microsoft.com/sts/v1.0/issueToken",
                []
            );

        if ($response->failed()) {
            throw new AppException($response->body());
        }

        $body = $response->body();

        return $this->accessTokenService->createToken(
            EAccessTokenType::AZURE_COGNITIVE,
            $body,
            date("Y-m-d H:i:s", strtotime("+9 minutes")),
            []
        );
    }

    public function generateAudio(string $text, AudioConfig $config, string $customFilePath = null)
    {
        $data = "
            <speak version='1.0' xml:lang='" . $config->getLocale() . "'><voice xml:lang='" . $config->getLocale() . "' xml:gender='" . $config->getGender() . "'
                name='" . $config->getNeural() . "'>
                    $text
            </voice></speak>
        ";

        $token = $this->getAccessToken();

        $options = array(
            'http' => array(
                'header' => "Content-type: application/ssml+xml\r\n" .
                "X-Microsoft-OutputFormat: riff-24khz-16bit-mono-pcm\r\n" .
                "Authorization: " . "Bearer " . $token->access_token . "\r\n" .
                "X-Search-AppId: 07D3234E49CE426DAA29772419F436CA\r\n" .
                "X-Search-ClientID: 1ECFAE91408841A480F00935DC390960\r\n" .
                "User-Agent: " . Config::get("credentials.azure.NAME") . "\r\n" .
                "content-length: " . strlen($data) . "\r\n",
                'method' => 'POST',
                'content' => $data,
            ),
        );

        $context = stream_context_create($options);
        $fileContents = file_get_contents(Config::get("credentials.azure.PATH", ""), false, $context);

        return $this->assetService->createAudioAsset($fileContents, $customFilePath);
    }
}
