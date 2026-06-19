<?php

return [
    'line' => [
        'channel_access_token' => env('LINE_CHANNEL_ACCESS_TOKEN'),
        'user_id' => env('LINE_USER_ID'),
        'group_id' => env('LINE_GROUP_ID'),
        'admin_base_url' => env('LINE_ADMIN_BASE_URL', 'http://127.0.0.1:8000'),
    ],
];
