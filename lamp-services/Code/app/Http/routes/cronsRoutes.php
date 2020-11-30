<?php
Route::group(['before' => 'authenticates'], function() {    
    Route::get('crons/index', 'CronsController@indexAction');
    Route::get('crons/add', 'CronsController@addAction');
    Route::get('crons/edit', 'CronsController@editAction');
    //Route::post('legalentity/save', 'LegalEntityController@saveAction');
});