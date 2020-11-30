<?php
Route::group(['middleware' => ['web']], function () {
    Route::group(['namespace' => 'App\Modules\Retailer\Controllers'], function () {
        Route::get('retailers/index', 'RetailerController@retailersList');
        Route::get('retailers/add', 'RetailerController@index');
        Route::post('retailers/update', 'RetailerController@update');
        Route::any('retailers/show', 'RetailerController@show');
        Route::any('retailers/edit/{retId}', 'RetailerController@editAction');
        Route::any('retailers/approve/{retId}', 'RetailerController@approveAction');
        Route::post('retailers/approvalsubmit', 'RetailerController@approvalSubmitAction');
        Route::get('retailers/delete', 'RetailerController@destroy');
        Route::get('retailers/blockusers', 'RetailerController@blockUsers');
        Route::get('retailers/getRetailers', 'RetailerController@getRetailers');
        Route::get('retailers/getUsersList', 'RetailerController@getUsersList');
        Route::get('retailers/getDocumentList/{legalEntityId}', 'RetailerController@getDocumentList');
        Route::post('retailers/getBeatDataPincode', 'RetailerController@getBeatDataPincode');
        Route::post('retailers/getAreaList', 'RetailerController@getAreaList');
        Route::post('retailers/getSpokesList', 'RetailerController@getSpokesList');
        Route::post('retailers/getBeatList', 'RetailerController@getBeatList');
        Route::get('retailers/getServicableList/{pincode}', 'RetailerController@getServicableList');
        Route::get('retailers/getOrderList/{legalEntityId}', 'RetailerController@getOrdersList');
        Route::get('retailers/getCollectionDetails/{legalEntityId}', 'RetailerController@getCollectionDetails');
        Route::get('retailers/exportCustomers', 'RetailerController@exportCustomers');
        Route::post('retailers/importRetailers', 'RetailerController@importRetailers');
        Route::post('retailers/sendsms', 'RetailerController@sendSms');
        Route::any('retailers/dashboardcustomers', 'RetailerController@dashboardCustomers');
        Route::any('retailers/editusers/{userid}', 'RetailerController@editUser');
        Route::post('retailers/updateuser', 'RetailerController@updateUser');
		Route::any('retailers/selforders', 'RetailerController@getSelfOrdersPlaced');
        Route::any('retailers/creditdetails/{Id}', 'RetailerController@creditDetails');
        Route::any('retailers/updateCreditLimit', 'RetailerController@updateCreditLimit');
        Route::any('retailers/getBeats', 'RetailerController@getBeatsforLeId');

        Route::any('retailers/ecashupdate','RetailerController@updateecash');
        //adding cashback
        Route::any('retailers/editorders/{orderId}', 'RetailerController@editOrder');
        Route::any('retailers/addcashback', 'RetailerController@addcashback');
        Route::any('retailers/getcashback', 'RetailerController@getcashback');

        Route::get('retailers/Lender/partner','RetailerController@mfcGridDetails');

        Route::post('retailers/mfcMapping','RetailerController@mappingMfcDetails');

        Route::any('retailers/editMfc/{id}','RetailerController@editDetails');

        Route::post('retailers/updateUser','RetailerController@updateDatailsSave');

        Route::any('retailers/mfc_bussiness_names','RetailerController@mfcBussinessNamesDropDown');

        Route::any('retailers/uploadCreditlimit','RetailerController@uploadCreditlimit');

         Route::any('creditLimitDonwload','RetailerController@creditLimitDonwload');


       Route::any('retailers/downloadCreditLimitTemplate', 'RetailerController@downloadCreditLimitTemplate');
       Route::any('retailers/importTemplate','RetailerController@downloadImportTemplate');

       Route::any('retailers/validateaadharno','RetailerController@validateaadharno');
       Route::any('retailers/validatefssai/{legal_entity_id}','RetailerController@validatefssai');

       Route::get('retailers/getfeedbackhistory/{legalEntityId}', 'RetailerController@getfeedback');
       Route::any('/retailers/feedbackview/{id}', 'RetailerController@viewfeedback');
       Route::any('retailers/addfeedback', 'RetailerController@addfeedback');
       Route::any('/retailers/delete/{id}','RetailerController@deletefeedback');
       Route::any('retailers/groupspecific/{id}','RetailerController@groupspecific');

       Route::any('/retailers/uploadDoc','RetailerController@uploadDoc');  
       Route::any('/retailers/deleteDoc','RetailerController@deleteDoc');

      // Validating State codes for GST Number
      Route::post('retailers/checkgstin','RetailerController@checkGstStateCode');
    });
});