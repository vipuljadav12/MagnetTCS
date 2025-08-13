<?php

Route::group(['module' => 'Program', 'middleware' => ['api'], 'namespace' => 'App\Modules\Program\Controllers'], function() {

    Route::resource('Program', 'ProgramController');

});
