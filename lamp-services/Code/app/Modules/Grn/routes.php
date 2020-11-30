<?php

Route::group(['middleware' => ['web']], function () {
    Route::group(['namespace' => 'App\Modules\Grn\Controllers'], function () {
    Route::get('/grn/index/{status?}','GrnController@indexAction');
    Route::get('/grn','GrnController@indexAction');

    Route::get('/grn/edit/{grnId}','GrnController@editAction');
    Route::get('/grn/details/{grnId}','GrnController@detailsAction');
    Route::get('/grn/pdf/{grnId}','GrnController@pdfAction');
    Route::get('/grn/create/{po_id?}','GrnController@createAction');
    Route::post('/grn/updateGrn','GrnController@updateAction');

    Route::post('/grn/getWarehouses','GrnController@supplierWarehouseOptions');
    Route::post('/grn/getProductInfo','GrnController@getProductInfoBySku');
    Route::any('/grn/getProductPackInfo','GrnController@getProductPackInfo');
    Route::post('/grn/addGrnSkuText','GrnController@addGrnSkuText');
    Route::post('/grn/getPackText','GrnController@getPackText');
    Route::post('/grn/createPackInputText','GrnController@createPackInputText');
    Route::post('/grn/createGrn','GrnController@storeGrnData');

    Route::any('/grn/getgrn/{status}','GrnController@getGrnAction');
    Route::get('/grn/getSkus','GrnController@getSkus');
    Route::get('/grn/getsuppliers','GrnController@getSuppliers');
    Route::any('/grn/createDisput','GrnController@createDisputAction');
    Route::any('/grn/getDisput/{inwardId}','GrnController@getDisputAction');
    Route::any('/grn/uploadDoc','GrnController@uploadDocumentAction');
    Route::get('/grn/download','GrnController@downloadAction');
    Route::get('/grn/print/{inwardId}','GrnController@printGrn');
    Route::any('/grn/delete','GrnController@deleteAction');
    Route::any('/grn/purchaseVoucher/{inwardid}','GrnController@creatPurchaseVoucher');
    Route::any('/grn/purchaseReturnVoucher/{prid}','GrnController@createPurchaseReturnVoucher');
	Route::any('/grn/purchaseVoucherOld/{inwardid}','GrnController@creatPurchaseVoucherOld');
    Route::any('/grn/generatevouchers/{date}','GrnController@generateVouchers');
    Route::any('/grn/getpoapprovalhistory/{po_id}','GrnController@getPoApprovalHistory');
    
    Route::any('/grn/exportGRN','GrnController@exportGRN');
    Route::any('/grn/savereferenceno','GrnController@SaveReferenceNo');
    
    //Route::any('/return/ajax/returns/{inward_id}','PurchaseReturnController@returnAjaxAction');
    Route::any('/return/createreturn/{inward_id}','GrnController@detailsAction');
    //Route::any('/return/returndetails/{inward_id}/{return_id}','GrnController@detailsAction');
    //Route::any('/return/getReturnDetails/{return_id}','PurchaseReturnController@ajaxDetailsAction');
    Route::any('/return/saveReturn','PurchaseReturnController@saveReturn');
    //Route::any('/return/approvalSubmit','PurchaseReturnController@approvalSubmit');
    //Route::any('/return/getReturnHistory/{return_id}','PurchaseReturnController@getReturnHistory');
    Route::get('/grn/purchaseVoucherByDate/{from_date}/{todate}','GrnController@creatPurchaseVoucherByDate');
    Route::any('/return/purchasereturnVoucher/{prid}','GrnController@createPurchaseReturnVoucher');
    Route::any('/grn/deliverorderongrn/{order_id}/{po_id}','GrnController@deliverPOSOOrder');
    Route::any('/grn/createchildpo/{inward_id}/{po_id}/{supplier_id}/{le_wh_id}','GrnController@createPoByData');
    Route::any('/grn/createSubPoWithMissingItems/{po_id}/{user_id}','GrnController@createSubPoWithMissingItems');
    Route::any('/grn/autoepenale/{grnid}','GrnController@enableCp');
    Route::any('/grn/getInvoiceDate','GrnController@getInvoiceDate');

	});
});
