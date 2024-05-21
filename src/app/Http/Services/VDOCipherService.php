<?php

namespace App\Http\Services;

use Exception;
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
}
