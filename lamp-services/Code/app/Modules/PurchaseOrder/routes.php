<?php

Route::group(['middleware' => ['web']], function () {
    Route::group(['namespace' => 'App\Modules\PurchaseOrder\Controllers'], function () {
        Route::get('/po/index', 'PurchaseOrderController@indexAction');
        Route::get('/po/index/{status}', 'PurchaseOrderController@indexAction'); //  for sorting
        Route::get('/po/ajax/{status}', 'PurchaseOrderController@ajaxAction');
        Route::get('/po/details/{id}', 'PurchaseOrderController@detailsAction');
        Route::get('/po/poDetails/{id}', 'PurchaseOrderController@poDetailsAction');
        Route::get('/po/printpo/{id}/{type?}', 'PurchaseOrderController@printPoAction');
        Route::get('/po/download/{id}', 'PurchaseOrderController@downloadPOAction');
        Route::get('/po/excel/{orderId}','PurchaseOrderController@excelReports');

        Route::get('/po/create', 'PurchaseOrderController@createAction');
        Route::post('/po/create_order_to_po/{po_id}', 'PurchaseOrderController@placeNewOrder');
        Route::get('/po/edit/{id}', 'PurchaseOrderController@editAction');
        Route::any('/po/updatePo/', 'PurchaseOrderController@updatePOAction');
        Route::any('/po/splitpo/{po_id}', 'PurchaseOrderController@splitPOAction');
        Route::get('/po/getsuppliers', 'PurchaseOrderController@getSuppliersAction');
        Route::any('/po/savepo', 'PurchaseOrderController@savePurchaseOrderAction');
        Route::any('/po/uploadpodocs', 'PurchaseOrderController@uploadDocumentAction');
        Route::any('/po/deleteDoc/{doc_id}', 'PurchaseOrderController@deleteDoc');

        Route::get('/po/getSkus', 'PurchaseOrderController@getSkus');
        Route::post('/po/getProductInfo', 'PurchaseOrderController@getProductInfo');

        Route::get('/po/test', 'PurchaseOrderController@testAction');
        Route::get('/po', 'PurchaseOrderController@indexAction');
        Route::any('/po/getreason', 'PurchaseOrderController@getReasonAction');
        Route::any('/po/getWarehouseBySupplierId', 'PurchaseOrderController@getWarehouseBySupplierId');
        Route::any('/po/downloadPOExcel', 'PurchaseOrderController@downloadPOExcel');
        Route::any('/po/downloadPOReport', 'PurchaseOrderController@downloadPOReport');
        Route::any('/po/downloadPOHsnReport', 'PurchaseOrderController@downloadPOHsnReport');
        Route::any('/po/importPOExcel', 'PurchaseOrderController@importPOExcel');
        Route::any('/po/deletePoProduct', 'PurchaseOrderController@deletePoProduct');
        Route::any('/po/closePO', 'PurchaseOrderController@closePO');

        Route::get('/po/invoiceDetail/{invoiceId}', 'PurchaseInvoiceController@invoiceDetail');
        Route::get('/po/poInvoicePrint/{invoiceId}', 'PurchaseInvoiceController@poInvoicePrint');
        Route::get('/po/poInvoicePdf/{invoiceId}', 'PurchaseInvoiceController@poInvoicePdf');
        Route::get('/po/ajax/invoices/{id}', 'PurchaseInvoiceController@invoicesAjaxAction');
        Route::get('/po/createPOInvoice/{id}', 'PurchaseInvoiceController@createInvoiceByinwardId');
        Route::any('/po/approvalSubmit', 'PurchaseInvoiceController@approvalSubmit');

        //Route::get('/po/poInvoicePaymentVoucher/{id}', 'PurchaseInvoiceController@creatPaymentVoucher');

        Route::any('/po/addpayment', 'PaymentController@addPayment');
        Route::any('/po/addlegalpayment', 'PaymentController@addLegalPayment');
        Route::get('/po/ajax/payments/{id}/{module?}', 'PaymentController@paymentsAjaxAction');

        Route::any('/po/getApprlForm/{module}/{id}','PaymentController@getDataApprovalForm');
        Route::any('/po/getApprovalHistory/{module}/{id}','PaymentController@getApprovalHistory');
        Route::any('/payment/paymentdetails/{id}','PaymentController@vendorPaymentDetails');
        Route::any('/payment/paymentdetailsdata/{id}','PaymentController@vendorPaymentData');
        Route::post('/po/updatesupplier','PurchaseOrderController@updateSupplier');
        Route::post('/po/updatesuppydc','PurchaseOrderController@updateSupplyDC');
        // update po to so order code
        Route::post('/po/po_so_code_update','PurchaseOrderController@updatePoSoCode');
        Route::post('/po/updatestdc','PurchaseOrderController@updateStDC');


        Route::any('/po/downloadpoGSTReport', 'PurchaseOrderController@downloadpoGSTReport');
        // po to so by automatic
        Route::any('/po/potoso', 'PurchaseOrderController@createSoByPoData');

        // download excel file for PO Import
        Route::any('/po/downloadpoimport', 'PurchaseOrderController@downloadPOImportExcel');

        //New Product Details Email 
        Route::get('/productemail/{approval_unique_id}/{le_wh_id}', 'PurchaseInvoiceController@newproductemail');

        //PO Payment delete 
        Route::post('/po/deletePOPayment/{id}', 'PaymentController@deletePOPayment');
        Route::get('/po/poPaymentVoucher/{pay_code}', 'PaymentController@createPaymentVoucher');

        //txn_reff_code
        Route::post('/po/txn_list', 'PaymentController@getTxnsList');

        
    });
});
