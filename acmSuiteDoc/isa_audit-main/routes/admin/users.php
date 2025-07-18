<?php

use App\Http\Controllers\Admin\UsersController;
use Illuminate\Support\Facades\Route;

// view
Route::get('users', [UsersController::class, 'index'])->middleware('profile');
Route::post('users', [UsersController::class, 'getUsersDT']); // obtain users list for datatable
Route::post('users-list', [UsersController::class, 'getUsers']); // obtain users list
// crud
Route::get('users/{idUser}', [UsersController::class, 'getUser']);
Route::post('users/set', [UsersController::class, 'setUser']);
Route::post('users/update', [UsersController::class, 'updateUser']);
Route::post('users/delete', [UsersController::class, 'deleteUser']);
// source
Route::post('users/password/set', [UsersController::class, 'setPassword']);
//
Route::post('users/img', [UsersController::class, 'setUserImg']);