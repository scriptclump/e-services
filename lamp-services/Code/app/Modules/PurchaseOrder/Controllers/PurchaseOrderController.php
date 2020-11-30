<?php

/*
 * Filename: PurchaseOrderController.php
 * Description: This file is used for manage purchase order
 * Author: Ebutor <info@ebutor.com>
 * Copyright: ebutor@2016
 * Version: v1.0
 * Created date: 4 July 2016
 * Modified date: 4 July 2016
 */

/*
 * PurchaseOrderController is used to manage purchase order
 * @author      Ebutor <info@ebutor.com>
 * @copyright   ebutor@2016
 * @package     PO
 * @version:    v1.0
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
use App\Modules\Indent\Models\IndentModel;
use App\Central\Repositories\RoleRepo;
use App\Modules\Indent\Models\LegalEntity;
use App\Modules\Indent\Models\Products;
use App\Modules\Grn\Models\Inward;
use App\Central\Repositories\ProductRepo;
use Utility;
use Lang;
use Excel;
use Config;
use Notifications;
use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;
use App\Modules\Users\Models\Users;
use App\Modules\Orders\Models\GdsBusinessUnit;
use App\Modules\Notifications\Models\NotificationsModel;
use App\Modules\Cpmanager\Models\CartModel;
use App\Modules\Cpmanager\Controllers\CartController;
use App\Modules\Orders\Models\OrderModel;
use App\Modules\Roles\Models\Role;
use App\Modules\Cpmanager\Controllers\OrderController;
use App\Central\Repositories\CustomerRepo;
use Cache;
use App\Central\Repositories\ReportsRepo;
use Carbon\Carbon;
use File;
use Artisan;
use URL;
use App\Modules\PurchaseOrder\Models\VendorPaymentRequest;
class PurchaseOrderController extends BaseController {

    protected $_poModel;
    protected $_masterLookup;
    protected $_indent;
    protected $_roleRepo;
    protected $_LegalEntity;
    protected $_productRepo;
    protected $_products;
    protected $_filterStatus;
    protected $_approvalStatuslist;
    protected $_gdsBus;
    protected $_orderModel;
    protected $_roleModel;
    /*
     * __construct() method is used to call model
     * @param Null
     * @return Null
     */

    public function __construct($forApi=0) {
        date_default_timezone_set('Asia/Kolkata');
        $this->middleware(function ($request, $next) use($forApi){
		    if (!Session::has('userId') && $forApi==0) {               
		        Redirect::to('/login')->send();
		    }
		    return $next($request);
		});
        $this->_poModel = new PurchaseOrder();
//        $this->_vendorPaymentRequestModel = new VendorPaymentRequest();
        $this->_masterLookup = new MasterLookup();
        $this->_indent = new IndentModel();
        $this->_roleRepo = new RoleRepo();
        $this->_LegalEntity = new LegalEntity();
        $this->_productRepo = new ProductRepo();
        $this->_gdsBus =new GdsBusinessUnit();
        $this->_orderModel = new OrderModel();
        $this->_roleModel = new Role(); 
        $this->_reportsrepo = new ReportsRepo();
        parent::Title('Purchase Orders - '.Lang::get('headings.Company'));
            $this->grid_field_db_match = array(
            'poId'   => 'po.po_code',
            'Supplier'        => 'legal_entities.business_legal_name',
            'le_code'        => 'legal_entities.le_code',
            'shipTo'      => 'lwh.lp_wh_name',
            'validity'      => 'po.po_validity',
            'poValue'       => 'poValue',
            'createdBy'      => 'user_name',
            'createdOn'      => 'po.po_date',
            'payment_mode'      => 'po.payment_mode',
            'tlm_name'      => 'po.tlm_name',
            'Status' => 'lookup.master_lookup_name',
            'poValue' => 'poValue',
            'grn_value' => 'grn_value',
            'po_grn_diff' => 'po_grn_diff',
            'grn_created' => 'grn_created',
            'payment_status' => 'po.payment_status',
            'payment_due_date' => 'payment_due_date',
            'po_so_order_link' => 'po_so_order_code',
            'po_parent_link' => 'po_parent_code',
            'duedays' => 'duedays'
        );
    $this->_filterStatus = array('open'=>'87001', 'partial'=>'87005','closed'=>'87002', 'expired'=>'87003', 'canceled'=>'87004');
        $this->_approvalStatuslist = ['initiated'=>57106,'created'=>57029,'verified'=>57030,'approved'=>57031,'posit'=>57033,'checked'=>57107,'receivedatdc'=>57034,'grncreated'=>57035,'shelved'=>1,'payments'=>57032]; //'shelved'=>57108
    }

    /*
     * indexAction() method is used to list of inventory
     * @param Null
     * @return String
     *
     * Status
     *
     * 87001 - Open
     * 87002 - Closed
     * 87003 - Expired
     *
     */

    public function indexAction($status=null) {

        try{

            $hasAccess = $this->_roleRepo->checkPermissionByFeatureCode('PO001');
            $poGSTReport = $this->_roleRepo->checkPermissionByFeatureCode('POGSTR');
            if($hasAccess == false) {
                return View::make('PurchaseOrder::error');
            }

            $legalEntityId = Session::get('legal_entity_id');
            $input = Input::all();
            $from_date = isset($input['from_date'])?$input['from_date']:"";
            $to_date = isset($input['to_date'])?$input['to_date']:"";
            $user_id = Session::get("userId");
           // print_r($from_date);die;
                // if($from_date == ""){
                //     Session::set("from_date",$from_date);
                // }
                // if($to_date == ""){
                //     Session::set("to_date",$to_date);
                // }
            $allStatusArr = $this->_masterLookup->getAllOrderStatus('PURCHASE_ORDER');

            $suppliers = $this->_poModel->getSupplierByLEId($legalEntityId);
            $Json = json_decode($this->_roleModel->getFilterData(6), 1);
            $filters = json_decode($Json['sbu'], 1);
            $dc_acess_list = isset($filters['118001']) ? $filters['118001'] : 'NULL';
            $allDc = $this->_orderModel->getDcHubDataByAcess($dc_acess_list);
            $filter_options['dc_data'] = $allDc;
            $allPOCountArr = $this->_poModel->getPoCountByStatus($legalEntityId,0,$from_date,$to_date);
            $allPOApprovalCountArr = $this->_poModel->getPoCountByStatus($legalEntityId,1,$from_date,$to_date);
            $finalApprovalCountArr = $this->_poModel->getPoCountByStatus($legalEntityId,2,$from_date,$to_date);
            $partialCountArr = $this->_poModel->getPoCountByStatus($legalEntityId,3,$from_date,$to_date);
            $grnCountArr = $this->_poModel->getPoCountByStatus($legalEntityId,4,$from_date,$to_date);
                        $partial=isset($partialCountArr[87005]) ? (int)$partialCountArr[87005] : 0;
                        $closed=isset($grnCountArr[87002]) ? (int)$grnCountArr[87002] : 0;
                        $approval_cancel = (isset($allPOApprovalCountArr[57117]) ? (int)$allPOApprovalCountArr[57117] : 0);
                        $canceled = (isset($allPOCountArr[87004]) ? (int)$allPOCountArr[87004] : 0)+$approval_cancel;
                        $opened = (isset($allPOCountArr[87001]) ? (int)$allPOCountArr[87001] : 0)-$approval_cancel;
                        $shelved = isset($finalApprovalCountArr[1]) ? (int)$finalApprovalCountArr[1] : 0;
                        $immediatePay = $this->_poModel->getPoCountByStatus($legalEntityId,5,$from_date,$to_date);
                        $accept_full = isset($allPOApprovalCountArr[57107]) ? (int)$allPOApprovalCountArr[57107] : 0;
                        $accept_part = isset($allPOApprovalCountArr[57119]) ? (int)$allPOApprovalCountArr[57119] : 0;
                        $accept_part_closed = isset($allPOApprovalCountArr[57120]) ? (int)$allPOApprovalCountArr[57120] : 0;
                        $inspected_full = isset($allPOApprovalCountArr[57034]) ? (int)$allPOApprovalCountArr[57034] : 0;
                        $inspected_part = isset($allPOApprovalCountArr[57122]) ? (int)$allPOApprovalCountArr[57122] : 0;
                        $checked = $accept_full+$accept_part+$accept_part_closed;
                        $initiated=isset($allPOApprovalCountArr[57106]) ? (int)$allPOApprovalCountArr[57106] : 0;
                        $created=isset($allPOApprovalCountArr[57029]) ? (int)$allPOApprovalCountArr[57029] : 0;
                        $verified=isset($allPOApprovalCountArr[57030]) ? (int)$allPOApprovalCountArr[57030] : 0;
                        //echo "<pre/>";print_r($allPOCountArr);die;
                        $poCounts = array(
                                        'all'=>array_sum($allPOCountArr),
                                        'opened'=>$opened,
                                        'partial'=>$partial,
                                        'closed'=>$closed,
                                        'canceled'=>$canceled,
                                        'expired'=>isset($allPOCountArr[87003]) ? (int)$allPOCountArr[87003] : 0,
                                        'initiated'=>$initiated,
                                        'created'=>$created,
                                        'verified'=>$verified,
                                        'approved'=>isset($allPOApprovalCountArr[57031]) ? (int)$allPOApprovalCountArr[57031] : 0,
                                        'paid'=> $opened+$partial+$shelved,
                                        'immediatepay'=>  array_sum($immediatePay),
                                        'posit'=>isset($allPOApprovalCountArr[57033]) ? (int)$allPOApprovalCountArr[57033] : 0,
                                        'receivedatdc'=>($inspected_part+$inspected_full),
                                        'checked'=>$checked,
                                        'grncreated'=>isset($allPOApprovalCountArr[57035]) ? (int)$allPOApprovalCountArr[57035] : 0,
                                        'shelved'=>$shelved,
                                        );
            $filter_status = empty($status) ? 'allpo' : $status;
            $dates='';
            if($from_date !="" && $to_date != ""){
                $dates = "?from_date=$from_date&to_date=$to_date";
            } 
            $createFeature = $this->_roleRepo->checkPermissionByFeatureCode('PO002');
            $exportFeature = $this->_roleRepo->checkPermissionByFeatureCode('PO008');
            $featureAccess = array('createFeature'=>$createFeature,'exportFeature'=>$exportFeature);
            return view('PurchaseOrder::index')->with('poCounts', $poCounts)
                                                            ->with('filter_status', $filter_status)
                                                            ->with('suppliers', $suppliers)
                                                            ->with('featureAccess', $featureAccess)
                                                            ->with('allStatusArr', $allStatusArr)
                                                            ->with('poGSTReport',$poGSTReport)
                                                            ->with('filter_options',$filter_options)
                                                            ->with('dates',$dates);
                                                            
        }
        catch(Exception $e) {

        }
    }

    /*
     * ajaxAction() method is used to get all purchase order for grid
     * @param $status String, by default null
     * @return JSON
     */

    public function ajaxAction($status=null, Request $request) {

        try{
            $status = empty($status) ? 'allpo' : $status;
            $allStatusArr = $this->_masterLookup->getAllOrderStatus('PURCHASE_ORDER');
            $filters = array();
            $orderby_array  = "";
            $filterData = $request->all();
            
            if(isset($filterData['$filter'])){
                $filters = $this->filterData($filterData['$filter']);
            }
  
            if(array_key_exists($status, $this->_filterStatus)) {
                $filters['po_status_id'] = $this->_filterStatus[$status];
            }
            if(array_key_exists($status, $this->_approvalStatuslist)) {
                $filters['approval_status_id'] = $this->_approvalStatuslist[$status];
            }
            if(isset($filterData['from_date']) && $filterData['from_date'] != "") {
                $filters['from_date'] = $filterData['from_date'];
                //Session::set('from_date', $filters['from_date']);
            }
            if(isset($filterData['to_date']) && $filterData['to_date'] != "") {
                $filters['to_date'] = $filterData['to_date'];
                //Session::set('to_date', $filters['to_date']);
            }
            if (isset($filterData['$orderby'])) {             //checking for sorting
                $order = explode(' ', $request->input('$orderby'));
                $order_query_field = $order[0]; //on which field sorting need to be done
                $order_query_type = $order[1]; //sort type asc or desc
                $order_by_type = 'desc';

                if ($order_query_type == 'asc') {
                    $order_by_type = 'asc';
                }
                $order_by = '';
                if (isset($this->grid_field_db_match[$order_query_field])) { //getting appropriate table field based on grid field
                    $order_by = $this->grid_field_db_match[$order_query_field];
                }
                // print_r($order_by);die;
                if (empty($order_by)) {
                    $order_by = 'po.po_date';
                    $order_by_type = 'desc';
                }
                $orderby_array = $order_by . " " . $order_by_type;
            }

            $offset = (int)$request->input('page');
            $perpage = $request->input('pageSize');

            $poDataArr = $this->_poModel->getAllPurchasedOrders($orderby_array, $filters, 0, $offset, $perpage);
            $totalPurchageOrders = $this->_poModel->getAllPurchasedOrders($orderby_array, $filters, 1);
            
            $dataArr = array();
            if(count($poDataArr)) {
                $detailFeature = $this->_roleRepo->checkPermissionByFeatureCode('PO003');
                $printFeature = $this->_roleRepo->checkPermissionByFeatureCode('PO004');
                $downloadFeature = $this->_roleRepo->checkPermissionByFeatureCode('PO005');
                $editFeature = $this->_roleRepo->checkPermissionByFeatureCode('PO007');

                foreach($poDataArr as $po) {
                    $shipTo = $po->lp_wh_name;
                    $poValidity = $po->po_validity . ' ' . (($po->po_validity > 1) ? 'Days' : 'Day');
                    $po_status = isset($allStatusArr[$po->po_status]) ? $allStatusArr[$po->po_status] : '';
                    $approvalStatus = isset($po->approval_status) ? $po->approval_status : '';
                    $paymentStatus = isset($po->payment_status) ? $po->payment_status : '';
                    $poValue = ($po->poValue != '') ? $po->poValue : 0;
                    $actions = '';                    

                    if ($detailFeature) {
                        $actions.='<a class="" href="/po/details/' . $po->po_id . '"> <i class="fa fa-eye"></i></a>&nbsp;';
                    }
                    if ($printFeature) {
                        $actions.= '<a target="_blank" class="" href="/po/printpo/' . $po->po_id . '"> <i class="fa fa-print"></i></a>&nbsp;';
                    }
                    if ($downloadFeature) {
                        $actions.= '<a class="" href="/po/download/' . $po->po_id . '"> <i class="fa fa-download"></i></a>&nbsp;';
                    }
                    if ($po->po_status == '87001' && $po->approval_status_val != '57117' && $editFeature) {
                        $actions.= '<a class="" href="/po/edit/' . $po->po_id . '"> <i class="fa fa-pencil"></i></a>&nbsp;';
                    }

                    if ($po->po_status == '87001' && in_array($po->approval_status_val, [57107,57119,57120])) {
                        $actions.= '<a class="" href="/grn/create/' . $po->po_id . '" target="_blank"> <i title="Create GRN" class="fa fa-truck"></i></a>&nbsp;';
                    }

                    if ($po->payment_mode == 2) {
                        $payment_mode = ($po->parent_id > 0) ? '<strong style="color:green;">Pre Paid</strong>' : '<strong style="color:blue;">Pre Paid</strong>';
                    } else {
                        $payment_mode = ($po->parent_id > 0) ? '<strong style="color:green;">Post Paid</strong>' : 'Post Paid';
                    }
                    $tlm_name = $po->tlm_name;
                    $po_so_order_link = "";
                    if($po->po_so_order_code !='' ){
                        $po_so_order_id = $this->_poModel->getOrderIdByCode($po->po_so_order_code);
                        if($po_so_order_id != 0 and $po_so_order_id!="")
                            $po_so_order_link = "<a class='' target='_blank' href='/salesorders/detail/".$po_so_order_id."'> ".$po->po_so_order_code."</a>";
                    }
                    $po_parent_link = "";
                    if($po->po_parent_code !='' ){
                        $po_parent_link = "<a class='' target='_blank' href='/po/details/".$po->po_parent_id."'> ".$po->po_parent_code."</a>";
                    }

                    $dataArr[] = array(
                        'poId' => $po->po_code,
                        'le_code' => $po->le_code,
                        'Supplier' => $po->business_legal_name,
                        'shipTo' => $shipTo,
                        'validity' => $poValidity,
                        'poValue' => $poValue,
                        'payment_mode' => $payment_mode,
                        'payment_due_date' => $po->payment_due_date,
                        'tlm_name' => $tlm_name,
                        'createdBy' => $po->user_name,
                        'createdOn' => ($po->po_date),
                        'Status' => $po_status,
                        'approval_status' => $approvalStatus,
                        'payment_status' => $paymentStatus,
                        'Actions' => $actions,
                        'grn_value' => ($po->grn_value),
                        'po_grn_diff' => $po->po_grn_diff,
                        'grn_created' => $po->grn_created,
                        'po_so_order_link'=>$po_so_order_link,
                        'po_parent_link'=>$po_parent_link
                    );
                }
                return Response::json(array('data'=>$dataArr, 'totalPurchageOrders'=>$totalPurchageOrders));
            }
            else {
                return Response::json(array('data'=>array(), 'totalPurchageOrders'=>0));
            }
        }
        catch(Exception $e) {
            return Response::json(array('data'=>array(), 'totalPurchageOrders'=>0));
        }
    }

	private function getTaxInfo($poArr) {
		$taxArr = array();
                if(is_array($poArr)) {
                    $leWhId = isset($poArr[0]->le_wh_id)?$poArr[0]->le_wh_id:0;
                    $leId = isset($poArr[0]->legal_entity_id)?$poArr[0]->legal_entity_id:0;
                    $whDetail = $this->_LegalEntity->getWarehouseById($leWhId);
                    $supplierInfo = $this->_poModel->getLegalEntityById($leId);
                    $wh_le_id = isset($whDetail->legal_entity_id)?$whDetail->legal_entity_id:0;
                    if($wh_le_id > 0 && $leId==24766){
                        $le_type_id = $this->_poModel->getLegalEntityTypeId($wh_le_id);
                        // print_r($supplierInfo);die;
                        if($le_type_id == 1016){
                            $apob_data = $this->_poModel->getApobData($wh_le_id);
                            if(count($apob_data)){
                                $supplierInfo = $apob_data;
                            }
                        }
                    }
                    $wh_state_code = isset($whDetail->state)?$whDetail->state:4033;
                    $seller_state_code = isset($supplierInfo->state_id)?$supplierInfo->state_id:4033;
                    foreach ($poArr as $product) {
                        $prodTaxArr = $this->getProductTaxClass($product->product_id,$wh_state_code,$seller_state_code);
                        $taxArr[$product->product_id] = $prodTaxArr;
                    }
		}
		return $taxArr;
	}
    public function getProductTaxClass($product_id,$wh_state_code=4033,$seller_state_code=4033) {
        try {
            $url=env('APP_TAXAPI');
            $data['product_id'] = (int)$product_id;
            $data['buyer_state_id'] = (int)$wh_state_code;
            $data['seller_state_id'] = (int)$seller_state_code;
            $taxData = Utility::sendRequest($url,$data);
            return $taxData;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
   
 public function detailsAction($id) {
        try {
            $hasAccess = $this->_roleRepo->checkPermissionByFeatureCode('PO003');
            if ($hasAccess == false) {
                return View::make('PurchaseOrder::error');
            }
            $poDetailArr = $this->_poModel->getPoDetailById($id);
            if (count($poDetailArr) == 0) {
                Redirect::to('/po/index')->send();
                die();
            }
            Session::set('po_id', $id);
            $leWhId = isset($poDetailArr[0]->le_wh_id) ? $poDetailArr[0]->le_wh_id : 0;
            $leId = isset($poDetailArr[0]->legal_entity_id) ? $poDetailArr[0]->legal_entity_id : 0;
            $history = $this->_poModel->getApprovalHistory('Purchase Order', $id);
            $printFeature = $this->_roleRepo->checkPermissionByFeatureCode('PO004');
            $downloadFeature = $this->_roleRepo->checkPermissionByFeatureCode('PO005');
            $editFeature = $this->_roleRepo->checkPermissionByFeatureCode('PO007');
            $closeFeature = $this->_roleRepo->checkPermissionByFeatureCode('PO009');
            $spiltFeature = $this->_roleRepo->checkPermissionByFeatureCode('POSP001');
            $createOrderFeature = $this->_roleRepo->checkPermissionByFeatureCode('POCRT001');
            
            $allHubs = $allDcs = $allWarehouses = $allStates = false;
            /* If the user has split Feature, then he can create/view order for the PO*/
            $orderId = "";

            if($createOrderFeature){
                // Check weather he created the order or not
                if(isset($poDetailArr[0]->po_so_status) && $poDetailArr[0]->po_so_status){
                    $orderId = $this->_poModel->getOrderIdByCode($poDetailArr[0]->po_so_order_code);
                }
                $allDcs = $this->_poModel->getAllWarehouses(118001);
                $allHubs = $this->_poModel->getAllWarehouses(118002);
                // 99 is the Country Id of India
                $allStates = $this->_poModel->getAllStates(99);
            }

            $splitdcs = $this->_masterLookup->getMasterLokup(78022);
            $notallowsplitdcs = isset($splitdcs->description)?explode(',',$splitdcs->description):0;

            $featureAccess = array('printFeature'=>$printFeature,'downloadFeature'=>$downloadFeature,'editFeature'=>$editFeature,'closeFeature'=>$closeFeature,'spiltFeature'=>$spiltFeature,'createOrderFeature'=>$createOrderFeature);
            $invoiceCount = $this->_poModel->poInvoiceCountByPOId($id);
            $packTypes = $this->_masterLookup->getAllOrderStatus('Levels');

            $poCode = explode('_',$poDetailArr[0]->po_code);
            $po_code = isset($poCode[0])?$poCode[0]:0;
            $parent = $this->_poModel->getPODetailsByCode($po_code);
            $parentPOId = isset($parent->po_id)?$parent->po_id:$id;
            $totalPayments = (int) $this->_poModel->getAllPayments($leId, 1);
            //$ledgerAccounts = $this->_poModel->getTallyLedgerAccounts();
            //$paymentType = $this->_masterLookup->getAllOrderStatus('Payment Type',[2,3]);
            $user_id = Session::get("userId");
            $is_Supplier = $this->_poModel->checkUserIsSupplier($user_id);
            $is_Supplier = count($is_Supplier);
            return view('PurchaseOrder::details')->with('productArr', $poDetailArr)
                            ->with('history', $history)
                            ->with('invoiceCount', $invoiceCount)
                            ->with('totalPayments', $totalPayments)
                            ->with('featureAccess', $featureAccess)
                            ->with('notallowsplitdcs', $notallowsplitdcs)
                            ->with('leId', $leId)
                            ->with('parentPOId', $parentPOId)
                            ->with('allDcs', $allDcs)
                            ->with('allHubs', $allHubs)
                            ->with('allStates', $allStates)
                            ->with('po_id', $id)
                            ->with('packTypes', $packTypes)
                            ->with('is_Supplier', $is_Supplier)
                            ->with('orderId', $orderId);
                            //->with('paymentType', $paymentType)
                            //->with('ledgerAccounts', $ledgerAccounts);
        } catch (Exception $e) {
            Log::error($ex->getMessage() . ' ' . $ex->getTraceAsString());
        }
    }

    public function poDetailsAction($id) {
        try {
            $hasAccess = $this->_roleRepo->checkPermissionByFeatureCode('PO003');
            if ($hasAccess == false) {
                $view = View::make('PurchaseOrder::error');
                $contents = $view->render();
                return Response::json(array('status' => 200, 'message' => $contents));
            }
            $packTypes = $this->_masterLookup->getAllOrderStatus('Levels');
            $poDetailArr = $this->_poModel->getPoDetailById($id);
            if (count($poDetailArr) == 0) {
                return Response::json(array('status' => 400, 'message' => 'Failed'));
            }
            $leWhId = isset($poDetailArr[0]->le_wh_id) ? $poDetailArr[0]->le_wh_id : 0;
            $indentId = isset($poDetailArr[0]->indent_id) ? $poDetailArr[0]->indent_id : 0;
            $leId = isset($poDetailArr[0]->legal_entity_id) ? $poDetailArr[0]->legal_entity_id : 0;
            $poStatusArr = $this->_masterLookup->getAllOrderStatus('PURCHASE_ORDER');
            $po_status = isset($poDetailArr[0]->po_status) ? $poDetailArr[0]->po_status : 0;
            $payment_status = isset($poDetailArr[0]->payment_status) ? $poDetailArr[0]->payment_status : 0;
            $poStatus = isset($poStatusArr[$po_status]) ? $poStatusArr[$po_status] : '';
            $indentCode = '';
            if ($indentId) {
                $indentCode = $this->_indent->getIndentCodeById($indentId);
            }
            $whDetail = $this->_LegalEntity->getWarehouseById($leWhId);
            $userInfo = $this->_poModel->getUserByLeId($leId);
            $supplierInfo = $this->_poModel->getLegalEntityById($leId);
            // checking supplier is global supplier le id = 24766
            $wh_le_id = isset($whDetail->legal_entity_id)?$whDetail->legal_entity_id:0;
            if($leId == 24766){
                $apob_data = $this->_poModel->getApobData($wh_le_id);
                if(count($apob_data)){
                    $supplierInfo = $apob_data;
                }
            }

            $start = date('Y-m-d',strtotime('-30 days'));
            $end = date('Y-m-d');
            $diff = date_diff(date_create($start),date_create($end));
            $daysDiff =  $diff->format("%a") + 1;
            $dateRange = $this->_poModel->dateFunct($start, $end);
            $daysCount = $daysDiff-count($dateRange);

            $taxBreakup = $this->getTaxBreakup($poDetailArr);
            foreach($poDetailArr as $key=>$product){
                $product_id = $product->product_id;
                $newProduct = $this->_poModel->verifyNewProductInWH($leWhId,$product_id);
                if($newProduct==0){
                    $newPrClass = 'class=newproduct';
                }else{
                    $newPrClass = '';
                }
                $product->newPrClass = $newPrClass;
                $pendingRetQty = $this->_poModel->pendingReturns($product_id, $leWhId);
                $openPOQty = $this->_poModel->openPOQty($product_id, $leWhId);
                $net_sold_qty = $this->_poModel->netSoldQty([$product_id], $start.' 00:00:00', $end.' 23:59:59', $leWhId);
                if($net_sold_qty !== 0){
                     $avg_day_sales_eaches = $net_sold_qty / $daysCount;
                } else {
                    $avg_day_sales_eaches = 0.0000;
                }
                $available_inventory = $product->available_inventory;
                if($avg_day_sales_eaches>0){
                    $product->avlble_inv_days = ($available_inventory+$pendingRetQty+$openPOQty)/$avg_day_sales_eaches;
                }else{
                    $product->avlble_inv_days = 0;
                }
                $poDetailArr[$key]=$product;
            }
            $printFeature = $this->_roleRepo->checkPermissionByFeatureCode('PO004');
            $downloadFeature = $this->_roleRepo->checkPermissionByFeatureCode('PO005');
            $editFeature = $this->_roleRepo->checkPermissionByFeatureCode('PO007');
            $featureAccess = array('printFeature'=>$printFeature,'downloadFeature'=>$downloadFeature,'editFeature'=>$editFeature);
            $PaymentTypes = $this->_masterLookup->getAllOrderStatus('Payment Type',[2,3]);
            $payment_type = isset($poDetailArr[0]->payment_type) ? $poDetailArr[0]->payment_type : 0;
            $paymentType = isset($PaymentTypes[$payment_type]) ? $PaymentTypes[$payment_type] : '';

            /*             * * data required for Approval Workflow ** */
            $approval_flow_func = new CommonApprovalFlowFunctionModel();
            if(isset($poDetailArr[0]->approval_status) && $poDetailArr[0]->approval_status != 0 && $poDetailArr[0]->approval_status!=1){
                $status = $poDetailArr[0]->approval_status;
            }else if(isset($poDetailArr[0]->approval_status) && $poDetailArr[0]->approval_status==1){
                $status = 57108;
            }else{
                $status = 57106;
            }
            $res_approval_flow_func = $approval_flow_func->getApprovalFlowDetails('Purchase Order', $status, \Session::get('userId'));
            $current_status = (isset($res_approval_flow_func["currentStatusId"])) ? $res_approval_flow_func["currentStatusId"] : '';
            $approvalOptions = array();
            $approvalVal = array();
            $isApprovalFinalStep = 0;
            $financeStatuses = [57118,57032];
            $acceptStatuses = [57107,57119,57120];
            if (isset($res_approval_flow_func["currentStatusId"]) && isset($res_approval_flow_func["data"])) {
                foreach ($res_approval_flow_func["data"] as $options) {
                    if ($options['isFinalStep'] == 1) {
                        $isApprovalFinalStep = $options['isFinalStep'];
                    }
                    if(in_array($options['nextStatusId'], $financeStatuses)){
                        if($payment_status==57118 && $options['nextStatusId']!=57118){
                            $approvalOptions[$options['nextStatusId'] . ',' . $options['isFinalStep'] . ',' . $options['conditionId']] = $options['condition'];
                        }else if($payment_status==$options['nextStatusId'] || $payment_status==57032){
                        }else{
                            $approvalOptions[$options['nextStatusId'] . ',' . $options['isFinalStep'] . ',' . $options['conditionId']] = $options['condition'];
                        }
                    }else{
                        if(in_array($current_status, $acceptStatuses) && $options['nextStatusId']==57035){
                        }else{
                            $approvalOptions[$options['nextStatusId'] . ',' . $options['isFinalStep'] . ',' . $options['conditionId']] = $options['condition'];
                        }
                    }
                }
            }
            $approvalOptions['57121,0,58060'] = 'Comment';
            $approvalVal = array('current_status' => $current_status,
                'approval_unique_id' => $id,
                'approval_module' => 'Purchase Order',
                'table_name' => 'po',
                'unique_column' => 'po_id',
                'approvalurl' => '/po/approvalSubmit',
            );
            $approvalHistory = $this->_poModel->getApprovalHistory('Purchase Order', $id);
            /*             * * data required for Approval Workflow** */

            $approvalStatus = $this->_masterLookup->getAllOrderStatus('Approval Status');
            $approvedStatus = (isset($approvalStatus[$poDetailArr[0]->approval_status])) ? $approvalStatus[$poDetailArr[0]->approval_status] : '';
            if ($poDetailArr[0]->approval_status == 1) {
                $approvedStatus = 'Shelved';
            }
            $poDocs = $this->_poModel->getpoDocs($id);

            $user_id = Session::get("userId");
            $is_Supplier = $this->_poModel->checkUserIsSupplier($user_id);
            $is_Supplier = count($is_Supplier);
            
            $view = view('PurchaseOrder::Form.poDetail')->with('productArr', $poDetailArr)
                    ->with('supplier', $supplierInfo)
                    ->with('packTypes', $packTypes)
                    ->with('whDetail', $whDetail)
                    ->with('userInfo', $userInfo)
                    ->with('indentCode', $indentCode)
                    ->with('poStatus', $poStatus)
                    ->with('paymentType', $paymentType)
                    ->with('taxBreakup', $taxBreakup)
                    ->with('featureAccess', $featureAccess)
                    ->with('approvedStatus', $approvedStatus)
                    ->with('approvalOptions', $approvalOptions)
                    ->with('approvalVal', $approvalVal)
                    ->with('isApprovalFinalStep', $isApprovalFinalStep)
                    ->with('history', $approvalHistory)
                    ->with('is_Supplier', $is_Supplier)
                    ->with('poDocs',$poDocs);
            $contents = $view->render();
            return Response::json(array('status' => 200, 'message' => $contents));
        } catch (Exception $e) {
            Log::error($ex->getMessage() . ' ' . $ex->getTraceAsString());
        }
    }

    public function getTaxBreakup($products) {
        $finalTaxArr = array();
        $gst_taxes = ['GST','IGST','CGST','SGST','UTGST'];
        if(is_array($products)) {
            $taxKey = 0;
            foreach ($products as $product) {
                $taxName = strtoupper($product->tax_name);
                if(in_array($taxName, $gst_taxes) && $product->tax_data!=''){
                    $tax_data = json_decode($product->tax_data, true);
                    foreach($tax_data as $key=>$val){
                        $cgst = isset($val['CGST'])?$val['CGST']:0;
                        $sgst = isset($val['SGST'])?$val['SGST']:0;
                        $igst = isset($val['IGST'])?$val['IGST']:0;
                        $utgst = isset($val['UTGST'])?$val['UTGST']:0;
                        $cgst_val = ($product->tax_amt*$cgst)/100;
                        $sgst_val = ($product->tax_amt*$sgst)/100;
                        $igst_val = ($product->tax_amt*$igst)/100;
                        $utgst_val = ($product->tax_amt*$utgst)/100;
                        $finalTaxArr['CGST'][] = array('tax'=>'', 'name'=>'CGST', 'tax_amt'=>$cgst_val);
                        $finalTaxArr['SGST'][] = array('tax'=>'', 'name'=>'SGST', 'tax_amt'=>$sgst_val);
                        $finalTaxArr['IGST'][] = array('tax'=>'', 'name'=>'IGST', 'tax_amt'=>$igst_val);
                        $finalTaxArr['UTGST'][] = array('tax'=>'', 'name'=>'UTGST', 'tax_amt'=>$utgst_val);
                    }
                }else{
                    $taxKey = (float)$product->tax_name.'-'.$product->tax_per;
                    if($taxKey != '0-0') {
                        $finalTaxArr[$taxKey][] = array('tax'=>$product->tax_per, 'name'=>$product->tax_name, 'tax_amt'=>$product->tax_amt);
                    }
                }
            }
            $finalNewTaxArr = array();
            foreach ($finalTaxArr as $key => $taxArr) {
              $finalNewTaxArr[$key] = array();
              $totAmt = 0;
              foreach ($taxArr as $tax) {
                $totAmt = $totAmt + $tax['tax_amt'];
                $finalNewTaxArr[$key]['name'] = $tax['name'];
                $finalNewTaxArr[$key]['tax'] = $tax['tax'];
              }

              $finalNewTaxArr[$key]['tax_value'] = $totAmt;
            }
            return $finalNewTaxArr;
        }
    }

    public function editAction($id) {
        try {
            $hasAccess = $this->_roleRepo->checkPermissionByFeatureCode('PO007');
            if ($hasAccess == false) {
                return View::make('PurchaseOrder::error');
            }
            $poDetailArr = $this->_poModel->getPoDetailById($id,'po_product');
            if (count($poDetailArr) == 0) {
                Redirect::to('/po/index')->send();
                die();
            }
           /* if ($poDetailArr[0]->poparentid != 0) {
                Redirect::to('/po/index')->send();
                die();
            }*/
            Session::set('po_id', $id);
            Session::put('podocs', array());
            $leWhId = isset($poDetailArr[0]->le_wh_id) ? $poDetailArr[0]->le_wh_id : 0;
            $indentId = isset($poDetailArr[0]->indent_id) ? $poDetailArr[0]->indent_id : 0;
            $leId = isset($poDetailArr[0]->legal_entity_id) ? $poDetailArr[0]->legal_entity_id : 0;
            $dc_name = isset($poDetailArr[0]->dc_name) ? $poDetailArr[0]->dc_name : 0;
            $st_dc_name = isset($poDetailArr[0]->st_dc_name) ? $poDetailArr[0]->st_dc_name : 0;
            $supply_le_wh_id = isset($poDetailArr[0]->supply_le_wh_id) ? $poDetailArr[0]->supply_le_wh_id : 0;
            $po_so_order_code = isset($poDetailArr[0]->po_so_order_code) ? $poDetailArr[0]->po_so_order_code : 0;
            $stock_transfer_dc = isset($poDetailArr[0]->stock_transfer_dc) ? $poDetailArr[0]->stock_transfer_dc : 0;
            $legal_entity_id = \Session::get('legal_entity_id');
            $warehouseList = $this->_LegalEntity->getWarehouseBySupplierId($legal_entity_id);
            $indentCode = '';
            if ($indentId) {
                $indentCode = $this->_indent->getIndentCodeById($indentId);
            }

            $whDetail = $this->_LegalEntity->getWarehouseById($leWhId);
            $userInfo = $this->_poModel->getUserByLeId($leId);
            $supplierInfo = $this->_poModel->getLegalEntityById($leId);
            $taxArr = $this->getTaxInfo($poDetailArr);
            $uom = array();
            $freeuom = array();
            $inwardProductCount = $this->_poModel->getInwardProductsCountByPOId($id);
            $poDetailArr1 = array();
            $legalEntityId = Session::get('legal_entity_id');
            $suppliers_list = $this->_poModel->getSuppliersforIndents(array("indent_id"=>0));

            $le_le_wh_id = DB::SELECT(DB::raw("SELECT lewh.le_wh_id FROM legalentity_warehouses AS lewh WHERE lewh .`legal_entity_id` = $leId
                            AND lewh.dc_type=118001"));
            $le_le_wh_id = isset($le_le_wh_id[0]->le_wh_id)?$le_le_wh_id[0]->le_wh_id:0;
            foreach($poDetailArr as $key=>$products){
                $product_id = $products->product_id;
                $InwardQty = (isset($inwardProductCount[$product_id]))?$inwardProductCount[$product_id]:0;
                $poQty = $products->qty*$products->no_of_eaches;
                if ($poQty>0 && $poQty == $InwardQty) {
                    unset($poDetailArr[$key]);
                }else{
                    $packuom = $products->uom;
                    $free_packuom = $products->free_uom;
                    $packs = $this->_poModel->getProductPackInfo($product_id);
                    $uom[$product_id] = '';
                    $freeuom[$product_id] = '';
                    foreach($packs as $pack){
                        $uom_selected = ($pack->level == $packuom && $pack->no_of_eaches == $products->no_of_eaches) ? 'selected="selected"':'';
                        $freeuom_selected = ($pack->level == $free_packuom && $pack->no_of_eaches == $products->free_eaches) ? 'selected="selected"':'';
                        $uom[$product_id] .= '<option value="'.$pack->pack_id.'" '.$uom_selected.' data-noofeach="'.$pack->no_of_eaches.'">'.$pack->packname.'('.$pack->no_of_eaches.')</option>';
                        $freeuom[$product_id] .= '<option value="'.$pack->pack_id.'" '.$freeuom_selected.' data-noofeach="'.$pack->no_of_eaches.'">'.$pack->packname.'('.$pack->no_of_eaches.')</option>';
                    }
                    if($le_le_wh_id !=0){
                        $current_soh = $this->_poModel->checkInventory($product_id,$le_le_wh_id);
                        $noe= DB::select(DB::raw("SELECT SUM(pop.`qty`*pop.`no_of_eaches`) AS noe FROM po_products AS pop JOIN po AS po  ON po.po_id=pop.po_id WHERE pop.product_id=$product_id AND po.`po_status`=87001 AND po.legal_entity_id=$leId"));
                        $final_soh = $current_soh - $noe[0]->noe;
                       
                    }else{
                        $final_soh = 0;
                    }
                    $products->final_soh = $final_soh;
                    $poDetailArr1[] = $products;
                }
                $po_status = isset($poDetailArr[$key]->po_status) ? $poDetailArr[$key]->po_status : 0;
                $approval_status = isset($poDetailArr[$key]->approval_status) ? $poDetailArr[$key]->approval_status : 0;
            }
            $closeFeature = $this->_roleRepo->checkPermissionByFeatureCode('PO009');
            $updatePaymentFeature = $this->_roleRepo->checkPermissionByFeatureCode('PO0010');
            $po_so_update_ftr = $this->_roleRepo->checkPermissionByFeatureCode('PO0012');
            $featureAccess = array('closeFeature'=>$closeFeature,'updatePaymentFeature'=>$updatePaymentFeature,"poSOupdateFeature"=>$po_so_update_ftr);
            if(count($poDetailArr1)<=0){
                Redirect::to('/po/index')->send();
                die();
            }
            $ledgerAccounts = $this->_poModel->getTallyLedgerAccounts();
            $paymentType = $this->_masterLookup->getAllOrderStatus('Payment Type',[2,3]);
            $poDocs = $this->_poModel->getpoDocs($id);

            $poStatusArr = $this->_masterLookup->getAllOrderStatus('PURCHASE_ORDER');
            $poStatus = isset($poStatusArr[$po_status]) ? $poStatusArr[$po_status] : '';

            $approvalStatus = $this->_masterLookup->getAllOrderStatus('Approval Status');
            $approvedStatus = (isset($approvalStatus[$approval_status])) ? $approvalStatus[$approval_status] : '';
            if ($approval_status == 1) {
                $approvedStatus = 'Approved';
            }
            return view('PurchaseOrder::edit')->with('productArr', $poDetailArr1)
                            ->with('supplier', $supplierInfo)
                            ->with('whDetail', $whDetail)
                            ->with('userInfo', $userInfo)
                            ->with('indentCode', $indentCode)
                            ->with('uom', $uom)
                            ->with('freeuom', $freeuom)
                            ->with('taxArr', $taxArr)
                            ->with('featureAccess', $featureAccess)
                            ->with('paymentType', $paymentType)
                            ->with('ledgerAccounts', $ledgerAccounts)
                            ->with('poDocs', $poDocs)
                            ->with('po_status', $poStatus)
                            ->with('approvedStatus', $approvedStatus)
                            ->with('suppliers_list', $suppliers_list)
                            ->with('supply_dc_name', $dc_name)
                            ->with('st_dc_name', $st_dc_name)
                            ->with('supply_le_wh_id', $supply_le_wh_id)
                            ->with('po_so_order_code', $po_so_order_code)
                            ->with('stock_transfer_dc', $stock_transfer_dc)
                            ->with('warehouseList', $warehouseList);
        } catch (Exception $e) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }

    /**
	 * [downloadPOAction description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */

    public function downloadPOAction($id, $forEmail = 0) {
        try {
            $hasAccess = $this->_roleRepo->checkPermissionByFeatureCode('PO005');
            if ($hasAccess == false && $forEmail == 0) {
                return View::make('PurchaseOrder::error');
            }
            $poDetailArr = $this->_poModel->getPoDetailById($id);

            if (count($poDetailArr) == 0) {
                Redirect::to('/po/index')->send();
                die();
            }
            $packTypes = $this->_masterLookup->getAllOrderStatus('Levels');
            $leWhId = isset($poDetailArr[0]->le_wh_id) ? $poDetailArr[0]->le_wh_id : 0;
            $indentId = isset($poDetailArr[0]->indent_id) ? $poDetailArr[0]->indent_id : 0;
            $leId = isset($poDetailArr[0]->legal_entity_id) ? $poDetailArr[0]->legal_entity_id : 0;
            $indentCode = '';
            if ($indentId) {
                $indentCode = $this->_indent->getIndentCodeById($indentId);
            }

            $whDetail = $this->_LegalEntity->getWarehouseById($leWhId);
            $leParentId = $this->_LegalEntity->getLeParentIdByLeId($leId);
            $leDetail = $this->_LegalEntity->getLegalEntityById($leParentId);
            if($leParentId)
                $leDetail = $this->_LegalEntity->getLegalEntityById($leParentId);
            else{
                $leDetail = $this->_LegalEntity->getLegalEntityById($whDetail->legal_entity_id);
            }
            $userInfo = $this->_poModel->getUserByLeId($leId);
            $supplierInfo = $this->_poModel->getLegalEntityById($leId);

            // checking supplier is global supplier le id = 24766
            $wh_le_id = isset($whDetail->legal_entity_id)?$whDetail->legal_entity_id:0;
            if($leId == 24766){
                $apob_data = $this->_poModel->getApobData($wh_le_id);
                if(count($apob_data)){
                    $supplierInfo = $apob_data;
                }
            }


            $wh_state = isset($whDetail->state)?$whDetail->state:0;
            $check_apob = $this->_LegalEntity->checkisApob($leWhId);
            if($wh_state > 0 && $check_apob){
                $wh_state_data = $this->_LegalEntity->getStateBillingDC($wh_state);
                if(count($wh_state_data)){
                    $leDetail = $this->_LegalEntity->getWarehouseById($wh_state_data->le_wh_id);
                }
            }
            //print_r($supplierInfo);die;
            $taxArr = $this->getTaxInfo($poDetailArr);
            $companyInfo = $this->_LegalEntity->getCompanyAccountByLeId($leParentId);
            foreach($poDetailArr as $key=>$product){
                $product_id = $product->product_id;
                $newProduct = $this->_poModel->verifyNewProductInWH($leWhId,$product_id);
                if($newProduct==0){
                    $newPrClass = 'style=color:blue;font-weight:bold;';
                }else{
                    $newPrClass = '';
                }
                $product->newPrClass = $newPrClass;
                $poDetailArr[$key]=$product;
            }
            $PaymentTypes = $this->_masterLookup->getAllOrderStatus('Payment Type',[2,3]);
            $payment_type = isset($poDetailArr[0]->payment_type) ? $poDetailArr[0]->payment_type : 0;
            $paymentType = isset($PaymentTypes[$payment_type]) ? $PaymentTypes[$payment_type] : '';

            $poStatusArr = $this->_masterLookup->getAllOrderStatus('PURCHASE_ORDER');
            $po_status = isset($poDetailArr[0]->po_status) ? $poDetailArr[0]->po_status : 0;
            $poStatus = isset($poStatusArr[$po_status]) ? $poStatusArr[$po_status] : '';
            $approvalStatus = $this->_masterLookup->getAllOrderStatus('Approval Status');
            $approvedStatus = (isset($approvalStatus[$poDetailArr[0]->approval_status])) ? $approvalStatus[$poDetailArr[0]->approval_status] : '';
            if ($poDetailArr[0]->approval_status == 1) {
                $approvedStatus = 'Approved';
            }
            $taxBreakup = $this->getTaxBreakup($poDetailArr);
            $taxPerr = '';
            foreach($taxBreakup as $tax1){
                $taxPerr = ($taxPerr=='')?$tax1['tax']:$taxPerr;
            }
            $template = 'downloadPO';
            if($taxPerr=='') {
                $template = 'downloadPO_gst';
            }
            $data = array('supplier' => $supplierInfo, 'productArr' => $poDetailArr, 'taxArr' => $taxArr
                , 'packTypes' => $packTypes, 'whDetail' => $whDetail, 'userInfo' => $userInfo,
                'indentCode' => $indentCode, 'leDetail' => $leDetail, 'companyInfo' => $companyInfo,
                         'paymentType'=>$paymentType,'poStatus'=>$poStatus,'approvedStatus'=>$approvedStatus,'taxBreakup'=>$taxBreakup);
            // return view('PurchaseOrder::'.$template, $data);
            $pdf = PDF::loadView('PurchaseOrder::'.$template, $data);
            return $pdf->download('po_' . $id . '.pdf');
        } catch (Exception $e) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }

    /**
	 * [printPoAction description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */

    public function printPoAction($id,$type=0) {

        try {

            $hasAccess = $this->_roleRepo->checkPermissionByFeatureCode('PO004');
            if ($hasAccess == false) {
                return View::make('PurchaseOrder::error');
            }


            $poDetailArr = $this->_poModel->getPoDetailById($id);

            if (count($poDetailArr) == 0) {
                Redirect::to('/po/index')->send();
                die();
            }
            $packTypes = $this->_masterLookup->getAllOrderStatus('Levels');
            $productTaxArr = array();

            $leWhId = isset($poDetailArr[0]->le_wh_id) ? $poDetailArr[0]->le_wh_id : 0;
            $indentId = isset($poDetailArr[0]->indent_id) ? $poDetailArr[0]->indent_id : 0;
            $leId = isset($poDetailArr[0]->legal_entity_id) ? $poDetailArr[0]->legal_entity_id : 0;

            $indentCode = '';
            if ($indentId) {
                $indentCode = $this->_indent->getIndentCodeById($indentId);
            }

            // company admin id
            $whDetail = $this->_LegalEntity->getWarehouseById($leWhId);
            $leParentId = $this->_LegalEntity->getLeParentIdByLeId($leId);
            if($leParentId)
                $leDetail = $this->_LegalEntity->getLegalEntityById($leParentId);
            else{
                $leDetail = $this->_LegalEntity->getLegalEntityById($whDetail->legal_entity_id);
            }

            $wh_state = isset($whDetail->state)?$whDetail->state:0;
            $check_apob = $this->_LegalEntity->checkisApob($leWhId);
            if($wh_state > 0 && $check_apob){
                $wh_state_data = $this->_LegalEntity->getStateBillingDC($wh_state);
                if(count($wh_state_data)){
                    $leDetail = $this->_LegalEntity->getWarehouseById($wh_state_data->le_wh_id);
                }
            }
            $userInfo = $this->_poModel->getUserByLeId($leId);
            $supplierInfo = $this->_poModel->getLegalEntityById($leId);
            $companyInfo = $this->_LegalEntity->getCompanyAccountByLeId($leParentId);

            // checking supplier is global supplier le id = 24766
            $wh_le_id = isset($whDetail->legal_entity_id)?$whDetail->legal_entity_id:0;
            if($leId == 24766){
                $apob_data = $this->_poModel->getApobData($wh_le_id);
                if(count($apob_data)){
                    $supplierInfo = $apob_data;
                }
            }
            
            foreach ($poDetailArr as $key => $product) {
                $product_id = $product->product_id;
                $newProduct = $this->_poModel->verifyNewProductInWH($leWhId, $product_id);
                if ($newProduct == 0) {
                    $newPrClass = 'class=newproduct';
                } else {
                    $newPrClass = '';
                }
                $product->newPrClass = $newPrClass;
                $poDetailArr[$key] = $product;
            }
            $PaymentTypes = $this->_masterLookup->getAllOrderStatus('Payment Type',[2,3]);
            $payment_type = isset($poDetailArr[0]->payment_type) ? $poDetailArr[0]->payment_type : 0;
            $paymentType = isset($PaymentTypes[$payment_type]) ? $PaymentTypes[$payment_type] : '';

            $poStatusArr = $this->_masterLookup->getAllOrderStatus('PURCHASE_ORDER');
            $po_status = isset($poDetailArr[0]->po_status) ? $poDetailArr[0]->po_status : 0;
            $poStatus = isset($poStatusArr[$po_status]) ? $poStatusArr[$po_status] : '';
            $approvalStatus = $this->_masterLookup->getAllOrderStatus('Approval Status');
            $approvedStatus = (isset($approvalStatus[$poDetailArr[0]->approval_status])) ? $approvalStatus[$poDetailArr[0]->approval_status] : '';
            if ($poDetailArr[0]->approval_status == 1) {
                $approvedStatus = 'Approved';
            }
            $taxBreakup = $this->getTaxBreakup($poDetailArr);
            $taxPerr = '';
            foreach($taxBreakup as $tax1){
                $taxPerr = ($taxPerr=='')?$tax1['tax']:$taxPerr;
            }
            $template = 'printpo';
            if($taxPerr=='') {
                $template = 'printpo_gst';
            }
            if($type == 1){
                $template = "printpo_st";
            }
            return view('PurchaseOrder::'.$template)->with('productArr', $poDetailArr)
                            ->with('packTypes', $packTypes)
                            ->with('supplier', $supplierInfo)
                            ->with('leDetail', $leDetail)
                            ->with('whDetail', $whDetail)
                            ->with('userInfo', $userInfo)
                            ->with('companyInfo', $companyInfo)
                            ->with('paymentType', $paymentType)
                            ->with('poStatus', $poStatus)
                            ->with('approvedStatus', $approvedStatus)
                            ->with('taxBreakup', $taxBreakup)
                            ->with('indentCode', $indentCode);
            // ->with('taxArr', $taxArr);
        } catch (Exception $e) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }

    public function createAction(Request $request)
    {
        try{
            $hasAccess = $this->_roleRepo->checkPermissionByFeatureCode('PO002');
            if($hasAccess == false) {
                return View::make('PurchaseOrder::error');
            }
            Session::put('podocs', array());
            Session::set("po_credit_message","");
            $indent_id = $this->_roleRepo->decodeData($request->input('indentid'));  //optional
            // echo $indent_id;die;
            $purchaseOrder = new PurchaseOrder();
            $poData = $purchaseOrder->getCreatePoData();
            $reasonsArr = $purchaseOrder->getRemarkReasons(0);
            $ledgerAccounts = $purchaseOrder->getTallyLedgerAccounts();
            $paymentType = $this->_masterLookup->getAllOrderStatus('Payment Type',[2,3]);
            $updatePaymentFeature = $this->_roleRepo->checkPermissionByFeatureCode('PO0010');
            $featureAccess = array('updatePaymentFeature'=>$updatePaymentFeature);
            return view('PurchaseOrder::create')->with($poData)
                                                ->with('reasonsArr', $reasonsArr)
                                                ->with('paymentType', $paymentType)
                                                ->with('featureAccess', $featureAccess)
                                                ->with('indentId', $indent_id)
                                                ->with('ledgerAccounts', $ledgerAccounts);
        }
        catch(\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }

    public function getReasonAction() {
        $reasonId = Input::get('reasonId');
        $purchaseOrder = new PurchaseOrder();
        $reasonsArr = $purchaseOrder->getRemarkReasonsById($reasonId);
        $remarks = '';
        foreach ($reasonsArr as $reason) {
            $remarks .= $reason->description."\n";
        }
        return Response::json(array('remarks'=>$remarks));
    }

    public function getSuppliersAction()
    {
        try{
            $supplierData = [];
            $data = \Input::all();
            $supplierData = $this->_poModel->getSuppliers($data);
            $addfrom = isset($data['addfrom'])?$data['addfrom']:'';
            $productList='';
            $sno = 1;
            if (isset($supplierData['products']) && is_array($supplierData['products']) && count($supplierData['products']) > 0) {
                foreach ($supplierData['products'] as $product) {
                    $product_id = $product->product_id;
                    $supplier_id = isset($product->legal_entity_id) ? $product->legal_entity_id : '';
                    $warehouse_id = isset($product->le_wh_id) ? $product->le_wh_id : '';
                    $prodsubscribed = isset($product->subscribe) ? $product->subscribe : '';
                    if($prodsubscribed=="") {
                        $this->subscribeProducts($supplier_id,$warehouse_id,$product_id);
                    } else if($prodsubscribed==0){
                        $product_tot = ['subscribe'=>1];
                        $this->_poModel->updateProductTot($product_tot,$supplier_id,$warehouse_id,$product_id);
                    }
                    $indent_id = isset($product->indent_id) ? $product->indent_id : '';
                    $freebieParent = $this->_poModel->getFreebieParent($product_id);
                    $parent_id = (isset($freebieParent->main_prd_id)) ? $freebieParent->main_prd_id : 0;

                    $totPoQty = $this->_poModel->getPoProductQtyByIndentId($indent_id,$product_id);
                    $objIndent = new IndentModel();
                    $totIndentQty = (int)$objIndent->getIndentProductQtyById($indent_id,$product_id);
                    if($totPoQty < $totIndentQty){

                        $productTextArr = $this->getPOProductRow($product_id, $parent_id, $supplier_id, $warehouse_id, $addfrom,$indent_id);
                    }else{
                        $productTextArr['productList'] = '';
                    }
                    $productList.=(isset($productTextArr['productList'])) ? $productTextArr['productList'] : '';
                }
            }
            $supplierData['productList'] = $productList;
        }
        catch(\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
        return json_encode($supplierData);
    }

    /**
     * [savePurchaseOrderAction description]
     * @return [type] [description]
     */

    public function savePurchaseOrderAction($data=array())
    {
        try{
            if(empty($data) && count($data)==0){
                $data = Input::all();
            }
            if(empty($data))
            {
            	return json_encode(array('status'=>400, 'message'=>Lang::get('salesorders.errorInputData'), 'po_id'=>''));
            }
            else if(empty($data['supplier_list']) || empty($data['warehouse_list'])) {
            	return json_encode(array('status'=>400, 'message'=>Lang::get('po.alertWH'), 'po_id'=>''));
            }
            else if(!isset($data['po_product_id'])) {
            	return json_encode(array('status'=>400, 'message'=>'Please select products', 'po_id'=>''));
            }
            else {
            	$purchaseOrder = new PurchaseOrder();
                $supplier_id = isset($data['supplier_list']) ? $data['supplier_list'] : 0;
                $checkSupplier = $purchaseOrder->checkSupplier($supplier_id);
                if(is_array($checkSupplier) && count($checkSupplier)==0){
                    return json_encode(array('status'=>400, 'message'=>'Please check supplier is not Active/Approved', 'po_id'=>''));
                }
                //check order limit to be greater than purchase order i.e purchase order should not exceed order limit
                $productInfo = isset($data['po_product_id']) ? $data['po_product_id'] : [];
                $warehouse_id = isset($data['warehouse_list']) ? $data['warehouse_list'] : 0;
                $supply_le_wh_id = isset($data['supply_le_wh_id']) ? $data['supply_le_wh_id'] : 0;
                $poDetails['le_wh_id'] = $warehouse_id;
                $packsize = $data['packsize'];
                $st_warehouse_id = isset($data['st_warehouse_id']) ? $data['st_warehouse_id'] : 0;
                $stock_transfer = isset($data['stock_transfer']) ? $data['stock_transfer'] : 0;
                if($stock_transfer > 0){

                    $product_inv_ids = array();
                    $inventoryData = '<tr class="subhead">
                                        <th width="66%" align="left" valign="middle">Product Name (SKU) </th>
                                        <th width="17%" align="left" valign="middle">Avail Qty</th>
                                        <th width="17%" align="left" valign="middle">PO Qty</th>
                                    </tr>';
                    if($supplier_id != 24766){
                        return json_encode(array('status'=>400, 'message'=>'Please select "Ebutor Supplier" to transfer stock', 'po_id'=>''));
                    }

                    if($st_warehouse_id == $warehouse_id){
                        return json_encode(array('status'=>400, 'message'=>'"Stock Transfer Location" and "Delivery Location" should not same to transfer stock', 'po_id'=>''));
                    }
                    if($supply_le_wh_id > 0){
                        return json_encode(array('status'=>400, 'message'=>'Please uncheck "Stock Transfer" to Select "DC Supply".', 'po_id'=>''));
                    }
                    if($st_warehouse_id == "" || $st_warehouse_id == 0){
                        return json_encode(array('status'=>400, 'message'=>'Please select "Stock Transfer Location" to transfer stock', 'po_id'=>''));
                    }
                    $whDetail = $this->_LegalEntity->getWarehouseById($warehouse_id);
                    $whDetailTypeId = isset($whDetail->legal_entity_type_id) ? $whDetail->legal_entity_type_id : 0;
                    $stWhDetail = $this->_LegalEntity->getWarehouseById($st_warehouse_id);
                    $stWhDetailTypeId = isset($stWhDetail->legal_entity_type_id) ? $stWhDetail->legal_entity_type_id : 0;
                    if($whDetailTypeId != 1001 || $stWhDetailTypeId != 1001){
                        return json_encode(array('status'=>400, 'message'=>'"Dispatch Location" and "Delivery Location" should be "APOB" to transfer stock.', 'po_id'=>''));
                    }

                    $whDetailStateId = isset($whDetail->state) ? $whDetail->state : 0;
                    $stWhDetailStateId = isset($stWhDetail->state) ? $stWhDetail->state : 0;
                    if($whDetailStateId != $stWhDetailStateId){
                        return json_encode(array('status'=>400, 'message'=>'"Dispatch Location" and "Delivery Location" should be in same state to transfer stock.', 'po_id'=>''));
                    }

                }else if($st_warehouse_id > 0){
                    return json_encode(array('status'=>400, 'message'=>'Please check "Stock Transfer" to transfer stock.', 'po_id'=>''));
                }
               
                
                $getleidfordcid=$this->_reportsrepo->getlegalidbasedondcid($poDetails['le_wh_id']);
                $getleidfordcid=json_decode(json_encode($getleidfordcid),True);
                $is_self_tax=$purchaseOrder->checkIsSelfTax($getleidfordcid['legal_entity_id']);
                
                $is_self_tax=json_decode(json_encode($is_self_tax),True);
                $checkorderlimitwith_po=false;
                $qty=0;
                $no_of_eaches=0;
                $cur_elp=0;
                $poamount=0;
                // if($is_self_tax[0]['is_self_tax']==0){
                if(!empty($productInfo))
                {
                    
                    foreach($productInfo as $key=>$product_id)
                    {
                        $po_product = array();
                        
                        $po_product['qty'] = (isset($data['qty'][$key]))?$data['qty'][$key]:1;
                        $qty=$po_product['qty'];
                        //free eaches
                        $pack_id = (isset($packsize[$key]) && $packsize[$key]!='')?$packsize[$key]:'';
                        $uomPackinfo = $purchaseOrder->getProductPackUOMInfoById($pack_id);
                        $po_product['no_of_eaches'] = (isset($uomPackinfo->no_of_eaches))?$uomPackinfo->no_of_eaches:0;
                        $no_of_eaches=$po_product['no_of_eaches'];
                        $po_product['cur_elp'] = (isset($data['curelpval'][$product_id]))?$data['curelpval'][$product_id]:0;
                        $cur_elp=$po_product['cur_elp'];
                        $poamount=$poamount+($qty*$no_of_eaches*$cur_elp);

                        $tax_name = (isset($data['po_taxname'][$product_id]) && $data['po_taxname'][$product_id] != '')?$data['po_taxname'][$product_id]:'';
//                        Log::info($tax_name." -- ".strpos($tax_name, ','));
                        $tax_name_arr = explode(",",$tax_name);
                        if(count($tax_name_arr)>1 || strpos($tax_name, ',') !== false){
                            return json_encode(array('status'=>400, 'message'=>'Tax Info Error For Product Id:'.$product_id,'po_id'=>'',"product_id"=>$product_id));
                        }

                        // stock transfer inventory check
                        if($stock_transfer > 0){
                            $productdet = $this->_poModel->getProductdetails($product_id);
                            $product_title = isset($productdet->product_title)?$productdet->product_title:0;
                            $sku = isset($productdet->sku)?$productdet->sku:0;
                            $availQty = $this->_poModel->checkInventory($product_id,$st_warehouse_id);
                            $poProductQty = $po_product['no_of_eaches'] * $po_product['qty'];
                            if($availQty < $poProductQty){
                                array_push($product_inv_ids, $product_id);
                                $inventoryData .= '<tr class="subhead priceerrorname">
                                                    <td align="left" valign="middle"><b>'.$product_title.' <span style="color:blue"><b>('.$sku.')</b></span></b></td>
                                                    <td style="color:red" align="left" valign="middle">'.$availQty.'</td>
                                                    <td align="left" valign="middle">'.$poProductQty.'</td>
                                                        </tr>';
                            }
                        }
                    }
                    if($stock_transfer > 0)
                        if(count($product_inv_ids)){
                            return json_encode(["status" => 'failure',"reason" => "No Inventory to transfer stock!", "message" => "inv_error_found","adjust_message"=>"Add or Remove for No Inventory Products",'data'=>$inventoryData]);
                        }
                   
                }
            // }
            $dcleid = (isset($data['dc_warehouse_id']))?$data['dc_warehouse_id']:0;
            //echo $poamount;exit;

            if($dcleid == ""){
                $dcleid = $poDetails['le_wh_id'];
            }
            $checkLOC = $purchaseOrder->checkLOCByLeWhid($dcleid);
            // print_r($checkLOC);exit;
            if($poamount>$checkLOC){
                $contact_data = $this->_poModel->getLEWHById($dcleid);
                $credit_limit_check = $contact_data->credit_limit_check;
                if($credit_limit_check == 1 ){
                    /*return ["status" => 400, 'message' => 'PO is greater than order limit,PO cannot be placed.','data'=>''];*/
                    $whDetail = $this->_LegalEntity->getWarehouseById($dcleid);
                    $display_name = $whDetail->display_name;
                    if(Session::get("po_credit_message") != $dcleid || Session::get("po_product_count") !=count($productInfo)){
                        Session::set("po_credit_message",$dcleid);
                        Session::set("po_product_count",count($productInfo));
                        return json_encode(array('status'=>400, 'message'=>'PO value is greaterthan order limit, PO cannot be placed. Current order limit for '.$display_name.' is Rs '.$checkLOC, 'po_id'=>''));
                    }
                }
            }
              //  Log::info("po data");
               // Log::info($data);
                //end of checking purchase order limit
                $saveData = $purchaseOrder->savePurchaseOrderData($data);
                if($saveData['status'] == 400){
                    return json_encode(array('status'=>400, 'message'=>$saveData['message'], 'po_id'=>''));
                }
                $serialNumber = $saveData['serialNumber'];
                $poId = $saveData['po_id'];
                /**
                * default approval status
                */
                $approval_flow_func = new CommonApprovalFlowFunctionModel();
                $created_by = (isset($data['created_by']) && $data['created_by']!='')?$data['created_by']:\Session::get('userId');
                $res_approval_flow_func = $approval_flow_func->getApprovalFlowDetails('Purchase Order', '57106', $created_by);
                if (isset($res_approval_flow_func["currentStatusId"]) && isset($res_approval_flow_func["data"])) {
                    $current_status_id = $res_approval_flow_func["currentStatusId"];
                    $next_status_id = $res_approval_flow_func["data"][0]["nextStatusId"];
                    $table = 'po';
                    $unique_column = 'po_id';
                    // TODO: Basant
                    $purchaseOrder->updateStatusAWF($table, $unique_column, $poId, $next_status_id . ",0");
                    $appr_comment = (isset($data['approval_comments']))?$data['approval_comments']:'System approval at the time of insertion';
                    $approval_flow_func->storeWorkFlowHistory('Purchase Order', $poId, $current_status_id, $next_status_id, $appr_comment, $created_by);
                }

                Notifications::addNotification(['note_code' => 'PO001','note_message'=>'PO #POID Created Successfully', 'note_priority' => 1, 'note_type' => 1, 'note_params' => ['POID' => $serialNumber], 'note_link' => '/po/details/'.$poId]);
                $mailData['subject'] = 'New PO#'.$serialNumber.' Created';
                $mailData['message'] = '<p>New PO created!</p>';
                $subject = $mailData['subject'];//;
                $this->emailWithAttachment($poId,$serialNumber,$mailData);
                $this->poDocUpdate($poId);
                $indentId = isset($data['indent_id']) ? (int)$data['indent_id'] : 0;
                if($indentId > 0) {
                    $totPoQty = $purchaseOrder->getPoQtyByIndentId($indentId);
                    $objIndent = new IndentModel();
                    $totIndentQty = (int)$objIndent->getIndentQtyById($indentId);
                    $indent_status = ($totPoQty >= $totIndentQty) ? 70002 : 70001;
                    //$indent_status = 70002;
                    $objIndent->updateIndent($indentId, array('indent_status'=>$indent_status));
                }
            	return json_encode($saveData);
            }
        }
        catch(\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }
    public function emailWithAttachment($poId,$po_code,$mailData) {

        try{
            $purchaseOrder = new PurchaseOrder();
            $instance = env('MAIL_ENV');
            $subject = $instance.$mailData['subject'];
            //$pdf = $this->downloadPOAction($poId,1);
            $body['attachment'] = array('nameSpace' => '\App\Modules\PurchaseOrder\Controllers\PurchaseOrderController','functionName'=>'downloadPOAction','args'=>array($poId,1));
            $body['file_name'] = 'PO_'.$po_code.'.pdf';
            $body['template'] = 'emails.po';
            $body['name'] = 'Hello All';
            $body['comment'] = $mailData['message'];

            $notificationObj= new NotificationsModel();
            $userIdData= $notificationObj->getUsersByCode('PO001');
            $userIdData=json_decode(json_encode($userIdData));
            $userEmailArr = $purchaseOrder->getUserEmailByIds($userIdData);
            $toEmails = array();
            if(is_array($userEmailArr) && count($userEmailArr) > 0) {
                foreach($userEmailArr as $userData){
                    $toEmails[] = $userData['email_id'];
                }
            }
            Utility::sendEmail($toEmails, $subject, $body);
        } catch (Exception $ex) {

        }
    }
    /**
     * [updatePOAction description]
     * @return [type] [description]
     */

    public function updatePOAction($data=array())
    {
        try{
            if(empty($data) && count($data)==0){
                $data = Input::all();
            }
            if(empty($data))
            {
            	return json_encode(array('status'=>400, 'message'=>Lang::get('salesorders.errorInputData'), 'po_id'=>''));
            }
            else if(!isset($data['po_product_id'])) {
            	return json_encode(array('status'=>400, 'message'=>'Please select products', 'po_id'=>''));
            }
            else {
            	$poId = $data['po_id'];
                if($poId)
                {
                    $productInfo = isset($data['po_product_id']) ? $data['po_product_id'] : [];
                    $supplier_id = isset($data['supplier_id']) ? $data['supplier_id'] : 0;
                    $warehouse_id = isset($data['warehouse_id']) ? $data['warehouse_id'] : 0;
                    $stock_transfer = isset($data['stock_transfer']) ? $data['stock_transfer'] : 0;
                    $supply_le_wh_id = isset($data['supply_le_wh_id']) ? $data['supply_le_wh_id'] : 0;
                    $stock_transfer_dc = isset($data['stock_transfer_dc']) ? $data['stock_transfer_dc'] : 0;
                    $po_so_order_code = isset($data['po_so_order_code']) ? $data['po_so_order_code'] : "";
                    if($stock_transfer == 1 ){
                        if($po_so_order_code != "" && $po_so_order_code != 0){
                            return json_encode(array('status'=>400, 'message'=>'PO has SO,It cannot be updated as Stock Transfer PO', 'po_id'=>$poId));
                        }
                        if($stock_transfer_dc == ""){
                            return json_encode(array('status'=>400, 'message'=>'Please select Stock Transfer Location! ', 'po_id'=>$poId));
                        }
                    }

                    if($stock_transfer > 0){

                        if($supplier_id != 24766){
                            return json_encode(array('status'=>400, 'message'=>'Please select "Ebutor Supplier" to transfer stock', 'po_id'=>''));
                        }
                        if($stock_transfer_dc == $warehouse_id){
                            return json_encode(array('status'=>400, 'message'=>'"Stock Transfer Location" and "Delivery Location" should not same to transfer stock', 'po_id'=>''));
                        }
                        if($supply_le_wh_id > 0){
                            return json_encode(array('status'=>400, 'message'=>'Please uncheck "Stock Transfer" to Select "DC Supply".', 'po_id'=>''));
                        }
                        if($stock_transfer_dc == "" || $stock_transfer_dc == 0){
                            return json_encode(array('status'=>400, 'message'=>'Please select "Stock Transfer Location" to transfer stock', 'po_id'=>''));
                        }
                        $whDetail = $this->_LegalEntity->getWarehouseById($warehouse_id);
                        $whDetailTypeId = isset($whDetail->legal_entity_type_id) ? $whDetail->legal_entity_type_id : 0;
                        $stWhDetail = $this->_LegalEntity->getWarehouseById($stock_transfer_dc);
                        $stWhDetailTypeId = isset($stWhDetail->legal_entity_type_id) ? $stWhDetail->legal_entity_type_id : 0;
                        if($whDetailTypeId != 1001 || $stWhDetailTypeId != 1001){
                            return json_encode(array('status'=>400, 'message'=>'"Dispatch Location" and "Delivery Location" should be "APOB" to transfer stock.', 'po_id'=>''));
                        }

                        $whDetailStateId = isset($whDetail->state) ? $whDetail->state : 0;
                        $stWhDetailStateId = isset($stWhDetail->state) ? $stWhDetail->state : 0;
                        if($whDetailStateId != $stWhDetailStateId){
                            return json_encode(array('status'=>400, 'message'=>'"Dispatch Location" and "Delivery Location" should be in same state to transfer stock.', 'po_id'=>''));
                        }
                        $product_inv_ids = array();
                        $inventoryData = '<tr class="subhead">
                                            <th width="66%" align="left" valign="middle">Product Name (SKU) </th>
                                            <th width="17%" align="left" valign="middle">Avail Qty</th>
                                            <th width="17%" align="left" valign="middle">PO Qty</th>
                                        </tr>';

                    }else if($stock_transfer_dc > 0){
                        return json_encode(array('status'=>400, 'message'=>'Please check "Stock Transfer" to transfer stock.', 'po_id'=>''));
                    }
                    $po_type = isset($data['po_type']) ? $data['po_type'] : 0;
                    $packsize = $data['packsize'];
                    $mailMsg = '';
                    if(isset($data['delete_product']) && !empty($data['delete_product']))
                    {
                        foreach($data['delete_product'] as $key=>$delproduct_id)
                        {
                            $product = $this->_poModel->getProductInfoByID($delproduct_id,$supplier_id,$warehouse_id);
                            $product = json_decode(json_encode($product),true);
                            $preDeleteData = $this->_poModel->getPreUpdatePOProducts($poId,$delproduct_id);
                            $tax_per = isset($preDeleteData['tax_per'])?$preDeleteData['tax_per']:0;
                            $this->_poModel->deletePoProducts($poId,$delproduct_id);
//                            $this->_poModel->updateElp($poId,$delproduct_id,$supplier_id,$warehouse_id,$tax_per);
                            $mailMsg .= '<p><strong>'.$product['pname'].' Deleted</strong></p>';
                        }
                    }
                    if(!empty($productInfo))
                    {
                        $updated_by = \Session::get('userId');
                        foreach($productInfo as $key=>$product_id)
                        {
                            $product = $this->_poModel->getProductInfoByID($product_id,$supplier_id,$warehouse_id);
                            $product = json_decode(json_encode($product),true);
                            $po_product = array();
                            $po_product['qty'] = (isset($data['qty'][$key]))?$data['qty'][$key]:1;
                            $pack_id = (isset($packsize[$key]) && $packsize[$key]!='')?$packsize[$key]:'';
                            $uomPackinfo = $this->_poModel->getProductPackUOMInfoById($pack_id);
                            $po_product['uom'] = (isset($uomPackinfo->value))?$uomPackinfo->value:0;
                            $po_product['no_of_eaches'] = (isset($uomPackinfo->no_of_eaches))?$uomPackinfo->no_of_eaches:0;
                            $po_product['free_qty'] = (isset($data['freeqty'][$key]))?$data['freeqty'][$key]:0;
                            $free_pack_id=(isset($data['freepacksize'][$key]) && $data['freeqty'][$key]!=0)?$data['freepacksize'][$key]:'';
                            $freeUOMPackinfo = $this->_poModel->getProductPackUOMInfoById($free_pack_id);
                            $po_product['free_uom'] = (isset($freeUOMPackinfo->value) && $data['freeqty'][$key]!=0)?$freeUOMPackinfo->value:0;
                            $po_product['free_eaches'] = (isset($freeUOMPackinfo->no_of_eaches))?$freeUOMPackinfo->no_of_eaches:0;
                            $po_product['is_tax_included'] = (isset($data['pretax'][$product_id]))?$data['pretax'][$product_id]:0;
                            $po_product['apply_discount'] = (isset($data['apply_discount'][$product_id]))?$data['apply_discount'][$product_id]:0;
                            $po_product['discount_type'] = (isset($data['item_discount_type'][$product_id]))?$data['item_discount_type'][$product_id]:0;
                            $po_product['discount'] = (isset($data['item_discount'][$product_id]))?$data['item_discount'][$product_id]:0;
                            if($po_type==1){
                                $po_product['unit_price'] = number_format(0,5,'.','');
                                $po_product['price'] = number_format(0,5,'.','');
                                $po_product['tax_name'] = '';
                                $po_product['tax_per'] = number_format(0,5,'.','');
                                $po_product['tax_amt'] = number_format(0,5,'.','');
                                $po_product['sub_total'] = number_format(0,5,'.','');
                            }else{
                                $po_product['unit_price'] = (isset($data['unit_price'][$product_id]))?number_format($data['unit_price'][$product_id],5,'.',''):0;
                                $po_product['price'] = (isset($data['po_baseprice'][$key]))?number_format($data['po_baseprice'][$key],5,'.',''):0;
                                $po_product['tax_name'] = (isset($data['po_taxname'][$product_id]))?$data['po_taxname'][$product_id]:'';
                                $po_product['tax_per'] = (isset($data['po_taxper'][$product_id]))?number_format($data['po_taxper'][$product_id],5,'.',''):0;
                                $po_product['tax_amt'] = (isset($data['po_taxvalue'][$product_id]))?number_format($data['po_taxvalue'][$product_id],5,'.',''):0;
                                $po_product['sub_total'] = (isset($data['po_totprice'][$key]))?number_format($data['po_totprice'][$key],5,'.',''):0;
                            }

                            $productExist = $this->_poModel->checkPOProductExist($poId,$product_id);
                            $preUpdatePOProducts = $this->_poModel->getPreUpdatePOProducts($poId,$product_id);
                            $updated_by = (isset($data['updated_by']) && $data['updated_by']!='')?$data['updated_by']:\Session::get('userId');
                            if($productExist==1){
                                $flagdata['updated_by']=$updated_by;
                                $changeResult=array_diff($po_product,$preUpdatePOProducts);
                                if(!empty($changeResult) && count($changeResult)>0){
                                    $mailMsg .= '<p><strong>'.$product['pname'].' Updated</strong></p>';
                                }
                                $po_product['tax_data'] = (isset($data['po_taxdata'][$product_id]) && $data['po_taxdata'][$product_id]!='')?base64_decode($data['po_taxdata'][$product_id],true):'{}';
                                $po_product['hsn_code'] = (isset($data['hsn_code'][$product_id]))?$data['hsn_code'][$product_id]:0;
                                $po_product['cur_elp'] = (isset($data['curelpval'][$product_id]))?$data['curelpval'][$product_id]:0;
                                $this->_poModel->updatePOProducts($po_product,$product_id,$poId,$flagdata);
                            }else{
                                $po_product['po_id'] = $poId;
                                $po_product['product_id'] = $product_id;
                                $po_product['mrp'] = (isset($product['mrp']) && $product['mrp']!='')?$product['mrp']:0;
                                $po_product['parent_id'] = (isset($data['parent_id'][$key]))?$data['parent_id'][$key]:0;
                                $po_product['tax_data'] = (isset($data['po_taxdata'][$product_id]) && $data['po_taxdata'][$product_id]!='')?base64_decode($data['po_taxdata'][$product_id],true):'{}';
                                $po_product['hsn_code'] = (isset($data['hsn_code'][$product_id]))?$data['hsn_code'][$product_id]:0;
                                $po_product['cur_elp'] = (isset($data['curelpval'][$product_id]))?$data['curelpval'][$product_id]:0;
                                $this->_poModel->savePoProducts($po_product);
                                $mailMsg .= '<p><strong>'.$product['pname'].' Added</strong></p>';
                            }
                            if($po_type==2){
                                $po_product['created_by'] = $updated_by;
                                $po_product['po_id'] = $poId;
                                // $this->_poModel->savePurchaseHistory($po_product,$product);
                            }

                            // stock transfer inventory check
                            if($stock_transfer > 0){
                                $product_title = isset($product['pname'])?$product['pname']:0;
                                $sku = isset($product['sku'])?$product['sku']:0;
                                $availQty = $this->_poModel->checkInventory($product_id,$stock_transfer_dc);
                                $poProductQty = $po_product['no_of_eaches'] * $po_product['qty'];

                                if($availQty < $poProductQty){
                                    array_push($product_inv_ids, $product_id);
                                    $inventoryData .= '<tr class="subhead priceerrorname">
                                                        <td align="left" valign="middle"><b>'.$product_title.' <span style="color:blue"><b>('.$sku.')</b></span></b></td>
                                                        <td style="color:red" align="left" valign="middle">'.$availQty.'</td>
                                                        <td align="left" valign="middle">'.$poProductQty.'</td>
                                                            </tr>';
                                }
                            }
                        }
                        if($stock_transfer > 0)
                            if(count($product_inv_ids)){
                                return json_encode(["status" => 'failure',"reason" => "No Inventory to transfer stock!", "message" => "inv_error_found","adjust_message"=>"Add or Remove for No Inventory Products",'data'=>$inventoryData]);
                            }
                        $poArr=array('updated_by'=>$updated_by,'updated_at'=>date('Y-m-d H:i:s'));
                        if(isset($data['logistics_cost']) && $data['logistics_cost']!=''){
                           $poArr['logistics_cost']= $data['logistics_cost'];
                        }
                        $payment_mode =  isset($data['payment_mode']) ? $data['payment_mode'] : 1;
                        $paid_through =  isset($data['paid_through']) ? $data['paid_through'] : '';
                        $accountinfo = explode('===', $paid_through);
                        $tlm_name = (isset($accountinfo[0]))?$accountinfo[0]:'';
                        $tlm_group = (isset($accountinfo[1]))?$accountinfo[1]:'';
                        $payment_type =  isset($data['payment_type']) ? $data['payment_type'] : '';
                        $payment_ref =  isset($data['payment_ref']) ? $data['payment_ref'] : '';

                        $poArr['payment_mode'] = $payment_mode;
                        $poArr['payment_type'] = ($payment_mode==2)?$payment_type:'';
                        $poArr['payment_refno'] = ($payment_mode==2)?$payment_ref:'';
                        $poArr['tlm_name'] = ($payment_mode==2)?$tlm_name:'';
                        $poArr['tlm_group'] = ($payment_mode==2)?$tlm_group:'';
                        $poArr['payment_due_date'] = '';
                        $poArr['po_remarks'] = (isset($data['po_remarks']))?$data['po_remarks']:'';
                        $poArr['apply_discount_on_bill'] = (isset($data['apply_bill_discount']))?$data['apply_bill_discount']:0;
                        $poArr['discount_type'] = (isset($data['bill_discount_type']))?$data['bill_discount_type']:0;
                        $poArr['discount'] = (isset($data['bill_discount']))?$data['bill_discount']:0;
                        $poArr['discount_before_tax'] = (isset($data['discount_before_tax']))?$data['discount_before_tax']:0;
                        $poArr['is_stock_transfer'] = (isset($data['stock_transfer']))?$data['stock_transfer']:0;
                        if(isset($data['payment_due_date']) && !empty($data['payment_due_date']) && $payment_mode==1) {
                            $poArr['payment_due_date'] = date('Y-m-d', strtotime($data['payment_due_date'])).' '.date('H:i:s');
                        }
                        $this->_poModel->updatePO($poId, $poArr);
                    }
                    $poCode = $this->_poModel->getPoCodeById($poId);
                    $po_code = (isset($poCode->po_code))?$poCode->po_code:'';
                    if($mailMsg!=''){
                        $mailData['subject'] = 'PO#'.$po_code.' Updated';
                        $mailData['message'] = $mailMsg;
                        $this->emailWithAttachment($poId,$po_code,$mailData);
                        $this->_poModel->updatePO($poId, ['approval_status'=>57106,'approved_by'=>\Session::get('userId'),'approved_at'=>date('Y-m-d H:i:s')]);
                        $current_status=(isset($poCode->approval_status))?$poCode->approval_status:'';
                        $approval_flow_func = new CommonApprovalFlowFunctionModel();
                        $approval_flow_func->storeWorkFlowHistory('Purchase Order', $poId, $current_status, 57106, 'PO has been modified hence moving to intiated', \Session::get('userId'));

                    }
                    $this->poDocUpdate($poId);
                    return json_encode(array('status'=>200, 'message'=>'PO Updated Successfully', 'po_id'=>$poId, 'po_code'=>$po_code));
                }else{
                    return json_encode(array('status'=>400, 'message'=>'PO ID should not be empty', 'po_id'=>'', 'po_code'=>''));
                }
            }
        }
        catch(\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
            return json_encode(array('status'=>400, 'message'=>$ex->getMessage(), 'po_id'=>''));
        }
    }

    public function getSkus()
    {
        try{
            $data = \Input::all();
            $purchaseOrder = new PurchaseOrder();
            $skus = $purchaseOrder->getSkus($data);
            return $skus;die;
        }
        catch(\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }
    public function subscribeProducts($supplier_id,$warehouse_id,$product_id)
    {
        try{
           $subscribe = $this->_poModel->checkProductSuscribe($supplier_id,$warehouse_id,$product_id);
            if(count($subscribe)>0 && isset($subscribe->subscribe)){
                if($subscribe->subscribe==0){
                    $product_tot = ['subscribe'=>1];
                    $this->_poModel->updateProductTot($product_tot,$supplier_id,$warehouse_id,$product_id);
                }
            } else {
                $productdet = $this->_poModel->getProductdetails($product_id);
                $product_title = isset($productdet->product_title)?$productdet->product_title:0;
                $product_tot = ['product_id'=>$product_id,'le_wh_id'=>$warehouse_id,'supplier_id'=>$supplier_id,'product_name'=>$product_title,'is_active'=>1,'subscribe'=>1];
                $this->_poModel->saveProductTot($product_tot);
            }
            return 1;
        }
        catch(\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }
    public function getProductInfo()
    {
        try{
            //Log::info(DB::enableQueryLog());
            $data = \Input::all();
            $product_id = $data['product_id'];
            $supplier_id = $data['supplier_id'];
            $warehouse_id = $data['warehouse_id'];
            $sno = $data['sno_increment'];
            $this->subscribeProducts($supplier_id,$warehouse_id,$product_id);

            $productsAdded = isset($data['products'])?$data['products']:array();
            $addfrom = isset($data['addfrom'])?$data['addfrom']:'';
            $freebieParent = $this->_poModel->getFreebieParent($product_id);
           // print_r($freebieParent);
            //Log::info(DB::getQueryLog());
            $parent_id = (isset($freebieParent->main_prd_id)) ? $freebieParent->main_prd_id : 0;
            if($parent_id>0){
                //if main product exists check offer pack configured for product in product attributes,if main product is not consumer pack outside through error
                $productattributes=$this->_poModel->getProductAttributes($parent_id);
                if($productattributes->value!='Consumer Pack Outside')
                {
                    return $response = array('status' => 400, 'message' => 'Improper Offer Pack Configuration', 'productList' => '');
                }
            }elseif($parent_id==0 && $product_id>0){
                //for any product if offer pack is configured as consumer pack outside it should have freebie else through error message
                $productattributes=$this->_poModel->getProductAttributes($product_id);
                $getFreebiee=$this->_poModel->getFreebieProducts($product_id);
                if(($productattributes->value=='Consumer Pack Outside' && count($getFreebiee)==0) || ($productattributes->value!='Consumer Pack Outside' && count($getFreebiee)>0))
                {
                        return $response = array('status' => 400, 'message' => 'Improper Offer Pack Configuration', 'productList' => '');
                }
            }
            $productTextArr = $this->getPOProductRow($product_id,$parent_id,$supplier_id,$warehouse_id,$addfrom);
            $productList = '';
            if(is_array($productTextArr) && isset($productTextArr['status'])){
                if($productTextArr['status']==200){
                    $productList=(isset($productTextArr['productList']))?$productTextArr['productList']:'';
                    $freebieProducts = $this->_poModel->getFreebieProducts($product_id);
                    foreach($freebieProducts as $freeproduct){
                        if($freeproduct->main_prd_id!=$freeproduct->free_prd_id && !in_array($freeproduct->free_prd_id, $productsAdded)){
                            $this->subscribeProducts($supplier_id,$warehouse_id,$freeproduct->free_prd_id);
                            $productarr = $this->_poModel->getProductInfoByID($freeproduct->free_prd_id, $supplier_id, $warehouse_id);
                            $freeProductTextArr=array();
                            if (count($productarr) > 0) {
                                if ($productarr->is_sellable == 0 && $productarr->KVI == 'Q9') {
                                    $freeProductTextArr = $this->getPOProductRow($freeproduct->free_prd_id,$product_id,$supplier_id,$warehouse_id,$addfrom);
                                }
                            }
                            if(is_array($freeProductTextArr) && isset($freeProductTextArr['status']) && $freeProductTextArr['status']==200){
                                $productList.=$freeProductTextArr['productList'];
                            }
                        }
                    }
                }
                $response = array('status' => $productTextArr['status'], 'message' => $productTextArr['message'], 'productList' => $productList);
            }else{
                $response = array('status' => 400, 'message' => 'Something went wrong', 'productList' => $productList);
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
            $response=array('status'=> 400,'message'=>$ex->getMessage());
        }
        return $response;
    }
    public function getPOProductRow($product_id,$parent_id,$supplier_id, $warehouse_id,$addfrom,$indent_id=0) {
        try {
            $product = $this->_poModel->getProductInfoByID($product_id, $supplier_id, $warehouse_id);
            if (count($product) > 0) {
                $poArr[] =(object)array('product_id'=>$product_id,'le_wh_id'=>$warehouse_id,'legal_entity_id'=>$supplier_id);
                $taxArr = $this->getTaxInfo($poArr);
                if ((isset($taxArr[$product_id]) && is_array($taxArr[$product_id])) || ($product->is_sellable == 0 && $product->KVI == 'Q9')) {
                    $hsn_code = isset($taxArr[$product_id][0]['HSN_Code'])?$taxArr[$product_id][0]['HSN_Code']:'';
                    if($hsn_code==''){
                        $msg = 'Please check HSN code could not find';
                        $response = array('status' => 400, 'message' => $msg, 'productList' => '');
                        return $response;die;
                    }
                    $packs = $this->_poModel->getProductPackInfo($product_id);
                    $uom = '';
                    if (!empty($packs) && count($packs) > 0) {

                        $free_qty=0;
                        if($parent_id!=0 && $product->is_sellable == 0 && $product->KVI == 'Q9'){
                            $free_qty = 1;
                        }else{
                            $parent_id=0;
                        }
                        if($indent_id>0){
                            $indentProd = $this->_poModel->getIndentProduct($indent_id,$product_id);
                        }
                        $defltUOMEaches = (isset($packs[0]->no_of_eaches) && $packs[0]->no_of_eaches != 0) ? $packs[0]->no_of_eaches : 1;
                        foreach ($packs as $pack) {
                            $selectoum = '';
                            if($indent_id>0 && $pack->no_of_eaches==$indentProd->no_of_units){
                                $defltUOMEaches = $pack->no_of_eaches;
                                $selectoum = 'selected="selected"';
                                if($pack->level == $indentProd->pack_type){
                                    $selectoum = 'selected="selected"';
                                }else{
                                    $selectoum = '';
                                }
                            }
                            $uom .= '<option value="' . $pack->pack_id . '" data-noofeach="' . $pack->no_of_eaches . '" '. $selectoum.'>' . $pack->packname . '(' . $pack->no_of_eaches . ')</option>';

                        }
                        $cur_symbol = (isset($product->symbol) && $product->symbol != '') ? $product->symbol : 'Rs.';
                        $mrp = (isset($product->mrp) && $product->mrp != '') ? $product->mrp : 0;
                        $current_elp = (isset($product->dlp) && $product->dlp != '') ? $product->dlp : 0;
                        $prev_elp = (isset($product->prev_elp) && $product->prev_elp != '') ? $product->prev_elp : 0;
//                        Log::info($current_elp);
                        if($indent_id>0){
                            $totPoQty = $this->_poModel->getPoProductQtyByIndentId($indent_id,$product_id);
                            $objIndent = new IndentModel();
                            $totIndentQty = (int)$objIndent->getIndentProductQtyById($indent_id,$product_id);
                            $diffCount = $totIndentQty-$totPoQty;
                            $diffResult = ($diffCount/($defltUOMEaches));
                            if(is_float($diffResult))
                            {
                            }else{
                                $diffCount = $diffResult;
                            }
                            $qty = $diffCount;
                            $packPrice = (isset($indentProd->target_elp) && $indentProd->target_elp != '') ? $indentProd->target_elp : 0;
                            $dlp = $packPrice/$defltUOMEaches; //one cfc price
                            $current_elp = $dlp;
                        }else{
                            $qty = 1;
                            $dlp = $current_elp;
                            $packPrice = $dlp * $defltUOMEaches;
                        }

                        $total = $packPrice * ($qty-$free_qty);

                        $sumTax = 0;
                        $taxText = '';
                        $taxper = 0;
                        $taxname = '';
                        $hsn_code = '';
                        $tax_code = '';
                        $tax_data = '';
                        if (is_array($taxArr[$product_id])) {
                            foreach ($taxArr[$product_id] as $tax) {
                                $sumTax = $sumTax + $tax['Tax Percentage'];
                                $taxText .= $tax['Tax Type'] . '@' . $tax['Tax Percentage'] . '<br>';
                                $taxper = $tax['Tax Percentage'];
                                $taxname = $tax['Tax Type'];
                                if($hsn_code==''){
                                    $hsn_code = isset($tax['HSN_Code'])?$tax['HSN_Code']:'';
                                }
                                if($tax_code==''){
                                    $tax_code = isset($tax['Tax Code'])?$tax['Tax Code']:'';
                                }
                            }
                            $tax_data = base64_encode(json_encode($taxArr[$product_id]));
                        }
                        $base_price = ($total / (100 + $taxper)) * 100;
                        $taxAmt = $total - $base_price;
                        $newProduct = $this->_poModel->verifyNewProductInWH($warehouse_id, $product_id);
                        if ($newProduct == 0) {
                            $newPrClass = 'class="newproduct"';
                        } else {
                            $newPrClass = '';
                        }
                        $le_wh_id= DB::SELECT(DB::raw("SELECT lewh.le_wh_id FROM legalentity_warehouses AS lewh WHERE lewh .`legal_entity_id` = $supplier_id
                            AND lewh.dc_type=118001"));
                        
                        if(count($le_wh_id) > 0){
                            $le_wh_id=$le_wh_id[0]->le_wh_id;
                            $current_soh = $this->_poModel->checkInventory($product_id,$le_wh_id);
                            $noe= DB::select(DB::raw("SELECT SUM(pop.`qty`*pop.`no_of_eaches`) AS noe FROM po_products AS pop JOIN po AS po  ON po.po_id=pop.po_id WHERE pop.product_id=$product_id AND po_status=87001 AND po.legal_entity_id=$supplier_id"));
                            $final_soh = $current_soh - $noe[0]->noe;
                        }                       
                        else{
                            $final_soh = 0;
                        }
                        $productList='';
                        $productList .= '<tr>';
                        $productList .= '<td><span class="snos">1</span></td>';
                        $productList .= '<td><span ' . $newPrClass . '>' . $product->sku . '</span><input type="hidden" name="po_product_id[]" value="' . $product_id . '">
                            <input type="hidden" id="product_sku' . $product_id . '" value="' . $product->sku . '">
                                <input type="hidden" name="parent_id[]" value="' . $parent_id . '"></td>';
                        $productList .= '<td><span ' . $newPrClass . '>' . $product->pname . '</span></td>'; //<br><strong>EAN:</strong> '.(!empty($product->upc) ? $product->upc : $product->seller_sku).'
                        $productList .= '<td>
                                        <div style="width:175px">
               <div style="float:left"> <input class="form-control" size="3" type="number" id="qty' . $product_id . '" min="1" value="' . (int) $qty . '" name="qty[]" style=" width:70px"></div>
               <div style="float:right"> <select class="form-control packsize' . $product_id . '" name="packsize[]" style="width:100px" required="required">' . $uom . '</select></div>
             </div>
                                        </td>';
                        $productList .= '<td>
                                        <div style="width:175px">
               <div style="float:left"> <input class="form-control" id="freeqty' . $product_id . '" min="0" type="number" size="3" style=" width:70px  " value="' . (int) $free_qty . '" name="freeqty[]"></div>
               <div style="float:right"><select class="form-control freepacksize' . $product_id . '" name="freepacksize[]" required="required" style="width:100px">' . $uom . '</select></div>
               </div>
                                        </td>';
                        $newproduct = '';
                        if (isset($addfrom) && $addfrom == 'edit_po') {
                            $newproduct ='<input id="newproductadd' . $product_id . '" name="newproductadd[]" type="hidden" value="' . $product_id . '">';
                        } else {
                            $productList .= '<td>' . $product->soh . '</td>';
                        }
                        $productList .= '<td>' . $final_soh . '</td>';
                        $productList .= '<td>' . number_format($mrp, 5) . '</td>';
                        if ($current_elp>$prev_elp) {
                             $css_colour = "style='color:red'";
                        }else{
                            $css_colour='';
                        }

                        $productList .= '<td>' . number_format($prev_elp, 5) . '</td> <input type="hidden" name="prev_elp_value[' . $product_id . ']" id="prev_elp_value' . $product_id . '" value="'.number_format($prev_elp, 5).'"/> ';
                        $productList .= '<td><span id="curelptext' . $product_id . '" '.$css_colour.'>' . number_format($current_elp, 5) . '</span><input type="hidden" name="curelpval[' . $product_id . ']" id="curelpval' . $product_id . '" value="'.$current_elp.'"/></td>';
                        $productList .= '<td class="potypeshow">
                                                                                <div style="width:170px">
               <div style="float:left"> <input class="form-control pobaseprice" min="0" id="baseprice' . $product_id . '"  name="po_baseprice[]" type="number" value="' . ($packPrice) . '" style="width:100px"></div>
                    <div style="float:left"><input class="pretax pretax' . $product_id . '" checked="checked" data-id="' . $product_id . '" name="pretax[' . $product_id . ']" type="checkbox" value="1" style="margin:7px 10px 0px 10px;"></div>
                    <div style="float:left"><span style="margin-top:8px; font-size:9px;">Incl.Tax </span>  </div>
                  </div>
                                        <input id="taxname' . $product_id . '" name="po_taxname[' . $product_id . ']" type="hidden" value="' . $taxname . '">
                                        <input id="taxdata' . $product_id . '" name="po_taxdata[' . $product_id . ']" type="hidden" value="' . $tax_data . '">
                                        <input id="hsn_code' . $product_id . '" name="hsn_code[' . $product_id . ']" type="hidden" value="' . $hsn_code . '">
                                        <input id="tax_code' . $product_id . '" name="tax_code[' . $product_id . ']" type="hidden" class="tax_code" value="' . $tax_code . '">
                                        <input id="mrp' . $product_id . '" type="hidden" value="' . $mrp . '">
                                        <input id="unit_price' . $product_id . '" name="unit_price[' . $product_id . ']" class="unitPrice" data-product_id="'.$product_id.'" type="hidden" value="' . $dlp . '">
                                        <input id="taxper' . $product_id . '" name="po_taxper[' . $product_id . ']" type="hidden" value="' . $taxper . '">
                                        <input name="po_taxvalue[' . $product_id . ']" id="taxval' . $product_id . '" type="hidden" value="' . $taxAmt . '">'.$newproduct.'
                                </td>';
                        $productList .= '<td class="potypeshow" align="right"><span id="totalPriceText' . $product_id . '">' . $total . '</span></td>';
                        $productList .= '<td class="potypeshow">' . $taxText . '</td>';
                        $productList .= '<td class="potypeshow" align="right"><span id="taxtext' . $product_id . '">' . number_format($taxAmt, 5) . '</span></td>';
                        $productList .= '<td class="potypeshow" align="right">'
                                .'<input class="apply_discount_item" id="apply_discount' . $product_id . '" data-id="' . $product_id . '" name="apply_discount[' . $product_id . ']" type="checkbox" value="1" style="margin:7px 10px 0px 10px;">
                                </td>';
                        $productList .= '<td class="potypeshow" align="right"><div style="width:170px">'
                                . '<div style="float:left"><input class="form-control item_discount" min="0" id="discount' . $product_id . '" style="width:100px;" name="item_discount[' . $product_id . ']" type="number" value="0"></div>'
                                .'<div style="float:left"><input class="item_discount_type"  id="item_discount_type'.$product_id.'" name="item_discount_type[' . $product_id . ']" type="checkbox" value="1" style="margin:7px 10px 0px 10px;">
                                    <input class="" id="item_discount_amt'.$product_id.'" name="" type="hidden" value="0"></div>
                                <div style="float:left"><span style="margin-top:8px; font-size:9px;"><strong>%</strong></span></div>
                                </div>'
                                . '</td>';
                        $productList .= '<td class="potypeshow" align="right">
                                        <input class="form-control pototprice" min="0" id="totprice' . $product_id . '" style="width:100px;" name="po_totprice[]" type="number" value="' . ($total) . '">
                                        </td>';
                        $productList .= '<td class="" align="center"><a class="fa fa-trash-o delete_product" data-id="' . $product_id . '"></a></td>';
                        $productList .= '</tr>';
                        $response = array('status' => 200, 'message' => 'Success', 'productList' => $productList);
                    } else {
                        $response = array('status' => 400, 'message' => 'Please add pack configuration', 'productList' => '');
                    }
                } else {
                    if(isset($taxArr[$product_id])){
                        $msg = $taxArr[$product_id];
                    }else{
                        $msg = 'please check tax information could not find';
                    }
                    $response = array('status' => 400, 'message' => $msg, 'productList' => '');
                }
            } else {
                $response = array('status' => 400, 'message' => 'Product Info not found', 'productList' => '');
            }
        } catch (Exception $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
            $response=array('status'=> 400,'message'=>$ex->getMessage());
        }
        return $response;
    }
    public function getWarehouseBySupplierId()
    {
        try{
            $data = \Input::all();
            //$supplierId = $data['supplier_id'];
            $legal_entity_id = \Session::get('legal_entity_id');
            $warehouseList = $this->_LegalEntity->getWarehouseBySupplierId($legal_entity_id);
            $warehouseOptions='<option value="">Select Delivery Location</option>';
            foreach($warehouseList as $warehouse){
                $margin = (isset($warehouse->margin) && $warehouse->margin!="")?$warehouse->margin:0;
                $warehouseOptions .= '<option value='.$warehouse->le_wh_id.' data-margin='.$margin.' le_type='.$warehouse->legal_entity_type_id.'>'.$warehouse->lp_wh_name.'</option>';
            }
            return json_encode(array('warehouses'=>$warehouseOptions));
        }
        catch(\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }
    /*
	 * downloadPOExcel() method is used to Download All Orders
	 * @param NULL
	 * @return Excell
	 */
    public function downloadPOExcel() {
        try{
            $filterData = Input::get();
            $fdate = (isset($filterData['fdate']) && !empty($filterData['fdate'])) ? $filterData['fdate'] : date('Y-m').'-01';
            $fdate = date('Y-m-d', strtotime($fdate));
            $tdate = (isset($filterData['tdate']) && !empty($filterData['tdate'])) ? $filterData['tdate'] : date('Y-m-d');
            $tdate = date('Y-m-d', strtotime($tdate));
            $dcID =$filterData['loc_dc_id'];
            $dcNames = implode(',',$dcID);
            if ($dcNames==0){
                $dcNames = 'NULL';
            }else{
                $dcNames =  "'".$dcNames."'";
            }
            $query = "CALL getPurchaseDetails('$fdate','$tdate',$dcNames)";
            $file_name = 'PO_Report_' .date('Y-m-d-H-i-s').'.csv';
            $this->exportToCsv($query, $file_name);die;

        }catch(Exception $e) {
                Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return Response::json(array('status' => 404, 'Message' => Lang::get('salesorders.errorInputData')));
        }
    }
    public function downloadPOReport() {
        try{
            $filterData = Input::get();
            $fdate = (isset($filterData['fdate']) && !empty($filterData['fdate'])) ? $filterData['fdate'] : date('Y-m').'-01';
            $fdate = date('Y-m-d', strtotime($fdate));
            $tdate = (isset($filterData['tdate']) && !empty($filterData['tdate'])) ? $filterData['tdate'] : date('Y-m-d');
            $tdate = date('Y-m-d', strtotime($tdate));
            $is_grndate = (isset($filterData['grn_date'])) ? 1 : 0;
            $dcID =$filterData['loc_dc_id'];            
            $purchaseData = $this->_poModel->getPurchasedata($fdate,$tdate,$is_grndate,$dcID);
            if(isset($purchaseData) && is_array($purchaseData)){
                foreach($purchaseData as $purchase){
                    $inwardId = $purchase->inward_id;
                    $invoice_grid_id = $purchase->po_invoice_grid_id;
                    $discountAmount = $purchase->discount_total;
                    $grnValue = $purchase->grnValue;
                    $invoiceValue = $purchase->invoiceValue;
                    $invoice_no = $purchase->invoice_no;

                    $reference_arr = explode(',', $purchase->SupplierInvoice);

                    if(count($reference_arr)>0){
                        $ref_uniq = array_unique($reference_arr);
                        $supplierInvoice = trim(implode(',', $ref_uniq),',');
                    }
                    if($supplierInvoice=="" || $supplierInvoice==0){
                        $supplierInvoice = $invoice_no;
                    }
                    $purchase->SupplierInvoice = $supplierInvoice;
                    $purchase->twoainvoice = (isset($purchase->twoainvoice) && $purchase->twoainvoice!="")?$purchase->twoainvoice:$supplierInvoice;
                    $taxArr = array();
                    $baseAmtArr = array();
                    //if($invoice_grid_id!='' && $invoice_grid_id>0){
                        //$grnProducts= $this->_poModel->getInvoiceDetailById($invoice_grid_id);
                        $grnProducts= $this->_poModel->getInwardDetailById($inwardId);
                        if(is_array($grnProducts) && count($grnProducts)>0){
                            foreach ($grnProducts as $product) {
                                $taxper = $product->tax_per;
                                $discountAmount = $discountAmount + $product->discount_total;
                                if ($product->tax_amount > 0) {
                                    if (isset($taxArr[$taxper])) {
                                        $taxArr[$taxper] += $product->tax_amount;
                                    } else {
                                        $taxArr[$taxper] = $product->tax_amount;
                                    }
                                }
                                    if (isset($baseAmtArr[$taxper])) {
                                        $baseAmtArr[$taxper] += $product->sub_total;
                                    } else {
                                        $baseAmtArr[$taxper] = $product->sub_total;
                                    }
                                }
                            }
                    //}
                    $tax_data = [];
                    $gstTaxamt = 0;
                    $gstPer = 50;
                    $utgstPer =50;
                    $gstBaseamt = array_sum($baseAmtArr);
                    $gstTaxamt = array_sum($taxArr);
                    $purchase->gstbase = round($gstBaseamt,2);
                    if(isset($grnProducts[0]->tax_name) && in_array($grnProducts[0]->tax_name, ['IGST'])){
                       $purchase->cgst = 0;
                        $purchase->sgst = 0;
                        $purchase->igst = round($gstTaxamt,2);
                        $purchase->utgst = 0;  

                    }elseif(in_array($grnProducts[0]->tax_name,['UTGST'])){
                        $purchase->cgst = round(($gstTaxamt*$utgstPer)/100,2);
                        $purchase->sgst = 0;
                        $purchase->igst = 0;
                        $purchase->utgst = round(($gstTaxamt*$utgstPer)/100,2);

                    }else {
                        $purchase->cgst = round(($gstTaxamt*$gstPer)/100,2);
                        $purchase->sgst = round(($gstTaxamt*$gstPer)/100,2);
                        $purchase->igst = 0;
                        $purchase->utgst = 0;
                    }

                    //$purchase->basic_at_14 = isset($baseAmtArr['14.50000'])?$baseAmtArr['14.50000']:0;
                    //$purchase->vat_at_14 = isset($taxArr['14.50000'])?$taxArr['14.50000']:0;
                    //$purchase->basic_at_5 = isset($baseAmtArr['5.00000'])?$baseAmtArr['5.00000']:0;
                    //$purchase->vat_at_5 = isset($taxArr['5.00000'])?$taxArr['5.00000']:0;
                    $drTotals = 0;
                    $crTotals = 0;

                    $crTotals = $invoiceValue+$discountAmount;
                    $drTotals = $drTotals + array_sum($baseAmtArr) + array_sum($taxArr);
                    $drTotals = (str_replace(',', '', number_format($drTotals, 2, '.', '')));
                    $crTotals = (str_replace(',', '', number_format($crTotals, 2, '.', '')));
                    if($drTotals > $crTotals)
                    {
                        $roundAmount = ($drTotals - $crTotals);
                    }else{
                        $roundAmount = ($crTotals - $drTotals);
                    }
                    $purchase->discount_total = $discountAmount;
                    $purchase->roundoff = $roundAmount;
                    unset($purchase->po_invoice_grid_id);
                    unset($purchase->po_id);
                    unset($purchase->inward_id);
                    unset($purchase->invoice_no);
                }
            }
            //echo '<pre/>';print_r($purchaseData);die;
            $purchaseData = json_decode(json_encode($purchaseData),true);
            $csvHeaders = array(
                            'EP Purchase date',
                            'Supplier Name',
                            'Supplier Code',
                            'DC Name',
                            'GST No',
                            'Supplier Invoice',
                            'II A Invoice No',
                            'Invoice date',
                            'PO Code',
                            'PO Date',
                            'PO Value',
                            'GRN Code',
                            'GRN Date',
                            'GRN Value',
                            'Discount',
                            'Total Invoice Value',
                            'GST Base Value ',
                            'CGST ',
                            'SGST ',
                            'IGST ',
                            'UTGST',
                            //'Purchase Basic Value@ 14.5% ',
                            //'Input Vat @14.5% Value',
                            //'Purchase Basic Value @ 5% ',
                            //'Input Vat @ 5% Value',
                            'Roundoff',
                        );
                $file_name = 'Purchase_Report_' .date('Y-m-d-H-i-s').'.csv';
                $this->downloadCsv($csvHeaders, $purchaseData, $file_name);
            die;
            //$this->exportToCsv($query, $file_name);die;

        }catch(Exception $e) {
                Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return Response::json(array('status' => 404, 'Message' => Lang::get('salesorders.errorInputData')));
        }
    }
    public function downloadPOHsnReport() {
        try{
            $filterData = Input::get();
            $fdate = (isset($filterData['fdate']) && !empty($filterData['fdate'])) ? $filterData['fdate'] : date('Y-m').'-01';
            $fdate = date('Y-m-d', strtotime($fdate));
            $tdate = (isset($filterData['tdate']) && !empty($filterData['tdate'])) ? $filterData['tdate'] : date('Y-m-d');
            $tdate = date('Y-m-d', strtotime($tdate));
            $is_grndate = (isset($filterData['grn_date'])) ? 1 : 0;
            $dcID =$filterData['loc_dc_id'];            
            $purchaseData = $this->_poModel->getPurchaseHSNdata($fdate,$tdate,$is_grndate,$dcID);
            if(isset($purchaseData) && is_array($purchaseData)){
                foreach($purchaseData as $purchase){
                    $inwardId = $purchase->inward_id;
                    //$invoice_grid_id = $purchase->po_invoice_grid_id;
                    $discountAmount = $purchase->discount_total;
                    $grnValue = $purchase->grnValue;
                    //$invoiceValue = $purchase->invoiceValue;
                    $invoice_no = $purchase->invoice_no;

                    $reference_arr = explode(',', $purchase->SupplierInvoice);

                    if(count($reference_arr)>0){
                        $ref_uniq = array_unique($reference_arr);
                        $supplierInvoice = trim(implode(',', $ref_uniq),',');
                    }
                    if($supplierInvoice=="" || $supplierInvoice==0){
                        $supplierInvoice = $invoice_no;
                    }
                    $purchase->SupplierInvoice = $supplierInvoice;
                    $taxArr = array();
                    $baseAmtArr = array();
                    //if($invoice_grid_id!='' && $invoice_grid_id>0){
                        //$grnProducts= $this->_poModel->getInvoiceDetailById($invoice_grid_id);
                     /*   $grnProducts= $this->_poModel->getInwardDetailById($inwardId);
                        if(is_array($grnProducts) && count($grnProducts)>0){
                            foreach ($grnProducts as $product) {
                                $taxper = $product->tax_per;
                                $discountAmount = $discountAmount + $product->discount_total;
                                if ($product->tax_amount > 0) {
                                    if (isset($taxArr[$taxper])) {
                                        $taxArr[$taxper] += $product->tax_amount;
                                    } else {
                                        $taxArr[$taxper] = $product->tax_amount;
                                    }
                                }
                                    if (isset($baseAmtArr[$taxper])) {
                                        $baseAmtArr[$taxper] += $product->sub_total;
                                    } else {
                                        $baseAmtArr[$taxper] = $product->sub_total;
                                    }
                                }
                            } */
                    //}
                    $tax_data = [];
                    $gstTaxamt = 0;
                    $gstPer = 50;
                    $utgstPer =50;
                    //$gstBaseamt = array_sum($baseAmtArr);
                    //$gstTaxamt = array_sum($taxArr);
                    //$purchase->gstbase = round($gstBaseamt,2);
                    $gstTaxamt = $purchase->tax_amount;
                    if(isset($purchase->tax_name) && in_array($purchase->tax_name, ['IGST'])){
                        $purchase->cgst = 0;
                        $purchase->sgst = 0;
                        $purchase->igst = round($gstTaxamt,2);
                        $purchase->utgst = 0;  
                    }elseif(in_array($purchase->tax_name,['UTGST'])){
                        $purchase->cgst = round(($gstTaxamt*$utgstPer)/100,2);
                        $purchase->sgst = 0;
                        $purchase->igst = 0;
                        $purchase->utgst = round(($gstTaxamt*$utgstPer)/100,2);

                    }else {
                        $purchase->cgst = round(($gstTaxamt*$gstPer)/100,2);
                        $purchase->sgst = round(($gstTaxamt*$gstPer)/100,2);
                        $purchase->igst = 0;
                        $purchase->utgst = 0;  
                    }

                    //$purchase->basic_at_14 = isset($baseAmtArr['14.50000'])?$baseAmtArr['14.50000']:0;
                    //$purchase->vat_at_14 = isset($taxArr['14.50000'])?$taxArr['14.50000']:0;
                    //$purchase->basic_at_5 = isset($baseAmtArr['5.00000'])?$baseAmtArr['5.00000']:0;
                    //$purchase->vat_at_5 = isset($taxArr['5.00000'])?$taxArr['5.00000']:0;
                    /*$drTotals = 0;
                    $crTotals = 0;

                    $crTotals = $invoiceValue+$discountAmount;
                    $drTotals = $drTotals + array_sum($baseAmtArr) + array_sum($taxArr);
                    $drTotals = (str_replace(',', '', number_format($drTotals, 2, '.', '')));
                    $crTotals = (str_replace(',', '', number_format($crTotals, 2, '.', '')));
                    if($drTotals > $crTotals)
                    {
                        $roundAmount = ($drTotals - $crTotals);
                    }else{
                        $roundAmount = ($crTotals - $drTotals);
                    }*/
                    $purchase->discount_total = $discountAmount;
                    //$purchase->roundoff = $roundAmount;
                    //unset($purchase->po_invoice_grid_id);
                    unset($purchase->po_id);
                    unset($purchase->inward_id);
                    unset($purchase->invoice_no);
                }
            }
            //echo '<pre/>';print_r($purchaseData);die;
            $purchaseData = json_decode(json_encode($purchaseData),true);
            $csvHeaders = array(
                            'EP Purchase date',
                            'Supplier Name',
                            'DC Name',
                            'GST No',
                            'Supplier Invoice',
                            'Invoice date',
                            'PO Code',
                            'PO Date',
                            'PO Value',
                            'GRN Code',
                            'GRN Date',
                            'GRN Value',
                            'Discount',
                            'HSN Code',
                            //'Total Invoice Value',
                            'GST Base Value ',
                            'Tax Type',
                            'Tax%',
                            'Tax Amount',
                            'CGST ',
                            'SGST ',
                            'IGST ',
                            'UTGST'
                            //'Purchase Basic Value@ 14.5% ',
                            //'Input Vat @14.5% Value',
                            //'Purchase Basic Value @ 5% ',
                            //'Input Vat @ 5% Value',
                            //'Roundoff'
                        );
                $file_name = 'Purchase_HSN_Report_' .date('Y-m-d-H-i-s').'.csv';
                $this->downloadCsv($csvHeaders, $purchaseData, $file_name);
            die;
            //$this->exportToCsv($query, $file_name);die;

        }catch(Exception $e) {
                Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return Response::json(array('status' => 404, 'Message' => Lang::get('salesorders.errorInputData')));
        }
    }
    public function downloadCsv($csvHeaders, $csvData, $filename) {
        //echo '<pre/>';print_r($csvData);die;
        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename='.$filename.'');

        // do not cache the file
        header('Pragma: no-cache');
        header('Expires: 0');

        // create a file pointer connected to the output stream
        $file = fopen('php://output', 'w');

        // send the column headers
        fputcsv($file, $csvHeaders);

        // output each row of the data
        foreach ($csvData as $row)
        {
            fputcsv($file, $row);
        }

        exit();
    }
    public function exportToCsv($query, $filename) {
        $host = env('READ_DB_HOST');
        $port = env('DB_PORT');
        $dbname = env('DB_DATABASE');
        $uname = env('DB_USERNAME');
        $pwd = env('DB_PASSWORD');
        $filePath = public_path().'/uploads/reports/'.$filename;
        //echo $filePath;die;
        $sqlIssolation = 'SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;';
        $sqlCommit = 'COMMIT';
        $exportCommand = "mysql -h ".$host." -u ".$uname." -p'".$pwd."' ".$dbname." -e \"".$sqlIssolation.$query.';'.$sqlCommit.";\" | sed  's/\\t/\",\"/g;s/^/\"/;s/$/\"/g' > ".$filePath;

        system($exportCommand);

        header("Content-Type: application/force-download");
        header("Content-Disposition:  attachment; filename=\"" . $filename . "\";" );
        header("Content-Transfer-Encoding:  binary");
        header("Accept-Ranges: bytes");
        header('Content-Length: ' . filesize($filePath));

        $readFile = file($filePath);
        foreach($readFile as $val){
            echo $val;
        }
        exit;
    }
    /*
	 * importPOExcel() method is used to Import PO data
	 * @param NULL
	 * @return array
	 */
    public function importPOExcel() {
        try{
            $msg = '';
            if (Input::hasFile('pofile')) {
                $path = Input::file('pofile')->getRealPath();
                $data = $this->readImportExcel($path);
                $data = json_decode(json_encode($data), 1);
                $msg = '';
                if(isset($data['prod_data']) && !empty($data['prod_data']) && count($data['prod_data'])>0){
                                 /*
                    expected delivery date according to supplier service day
                     */
                    $expDeliveryDateArr = array();
                    $expDeliveryDate  = implode(",", $expDeliveryDateArr);

                    $supCode = isset($data['po_data'][0]) ? $data['po_data'][0] : '';
                    $dcName = isset($data['po_data'][1]) ? $data['po_data'][1] : '';
                    $sup = $this->_poModel->getSupplierIDCode($supCode);
                    $supId = (isset($sup->legal_entity_id)) ? $sup->legal_entity_id : 0;
                    $dc = $this->_poModel->getWarehouseIDByCode($dcName);
                    $dcId = (isset($dc->le_wh_id)) ? $dc->le_wh_id : 0;
                    $validFlag =1;
                    if($supId!=0 && $dcId!=0){
                        $state_id = (isset($dc->state)) ? $dc->state : 4033;
                        $_cusRepo = new CustomerRepo();
                        $refNoArr = $_cusRepo->getRefCode('PO',$state_id);
                        $serialNumber = $refNoArr;
                        $poDetails['legal_entity_id'] = $supId;
                        $poDetails['le_wh_id'] = $dcId;

                        $podate = isset($data['prod_data'][0]['po_date']['date']) ? $data['prod_data'][0]['po_date']['date'] : date('Y-m-d');
                        if(!empty($podate)) {
                            $poDetails['po_date'] = date('Y-m-d', strtotime($podate)).' '.date('H:i:s');
                        }
                        $poDetails['po_validity'] = isset($data['prod_data'][0]['validitydays']) ? $data['prod_data'][0]['validitydays'] : 7;
                        $po_date = date('d-m-Y',strtotime($podate));
                        $date = new \DateTime($po_date);
                        if($poDetails['po_validity']!=0){
                            $date->add(new \DateInterval('P'.$poDetails['po_validity'].'D')); //new DateInterval('P7Y5M4DT4H3M2S')
                            $delivery_date = $date->format('Y-m-d H:i:s');
                        }else{
                            $delivery_date = date('Y-m-d H:i:s');
                        }
                        $poDetails['delivery_date'] = $delivery_date;
                        $poDetails['po_type'] = 0;
                        $poDetails['created_by'] = \Session::get('userId');
                        $poDetails['po_remarks'] = '';
                        $poDetails['po_code'] = $serialNumber;
                        $poDetails['approval_status'] = 57106;

                        if(!empty($expDeliveryDate)) {
                            $poDetails['exp_delivery_date'] = $expDeliveryDate;
                        }
                        $skulistfromexcelsheet=array_column($data['prod_data'], 'sku');
//                        Log::info(json_encode($data));
                        DB::beginTransaction();
                        $poId = $this->_poModel->create($poDetails);
                        $poId = $poId->po_id;
                        if($poId!='' && $poId>0){
                            $uomArr = $this->_masterLookup->getAllOrderStatus('Levels');
                            $uomArr=array_flip($uomArr);
                            $skuArray = array();
                            $productArr = array();
                            $timestamp = md5(microtime(true));
                            $txtFileName = 'po-import-' . $timestamp . '.html';
                            $file_path = 'download' . DIRECTORY_SEPARATOR . 'po_log' . DIRECTORY_SEPARATOR . $txtFileName;
                            $files_to_delete = File::files('download' . DIRECTORY_SEPARATOR . 'po_log/');
                            File::delete($files_to_delete);
                            $msg = '';
                            $excelRowcounter = 1;
                            $errorCnt = 0;
                            foreach($data['prod_data'] as $poproducts){
                                $uom = (isset($uomArr[$poproducts['uom']]))?$uomArr[$poproducts['uom']]:'';
                                $msg .= "#".$excelRowcounter." SKU (".$poproducts['sku'].") ";
                                if($uom != ""){
                                    if($poproducts['sku']!='' && $uom!=''){
                                        $product_id = $this->_poModel->getProductIdbySku($poproducts['sku']);
                                        //considering excel product as main product and trying to get child products if any is configured and checking if that freebie is uploaded in excel sheet if freebie is not present we will throw error
                                        $isFreebieproduct=0;
                                        $checkFreebieeforproduct = $this->_poModel->getFreebieProducts($product_id);
                                        if(count($checkFreebieeforproduct)>0)
                                        {
                                            $checkproductattributes=$this->_poModel->getProductAttributes($product_id);
                                            if($checkproductattributes->value!='Consumer Pack Outside'){
                                                $errorCnt++;
                                                $msg .= "Improper Offer Pack Configuration";
                                                    break;
                                            }
                                            //foreach is repeated if main product has multiple freebies check all are uploaded through excel sheet
                                            foreach ($checkFreebieeforproduct as  $value) 
                                            {
                                                $getskuname=$this->_poModel->getSKUByProductId($value->free_prd_id);
                                                if(!in_array($getskuname->sku, $skulistfromexcelsheet))
                                                {
                                                    $errorCnt++;
                                                    $msg .= "FreeBie Missing(".$getskuname->sku.")";
                                                    break;
                                                }
                                            }
                                        }
                                        //this is to check if product is freebie then get main and check if main product is in excel sheet else throw the error main product is missing
                                        $checkParentforproduct = $this->_poModel->getFreebieParent($product_id);
                                        $kvivalue=$this->_poModel->getKVIByProductId($product_id);
                                        if(isset($checkParentforproduct->main_prd_id) && $checkParentforproduct->main_prd_id>0){
                                                $isFreebieproduct=1;//if is freebie then base price is zero irrespective of baseprice uploaded in sheet
                                                $getparentskuname=$this->_poModel->getSKUByProductId($checkParentforproduct->main_prd_id);
                                                if(!in_array($getparentskuname->sku, $skulistfromexcelsheet))
                                                {
                                                    $errorCnt++;
                                                    $msg .= "Main Product Missing(".$getparentskuname->sku.")";
                                                    break;
                                                }

                                        }elseif($kvivalue->kvi==69010){
                                                    $errorCnt++;
                                                    $msg .= "Main Product Missing!";
                                                    break;
                                        }
                                        
                                        $parentproductid=isset($checkParentforproduct->main_prd_id)?$checkParentforproduct->main_prd_id:0;
                                       // Log::info("product_id".$product_id."  po_id".$poId);
                                        if($product_id != 0){
                                            $this->subscribeProducts($supId,$dcId,$product_id);
                                            $product = $this->_poModel->getProductInfoBySku($poproducts['sku'],$dcId,$supId);
                                            $whDetail = $this->_LegalEntity->getWarehouseById($dcId);
                                            $supplierInfo = $this->_poModel->getLegalEntityById($supId);
                                            $wh_state_code = isset($whDetail->state)?$whDetail->state:4033;
                                            $seller_state_code = isset($supplierInfo->state_id)?$supplierInfo->state_id:4033;
                                            // product should exist,tax should found,same sku checking
                                            if(count($product)>0 && !in_array($poproducts['sku'], $skuArray)){
                                                // pushing sku to checking duplicate sku
                                                $poproducts['sku'] = trim($poproducts['sku']);
                                                array_push($skuArray, $poproducts['sku']);
                                                $po_product = array();
                                                $product = json_decode(json_encode($product),1);
                                                $product_id = $product['product_id'];
                                                $txArr = [];
                                                $txArr[] =(object)array('product_id'=>$product_id,'le_wh_id'=>$dcId,'legal_entity_id'=>$supId);
                                                $tax_data = $this->getTaxInfo($txArr);
                                                // $tax_data = $this->getProductTaxClass($product_id,$wh_state_code,$seller_state_code);
                                                $tax_data = isset($tax_data[$product_id]) ? $tax_data[$product_id] : [];
                                                $packConfigdata = $this->_poModel->getProductPackUOMInfo($product_id,$uom);
                                                if(isset($packConfigdata->no_of_eaches)){
                                                    $po_product['parent_id']=$parentproductid;
                                                    $po_product['no_of_eaches'] = $packConfigdata->no_of_eaches;
                                                    $po_product['product_id'] = $product_id;
                                                    $po_product['upc'] = $product['upc'];
                                                    $po_product['sku'] = $product['sku'];
                                                    $po_product['mrp'] = (isset($product['mrp']) && $product['mrp']!='')?$product['mrp']:0;
                                                    $po_product['brand_id'] =(isset($product['brand_id']) && $product['brand_id']!='')?$product['brand_id']:0;
                                                    $po_product['qty'] = (isset($poproducts['qty']))?$poproducts['qty']:1;
                                                    if(is_numeric($poproducts['qty']) && $poproducts['qty'] !='' && $poproducts['qty'] >= 1){
                                                        if(is_numeric($poproducts['base_price']) && $poproducts['base_price'] !==''){
                                                            if($isFreebieproduct){
                                                                $po_product['unit_price'] =0;
                                                                $po_product['price'] = 0;    
                                                            }else{
                                                                $po_product['unit_price'] = ($poproducts['base_price']!='')?$poproducts['base_price']:0;
                                                                $po_product['price'] = $packConfigdata->no_of_eaches * $poproducts['base_price'];
                                                            }
                                                            
                                                            $po_product['sub_total'] = $po_product['qty'] * $po_product['price'];
                                                            $po_product['cur_elp'] = $po_product['unit_price'];
                                                            $po_product['is_tax_included'] = 1;
                                                            $po_product['product_name'] = ($product['product_name']!='')?$product['product_name']:'';
                                                            $po_product['uom'] = $uom;
                                                            $po_product['inv_on_hand'] = (isset($product['soh']) && $product['soh']!='')?$product['soh']:0;
                                                            $po_product['inv_reserved'] = (isset($product['order_qty']) && $product['order_qty']!='')?$product['order_qty']:0;
                                                            $po_product['tax_data'] = json_encode(isset($tax_data[0])?$tax_data:array());
                                                            if(isset($tax_data[0]) && count($tax_data[0]) && is_array ($tax_data[0])){
                                                                $tax_data = isset($tax_data[0])?$tax_data[0]:[];
                                                                $tax_amt = 0;
                                                                $po_product['tax_name'] = (isset($tax_data['Tax Type']) && $tax_data['Tax Type'] != '')?$tax_data['Tax Type']:'';
                                                                $po_product['tax_per'] = (isset($tax_data['Tax Percentage']))?$tax_data['Tax Percentage']:0.00;
                                                                $po_product['tax_amt'] = $tax_amt;
                                                                $po_product['hsn_code'] = (isset($tax_data['HSN_Code']))?$tax_data['HSN_Code']:0;
                                                                $price_excltax = ($po_product['sub_total']/(1+(($po_product['tax_per'])/100)));
                                                                $taxAmt = $po_product['sub_total']-$price_excltax;
                                                                $po_product['tax_amt'] = $taxAmt; 
                                                                $po_product['po_id'] = $poId;
                                                                $this->_poModel->savePoProducts($po_product);
                                                                array_push($productArr, $po_product);
                                                                $msg .= " - Looks Good!";
                                                            }else{
                                                                $errorCnt++;
                                                                $msg .= "Tax Not Configured!";
                                                            }
                                                        }else{
                                                            $errorCnt++;
                                                            $msg .= "Given Base Price is not a number!";
                                                        }
                                                    }else{
                                                        $errorCnt++;
                                                        $msg .= "Given Qty is not a number!";
                                                    }
                                                }else{
                                                    $errorCnt++;
                                                    $msg .= "Given UOM (".$poproducts['uom'].") not exist!"; 
                                                }
                                            }else{
                                                $errorCnt++;
                                                $msg .= "Product for given SKU not exist!";
                                            }
                                        }else{
                                            $errorCnt++;
                                            $msg .= "Invalid SKU";
                                        }
                                    }else{
                                        $errorCnt++;
                                        $msg .= "SKU is empty";
                                    }
                                }else{
                                    $errorCnt++;
                                    $msg .= "UOM is empty or Invalid!";
                                }
                                $excelRowcounter++;
                                $msg .= "</br></br>";
                            }

                            if($errorCnt == 0){
                                if(count($productArr)){
                                    DB::commit();
                                    $msg=Lang::get('po.successPO');
                                }else{
                                    DB::rollback();
                                    $msg='Invalid Products!Po creation aborted';
                                }
                            }
                        }
                    }else{
                        $errorCnt = 1;
                        $msg .='supplier id and warehouse id should not empty';
                    }
                }else{
                    $errorCnt = 1;
                    $msg .= 'product data should not be empty';
                }

            }else{
                $errorCnt = 1;
                $msg .= 'please select file';
            }
            if($errorCnt == 0){
                $status = 200;
                $msg = Lang::get('po.successPO');
                $url = "po/details/".$poId;
                $message = Lang::get('po.successPO');
                $message = "Click <a href=".$url." target='_blank'> here </a> to open Po.";
            }else{
                $msg .= PHP_EOL;
                $status = 400;
                $url = "";
                //create the log file as per the excel sheet
                if(isset($file_path)){
                    $file = fopen($file_path, "w");
                    fwrite($file, $msg);
                    fclose($file);
                    $toEmails = array();
                    $url = $file_path;
                    $message = "Errors in excel sheet.Click <a href=".'/'.$file_path." target='_blank'> here </a> to open file.";
                }else{
                    $message = $msg;
                }
            }
            $returnArray = array('status'=>$status, 'message'=>$message,"url"=>$url);
            return Response::json($returnArray);
        }catch(Exception $e) {
            DB::rollback();
                Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return Response::json(array('status' => 404, 'Message' => Lang::get('salesorders.errorInputData')));
        }
    }
    public function readExcel($path) {
        try {
            $headerRowNumber = 1;
            Config::set('excel.import.startRow', $headerRowNumber);
            Config::set('excel.import.heading', 'numeric');
            $cat_data = Excel::selectSheetsByIndex(0)->load($path, function($reader) {
                    })->first();
            $headerRowNumber = 1;
            Config::set('excel.import.startRow', $headerRowNumber);
            Config::set('excel.import.heading', 'slugged');
            $prod_data = Excel::selectSheetsByIndex(0)->load($path, function($reader) {
                    })->get();
            $data['cat_data'] = $cat_data;
            $data['prod_data'] = $prod_data;
            return $data;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
        }
    }

    public function readImportExcel($path) {
        try {
            $headerRowNumber = 1;
            Config::set('excel.import.startRow', $headerRowNumber);
            Config::set('excel.import.heading', 'false');
            $cat_data = Excel::selectSheetsByIndex(0)->load($path, function($reader) {
                        
                    })->first();
            
            $headerRowNumber = 1;
            Config::set('excel.import.startRow', $headerRowNumber);
            Config::set('excel.import.heading', 'false');
            $headres = Excel::selectSheetsByIndex(0)->load($path, function($reader) {
                        
                    })->first();
            
            $headerRowNumber = 3;
            Config::set('excel.import.startRow', $headerRowNumber);
            Config::set('excel.import.heading', 'slugged');
            $prod_data = Excel::selectSheetsByIndex(0)->load($path, function($reader) {
                        
                    })->get();
            $data['po_data'] = $cat_data;
            $data['header_data'] = $headres;
            $data['prod_data'] = $prod_data;
            return $data;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
        }
    }
    public function deletePoProduct() {
        try {
            $data = Input::get();
            $productId = $data['product_id'];
            $poId = $data['po_id'];
            $delete= $this->_poModel->deletePoProducts($poId,$productId);
            return Response::json(array('status' => 200, 'Message' => $delete));
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
        }
    }
    public function closePO() {
        try {
            $data = Input::get();
            $poId = $data['po_id'];
            $reason = $data['close_reason'];
            $po_status = $data['po_status'];
            $this->_poModel->closePO($poId,$po_status,$reason);
            return Response::json(array('status' => 200, 'message' => 'Updated Successfully'));
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            return Response::json(array('status' => 400, 'message' => $ex->getMessage()));
        }
    }
    /*
     * filterData() method is used to prepare filters condition from string
     * @param $filter String
     * @return Array
     */
    private function filterData($filter) {
        try {
            $stringArr = explode(' and ', $filter);
            $filterDataArr = array();
            if (is_array($stringArr)) {
                foreach ($stringArr as $data) {
                    $dataArr = explode(' ', $data);
                    if (substr_count($data, 'createdOn')) {
                        $filterDataArr['createdOn']['operator'] = $this->getCondOperator($dataArr[1]);
                        if (substr_count($dataArr[2], 'DateTime')) {
                            $dataArrr = explode("'", $dataArr[2]);
                            $time = strtotime($dataArrr[1]);
                            $filterDataArr['createdOn'][] = date("d", $time);
                            $filterDataArr['createdOn'][] = date("m", $time);
                            $filterDataArr['createdOn'][] = date("Y", $time);
                        } else {
                            $filterDataArr['createdOn'][] = $dataArr[2];
                        }
                    }
                    if (substr_count($data, 'grn_created')) {
                        $filterDataArr['grn_created']['operator'] = $this->getCondOperator($dataArr[1]);
                        if (substr_count($dataArr[2], 'DateTime')) {
                            $dataArrr = explode("'", $dataArr[2]);
                            $time = strtotime($dataArrr[1]);
                            $filterDataArr['grn_created'][] = date("d", $time);
                            $filterDataArr['grn_created'][] = date("m", $time);
                            $filterDataArr['grn_created'][] = date("Y", $time);
                        } else {
                            $filterDataArr['grn_created'][] = $dataArr[2];
                        }
                    }
                    if (substr_count($data, 'payment_due_date')) {
                        $filterDataArr['payment_due_date']['operator'] = $this->getCondOperator($dataArr[1]);
                        if (substr_count($dataArr[2], 'DateTime')) {
                            $dataArrr = explode("'", $dataArr[2]);
                            $time = strtotime($dataArrr[1]);
                            $filterDataArr['payment_due_date'][] = date("d", $time);
                            $filterDataArr['payment_due_date'][] = date("m", $time);
                            $filterDataArr['payment_due_date'][] = date("Y", $time);
                        } else {
                            $filterDataArr['payment_due_date'][] = $dataArr[2];
                        }
                    }
                    if (substr_count($data, 'po_grn_diff')) {
                        $filterDataArr['po_grn_diff']['operator'] = $this->getCondOperator($dataArr[1]);
                        $filterDataArr['po_grn_diff']['value'] = $dataArr[2];
                    }
                    if (substr_count($data, 'poValue')) {
                        $filterDataArr['poValue']['operator'] = $this->getCondOperator($dataArr[1]);
                        $filterDataArr['poValue']['value'] = $dataArr[2];
                    }
                    if (substr_count($data, 'grn_value')) {
                        $filterDataArr['grn_value']['operator'] = $this->getCondOperator($dataArr[1]);
                        $filterDataArr['grn_value']['value'] = $dataArr[2];
                    }
                    if (substr_count($data, 'poId') && !array_key_exists('poId', $filterDataArr)) {
                        $poIdValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'poId'), '', $data));
                        $value = (isset($poIdValArr[1]) && $poIdValArr[1] == 'eq' && isset($poIdValArr[2])) ? $poIdValArr[2] : '%'.$poIdValArr[0].'%';
                        $operator = (isset($poIdValArr[1]) && $poIdValArr[1] == 'eq') ? '=' : 'LIKE';
                        $filterDataArr['poId'] = array('operator' => $operator, 'value' => $value);
                    }

                    if (substr_count($data, 'duedays') && !array_key_exists('duedays', $filterDataArr)) {
                        $suppcodeValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'duedays'), '', $data));
                        $value = (isset($suppcodeValArr[1]) && $suppcodeValArr[1] == 'eq' && isset($suppcodeValArr[2])) ? $suppcodeValArr[2] : '%'.$suppcodeValArr[0].'%';
                        $operator = (isset($suppcodeValArr[1]) && $suppcodeValArr[1] == 'eq') ? '=' : 'LIKE';
                        $filterDataArr['duedays'] = array('operator' => $operator, 'value' => $value);
                    }

                    if (substr_count($data, 'le_code') && !array_key_exists('le_code', $filterDataArr)) {
                        $suppcodeValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'le_code'), '', $data));
                        $value = (isset($suppcodeValArr[1]) && $suppcodeValArr[1] == 'eq' && isset($suppcodeValArr[2])) ? $suppcodeValArr[2] : '%'.$suppcodeValArr[0].'%';
                        $operator = (isset($suppcodeValArr[1]) && $suppcodeValArr[1] == 'eq') ? '=' : 'LIKE';
                        $filterDataArr['le_code'] = array('operator' => $operator, 'value' => $value);
                    }
                    if (substr_count($data, 'Supplier') && !array_key_exists('Supplier', $filterDataArr)) {
                        $sup = explode('ge', $data);
                        $pos = strpos($data, 'eq');
                        $suppValArr = str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'Supplier','eq '), '', $sup[0]);
                        $value = ($pos>0) ? trim($suppValArr,' ') : '%'.trim($suppValArr,' ').'%';
                        $operator = ($pos>0) ? '=' : 'LIKE';
                        $filterDataArr['Supplier'] = array('operator' => $operator, 'value' => $value);
                    }

                    if (substr_count($data, 'shipTo') && !array_key_exists('shipTo', $filterDataArr)) {
                        $sup = explode('ge', $data);
                        $pos = strpos($data, 'eq');
                        $shipToValArr = str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'shipTo','eq '), '', $sup[0]);
                        $value = ($pos>0) ? trim($shipToValArr,' ') : '%'.trim($shipToValArr,' ').'%';
                        $operator = ($pos>0) ? '=' : 'LIKE';
                        $filterDataArr['shipTo'] = array('operator' => $operator, 'value' => $value);
                    }

                    if (substr_count($data, 'validity') && !array_key_exists('validity', $filterDataArr)) {
                        $sup = explode('ge', $data);
                        $pos = strpos($data, 'eq');
                        $validityValArr = str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'validity','eq '), '', $sup[0]);
                        $value = ($pos>0) ? trim($validityValArr,' ') : '%'.trim($validityValArr,' ').'%';
                        $operator = ($pos>0) ? '=' : 'LIKE';
                        $filterDataArr['validity'] = array('operator' => $operator, 'value' => $value);
                    }
                    if (substr_count($data, 'payment_mode') && !array_key_exists('payment_mode', $filterDataArr)) {
                        $paymentValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'payment_mode'), '', $data));
                        $modes = array(1 => 'post paid', 2 => 'pre paid');
                        $value = ($paymentValArr[0] == '' && $paymentValArr[1] == 'eq' && isset($paymentValArr[2])) ? $paymentValArr[2] : $paymentValArr[0];
                        $input = preg_quote($value, '~'); // don't forget to quote input string!
                        $result = preg_grep('~' . $input . '~', $modes);
                        $mode = array();
                        foreach ($result as $key => $val) {
                            $mode[] = $key;
                        }
                        $filterDataArr['payment_mode'] = array('operator' => '=', 'value' => $mode);
                    }
                    if (substr_count($data, 'tlm_name') && !array_key_exists('tlm_name', $filterDataArr)) {
                        $sup = explode('ge', $data);
                        $pos = strpos($data, 'eq');
                        $tlm_nameValArr = str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'tlm_name','eq '), '', $sup[0]);
                        $value = ($pos>0) ? trim($tlm_nameValArr,' ') : '%'.trim($tlm_nameValArr,' ').'%';
                        $operator = ($pos>0) ? '=' : 'LIKE';
                        $filterDataArr['tlm_name'] = array('operator' => $operator, 'value' => $value);
                    }

                    if (substr_count($data, 'Status') && !array_key_exists('Status', $filterDataArr)) {
                        $sup = explode('ge', $data);
                        $pos = strpos($data, 'eq');
                        $poStatusValArr = str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'Status','eq '), '', $sup[0]);
                        $value = ($pos>0) ? trim($poStatusValArr,' ') : '%'.trim($poStatusValArr,' ').'%';
                        $operator = ($pos>0) ? '=' : 'LIKE';
                        $filterDataArr['Status'] = array('operator' => $operator, 'value' => $value);
                    }
                    if (substr_count($data, 'payment_status') && !array_key_exists('payment_status', $filterDataArr)) {
                        $sup = explode('ge', $data);
                        $pos = strpos($data, 'eq');
                        $poStatusValArr = str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'payment_status','eq '), '', $sup[0]);
                        $value = ($pos>0) ? trim($poStatusValArr,' ') : '%'.trim($poStatusValArr,' ').'%';
                        $operator = ($pos>0) ? '=' : 'LIKE';
                        $filterDataArr['payment_status'] = array('operator' => $operator, 'value' => $value);
                    }
                    if (substr_count($data, 'approval_status') && !array_key_exists('approval_status', $filterDataArr)) {
                        $sup = explode('ge', $data);
                        $pos = strpos($data, 'eq');
                        $poStatusValArr = str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'approval_status','eq '), '', $sup[0]);
                        $value = ($pos>0) ? trim($poStatusValArr,' ') : '%'.trim($poStatusValArr,' ').'%';
                        $operator = ($pos>0) ? '=' : 'LIKE';
                        $filterDataArr['approval_status'] = array('operator' => $operator, 'value' => $value);
                    }
                    if (substr_count($data, 'po_so_order_link') && !array_key_exists('po_so_order_link', $filterDataArr)) {
                        $sup = explode('ge', $data);
                        $pos = strpos($data, 'eq');
                        $poStatusValArr = str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'po_so_order_link','eq '), '', $sup[0]);
                        $value = ($pos>0) ? trim($poStatusValArr,' ') : '%'.trim($poStatusValArr,' ').'%';
                        $operator = ($pos>0) ? '=' : 'LIKE';
                        $filterDataArr['po_so_order_link'] = array('operator' => $operator, 'value' => $value);
                    }
                    if (substr_count($data, 'po_parent_link') && !array_key_exists('po_parent_link', $filterDataArr)) {
                        $sup = explode('ge', $data);
                        $pos = strpos($data, 'eq');
                        $poStatusValArr = str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'po_parent_link','eq '), '', $sup[0]);
                        $value = ($pos>0) ? trim($poStatusValArr,' ') : '%'.trim($poStatusValArr,' ').'%';
                        $operator = ($pos>0) ? '=' : 'LIKE';
                        $filterDataArr['po_parent_link'] = array('operator' => $operator, 'value' => $value);
                    }
                    if (substr_count($data, 'createdBy') && !array_key_exists('createdBy', $filterDataArr)) {
                        $sup = explode('ge', $data);
                        $pos = strpos($data, 'eq');
                        $createdByValArr = str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'createdBy','eq '), '', $sup[0]);
                        $value = ($pos>0) ? trim($createdByValArr,' ') : '%'.trim($createdByValArr,' ').'%';
                        $operator = ($pos>0) ? '=' : 'LIKE';
                        $filterDataArr['createdBy'] = array('operator' => $operator, 'value' => $value);
                    }
                }
            }
            return $filterDataArr;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    /*
     * getCondOperator() method is used to get condition operator
     * @param $operator String
     * @return String
     */
    private function getCondOperator($operator) {
        try {
            switch ($operator) {
                case 'eq' :
                    $condOperator = '=';
                    break;

                case 'ne':
                    $condOperator = '!=';
                    break;

                case 'gt' :
                    $condOperator = '>';
                    break;

                case 'lt' :
                    $condOperator = '<';
                    break;

                case 'ge' :
                    $condOperator = '>=';
                    break;

                case 'le' :
                    $condOperator = '<=';
                    break;
            }
            return $condOperator;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    /**
     * uploadDocumentAction() method is use upload document
     * @param  $request Object
     * @return JSON
     */

    public function uploadDocumentAction(Request $request) {
        try{
            $postData = Input::all();
            $po_id = isset($postData['po_id']) ? $postData['po_id'] : 0;
            if ($request->hasFile('upload_file')) {
                $extension = Input::file('upload_file')->getClientOriginalExtension();
                
                if(!in_array($extension, array('pdf', 'doc', 'docx', 'png', 'jpg', 'jpeg','jfif','JPG','PNG','JPEG','JFIF'))) {
                    $msg = 'Please upload only pdf, doc, docx, png, jpg, jpeg,jfif extensions.';
                    return json_encode(array('status'=>400, 'message'=>$msg));
                }
                $imageObj = $request->file('upload_file');
               $url = $this->_productRepo->uploadToS3($imageObj,'po_proforma',1);
                //$url = "";
                if($url!='') {
                    $docsArr = array(
                        'po_id'=>$po_id,
                        'file_path'=>$url,
                        'created_at'=>date('Y-m-d H:i:s')
                    );
                    $doc_id=$this->_poModel->saveDocument($docsArr);
                    Session::push('podocs', $doc_id);
                    $docText='<div><span><i class="fa fa-close downloadclose" data-doc_id="'.$doc_id.'"></i></span>'
                            . '<a href="'.$url.'"  target ="_blank" class="closedownload" ><i class="fa fa-download" ></i></a></div>';
                    return json_encode(array('status'=>200, 'message'=>Lang::get('inward.successUploaded'),'docText'=>$docText));
                }
            }
            else {
                return json_encode(array('status'=>200, 'message'=>Lang::get('salesorders.errorInputData')));
            }
        }
        catch(Exception $e) {
            return json_encode(array('status'=>400, 'message'=>Lang::get('salesorders.errorInputData')));
        }
    }
    public function poDocUpdate($po_id) {
        try {
            $podocid = Session::get('podocs');
            if (isset($podocid) && is_array($podocid) && count($podocid) > 0) {
                foreach ($podocid as $docid) {
                    $this->_poModel->poDocUpdate($po_id, $docid);
                    Session::put('podocs', array());
                }
            }
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function deleteDoc($doc_id) {
        try {
            $this->_poModel->deleteDoc($doc_id);
            return json_encode(['status'=>'200','message'=>'doc deleted successfully']);
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    /**
     * poPutawayCompleted() method is update the po approval_status to "shelved" when putaway completed
     * @param
     * @return JSON
     */
    public function poPutawayCompleted($grn_id, $user_id)
    {
        try
        {
            $inwardModel = new Inward();
            $inwardData = $inwardModel->getPOIdByInwardId($grn_id);
            $po_id = isset($inwardData->po_id)?$inwardData->po_id:'';
            if($po_id!=''){
                $userObj = new Users();
                $userInfo = $userObj->getUsers($user_id);
                $userName = isset($userInfo->firstname)?$userInfo->firstname.' '.$userInfo->lastname:' ';
                $current_status=(isset($inwardData->approval_status))?$inwardData->approval_status:'';
                $this->_poModel->updatePO($po_id, ['approval_status'=>1,'approved_by'=>$user_id,'approved_at'=>date('Y-m-d H:i:s')]);
                $approval_flow_func = new CommonApprovalFlowFunctionModel();
                $approval_flow_func->storeWorkFlowHistory('Purchase Order', $po_id, $current_status, 57108, 'Putaway completed By '.$userName, $user_id); //57108 - shelved
            }
            return;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }
    public function excelReports($orderId) {
        try {  
           $po_excel = array();         
           $po_excel = $this->printPo($orderId);
                Excel::create('po_excel', function($excel) use($po_excel) {  
                    $excel->sheet('po_excel', function($sheet) use($po_excel) {  
                            $sheet->setOrientation('landscape');
                            $sheet->setMergeColumn(array(
                              'columns' => array('A','B','C','D','E','F','G','H','I','J'),
                               'rows' => array(array(1,2),)));

                            $sheet->cells('A4:J4', function($cells)
                            {
                                $cells->setValignment('top');
                            });
                         $sheet->setSize(array( 
                            'A4' => array('width'     => 50,
                                        'height'    => 100
                                         )
                                   ));
                            $sheet->loadView('PurchaseOrder::poExcelSheet')->with('loadSheet', $po_excel);
                        });
                })->export('xls');
        
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
   public function printPo($id) {

        try {

            $hasAccess = $this->_roleRepo->checkPermissionByFeatureCode('PO004');
            if ($hasAccess == false) {
                return View::make('PurchaseOrder::error');
            }

                        
            $poDetailArr = $this->_poModel->getPoDetailById($id);

            if (count($poDetailArr) == 0) {
                Redirect::to('/po/index')->send();
                die();
            }
            $packTypes = $this->_masterLookup->getAllOrderStatus('Levels');
            $productTaxArr = array();           
            
            $leWhId = isset($poDetailArr[0]->le_wh_id) ? $poDetailArr[0]->le_wh_id : 0;
            $indentId = isset($poDetailArr[0]->indent_id) ? $poDetailArr[0]->indent_id : 0;
            $leId = isset($poDetailArr[0]->legal_entity_id) ? $poDetailArr[0]->legal_entity_id : 0;

            $indentCode = '';
            if ($indentId) {
                $indentCode = $this->_indent->getIndentCodeById($indentId);
            }

            // company admin id
            $leParentId = $this->_LegalEntity->getLeParentIdByLeId($leId);
            if($leParentId)
                $leDetail = $this->_LegalEntity->getLegalEntityById($leParentId);
            else
                $leDetail = $this->_LegalEntity->getLegalEntityById($leId);
            $whDetail = $this->_LegalEntity->getWarehouseById($leWhId);
            $userInfo = $this->_poModel->getUserByLeId($leId);
            $supplierInfo = $this->_poModel->getLegalEntityById($leId);
            $companyInfo = $this->_LegalEntity->getCompanyAccountByLeId($leParentId);
            
            foreach ($poDetailArr as $key => $product) {
                $product_id = $product->product_id;
                $newProduct = $this->_poModel->verifyNewProductInWH($leWhId, $product_id);
                if ($newProduct == 0) {
                    $newPrClass = 'class=newproduct';
                } else {
                    $newPrClass = '';
                }
                $product->newPrClass = $newPrClass;
                $poDetailArr[$key] = $product;
            }
            $PaymentTypes = $this->_masterLookup->getAllOrderStatus('Payment Type',[2,3]);
            $payment_type = isset($poDetailArr[0]->payment_type) ? $poDetailArr[0]->payment_type : 0;
            $paymentType = isset($PaymentTypes[$payment_type]) ? $PaymentTypes[$payment_type] : '';
            
            $poStatusArr = $this->_masterLookup->getAllOrderStatus('PURCHASE_ORDER');
            $po_status = isset($poDetailArr[0]->po_status) ? $poDetailArr[0]->po_status : 0;
            $poStatus = isset($poStatusArr[$po_status]) ? $poStatusArr[$po_status] : '';
            $approvalStatus = $this->_masterLookup->getAllOrderStatus('Approval Status');
            $approvedStatus = (isset($approvalStatus[$poDetailArr[0]->approval_status])) ? $approvalStatus[$poDetailArr[0]->approval_status] : '';
            if ($poDetailArr[0]->approval_status == 1) {
                $approvedStatus = 'Approved';
            }

            $taxBreakup = $this->getTaxBreakup($poDetailArr);
            $loadSheet = array();
            $loadSheet['packTypes'] = $packTypes;
            $loadSheet['supplier'] = $supplierInfo;
            $loadSheet['leDetail'] = $leDetail;
            $loadSheet['whDetail'] = $whDetail;
            $loadSheet['userInfo'] = $userInfo;
            $loadSheet['companyInfo'] = $companyInfo;
            $loadSheet['paymentType'] = $paymentType;
            $loadSheet['poStatus'] = $poStatus;
            $loadSheet['approvedStatus'] = $approvedStatus;
            $loadSheet['taxBreakup'] = $taxBreakup;
            $loadSheet['indentCode'] = $indentCode;
            $loadSheet['productArr']=$poDetailArr;
            return $loadSheet;
            
        } catch (Exception $e) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }
   public function splitPOAction($po_id) {
        try {
            $hasAccess = $this->_roleRepo->checkPermissionByFeatureCode('PO004');
            if ($hasAccess == false) {
                return View::make('PurchaseOrder::error');
            }
            $childPOexist = $this->_poModel->checkChildPoExist($po_id);
            if($childPOexist>0){
                return json_encode(['status'=>200,'message'=>'Already has child POs']);
            }
            $poDetailArr = $this->_poModel->getPoProdutDetailById($po_id);
            $manfProducts =[];
            $masvalue = $this->_masterLookup->getMasterLokup(78021);
            $virtualdc = isset($masvalue->description)?$masvalue->description:0;
            foreach($poDetailArr as $key=>$poproducts){
                $manfId = (isset($poproducts->manufacturer_id)&& $poproducts->manufacturer_id!='')?$poproducts->manufacturer_id:0;
                $manf_name = (isset($poproducts->manf_name)&& $poproducts->manf_name!='')?$poproducts->manf_name:0;
                $le_wh_id = (isset($poproducts->le_wh_id)&& $poproducts->le_wh_id!='')?$poproducts->le_wh_id:0;
                $supmapping = $this->_poModel->getManfMapSupplier($le_wh_id,$manfId);
                $supId = isset($supmapping->legal_entity_id)?$supmapping->legal_entity_id:0;
                if($supId!='' && $supId!=0){
                    $product_id = isset($poproducts->product_id)?$poproducts->product_id:0;
                    $product_title = isset($poproducts->product_title)?$poproducts->product_title:0;
                    $legal_entity_id = isset($poproducts->legal_entity_id)?$poproducts->legal_entity_id:0;
                    //$supdetails = $this->_poModel->getSupplierId($legal_entity_id,$product_id);
                    $subscribe = $this->_poModel->checkProductSuscribe($supId,$virtualdc,$product_id);
                    if(count($subscribe)>0 && isset($subscribe->subscribe)){
                        if($subscribe->subscribe==0){
                            $product_tot = ['subscribe'=>1];
                            $this->_poModel->updateProductTot($product_tot,$supId,$virtualdc,$product_id);
                        }
                    } else {
                        $product_tot = ['product_id'=>$product_id,'le_wh_id'=>$virtualdc,'supplier_id'=>$supId,'product_name'=>$product_title,'is_active'=>1,'subscribe'=>1];
                        $this->_poModel->saveProductTot($product_tot);
                    }
                    if($supId>0){
                        $manfProducts[$supId][]=$product_id;
                    }
                }else{
                    return json_encode(['status'=>200,'message'=>'No Supplier Mapped for '.$manf_name.' Manufacturer']);
                }
            }
            
            $whdata = $this->_poModel->getLEWHById($le_wh_id);
            $state_code = isset($whdata->state_code)?$whdata->state_code:"TS";

            foreach($manfProducts as $manf=>$products){
                $poArr = $this->_poModel->getPoById($po_id);
                $poArr = json_decode(json_encode($poArr),true);
                $remove = ['po_id','po_code','created_at','updated_by','updated_at','approved_by','approved_at'];
                $poArr = array_diff_key($poArr, array_flip($remove));
                //echo '<pre/>';
                $serialNumber = Utility::getReferenceCode("PO",$state_code);
                $poArr['legal_entity_id']=$manf;
                $poArr['parent_id']=$po_id;
                $poArr['le_wh_id']=$virtualdc;
                $poArr['po_code']=$serialNumber;
                $poArr['created_by']=\Session::get('userId');
                $newpoId = $this->_poModel->savePo($poArr);
                //$newpo = $this->_poModel->create($poArr);
                //$newpoId = $newpo->po_id;
                $saveProducts=[];
                foreach($products as $product_id){
                    $poProductArr = $this->_poModel->getPoProdutsById($po_id,$product_id);
                    $poProductArr = json_decode(json_encode($poProductArr),true);
                    $prremove = ['po_product_id','po_id','created_at'];
                    $poProductArr = array_diff_key($poProductArr, array_flip($prremove));
                    $poProductArr['po_id']=$newpoId;
                    // fetcing data for elp for child PO
                    $dlp = $this->_poModel->getTotData($product_id,$poArr['le_wh_id'],$manf);
                    // calculating new tax and totals
                    if($dlp>0){
                        $poProductArr['is_tax_included'] = 1;
                        $poProductArr['cur_elp'] = $dlp;
                        $poProductArr['unit_price'] = $dlp;
                        $poProductArr['price'] = $poProductArr['no_of_eaches'] * $dlp;
                        $poProductArr['sub_total'] = $poProductArr['price'] * $poProductArr['qty'];
                        $poProductArr['tax_amt'] = $poProductArr['sub_total'] - ($poProductArr['sub_total']/(100+$poProductArr['tax_per']) * 100);
                        $poProductArr['tax_amt'] = number_format((float)$poProductArr['tax_amt'],5,'.','');
                    }
                    $saveProducts[] = $poProductArr;
                }
                if(count($saveProducts)>0){
                    $this->_poModel->savePoProducts($saveProducts);
                }
            }
            return json_encode(['status'=>200,'message'=>'Successfully created PO']);
        } catch (Exception $e) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }
    /*
    * function: placeNewOrder
    * This function is to create an order from the PO
    */
    public function placeNewOrder($id,$poData=array())
    {
        // Validations
        if(count($poData)>0){
            $_POST = $poData;       
        }
        if($id == "" or $id == null)
            return ["status" => 'failure', "message" => "Improper Request, Please refresh the page and try again"];
        if(!isset($_POST['dc_id']) or ($_POST['dc_id'] == "" or $_POST['dc_id'] == null))
            return ["status" => 'failure', "message" => "Please select a Dc"];
        if(!isset($_POST['state_id']) or ($_POST['state_id'] == "" or $_POST['state_id'] == null))
            return ["status" => 'failure', "message" => "Please select a State"];
        if(!isset($_POST['product_ids']) or ($_POST['product_ids'] == "" or $_POST['product_ids'] == null))
            return ["status" => 'failure', "message" => "Please select atlease 1 Product to place an Order"];
        if(!isset($_POST['po_id']) or ($_POST['po_id'] == "" or $_POST['po_id'] == null))
            return ["status" => 'failure', "message" => "Invalid PO Id"];

        // Now we insert the Product Ids in to Cart
       
        $poDetailArr = $this->_poModel->getPoDetailById($_POST['po_id']);
        if($poDetailArr[0]->po_so_status == 0){
            $scheduled_delivery_date = date('Y-m-d 00:00:00' ,time() + 2 * 86400);
            $le_wh_id = $poDetailArr[0]->le_wh_id;
            $so_le_wh_id = $_POST['dc_id'];
            $contact_data = $this->_poModel->getLEWHById($le_wh_id);
            $legal_entity_id = $contact_data->legal_entity_id;
            $credit_limit_check = $contact_data->credit_limit_check;
            $customer_type_id = $this->_poModel->getStockistPriceGroup($legal_entity_id,$le_wh_id);
            if(isset($poDetailArr[0]->stock_transfer) && $poDetailArr[0]->stock_transfer == 1){
                return ["status" => 'failure', "message" => "Order cannot be created for Stock Transfer PO!"];
            }
            if($customer_type_id == 0){
                return ["status" => 'failure', "message" => "Pricing not found for stockist!"];
            }
            $po_id = $_POST['po_id'];
            $checkPricingMismatch = $this->_poModel->checkPricingMismatch($po_id,$customer_type_id,$_POST['dc_id']);
            $priceMismatch = '<tr class="subhead">
                                <th width="66%" align="left" valign="middle">Product Name (SKU) </th>
                                <th width="17%" align="left" valign="middle">PO Price</th>
                                <th width="17%" align="left" valign="middle">Selling Price</th>
                                <th width="17%" align="left" valign="middle">CP Enable</th>
                                <th width="17%" align="left" valign="middle">Is Sellable</th>
                            </tr>';
            foreach ($checkPricingMismatch as $key => $value) {
                $cp_enable = ($value->cp_enable == 1)?"<span style='color:green'>Yes</span>":"<span style='color:red'>No</span>";
                $is_sellable = ($value->is_sellable == 1)?"<span style='color:green'>Yes</span>":"<span style='color:red'>No</span>";

                $priceMismatch .= '<tr class="subhead priceerrorname">
                                        <td align="left" valign="middle"><b>'.$value->prd_name.' <span style="color:blue"><b>('.$value->sku.')</b></span></b></td>
                                        <td style="color:red" align="left" valign="middle">'.$value->po_price.'</td>
                                        <td align="left" valign="middle">'.$value->slab_price.'</td>
                                        <td align="left" valign="middle">'.$cp_enable.'</td>
                                        <td align="left" valign="middle">'.$is_sellable.'</td>
                                    </tr>';
            }
            if(count($checkPricingMismatch) > 0){
                return ["status" => 'failure', "message" => "pricing_mismatch_found","reason" => "Pricing Mismatch",'data'=>$priceMismatch,"adjust_message"=>"Adjust all mismatch prices to selling prices."];
            }

            $mobile_number = $contact_data->phone_no;
            $customer_data = $this->_poModel->getCustomerDataByNo($mobile_number);
            if(!isset($customer_data->user_id)){
                return ["status" => 'failure', "message" => "No user data found for $mobile_number!"];
            }
            $checkLOC = $this->_poModel->checkLOCByLeID($legal_entity_id);
            $cartFinalArray = array();
            $cartTotal = 0;
            $user_id = $customer_data->user_id;
            $customer_token = $customer_data->password_token;

            $discount_before_tax = isset($poDetailArr[0]->discount_before_tax) ? $poDetailArr[0]->discount_before_tax : 0;
            $bill_discount_type = isset($poDetailArr[0]->discount_type) ? $poDetailArr[0]->discount_type : 0;
            $bill_discount = isset($poDetailArr[0]->discount) ? $poDetailArr[0]->discount : 0;
            if($customer_token == "" || $customer_token == NUll){
                $customer_token = md5(uniqid(mt_rand(), true));
                $this->_poModel->updateCustomerToken($user_id,$customer_token);
            }
            $productPackData = array();
            $all_products_ids = array();
            $all_products_qtys = array();
            // foreach ($_POST['product_ids'] as $productId) {
            //     array_push($all_products_ids, $productId['product_id']);
            //     $all_products_qtys[$productId['product_id']] = $productId['qty'];
            // }
            $selected_product_ids = array_column($_POST['product_ids'], 'product_id');
            $product_inv_ids = array();
            $inventoryData = '<tr class="subhead">
                                <th width="66%" align="left" valign="middle">Product Name (SKU) </th>
                                <th width="17%" align="left" valign="middle">Avail Qty</th>
                                <th width="17%" align="left" valign="middle">PO Qty</th>
                            </tr>';
            $product_prc_ids = array();
            $priceNotFoundData = '<tr class="subhead">
                                <th width="66%" align="left" valign="middle">Product Name (SKU) </th>
                                <th width="17%" align="left" valign="middle">PO Price</th>
                                <th width="17%" align="left" valign="middle">Selling Price</th>
                                <th width="17%" align="left" valign="middle">CP Enable</th>
                                <th width="17%" align="left" valign="middle">Is Sellable</th>
                            </tr>';
            foreach ($poDetailArr as $key => $poDetail) {
                if(in_array($poDetail->product_id,$selected_product_ids)){
                    $cartTotal += ($poDetail->unit_price*$poDetail->no_of_eaches*$poDetail->qty);
                    $qty = $poDetail->qty;
                    $uom = $poDetail->uom;
                    $product_title = $poDetail->product_title;
                    $sku = $poDetail->sku;
                    $unit_price = $poDetail->unit_price;

                    $subTotal = $poDetail->unit_price*$poDetail->no_of_eaches*$poDetail->qty;
                    $product_id = $poDetail->product_id;
                     // calculating discount amount
                    $discountType = "percentage";
                    if($poDetail->item_discount_type == 0 && $bill_discount_type == 0)
                        $discountType = "value";
                    $discountOn = "Product";
                    // product level discount
                    $discount = $poDetail->item_discount;

                    if(($bill_discount_type == 1 && $discount_before_tax == 1) || $bill_discount_type == 1){
                        $discount = $bill_discount;
                    }                    
                    if($discount_before_tax == 1 && $discountType == "percentage"){
                        $subTotal = ($poDetail->sub_total - $poDetail->tax_amt);
                    }
                    if($bill_discount_type == 1 && $discount_before_tax==0){
                        $discount=0;
                        $discountType='';
                        $discountOn='';
                    }
                    $freebieConfig = $this->_poModel->getFreebieProducts($product_id);
                    $freebee_mpq = 0;
                    $freebee_qty = 0.0;
                    $checkFreebiee = $this->_poModel->getFreebieParent($product_id);
                    $parent_id = isset($checkFreebiee->main_prd_id)?$checkFreebiee->main_prd_id:$product_id;
                    if(count($freebieConfig)>0 && !empty($freebieConfig)){
                        $freebieConfig = $freebieConfig[0];
                        $freebee_mpq =  $freebieConfig->mpq;
                        $freebee_qty = $freebieConfig->qty;
                        $freebieProductId = $product_id;

                    }

                    // checking price
                    $appKeyData = env('DB_DATABASE');
                    $keyString = $appKeyData . '_product_slab_' . $product_id . '_customer_type_' . $customer_type_id.'_le_wh_id_'.$so_le_wh_id;
                    //Log::info($keyString);
                    $response = Cache::get($keyString);
                    //Log::info($response);
                    $unitPriceData = ($response != '') ? (json_decode($response, true)) : [];
                    $temp = trim($so_le_wh_id, "'");
                    $temp = str_replace(',', '_', $temp);
                    if ($user_id == 0) {
                        $temp = 0;
                    }
                    $availQty = $this->_poModel->checkInventory($product_id,$so_le_wh_id);
                    if (isset($unitPriceData[$temp]) && count($unitPriceData[$temp])) {
                        $CheckUnitPrice = $unitPriceData[$temp];
                        $tempDetails = [];
                        if (isset($availQty)) {
                            foreach ($CheckUnitPrice as $slabData) {
                                if (isset($slabData['stock'])) {
                                    $slabData['stock'] = $availQty;
                                }
                                $tempDetails[] = $slabData;
                            }
                        }
                        if (!empty($tempDetails)) {
                            $CheckUnitPrice = $tempDetails;
                        }
                        $unitPriceData[$temp] = json_decode(json_encode($CheckUnitPrice), true);
                        Cache::put($keyString, json_encode($unitPriceData), 60);
                    } else {
                        $productSlabs = DB::selectFromWriteConnection(DB::raw("CALL ProdSlabFlatRefreshByProductId($product_id,$so_le_wh_id)"));
                        $CheckUnitPrice = DB::selectFromWriteConnection(DB::raw("CALL getProductSlabsByCust($product_id,'" . $so_le_wh_id . "',$user_id,$customer_type_id)"));
                        $unitPriceData[$temp] = json_decode(json_encode($CheckUnitPrice), true);
                        if(count($CheckUnitPrice))
                            Cache::put($keyString, json_encode($unitPriceData), 60);
                    }
                    $packSizeArr = array();
                    $cartObj = new CartModel();
                    $isFreebie = 0;
                    $isFreebie = $cartObj->isFreebie($product_id);
                    $packConfigdata = $this->_poModel->getProductPackUOMInfo($product_id,$uom);
                    if(!count($CheckUnitPrice) && !$isFreebie ){
                        
                        array_push($product_prc_ids, $product_id);
                        $packSizeArr = array();
                        foreach ($CheckUnitPrice as $price) {
                            if (is_array($price)) {
                                //Log::info('This is array');
                                $packSizeArr[$price['pack_size']] = $price['unit_price'];
                            } elseif (is_object($price)) {
                                //Log::info('This is object');
                                $packSizeArr[$price->pack_size] = $price->unit_price;
                            }
                        }
                        $poProductQty = $packConfigdata->no_of_eaches * $qty;
                        $packSizePrice = $cartObj->getPackPrice($poProductQty, $packSizeArr);
                        if($packSizePrice=="" || !count($packSizePrice))
                            $CheckUnitPrice = "";
                        else{
                            $CheckUnitPrice = $packSizePrice;
                        }
                        $cp_enable_data = $this->_poModel->getCPEnableData($product_id,$so_le_wh_id);
                        $cp_enable = (isset($cp_enable_data->cp_enabled) && $cp_enable_data->cp_enabled == 1)?"<span style='color:green'>Yes</span>":"<span style='color:red'>No</span>";
                        $is_sellable = (isset($cp_enable_data->is_sellable) && $cp_enable_data->is_sellable == 1)?"<span style='color:green'>Yes</span>":"<span style='color:red'>No</span>";
                        $priceNotFoundData .= '<tr class="subhead priceerrorname">
                                            <td align="left" valign="middle"><b>'.$product_title.' <span style="color:blue"><b>('.$poDetail->sku.')</b></span></b></td>
                                            <td style="color:red" align="left" valign="middle">'.$unit_price.'</td>
                                            <td align="left" valign="middle">'.$CheckUnitPrice.'</td>
                                            <td align="left" valign="middle">'.$cp_enable.'</td>
                                            <td align="left" valign="middle">'.$is_sellable.'</td>
                                                </tr>';
                    }
                    
                    // checking inventory

                    $cartObj = new CartModel();
                    $deleteCartArr = ['customer_token' => $customer_token, 'isClearCart' => 'true'];
                    $cartObj->deletecart($deleteCartArr);

                    
                    $availQty = $this->_poModel->checkInventory($product_id,$so_le_wh_id);
                    $poProductQty = $packConfigdata->no_of_eaches * $qty;

                    if($availQty < $poProductQty){
                        array_push($product_inv_ids, $product_id);
                        $inventoryData .= '<tr class="subhead priceerrorname">
                                            <td align="left" valign="middle"><b>'.$product_title.' <span style="color:blue"><b>('.$sku.')</b></span></b></td>
                                            <td style="color:red" align="left" valign="middle">'.$availQty.'</td>
                                            <td align="left" valign="middle">'.$poProductQty.'</td>
                                                </tr>';
                    }

                    $cartArray = array("product_id"=>$product_id,
                        "quantity"=>$packConfigdata->no_of_eaches * $qty,
                        "is_slab"=>1,
                        "blocked_qty"=>0,
                        "le_wh_id"=>$so_le_wh_id,
                        "segment_id"=>"48001",
                        "customer_type"=>$customer_type_id,
                        "total_price"=>$subTotal,
                        "unit_price"=>$poDetail->unit_price,
                        "applied_margin"=>0,
                        "esu_quantity"=>$qty,
                        'reserved_qty'=>$qty,
                        'parent_id'=>$parent_id,
                        "hub_id"=>$_POST['hub_id']);
                    array_push($cartFinalArray, $cartArray);
                    $productPackArr = array("product_id"=>$product_id,
                            "parent_id"=>$parent_id,
                            "total_qty"=>$packConfigdata->no_of_eaches * $qty,
                            "esu_quantity"=>$qty,
                            "total_price"=>$subTotal,
                            "applied_margin"=>0,
                            "discount"=>$discount,
                            "discount_type"=>$discountType,
                            "discount_on"=>$discountOn,
                            "unit_price"=>$poDetail->unit_price,
                            "is_slab"=>0,
                            "blocked_qty"=>0,
                            "star"=>$packConfigdata->starCode,
                            "hub"=>$_POST['hub_id'],
                            "prmt_det_id"=>0,
                            "product_slab_id"=>0,
                            "pack_level"=>$poDetail->uom,
                            "esu"=>$packConfigdata->esu,
                            "freebee_mpq"=>$freebee_mpq,
                            "freebee_qty"=>$freebee_qty,
                            "sku"=>$poDetail->sku,
                            "packs"=>array(array("esu"=>$packConfigdata->esu,
                                "qty"=>$qty ,
                                "pack_qty"=>$packConfigdata->no_of_eaches * $qty,
                                "pack_size"=>$packConfigdata->no_of_eaches,
                                "pack_level"=>$poDetail->uom,
                                "star"=>$packConfigdata->star,
                                "pack_cashback"=>""))
                            );
                    array_push($productPackData, $productPackArr);
                }
                
            }

            if(count($product_prc_ids)){
                return ["status" => 'failure', "reason" => "Pricing Not Found!","message" => "pricing_mismatch_found","adjust_message"=>"Please Clear Cache or Upload Prices",'data'=>$priceNotFoundData];
            }

            if(count($product_inv_ids)){
                return ["status" => 'failure',"reason" => "No Inventory!", "message" => "pricing_mismatch_found","adjust_message"=>"Add or Remove for No Inventory Products",'data'=>$inventoryData];
            }
            $cartContObj = new CartController();
            $checkCart = array("sales_rep_id"=>"",
                            "sales_token"=>"",
                            "customer_type"=>$customer_type_id,
                            "customer_token"=>$customer_token,
                            "le_wh_id"=>$_POST['dc_id'],
                            "customer_legal_entity_id"=>$legal_entity_id,
                            "is_web"=>1,
                            "segment_id"=>"48001",
                            "hub"=>$_POST['hub_id'],
                            "products"=>$productPackData
                            );
            // code to check loc is available or not!
            $availablebalance  = $checkLOC - $cartTotal;
            if($credit_limit_check == 1){
                if($availablebalance < 0){
                    return ["status" => 'failure', "message" => "Insufficient balance to place the order!"];
                }
            }
            //$cartResp = $this->_poModel->addToStockistCart($cartFinalArray,$user_id,$customer_token);
            $cartResp['cart'] = array();
            $cartCheckdata = json_decode($cartContObj->CheckCartInventory(json_encode($checkCart)),true);
            if(isset($cartCheckdata['status']) && $cartCheckdata['status'] != "success"){
                return ["status" => 'failure', "message" => $cartCheckdata['message']];
            }

             //Log::info('$cartCheckdata');
            //die;
            //Log::info($status_codes);           

            $cartIds = array_column($cartCheckdata['data'], 'cartId');
            if(count(array_filter($cartIds)) != count($cartIds)) {
                return ["status" => 'failure', "message" => "Cart Refreshed! Please Try Again."];
            }
            
            $seller_contact_data = $this->_poModel->getLEWHById($so_le_wh_id);
            $seller_state_code = isset($seller_contact_data->state_code)?$seller_contact_data->state_code:"TS";

            $serialNumber = Utility::getReferenceCode("SO",$seller_state_code);

            $invoice_flag = isset($_POST['invoice_flag'])?$_POST['invoice_flag']:0;
            $cartResp = array(
                            "sales_token"=>"",
                            "customer_token"=>$customer_token,
                            "customer_id"=>$user_id,
                            "address_id"=>"",
                            "order_level_cashback"=>"",
                            "otp"=>"",
                            "orderId"=>$serialNumber,
                            "cartId"=>$cartIds,
                            "total"=>$cartTotal,
                            "discountAmount"=>0,
                            "final_amount"=>$cartTotal,
                            "coupon_code"=>"",
                            "coupon_value"=>"",
                            "offerId"=>"",
                            "platform_id"=>"5004",
                            "paymentmode"=>"cod",
                            "wCollectTxnId"=>"",
                            "merchTranId"=>"",
                            "legal_entity_id"=>$legal_entity_id,
                            "pincode"=>"",
                            "le_wh_id"=>$_POST['dc_id'],
                            "hub"=>$_POST['hub_id'],
                            "segment_id"=>"47001",
                            "customer_type"=>$customer_type_id,
                            "scheduled_delivery_date"=>$scheduled_delivery_date,
                            "pref_value"=>"110003",
                            "pref_value1"=>"110001",
                            "latitude"=>0,
                            "longitude"=>0,
                            "po_id"=>$_POST['po_id'],
                            "ignore_discount_check"=>1,
                            "discount_on_tax_less"=>$discount_before_tax,
                            "auto_invoice"=>$invoice_flag);
            $ordObj = new OrderController();
            $orderResp = $ordObj->addOrder1(json_encode($cartResp));
            return $orderResp;
        }else{
            return ["status" => 'failure', "message" => "Order already placed!"];
        }
    }

    public function updateSupplier(Request $request){
        $_POST = $request->input();
        $new_supplier_id = $_POST['supp_name'];
        $po_id = $_POST['po_id'];
        $stock_transfer_dc = $_POST['stock_transfer_dc'];
        $supplier_id = $new_supplier_id;
        $supply_le_wh_id = $_POST['supply_le_wh_id'];
        $stock_transfer = $_POST['stock_transfer'];
        $warehouse_id = $_POST['warehouse_id'];
        $po = $this->_poModel->getPOPaymentRequests($po_id,[57203,57204,57218,57219,57222]);
        if(count($po)>0){
            return array('status'=>400,'message'=>'Please close payment requests initiated to cancel this PO', 'po_id'=>'');
        }
        if($stock_transfer > 0){

            if($supplier_id != 24766){
                return array('status'=>400, 'message'=>'Please select "Ebutor Supplier" to transfer stock', 'po_id'=>'');
            }
            if($stock_transfer_dc == $warehouse_id){
                return array('status'=>400, 'message'=>'"Stock Transfer Location" and "Delivery Location" should not same to transfer stock', 'po_id'=>'');
            }
            if($supply_le_wh_id > 0){
                return array('status'=>400, 'message'=>'Please uncheck "Stock Transfer" to Select "DC Supply".', 'po_id'=>'');
            }
            if($stock_transfer_dc == "" || $stock_transfer_dc == 0){
                return array('status'=>400, 'message'=>'Please select "Stock Transfer Location" to transfer stock', 'po_id'=>'');
            }

            $whDetail = $this->_LegalEntity->getWarehouseById($warehouse_id);
            $whDetailTypeId = isset($whDetail->legal_entity_type_id) ? $whDetail->legal_entity_type_id : 0;
            $stWhDetail = $this->_LegalEntity->getWarehouseById($stock_transfer_dc);
            $stWhDetailTypeId = isset($stWhDetail->legal_entity_type_id) ? $stWhDetail->legal_entity_type_id : 0;
            if($whDetailTypeId != 1001 || $stWhDetailTypeId != 1001){
                return array('status'=>400, 'message'=>'"Dispatch Location" and "Delivery Location" should be "APOB" to transfer stock.', 'po_id'=>'');
            }

            $whDetailStateId = isset($whDetail->state) ? $whDetail->state : 0;
            $stWhDetailStateId = isset($stWhDetail->state) ? $stWhDetail->state : 0;
            if($whDetailStateId != $stWhDetailStateId){
                return array('status'=>400, 'message'=>'"Dispatch Location" and "Delivery Location" should be in same state to transfer stock.', 'po_id'=>'');
            }

        }else if($stock_transfer_dc > 0){
            return array('status'=>400, 'message'=>'Please check "Stock Transfer" to transfer stock.', 'po_id'=>'');
        }
        $this->_poModel->updatePO($po_id, ['legal_entity_id'=>$new_supplier_id]);
        $poDetailArr = $this->_poModel->getPoDetailById($po_id);
        foreach ($poDetailArr as $key => $poDetail) {
            $this->subscribeProducts($new_supplier_id,$poDetail->le_wh_id,$poDetail->product_id);
        }
        $returnArray  = array("status"=>1,
            "message"=>"Supplier updated successfully.",
            "data"=>[]);
        return $returnArray;
    }

    public function updateSupplyDC(Request $request){
        $_POST = $request->input();
        $le_wh_id = $_POST['supply_dc_name'];
        $po_id = $_POST['po_id'];
        $stock_transfer_dc = $_POST['stock_transfer_dc'];
        $supplier_id = $_POST['supplier_id'];
        $supply_le_wh_id = $le_wh_id;
        $stock_transfer = $_POST['stock_transfer'];
        $warehouse_id = $_POST['warehouse_id'];
        if($stock_transfer > 0){

            if($supplier_id != 24766){
                return array('status'=>400, 'message'=>'Please select "Ebutor Supplier" to transfer stock', 'po_id'=>'');
            }
            if($stock_transfer_dc == $warehouse_id){
                return array('status'=>400, 'message'=>'"Stock Transfer Location" and "Delivery Location" should not same to transfer stock', 'po_id'=>'');
            }
            if($supply_le_wh_id > 0){
                return array('status'=>400, 'message'=>'Please uncheck "Stock Transfer" to Select "DC Supply".', 'po_id'=>'');
            }
            if($stock_transfer_dc == "" || $stock_transfer_dc == 0){
                return array('status'=>400, 'message'=>'Please select "Stock Transfer Location" to transfer stock', 'po_id'=>'');
            }
            $whDetail = $this->_LegalEntity->getWarehouseById($warehouse_id);
            $whDetailTypeId = isset($whDetail->legal_entity_type_id) ? $whDetail->legal_entity_type_id : 0;
            $stWhDetail = $this->_LegalEntity->getWarehouseById($stock_transfer_dc);
            $stWhDetailTypeId = isset($stWhDetail->legal_entity_type_id) ? $stWhDetail->legal_entity_type_id : 0;
            if($whDetailTypeId != 1001 || $stWhDetailTypeId != 1001){
                return array('status'=>400, 'message'=>'"Dispatch Location" and "Delivery Location" should be "APOB" to transfer stock.', 'po_id'=>'');
            }

            $whDetailStateId = isset($whDetail->state) ? $whDetail->state : 0;
            $stWhDetailStateId = isset($stWhDetail->state) ? $stWhDetail->state : 0;
            if($whDetailStateId != $stWhDetailStateId){
                return array('status'=>400, 'message'=>'"Dispatch Location" and "Delivery Location" should be in same state to transfer stock.', 'po_id'=>'');
            }

        }else if($stock_transfer_dc > 0){
            return array('status'=>400, 'message'=>'Please check "Stock Transfer" to transfer stock.', 'po_id'=>'');
        }
        $this->_poModel->updatePO($po_id, ['supply_le_wh_id'=>$le_wh_id]);
        $returnArray  = array("status"=>1,
            "message"=>"Supply DC updated successfully.",
            "data"=>[]);
        return $returnArray;
    }

    public function updateStDC(Request $request){
        $_POST = $request->input();
        $le_wh_id = $_POST['st_dc_name'];
        $po_id = $_POST['po_id'];
        $stock_transfer_dc = $_POST['st_dc_name'];
        $supplier_id = $_POST['supplier_id'];
        $supply_le_wh_id = $_POST['supply_le_wh_id'];
        $stock_transfer = $_POST['stock_transfer'];
        $warehouse_id = $_POST['warehouse_id'];
        if($stock_transfer_dc > 0){
            $stock_transfer = 1; // this stock transfer parameter is been over ridden because when stock transfer dc is seleted, is_stock_transfer should be 1 else 0;    
        }else{
            $stock_transfer = 0;
        }
        if($stock_transfer > 0){

            if($supplier_id != 24766){
                return array('status'=>400, 'message'=>'Please select "Ebutor Supplier" to transfer stock', 'po_id'=>'');
            }
            if($stock_transfer_dc == $warehouse_id){
                return array('status'=>400, 'message'=>'"Stock Transfer Location" and "Delivery Location" should not same to transfer stock', 'po_id'=>'');
            }
            if($supply_le_wh_id > 0){
                return array('status'=>400, 'message'=>'Please uncheck "Stock Transfer" to Select "DC Supply".', 'po_id'=>'');
            }
            if($stock_transfer_dc == "" || $stock_transfer_dc == 0){
                return array('status'=>400, 'message'=>'Please select "Stock Transfer Location" to transfer stock', 'po_id'=>'');
            }
            $whDetail = $this->_LegalEntity->getWarehouseById($warehouse_id);
            $whDetailTypeId = isset($whDetail->legal_entity_type_id) ? $whDetail->legal_entity_type_id : 0;
            $stWhDetail = $this->_LegalEntity->getWarehouseById($stock_transfer_dc);
            $stWhDetailTypeId = isset($stWhDetail->legal_entity_type_id) ? $stWhDetail->legal_entity_type_id : 0;
            if($whDetailTypeId != 1001 || $stWhDetailTypeId != 1001){
                return array('status'=>400, 'message'=>'"Dispatch Location" and "Delivery Location" should be "APOB" to transfer stock.', 'po_id'=>'');
            }

            $whDetailStateId = isset($whDetail->state) ? $whDetail->state : 0;
            $stWhDetailStateId = isset($stWhDetail->state) ? $stWhDetail->state : 0;
            if($whDetailStateId != $stWhDetailStateId){
                return array('status'=>400, 'message'=>'"Dispatch Location" and "Delivery Location" should be in same state to transfer stock.', 'po_id'=>'');
            }

        }else if($stock_transfer_dc > 0){
            return array('status'=>400, 'message'=>'Please check "Stock Transfer" to transfer stock.', 'po_id'=>'');
        }
        $this->_poModel->updatePO($po_id, ['stock_transfer_dc'=>$le_wh_id,'is_stock_transfer'=>$stock_transfer]);
        $returnArray  = array("status"=>1,
            "message"=>"Stock Transfer Location updated successfully.",
            "data"=>[]);
        return $returnArray;
    }

    public function updatePoSoCode(Request $request){
        try{
            $_POST = $request->input();
            $po_so_order_code = isset($_POST['po_so_order_code'])?$_POST['po_so_order_code']:0;
            $old_po_so_order_code = isset($_POST['old_po_so_order_code'])?$_POST['old_po_so_order_code']:0;
            $po_so_order_code = trim($po_so_order_code);
            $old_po_so_order_code = trim($old_po_so_order_code);
            if( ($old_po_so_order_code === "" || $old_po_so_order_code === 0 ) && ($po_so_order_code === 0 || $po_so_order_code === "" ) ){
                $returnArray  = array("status"=>2,
                            "message"=>"Order code cannot be empty!",
                            "data"=>[]);
                return $returnArray;
            }
            if (!preg_match('/^[A-Za-z]{2}[SO]{2}[0-9]{11}$/', $po_so_order_code) && $po_so_order_code !== "" && $po_so_order_code !== 0 ){
                $returnArray  = array("status"=>2,
                        "message"=>"Invalid Order Code!",
                        "data"=>[]);
                return $returnArray;
            }
            if($old_po_so_order_code !== "" && $old_po_so_order_code !== 0){
               
                $orderId = $this->_poModel->getOrderIdByCode($old_po_so_order_code);
                $order_data = $this->_orderModel->getOrderInfoById($orderId, "order_status_id");
                if(isset($order_data->order_status_id)){
                    if(!in_array($order_data->order_status_id, [17009,17015,17022])){
                        $returnArray  = array("status"=>2,
                            "message"=>"Order status should be 'Cancelled' or 'Returned' status!",
                            "data"=>[]);
                        return $returnArray;
                    }
                }
            }
            $po_id = $_POST['po_id'];
            $po_so_status = "1";
            if($po_so_order_code == "" || $po_so_order_code == "0"){
                $po_so_status = "0";
            }else{

                if($po_so_order_code != $old_po_so_order_code){
                    $checkData = PurchaseOrder::where("po_so_order_code",$po_so_order_code)->get();
                    if(count($checkData) > 0){
                        $returnArray  = array("status"=>2,
                                "message"=>"Order already assinged to another PO!",
                                "data"=>[]);
                        return $returnArray;
                    }
                }
                $orderId = $this->_poModel->getOrderIdByCode($po_so_order_code);
                if($orderId == 0 || $orderId == ""){
                    $returnArray  = array("status"=>2,
                            "message"=>"No Order exist with given order no!",
                            "data"=>[]);
                    return $returnArray;
                }
            }

            $this->_poModel->updatePO($po_id, ['po_so_order_code'=>$po_so_order_code,"po_so_status"=>$po_so_status]);
            $returnArray  = array("status"=>1,
                "message"=>"PO SO Code Updated Successfully.",
                "data"=>[]);
            return $returnArray;
        } catch (Exception $e) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
            $returnArray  = array("status"=>2,
                "message"=>"Technical Error!.",
                "data"=>[]);
            return $returnArray;
        }

        
    }

    public function downloadpoGSTReport(){
        $filterData = Input::get(); 
        $fdate = (isset($filterData['fdate']) && !empty($filterData['fdate'])) ? $filterData['fdate'] : date('Y-m').'-01';
        $fdate = date('Y-m-d', strtotime($fdate));
        $tdate = (isset($filterData['tdate']) && !empty($filterData['tdate'])) ? $filterData['tdate'] : date('Y-m-d');
        $tdate = date('Y-m-d', strtotime($tdate));
        $is_grndate = (isset($filterData['grn_date'])) ? 1 : 0;
        $dcID =$filterData['loc_dc_id'];
        $purchaseData = $this->_poModel->getPurchaseGSTdata($fdate,$tdate,$is_grndate,$dcID);
        $purchaseDataUnique = array();
        $i = 1;
        if(isset($purchaseData) && is_array($purchaseData)){
                foreach($purchaseData as $purchase){
                    $inwardId = $purchase->inward_id;
                    $grnValue = $purchase->grnValue;
                    $poValue = $purchase->poValue;
                    $wh_legal_id = $purchase->wh_legal_id;
                    $sup_legal_id = $purchase->sup_legal_id;

                    // checking supplier is global supplier le id = 24766
                    if($sup_legal_id == 24766){
                        $apob_data = $this->_poModel->getApobData($wh_legal_id);
                        if(count($apob_data)){
                            $purchase->State = $apob_data->state_name;
                        }
                    }

                    $taxArr = array();
                    $baseAmtArr = array();
                    $new = array();
                    //if($invoice_grid_id!='' && $invoice_grid_id>0){
                        //$grnProducts= $this->_poModel->getInvoiceDetailById($invoice_grid_id);
                        $grnProducts= $this->_poModel->getInwardDetailById($inwardId);
                        $inv_no = $purchase->SupplierInvoice;
                        $invoiceinfo = $this->_poModel->getInvoiceByCode($inv_no);
                        if(count($invoiceinfo)>0){
                            $invoice_date = date('Y-m-d',strtotime($invoiceinfo->created_at));
                        }else{
                            $invoice_date = $purchase->invoice_date;
                        }
                        if(is_array($grnProducts) && count($grnProducts)>0){
                            foreach ($grnProducts as $product) {
                                $taxper = $product->tax_per;
                                // $discountAmount = $discountAmount + $product->discount_total;
                                if ($product->tax_amount >= 0) {
                                    if (isset($taxArr[$taxper])) {
                                        $taxArr[$taxper] += $product->tax_amount;
                                    } else {
                                        $taxArr[$taxper] = $product->tax_amount;
                                    }
                                    if (isset($baseAmtArr[$taxper])) {
                                        $baseAmtArr[$taxper] += $product->sub_total;
                                    } else {
                                        $baseAmtArr[$taxper] = $product->sub_total;
                                    }
                                }
                            }
                            
                            foreach($baseAmtArr as $taxper=>$baseAmount){
                                $tax_data = isset($grnProducts[0]->tax_data)?$grnProducts[0]->tax_data:'{}';
                               // $tax_name = (isset($grnProducts[0]->tax_name) && $grnProducts[0]->tax_name=='IGST')?"IGST":"GST";
                                $tax_name = $grnProducts[0]->tax_name;
                                $gstPer = isset($tax_data['CGST'])?$tax_data['CGST']:(($tax_name=='GST')?50:0);
                                $iGstPer = isset($tax_data['IGST'])?$tax_data['IGST']:(($tax_name=='IGST')?100:0);
                                $utgstPer =isset($tax_data['UTGST'])?$tax_data['UTGST']:(($tax_name=='UTGST')?50 :0);
                                $gstTaxamt = $taxArr[$taxper];
                                $supplierInvoice = $purchase->SupplierInvoice;
                                $reference_arr = explode(',', $purchase->SupplierInvoice);

                                if(count($reference_arr)>0){
                                    $ref_uniq = array_unique($reference_arr);
                                    $supplierInvoice = trim(implode(',', $ref_uniq));
                                }

                                $purchaseDataArr['gstin'] = $purchase->gstin;
                                $purchaseDataArr['business_legal_name'] = $purchase->business_legal_name;
                                $purchaseDataArr['SupplierInvoice'] = $supplierInvoice;
                                $purchaseDataArr['IIAInvoiceNo'] = ($purchase->twoainvoice!="")?$purchase->twoainvoice:$supplierInvoice;
                                $purchaseDataArr['invoicetype'] = "R";
                                $purchaseDataArr['invoice_date'] = $invoice_date;
                                $purchaseDataArr['grnValue'] = $purchase->grnValue;
                                $purchaseDataArr['State'] = $purchase->State;
                                $purchaseDataArr['reverse_charge'] = "N";
                                $purchaseDataArr['rate'] = $taxper;
                                $purchaseDataArr['taxablevalue'] = round($baseAmount,2);
                                $purchaseDataArr['igst'] = round(($gstTaxamt*$iGstPer)/100,2);
                                $purchaseDataArr['cgst'] = round(($gstTaxamt*$gstPer)/100,2);
                                $purchaseDataArr['sgst'] = round(($gstTaxamt*$gstPer)/100,2);
                                $purchaseDataArr['utgst'] = round(($gstTaxamt*$utgstPer)/100,2);
                                $purchaseDataArr['cgst'] = round(($gstTaxamt*$utgstPer)/100,2);
                                $purchaseDataArr['cess'] = 0;
                                $purchaseDataArr['counterpartystatus'] = 'submitted';
                                $purchaseDataArr['povalue'] = $poValue;
                                array_push($purchaseDataUnique, (array)$purchaseDataArr);
                             }
                        }
                    //}
                }
            }
        $purchaseData = json_decode(json_encode($purchaseDataUnique),true);
        $csvHeaders = array(                          
                        'GSTIN of supplier',
                        'Trade/Legal name of the Supplier',
                        'Invoice number',
                        'II A Invoice No',
                        'Invoice Type',
                        'Invoice date',
                        'Invoice Value (Rs)',
                        'Place of supply',
                        'Supply Attract Reverse Change',
                        'Rate (%)',
                        'Taxable Value (Rs)', 
                        'Integrated Tax  (Rs)',
                        'Central Tax (Rs)',
                       'State/UT tax (Rs)',
                       'Cess  (Rs)',
                        'Counter Party Return Status',
                        'PO Value',);
            $file_name = 'Purchase_Report_' .date('Y-m-d-H-i-s').'.csv';
            $this->downloadCsv($csvHeaders, $purchaseData, $file_name);
           die;
    }

    public function createSoByPoData($poData=array()){
        $createSoByPO = $this->_roleRepo->checkPermissionByFeatureCode('ATPSO001');
        if($createSoByPO == false){
            return false;
        }
        // $soToAutoInvoice = $this->_roleRepo->checkPermissionByFeatureCode('ASOIN001');
        $poData['invoice_flag'] = 0;
        // if($soToAutoInvoice){
        //     $poData['invoice_flag'] = 1;
        // }
        $po_id = $poData['po_id'];
        $poSoData = $this->placeNewOrder($po_id,$poData);
        return $poSoData;
    }

    public function downloadPOImportExcel(){

        $mytime = Carbon::now();
        $headers = array('supplier_code','dc_code');
        $headers_second_page = array('Supplier Name','Supplier Code','DC Name','DC Code','Pack Data');

        $dcDet = json_decode($this->_poModel->getAllDCFCData(), true);
        $packDet = json_decode($this->_poModel->getUOMdata(), true);
        $supplierData = $this->_poModel->getSuppliersforIndents(array('indent_id'=>0));
        $exceldata = array('supplier_name','dc_name');

        $loopCounter = 0;
        $exceldata_second = array();

        foreach($supplierData as $val){
            $exceldata_second[$loopCounter]['sup'] = isset($supplierData[$loopCounter]) ? $supplierData[$loopCounter]['business_legal_name'] : '';
            $exceldata_second[$loopCounter]['sup_code'] = isset($supplierData[$loopCounter]) ? isset($supplierData[$loopCounter]['le_code'])?$supplierData[$loopCounter]['le_code']:"" : '';

            $exceldata_second[$loopCounter]['dc'] = isset($dcDet[$loopCounter]) ? $dcDet[$loopCounter]['lp_wh_name'] : '';
            $exceldata_second[$loopCounter]['dc_code'] = isset($dcDet[$loopCounter]) ? $dcDet[$loopCounter]['le_wh_code'] : '';
            $exceldata_second[$loopCounter]['pack_name'] = isset($packDet[$loopCounter]) ? $packDet[$loopCounter]['master_lookup_name'] : '';
            $loopCounter++;

        }
                
        $dummyData = array('poExcelName'=>'Po Template Sheet-'.$mytime->toDateTimeString());
        // UserActivity::userActivityLog('Po',$dummyData, 'Po Excel downloaded by user');

        Excel::create('Po Template Sheet-'.$mytime->toDateTimeString(), function($excel) use($headers, $exceldata, $headers_second_page, $exceldata_second) 
        {

            $excel->sheet("Po", function($sheet) use($headers, $exceldata)
            {
                $sheet->loadView('PurchaseOrder::poimportproductdata', array('headers' => $headers, 'data' => $exceldata)); 
            });

            $excel->sheet("Supplier_and_Warehouse_Data", function($sheet) use($headers_second_page, $exceldata_second)
            {
                $sheet->loadView('PurchaseOrder::poimportsupplierdata', array('headers' => $headers_second_page, 'data' => $exceldata_second)); 
            });
        })->export('xlsx');

    }

    /**
     * List of suppliers whose payment is due
     * @param  [type] $status [description]
     * @return [type]         [description]
     */
    public function supplierPaymentDueList($status='open') {

        try{

            $hasAccess = $this->_roleRepo->checkPermissionByFeatureCode('PO001');
            $poGSTReport = $this->_roleRepo->checkPermissionByFeatureCode('POGSTR');
            if($hasAccess == false) {
                return View::make('PurchaseOrder::error');
            }

            $legalEntityId = Session::get('legal_entity_id');
            $allStatusArr = $this->_masterLookup->getAllOrderStatus('PURCHASE_ORDER');

            $suppliers = $this->_poModel->getSupplierByLEId($legalEntityId);
            $Json = json_decode($this->_roleModel->getFilterData(6), 1);
            $filters = json_decode($Json['sbu'], 1);
            $dc_acess_list = isset($filters['118001']) ? $filters['118001'] : 'NULL';
            $allDc = $this->_orderModel->getDcHubDataByAcess($dc_acess_list);
            $filter_options['dc_data'] = $allDc;
            $allPOCountArr = $this->_poModel->getPoCountByStatus($legalEntityId);
            $allPOApprovalCountArr = $this->_poModel->getPoCountByStatus($legalEntityId,1);
                        $finalApprovalCountArr = $this->_poModel->getPoCountByStatus($legalEntityId,2);
            $partialCountArr = $this->_poModel->getPoCountByStatus($legalEntityId,3);
            $grnCountArr = $this->_poModel->getPoCountByStatus($legalEntityId,4);
                        $partial=isset($partialCountArr[87005]) ? (int)$partialCountArr[87005] : 0;
                        $closed=isset($grnCountArr[87002]) ? (int)$grnCountArr[87002] : 0;
                        $approval_cancel = (isset($allPOApprovalCountArr[57117]) ? (int)$allPOApprovalCountArr[57117] : 0);
                        $canceled = (isset($allPOCountArr[87004]) ? (int)$allPOCountArr[87004] : 0)+$approval_cancel;
                        $opened = (isset($allPOCountArr[87001]) ? (int)$allPOCountArr[87001] : 0)-$approval_cancel;
                        $shelved = isset($finalApprovalCountArr[1]) ? (int)$finalApprovalCountArr[1] : 0;
                        $immediatePay = $this->_poModel->getPoCountByStatus($legalEntityId,5);
                        $accept_full = isset($allPOApprovalCountArr[57107]) ? (int)$allPOApprovalCountArr[57107] : 0;
                        $accept_part = isset($allPOApprovalCountArr[57119]) ? (int)$allPOApprovalCountArr[57119] : 0;
                        $accept_part_closed = isset($allPOApprovalCountArr[57120]) ? (int)$allPOApprovalCountArr[57120] : 0;
                        $inspected_full = isset($allPOApprovalCountArr[57034]) ? (int)$allPOApprovalCountArr[57034] : 0;
                        $inspected_part = isset($allPOApprovalCountArr[57122]) ? (int)$allPOApprovalCountArr[57122] : 0;
                        $checked = $accept_full+$accept_part+$accept_part_closed;
                        $initiated=isset($allPOApprovalCountArr[57106]) ? (int)$allPOApprovalCountArr[57106] : 0;
                        $created=isset($allPOApprovalCountArr[57029]) ? (int)$allPOApprovalCountArr[57029] : 0;
                        $verified=isset($allPOApprovalCountArr[57030]) ? (int)$allPOApprovalCountArr[57030] : 0;
                        $poCounts = array(
                                        'all'=>array_sum($allPOCountArr),
                                        'opened'=>$opened,
                                        'partial'=>$partial,
                                        'closed'=>$closed,
                                        'canceled'=>$canceled,
                                        'expired'=>isset($allPOCountArr[87003]) ? (int)$allPOCountArr[87003] : 0,
                                        'initiated'=>$initiated,
                                        'created'=>$created,
                                        'verified'=>$verified,
                                        'approved'=>isset($allPOApprovalCountArr[57031]) ? (int)$allPOApprovalCountArr[57031] : 0,
                                        'paid'=> $opened+$partial+$shelved,
                                        'immediatepay'=>  array_sum($immediatePay),
                                        'posit'=>isset($allPOApprovalCountArr[57033]) ? (int)$allPOApprovalCountArr[57033] : 0,
                                        'receivedatdc'=>($inspected_part+$inspected_full),
                                        'checked'=>$checked,
                                        'grncreated'=>isset($allPOApprovalCountArr[57035]) ? (int)$allPOApprovalCountArr[57035] : 0,
                                        'shelved'=>$shelved,
                                        );
            $filter_status = empty($status) ? 'allpo' : $status;
            $createFeature = $this->_roleRepo->checkPermissionByFeatureCode('PO002');
            $exportFeature = $this->_roleRepo->checkPermissionByFeatureCode('PO008');
            $featureAccess = array('createFeature'=>$createFeature,'exportFeature'=>$exportFeature);
            return view('PurchaseOrder::supplierPaymentDueList')->with('poCounts', $poCounts)
                                                            ->with('filter_status', $filter_status)
                                                            ->with('suppliers', $suppliers)
                                                            ->with('featureAccess', $featureAccess)
                                                            ->with('allStatusArr', $allStatusArr)
                                                            ->with('poGSTReport',$poGSTReport)
                                                            ->with('filter_options',$filter_options);
        }
        catch(Exception $e) {

        }
    }

    /*
     * Get the list due of the payment supplier
     * @param $status String, by default null
     * @return JSON
     */
    public function getSupplierPaymentDueList($status = null, Request $request)
    {
        
        try {
            $status        = empty($status) ? 'allpo' : $status;
            $allStatusArr  = $this->_masterLookup->getAllOrderStatus('PURCHASE_ORDER');
            $filters       = array();           
            $orderby_array = "duedays asc";

            $filterData    = $request->all();
           
            if (isset($filterData['$filter'])) {
                $filters = $this->filterData($filterData['$filter']);
            }
          
            if (array_key_exists($status, $this->_filterStatus)) {
                $filters['po_status_id'] = $this->_filterStatus[$status];
            }
            if (array_key_exists($status, $this->_approvalStatuslist)) {
                $filters['approval_status_id'] = $this->_approvalStatuslist[$status];
            }
            
            if (isset($filterData['$orderby'])) { //checking for sorting
                $order             = explode(' ', $request->input('$orderby'));
                $order_query_field = $order[0]; //on which field sorting need to be done
                $order_query_type  = $order[1]; //sort type asc or desc
                $order_by_type     = 'desc';
                
                if ($order_query_type == 'asc') {
                    $order_by_type = 'asc';
                }
                $order_by = '';
                if (isset($this->grid_field_db_match[$order_query_field])) { //getting appropriate table field based on grid field
                    $order_by = $this->grid_field_db_match[$order_query_field];
                }
                // print_r($order_by);die;
                if (empty($order_by)) {
                    $order_by      = 'duedays';
                    $order_by_type = 'asc';
                }
                $orderby_array = $order_by . " " . $order_by_type;
            }
            
            $offset  = (int) $request->input('page');
            $perpage = $request->input('pageSize');
            // echo '<pre>';
            // print_r($filters);
            // exit;
            $poDataArr           = $this->_poModel->getUpcomingPayments($orderby_array, $filters, 0, $offset, $perpage);

            $totalPurchageOrders = $this->_poModel->getUpcomingPayments($orderby_array, $filters, 1);
            
            $dataArr = array();
            if (count($poDataArr)) {
                foreach ($poDataArr as $po) {
                    $shipTo         = $po->lp_wh_name;
                    $poValidity     = $po->po_validity . ' ' . (($po->po_validity > 1) ? 'Days' : 'Day');
                    $po_status      = isset($allStatusArr[$po->po_status]) ? $allStatusArr[$po->po_status] : '';
                    $approvalStatus = isset($po->approval_status) ? $po->approval_status : '';
                    $paymentStatus  = isset($po->payment_status) ? $po->payment_status : '';
                    $poValue        = ($po->poValue != '') ? $po->poValue : 0;
                    $actions        = '';
                    
                    $detailFeature   = $this->_roleRepo->checkPermissionByFeatureCode('PO003');
                    $printFeature    = $this->_roleRepo->checkPermissionByFeatureCode('PO004');
                    $downloadFeature = $this->_roleRepo->checkPermissionByFeatureCode('PO005');
                    $editFeature     = $this->_roleRepo->checkPermissionByFeatureCode('PO007');
                    
                    if ($po->po_status == '87001' && $po->approval_status_val != '57117' && $editFeature) {
                        $actions .= '<a href="#" onclick="approveStatus('. $po->po_id .')" > <i class="fa fa-thumbs-o-up"></i></a>&nbsp;';
                    }

                    if ($detailFeature && $po->request_id != "") {
                        $actions .= '<a href="#" onclick="viewhistory('. $po->request_id .')"> <i class="fa fa-eye"></i></a>&nbsp;';
                    }
                    // if ($printFeature) {
                    //     $actions .= '<a target="_blank" class="" href="/po/printpo/' . $po->po_id . '"> <i class="fa fa-print"></i></a>&nbsp;';
                    // }
                    // if ($downloadFeature) {
                    //     $actions .= '<a class="" href="/po/download/' . $po->po_id . '"> <i class="fa fa-download"></i></a>&nbsp;';
                    // }
                    
                    
                    if ($po->payment_mode == 2) {
                        $payment_mode = ($po->parent_id > 0) ? '<strong style="color:green;">Pre Paid</strong>' : '<strong style="color:blue;">Pre Paid</strong>';
                    } else {
                        $payment_mode = ($po->parent_id > 0) ? '<strong style="color:green;">Post Paid</strong>' : 'Post Paid';
                    }
                    $tlm_name         = $po->tlm_name;
                    $po_so_order_link = "";
                    if ($po->po_so_order_code != '') {
                        $po_so_order_id = $this->_poModel->getOrderIdByCode($po->po_so_order_code);
                        if ($po_so_order_id != 0 and $po_so_order_id != "")
                            $po_so_order_link = "<a class='' target='_blank' href='/salesorders/detail/" . $po_so_order_id . "'> " . $po->po_so_order_code . "</a>";
                    }

                    $chk = '<input class="check_box" type="checkbox" name="chk[]" value="'.$po->po_id.'">';

                    $dataArr[] = array(
                        'chk'=>$chk,
                        'poId' => $po->po_code,
                        'duedays' => $po->duedays,
                        'le_code' => $po->le_code,
                        'Supplier' => $po->business_legal_name,
                        'shipTo' => $shipTo,
                        'validity' => $poValidity,
                        'poValue' => $poValue,
                        'payment_mode' => $payment_mode,
                        'payment_due_date' => $po->payment_due_date,
                        'tlm_name' => $tlm_name,
                        'createdBy' => $po->user_name,
                        'createdOn' => ($po->po_date),
                        'Status' => $po_status,
                        'approval_status' => $approvalStatus,
                        'payment_status' => $paymentStatus,
                        'Actions' => $actions,
                        'grn_value' => ($po->grn_value),
                        'po_grn_diff' => $po->po_grn_diff,
                        'grn_created' => $po->grn_created,
                        'po_so_order_link' => $po_so_order_link
                    );
                }
                return Response::json(array(
                    'data' => $dataArr,
                    'totalPurchageOrders' => $totalPurchageOrders
                ));
            } else {
                return Response::json(array(
                    'data' => array(),
                    'totalPurchageOrders' => 0
                ));
            }
        }
        catch (Exception $e) {
            return Response::json(array(
                'data' => array(),
                'totalPurchageOrders' => 0
            ));
        }
    }


    /*
     * Raise the payment request for the purchase order
     * @param $poIds Array  Array of purchase order IDs
     * @return JSON Message after the payment request raised or fail
     */
 /*   public function raisePaymentRequest(Request $request)
    {
        try {
            $approval_flow         = new CommonApprovalFlowFunctionModel();
            $currentStatusId       = 57202;
            $approvalWorkflowID    = 565;
            $approvalStatusDetails = $approval_flow->getApprovalFlowDetails('Vendor Payment Request',$currentStatusId, Session::get('userId'));
            $NextStatusId          = $approvalStatusDetails['data'][0]['nextStatusId'];
            
            $data                  = [];
            $poIds                 = $request->input('poIds');
            $poData                = $this->_vendorPaymentRequestModel->getPoDetails($poIds);
            if($poData){
                foreach ($poData as $po) {
                    $data = array(
                        'po_id'            => $po->po_id,
                        'amount'           => $po->amount,
                        'requested_amount' => $po->amount
                        //'created_by'       => Auth::user()->id
                        // 'created_at'       => $created_at,
                        // 'updated_by'       => $updated_by,
                        // 'updated_at'       => $updated_at
                    );
                    VendorPaymentRequest::insert($data);
                    $paymentRequestId = DB::getPdo()->lastInsertId();
                    $approval_flow->storeWorkFlowHistory("Vendor Payment Request", $paymentRequestId, $currentStatusId, $NextStatusId, "New Payment Request raised !!", Session::get('userId'));
                }
                
            }
            
            

          //   echo '<pre>';
          //   print_r($poData);
          // //  getPoDetails
          //   // $vprm = new VendorPaymentRequestModel;
          //   $data[] = array(
          //       'po_id'            => $po_id,
          //       'amount'           => $amount,
          //       'requested_amount' => $amount,
          //       'created_by'       => $created_by,
          //       'created_at' 
          //             => $created_at,
          //       'updated_by'       => $updated_by,
          //       'updated_at'       => $updated_at
          //   );

            // $VendorPaymentRequestModel::insert($data);

           // return $this->_vendorPaymentRequestModel->createPaymentRequest($poIds);
        }
        catch (Exception $e) {
            // return Response::json(array(
            //     'data' => array(),
            //     'totalPurchageOrders' => 0
            // ));
        }
    }
    
    /**
     * Render the hostory for the payment request
     * @param  Request $id  Payment request ID
     * @return HTML          Render the view of payment request history
     */
 /*   public function raisedPaymentRequestHistory($id){
        $history = $this->_poModel->getApprovalHistory('Vendor Payment Request', 19);
        return view('PurchaseOrder::paymentRequestHistory')->with('history', $history);
    }

    /**
     * Update the status of the payment
     * @param  int $request_id Payment request ID
     * @return [type]             [description]
     */
    public function paymentRequestStatusUpdate($request_id){

        $res_approval_flow_func = $approval_flow_func->getApprovalFlowDetails('Purchase Order', $status, \Session::get('userId'));
        $current_status         = (isset($res_approval_flow_func["currentStatusId"])) ? $res_approval_flow_func["currentStatusId"] : '';
        $approvalOptions = array();
        $approvalVal = array();
        $isApprovalFinalStep = 0;
        $financeStatuses = [57118,57032];
        $acceptStatuses = [57107,57119,57120];
        if (isset($res_approval_flow_func["currentStatusId"]) && isset($res_approval_flow_func["data"])) {
            foreach ($res_approval_flow_func["data"] as $options) {
                if ($options['isFinalStep'] == 1) {
                    $isApprovalFinalStep = $options['isFinalStep'];
                }
                if(in_array($options['nextStatusId'], $financeStatuses)){
                    if($payment_status==57118 && $options['nextStatusId']!=57118){
                        $approvalOptions[$options['nextStatusId'] . ',' . $options['isFinalStep'] . ',' . $options['conditionId']] = $options['condition'];
                    }else if($payment_status==$options['nextStatusId'] || $payment_status==57032){
                    }else{
                        $approvalOptions[$options['nextStatusId'] . ',' . $options['isFinalStep'] . ',' . $options['conditionId']] = $options['condition'];
                    }
                }else{
                    if(in_array($current_status, $acceptStatuses) && $options['nextStatusId']==57035){
                    }else{
                        $approvalOptions[$options['nextStatusId'] . ',' . $options['isFinalStep'] . ',' . $options['conditionId']] = $options['condition'];
                    }
                }
            }
        }
        return view('PurchaseOrder::Form.paymentRequestApprovalForm')->with('approvedStatus', $approvedStatus);
    }
}
