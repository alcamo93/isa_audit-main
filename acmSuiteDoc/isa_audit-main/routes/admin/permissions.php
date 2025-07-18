<?php

use App\Http\Controllers\Admin\PermissionController;
use Illuminate\Support\Facades\Route;

// crud
Route::get('permissions', [PermissionController::class, 'index'])->middleware('profile');
Route::post('permissions', [PermissionController::class, 'getPermissionsDT']);
Route::post('permissions/set', [PermissionController::class, 'setPermission']);
Route::get('permissions/get', [PermissionController::class, 'getPermissions']);
//submodel
Route::post('permissions/submodule', [PermissionController::class, 'getPermissionsSubmoduleDT']);