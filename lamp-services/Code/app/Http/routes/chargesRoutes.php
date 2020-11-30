<?php
Route::group(['before' => 'authenticates'], function() {    
    Route::get('charges/index', 'ChargesController@indexAction');
    Route::get('charges/add', 'ChargesController@addAction');
    Route::get('charges/edit', 'ChargesController@editAction');
    //Route::post('legalentity/save', 'LegalEntityController@saveAction');
});