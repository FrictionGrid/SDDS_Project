<?php

return [
    'api_key' => env('OPENAI_API_KEY'),
    'chat_model' => env('OPENAI_CHAT_MODEL', 'gpt-4o-mini'),
    'embedding_model' => env('OPENAI_EMBEDDING_MODEL', 'text-embedding-3-small'),
    'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
    'timeout' => (int) env('OPENAI_HTTP_TIMEOUT', 30),
];