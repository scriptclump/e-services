<?php
Route::group(['before' => 'authenticates'], function() {    
    Route::get('manifest/index', 'ManifestController@indexAction');
    Route::get('manifest/add', 'ManifestController@addAction');
    Route::get('manifest/edit', 'ManifestController@editAction');
    //Route::post('legalentity/save', 'LegalEntityController@saveAction');
});