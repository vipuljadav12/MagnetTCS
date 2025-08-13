<?php

Route::group(['module' => 'AddressOverwrite', 'middleware' => ['api'], 'namespace' => 'App\Modules\AddressOverwrite\Controllers'], function() {

    Route::resource('AddressOverwrite', 'AddressOverwriteController');

});
