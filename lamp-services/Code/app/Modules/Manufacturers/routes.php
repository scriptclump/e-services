<?php

Route::group(['middleware' => ['web']], function () {
    Route::group(['namespace' => 'App\Modules\Manufacturers\Controllers'], function () {

        Route::any('brands', 'ManufacturerController@brandList');
        Route::get('brands/add', 'ManufacturerController@index');
        Route::any('brands/show', 'ManufacturerController@show');
        Route::any('brands/edit/{supplier_id}', 'ManufacturerController@editAction');
        Route::post('brands/delete', 'ManufacturerController@destroy');
        Route::get('brands/getProducts/{legalentity_id}', 'ManufacturerController@getProducts');
        Route::get('brands/getBrands/{legalentity_id}', 'ManufacturerController@getBrands');
        Route::get('brands/getBrandsGrid', 'ManufacturerController@getBrandsFromView');
        Route::get('brands/getManfBrandsGrid', 'ManufacturerController@getManfBrandsFromView');
        Route::get('brands/getProductsGrid', 'ManufacturerController@getProductsFromView');
        Route::get('brands/getManfProductsGrid', 'ManufacturerController@getManfProductsFromView');
        Route::get('brands/getBrands', 'ManufacturerController@getBrands');
        Route::get('brands/getManufacturers', 'ManufacturerController@getManufacturers');        
        Route::any('approvalsave','ManufacturerController@approvalSave');
        Route::any('manu/save', 'ManufacturerController@manufacturerSave');
        Route::any('manu/edit/{manufacturer_id}', 'ManufacturerController@manufacturerEdit');
        Route::any('brands/save', 'ManufacturerController@brandSave');
        Route::any('brands/deletebrand/{brand_id}', 'ManufacturerController@deleteBrandAction');
        Route::any('brands/deleteProduct/{product_id}', 'ManufacturerController@deleteProductAction');
        Route::get('brands/editBrand/{brand_id}', 'ManufacturerController@editBrandAction');
        Route::get('brands/editProduct/{product_id}', 'ManufacturerController@editProductAction');
        Route::post('brands/manfuniq', 'ManufacturerController@manfuniq');
        Route::post('brands/brandfuniq', 'ManufacturerController@brandfuniq');
    });
});
