<?php

use App\Http\Controllers\News\NewsController;
use Illuminate\Support\Facades\Route;

// view
Route::get('news', [NewsController::class, 'index'])->middleware('profile');
Route::post('get/news', [NewsController::class, 'getListNews']);
// crud
Route::get('news/{idNew}', [NewsController::class, 'getNew']);
Route::post('news/set', [NewsController::class, 'saveNews']);
Route::post('news/update', [NewsController::class, 'updateNews']);
Route::post('news/delete', [NewsController::class, 'deleteNews']);
// source
/*Route::get('profiles/filter/{idCorporate}', 'Admin\ProfilesController@getProfilesFilter');*/
