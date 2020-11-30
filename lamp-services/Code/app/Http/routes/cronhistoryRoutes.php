<?php
Route::group(['before' => 'authenticates'], function() {    
    Route::get('cronhistory/index', 'CronHistoryController@indexAction');
    Route::get('cronhistory/add', 'CronHistoryController@addAction');
    Route::get('cronhistory/edit', 'CronHistoryController@editAction');
    //Route::post('legalentity/save', 'LegalEntityController@saveAction');
});