<?php

use Illuminate\Support\Facades\Route;

Route::group(['module' => 'StudentProfileEligibility', 'middleware' => ['api'], 'namespace' => 'App\Modules\StudentProfileEligibility\Controllers'], function() {

    Route::resource('StudentProfileEligibility', 'PriorityController');

});
