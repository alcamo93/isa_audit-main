<?php

use App\Http\Controllers\Dashboard\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('dashboard', [DashboardController::class, 'index']);
Route::post('dashboard/contract', [DashboardController::class, 'getContractInfo']);
Route::post('dashboard/corporates', [DashboardController::class, 'getCorporates']);