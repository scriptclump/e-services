<?php

Route::group(['middleware' => ['web']], function () {
    Route::group(['namespace' => 'App\Modules\SellerWarehouses\Controllers'], function () {
        Route::group(['before' => 'authenticates'], function() {
            Route::get('warehouse', 'SellerWarehouseController@index');
            Route::get('warehouse/getLpLists', 'SellerWarehouseController@getLpLists');
            Route::get('warehouse/addCustom', 'SellerWarehouseController@addCustom');
            Route::get('warehouse/getlegalentitys', 'SellerWarehouseController@getLegalentitys');
            Route::get('warehouse/logisticspartners', 'SellerWarehouseController@getLogisticsPartners');
            Route::get('warehouse/lpwarehoues', 'SellerWarehouseController@getLpWarehoues');
            Route::get('warehouse/delete/{lp_id}', 'SellerWarehouseController@deleteLpWharehouses');
            Route::get('warehouse/create', 'SellerWarehouseController@logisticPartners');
            Route::get('warehouse/getLpWarehouses/{id}', 'SellerWarehouseController@getWarehouses');
            Route::post('warehouse/saveWarehouse', 'SellerWarehouseController@saveWarehouse');
            Route::get('warehouse/editCustom/{id}', 'SellerWarehouseController@editCustom');
            Route::get('warehouse/editWarehouse/{id}', 'SellerWarehouseController@editWarehouse');
            Route::post('warehouse/updateCustomWarehouse/{id}', 'SellerWarehouseController@updateCustomWarehouse');
            Route::post('warehouse/updateWarehouse/{id}', 'SellerWarehouseController@updateWarehouse');
            Route::post('warehouse/saveCustomWarehouse', 'SellerWarehouseController@saveCustomWarehouse');
            Route::post('warehouse/savePinLocations', 'SellerWarehouseController@savePinLocations');
            Route::post('warehouse/saveDocs', 'SellerWarehouseController@saveDocs');
            Route::get('warehouse/getPinLocations/{id}', 'SellerWarehouseController@getPinLocations');
            Route::any('warehouse/exportPin/{id}/{smpl}', 'SellerWarehouseController@exportPin');
            Route::get('warehouse/deletePin/{id}', 'SellerWarehouseController@deletePin');
            Route::post('warehouse/importExcel', 'SellerWarehouseController@importExcel');
            Route::any('warehouse/downloadPinSample', 'SellerWarehouseController@downloadPinSample');
            Route::post('warehouse/importPinSample', 'SellerWarehouseController@importPinSample');
            Route::post('warehouse/updateDocs', 'SellerWarehouseController@updateDocs');
            Route::post('warehouse/checkUnique', 'SellerWarehouseController@checkUnique');
            Route::get('warehouse/getSavedPincodes/{wh_id}/{id}', 'SellerWarehouseController@getSavedPincodes');
            Route::get('warehouse/getPincodeAreas/{pincode}/{beatId}', 'SellerWarehouseController@getPincodeAreas');
            Route::post('warehouse/savePJP/{le_wh_id}/{legal_id}', 'SellerWarehouseController@savePJP');
            Route::get('warehouse/getPJPs', 'SellerWarehouseController@getPJPs');
            Route::get('warehouse/deletePJP/{id}', 'SellerWarehouseController@deletePJP');
            Route::get('warehouse/deletePJPArea/{id}', 'SellerWarehouseController@deletePJPArea');
            Route::post('warehouse/checkUniquePJP', 'SellerWarehouseController@checkUniquePJP');
            Route::post('warehouse/checkUniquePJPArea', 'SellerWarehouseController@checkUniquePJPArea');
            Route::get('warehouse/editPJP/{pjp_pincode_area_id}', 'SellerWarehouseController@editPJP');
            Route::get('warehouse/addPJPArea/{pjp_pincode_area_id}', 'SellerWarehouseController@addPJPArea');
            Route::post('warehouse/savePJPArea', 'SellerWarehouseController@savePJPArea');
            Route::post('warehouse/updatePJP', 'SellerWarehouseController@updatePJP');
            Route::get('warehouse/getChildPJPs', 'SellerWarehouseController@getChildPJPs');
            Route::get('warehouse/getChildPJPAreas', 'SellerWarehouseController@getChildPJPAreas');
            Route::post('warehouse/mapArea', 'SellerWarehouseController@mapArea');
            Route::get('warehouse/checkhubpins/{data}', 'SellerWarehouseController@checkHubPins');
            Route::get('bussinessunits', 'SellerWarehouseController@bussinessUnitsData');
            Route::post('warehouse/addspoke', 'SellerWarehouseController@addSpoke');
            Route::post('warehouse/movespoke', 'SellerWarehouseController@moveSpoke');
            Route::post('warehouse/checkUniqueSpoke', 'SellerWarehouseController@checkUniqueSpoke');
            Route::post('warehouse/updatespoke', 'SellerWarehouseController@updateSpoke');
            Route::get('warehouse/getspokes/{le_wh_id}', 'SellerWarehouseController@getSpokes');
            Route::get('warehouse/getallspokes', 'SellerWarehouseController@getAllSpokes');
            Route::get('warehouse/editspoke/{spoke_id}', 'SellerWarehouseController@editSpoke');
            Route::get('warehouse/exportspokes/{hub_id}', 'SellerWarehouseController@exportSpokes');
            Route::get('warehouse/getallspokesbeats/{le_wh_id}', 'SellerWarehouseController@getAllSpokesBeats');
            Route::get('warehouse/getallspokesbeats', 'SellerWarehouseController@getAllSpokesBeats');
            Route::post('warehouse/hubvalidation/{id}','SellerWarehouseController@gethubValidate');
            Route::post('warehouse/fcvalidation/{fc}','SellerWarehouseController@getFcValidate');

            Route::get('warehouse/gstaddress', 'SellerWarehouseController@gstAddress');
            Route::get('warehouse/gstaddresslist', 'SellerWarehouseController@listGstAddress');
            Route::get('warehouse/addGst', 'SellerWarehouseController@addGst');
            Route::post('warehouse/saveGStAddress', 'SellerWarehouseController@saveGStAddress');
            Route::post('warehouse/checkstate', 'SellerWarehouseController@checkState');
            Route::get('warehouse/editGstAddress/{id}', 'SellerWarehouseController@editGstAddress');
            Route::get('warehouse/deletegst/{billing_id}', 'SellerWarehouseController@deleteGstAddress');
            Route::post('warehouse/updateGstAddress/{id}', 'SellerWarehouseController@updateGstAddress');
            Route::post('warehouse/checkgstin', 'SellerWarehouseController@checkGstin');


        });
    });
});
?>