<?php

Route::group(['module' => 'AuditTrailData', 'middleware' => ['api'], 'namespace' => 'App\Modules\AuditTrailData\Controllers'], function() {

    Route::resource('AuditTrailData', 'AuditTrailDataController');

});
