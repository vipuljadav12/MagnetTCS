<?php
Route::group(['prefix'=>'admin/SetEligibility','module' => 'SetEligibility', 'middleware' => ['web','auth','permission'], 'namespace' => 'App\Modules\SetEligibility\Controllers'], function() {

    Route::get('/edit/{id}', 'SetEligibilityController@edit');
    Route::get('/edit/{id}/{application_id}', 'SetEligibilityController@edit');

    Route::post("/update/{id}","SetEligibilityController@update");

    Route::get('/extra_values', 'SetEligibilityController@extra_values');
    Route::get('/extra_values/session', 'SetEligibilityController@extra_values_session');
    Route::post('/extra_values/save', 'SetEligibilityController@extra_value_save');

    Route::get('/configurations', 'SetEligibilityController@configurations');
    Route::get('/configurations/session', 'SetEligibilityController@configurations_session');
    Route::post('/configurations/save', 'SetEligibilityController@configurations_save');

    Route::resource('/', 'SetEligibilityController');

    /*Route::get('student_profile', 'SetEligibilityController@studentProfile');
    Route::post('student_profile/test_score', 'SetEligibilityController@testScore');*/
});