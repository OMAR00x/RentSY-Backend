<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/storage/private/{path}', function ($path) {
    if (!Storage::disk('local')->exists($path)) {
        abort(404);
    }
    return response()->file(Storage::disk('local')->path($path));
})->where('path', '.*')->name('private.file');

// Test Notifications Interface
Route::get('/test-notifications', function () {
    return view('test-notifications');
});
