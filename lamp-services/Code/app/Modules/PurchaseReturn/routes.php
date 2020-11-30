<?php
   
Route::group(['middleware' => ['web']], function () {
    Route::group(['namespace' => 'App\Modules\PurchaseReturn\Controllers'], function () {
    Route::get('/pr/index/{status?}','PurchaseReturnController@indexAction');
    Route::get('/pr/ajax/{status?}/{inward_id?}','PurchaseReturnController@ajaxAction');
    Route::get('/pr/details/{id}','PurchaseReturnController@detailsAction');
    Route::get('/pr','PurchaseReturnController@indexAction');
    Route::get('/pr/printpr/{id}','PurchaseReturnController@printPrAction');
    Route::get('/pr/downloadpr/{id}','PurchaseReturnController@downloadPRAction');
     Route::get('/pr/excel/{id}','PurchaseReturnController@excelAction');

    Route::get('/pr/create','PurchaseReturnController@createAction');
    Route::get('/pr/edit/{id}', 'PurchaseReturnController@editAction');
    Route::any('/pr/updatePr/', 'PurchaseReturnController@updatePRAction');
    Route::get('/pr/getsuppliers','PurchaseReturnController@getSuppliers');
    Route::post('/pr/savepr','PurchaseReturnController@savePurchaseReturnAction');
    Route::any('/pr/getWarehouseBySupplierId', 'PurchaseReturnController@getWarehouseBySupplierId');
    Route::post('/pr/getProductInfo', 'PurchaseReturnController@getProductInfo');
    Route::post('/pr/savepicklist', 'PurchaseReturnController@savePickerDetails');
    Route::any('/pr/printPicklist','PurchaseReturnController@printPicklist');
    Route::any('/return/approvalSubmit','PurchaseReturnController@approvalSubmit');
    Route::any('/pr/downloadPRExcel', 'PurchaseReturnController@downloadPRExcel');
    Route::any('/pr/checksrinvno', 'PurchaseReturnController@checkSrInvoice');
    Route::any('/pr/salesreturnbyprid/{pr_id}/{gds_order_id}/{le_wh_id}', 'PurchaseReturnController@salesReturnByPrId');
    // route for import PR
    Route::any('/pr/importPRExcel', 'PurchaseReturnController@importPRExcel');
    // download excel file for PR Import
    Route::any('/pr/downloadprimport', 'PurchaseReturnController@downloadPRImportExcel');
    //upload document
    Route::any('/pr/uploadpodocs', 'PurchaseReturnController@uploadDocumentAction');
    //delete upload document
    Route::any('/pr/deleteDoc/{doc_id}', 'PurchaseReturnController@deleteDoc');

	});
});
