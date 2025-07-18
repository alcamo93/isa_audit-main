<?php

use App\Http\Controllers\V2\KnowledgeController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth'], 'prefix' => 'v2'], function () {
	Route::controller(KnowledgeController::class)->group(function () {
		Route::get('knowledge/view', 'view')->name('v2.knowledge.view');
		Route::get('knowledge', 'index')->name('v2.knowledge.index');
		Route::post('knowledge', 'store')->name('v2.knowledge.store');
		Route::get('knowledge/{id}', 'show')->name('v2.knowledge.show');
		Route::post('knowledge/{id}', 'update')->name('v2.knowledge.update');
		Route::delete('knowledge/{id}', 'destroy')->name('v2.knowledge.destroy');
		Route::put('knowledge/{id_status}/{id}/status', 'status')->name('v2.knowledge.status');
	});
});