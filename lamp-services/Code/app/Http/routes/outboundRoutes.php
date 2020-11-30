<?php
Route::group(['before' => 'authenticates'], function() {    
    Route::get('outbound/index', 'OutboundController@indexAction');
    Route::get('outbound/add', 'OutboundController@addAction');
    Route::get('outbound/edit', 'OutboundController@editAction');
    //Route::post('legalentity/save', 'LegalEntityController@saveAction');
});