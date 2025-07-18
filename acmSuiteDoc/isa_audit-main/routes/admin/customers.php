<?php

use App\Http\Controllers\Admin\CustomersController;
use Illuminate\Support\Facades\Route;

// view
Route::get('customers', [CustomersController::class, 'index'])->middleware('profile');
Route::post('customers', [CustomersController::class, 'getCustomersDT']);
// crud
Route::get('customers/{idCustomer}', [CustomersController::class, 'getCustomer']);
Route::post('customers/set', [CustomersController::class, 'setCustomer']);
Route::post('customers/update', [CustomersController::class, 'updateCustomer']);
Route::post('customers/delete', [CustomersController::class, 'deleteCustomer']);
// logos
Route::post('customers/logos/set', [CustomersController::class, 'setCustomerLogo']);
Route::post('customers/logos/set/sm', [CustomersController::class, 'setCustomerLogoSM']);
Route::post('customers/logos/set/lg', [CustomersController::class, 'setCustomerLogoLG']);