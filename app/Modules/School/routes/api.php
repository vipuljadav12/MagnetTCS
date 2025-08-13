<?php

Route::group(['module' => 'School', 'middleware' => ['api'], 'namespace' => 'App\Modules\School\Controllers'], function() {

    Route::resource('School', 'SchoolController');

});
