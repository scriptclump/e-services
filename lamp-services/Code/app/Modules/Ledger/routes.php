<?php

Route::group(['middleware' => ['web']], function () {

    Route::group(['namespace' => 'App\Modules\Ledger\Controllers'], function () {
        
    	Route::group(['before' => 'authenticates'], function() {    

            Route::any('/paymentsApproval','LedgerController@indexAction');
            Route::any('/payments/getPaymentsByDelExec/{delivery_exec}/{delivery_fdate}/{delivery_tdate}/{status}','LedgerController@getPaymentsByDelExec');
            Route::any('/payments/approvePayments','LedgerController@approvePayments');
            Route::any('/payments/getRemittanceDetails','LedgerController@getRemittanceDetails');
            Route::any('/remittance/getApprlForm','LedgerController@getDataApprovalForm');
            Route::any('/payments/approvalSubmit','LedgerController@approvalSubmit');
            Route::any('/payments/getApprovalHistory/{module}/{id}','LedgerController@getApprovalHistory');
            Route::any('/payments/paymentReport','LedgerController@paymentReport');
            
            Route::any('/payments/collectiondetail','LedgerController@collectionDetail');
            Route::any('/payments/getCollectionsByDelExec/{delivery_exec}/{delivery_fdate}/{delivery_tdate}','LedgerController@getCollectionsByDelExec');
            Route::any('/payments/collectionReport','LedgerController@collectionReport');
            Route::any('/payments/insertextrareceipt','LedgerController@insertExtraReceiptVocuhers');
            Route::any('/locreport','LedgerController@getLOCReport');
        
            Route::any('/payments/receiptvoucher/{startdate}/{enddate}/{table}','LedgerController@insertReceiptVouchers');

            Route::get('/getBrandDetails','LedgerController@getBrandDetails');
            Route::any('getBrandDetails/download','LedgerController@getBrandDetailsDownload');
            Route::any('getInventoryData/download','LedgerController@getInventoryData');

            Route::get('/supplierMapping','LedgerController@getSupplierMapping');
            Route::post('/dataInsert','LedgerController@getSupplierMappingData'); 
            Route::get('/suppliersGridData','LedgerController@getSuppliersGridData');
            Route::get('editGridDetails/{id}','LedgerController@getUpdateDetails');
            Route::post('updateSuppliersMapping','LedgerController@updateSuppliersMapping');
            Route::any('deleteSupplierID/{id}','LedgerController@deleteSupplierDetails');
            Route::any('checkbrandmanufbysupplierid/{sid}','LedgerController@getBrandmanufBySupplierId');
            Route::any('brandsForManufacture','LedgerController@getBrandsForManufacture');
            Route::any('brandsForSupplier','LedgerController@getBrandsForSupplier');
            Route::any('getBrandsForManufacture','LedgerController@brandsForManufactureController');
                        
        });
	
	});
});
