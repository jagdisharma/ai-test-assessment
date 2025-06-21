<?php

return [
    'api_key' => env('OPENAI_API_KEY'),
    'classify_enabled' => env('OPENAI_CLASSIFY_ENABLED', false),
    'base_uri' => env('OPENAI_BASE_URI', 'https://api.openai.com/v1'),
    'request_timeout' => env('OPENAI_REQUEST_TIMEOUT', 30),
    'max_retries' => env('OPENAI_MAX_RETRIES', 1),
    'retry_delay' => env('OPENAI_RETRY_DELAY', 100),
    'handler' => null,
];
