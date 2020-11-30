<?php
Route::group(['before' => 'authenticates'], function() {    
    Route::get('apilogs/index', 'ApiLogsController@indexAction');
    Route::get('apilogs/add', 'ApiLogsController@addAction');
    Route::get('apilogs/edit', 'ApiLogsController@editAction');
    //Route::post('legalentity/save', 'LegalEntityController@saveAction');
});