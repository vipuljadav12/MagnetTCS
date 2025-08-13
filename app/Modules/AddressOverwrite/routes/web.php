<?php

Route::group(['prefix'=>'admin/AddressOverwrite', 'module' => 'AddressOverwrite', 'middleware' => ['web', 'auth'], 'namespace' => 'App\Modules\AddressOverwrite\Controllers'], function() {

	Route::get('/', 'AddressOverwriteController@index');
	Route::post('/data', 'AddressOverwriteController@data');
	Route::post('/data/update', 'AddressOverwriteController@updateData');
	Route::get('/listing', 'AddressOverwriteController@getListing');
});
