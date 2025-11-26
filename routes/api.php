<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;



Route::middleware('admin')->prefix('admin')->group(function () {
    Route::get('/users/pending', [AdminController::class, 'pendingUsers']);
    Route::put('/users/{id}/approve', [AdminController::class, 'approveUser']);
    Route::put('/users/{id}/reject', [AdminController::class, 'rejectUser']);

    Route::get('/properties/pending', [AdminController::class, 'pendingProperties']);
    Route::put('/properties/{id}/approve', [AdminController::class, 'approveProperty']);
    Route::put('/properties/{id}/reject', [AdminController::class, 'rejectProperty']);
});
