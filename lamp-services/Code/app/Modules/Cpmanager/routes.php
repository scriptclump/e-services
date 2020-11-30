<?php

Route::group(['middleware' => ['mobile']], function () {
    /* Cpmanager Routies */
    Route::group(['prefix' => 'cpmanager', 'namespace' => 'App\Modules\Cpmanager\Controllers'], function () {
        Route::post('/registration', 'RegistrationApiController@registration');
        Route::post('/address', 'RegistrationApiController@address');
        Route::post('/registrationController', 'RegistrationApiController@generate_Appid');
        Route::post('/getCategories', 'categoryController@getCategories');
        Route::post('/productDetails', 'categoryController@productDetails');
        Route::post('/profile', 'accountController@index');
        Route::post('/DisableContactuser', 'accountController@DisableContactuser');
        Route::post('/getfeaturedproducts', 'HomeController@getfeaturedproducts');
        Route::post('/getfeaturedproductsViewall', 'HomeController@getfeaturedproductsViewall');
        Route::post('/updateProfile', 'accountController@updateProfile');
        Route::post('/getShippingAddress', 'accountController@getShippingAddress');
        Route::post('/getMasterLookup', 'MasterLookupController@getMasterLookup');
        Route::post('/getSearchAjax', 'SearchController@getSearchAjax');
        Route::post('/getOfferProducts', 'categoryController@getOfferProducts');
        Route::post('/addcart', 'CartController@addcart');
        Route::post('/deletecart', 'CartController@deletecart');
        Route::post('/getcartdetails', 'CartController@Viewcart');
        Route::post('/editCart', 'CartController@editCart');
        Route::post('/cartcount', 'CartController@cartcount');
        Route::post('/CheckCartInventory', 'CartController@CheckCartInventory');
        Route::post('/addcart1', 'CartController@addcart1');
        Route::get('/flushpricecache/{productId}', 'CartController@flushPriceCache');
        Route::get('/flushpricecachebyorderId/{orderId}', 'CartController@flushPriceCacheByOrderId');
        Route::get('/flushcache/{key}', 'CartController@flushCache');
        Route::post('/addReviewRating', 'categoryController@addReviewRating');
        Route::post('/getFilters', 'FilterDataController@getFilters');
        Route::post('/tracking', 'TrackingController@index');
        Route::post('/createOrder', 'OrderController@addOrder');
        Route::post('/createOrder1', 'OrderController@addOrder1');
        Route::post('/getMyOrders', 'OrderController@GetMyOrders');
        Route::post('/Orderdetails', 'OrderController@Orderdetails');
        Route::post('/cancelOrder', 'OrderController@cancelOrder');
        Route::post('/returnOrder', 'OrderController@returnOrder');
        Route::get('/generateInvoice/{orderId}', 'OrderController@generateInvoice');
        Route::post('/saveAddress', 'accountController@saveAddress');
        Route::post('/editAddress', 'accountController@editAddress');
        Route::post('/getCountries', 'accountController@getStateCountries');
        Route::post('/getheaders', 'HomeController@getheaders');
        Route::post('/getBanner', 'HomeController@getBanner');
        Route::post('/updateOrderStatus', 'OrderController@updateOrderStatus');
        Route::post('/pincode', 'TrackingController@CheckPincode');
        Route::post('/getShoppingList', 'ShoppingListController@getShoppingList');
        Route::post('/productListOperations', 'ShoppingListController@productListOperations');
        Route::post('/search', 'SearchController@getSearch');
        Route::post('/sorting', 'HomeController@getSortingDataFilter');
        Route::post('/returnresons', 'OrderController@returnReasons');
        Route::post('/cancelresons', 'OrderController@cancelReasons');
        Route::post('/getCategories1', 'categoryController@getCategories1');
        Route::post('/retailers', 'RegistrationApiController@getAllCustomers');
        Route::post('/versioncheck', 'HomeController@getversion');
        Route::post('/retailertoken', 'RegistrationApiController@generateRetailerToken');
        Route::post('/getOfflineProducts', 'categoryController@getOfflineProducts');
        Route::post('/getProductSlabs', 'categoryController@getProductSlabs');
        Route::post('/getotp', 'RegistrationApiController@getOtp');
        Route::post('/getPincodeAreas', 'MasterLookupController@getPincodeAreas');
        Route::post('/updateRetailerData', 'RegistrationApiController@updateRetailerData');
        Route::post('/ffcomments', 'RegistrationApiController@InsertFfComments');
        Route::post('/dashboardreport', 'MasterLookupController@getDashboardReport');
        Route::post('/salestargetreport', 'MasterLookupController@getSalesTargetReport');
        Route::post('/getMediaDesc', 'categoryController@getMediaDesc');
        Route::post('/getReviewSpec', 'categoryController@getReviewSpec');
        Route::post('/getPincodeData', 'MasterLookupController@getPincodeData');




        /* SRM Routes */
        Route::post('/getInventory', 'SrmController@getInventory');
        Route::post('/getPolist', 'SrmpoOrderController@getPolist');
        Route::post('/getPolistByStatus', 'SrmpoOrderController@getPolistByStatus');
        Route::post('/getPodetails', 'SrmpoOrderController@getPodetails');
        Route::post('/createPO', 'SrmpoOrderController@createPO');
        Route::post('/updatePO', 'SrmpoOrderController@updatePO');
        Route::post('/editPO', 'SrmpoOrderController@editPO');
        Route::post('/getPoStatusList', 'SrmpoOrderController@getPoStatusList');
        Route::post('/getPurchaseSlabs', 'SrmController@getPurchaseSlabs');
        Route::post('/getPurchasepricehistory', 'SrmController@getPurchasepricehistory');
        Route::post('/getwarehouseslist', 'SrmController@getWarehouseList');
        Route::post('/getsuppplierlist', 'SrmController@getSupplierList');
        Route::post('/getSupplierProductLists', 'SrmController@getSupplierProductLists');
        Route::post('/getSrmProducts', 'SrmController@getSrmProducts');
        Route::post('/createSupplier', 'SrmController@createSupplier');
        Route::post('/getmanufacturerproducts', 'SrmController@getManufacturerProducts');
        Route::post('/getallmanfproducts', 'SrmController@getManufacturerSubscribeProducts');

        Route::post('/getmanufacturerlist', 'SrmController@getManufacturerList');
        Route::post('/getSupplierMasterlookupdata', 'SrmController@getSupplierMasterlookupdata');
        Route::post('/getPOMasterlookupdata', 'SrmpoOrderController@getPOMasterlookupdata');
        Route::post('/getSubscribeProducts', 'SrmController@getSubscribeProducts');
        /* Picklist Routes */
        Route::post('/getpicklistdetails', 'pickController@getpicklistdetails');
        Route::post('/getPickOrderList', 'pickController@getPickOrderList');
        Route::post('/getorderdetailbyinvoice', 'pickController@getorderdetailbyinvoice');
        Route::post('/getDeliverOrderList', 'pickController@getPickOrderList');
        Route::post('/getProductbyBarcode', 'pickController@getProductbyBarcode');
        Route::post('/getCollectiondetails', 'pickController@getCollectiondetails');
        Route::post('/getPaymentMethod', 'pickController@getPaymentMethod');
        Route::post('/savepicklist', 'pickController@SavePickList');
        Route::post('/getBagsbybarcode', 'pickController@getBagsbybarcode');
        Route::post('/getInvoiceByReturn', 'pickController@getInvoiceByReturn');
        Route::post('/holdreasons', 'pickController@holdReasons');
        Route::post('/getOrderHold', 'pickController@getOrderHold');
        Route::post('/getCollectiondetails', 'pickController@getCollectiondetails');

        Route::post('/saveContainerData', 'pickController@saveContainerData');
        Route::post('/saveTripsheetData', 'pickController@saveTripsheetData');
        Route::post('/getBeatsbyffID', 'HomeController@getBeatsbyffID');
        Route::post('/UnBilledskus', 'HomeController@UnBilledskus');
        Route::post('/getStatusCount', 'pickController@getStatusCount');
        Route::post('/remittanceHistory', 'pickController@remittanceHistory');
        Route::post('/pickercancelreason', 'MasterLookupController@getCancelReason');
        Route::post('/getcontainerbyorder', 'pickController@getcontainerbyorder');

        /// feedback
        Route::post('/getFeedbackReasons', 'FeedbackController@getFeedbackReasons');
        Route::post('/saveFeedbackReasons', 'FeedbackController@saveFeedbackReasons');

        Route::post('/locationupdate', 'LocationUpdateController@saveLocationData');
        Route::post('/getlocationdata', 'LocationUpdateController@getLocationData');
        Route::post('/getfieldforcelist', 'MasterLookupController@getFieldForceList');
        Route::post('/getsodashboard', 'FieldForceDashboardController@getSoDashboard');
        Route::post('/getsodashboardfilters', 'FieldForceDashboardController@getSoDashboardFilters');
        
        /* GRN Routes */
        Route::post('/creategrn', 'GrnController@createGrn');
        Route::post('/getopenpolist', 'GrnController@getOpenPoList');
        Route::post('/getpolistforgrn', 'GrnController@getPoList');
        Route::post('/getassignedpolist', 'GrnController@getAssignedPoList');
        Route::post('/getpickerlist', 'GrnController@getPickerList');
        Route::post('/assignpicker', 'GrnController@assignPickertoPO');
        Route::post('/assignpickertogrn', 'GrnController@assignPickertoGRN');
        Route::post('/getgrnlist', 'GrnController@getGRNList');
        
        Route::post('/getbeats', 'MasterLookupController@getFfBeat');
        Route::post('/getBeatsByPincode','MasterLookupController@getFFBeatByPincode');
        Route::post('/getinvoiceorderlist', 'pickController@getInvoiceOderlist'); 
        Route::post('/updategeo', 'pickController@UpdateGeo');  
        Route::post('/getdedetails', 'pickController@getDeDetails');
        
        /*Orders*/
        Route::post('/orderlist', 'OrderReportController@getOrdersList');
        Route::post('/praorderlist', 'OrderReportController@getPRAOrdersList');
        Route::post('/orderdetails', 'OrderReportController@getOrdersDetails');
        Route::any('/getstatulist','OrderReportController@getStatusList');
        Route::any('/ordercommenthistory','OrderReportController@commentHistoryAction');
        Route::post('/savetransit', 'AdminOrderController@saveTransitStatus');
        Route::post('/docketorderlist', 'AdminOrderController@getOrdersBasedOnDocket');

         /**
       * Routes related to assign orders
       */
      
		Route::post('/assignOrderToPicker', 'AssignOrderController@assignOrderToPickerAction');
		Route::post('/assignOrderToDelivery', 'AssignOrderController@assignOrderToDeliveryAction');
		Route::post('/recieveStockInHub', 'AssignOrderController@recieveStockInHubAction');
		Route::post('/stockTransfer', 'AssignOrderController@stockTransferAction');
		Route::post('/stockTransferHubtoDc', 'AssignOrderController@stockTransferHubToDcAction');
		Route::post('/recieveStockInDc', 'AssignOrderController@recieveStockInDcAction');
      
        Route::post('/sendemailtoff', 'CartController@sendEmailtoFF');
        Route::post('/updatebeat', 'CartController@updateBeat');
        Route::post('/getInventoryByProductlist', 'pickController@getInventoryByProductlist');
        Route::post('/assignproductpicker', 'pickController@assignPickerProduct');
        Route::post('/getApprovalOptions', 'pickController@getApprovalOptions');
        Route::post('/getstockassignedbypicker', 'pickController@getStockTakeByPicker');
        Route::post('/submitapprovestatus', 'pickController@submitApprovalStatus');
        Route::post('/getapprovalhistory', 'pickController@getApprovalHistory');
	   Route::post('/getPendingCollectionDate', 'AssignOrderController@getPendingCollectionDate');	
        Route::post('/printInvoiceDotMatrix', 'DotmatrixController@printInvoiceDotMatrix');
            Route::post('/getOrdersByHub', 'DotmatrixController@getOrdersByHub');
            Route::post('/getHubsList', 'DotmatrixController@getHubsList');
            Route::post('/getBeatsByHub', 'DotmatrixController@getBeatsByHub');
            Route::post('/updateInvoicePrintStatus', 'DotmatrixController@updateInvoicePrintStatus');
        Route::post('/getVehiclesByUserId', 'AssignOrderController@getVehiclesByUserId');
        Route::post('/printDeliveryChallanDotMatrix', 'DotmatrixController@printDeliveryChallan');

        /**
      * Routes related to payment collections
      */     
		Route::post('/getRemittanceDetails', 'PaymentCollectionsController@getRemittanceDetails');
		Route::post('/getRemittanceCollectionDetails', 'PaymentCollectionsController@getRemittanceCollectionDetails');
		Route::post('/submitApprovalStatus', 'PaymentCollectionsController@submitApprovalStatus');
		/* carete Container Master*/
		Route::post('/getbeatinfo', 'BeatDashboardController@getBeatInfo');
		Route::post('/updatecontainer', 'pickController@containermaster');
		Route::post('/checkcontainer', 'pickController@checkcontainer');
		Route::post('/getPaymentmodeByOrderId', 'PaymentCollectionsController@getPaymentmodeByOrderId');
		Route::post('/updatePaymentmodeByOrderId', 'PaymentCollectionsController@updatePaymentmodeByOrderId');
		 /* Beat dashboard links */
      Route::post('/getalldata', 'BeatDashboardController@getAllData');
      Route::post('/storespoke', 'BeatDashboardController@storeSpoke');
      Route::post('/storebeat', 'BeatDashboardController@storeBeat');
        /* Check In Validation */
        Route::post('/checkin', 'HomeController@checkInValidation');

      /* Beat dashboard links */
	
        Route::post('/savegeodata', 'AdminOrderController@saveGeoData');
        Route::post('/getgeodata', 'AdminOrderController@getGeoData');
		Route::post('/genarateOrderref', 'OrderController@genarateOrderref');
        Route::post('/generateotp', 'OrderController@generateOtpOrder');
        Route::post('/orderotpconfirm', 'OrderController@orderOtpConfirmation');
        Route::post('/getreturnproductreason', 'AdminOrderController@getReturnProductWithReason');
        Route::post('/getfilterorderstatus', 'OrderController@getFilterOrderStatus');
        Route::post('/updateprogressflag', 'AdminOrderController@updateProgressFlag');
        Route::post('/searchallproducts', 'AdminOrderController@searchAllProduct');

      /*cashback routes*/
      Route::post('/getOrderCashbackData', 'MasterLookupController@getOrderCashbackData');
      Route::post('/getOrderFreeQtyData', 'MasterLookupController@getOrderFreeQtyData');
      Route::post('/getCashbackHistory', 'MasterLookupController@getCashbackHistory');
      Route::post('/collectionpending', 'pickController@retailerCollectionPendingOrders');
      Route::post('/saveCustCollection', 'pickController@saveCustCollection');

      /*Checkers Count*/
      Route::post('/getCheckersCount', 'AssignOrderController@getCheckersCount');
     
      /*attendance route*/
      Route::post('/getHubUsers', 'AttendanceController@getHubUsers');
      Route::post('/saveAttendance', 'AttendanceController@saveAttendance');

      /*Vehicle Attendance route*/
      Route::post('/getvehicleidsbyuserid', 'AttendanceController@getVehicleIdsByUserId');
      Route::post('/savevehicleattendance', 'AttendanceController@saveVehicleAttendance');

      /* To create Temporary Vehicle*/
      Route::post('/savetemporaryvehicle', 'AttendanceController@saveTemporaryVehicle');
     
       /*reorder*/
      Route::any('/reorder', 'ReOrderController@reOrdering');

      /* Checker List*/
      Route::post('/getCheckersList', 'AssignOrderController@getCheckersList');

      /*Get assigned but not yet verified ORders List*/

      Route::post('/getpendingVerification','AssignOrderController@getPendingtVerificationList');

      /*get assigned orders list*/

      Route::post('/assignOrdersForChecker','AssignOrderController@assignOrdersForChecker');

      //getRtdOrdersList

      Route::post('/getRtdOrdersData','AssignOrderController@getRtdOrdersList');

      /*getAssignedVerificationList*/
      Route::post('/getAssignedVerificationList','AssignOrderController@getAssignedVerificationList');

      Route::post('authorizeuser','AssignOrderController@userAuthorization');
      
      // MFC Order Routes 
      Route::post('sendordernotify','pickController@sendOrderNotify');
      
      Route::post('updatemfcorderstatus','pickController@updateMFCOrderStatus');
      
      Route::post('checkmfcorderstatus','pickController@checkMFCOrderStatus');

      Route::post('getmfcdelivery','pickController@getMFCDeliveryData');
      
      Route::post('getcustomertype','MasterLookupController@getCustomerType');

      Route::any('/API/getdropdowndcs', 'RegistrationApiController@getAllDcByuserId');
      Route::any('/API/stockistdropdown','RegistrationApiController@getAllStockists');
      // calculate instant cashback
      Route::any('/calculateinstantcashback','pickController@calculateInstantCashback');
      Route::any('/getffPincodeList','MasterLookupController@getffPincodeList');
      Route::any('getpickerdeliveryDataByHub','OrderController@getPickerDeliveryData');
      Route::any('/getPincodeList','HomeController@getFFPincodeList');
      Route::any('/updateRetailerPincode','HomeController@updateRetailerPincode');

      Route::any('/mobileInvoicePDF/{invoiceID}', 'DotmatrixController@mobileInvoicePDF');
      Route::any('/bulkInvoicePDF/{id}', 'DotmatrixController@bulkInvoiceApi');

      Route::any('getffonmaps','OrderController@getffmaps');
      Route::any('/getbrandsmanufacturerproductGroup', 'RegistrationApiController@getBrandsManufacturerProductGroupByUser');

      Route::any('/timeslotsList','accountController@timeslotData');
      Route::any('/geticondata','MasterLookupController@iconData');

      // send otp to customer/ff
      Route::any('/senddeliveryotp','pickController@sendDeliveryOtp');
      Route::any('/verifydeliveryotp','pickController@verifyDeliveryOtp');


      //ff geo track

      Route::any('/getffgeodata','TrackingController@getFFGeoData');
      // check inv before order verifaction
      Route::any('/checkinventory','pickController@checkInventory');

      Route::post('/getmustskulist', 'categoryController@getMustSkuProductsList');
      Route::post('/searchfeatures','SearchController@getFeaturesBySearch');

      Route::post('/dcfcmanagerslist','MasterLookupController@getDCFCManagerList');
      Route::post('/getretailerinfo','accountController@getRetailerData');
      Route::post('/getffbyhub','MasterLookupController@getFFByHub');
      Route::post('/hrmsdashboard','HrmsController@hrmsDashboard');

      Route::post('/wikilinks','RegistrationApiController@wikiLinks');

      Route::any('/deleteretailer', 'RegistrationApiController@deleteretailerwithnoorder');

      Route::any('/syncdata','MasterLookupController@syncData');

      // get Free Qty
      Route::any('/checkfreeqty','pickController@checkFreeQty');
      //for getting custom pack data
      Route::post('/getcustompackdata','categoryController@getCustomPackData');

      //get pending cashback
        Route::get('/getpdngcbk/{user_id}','pickController@getPendingCashback');
      Route::post('/getsaleslist', 'OrderController@getSalesList');

      // Recomendation API's
      Route::post('/recommended-products', 'OrderController@recomendedCart');

    });
});
?>