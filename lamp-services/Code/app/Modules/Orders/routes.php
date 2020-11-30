<?php


Route::group(['middleware' => ['web']], function () {
    Route::group(['namespace' => 'App\Modules\Orders\Controllers'], function () {
    
	Route::get('/salesorders/detail/{id}','OrdersController@detailAction');
	
	
	Route::get('/salesorders/invoices/{id}','OrdersController@invoicesAction');

	
	
	Route::get('/salesorders/createshipment/{id}','OrdersController@createShipmentAction');

	Route::get('/salesorders/addInvoice/{orderId}/{shipmentId}','OrdersController@addInvoice');

	Route::any('/salesorders/updateOrderStatus','OrdersController@updateOrderStatusAction');
	Route::any('/salesorders/commentHistory','OrdersController@commentHistoryAction');
	
	Route::any('/salesorders/shipping','OrdersController@shippingAction');
	Route::any('/salesorders/createShipment','ShipmentController@createShipmentAction');

	Route::any('/salesorders/commentHistory/{orderId}','CommentController@commentHistoryAction');

	Route::any('/salesorders/addInvoiceData','OrdersController@addInvoiceAction');
	

	Route::any('/salesorders/verficationlist/{orderId}','OrdersController@verificationListAction');
	
	
	
	
	Route::any('/salesorders/downloadInvoice','OrdersController@downloadInvoice');
	
	Route::any('/salesorders/getOrderDetail','OrdersController@getOrderDetailAction');
	Route::any('/salesorders/getStats','OrdersController@getStatsAction');
	Route::get('/salesorders/pickings','OrdersController@pickingsAction');
	Route::get('/salesorders/getOrderPicking','OrdersController@getOrderPickingAction');
	Route::any('/salesorders/createpicking','OrdersController@createPickingAction');
	Route::any('/salesorders/createinvoice/{orderId}/{shipmentId}','OrdersController@createInvoiceAction');
	Route::any('/salesorders/createinvoice/{orderId}','OrdersController@createInvoiceAction');
	Route::post('/salesorders/saveInvoice','OrdersController@saveInvoice');
	Route::get('/salesorders/print/{orderId}','OrdersController@printAction');
	Route::get('/salesorders/pdf/{orderId}','OrdersController@pdfAction');
	Route::any('/salesorders/getorderstats','OrdersController@getOrderStatsAction');

  	Route::any('/salesorders/bulkshipment','BulkshipmentController@bulkShipmentAction');
  	Route::any('/salesorders/savebulkshipment','BulkshipmentController@saveBulkShipmentAction');
    Route::any('/salesorders/saveordersession','OrdersController@saveOrderInSessionAction');
  	Route::any('/salesorders/savebulkinvoicesession','OrdersController@saveBulkInvoiceOrdersAction');
  	Route::get('/salesorders/bulkprint','OrdersController@bulkPrintAction');
  	Route::get('/salesorders/bulkinvoiceprint','OrdersController@printBulkInvoiceAction');
  	Route::any('/salesorders/savepicklist/{data?}','OrdersController@savePicklistAction');
	Route::any('/salesorders/markasdelivered','OrdersController@markAsDelivered');
	Route::any('/salesorders/assigndelexec','OrdersController@assignDeliveryExec');
	Route::any('/salesorders/stocktransfer','OrdersController@stockTransfer');
	Route::any('/salesorders/confirmstockdocket','OrdersController@confirmStockDocket');

	/**
	 * Routes related to Cancel (CancelController) are bellow
	 */

	Route::get('/salesorders/addOrderCancelation/{id}','CancelController@addOrderCancelation');
	Route::any('/salesorders/cancelItem','CancelController@cancelItemAction');

	/**
  	 * Routes related to Report (ReportController) are bellow
  	 */
  		
	Route::any('/salesorders/downloadOrderDetails','ReportController@DownloadOrderDetails');
	Route::any('/salesorders/downloadConsolidateOrders','ReportController@DownloadConsolidateOrders');
	Route::any('/salesorders/downloadOrders','ReportController@downloadOrders');
	Route::any('/salesorders/generatedsr','ReportController@generateDsrAction');
	Route::any('/salesorders/returnReport','ReportController@DownloadReturnReport');
	Route::any('/salesorders/salesVouchersReport','ReportController@salesVouchersReportAction');
	Route::any('/salesorders/salesSummaryReport','ReportController@salesSummaryReportAction');
	Route::any('/salesorders/ofdOrdersReport','ReportController@ofdOrdersReportDownload');

	/**
	 * Routes related to invoices
	 */
	Route::any('/salesorders/generateinvoice','OrdersController@generateInvoiceAction');
	Route::any('/salesorders/generateInvoiceFromOpen','OrdersController@generateInvoiceFromOpen');
	Route::any('/salesorders/createshipmentbypicklist','OrdersController@createShipmentByPicklistApiAction');
	Route::get('/salesorders/bulkinvoiceprintnew','InvoiceController@printBulkInvoiceAction');
	Route::post('/salesorders/addRemarks','InvoiceController@addRemarksAction');
  	/**
  	 * Routes related to Retrun Order (ReturnController) are bellow
  	 */
  	Route::any('/salesorders/createreturn/{orderId}','ReturnController@createReturnAction');
  	Route::any('/salesorders/saveReturnActionAjax','ReturnController@saveReturnActionAjax');
  	Route::any('/salesorders/updateReturn','ReturnController@updateReturnActionAjax');
    Route::any('/salesorders/returndetail/{returnId}','ReturnController@returnDetailAction');
	Route::any('/salesorders/getreturns/{orderId}','ReturnController@getReturnsAction');
	Route::get('/salesorders/checkCreateReturns','ReturnController@checkCreateReturns');

  	Route::any('/salesorders/createCollection','PaymentController@createCollection');
  	Route::any('/salesorders/getCollections/{orderId}','PaymentController@getAllCollectionsByOrderId');
  	Route::any('/salesorders/collectiondetailbyid/{collectionHistoryId}','PaymentController@getCollectionByCollectionHistoryId');
  	Route::any('/salesorders/updateCollection','PaymentController@updateCollectionDetails');

  	Route::any('/salesorders/getpendingpayments/{orderId}','PaymentController@getPendingPaymentHistoryAction');
  	
  	
  	/**
  	 * Payment controller
  	 */
	Route::any('/salesorders/getOrderPickerDetails','PaymentController@getOrderPickerDetails');
	Route::any('/salesorders/getOrderMarkDeliveredDetails','PaymentController@getOrderMarkDeliveredDetails');
	Route::any('/salesorders/getInvoicesByOrderid/{orderId}','PaymentController@getInvoicesListByOrderid');
	Route::any('/salesorders/getInvoiceDueAmount/{invoiceId}','PaymentController@getInvoiceDueAmount');
	Route::any('/salesorders/getTotalPayments/{orderId}','PaymentController@getTotalPaymentsByOrderId');

	/**
  	 * Routes related to Cancel Order (CancelController) are bellow
  	 */
  	
  	Route::get('/salesorders/ajax/orderCancelList/{id}','CancelController@orderCancelList');
  	Route::any('/salesorders/canceldetail/{cancelId}','CancelController@cancelDetailAction');

  	/**
  	 * Routes related to Shipment Order (ShipmentController) are bellow
  	 */
  	Route::get('/salesorders/ajax/shipment/{id}','ShipmentController@shipmentAjaxAction');
   	Route::any('/salesorders/shipmentdetail/{shipmentId}','ShipmentController@shipmentDetailAction');
   	Route::any('/salesorders/addshipment/{orderId}','ShipmentController@addShipmentAction');


   	/**
  	 * Routes related to OFD to Delivery are bellow
  	 */
    Route::any('/salesorders/getOFDDeliveryDetails','OrdersGridController@getOFDDeliveryDetails');
    Route::any('/salesorders/getDeliveryDetails','OrdersGridController@getDeliveryDetails');
    Route::any('/salesorders/fulldeliver','OrdersGridController@fulldeliver');
     Route::any('/salesorders/getorderdetails','OrdersGridController@getOrderDeliveryData');
     Route::any('/salesorders/saveorderdeliveryintemp','OrdersGridController@saveOrderdeliveryTemp');
     Route::any('/salesorders/deliverallorder','OrdersGridController@deliverOrderDetails');


    

		
   	/**
  	 * Routes related to Invoice Order (InvoiceController) are bellow
  	 */
   	Route::get('/salesorders/ajax/invoices/{id}','InvoiceController@invoicesAjaxAction');
   	Route::any('/salesorders/invoicedetail/{invoiceId}/{orderId}','InvoiceController@invoiceDetailAction');
   	Route::get('/salesorders/printinvoice/{id}/{orderId}/{invoice_type?}','InvoiceController@printInvoiceAction');
   	Route::get('/salesorders/invoicepdf/{id}/{orderId}/{invoice_type?}','InvoiceController@invoicePdfAction');


   	Route::any('/salesorders/rectifySalesVoucher/{orderId}','InvoiceController@rectifySalesVoucherAction');
   	
	Route::any('/salesorders/rectifyInvoiceTax/{orderId}','InvoiceController@rectifyInvoiceTaxAction');

	/**
  	 * Routes related to Refund (RefundController) are bellow
  	 */
   	Route::any('/salesorders/getrefunds/{orderId}','RefundController@getRefundsAction');

   	/**
   	 * 
   	 */
   	
   
	Route::get('/salesorders/index/{status}/{sales_id?}', 'OrdersGridController@indexAction');
	Route::get('/salesorders/index','OrdersGridController@indexAction');
	Route::get('/salesorders','OrdersGridController@indexAction');

	// getting list of centers like DC or FC or FF
	Route::any('/salesorders/getCenterList/{centerTypeId}/{all_access}','OrdersGridController@getCenterList');

	Route::get('/salesorders/ajax/index/{status}/{sales_id?}','OrdersGridController@filterOrdersAction');
	Route::get('/salesorders/ajax/index','OrdersGridController@filterOrdersAction');
	
	Route::any('/salesorders/trip/{vehicle_id}', 'ConsignmentController@excelSalesReports');
	Route::any('/salesorders/triphub/{vehicle_id}', 'ConsignmentController@excelSalesReportsHubtoDc');
	Route::any('/salesorders/tripofd/{vehicle_id}', 'ConsignmentController@excelSalesReportsOfd');

	Route::any('/salesorders/printtrip', 'ConsignmentController@printSalesReports');
	Route::any('/salesorders/printtriphub', 'ConsignmentController@printSalesReportsHubtoDc');
	Route::any('/salesorders/trippdf', 'ConsignmentController@tripPdfAction');
	Route::any('/salesorders/triphubpdf', 'ConsignmentController@tripHubPdfAction');
	Route::any('/salesorders/getvehiclebyhub/{hub_id}/{dc_hub}', 'OrdersGridController@getVehicleByHub');
	Route::any('/salesorders/getdriverbyvehicle/{vehicle_id}', 'OrdersGridController@getDriverByVehicleId');
	Route::any('/salesorders/getdockets', 'ConsignmentController@getDockets');
	Route::any('/salesorders/getdocketdetails', 'ConsignmentController@getdocketDetails');


    Route::any('/salesorders/rollback/','CancelController@orderRollBack');
    Route::any('/salesorders/locReport','ReportController@locReportAction');
    Route::any('/salesorders/orderSummary','ReportController@orderSummaryAction');

    Route::any('/salesorders/saveSalesVoucher/{invoic_id}','InvoiceController@saveSalesVoucher');

    Route::any('/salesorders/invoiceToExcelDownload/{id}/{orderId}/{invoice_type?}','InvoiceController@invoiceToExcelDownload');
    Route::any('/salesorders/invoiceprinthsnsummary/{id}/{orderId}/{invoice_type?}','InvoiceController@invoicePrintHsnSummary');

     Route::any('getbu','OrdersController@odersTabGetBuUnit');    
     Route::any('/salesorders/setbuid','OrdersGridController@setBuidInSession');

    // Reasign Checkers in rtd

    Route::any('/salesorders/reassigOrders','OrdersGridController@reassignOrders');
    Route::any('/salesorders/editorder','OrdersController@editOrder');
    Route::any('/salesorders/checkcancellations','OrdersController@checkCancellations');
    Route::any('/salesorders/getlist','OrdersController@getList');
    Route::any('/salesorders/getpacks','OrdersController@getPacks');
    Route::any('/salesorders/addproductintoorder','OrdersController@addProductIntoOrder');
    Route::any('/salesorders/deleteproductintoorder','OrdersController@deleteOrder');

    Route::any('/salesorders/dcfcSalesReport','ReportController@dcFCSalesReportDownload');
	Route::any('/salesorders/retailerSalesReport','ReportController@retailerSalesDownloadOrder');
	Route::any('/salesorders/apobSalesReport','ReportController@apobSalesDownload');

	});
});

Route::group(['middleware' => ['mobile']], function () {

	Route::group(['namespace' => 'App\Modules\Orders\Controllers'], function () {

		// Route::post('/salesorders/api/approvereturn','ReturnController@updateReturnApiApproval');
		Route::post('/salesorders/api/approvereturn','OrdersApiController@updateReturnApiApproval');
		Route::any('/salesorders/api/fixreturntax','OrdersApiController@fixReturnTax');
		Route::post('/salesorders/GenerateInvoice','OrdersApiController@generateInvoiceAction');
		Route::post('/salesorders/verifyPickedQty','OrdersApiController@verifyPickedQtyAction');
	});

});
