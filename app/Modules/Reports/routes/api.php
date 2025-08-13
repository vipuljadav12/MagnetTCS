<?php

Route::group(['module' => 'Submissions', 'middleware' => ['api'], 'namespace' => 'App\Modules\Submissions\Controllers'], function() {

    Route::resource('Submissions', 'SubmissionsController');

});
