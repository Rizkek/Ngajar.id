<?php

return [
    'api_key' => env('XENDIT_API_KEY', ''),
    'callback_token' => env('XENDIT_CALLBACK_TOKEN', ''),
    'mode' => env('XENDIT_MODE', 'sandbox'), // sandbox or production
];
