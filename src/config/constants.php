<?php

use App\Models\AppConnection;
use App\Utils\Constant;

return [

    "SSO_ENDPOINT" => env("SSO_ENDPOINT", "https://auth.hippo.cards"),

    "CATEGORY_COLORS" => [
        "#AFD224",
        "#FFE172",
        "#3CB7DD",
        "#C6E256",
        "#FFD743",
        "#6DC9E5",
        "#D4E980",
        "#FFCA04",
        "#92D6EB",
    ],

    "CDN_URL" => env("CDN_URL", ""),
    "CLOUDFRONT_URL" => env("CLOUDFRONT_URL", ""),
    "MAIN_URL" => env("MAIN_APP_URL", ""),
    "CONTENT_URL" => env("CONTENT_APP_URL", ""),
    "CLASS_URL" => env("CLASS_URL", ""),
    "OTP_API_KEY" => env("OTP_API_KEY", ""),

    "QUIZ_SEASON" => env("QUIZ_SEASON", 1),

    "USER_STATS" => [
        [
            "title" => "Үг цээжилж буй дундаж хугацаа",
            "image" => env('APP_URL') . "/upload/profileIcon/time.webp",
            "type" => 0
        ],
        [
            "title" => "Нийт цээжилсэн үгс",
            "image" => env('APP_URL') . "/upload/profileIcon/brain.webp",
            "type" => 1
        ],
        [
            "title" => "Тасралтгүй цээжилсэн өдөр",
            "image" => env('APP_URL') . "/upload/profileIcon/calendar.webp",
            "type" => 2
        ],
        [
            "title" => "Өдөрт дунджаар цээжилж буй үгс",
            "image" => env('APP_URL') . "/upload/profileIcon/star.webp",
            "type" => 3
        ],
        [
            "title" => "Нийт уншсан бичвэрүүд",
            "image" => env('APP_URL') . "/upload/package_icons/80185ea21d76e52e609a9a07f8a37337.webp",
            "type" => 4
        ],
        [
            "title" => "Нийт хадгалсан үгс",
            "image" => env('APP_URL') . "/upload/package_icons/79d55969c5645c58e36d72327dc44c68.webp",
            "type" => 5
        ]
    ],

    "OBJECTIVE" => [
        [
            "id" => 0,
            "name" => "Travelling",
            "description" => "Book tickets, order food in cafes",
            "image" => env('APP_URL') . "/upload/profileIcon/star.webp",
        ],
        [
            "id" => 1,
            "name" => "Culture",
            "description" => "Book tickets, order food in cafes",
            "image" => env('APP_URL') . "/upload/profileIcon/star.webp",
        ],
        [
            "id" => 2,
            "name" => "Career",
            "description" => "Book tickets, order food in cafes",
            "image" => env('APP_URL') . "/upload/profileIcon/star.webp",
        ],
        [
            "id" => 3,
            "name" => "Self Development",
            "description" => "Book tickets, order food in cafes",
            "image" => env('APP_URL') . "/upload/profileIcon/star.webp",
        ],
        [
            "id" => 4,
            "name" => "Career",
            "description" => "Book tickets, order food in cafes",
            "image" => env('APP_URL') . "/upload/profileIcon/star.webp",
        ],
        [
            "id" => 5,
            "name" => "Self Development",
            "description" => "Book tickets, order food in cafes",
            "image" => env('APP_URL') . "/upload/profileIcon/star.webp",
        ],
    ],

    "PAYMENT_QPAY_BANKS" => [
        [
            "name" => "Khan bank",
            "active" => true
        ],
        [
            "name" => "State bank",
            "active" => true
        ],
        [
            "name" => "Xac bank",
            "active" => true
        ],
        [
            "name" => "qPay wallet",
            "active" => true
        ],
        [
            "name" => "M bank",
            "active" => true
        ],
        [
            "name" => "Bogd bank",
            "active" => true
        ],
        [
            "name" => "Capitron bank",
            "active" => true
        ],
        [
            "name" => "Chinggis khaan bank",
            "active" => true
        ],
        [
            "name" => "National investment bank",
            "active" => true
        ],
        [
            "name" => "Most money",
            "active" => true
        ],
        [
            "name" => "Ard App",
            "active" => true
        ],
        [
            "name" => "Trans bank",
            "active" => true
        ]
    ],
];
