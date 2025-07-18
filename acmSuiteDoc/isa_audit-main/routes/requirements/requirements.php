<?php

//requirements
Route::get('requirements', 'Requirements\RequirementsController@index')->middleware('profile');
Route::post('requirements/requirements', 'Requirements\RequirementsController@getRequirementsDT');
Route::post('requirements/requirements/set', 'Requirements\RequirementsController@setRequirement');
Route::post('requirements/requirements/get', 'Requirements\RequirementsController@getRequirementById');
Route::post('requirements/requirements/update', 'Requirements\RequirementsController@updateRequirement');
Route::post('requirements/requirements/delete', 'Requirements\RequirementsController@deleteRequirement');


