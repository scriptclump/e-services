<?php
Route::group(['before' => 'authenticates'], function() {    
    Route::get('categories/index', 'CategoriesController@indexAction');
    Route::get('categories/add', 'CategoriesController@addAction');
    Route::get('categories/edit', 'CategoriesController@editAction');
    Route::get('getCategoryList','CategoriesController@getCategoryList');
    Route::get('getAddCategoryList','CategoriesController@getAddCategoryList');
    //Route::post('legalentity/save', 'LegalEntityController@saveAction');
});