<?php

Route::group(['middleware' => ['web']], function () {
    Route::group(['namespace' => 'App\Modules\VendorPayment\Controllers'], function () {
       
        Route::get('/vendor/payments', 'VendorPaymentController@index');
        Route::get('/vendor/payments/{status}', 'VendorPaymentController@index');
        Route::get('/vendor/purchaseOrder/{status}', 'VendorPaymentController@getPurchaseOrders');
        Route::get('/vendor/purchaseOrder/{status}/{from_date}/{to_date}', 'VendorPaymentController@getPurchaseOrders');
        Route::get('/vendor/purchaseOrder/{status}/{from_date}/{to_date}/{sup_id}', 'VendorPaymentController@getPurchaseOrders');
        Route::get('/vendor/purchaseOrder/', 'VendorPaymentController@getPurchaseOrders');
        Route::post('/vendor/raise-payment-request', 'VendorPaymentController@raisePaymentRequest');
        Route::any('/vendor/po-request-list/', 'VendorPaymentController@vendorPaymentRequestRaised');
        Route::any('/vendor/payment-request-export/', 'VendorPaymentController@exportExcel');
        Route::any('/vendor/complete-payment/', 'VendorPaymentController@completePayment');
        Route::any('/vendor/payment-request-complete-export/', 'VendorPaymentController@exportExcelComplete');
        Route::any('/vendor/payment-request-export-upwb/', 'VendorPaymentController@exportExcelProcess');


      	// Route::get('/vendor/payment-request-list', 'VendorPaymentController@vendorPaymentRequestRaised');
      	// Route::get('/vendor/get-payment-request-list/{status}', 'VendorPaymentController@getVendorPaymentRequestList');
      	Route::get('/vendor/payment-request-history/{module}/{id}', 'VendorPaymentController@raisedPaymentRequestHistory');    
        Route::any('/vendor/payment-request-update/{id}', 'VendorPaymentController@paymentRequestStatusUpdate');
        Route::any('/vendor/approvalSubmit', 'VendorPaymentController@approvalSubmit');
        Route::any('/vendor/popaymentstatusupdate', 'VendorPaymentController@poPaymentStatusUpdate');
        Route::any('/vendor/downloadCompleteReport', 'VendorPaymentController@downloadCompleteReport');

       
    });
});
