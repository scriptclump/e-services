<?php

Route::group(['middleware' => ['mobile']], function () {
    Route::group(['namespace' => 'App\Modules\CrateManagement\Controllers'], function () {
        Route::any('/cratemanagement/gethubslist', 'CrateManagementController@getHubsList');
        Route::any('/cratemanagement/getordercratesbyhub', 'CrateManagementController@getOrderCratesByHub');
        Route::any('/cratemanagement/setcratestatus', 'CrateManagementController@setCrateStatus');
        Route::any('/cratemanagement/updatecrateloadingstatus', 'CrateManagementController@updateCrateLoadingStatus');
        Route::any('/cratemanagement/gettripsheetdetails', 'CrateManagementController@getTripSheetDetails');
        Route::any('/cratemanagement/exchangecrate', 'CrateManagementController@exchangeCrate');
        Route::any('/cratemanagement/missingcrateslist', 'CrateManagementController@missingCratesList');
        Route::any('/cratemanagement/rahdeliveryexcesslist', 'CrateManagementController@rahDeliveryExcessList');
        Route::any('/cratemanagement/setcrateexcess', 'CrateManagementController@setCrateExcess');
        Route::any('/cratemanagement/hubtodcdocketlist', 'CrateManagementController@hubToDcDocketList');
        Route::any('/cratemanagement/hubtodccratelist', 'CrateManagementController@hubToDcCrateList');
        //crate transfer
        Route::any('/cratemanagement/downloadCrate','CrateDashBoardController@downloadCrateTransfer');
        Route::any('/cratemanagement/uploadCrateTransferExcel','CrateDashBoardController@uploadCrateTransfer');
        
        // Dashboard routes
        Route::any('/cratemanagement/dashboard', 'CrateDashBoardController@indexAction');
        Route::any('/cratemanagement/getbytransactionstatus', 'CrateDashBoardController@getByTransactionStatus');
        Route::any('/cratemanagement/cratedetails', 'CrateDashBoardController@getCrateDetails');
        Route::any('/cratemanagement/statuscount', 'CrateDashBoardController@statusCount');
        Route::any('/cratemanagement/createCrate','CrateDashBoardController@createCrate');
        Route::any('/cratemanagement/crateeditdetails','CrateDashBoardController@crateEditDetails');
        Route::any('/cratemanagement/updatecreateCrate','CrateDashBoardController@updateCreateCrate');
        Route::any('/cratemanagement/downloadExcel','CrateDashBoardController@downloadExcel');
        Route::any('/cratemanagement/uploadCrateCodeExcel','CrateDashBoardController@uploadCrateCodeExcel');
    });
});
