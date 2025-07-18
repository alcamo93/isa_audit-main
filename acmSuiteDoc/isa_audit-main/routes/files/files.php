<?php

use App\Http\Controllers\Files\FilesController;
use Illuminate\Support\Facades\Route;

// views
Route::get('library', [FilesController::class, 'index'])->middleware('profile');
Route::post('library/documents', [FilesController::class, 'getFilesLibrary'])->middleware('profile');

// back
Route::post('files', [FilesController::class, 'getfilesDT']);
Route::get('files/download/{idFile}', [FilesController::class, 'downloadFile']);
Route::post('files/set', [FilesController::class, 'setFile']);
Route::post('files/delete', [FilesController::class, 'deleteFile']);

// generics
Route::get('files/{idCorporate}', [FilesController::class, 'getAuditProcessesByClientAndPlant']);
Route::get('files-test', [FilesController::class, 'testFiles']);
