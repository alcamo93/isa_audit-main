<?php

use App\Http\Controllers\V2\Files\FileController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth'], 'prefix' => 'v2'], function () {
    Route::controller(FileController::class)->group(function () {
        Route::get('file', 'index')->name('file.index');
        Route::post('file', 'store')->name('file.store');
        Route::put('file/{id}', 'update')->name('file.records.update');
        Route::get('file/download/{id}', 'download')->name('file.records.download');
        Route::get('file/download/base/{id}', 'downloadBase')->name('file.records.download.base');
    });
});