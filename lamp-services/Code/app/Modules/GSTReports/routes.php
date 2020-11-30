<?php

Route::group(['middleware' => ['web']], function () {
    Route::group(['namespace' => 'App\Modules\GSTReports\Controllers'], function () {
        Route::any('/gstreports', 'GstReportsController@indexAction');
        Route::any('/gstreports/index', 'GstReportsController@indexAction');
        Route::any('/gstreports/inv', 'GstReportsController@invoceReport');
        Route::any('/gstreports/export', 'GstReportsController@invExportReport');
        Route::any('/gstreports/getoutwardreport', 'GstReportsController@getOutwardReportAction');
        Route::get('/gstreports/hsnwise', 'GstReportsController@hsnwiseAction');
        Route::any('/gstreports/hsnexport', 'GstReportsController@getHsnOutwardReportAction');
        Route::get('/gstreports/returnttrp','GstReportsController@getReturnTaxReport');
        Route::any('/gstreports/exporttrp','GstReportsController@getReturnTaxReportToExcel');
        Route::get('/gstreports/returnhsn','GstReportsController@getReturnHsnWiseReport');
        Route::any('/gstreports/returnhsnreport','GstReportsController@getReturnHsnwiseReportToExcel');
        Route::get('/gstreports/deliveredhsnrp','GstReportsController@getDeliveredHsnWiseReport');
        Route::any('/gstreports/deliveredhsnreport','GstReportsController@getDeliveredHsnWiseReportToExcel');



        Route::any('/report', 'GstReportsController@index');
        Route::any('financeReport', 'GstReportsController@exportFinanceReports');
        Route::any('getbu','GstReportsController@getBuUnit');

    });
});



