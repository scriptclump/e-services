<?php
Route::group(['before' => 'authenticates'], function() {    
    Route::get('picklist/index', 'PicklistController@indexAction');
    Route::get('picklist/add', 'PicklistController@addAction');
    Route::get('picklist/edit', 'PicklistController@editAction');
    //Route::post('legalentity/save', 'LegalEntityController@saveAction');
});