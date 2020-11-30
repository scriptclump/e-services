<?php

/*
 * Filename: PurchaseInvoiceController.php
 * Description: This file is used for manage purchase Invoice
 * Author: Ebutor <info@ebutor.com>
 * Copyright: ebutor@2016
 * Version: v1.0
 * Created date: 3 Nov 2016
 * Modified date: 3 Nov 2016
 */

/*
 * PurchaseInvoiceController is used to manage purchase Invoice
 * @author		Ebutor <info@ebutor.com>
 * @copyright	ebutor@2016
 * @package		PO
 * @version: 	v1.0
 */

namespace App\Modules\PurchaseOrder\Controllers;

use App\Http\Controllers\BaseController;
use Session;
use View;
use Illuminate\Http\Request;
use Response;
use Log;
use DB;
use Input;
use PDF;
use Illuminate\Support\Facades\Redirect;
use App\Modules\PurchaseOrder\Models\PurchaseOrder;
use App\Modules\Orders\Models\MasterLookup;
use App\Central\Repositories\RoleRepo;
use App\Modules\Indent\Models\LegalEntity;
use App\Modules\Indent\Models\Products;
use App\Modules\Grn\Models\Inward;
use App\Central\Repositories\ProductRepo;
use App\Central\Repositories\CustomerRepo;
use Utility;
use Lang;
use Excel;
use Config;
use Notifications;
use Artisan;
use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;

class PurchaseInvoiceController extends BaseController {

    protected $_poModel;
    protected $_masterLookup;
    protected $_roleRepo;
    protected $_LegalEntity;
    protected $_productRepo;
    protected $_customerRepo;
    protected $_products;
    protected $_filterStatus;

    /*
     * __construct() method is used to call model
     * @param Null
     * @return Null
     */

    public function __construct($forApi = 0) {
        date_default_timezone_set('Asia/Kolkata');
        $this->middleware(function ($request, $next) use($forApi) {
            if (!Session::has('userId') && $forApi == 0) {
                Redirect::to('/login')->send();
            }
            return $next($request);
        });
        parent::Title('Purchase Invoice - '.Lang::get('headings.Company'));
        $this->_poModel = new PurchaseOrder();
        $this->_masterLookup = new MasterLookup();
        $this->_roleRepo = new RoleRepo();
        $this->_LegalEntity = new LegalEntity();
        $this->_productRepo = new ProductRepo();
        $this->_customerRepo = new CustomerRepo();
    }

    public function invoiceDetail($invoiceId) {
        try {
            $hasAccess = $this->_roleRepo->checkPermissionByFeatureCode('PO003');
            if ($hasAccess == false) {
                return View::make('PurchaseOrder::error');
            }
            $poDetailArr = $this->_poModel->getPoInvoiceDetailById($invoiceId);
            if (count($poDetailArr) == 0) {
                Redirect::to('/po/index')->send();
                die();
            }
            $leWhId = isset($poDetailArr[0]->le_wh_id) ? $poDetailArr[0]->le_wh_id : 0;
            $leId = isset($poDetailArr[0]->legal_entity_id) ? $poDetailArr[0]->legal_entity_id : 0;


            $whDetail = $this->_LegalEntity->getWarehouseById($leWhId);
            $userInfo = $this->_poModel->getUserByLeId($leId);
            $supplierInfo = $this->_poModel->getLegalEntityById($leId);

            $taxBreakup = $this->getTaxBreakup($poDetailArr);
            $packTypes = $this->_masterLookup->getAllOrderStatus('Levels');

            $printFeature = $this->_roleRepo->checkPermissionByFeatureCode('PO004');
            $downloadFeature = $this->_roleRepo->checkPermissionByFeatureCode('PO005');
            $featureAccess = array('printFeature' => $printFeature, 'downloadFeature' => $downloadFeature);
            $invoiceCount = $this->_poModel->poInvoiceCountByPOId($poDetailArr[0]->po_id);
            $totalPayments = (int) $this->_poModel->getAllPayments($leId, 1);
            return view('PurchaseOrder::invoiceDetail')->with('productArr', $poDetailArr)
                            ->with('supplier', $supplierInfo)
                            ->with('packTypes', $packTypes)
                            ->with('whDetail', $whDetail)
                            ->with('userInfo', $userInfo)
                            ->with('taxBreakup', $taxBreakup)
                            ->with('invoiceCount', $invoiceCount)
                            ->with('invoiceId', $invoiceId)
                            ->with('totalPayments', $totalPayments)
                            ->with('featureAccess', $featureAccess)
                            ->with('leId', $leId)
                            ->with('po_id', $poDetailArr[0]->po_id);
        } catch (Exception $e) {
            Log::error($ex->getMessage() . ' ' . $ex->getTraceAsString());
        }
    }
    public function poInvoicePrint($invoiceId) {
        try {
            $hasAccess = $this->_roleRepo->checkPermissionByFeatureCode('PO003');
            if ($hasAccess == false) {
                return View::make('PurchaseOrder::error');
            }
            $poDetailArr = $this->_poModel->getPoInvoiceDetailById($invoiceId);
            if (count($poDetailArr) == 0) {
                Redirect::to('/po/index')->send();
                die();
            }
            $leWhId = isset($poDetailArr[0]->le_wh_id) ? $poDetailArr[0]->le_wh_id : 0;
            $leId = isset($poDetailArr[0]->legal_entity_id) ? $poDetailArr[0]->legal_entity_id : 0;
            $leParentId = $this->_LegalEntity->getLeParentIdByLeId($leId);
            $leDetail = $this->_LegalEntity->getLegalEntityById($leParentId);
            $companyInfo = $this->_LegalEntity->getCompanyAccountByLeId($leParentId);

            $whDetail = $this->_LegalEntity->getWarehouseById($leWhId);
            $userInfo = $this->_poModel->getUserByLeId($leId);
            $supplierInfo = $this->_poModel->getLegalEntityById($leId);

            $taxBreakup = $this->getTaxBreakup($poDetailArr);
            $packTypes = $this->_masterLookup->getAllOrderStatus('Levels');
            $taxPerr = '';
            foreach($taxBreakup as $tax1){
                $taxPerr = ($taxPerr=='')?$tax1['tax']:$taxPerr;
            }
            $template = 'printPOInvoice';
            if($taxPerr=='') {
                $template = 'printPOInvoice_gst';
            }
            return view('PurchaseOrder::'.$template)->with('productArr', $poDetailArr)
                            ->with('supplier', $supplierInfo)
                            ->with('packTypes', $packTypes)
                            ->with('leDetail', $leDetail)
                            ->with('whDetail', $whDetail)
                            ->with('userInfo', $userInfo)
                            ->with('taxBreakup', $taxBreakup);
        } catch (Exception $e) {
            Log::error($ex->getMessage() . ' ' . $ex->getTraceAsString());
        }
    }
    public function poInvoicePdf($invoiceId) {
        try {
            $hasAccess = $this->_roleRepo->checkPermissionByFeatureCode('PO003');
            if ($hasAccess == false) {
                return View::make('PurchaseOrder::error');
            }
            $poDetailArr = $this->_poModel->getPoInvoiceDetailById($invoiceId);
            if (count($poDetailArr) == 0) {
                Redirect::to('/po/index')->send();
                die();
            }
            $leWhId = isset($poDetailArr[0]->le_wh_id) ? $poDetailArr[0]->le_wh_id : 0;
            $leId = isset($poDetailArr[0]->legal_entity_id) ? $poDetailArr[0]->legal_entity_id : 0;
            $leParentId = $this->_LegalEntity->getLeParentIdByLeId($leId);
            $leDetail = $this->_LegalEntity->getLegalEntityById($leParentId);
            $companyInfo = $this->_LegalEntity->getCompanyAccountByLeId($leParentId);

            $whDetail = $this->_LegalEntity->getWarehouseById($leWhId);
            $userInfo = $this->_poModel->getUserByLeId($leId);
            $supplierInfo = $this->_poModel->getLegalEntityById($leId);

            $taxBreakup = $this->getTaxBreakup($poDetailArr);
            $packTypes = $this->_masterLookup->getAllOrderStatus('Levels');
            $taxPerr = '';
            foreach($taxBreakup as $tax1){
                $taxPerr = ($taxPerr=='')?$tax1['tax']:$taxPerr;
            }
            $template = 'poInvoicePdf';
            if($taxPerr=='') {
                $template = 'poInvoicePdf_gst';
            }
           $data = array('supplier' => $supplierInfo, 'productArr' => $poDetailArr
                , 'packTypes' => $packTypes, 'whDetail' => $whDetail, 'userInfo' => $userInfo, 'leDetail' => $leDetail,
               'companyInfo' => $companyInfo,'taxBreakup'=>$taxBreakup);
            $pdf = PDF::loadView('PurchaseOrder::'.$template, $data);
            return $pdf->download('PO_Invoice' . $invoiceId . '.pdf');
        } catch (Exception $e) {
            Log::error($ex->getMessage() . ' ' . $ex->getTraceAsString());
        }
    }
    public function getTaxBreakup($products) {
        $finalTaxArr = array();
        $gst_taxes = ['GST','IGST','CGST','SGST','UTGST'];
        if (is_array($products)) {
            $taxKey = 0;
            foreach ($products as $product) {
                $taxName = strtoupper($product->tax_name);
                if (in_array($taxName, $gst_taxes) && $product->tax_data != '') {
                    $tax_data = json_decode($product->tax_data, true);
                    foreach ($tax_data as $key => $val) {
                        $cgst = isset($val['CGST']) ? $val['CGST'] : 0;
                        $sgst = isset($val['SGST']) ? $val['SGST'] : 0;
                        $igst = isset($val['IGST']) ? $val['IGST'] : 0;
                        $utgst = isset($val['UTGST']) ? $val['UTGST'] : 0;
                        $cgst_val = ($product->tax_amt * $cgst) / 100;
                        $sgst_val = ($product->tax_amt * $sgst) / 100;
                        $igst_val = ($product->tax_amt * $igst) / 100;
                        $utgst_val = ($product->tax_amt * $utgst) / 100;
                        $finalTaxArr['CGST'][] = array('price' => $product->price,'tax' => '', 'name' => 'CGST', 'tax_amt' => $cgst_val);
                        $finalTaxArr['SGST'][] = array('price' => $product->price,'tax' => '', 'name' => 'SGST', 'tax_amt' => $sgst_val);
                        $finalTaxArr['IGST'][] = array('price' => $product->price,'tax' => '', 'name' => 'IGST', 'tax_amt' => $igst_val);
                        $finalTaxArr['UTGST'][] = array('price' => $product->price,'tax' => '', 'name' => 'UTGST', 'tax_amt' => $utgst_val);
                    }
                } else {
                    $taxKey = (float) $product->tax_name . '-' . $product->tax_per;
                    if ($taxKey != '0-0') {
                        $finalTaxArr[$taxKey][] = array('price' => $product->price, 'tax' => $product->tax_per, 'name' => $product->tax_name, 'tax_amt' => $product->tax_amt);
                    }
                }
            }
            $finalNewTaxArr = array();
           foreach ($finalTaxArr as $key => $taxArr) {
                $finalNewTaxArr[$key] = array();
                $totAmt = 0;
                $totPrice = 0;
                foreach ($taxArr as $tax) {
                    $totPrice = $totPrice + $tax['price'];
                    $totAmt = $totAmt + $tax['tax_amt'];
                    $finalNewTaxArr[$key]['name'] = $tax['name'];
                    $finalNewTaxArr[$key]['tax'] = $tax['tax'];
                }

                $finalNewTaxArr[$key]['tax_price'] = $totPrice;
                $finalNewTaxArr[$key]['tax_value'] = $totAmt;
            }
            return $finalNewTaxArr;
        }
    }

    public function invoicesAjaxAction($id) {
        try {
            $invoicesArr = $this->_poModel->getAllInvoices($id);
            $totalInvoices = (int) $this->_poModel->getAllInvoices($id, 1);
            $commentStatusArr = $this->_masterLookup->getMasterLookupByCategoryName('PO Invoice Status');
            $dataArr = array();

            if (is_array($invoicesArr)) {
                foreach ($invoicesArr as $invoice) {
                    $popupurl = "window.open('/po/poInvoicePrint/" . ((int) $invoice->po_invoice_grid_id). "','' , 'scrollbars=yes,width=1000,height=800')";
                    $dataArr[] = array('invoiceId' => $invoice->invoice_code,
                        'inward_id' => $invoice->inward_code,
                        'billingName' => $invoice->billing_name,
                        'totalAmount' => number_format($invoice->grand_total, 2),
                        'invoiceDate' => $invoice->created_at,
                        'TotalQty' => (int) $invoice->totQty,
                        'status' => isset($commentStatusArr[$invoice->invoice_status]) ? $commentStatusArr[$invoice->invoice_status] : '',
                        'Actions' => '<a title="View" href="/po/invoiceDetail/' . ((int) $invoice->po_invoice_grid_id) . '"><i class="fa fa-eye"></i></a>&nbsp;<a title="Print Invoice" onclick="' . $popupurl . '" href="#"><i class="fa fa-print"></i></a>&nbsp;<a title="Download Invoice" href="/po/poInvoicePdf/' . ((int) $invoice->po_invoice_grid_id) . '"><i class="fa fa-download"></i></a>'
                    );
                }
            }

            echo json_encode(array('data' => $dataArr, 'totalInvoices' => $totalInvoices));
            die();
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function createInvoiceByinwardId($inwardId) {
        DB::beginTransaction();
        try {
            if ($inwardId == '' && $inwardId <= 0) {
                return json_encode(array('status'=>400,'message'=>'invalid inward id'));
            }
            $inwardDetails = $this->_poModel->getInwardDetailsById($inwardId);
            if (count($inwardDetails) <= 0) {
                return json_encode(array('status'=>400,'message'=>'inward details not found'));
            }
            $checkInvoiceExist = $this->_poModel->checkInvoiceByInwardId($inwardId);
            if($checkInvoiceExist==1){
                return json_encode(array('status'=>400,'message'=>'Invoice already created'));
            }
            $le_wh_id = (isset($inwardDetails[0]->le_wh_id)) ? $inwardDetails[0]->le_wh_id:0;
            $state_id = $this->_customerRepo->getSateIdByDcId($le_wh_id);
            $invoiceCode =$this->_customerRepo->getRefCode('PI',$state_id);
            //$grand_total = array_sum(array_column($inwardDetails,'tax_amount'))+array_sum(array_column($inwardDetails,'sub_total'));
            $grand_total = (isset($inwardDetails[0]->grand_total)) ? $inwardDetails[0]->grand_total:0;
            if($grand_total > 0)
            {
            $invoiceGrid = array(
                'invoice_code' => $invoiceCode,
                'inward_id' => $inwardId,
                'billing_name' => (isset($inwardDetails[0]->business_legal_name)) ? $inwardDetails[0]->business_legal_name : '',
                'discount_on_total' => (isset($inwardDetails[0]->discount_on_total)) ? $inwardDetails[0]->discount_on_total : 0,
                'shipping_fee' => (isset($inwardDetails[0]->shipping_fee)) ? $inwardDetails[0]->shipping_fee : 0,
                'grand_total' => (float)$grand_total,
                'invoice_status' => 11301,
                'approval_status' => 0,
                'created_by' => \Session::get('userId'),
            );
            $invoice_grid_id = $this->_poModel->savePOInvoice($invoiceGrid);
            $invoiceProduct = array();
            foreach ($inwardDetails as $product) {
                $invoiceProduct[] = array(
                    'po_invoice_grid_id' => $invoice_grid_id,
                    'product_id' => $product->product_id,
                    'qty' => $product->received_qty,
                    'free_qty' => $product->free_qty,
                    'damage_qty' => $product->damage_qty,
                    'unit_price' => $product->price,
                    'tax_name' => $product->tax_name,
                    'tax_per' => (float) $product->tax_per,
                    'tax_amount' => $product->tax_amount,
                    'hsn_code' => $product->hsn_code,
                    'tax_data' => $product->tax_data,
                    'discount_type' => $product->discount_type,
                    'discount_per' => $product->discount_percentage,
                    'discount_amount' => $product->discount_total,
                    'price' => $product->sub_total,
                    'sub_total' => $product->tax_amount+$product->sub_total,
                    'comment' => $product->remarks,
                    'created_by' => \Session::get('userId'),
                );
            }
            $this->_poModel->savePOInvoiceProducts($invoiceProduct);
            DB::commit();
            $mesg = 'Invoice created successfully';
            }else{
                $mesg = 'Invoice grand total cannot be zero.';
            }            
            return json_encode(array('status'=>200,'message'=>$mesg));
        } catch (Exception $e) {
            DB::rollback();
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function approvalSubmit() {
        try {
            $data = input::get();
            $approval_unique_id = $data['approval_unique_id'];
            $approval_status = $data['approval_status'];
            $approval_module = $data['approval_module'];
            $current_status = $data['current_status'];
            $approval_comment = $data['approval_comment'];
            $table = $data['table_name'];
            $unique_column = $data['unique_column'];
            $approval_flow_func= new CommonApprovalFlowFunctionModel();
            $status = explode(',',$approval_status);
            $nextStatus = $status[0];
            $tables =['po','payment_details'];
            $nextstatuses =[57121,57146];
            if($table =='po' && $nextStatus == 57117){
                $po = $this->_poModel->getPOPaymentRequests($approval_unique_id,[57203,57204,57218,57219,57222]);
                $finance_users = $this->_roleRepo->getUsersByRoleCode(['FS','FH','FFNO','FFNM']); // Only finance team can cancel PO if payment initiated
                $finance_users = json_decode(json_encode($finance_users),1);
                $user_id = array_column($finance_users, 'user_id');
                if(count($po)>0 && !in_array(\Session::get('userId'), $user_id)){
                    $response = array('status'=>400,'message'=>'Please close payment requests initiated to cancel this PO');
                    return json_encode($response);die;
                }
            }
            if(in_array($table, $tables) && in_array($nextStatus, $nextstatuses)){
                
            }else{
                 $this->_poModel->updateStatusAWF($table,$unique_column,$approval_unique_id, $approval_status);    
            }
            $approval_flow_func->storeWorkFlowHistory($approval_module, $approval_unique_id, $current_status, $nextStatus, $approval_comment, \Session::get('userId'));
            $response = array('status'=>200,'message'=>'Success');
            return json_encode($response);
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    public function newproductemail($approval_unique_id,$le_wh_id) {
        $exitCode = Artisan::call('autosmsnotify', ['notification_code'=>"NEWPRODUCT01","params"=>"$approval_unique_id,$le_wh_id"]);
    }
}
