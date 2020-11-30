<?php

Route::group(['middleware' => ['web']], function () {
    Route::group(['namespace' => 'App\Modules\InventoryStatusReports\Controllers'], function () {
        Route::any('inventorystatusreports/index', 'InventoryStatusReportsController@indexAction');
        Route::any('getInventoryStatusReports', 'InventoryStatusReportsController@getInventoryStatusReports');
        Route::any('inventoryReports/excel', 'InventoryStatusReportsController@excelInventoryReports');

    });
});
