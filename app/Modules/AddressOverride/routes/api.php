<?php

use Illuminate\Support\Facades\Route;

Route::group(['module' => 'AddressOverwrite', 'middleware' => ['api'], 'namespace' => 'App\Modules\AddressOverwrite\Controllers'], function() {

    Route::resource('AddressOverwrite', 'AddressOverwriteController');

});
