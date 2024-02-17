<?php declare(strict_types=1);

return [
    'token' => [
        'value' => env('API_TOKEN', '!Chang3Me!'),
        'length' => env('API_TOKEN_LENGTH', 64),
    ],
];
