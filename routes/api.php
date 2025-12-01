<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\userController;




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


//******************** */
Route::get('/get', [UserController::class, 'get']);
Route::post('/create', [UserController::class, 'create']);
Route::put('/update', [UserController::class, 'update']);
Route::delete('/delete', [UserController::class, 'delete']);
