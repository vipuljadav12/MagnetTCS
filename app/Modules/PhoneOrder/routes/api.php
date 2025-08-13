<?php

Route::group(['module' => 'PhoneOrder', 'middleware' => ['api'], 'namespace' => 'App\Modules\PhoneOrder\Controllers'], function() {

    Route::resource('PhoneOrder', 'PhoneOrderController');

});
