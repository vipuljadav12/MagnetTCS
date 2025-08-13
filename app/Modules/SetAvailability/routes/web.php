<?php

Route::group(['prefix'=> 'admin/Availability','module' => 'SetAvailability', 'middleware' => ['web','auth','permission'], 'namespace' => 'App\Modules\SetAvailability\Controllers'], function() {

    Route::get('/Set', 'SetAvailabilityController@index');
    Route::get('/getOptionsByProgram/{program}', 'SetAvailabilityController@getOptionsByProgram');
    Route::post('/store', 'SetAvailabilityController@store');

    Route::resource('/', 'SetAvailabilityController');

    // application filter programs
	Route::get('/get_programs', 'SetAvailabilityController@getPrograms');    
});
