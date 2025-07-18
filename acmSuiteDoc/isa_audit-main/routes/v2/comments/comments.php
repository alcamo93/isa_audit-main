<?php

use App\Http\Controllers\V2\Audit\CommentController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth'], 'prefix' => 'v2'], function () {
    Route::controller(CommentController::class)->group(function () {
      Route::get('comment', 'index')->name('comment.index');
      Route::post('comment', 'store')->name('comment.store');
      Route::get('comment/{id}', 'show')->name('comment.show');
      Route::put('comment/{id}', 'update')->name('comment.update');
      Route::delete('comment/{id}', 'destroy')->name('comment.destroy');
    });
});