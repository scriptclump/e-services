<?php
   
    Route::get('package/index', 'ProductPackageController@indexAction');
    Route::get('packages/add', 'ProductPackageController@addAction');
    Route::get('packages/edit', 'ProductPackageController@editAction');
    //Route::post('legalentity/save', 'ProductController@saveAction');
