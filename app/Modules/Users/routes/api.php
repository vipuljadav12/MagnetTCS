<?php

Route::group(['module' => 'Users', 'middleware' => ['api'], 'namespace' => 'App\Modules\Users\Controllers'], function() {

    Route::resource('Users', 'UsersController');

});
