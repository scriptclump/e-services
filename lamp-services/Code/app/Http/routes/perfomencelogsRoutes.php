<?php
Route::group(['before' => 'authenticates'], function() {    
    Route::get('perfomencelogs/index', 'PerfomenceLogsController@indexAction');
    Route::get('perfomencelogs/add', 'PerfomenceLogsController@addAction');
    Route::get('perfomencelogs/edit', 'PerfomenceLogsController@editAction');
    //Route::post('legalentity/save', 'LegalEntityController@saveAction');
});