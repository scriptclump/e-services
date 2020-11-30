<?php
Route::group(['before' => 'authenticates'], function() {    
    Route::get('outboundhistory/index', 'OutboundHistoryController@indexAction');
    Route::get('outboundhistory/add', 'OutboundHistoryController@addAction');
    Route::get('outboundhistory/edit', 'OutboundHistoryController@editAction');
    //Route::post('legalentity/save', 'LegalEntityController@saveAction');
});