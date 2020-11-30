<?php
   
    Route::get('listing/index', 'ProductListingController@indexAction');
    Route::get('product/add', 'ProductListingController@addAction');
    Route::get('product/edit', 'ProductListingController@editAction');
    //Route::post('legalentity/save', 'ProductController@saveAction');
