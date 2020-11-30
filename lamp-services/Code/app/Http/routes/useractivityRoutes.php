<?php
Route::group(['before' => 'authenticates'], function() {    
    Route::get('useractivity/index', 'UserActivityController@indexAction');
    Route::get('useractivity/add', 'UserActivityController@addAction');
    Route::get('useractivity/edit', 'UserActivityController@editAction');
    //Route::post('legalentity/save', 'LegalEntityController@saveAction');
});