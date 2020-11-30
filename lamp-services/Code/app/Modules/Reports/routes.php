<?php

Route::group(['middleware' => ['web']], function () {
    Route::group(['namespace' => 'App\Modules\Reports\Controllers'], function () {
        Route::any('ffreports', 'ReportsController@indexAction');
        Route::any('ffreports/getreports', 'ReportsController@getReports');
        Route::any('ffreports/excelSalesReports', 'ReportsController@excelSalesReports');
        Route::any('ffreports/getffnames', 'ReportsController@getFFNames');
        //business units for ff reports 
        Route::any('getbu','ReportsController@odersTabGetBuUnit');
        
        /*Cash bag report grid*/
        Route::any('commissions', 'CashbagController@cashbagReport');
        Route::any('cashbagUsersList', 'CashbagController@usersInfo');
        Route::any('cashbagRolesList', 'CashbagController@getRoles');
        Route::any('cashbagGrid', 'CashbagController@cashBagData');
        Route::any('cashbagGridHeadings', 'CashbagController@getProcedureHeadings');
         Route::any('getRoleId/{id}', 'CashbagController@getRoleId');

        /* attendance Report for ff */
        Route::get('attendance','ReportsController@index');
        Route::post('getAttendance','ReportsController@getAttendance');
        /*Report for Average stock value in DC/fc*/        
        Route::any('avgStock','ReportsController@indexAvgStock');
        Route::any('getavgStock','ReportsController@getAvgStock');
        Route::any('getSalesReport','ReportsController@getSalesReport');

        Route::any('getSalesConsolidateReport','ReportsController@getSalesConsolidateReport');
        Route::any('getinvoicereport','ReportsController@getInvoiceReport');

        //profitablityPointsReport
        Route::any('profitablityPointsReport','ReportsController@getProfitablityPointsReport');

         //Payment Ledger Report
        Route::any('getledgerreport','ReportsController@getLedgerReport');
        Route::any('getledgerpaymentreport','ReportsController@getLedgerPaymentReport');

        //TBV Reports
        Route::any('tbvreports', 'ReportsController@tbvReports');
        Route::any('gettbvreport', 'ReportsController@getTbvReport');
    });

    Route::group(['prefix' => 'powerbi', 'namespace' => 'App\Modules\Reports\Controllers'], function () {
        Route::get('/{id}', 'powerBIController@getPowerBIUrls');
        // Route::get('getUrlByFeatureCode','powerBIController@getFeatureCodeURl');
      });
});
