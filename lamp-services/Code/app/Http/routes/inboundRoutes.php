<?php
Route::group(['before' => 'authenticates'], function() {    
    Route::get('inbound/index', 'InboundController@indexAction');
    Route::get('inbound/add', 'InboundController@addAction');
    Route::get('inbound/edit', 'InboundController@editAction');
    //Route::post('legalentity/save', 'LegalEntityController@saveAction');
});