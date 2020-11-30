<?php
Route::group(['before' => 'authenticates'], function() {    
    Route::get('attribute/index', 'AttributeController@indexAction');
    Route::get('attribute/add', 'AttributeController@addAction');
    Route::get('attribute/edit', 'AttributeController@editAction');
    //Route::post('legalentity/save', 'LegalEntityController@saveAction');
});