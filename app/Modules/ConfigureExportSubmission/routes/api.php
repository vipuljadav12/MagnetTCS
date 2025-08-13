<?php

Route::group(['module' => 'ConfigureExportSubmission', 'middleware' => ['api'], 'namespace' => 'App\Modules\ConfigureExportSubmission\Controllers'], function() {

    Route::resource('ConfigureExportSubmission', 'PriorityController');

});
