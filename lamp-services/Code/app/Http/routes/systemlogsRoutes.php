<?php
Route::group(['before' => 'authenticates'], function() {    
    Route::get('systemlogs/index', 'SystemLogsController@indexAction');
    Route::get('systemlogs/add', 'SystemLogsController@addAction');
    Route::get('systemlogs/edit', 'SystemLogsController@editAction');
    //Route::post('legalentity/save', 'LegalEntityController@saveAction');
});