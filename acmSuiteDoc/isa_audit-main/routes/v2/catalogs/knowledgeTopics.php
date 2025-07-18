<?php

use App\Http\Controllers\V2\Catalogs\KnowledgeTopicController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth'], 'prefix' => 'v2/catalogs'], function () {
    Route::get('knowledge/{id_topic}/guideline_topic/view', [KnowledgeTopicController::class, 'view'])->name('v2.guideline_topic.view');
    Route::get('knowledge/topic/{id}', [KnowledgeTopicController::class, 'getGuidelinesByIdTopic'])->name('v2.guideline_by_topic.guideline_topic');
});