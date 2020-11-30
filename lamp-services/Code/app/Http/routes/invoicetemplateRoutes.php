<?php
Route::group(['before' => 'authenticates'], function() {    
    Route::get('invoicetemplate/index', 'InvoiceTemplateController@indexAction');
    Route::get('invoicetemplate/add', 'InvoiceTemplateController@addAction');
    Route::get('invoicetemplate/edit', 'InvoiceTemplateController@editAction');
    //Route::post('legalentity/save', 'LegalEntityController@saveAction');
});