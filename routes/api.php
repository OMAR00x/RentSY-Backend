<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\userController;
use App\Http\Controllers\ApartmentController;


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
