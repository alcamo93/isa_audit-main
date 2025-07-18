<?php

use App\Http\Controllers\V2\Files\ImageController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth'], 'prefix' => 'v2'], function () {
  Route::controller(ImageController::class)->group(function () {
    Route::post('image', 'store')->name('image.store');
    Route::get('image/{id}', 'show')->name('image.show');
    Route::delete('image/{id}', 'destroy')->name('image.destroy');
    Route::get('image/download/{id}', 'download')->name('image.download');
  });
});