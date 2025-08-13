<?php

Route::group(['module' => 'ProcessSelection', 'middleware' => ['api'], 'namespace' => 'App\Modules\ProcessSelection\Controllers'], function() {

    Route::resource('ProcessSelection', 'ProcessSelectionController');

});
