<?php

use App\Http\Controllers\Admin\CorporatesController;
use Illuminate\Support\Facades\Route;

// view
Route::get('corporates/customer/{idCustomer}', [CorporatesController::class, 'index'])->middleware('profile');;
Route::post('corporates', [CorporatesController::class, 'getCorporatesDT']);
// crud
Route::get('corporates/{idCorporate}', [CorporatesController::class, 'getCorporate']);
Route::post('corporates/set', [CorporatesController::class, 'setCorporate']);
Route::post('corporates/update', [CorporatesController::class, 'updateCorporate']);
Route::post('corporates/delete', [CorporatesController::class, 'deleteCorporate']);
// Address crud
Route::get('corporates/address/{idCorporate}', [CorporatesController::class, 'getAddressCorporate']);
Route::post('corporates/address/set', [CorporatesController::class, 'setAddressCorporate']);
Route::post('corporates/address/delete', [CorporatesController::class, 'deleteAddressCorporate']);
// Contact crud
Route::get('corporates/contact/{idCorporate}', [CorporatesController::class, 'getContactCorporate']);
Route::post('corporates/contact/set', [CorporatesController::class, 'setContactCorporate']);
Route::post('corporates/contact/delete', [CorporatesController::class, 'deleteContactCorporate']);
// source
Route::get('corporates/customers/all/{idCustomer}', [CorporatesController::class, 'getAllCorporates']);
Route::get('corporates/customers/active/{idCustomer}', [CorporatesController::class, 'getActiveCorporates']);