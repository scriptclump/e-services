<?php

Route::group(['middleware' => ['web']], function () {
    Route::group(['namespace' => 'App\Modules\InventoryAuditCycleCount\Controllers'], function () {
        Route::get('/inventoryauditcc/', 'InventoryAuditCycleCountController@indexAction');
        Route::get('/inventoryauditcc/index', 'InventoryAuditCycleCountController@indexAction');
        Route::any('/inventoryauditcc/downloadtemplatecc', 'InventoryAuditCycleCountController@donwloadTemplateCC');
        Route::any('/inventoryauditcc/downloadtemplatest', 'InventoryAuditCycleCountController@downloadTemplateST');
		Route::any('/inventoryauditcc/excelupload', 'InventoryAuditCycleCountController@excelUpload');
        Route::any('/inventoryauditcc/exceluploadstocktake', 'InventoryAuditCycleCountController@excelUploadStockTake');
        Route::any('/inventoryauditcc/excellogsaudit/{refid}', 'InventoryAuditCycleCountController@readExcelLogsAudit');
		Route::any('/inventoryauditcc/auditapprovaldownload/{auditid}', 'InventoryAuditCycleCountController@auditApproval');    //donwload approval ticket as Excel    

		Route::any('/inventoryauditcc/viewapprovalticket/{auditid}', 'InventoryAuditCycleCountController@viewApprovalTicket'); 
		Route::any('/inventoryauditcc/viewapprovalticket/{auditid}/{status}', 'InventoryAuditCycleCountController@viewApprovalTicket'); 
		Route::any('/inventoryauditcc/uploadappovalworkflowsheet', 'InventoryAuditCycleCountController@uploadAppovalWorkFlowSheet'); 
		Route::any('/inventoryauditcc/submitapprovalstatus', 'InventoryAuditCycleCountController@submitApprovalStatus'); 

        Route::any('/inventoryauditcc/getallclosedtickets', 'InventoryAuditCycleCountController@getAllClosedTickets'); 
        Route::any('/inventoryauditcc/auditapprovaldownloadclosdtkts/{auditid}', 'InventoryAuditCycleCountController@closedTicketData');
        Route::any('/inventoryauditcc/allopentickets', 'InventoryAuditCycleCountController@allOpenTickets');
        Route::any('/inventoryauditcc/opentkt/{tktid}', 'InventoryAuditCycleCountController@openTicketInfo');
        // /inventoryauditcc/opentkt/
        

		


        
      
    });
});
