<?php

// return [
//     'credentials' => [
//         'file' => env('FIREBASE_CREDENTIALS'),
//     ],

//     'storage' => [
//         'bucket' => env('FIREBASE_STORAGE_BUCKET'),
//     ],

//     'database' => [
//     'url' => env('FIREBASE_DATABASE_URL'),
// ],

// 'database_url' => env('FIREBASE_DATABASE_URL'),

// ];

return [
    'firebase' => [
        'credentials' => [
            'file' => env('FIREBASE_CREDENTIALS'),
        ],
        'database' => [
            'url' => env('FIREBASE_DATABASE_URL'),
        ],
        'storage' => [
            'bucket' => env('FIREBASE_STORAGE_BUCKET'),
        ],
    ],
];
