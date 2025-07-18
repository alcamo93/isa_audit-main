<?php

use App\Http\Controllers\Audit\CommentsController;
use Illuminate\Support\Facades\Route;

// Comments
Route::get('comments', [CommentsController::class, 'getComments']);
Route::post('comments/set', [CommentsController::class, 'setComment']);
Route::post('comments/delete', [CommentsController::class, 'deleteComment']);