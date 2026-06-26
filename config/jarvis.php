<?php

return [
    'release' => env('JARVIS_RELEASE', 'v1'),

    'storage' => [
        'asset_disk' => env('JARVIS_ASSET_DISK', env('FILESYSTEM_DISK', 'local')),
        'asset_root' => env('JARVIS_ASSET_ROOT', 'workspaces'),
        'max_upload_mb' => (int) env('JARVIS_MAX_UPLOAD_MB', 512),
    ],

    'queue' => [
        'publishing' => env('JARVIS_PUBLISHING_QUEUE', 'publishing'),
        'activity' => env('JARVIS_ACTIVITY_QUEUE', 'default'),
        'media' => env('JARVIS_MEDIA_QUEUE', 'media'),
    ],

    'features' => [
        'openai_enabled' => false,
        'external_publishing_enabled' => false,
        'video_editing_enabled' => false,
    ],
];
