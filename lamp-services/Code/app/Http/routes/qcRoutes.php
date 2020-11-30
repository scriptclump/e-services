<?php
Route::group(['before' => 'authenticates'], function() {    
    Route::get('qc/index', 'QcController@indexAction');
    Route::get('qc/add', 'QcController@addAction');
    Route::get('qc/edit', 'QcController@editAction');
    //Route::post('legalentity/save', 'LegalEntityController@saveAction');
});