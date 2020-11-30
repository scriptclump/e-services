<?php

Route::group(['middleware' => ['web']], function () {
    Route::group(['namespace' => 'App\Modules\Dashboard\Controllers'], function () {
        Route::get('/', 'DashboardController@indexAction');
        Route::post('/', 'DashboardController@getIndexData');
        Route::get('/warehouse/{wh_id}', 'DashboardController@indexAction');
        Route::get('/mfc', 'MFCDashboardController@indexAction');
        Route::post('/mfc', 'MFCDashboardController@getIndexData');

        // Below Tabs Routes
        Route::any('/selforders', 'DashboardController@getSelfOrdersPlaced');
        Route::any('/getnewcustomers', 'DashboardController@getNewCustomersDashboard');
        Route::any('/businessdashboard', 'DashboardController@busiDashAction');
        Route::any('/graphaction', 'DashboardController@graphAction');
        Route::any('/gettodayffuserslist', 'DashboardController@getTodayFFUsersList');
        Route::any('/getIndexData', 'DashboardController@getIndexData');
        Route::any('/deliverydashboard', 'DashboardController@getDeliveryDashboard');
        Route::any('/pickersdashboard', 'DashboardController@getPickersDashboard');
        Route::any('/verificationdashboard', 'DashboardController@getVerificationDashboard');
        Route::any('/shrinkagedashboard', 'DashboardController@getShrinkageDashboard');
        Route::any('/collectionsdashboard', 'DashboardController@getCollectionsDashboard');
        Route::any('/vehiclesdashboard', 'DashboardController@getVehiclesDashboard');
        Route::any('/logisticsdashboard', 'DashboardController@getLogisticsDashboard');
        Route::any('/inventorydashboard', 'DashboardController@getInventoryDashboard');

        //Sales target routes start from here

        Route::get('/salestarget', 'FFTargetController@indexAction');
        Route::post('/salestarget', 'FFTargetController@getIndexData');
        Route::post('/getsalestarget', 'FFTargetController@getSalesTarget');
        Route::get('/salestarget/{wh_id}', 'FFTargetController@indexAction');
        Route::any('/getbrands', 'DashboardController@getBrandsByManufacturerId');
        Route::any('/getproductgroupbybrand', 'DashboardController@getProductGroupByBrandId');

        Route::get('/dprsheet', 'DPRSheetController@indexAction');
        Route::get('/downloaddpr', 'DPRSheetController@excelDprReports');
        /*Route for getting the DC or Fc data*/
        Route::post('/dcfclist', 'DPRSheetController@dcFcList');
 

    });
});
