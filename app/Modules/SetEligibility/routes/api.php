<?php

Route::group(['module' => 'SetEligibility', 'middleware' => ['api'], 'namespace' => 'App\Modules\SetEligibility\Controllers'], function() {

    Route::resource('SetEligibility', 'SetEligibilityController');

});
