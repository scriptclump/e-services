<?php
Route::group(['before' => 'authenticates'], function() {    
    Route::get('consigment/index', 'ConsigmentController@indexAction');
    Route::get('consigment/add', 'ConsigmentController@addAction');
    Route::get('consigment/edit', 'ConsigmentController@editAction');
    //Route::post('legalentity/save', 'LegalEntityController@saveAction');
});