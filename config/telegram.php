<?php

return [
    'bot_token' => '6763100628:AAFRDw6t5mLcwC59TDJQBM9dr1P7eKP1dn0',
    'chat_id' => '1273055068',
    'webhook_url' => 'http://localhost:8000/your-webhook-endpoint',
    'curl_options' => [
        'verify' => false,
        CURLOPT_SSL_VERIFYPEER => false,
    ]
];
