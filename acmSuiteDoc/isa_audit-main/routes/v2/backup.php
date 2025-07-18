<?php

use App\Http\Controllers\V2\Files\BackupController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth'], 'prefix' => 'v2'], function () {
  Route::controller(BackupController::class)->group(function () {
    Route::get('backup/{idBackup}/view', 'view')->name('v2.backup.view');
    Route::post('backup', 'store')->name('v2.backup.store');
    Route::get('backup/{idBackup}', 'show')->name('v2.backup.show');
    Route::get('backup/{idBackup}/download', 'download')->name('v2.backup.download');
  });
});
