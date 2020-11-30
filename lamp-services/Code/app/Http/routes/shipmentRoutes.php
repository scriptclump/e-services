<?php
Route::group(['before' => 'authenticates'], function() {    
    Route::get('shipment/index', 'ShipmentController@indexAction');
    Route::get('shipment/add', 'ShipmentController@addAction');
    Route::get('shipment/edit', 'ShipmentController@editAction');
    //Route::post('legalentity/save', 'LegalEntityController@saveAction');
});