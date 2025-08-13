<?php

Route::group(['module' => 'Student', 'middleware' => ['api'], 'namespace' => 'App\Modules\Student\Controllers'], function() {

    Route::resource('Student', 'StudentController');

});
