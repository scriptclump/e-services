<?php
   
    Route::get('salesOrder/index', 'SalesOrderController@indexAction');
    Route::get('sales/add', 'SalesOrderController@addAction');
    Route::get('sales/edit', 'SalesOrderController@editAction');
    //Route::post('legalentity/save', 'ProductController@saveAction');
