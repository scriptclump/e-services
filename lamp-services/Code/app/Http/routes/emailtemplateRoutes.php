<?php
Route::group(['before' => 'authenticates'], function() {    
    Route::get('emailtemplate/index', 'EmailTemplateController@indexAction');
    Route::get('emailtemplate/add', 'EmailTemplateController@addAction');
    Route::get('emailtemplate/edit', 'EmailTemplateController@editAction');
    //Route::post('legalentity/save', 'LegalEntityController@saveAction');
});