<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return view('welcome');
});
Route::middleware(['auth:admin', 'AdminMiddleware'])->prefix('admin')->group(function () {
    Route::get('/users/pending', [AdminController::class, 'pendingUsers']);
    Route::put('/users/{id}/approve', [AdminController::class, 'approveUser']);
    Route::put('/users/{id}/reject', [AdminController::class, 'rejectUser']);

    Route::get('/apartments/pending', [AdminController::class, 'pendingApartments']);
    Route::put('/apartments/{id}/approve', [AdminController::class, 'approveApartment']);
    Route::put('/apartments/{id}/reject', [AdminController::class, 'rejectApartment']);
});
