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
    'credentials' => env('FIREBASE_CREDENTIALS', base_path('rent-sy-00-firebase-adminsdk-fbsvc-4ddcfa8cf4.json')),

    /*
    |--------------------------------------------------------------------------
    | Firebase Project ID
    |--------------------------------------------------------------------------
    |
    | Your Firebase project ID
    |
    */
    'project_id' => env('FIREBASE_PROJECT_ID', 'rent-sy-00'),
];
