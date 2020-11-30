<?php
Route::group(['before' => 'authenticates'], function() {    
    Route::get('penalities/index', 'PenalitiesController@indexAction');
    Route::get('penalities/add', 'PenalitiesController@addAction');
    Route::get('penalities/edit', 'PenalitiesController@editAction');
    //Route::post('legalentity/save', 'LegalEntityController@saveAction');
});