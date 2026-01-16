<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ApartmentController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ChatController;
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


    // Data
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

    // Owner - Apartments Management
    Route::get('/my-apartments', [ApartmentController::class, 'ownerApartments']);
    Route::post('/apartments', [ApartmentController::class, 'store']);
    Route::delete('/apartments/{id}', [ApartmentController::class, 'destroy']);

    // Owner - Booking Requests
    Route::get('/apartment-bookings', [BookingController::class, 'apartmentBookings']);
    Route::put('/bookings/{id}/status', [BookingController::class, 'updateStatus']);


    // Renter - Bookings
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/my-bookings', [BookingController::class, 'myBookings']);
    Route::put('/bookings/{id}/reschedule', [BookingController::class, 'reschedule']);
    Route::delete('/bookings/{id}', [BookingController::class, 'cancel']);

    // Reviews
    Route::post('/reviews', [ReviewController::class, 'store']);
    Route::get('/apartments/{id}/reviews', [ReviewController::class, 'apartmentReviews']);

    // Search History
    Route::get('/search-history', [SearchController::class, 'history']);
    //Route::delete('/search-history/{id}', [SearchController::class, 'delete']);
    //Route::delete('/search-history', [SearchController::class, 'clear']);

    // Chat
    Route::post('/chat/send', [ChatController::class, 'sendMessage']);
    Route::get('/chat/messages/{apartmentId}', [ChatController::class, 'getMessages']);
    Route::get('/chat/conversations', [ChatController::class, 'getConversations']);
    Route::post('/chat/mark-read/{apartmentId}', [ChatController::class, 'markAsRead']);
    Route::put('/chat/messages/{messageId}/read', [ChatController::class, 'markMessageAsRead']);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);


    // FCM Token
    Route::post('/fcm-token', [UserController::class, 'updateFcmToken']);
});
