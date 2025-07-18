<?php

use App\Http\Controllers\V2\Catalogs\ArticleController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth'], 'prefix' => 'v2/catalogs'], function () {
    Route::get('guideline/{id_guideline}/article/view', [ArticleController::class, 'view'])->name('v2.article.view');
});