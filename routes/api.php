<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\userController;
use App\Http\Controllers\ApartmentController;




Route::middleware('admin')->prefix('admin')->group(function () {
    Route::get('/users/pending', [AdminController::class, 'pendingUsers']);
    Route::put('/users/{id}/approve', [AdminController::class, 'approveUser']);
    Route::put('/users/{id}/reject', [AdminController::class, 'rejectUser']);

    Route::get('/apartments/pending', [AdminController::class, 'pendingApartments']);
    Route::put('/apartments/{id}/approve', [AdminController::class, 'approveApartment']);
    Route::put('/apartments/{id}/reject', [AdminController::class, 'rejectApartment']);
});




/***************** */
Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);
/******************** */

// Apartments Routes


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/apartments/{id}/favorite', [ApartmentController::class, 'toggleFavorite']);
    Route::get('/favorites', [ApartmentController::class, 'favorites']);
    Route::get('/apartments', [ApartmentController::class, 'index']);
    Route::get('/apartments/{id}', [ApartmentController::class, 'show']);
});
