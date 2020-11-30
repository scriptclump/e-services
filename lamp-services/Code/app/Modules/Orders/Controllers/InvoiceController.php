<?php

/*
 * Filename: OrdersController.php
 * Description: This file is used for manage sales orders
 * Author: Ebutor <info@ebutor.com>
 * Copyright: ebutor@2016
 * Version: v1.0
 * Created date: 23 June 2016
 * Modified date: 23 June 2016
 */

/*
 * OrdersController is used to manage orders
 * @author		Ebutor <info@ebutor.com>
 * @copyright	ebutor@2016
 * @package		Orders
 * @version: 	v1.0
 */

namespace App\Modules\Orders\Controllers;


use App\Http\Controllers\BaseController;
use Session;
use View;
use Illuminate\Http\Request;
use Log;
use DB;
use Auth;
use Response;
use Illuminate\Support\Facades\Redirect;

use Illuminate\Support\Facades\Input;
use App\Modules\Orders\Models\OrderModel;
use App\Modules\Orders\Models\MasterLookup;
use App\Modules\Orders\Models\Invoice;
use App\Modules\Orders\Models\Shipment;
use App\Modules\Orders\Models\Refund;
use App\Modules\Orders\Models\ReturnModel;
use App\Modules\Orders\Models\OrderTrack;
use App\Modules\Orders\Models\OrderProduct;

use Illuminate\Support\Facades\Route;
use App\Central\Repositories\RoleRepo;
use App\models\Dmapi\dmapiOrders;
use Notifications;
use Lang;
use App\Modules\Orders\Models\GdsLegalEntity;
use App\Modules\Indent\Models\LegalEntity;
use Excel;
use PDF;
use Utility;
use App\Modules\Orders\Controllers\OrdersController;
use Cache;


class InvoiceController extends BaseController {

	protected $_orderModel;
	protected $_masterLookup;
	protected $_commentTypeArr;
	protected $_invoiceModel;
	protected $_shipmentModel;
	protected $_roleRepo;
	protected $_sms;
	protected $_refund;
	protected $_leModel;
	protected $_filterStatus;
	protected $_returnModel;
    protected $_OrdersController;
    protected $_OrderTrack;
    protected $_GdsLegalEntity;
    protected $_OrderProduct;

    public function __construct($forApi = 0) {
		/*if (!Session::has('userId') && $forApi==0) {
			Redirect::to('/login')->send();
		}*/
        $this->middleware(function ($request, $next) use($forApi){
            if (!Session::has('userId') && $forApi==0) {
                Redirect::to('/login')->send();
            }
            return $next($request);
        });
		$this->_orderModel = new OrderModel();
		$this->_masterLookup = new MasterLookup();
		$this->_invoiceModel = new Invoice();
		$this->_shipmentModel = new Shipment();
		$this->_roleRepo = new RoleRepo();
		$this->_sms = new dmapiOrders();
		$this->_refund = new Refund();
		$this->_leModel = new LegalEntity();
		$this->_returnModel = new ReturnModel();
        $this->_OrdersController = new OrdersController(1);
        $this->_OrderTrack = new OrderTrack();
	    $this->_GdsLegalEntity = new GdsLegalEntity();
        $this->_OrderProduct = new OrderProduct();
    }

    /*
     * invoicesAjaxAction() method is used to fetch all invoices
     * @param $id Numbner
     * @return Array
     */

    public function invoicesAjaxAction($id) {
        try{
        $invoicesArr = $this->_orderModel->getAllInvoices($id);
                $totalInvoices = (int)$this->_orderModel->getAllInvoices($id, 1);
                $commentStatusArr = $this->_masterLookup->getStatusByPatentName('INVOICE_STATUS');
        $dataArr = array();
        $hsnsummaryaccess = $this->_roleRepo->checkPermissionByFeatureCode('INVHSN001');
        if(is_array($invoicesArr)) {
            foreach($invoicesArr as $invoice) {
                $popupurl = "window.open('/salesorders/printinvoice/".((int)$invoice->gds_invoice_grid_id)."/".$invoice->gds_order_id."/1','' , 'scrollbars=yes,width=1000,height=800')";
                $popupurlhsn = "window.open('/salesorders/invoiceprinthsnsummary/".((int)$invoice->gds_invoice_grid_id)."/".$invoice->gds_order_id."/1','' , 'scrollbars=yes,width=1000,height=800')";
                $actions = '<a title="View" href="/salesorders/invoicedetail/'.((int)$invoice->gds_invoice_grid_id).'/'.$invoice->gds_order_id.'"><i class="fa fa-eye"></i></a>&nbsp;<a title="Print Invoice" onclick="'.$popupurl.'" href="#"><i class="fa fa-print"></i></a>&nbsp;<a title="Download Invoice" href="/salesorders/invoicepdf/'.((int)$invoice->gds_invoice_grid_id.'/'.$invoice->gds_order_id.'/1').'"><i class="fa fa-download"></i></a>
                                    <a title="Download Invoice Excel" href="/salesorders/invoiceToExcelDownload/'.((int)$invoice->gds_invoice_grid_id.'/'.$invoice->gds_order_id.'/1').'"><i class="fa fa-file-excel-o"></i></a>';
                if($hsnsummaryaccess){
                    $actions .= '&nbsp;<a title="Invoice with HSN summary" onclick="'.$popupurlhsn.'" href="#"><i class="fa fa-print"></i></a>';
                }
                $dataArr[] = array('invoiceId'=>$invoice->invoice_code,
                                    'orderId'=>$invoice->order_code,
                                    'billingName'=>$invoice->billing_name,
                                    'totalAmount'=>number_format($invoice->grand_total, 2),
                                    'invoiceDate'=>date("d-m-Y H:i:s", strtotime($invoice->created_at)),
                                    'TotalQty'=>(int)$invoice->totQty,
                                    'status'=>isset($commentStatusArr[$invoice->invoice_status])?$commentStatusArr[$invoice->invoice_status]:'',
                                    'Actions'=>$actions
                                    );

            }
        }

        echo json_encode(array('data'=>$dataArr, 'totalInvoices'=>$totalInvoices));
        die();
            }
        catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    public function invoiceDetailAction($invoiceId,$orderId) {
        $hasAccess = $this->_roleRepo->checkPermissionByFeatureCode('ORD006');
        if($hasAccess == false) {
            return View::make('Indent::error');
        }
        
        $invoicedProdArr = $this->_orderModel->getInvoiceProductsById($invoiceId,$orderId);
        $taxArr = $this->_orderModel->getProductTaxByOrderId($orderId);
        $ecash_applied = isset($invoicedProdArr[0]->ecash_applied)?$invoicedProdArr[0]->ecash_applied:0;
        if(count($invoicedProdArr)==0) {
            Redirect::to('/salesorders/index')->send();die;
        }
        $trackInfo = $this->_OrderTrack->getTrackDetailByOrderId($orderId);
        $invoiceStatus = isset($invoicedProdArr[0]->invoice_status) ? $invoicedProdArr[0]->invoice_status : '54001';
        $orders = $this->_orderModel->getOrderDetailById($orderId);
        
        $billingAndShippingArr = $this->_orderModel->getBillAndShipAddrFrmLE($orderId);
        $billingAndShipping = $this->_OrdersController->convertBillingAndShippingAddress($billingAndShippingArr);
        $legalEntity = $this->_orderModel->getLegalEntityById($orders->legal_entity_id);
        $whDetails = $this->_leModel->getWarehouseById($orders->le_wh_id);
        $cratesList = $this->_orderModel->getContainerInfoByOrderId($orderId);
      
        $taxSummaryArr = $this->_orderModel->getTaxSummary($taxArr);
        $productTaxArr = isset($taxSummaryArr['item']) ? $taxSummaryArr['item'] : '';
        //print_r($final_products);die;
        $paymentModesArr    =   $this->_masterLookup->getMasterLookupNamesByCategoryId(22);
        //$allUsers       =   $this->_orderModel->getUsersByRoleName(array('Field Force Associate','Field Force Manager'));
        $url = env('CASHBACK_URL');
        $post_feild = ["order_id"=>$orderId];
        $headers = array("cache-control: no-cache","content-type: multipart/form-data");
        $cb_response = Utility::sendcUrlRequest($url, $post_feild, $headers,0);
        return view('Orders::invoiceDetail')
                    ->with('billing', (isset($billingAndShipping['billing']) ? $billingAndShipping['billing'] : ''))
                    ->with('legalEntity', $legalEntity)
                    ->with('whDetails', $whDetails)
                    ->with('tabHeading', 'Invoice Details')
                    ->with('paymentModesArr', $paymentModesArr)
                    ->with('actionName', 'invoiceDetail')
                    ->with('orderdata', $orders)
                    //->with('allUsers', $allUsers)
                    ->with('trackInfo', $trackInfo)
                    ->with('taxArr', $productTaxArr)
                    ->with('cratesList',$cratesList)
                    ->with('cb_response', $cb_response)
                    ->with('ecash_applied', $ecash_applied)
                    ->with('invoicedProdArr', $invoicedProdArr);
    }

    /*
     * printInvoiceAction() method is used to print invoice
     * @param $orderId Numeric
     * @return Array
     */

    public function printInvoiceAction($invoiceId,$orderId,$invoice_type='',$excel=0,$hsnsummary=0) {
            try{

                if($invoice_type==1){
                    $products = $this->_orderModel->getInvoiceProductsById($invoiceId,$orderId);
                }
                else{
                    $products = $this->_orderModel->getInvoiceProductByOrderId($invoiceId);
                }


                if(isset($products[0]->gds_order_id)){
                    $orderId = $products[0]->gds_order_id;
                    $ecash_applied = isset($products[0]->ecash_applied)?$products[0]->ecash_applied:0;
                    $orderDetails = $this->_orderModel->getOrderDetailById($orderId);
                    if(count($orderDetails)==0) {
                        Redirect::to('/salesorders/index')->send();
                    }
                    $trackInfo = $this->_OrderTrack->getTrackDetailByOrderId($orderId);
                    $taxArr = $this->_orderModel->getProductTaxByOrderId($orderId);
                    $billingAndShippingArr = $this->_orderModel->getBillAndShipAddrFrmLE($orderId);
                    $billingAndShipping = $this->_OrdersController->convertBillingAndShippingAddress($billingAndShippingArr);
                    $legalEntity = $this->_orderModel->getLegalEntityWarehouseById($orderDetails->legal_entity_id,$orderDetails->le_wh_id);
                    $whDetails = $this->_leModel->getWarehouseById($orderDetails->le_wh_id);
                    
                    $leInfo = $this->_leModel->getLegalEntityById($orderDetails->legal_entity_id);
                    $lewhInfo = $this->_leModel->getWarehouseById($orderDetails->le_wh_id);
                    $lewhInfo->authorized_by = str_replace('#LOGO', '<img style="width: 9px;padding-left:2px;vertical-align: middle;" src="/assets/admin/layout/img/small-logo.png" alt="logo" class="small-logo"/>', $lewhInfo->authorized_by);
                   //echo "<pre>";print_r($lewhInfo);die;
                    
                    $prodTaxes = array();
                    $hasGstProdTaxes = array();
                    foreach ($taxArr as $tax) {
                        $prodTaxes[$tax->product_id] = array('name'=>$tax->name, 'tax_value'=>$tax->tax_value, 'tax'=>$tax->tax, 
                            'cgstPer'=>(($tax->tax * $tax->CGST)/100),  
                            'sgstPer'=>(($tax->tax * $tax->SGST)/100), 
                            'igstPer'=>(($tax->tax * $tax->IGST)/100), 
                            'utgstPer'=>(($tax->tax * $tax->UTGST)/100), );
                        
                        if($tax->CGST > 0 || $tax->SGST > 0 || $tax->IGST > 0 || $tax->UTGST > 0) {
                            $hasGstProdTaxes[] = $tax->CGST;
                        }
                        
                    }
                    #echo '<pre>';print_r($hasGstProdTaxes);die;
                    $companyInfo = $this->_leModel->getCompanyAccountByLeId($orderDetails->legal_entity_id);
                    $userInfo = '';
                    if($orderDetails->created_by) {
                        $userInfo = $this->_leModel->getUserById($orderDetails->created_by);
                    }

                    $delSlots = $this->_masterLookup->getMasterLookupByCategoryName('Delivery Slots');                    
                    $cratesList = $this->_orderModel->getContainerInfoByOrderId($orderId);
                    $pickerInfo = $this->_OrderTrack->getPickerByOrderId($orderId);
                    $template = 'printinvoice';
                    $tempFile  = 'printinvoiceExcelSheet';
                    if(is_array($hasGstProdTaxes) && count($hasGstProdTaxes) > 0) {
                        $template = 'printinvoicenew';
                    }

                    $masvalue = $this->_masterLookup->getMasterLokup(78018);
                    $termsdisplay = isset($masvalue->description)?$masvalue->description:0;
                    if($excel==1) {
                    Excel::create('Sales_Invoice_'.$products[0]->invoice_code,function($excel) use($products,$billingAndShipping,$leInfo,$whDetails,$userInfo,$trackInfo,
                       $prodTaxes,$companyInfo,$orderDetails,$delSlots,$hasGstProdTaxes,$pickerInfo,$ecash_applied,$cratesList,$legalEntity,$lewhInfo,
                       $termsdisplay,$tempFile) {  

                       $excel->sheet('Sales_Invoice_'.$products[0]->invoice_code, function($sheet) use($products,$billingAndShipping,$leInfo,$whDetails,$userInfo,$trackInfo,$prodTaxes,$companyInfo,$orderDetails,$delSlots,$hasGstProdTaxes,$pickerInfo,$ecash_applied,$cratesList,$legalEntity,$termsdisplay,$lewhInfo,$tempFile) {  
                        $sheet->loadView('Orders::'.$tempFile)
                                        ->with('products',$products)
                                        ->with('billing', isset($billingAndShipping['billing']) ? $billingAndShipping['billing'] :'')
                                        ->with('shipping', isset($billingAndShipping['shipping']) ? $billingAndShipping['shipping'] :'')
                                        ->with('leInfo', $leInfo)
                                        ->with('whDetails', $whDetails)
                                        ->with('lewhInfo', $lewhInfo)
                                        ->with('userInfo', $userInfo)
                                        ->with('trackInfo', $trackInfo)
                                        ->with('prodTaxes', $prodTaxes)
                                        ->with('companyInfo', $companyInfo)
                                        ->with('orderDetails', $orderDetails)
                                        ->with('delSlots',$delSlots)
                                        ->with('hasGstProdTaxes',$hasGstProdTaxes)
                                        ->with('pickerInfo', $pickerInfo)
                                        ->with('ecash_applied', $ecash_applied)
                                        ->with('cratesList',$cratesList)
                                        ->with('legalEntity', $legalEntity)
                                        ->with('termsdisplay',$termsdisplay);
                                });
                         $excel->getActiveSheet()->setCellValueByColumnAndRow("A", 1, "");
                        })->export('xls');

                            return 1;
                    }
                    
                    return view('Orders::'.$template)->with('orderDetails', $orderDetails)
                                ->with('products',$products)
                                ->with('billing', isset($billingAndShipping['billing']) ? $billingAndShipping['billing'] :'')
                                ->with('shipping', isset($billingAndShipping['shipping']) ? $billingAndShipping['shipping'] :'')
                                ->with('leInfo', $leInfo)
                                ->with('whDetails', $whDetails)
                                ->with('lewhInfo', $lewhInfo)
                                ->with('userInfo', $userInfo)
                                ->with('trackInfo', $trackInfo)
                                ->with('prodTaxes', $prodTaxes)
                                ->with('companyInfo', $companyInfo)
                                ->with('orderDetails', $orderDetails)
                                ->with('delSlots',$delSlots)
                                ->with('hasGstProdTaxes',$hasGstProdTaxes)
                                ->with('pickerInfo', $pickerInfo)
                                ->with('ecash_applied', $ecash_applied)
                                ->with('cratesList',$cratesList)
                                ->with('legalEntity', $legalEntity)
                                ->with('hsnsummary', $hsnsummary)
                                ->with('termsdisplay',$termsdisplay);
                }


                    else{
                    Redirect::to('/salesorders/index')->send();
                }


            }
            catch(Exception $e) {
                Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            }
    }

    /*
     * printInvoiceAction() method is used to print invoice
     * @param $orderId Numeric
     * @return Array
     */

    public function invoicePdfAction($invoiceId,$orderId,$invoice_type='') {
            try{
                if($invoice_type==1){
                    $products = $this->_orderModel->getInvoiceProductsById($invoiceId,$orderId);
                }
                else{
                    $products = $this->_orderModel->getInvoiceProductByOrderId($invoiceId);
                }

                if(isset($products[0]->gds_order_id)){
                    $orderId = $products[0]->gds_order_id;
                    $ecash_applied = isset($products[0]->ecash_applied)?$products[0]->ecash_applied:0;
                    $orderDetails = $this->_orderModel->getOrderDetailById($orderId);
                                //print_r($orderDetails);die;
                    if(count($orderDetails)==0) {
                        Redirect::to('/salesorders/index')->send();
                    }
                    $trackInfo = $this->_OrderTrack->getTrackDetailByOrderId($orderId);
                    $taxArr = $this->_orderModel->getProductTaxByOrderId($orderId);
                    $billingAndShippingArr = $this->_orderModel->getBillAndShipAddrFrmLE($orderId);
                    $billingAndShipping = $this->_OrdersController->convertBillingAndShippingAddress($billingAndShippingArr);
                    $legalEntity = $this->_orderModel->getLegalEntityWarehouseById($orderDetails->legal_entity_id,$orderDetails->le_wh_id);


                    $taxSummaryArr = $this->_orderModel->getTaxSummary($taxArr);
                    $taxSummary = isset($taxSummaryArr['summary']) ? $taxSummaryArr['summary'] : '';
                    $productTaxArr = isset($taxSummaryArr['item']) ? $taxSummaryArr['item'] : '';
                    $taxBreakup = isset($taxSummaryArr['breakup']) ? $taxSummaryArr['breakup'] : '';
                    //echo '<pre>';print_r($orderDetails);die;
                    $leInfo = $this->_leModel->getLegalEntityById($orderDetails->legal_entity_id);
                    $lewhInfo = $this->_leModel->getWarehouseById($orderDetails->le_wh_id);
                    $lewhInfo->authorized_by = str_replace('#LOGO', '<img style="width: 9px;padding-left:2px;vertical-align: middle;" src="/assets/admin/layout/img/small-logo.png" alt="logo" class="small-logo"/>', $lewhInfo->authorized_by);
                    $prodTaxes = array();
                    
                    $hasGstProdTaxes = array();
                    foreach ($taxArr as $tax) {
                        $prodTaxes[$tax->product_id] = array('name'=>$tax->name, 'tax_value'=>$tax->tax_value, 'tax'=>$tax->tax, 
                            'cgstPer'=>(($tax->tax * $tax->CGST)/100),  
                            'sgstPer'=>(($tax->tax * $tax->SGST)/100), 
                            'igstPer'=>(($tax->tax * $tax->IGST)/100), 
                            'utgstPer'=>(($tax->tax * $tax->UTGST)/100));
                        if($tax->CGST > 0 || $tax->SGST > 0 || $tax->IGST > 0) {
                            $hasGstProdTaxes[] = $tax->CGST;
                        }
                    }
                    $companyInfo = $this->_leModel->getCompanyAccountByLeId($orderDetails->legal_entity_id);
                    $userInfo = '';
                    if($orderDetails->created_by) {
                        $userInfo = $this->_leModel->getUserById($orderDetails->created_by);
                    }
                    $delSlots = $this->_masterLookup->getMasterLookupByCategoryName('Delivery Slots');
                    
                    $cratesList = $this->_orderModel->getContainerInfoByOrderId($orderId);
                    $pickerInfo = $this->_OrderTrack->getPickerByOrderId($orderId);


                    $template = 'invoicePdf';
                    if(is_array($hasGstProdTaxes) && count($hasGstProdTaxes) > 0) {
                        $template = 'invoicePdfGST';
                    }

                    $data = array('products'=>$products,
                        'billing'=>isset($billingAndShipping['billing']) ? $billingAndShipping['billing'] :'',
                        'shipping'=>isset($billingAndShipping['shipping']) ? $billingAndShipping['shipping'] :'', 
                        'taxArr'=>$productTaxArr,
                        'taxSummaryArr'=>$taxArr, 
                        'leInfo'=>$leInfo, 
                        'lewhInfo'=>$lewhInfo,
                        'companyInfo'=>$companyInfo,
                        'taxBreakup'=>$taxBreakup,
                        'trackInfo'=>$trackInfo, 
                        'orderDetails'=>$orderDetails, 
                        'legalEntity'=>$legalEntity,
                        'cratesList'=>$cratesList,
                        'pickerInfo'=>$pickerInfo, 
                        'userInfo'=>$userInfo,
                        'delSlots'=>$delSlots,
                        'ecash_applied'=> $ecash_applied,
                        'prodTaxes'=>$prodTaxes);

                     $pdf = PDF::loadView('Orders::'.$template, $data);
                     return $pdf->download('invoice_'.$invoiceId.'.pdf');
                     //return view('Orders::'.$template)->with($data);
                }
                else{
                    Redirect::to('/salesorders/index')->send();
                }
            }
            catch(Exception $e) {
                Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            }
    }






    private function getOrderIds() {
        $orderIdsArr = Session::get('orderIds');
        $orders = array();
        if(is_array($orderIdsArr[0])) {
            foreach ($orderIdsArr[0] as $orderId) {
                $orders[] = $orderId;
            }
        }
        return $orders;
    }
  
    public function printBulkInvoiceAction() {
    
        try{
           
            $orderIdsArr = $this->getOrderIds();
            $tracksInfo = $this->_OrderTrack->getTrackDetails($orderIdsArr);
            
            $delSlots = $this->_masterLookup->getCachedMasterLookupByCatName('Delivery Slots', 'BulkInvoiceMasterLookup');

            #echo '<pre>';print_r($orderIdsArr);die;
            $bulkPrintData = array();
            $ordersArr = array();
            if(is_array($orderIdsArr)) {
                foreach ($orderIdsArr as $orderId) {     
                
                    $products = $this->_invoiceModel->getInvoiceProductByOrderId($orderId);
                    $ecash_applied = isset($products[0]->ecash_applied)?$products[0]->ecash_applied:0;
                    if(empty($products)) {
                        continue;
                    }
                    
                    #echo '<pre>';print_r($products);die;

                    $orderDetails = $this->_orderModel->getOrderDetailById($orderId);
                    $trackInfo = isset($tracksInfo[$orderId]) ? $tracksInfo[$orderId] : '';
                    
                    $taxArr = $this->_orderModel->getProductTaxByOrderId($orderId);
                    $billingAndShippingArr = $this->_orderModel->getBillAndShipAddrFrmLE($orderId);
                    $billingAndShipping = $this->_OrdersController->convertBillingAndShippingAddress($billingAndShippingArr);
                                                          
                    $taxSummaryArr = $this->_orderModel->getTaxSummary($taxArr);
                    $taxBreakup = isset($taxSummaryArr['breakup']) ? $taxSummaryArr['breakup'] : '';
                    //echo '<pre>';print_r($orderDetails);die;
                    $leInfo = $this->_GdsLegalEntity->getCachedLegalEntityById($orderDetails->legal_entity_id, 'BulkInvoiceLegalEntity');
                    $lewhInfo = $this->_GdsLegalEntity->getCachedWarehouseById($orderDetails->le_wh_id, 'BulkInvoiceWarehouse');
                    
                    $prodTaxes = array();
                    
                    foreach ($taxArr as $tax) {
                        $prodTaxes[$tax->product_id] = array('name'=>$tax->name, 'tax_value'=>$tax->tax_value, 'tax'=>$tax->tax);
                    }
                    $userInfo = '';
                    if($orderDetails->created_by) {
                        $userInfo = $this->_GdsLegalEntity->getUserById($orderDetails->created_by, array('firstname', 'lastname', 'mobile_no'));
                    }
                                                            
                    $cratesList = $this->_orderModel->getContainerInfoByOrderId($orderId);
                    $pickerInfo = isset($tracksInfo[$orderId]) ? $tracksInfo[$orderId] : '';

                    $data = array('orderDetails'=>$orderDetails, 
                                    'products'=>$products,
                                    'billing'=>(isset($billingAndShipping['billing']) ? $billingAndShipping['billing'] : ''),
                                    'shipping'=>(isset($billingAndShipping['shipping']) ? $billingAndShipping['shipping'] : ''),
                                    'taxSummaryArr'=>$taxArr,
                                    'taxBreakup'=>$taxBreakup,                              
                                    'leInfo'=>$leInfo,
                                    'prodTaxes'=>$prodTaxes,
                                    'trackInfo'=>$trackInfo,
                                    'lewhInfo'=>$lewhInfo,
                                    'userInfo'=>$userInfo,
                                    'delSlots'=>$delSlots,
                                    'pickerInfo'=>$pickerInfo,
                                    'ecash_applied'=>$ecash_applied,
                                    'cratesList'=>$cratesList
                                );
                        $bulkPrintData[] = view('Orders::printinvoicenew')->with($data)->render(); 
                }
               
                //echo "<pre>";print_r($bulkPrintData);die;
                return  view('Orders::bulkinvoice')->with('bulkPrintData',$bulkPrintData);
            }
        }
        catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }   
    
    }

    /**
     * rectifySalesVoucherAction() method is used to rectify sales voucher
     */

    public function rectifySalesVoucherAction($orderId){
        DB::beginTransaction();
        try{
            
            $query = DB::table('gds_invoice_grid as invgrid')->select(array('invgrid.gds_invoice_grid_id', 'invgrid.invoice_code'));
            $row = $query->where('invgrid.gds_order_id', $orderId)->first();
           
            if(isset($row->gds_invoice_grid_id) && $row->gds_invoice_grid_id > 0) {
                $invoice_code = $row->invoice_code;
                
                $this->_invoiceModel->updateSalesVoucher($invoice_code, array('is_posted'=>5));

                $result = $this->_invoiceModel->saveSalesVoucher($row->gds_invoice_grid_id, 'Sales Entry New');                
                DB::commit();
                return response::json(array('orderId'=>$orderId, 'invoice_code'=>$invoice_code));
            }
        }
        catch(Exception $e){
            DB::rollback();
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return false;
        }        
    }

    /**
     * rectifyInvoiceTaxAction() method is used to sales tax invoice
     */
    
    public function rectifyInvoiceTaxAction($orderId) {
        DB::beginTransaction();
        try{
            
            $taxArr = $this->_OrderProduct->getGdsOrderProductByOrderId($orderId);
            echo '<pre>';
            //print_r($taxArr);
            if(is_array($taxArr) && count($taxArr) > 0) {
                foreach ($taxArr as $tax) {
                   
                    $invoiced = $this->_invoiceModel->getInvoicedQtyByOrderIdAndProductId($orderId, $tax->product_id);
                    $InvQty = isset($invoiced->invoicedQty) ? (int)$invoiced->invoicedQty : 0;
                    if($InvQty) {
                        $product = $this->_orderModel->getProductByOrderIdProductId($orderId, $tax->product_id);

                        $tax_per_object = $this->_orderModel->getTaxPercentageOnGdsProductId($product->gds_order_prod_id);
                       
                        $tax_per = $tax_per_object->tax_percentage;

                        $singleUnitPrice = (($product->total / (100+$tax_per)*100) / $product->qty);
                        $net_value = ($singleUnitPrice * $InvQty);

                        $singleUnitPriceWithtax = (($tax_per/100) * $singleUnitPrice) + $singleUnitPrice;
                    
                        $tax_amount = (($singleUnitPrice * $tax_per) / 100 ) * $InvQty;

                        $rowTotalInclTax = ($tax_amount + $net_value);

                       $invoiceItem = array(
                            'product_id'=>$tax->product_id,
                            'tax'=>$tax_per,
                            'qty'=>$InvQty,
                            'price'=>$singleUnitPrice,
                            'price_incl_tax'=>$singleUnitPriceWithtax,
                            'base_cost'=>$singleUnitPrice,
                            'tax_amount'=>$tax_amount,
                            'row_total'=>$net_value,
                            'row_total_incl_tax'=>$rowTotalInclTax);
                       
                        $fields = array(
                            'price'=>$singleUnitPrice,
                            'price_incl_tax'=>$singleUnitPriceWithtax,
                            'base_cost'=>$singleUnitPrice,
                            'tax_amount'=>$tax_amount,
                            'row_total'=>$net_value,
                            'row_total_incl_tax'=>$rowTotalInclTax);
                        print_r($invoiceItem);

                        $this->_invoiceModel->updateInvoiceItem($orderId, $tax->product_id, $fields);
                        DB::commit();
                    }                    
                }
                return response::json(array('Success'));
            }            
        }
        catch(Exception $e) {
            DB::rollback();
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return false;
        }        
    }

    public function addRemarksAction(Request $request) {
        DB::beginTransaction();
        try{
            $postData = $request->all();
            $remarks = isset($postData['invoice_remarks']) ? $postData['invoice_remarks'] : '';
            $gridId = isset($postData['gds_invoice_grid_id']) ? $postData['gds_invoice_grid_id'] : 0;
            $this->_invoiceModel->updateInvoiceGridById($gridId, array('remarks'=>$remarks));
            DB::commit();
            return Response::json(array('status' => 200, 'message' => 'Successfully updated', 'data1'=>$remarks));
        }
        catch(Exception $e){
            DB::rollback();
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return Response::json(array('status' => 400, 'message' => 'Something went wrong'));
        }
    }


    public function saveSalesVoucher($invoice_id){
        $this->_invoiceModel->saveSalesVoucher($invoice_id);
    }

    public function invoiceToExcelDownload($invoiceId,$orderId,$invoice_type=''){
        $this->printInvoiceAction($invoiceId,$orderId,1,1);
    }
    public function invoicePrintHsnSummary($invoiceId,$orderId,$invoice_type=''){
      return $this->printInvoiceAction($invoiceId,$orderId,1,0,1);
    }

}
