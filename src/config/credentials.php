<?php

return [
    "qpay" => [
        "USERNAME" => env("QPAY_USERNAME", ""),
        "PASSWORD" => env("QPAY_PASSWORD", ""),
        "INVOICE_CODE" => env("QPAY_INVOICE_CODE", "")
    ],
    "facebook" => [
        "APP_ID" => env("FACEBOOK_APP_ID", ""),
        "APP_SECRET" => env("FACEBOOK_APP_SECRET", ""),
        "DEFAULT_GRAPH_VERSION" => env("FACEBOOK_GRAPH_VERSION", ""),
    ],
    "google" => [
        "CLIENT_ID" => env("GOOGLE_CLIENT_ID", ""),
        "CLIENT_SECRET" => env("GOOGLE_CLIENT_SECRET", ""),
    ],
    "hippo" => [
        "CLIENT_ID" =>  env("HIPPO_CLIENT_ID", ""),
        "CLIENT_SECRET" =>  env("HIPPO_CLIENT_SECRET", ""),
        "VERIFIER" => env("HIPPO_VERIFIER", "")
    ],
    'vdoCipher' => [
        "API_KEY" => env('VDO_CIPHER_API_KEY', "")
    ],
    'azure' => [
        'API_KEY' => "4ed45cda4d8c4bb28d094a3e2921663a",
        "PATH" => "https://eastus.tts.speech.microsoft.com/cognitiveservices/v1",
        "NAME" => "TTSPHP"
    ]
];
