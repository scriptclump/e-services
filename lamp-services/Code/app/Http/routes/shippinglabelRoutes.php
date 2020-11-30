<?php
Route::group(['before' => 'authenticates'], function() {    
    Route::get('shippinglabel/index', 'ShippingLabelController@indexAction');
    Route::get('shippinglabel/add', 'ShippingLabelController@addAction');
    Route::get('shippinglabel/edit', 'ShippingLabelController@editAction');
    //Route::post('legalentity/save', 'LegalEntityController@saveAction');
});