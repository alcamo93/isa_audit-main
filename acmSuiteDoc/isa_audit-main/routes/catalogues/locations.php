<?php

use App\Http\Controllers\Catalogues\LocationsController;
use Illuminate\Support\Facades\Route;

Route::get('locations/states/{idCountry}', [LocationsController::class, 'getStates']);
Route::get('locations/cities/{idState}', [LocationsController::class, 'getCities']);