<?php
Route::group(['middleware' => ['web']], function () {
   Route::group(['prefix' => '/kpi/', 'namespace' => 'App\Modules\KPIReports\Controllers'], function () { 

    // routes for product report
     Route::any('/productSummary', 'ProductAnalysisController@productSummary');
     Route::any('/productAnalysis', 'ProductAnalysisController@productAnalysis');

    // routes for supplier report
     Route::any('/supplieranalysis', 'supplierAnalysisController@supplierAnalysis');
     Route::any('/getSuppliers', 'supplierAnalysisController@getSuppliersData');        

    // routes for customer report
    Route::any('/customeranalysis', 'customerAnalysisController@customerAnalysis');
    Route::any('/getcustomers', 'customerAnalysisController@getCustomersData');

    //routes for expenses report
    Route::any('/expensesDetails','ExpensesDetailsController@expensesDetails');
    Route::any('/expensesData','ExpensesDetailsController@expensesData');
    
      // routes for nct analysis
    Route::any('/nctAnalysis', 'nctAnalysisController@nctAnalysis');
    Route::any('/getnct', 'nctAnalysisController@getnctData');
   

    //routes for inventory report
    Route::any('/InventoryDetails','InventoryAnalysisController@inventoryData');
    Route::any('/inventoryData','InventoryAnalysisController@inventoryDetails');        

    //routes for ELP Trends report
    Route::any('/elptrends','purchaseAnalysisController@purchaseAnalysis');
    Route::any('/purchasedata','purchaseAnalysisController@getpurchaseReturnsData');
    Route::any('/elpdownload','purchaseAnalysisController@getelpExcel');

    //routes for inventory report
    Route::any('/esptrends','espTrendsController@espAnalysis');
    Route::any('/espdata','espTrendsController@getespTrends');
    Route::any('/espdownload','espTrendsController@getespExcel');

    Route::any('/newcustomersdata','ProductAnalysisController@getNewCustomers');
    Route::any('/salestrends','ProductAnalysisController@getSalesTrendsData');
    
    Route::any('/newsalesdata','ProductAnalysisController@getTodayFFUsersList');
    Route::any('/salesbyperiod','ProductAnalysisController@getFFDataByPeriod');
    Route::any('/collection','commonController@getCollectionData');
    Route::any('/productSummaryexport','ProductAnalysisController@productExport');
    });
});
