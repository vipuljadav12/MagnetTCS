<?php

Route::group(['module' => 'From', 'middleware' => ['api'], 'namespace' => 'App\Modules\From\Controllers'], function() {

    Route::resource('From', 'FromController');

});
