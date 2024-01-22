<?php

namespace App\Http\Services;

use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Config;

class MessageService
{
    public function sendMessage(string $phone, string $message)
    {
        try {
            fetch_url(
                "https://api.messagepro.mn/send",
                "GET",
                [
                    "headers" => [
                        "x-api-key" => Config::get("constants.OTP_API_KEY")
                    ],
                    "query" => [
                        "from" => "72770000",
                        "to" => $phone,
                        "text" => $message
                    ]
                ]
            );
        } catch (ClientException $err) {
            return null;
        }
    }
}
