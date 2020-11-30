<?php
Route::group(['before' => 'authenticates'], function() {    
    Route::get('inventory/index', 'InventoryController@indexAction');
    Route::get('inventory/add', 'InventoryController@addAction');
    Route::get('inventory/edit', 'InventoryController@editAction');
    //Route::post('legalentity/save', 'LegalEntityController@saveAction');
});