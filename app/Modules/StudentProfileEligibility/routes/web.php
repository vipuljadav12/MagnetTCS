<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix'=>'admin/StudentProfileEligibility', 'module' => 'StudentProfileEligibility', 'middleware' => ['web', 'auth','super'], 'namespace' => 'App\Modules\StudentProfileEligibility\Controllers'], function() {

	Route::get('/set_data', 'StudentProfileEligibilityController@setData');
	Route::get('/', 'StudentProfileEligibilityController@index');
	Route::get('create', 'StudentProfileEligibilityController@createEdit');
	Route::post('test_score/{id?}', 'StudentProfileEligibilityController@testScore');
	Route::post('/store/{id?}', 'StudentProfileEligibilityController@store');
	Route::get('/edit/{id}', 'StudentProfileEligibilityController@createEdit');
	Route::get('/delete/{id}', 'StudentProfileEligibilityController@delete');
	Route::get('/validate/grades', 'StudentProfileEligibilityController@validateGrades');
});
