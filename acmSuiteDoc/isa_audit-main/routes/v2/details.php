<?php

use App\Http\Controllers\V2\DetailController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v2'], function () {
  Route::controller(DetailController::class)->group(function () {
    Route::get('details/{items}/{data}/view', 'view')->name('details.items.view');
    Route::get('details/{items}/{data}', 'render')->name('details.items.render');
  });
});