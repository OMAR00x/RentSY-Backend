<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Services\FirebaseService;
use App\Services\NotificationService;

Route::get('/storage/private/{path}', function ($path) {
    if (!Storage::disk('local')->exists($path)) {
        abort(404);
    }
    return response()->file(Storage::disk('local')->path($path));
})->where('path', '.*')->name('private.file');

// Test Firebase Notification
Route::get('/test-notification/{userId}', function ($userId) {
    $firebaseService = new FirebaseService();
    
    if (!$firebaseService->isInitialized()) {
        return response()->json([
            'status' => 'error',
            'message' => $firebaseService->getInitError()
        ], 500);
    }
    
    $notificationService = new NotificationService($firebaseService);
    
    $result = $notificationService->sendToUser(
        $userId,
        'اختبار الإشعارات',
        'هذا إشعار تجريبي من RentSY',
        ['type' => 'test', 'time' => now()->toDateTimeString()]
    );
    
    return response()->json([
        'status' => 'success',
        'result' => $result
    ]);
});
