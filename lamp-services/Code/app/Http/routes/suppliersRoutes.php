<?php

/*Route::get('seller/index','SellerController@indexAction');
Route::get('seller/add','SellerController@sellerAction');
Route::get('seller/edit','SellerController@editAction');*/

Route::get('suppliers','SupplierController@index');
Route::any('suppliers/create','SupplierController@store');
Route::any('suppliers/show','SupplierController@show');
