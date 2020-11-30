<?php
Route::group(['before' => 'authenticates'], function() {    
    Route::get('inboundhistory/index', 'InboundHistoryController@indexAction');
    Route::get('inboundhistory/add', 'InboundHistoryController@addAction');
    Route::get('inboundhistory/edit', 'InboundHistoryController@editAction');
    //Route::post('legalentity/save', 'LegalEntityController@saveAction');
});