<?php

namespace App\Http\Services;

use Exception;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class VDOCipherService
{
    public function importByUrl(string $url)
    {
        try {
            $response = fetch_url(
                "https://dev.vdocipher.com/api/videos/importUrl",
                "PUT",
                [
                    "headers" => [
                        "Authorization" => "Apisecret " . Config::get("credentials.vdoCipher.API_KEY", "")
                    ],
                    "form_params" => [
                        "url" => $url
                    ]
                ]
            );
            Log::channel("custom")->info("VDO Success: " . $url, [
                "data" => $response,
                "url" => $url,
            ]);
            return $response;
        } catch (Exception $err) {
            Log::channel("custom")->error("VDOException: " . $err->getMessage());
        }
    }

    public function getVideoOTP(string $videoId)
    {
        try {
            $response = fetch_url(
                "https://dev.vdocipher.com/api/videos/$videoId/otp",
                "POST",
                [
                    "headers" => [
                        "Authorization" => "Apisecret " . Config::get("credentials.vdoCipher.API_KEY", "")
                    ],
                    RequestOptions::JSON => [
                        "ttl" => 3600
                    ]
                ]
            );
            Log::channel("custom")->info("VDO Success: " . $videoId, [
                "data" => $response,
                "videoId" => $videoId,
            ]);
            return $response;
        } catch (Exception $err) {
            Log::channel("custom")->error("VDOException: " . $err->getMessage());
            return null;
        }
    }
}
