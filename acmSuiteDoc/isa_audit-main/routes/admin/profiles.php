<?php

use App\Http\Controllers\Admin\ProfilesController;
use Illuminate\Support\Facades\Route;

// view
Route::get('profiles', [ProfilesController::class, 'index'])->middleware('profile');
Route::post('profiles', [ProfilesController::class, 'getProfilesDT']);
// crud
Route::get('profiles/{idProfile}', [ProfilesController::class, 'getProfile']);
Route::post('profiles/set', [ProfilesController::class, 'setProfile']);
Route::post('profiles/update', [ProfilesController::class, 'updateProfile']);
Route::post('profiles/delete', [ProfilesController::class, 'deleteProfile']);
// source
Route::get('profiles/filter/{idCorporate}', [ProfilesController::class, 'getProfilesFilter']); 