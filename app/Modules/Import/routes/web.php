<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix'=>'admin/import','module' => 'Import', 'middleware' => ['web','auth'], 'namespace' => 'App\Modules\Import\Controllers'], function() {

    Route::get('/gifted_students', 'ImportController@importGiftedStudents');
    Route::post('/gifted_students/save', 'ImportController@saveGiftedStudents');

    Route::get('/agt_nch', 'ImportController@importAGTNewCentury');
    Route::post('/agt_nch/save', 'ImportController@storeImportAGTNewCentury');

    Route::get('/test_scores', 'ImportController@testScores');
    Route::get('/test_scores/sample', 'ImportController@sampleTestScores');
    Route::post('/test_scores/save', 'ImportController@storeTestScores');
});

/*admin/import/gifted_students*/