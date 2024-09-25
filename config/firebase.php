<?php

return [
    'credentials' => [
        'file' => env('FIREBASE_CREDENTIALS'),
    ],
    'database' => [
        'url' => env('FIREBASE_DATABASE_URL'),
    ],
    'storage' => [
        'bucket' => env('FIREBASE_STORAGE_BUCKET'),
    ],
    'project_id' => env('FIREBASE_PROJECT_ID'),
];