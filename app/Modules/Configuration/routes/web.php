<?php

Route::group(['prefix'=>'admin/Configuration','module' => 'Configuration', 'middleware' => ['web','auth','permission'], 'namespace' => 'App\Modules\Configuration\Controllers'], function() {

    Route::get('/', 'ConfigurationController@index');
    Route::get('/create', 'ConfigurationController@create');
    Route::post('/store', 'ConfigurationController@store');
    Route::get('/edit/{id}', 'ConfigurationController@edit');
    Route::post('/update/{id}', 'ConfigurationController@update');
    Route::get('/delete/{id}', 'ConfigurationController@delete');
    Route::get('/status', 'ConfigurationController@changeStatus');

});
