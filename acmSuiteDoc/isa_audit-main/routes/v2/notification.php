<?php

use App\Http\Controllers\V2\NotificationController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth'], 'prefix' => 'v2'], function () {
  Route::controller(NotificationController::class)->group(function () {
    Route::get('notification', 'index')->name('notification.records.index');
    Route::get('notification/total', 'total')->name('notification.records.total');
    Route::put('notification/update', 'update')->name('notification.records.update');
    Route::delete('notification/{id}', 'destroy')->name('notification.records.destroy');
  });
});