<?php

Route::group(['middleware' => ['mobile']], function () {
    Route::group(['namespace' => 'App\Modules\InventoryAudit\Controllers'], function () {
        Route::any('/inventoryaudit/getproductlist', 'InventoryAuditController@getProductList');
        Route::any('/inventoryaudit/savepickerassignment', 'InventoryAuditController@savePickerAssignment');
        Route::any('/inventoryaudit/getpickerassignment', 'InventoryAuditController@getPickerAssignment');
        Route::any('/inventoryaudit/updateaudit', 'InventoryAuditController@updateAudit');

        Route::any('/inventoryaudit/getavailablelocations', 'InventoryAuditController@getAvailableLocations');

        Route::any('/inventoryaudit/getavailablesoh', 'InventoryAuditController@getAvailableSOH');

        Route::any('/inventoryaudit/getpickerassigmentwithsoh', 'InventoryAuditController@getpickerassigmentwithSOH');

        Route::any('/inventoryaudit/saveallinventorydetailswithmobileapi', 'InventoryAuditController@saveallSOHandDITwithMobileApi');

        Route::any('/inventoryaudit/getproductlistcyclecount', 'InventoryAuditController@getProductListForCycle');

        Route::any('/inventoryaudit/getcategorylistcyclecount', 'InventoryAuditController@getCategoryListForCycle');
        

    });
});
