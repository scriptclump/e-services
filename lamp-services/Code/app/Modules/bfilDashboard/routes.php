<?php
Route::group(['middleware' => ['web']], function () {
    Route::group(['prefix' => 'stockist', 'namespace' => 'App\Modules\bfilDashboard\Controllers'], function () {
        Route::any('/', 'bfilDashboardController@index');
        Route::post('/', 'bfilDashboardController@getIndexData');
        Route::any('/stockistsales', 'bfilDashboardController@getStockistSales');
        
        Route::any('/gettodayStockistlist', 'bfilDashboardController@gettodayStockistlist');

        Route::any('/getbrands', 'bfilDashboardController@getBrands');

        Route::any('/getproductgroupbybrand', 'bfilDashboardController@getProductGroupByBrand');
        Route::any('/getbu','bfilDashboardController@getBuUnit');
        Route::any('/getexportdetails', 'bfilDashboardController@getExportSalesDetails');
    });
});
