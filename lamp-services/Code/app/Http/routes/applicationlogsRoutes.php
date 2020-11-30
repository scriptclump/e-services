<?php
Route::group(['before' => 'authenticates'], function() {    
    Route::get('applicationlogs/index', 'ApplicationLogsController@indexAction');
    Route::get('applicationlogs/add', 'ApplicationLogsController@addAction');
    Route::get('applicationlogs/edit', 'ApplicationLogsController@editAction');
    //Route::post('legalentity/save', 'LegalEntityController@saveAction');
});