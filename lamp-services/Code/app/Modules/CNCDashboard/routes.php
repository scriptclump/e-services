<?php

Route::group(['middleware' => ['web']], function () {
    Route::group(['prefix' => 'cnc', 'namespace' => 'App\Modules\CNCDashboard\Controllers'], function () {
        Route::get('/', 'CNCDashboardController@index');
        Route::post('/', 'CNCDashboardController@getIndexData');

        // Below Tab Routes
        Route::any('/selforders', 'CNCDashboardController@getSelfOrdersPlaced');
        Route::any('/gettodayffuserslist', 'CNCDashboardController@getTodayFFUsersList');
        Route::any('/getnewcustomers', 'CNCDashboardController@getNewCustomersDashboard');
        Route::any('/getIndexData', 'CNCDashboardController@getIndexData');
        Route::any('/deliverydashboard', 'CNCDashboardController@getDeliveryDashboard');
        Route::any('/pickersdashboard', 'CNCDashboardController@getPickersDashboard');
        Route::any('/verificationdashboard', 'CNCDashboardController@getVerificationDashboard');
        Route::any('/shrinkagedashboard', 'CNCDashboardController@getShrinkageDashboard');
        Route::any('/collectionsdashboard', 'CNCDashboardController@getCollectionsDashboard');
        Route::any('/vehiclesdashboard', 'CNCDashboardController@getVehiclesDashboard');
        Route::any('/logisticsdashboard', 'CNCDashboardController@getLogisticsDashboard');
        Route::any('/inventorydashboard', 'CNCDashboardController@getInventoryDashboard');
    });
});
