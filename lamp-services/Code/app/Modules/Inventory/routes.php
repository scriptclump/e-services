<?php

Route::group(['middleware' => ['web']], function () {
    Route::group(['namespace' => 'App\Modules\Inventory\Controllers'], function () {
        //routes commented burla added same route bwl
       /* Route::get('/inventory/index', 'InventoryController@indexAction');
        Route::get('/inventory', 'InventoryController@indexAction');*/
        Route::get('/inventory/totalinventory', 'InventoryController@getAllInventory');
        Route::get('/inventory/getExport', 'InventoryController@getExport');
        Route::get('/inventory/getpdf', 'InventoryController@getPdf');
        Route::any('/inventory/updateInventory', 'InventoryController@updateInventory');
        Route::any('/inventory/downloadTemplate', 'InventoryController@exportTemplate');
        Route::any('/inventory/excelUpload', 'InventoryController@excelImport');
        Route::any('/inventory/getProductsForProductPage', 'InventoryController@getProductsForProductPage');
        Route::any('/inventory/getAllProductsByWareHouseForExcel', 'InventoryController@getAllProductsByWareHouseForExcel');
        Route::get('/inventory/zeroReport', 'InventoryZeroReportController@indexAction');
        Route::get('/inventory/getProductsForProductPageZeroReport', 'InventoryZeroReportController@getProductsForProductPage');
        Route::get('/inventory/totalinventoryZeroReport', 'InventoryZeroReportController@getAllInventory');
        Route::get('/inventory/getExportZeroReport', 'InventoryZeroReportController@getExport');

        Route::get('/inventory/approvalworkflow/{trackingid}', 'InventoryApprovalController@approvalWorkFlowInventoryUpdate'); //Approvalworkflow route
        Route::any('/inventory/approvalworkflowdetails', 'InventoryApprovalController@approvalSubmit');

        Route::get('/inventory/bulkapprovalworkflow/{trackingid}', 'InventoryApprovalController@approvalWorkFlowBulkUpdate'); //Approvalworkflow route
        Route::any('/inventory/bulkapprovalworkflowdetails', 'InventoryApprovalController@bulkApprovalSubmit');
        Route::any('/inventory/excellogs/{refid}', 'InventoryController@readExcelLogs');
        Route::any('/inventory/excellogsreplanishment/{refid}', 'InventoryController@readExcelLogsReplanishement');

        Route::any('/inventory/replanishmentdownloadtemplate', 'InventoryController@replanishmentDownloadTemplate');
        Route::any('/inventory/exceluploadreplanishment', 'InventoryController@excelUploadReplanishment');
        /* routes for the inventory snapshot report download*/
        Route::any('/inventory/snapshotexport', 'InventorySnapshotController@exportData');
        Route::any('/inventory/openclosesnapshot', 'InventorySnapshotController@opencloseSnapshot');
        /* cycle count report download*/
        Route::any('/cycle/count/report', 'InventorySnapshotController@cycleCountReport');

        // Out Of Stock Report
        Route::get('/inventory/outofstockreport','InventoryController@outOfStockReport');
        Route::post('/inventory/getOOSReportChartData','InventoryController@outOfStockReportChartData');

        /*new routes invntory*/

        Route::get('/inventory', 'InventoryController@indexActionInventory');
        Route::get('/inventory/totalinventorygrid', 'InventoryController@getAllInventoryGrid');
        // new route download data
        Route::post('/inventory/getExportInventory', 'InventoryController@getExportInventoryData');

        // this routes for soh upload
        Route::any('/inventory/downloadSohTemplate', 'InventorySOHController@downloadSohTemplate');
        Route::any('/inventory/excelStockTransferUpload', 'InventorySOHController@stackTransferUploadExcel');
        Route::any('/inventory/approvalforstock/{tableid}', 'InventorySOHController@getApprovalsForStock');
        Route::any('/inventory/approvalRequestForSOH', 'InventorySOHController@ApprovalForNextStatusUsers');

        //new route for summary report
        Route::any('/inventory/summaryexport', 'InventorySummaryController@exportData');

        Route::get('/inventory/writeoff', 'InvWriteoffController@writeOffInv');
        Route::any('/inventory/writeoffdownload', 'InvWriteoffController@writeoffDownload');
        Route::any('/inventory/writeoffupload', 'InvWriteoffController@uploadWriteoff');
        Route::any('/inventorywriteoff/excellogs/{refid}', 'InvWriteoffController@readWriteoffExcelLogs');
        Route::get('/inventorywriteoffticket/{trackingid}', 'InvWriteoffController@approvalWorkFlowBulkUpdate'); 
        Route::any('/writeoffapprovalworkflowdetails', 'InvWriteoffController@bulkApprovalSubmit'); 

        //inventory adjustment report
        Route::any('/inventory/invadjustmentdownloadTemplate', 'InventoryController@exportInvAdjTemplate');
        Route::any('/inventory/InvAdjExcelImport', 'InventoryController@InvAdjExcelImport');
          Route::any('/inventory/invadjustmentdownloadTemplate', 'InventoryController@exportInvAdjTemplate');

          Route::any('/inventoryadjustment/excellogs/{refid}', 'InventoryController@readExcelLogs');
        Route::get('/inventory/adjustmentapprovalworkflow/{trackingid}', 'InventoryApprovalController@adjApprovalWorkFlowBulkUpdate');
        Route::any('/inventory/invadjbulkapprovalworkflowdetails', 'InventoryApprovalController@invAdjApprovalSubmit');
        Route::any('/inventory/stockistledgerexport','InventoryController@stockistLedgerReports');
        Route::any('/inventory/getbu','InventoryController@getBuUnit');
        Route::any('/inventory/batchindex','InventoryController@indexBatchAction'); 
        Route::any('/inventory/inventorybatchhistory','InventoryController@inventoryBatchHistory');
        Route::any('/inventory/getbatchIdsbySKU','InventoryController@getBatchIdsBySKU');  
        Route::get('/inventory/getbatchSkus', 'InventoryController@getBatchSkus');
        Route::post('/inventory/batchreport', 'InventoryController@getBatchReport');      
    });
});

Route::group(['middleware' => ['mobile']], function () {
    Route::group(['namespace' => 'App\Modules\Inventory\Controllers'], function () {
        Route::any('/inventory/mobileawf/awfbulkuploaddetails', 'InvMobileApprController@awfBulkUploadDetails');
        Route::any('/inventory/mobileawf/getinvbulkuploadtkts', 'InvMobileApprController@getInvBulkUploadTkts');
        Route::any('/inventory/mobileawf/approveinvbulkuploadtkt', 'InvMobileApprController@approveInvBulkUploadTkt');
        Route::any('/inventory/mobileawf/invapprovehistory', 'InvMobileApprController@approvalHistoryDetails');

        Route::any('/inventory/mobileawf/getinvbulkuploadtktsForCycleCount', 'InvMobileApprController@getInvBulkUploadTktsForCycleCount');
    });
});
