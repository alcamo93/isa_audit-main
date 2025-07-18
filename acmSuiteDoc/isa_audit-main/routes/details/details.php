<?php

use App\Http\Controllers\Details\DetailsController;
use Illuminate\Support\Facades\Route;

// views
Route::get('details/basis/{data}', [DetailsController::class, 'getBasiesAudit']);