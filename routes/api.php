<?php

use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/notification', [NotificationController::class, 'store']);
    Route::get('/notification/{notification}', [NotificationController::class, 'show']);
    Route::get('/users/{user}/notifications', [UserController::class, 'notifications']);
});
