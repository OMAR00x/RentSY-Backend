<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Firebase Credentials Path
    |--------------------------------------------------------------------------
    |
    | Path to your Firebase service account credentials JSON file
    |
    */
    'credentials' => env('FIREBASE_CREDENTIALS', base_path('rentsy-721c8-firebase-adminsdk-fbsvc-a8ac04453d.json')),

    /*
    |--------------------------------------------------------------------------
    | Firebase Project ID
    |--------------------------------------------------------------------------
    |
    | Your Firebase project ID
    |
    */
    'project_id' => env('FIREBASE_PROJECT_ID', 'rent-sy-721c8'),
];
