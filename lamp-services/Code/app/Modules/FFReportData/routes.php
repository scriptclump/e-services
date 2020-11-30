<?php

Route::group(['middleware' => ['web']], function () {
    Route::group(['namespace' => 'App\Modules\FFReportData\Controllers'], function () {
        Route::any('ffreportsdata', 'FFReportDataController@indexAction');
        Route::any('ffreportsdata/getreports', 'FFReportDataController@getReports');
        Route::any('ffreportsdata/excelSalesReports', 'FFReportDataController@excelSalesReports');
        Route::any('ffreportsdata/getffnames', 'FFReportDataController@getFFNames');
        Route::any('getbu','FFReportDataController@odersTabGetBuUnit');
        Route::any('/salesorders/setbuid','FFReportDataController@setBuidInSession');

    });
});



 