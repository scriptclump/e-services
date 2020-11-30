<?php
Route::group(['before' => 'authenticates'], function() {    
    Route::get('manifesttemplate/index', 'ManifestTemplateController@indexAction');
    Route::get('manifesttemplate/add', 'ManifestTemplateController@addAction');
    Route::get('manifesttemplate/edit', 'ManifestTemplateController@editAction');
    //Route::post('legalentity/save', 'LegalEntityController@saveAction');
});