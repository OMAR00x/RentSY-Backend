<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ApartmentController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\AmenityController;

// Auth
Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);


Route::middleware('auth:sanctum')->group(function () {

    // Apartments
    Route::get('/apartments', [ApartmentController::class, 'index']);
    Route::get('/apartments/{id}', [ApartmentController::class, 'show']);

    // Reviews
    Route::post('/reviews', [ReviewController::class, 'store']);
    Route::get('/apartments/{id}/reviews', [ReviewController::class, 'apartmentReviews']);

    //  Data
    Route::get('/cities', [CityController::class, 'index']);
    Route::get('/cities/{id}/areas', [CityController::class, 'areas']);
    Route::get('/amenities', [AmenityController::class, 'index']);

    // User
    Route::get('/profile', [UserController::class, 'profile']);
    Route::put('/profile', [UserController::class, 'updateProfile']);
    Route::post('/logout', [UserController::class, 'logout']);

    // Favorites
    Route::post('/apartments/{id}/favorite', [ApartmentController::class, 'toggleFavorite']);
    Route::get('/favorites', [ApartmentController::class, 'favorites']);

    // Owner Apartments
    Route::get('/my-apartments', [ApartmentController::class, 'ownerApartments']);
    Route::post('/apartments', [ApartmentController::class, 'store']);
    Route::put('/apartments/{id}', [ApartmentController::class, 'update']);
    Route::delete('/apartments/{id}', [ApartmentController::class, 'destroy']);

    // Bookings
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/my-bookings', [BookingController::class, 'myBookings']);
    Route::get('/apartment-bookings', [BookingController::class, 'apartmentBookings']);
    Route::put('/bookings/{id}/status', [BookingController::class, 'updateStatus']);
    Route::put('/bookings/{id}/reschedule', [BookingController::class, 'reschedule']);
    Route::delete('/bookings/{id}', [BookingController::class, 'cancel']);


    // Search History
    Route::get('/search-history', [SearchController::class, 'history']);
    Route::post('/search-history', [SearchController::class, 'store']);
    Route::delete('/search-history/{id}', [SearchController::class, 'delete']);
    Route::delete('/search-history', [SearchController::class, 'clear']);

    // Messages
    Route::get('/conversations', [MessageController::class, 'conversations']);
    Route::get('/chat/{userId}', [MessageController::class, 'chat']);
    Route::post('/messages', [MessageController::class, 'send']);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::put('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
});
