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
    ]
];
