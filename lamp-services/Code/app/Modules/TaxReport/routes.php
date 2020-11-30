<?php

Route::group(['middleware' => ['web']], function () {
    Route::group(['prefix' => 'taxreport', 'namespace' => 'App\Modules\TaxReport\Controllers'], function () {
        Route::any('/', 'TaxReportController@index');
        Route::any('/index', 'TaxReportController@index');
        Route::any('/inward', 'TaxReportController@inwardDashboard');
        Route::any('/outward', 'TaxReportController@outwardDashboard');
        Route::any('/excelexport', 'TaxReportController@taxReportExcelExport');
        //FF Credit Reports for Slab and Credit
        Route::any('/ffreport', 'FfCreditReportController@getFFCreditReport');
        Route::any('/downloadCreditReport', 'FfCreditReportController@downloadFFCreditReport');
        //FF Payment  Received Details Report
        Route::any('/paymentreceived', 'PaymentReceivedReportController@getPaymentReport');
        Route::any('/downloadPaymentReceivedReport', 'PaymentReceivedReportController@downloadPaymentReceivedReport');
        Route::any('/getpaymentdetails','PaymentReceivedReportController@getPaymentDetails');

        Route::any('/batchreport', 'FfCreditReportController@getBacthPrcsReport');
        Route::any('/downloadbatchreport', 'FfCreditReportController@downloadBatchReport');
    });
});
