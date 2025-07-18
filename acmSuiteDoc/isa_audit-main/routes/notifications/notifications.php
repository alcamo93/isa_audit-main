<?php

use App\Http\Controllers\Notifications\NotificationsController;
use Illuminate\Support\Facades\Route;

Route::get('notifications', [NotificationsController::class, 'index']);
Route::post('notifications/unread',[NotificationsController::class, 'unreadDT']);
Route::post('notifications/read',[NotificationsController::class, 'unreadDT']);
Route::post('notifications/marked/read',[NotificationsController::class, 'markedRead']);
Route::post('notifications/destroy',[NotificationsController::class, 'destroy']);
Route::get('notifications/total', [NotificationsController::class, 'totalNotification']);
