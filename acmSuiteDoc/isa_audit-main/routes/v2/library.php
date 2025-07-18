<?php

use App\Http\Controllers\V2\Files\LibraryController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth'], 'prefix' => 'v2'], function () {
    Route::controller(LibraryController::class)->group(function () {
        Route::get('library/view', 'view')->name('v2.library.view');
        Route::get('library', 'index')->name('v2.library.index');
        Route::post('library', 'store')->name('v2.library.store');
        Route::get('library/{id}', 'show')->name('v2.library.show');
        Route::post('library/{id}/update', 'update')->name('v2.library.update');
        Route::post('library/{id}/renewal', 'renewal')->name('v2.library.renewal');
        Route::put('library/{id}/approve', 'approve')->name('v2.library.approve');
    });
});