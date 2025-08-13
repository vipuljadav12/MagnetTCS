<?php

Route::group(['module' => 'Enrollment', 'middleware' => ['api'], 'namespace' => 'App\Modules\Enrollment\Controllers'], function() {

    Route::resource('Enrollment', 'EnrollmentController');

});
