<?php
Route::group(['middleware' => ['web']], function () {
    Route::group(['prefix' => 'purchase', 'namespace' => 'App\Modules\PurchaseDashboard\Controllers'], function () {
       Route::any('/', 'PurchaseDashboardController@index');
        Route::post('/', 'PurchaseDashboardController@getIndexData');
        Route::get('/{wh_id}', 'PurchaseDashboardController@index');
         // Below Tabs Routes
        Route::any('/getpoorderdetails', 'PurchaseDashboardController@getPOOrderDetailsDashboard');
        Route::any('/getgrndetails', 'PurchaseDashboardController@getGRNDetailsDashboard');
        Route::any('/getinventorydetails', 'PurchaseDashboardController@getInventoryDetailsDashboard');
        Route::any('/getbrands', 'PurchaseDashboardController@getBrands');
        Route::any('/getproductgroupbybrand', 'PurchaseDashboardController@getProductGroupByBrand');
        Route::any('/getbu','PurchaseDashboardController@getBuUnit');
        Route::any('/salesDetails', 'PurchaseDashboardController@getSaleDetails');
        Route::any('/getexportdetails', 'PurchaseDashboardController@getExportPoDetails');
        Route::any('/getSupplierList', 'PurchaseDashboardController@getSupplierList'); 
    });
});