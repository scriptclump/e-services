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
 * @author      Ebutor <info@ebutor.com>
 * @copyright   ebutor@2016
 * @package     Orders
 * @version:    v1.0
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
use App\Modules\Orders\Models\PaymentModel;
use App\Modules\Orders\Models\MasterLookup;
use App\Modules\Orders\Models\Invoice;
use App\Modules\Orders\Models\Shipment;
use App\Modules\Orders\Models\Refund;
use App\Modules\Orders\Models\ReturnModel;
use App\Modules\Orders\Models\OrderTrack;
use App\Modules\Orders\Models\GdsOrders;
use App\Modules\Roles\Models\Role;
use App\Modules\Grn\Models\Grn;

use Illuminate\Support\Facades\Route;
use App\Central\Repositories\RoleRepo;
use App\models\Dmapi\dmapiOrders;
use Notifications;
use Lang;
use App\Modules\Indent\Models\LegalEntity;
use Excel;
use PDF;
use Utility;

use App\Modules\Cpmanager\Controllers\pickController;
use App\Modules\Cpmanager\Models\AssignOrderModel;

class OrdersGridController extends BaseController {

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
    protected $_paymentModel;
    protected $_OrderTrack;
    protected $_gdsOrders;
    protected $_roleModel;
    protected $_AssignOrderModel;

    public function __construct() {

        $this->middleware(function ($request, $next) {
            if (!Session::has('userId')) {
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
        $this->_paymentModel = new PaymentModel();
        $this->_OrderTrack = new OrderTrack();
        $this->_gdsOrders = new GdsOrders();
        $this->_roleModel = new Role();
        $this->_grnModel = new Grn();
        $this->_commentTypeArr = array('17'=>'Order Status', 'SHIPMENT_STATUS', 'INVOICE_STATUS', 'Cancel Status', '66'=>'REFUNDS', '67'=>'RETURNS');
        $this->_filterStatus = array('open'=>'17001', 'confirmed'=>'17002', 'picked'=>'17003', 'packed'=>'17004', 'dispatch'=>'17005', 'shipped'=>'17006', 'processing'=>'17013', 'delivered'=>array('17007', '17023'), 'completed'=>'17008', 'cancelled'=>array('17009', '17015'), 'hold'=>'17014', 'picklist'=>'17020', 'invoiced'=>'17021', 'partial'=>'17013', 'return'=>array('17022'), 'stokctransit'=>'17024', 'stockhub'=>'17025', 'ofd'=>'17026');

        $this->grid_field_db_match = array(
                                            'ChannelName'   => 'orders.platform_id',
                                            'OrderID'        => 'orders.order_code',
                                            'OrderDate'      => 'orders.order_date',
                                            'Customer' => 'orders.shop_name',
                                            'User' => "created_by",
                                            'custRating' => 'le.legal_entity_id',
                                            'OrderValue' => 'order_value',
                                            'Hub' => 'lw.lp_wh_name',
                                            'spoke' => 'spokes.spoke_name',
                                            'skuCount' => 'totSku',
                                            'Status' => 'ordStatus.master_lookup_name',
                                            'remStat' =>'remappr.master_lookup_name', 
                                            'InvoiceValue' => 'inv.grand_total',
                                            'FillRate' => 'fillrate',
                                            'SDT' => 'orders.scheduled_delivery_date',
                                            'SDS1' => 'ordSlot1.master_lookup_name',
                                            'SDS2' => 'ordSlot2.master_lookup_name',
                                            'beat' => 'pjp.pjp_name',
                                            'Area' => 'city.officename',
                                            'pickedby' => "pickedby",
                                            'verifiedby' => "verifiedby",
                                            'ADT' => 'track.delivery_date',
                                            'del_name' => "deliveredby",
                                            'custcode' => 'le.le_code',
                                            'picker' => "picker",
                                            'pickno' => 'track.pick_code',
                                            'pickerdate'=>'track.scheduled_piceker_date',
                                            'canReason' => 'clookup.master_lookup_name',
                                            'CancelledValue' => 'cancelledValue',
                                            'cartons' => 'track.cfc_cnt',
                                            'crates' => 'track.crates_cnt',
                                            'DisFRate' => 'fillrate',
                                            'bags' => 'track.bags_cnt',
                                            'InvoiceDate' => 'inv.created_at',
                                            'invoice_code' => 'inv.invoice_code',
                                            'st_de_name' => "st_de_name",
                                            'st_del_date' => "track.st_del_date",
                                            'st_vehicle_no' => 'track.st_vehicle_no',
                                            'st_driver_name' => 'track.st_driver_name',
                                            'st_driver_mobile' => 'track.st_driver_mobile',
                                            'st_re_name'=>'st_re_name',
                                            'st_received_at'=>'track.st_received_at',
                                            'st_docket_no' => 'track.st_docket_no',
                                            'rt_de_name' => "rt_de_name",
                                            'rt_del_date' => "track.rt_del_date",
                                            'rt_vehicle_no' => 'track.rt_vehicle_no',
                                            'rt_driver_name' => 'track.rt_driver_name',
                                            'rt_driver_mobile' => 'track.rt_driver_mobile',
                                            'rt_re_name'=>'rt_re_name',
                                            'rt_received_at'=>'track.rt_received_at',
                                            'rt_docket_no' => 'track.rt_docket_no',
                                            'nctTracker' => 'nctmlookup.master_lookup_name',
                                            'hold_count' => 'track.hold_count',
                                            'hold_reason' => 'gdscomment.comment',
                                            'nextschdate' => 'track.delivery_date',
                                            'shipmentDate'=>'ship_date',
                                            'collection_code' => 'collections.collection_code',
                                            'pickedDate' => 'picked_date',
                                            'InvoiceQty' =>'totInvoiceQty',
                                            'InvoiceValue' =>'totInvoiceValue',
                                            'ReturnQty' =>'totReturnQty',
                                            'ReturnValue' =>'totReturnValue',
                                            'DamagedValue' =>'totDamagedValue',
                                            'MissingValue' =>'totMissingValue',
                                            'DamagedQty' =>'totDamagedQty',
                                            'MissingQty' =>'totMissingQty',
                                            'ExcessQty' =>'totExcessQty',
                                            'ExcessValue' =>'totExcessValue',
                                            'CancelledQty' =>'cancelledQty',
                                            'orderedQty'=>'orderedQty',
                                            'OrderValue'=>'order_value',
                                            'collected_amount' => 'collections.collected_amount',
                                            'collected_by' => 'collected_by',
                                            'collection_date' => 'collection_date',
                                            'remittance_code' => 'remittance.remittance_code',
                                            'remittance_date' => 'remittance_date',
                                            'hub_appr_date' => 'hub_appr_date',
                                            'hub_appr_by' => 'hub_appr_by',
                                            'fin_appr_date' => 'fin_appr_date',
                                            'fin_appr_by' => 'fin_appr_by',
                                            'ReturnDate' => 'rgrid.created_at',
                                            );

    }

    /*
     * indexAction() method is used to list of sales orders
     * @param Null
     * @return String
     *
     * Order Status of PRODUCTION
     *
     * New - 17001
     * Processing - 17002
     * Shipped - 17003
     * Canceled - 17004
     * Complete - 17005
     * Placed - 17006
     * Approve - 17007
     * Delivered - 17008
     * Confirmed - 17009
     * Packing - 17010
     * PACKED - 17013
     * READY_TO_DISPATCH - 17014
     *
     */
    
    public function indexAction($status=null,$sales_type=2) {

     try{
        $businessunit=Session::get('business_unitid');
        if(empty($businessunit)){
                $getaccessbuids=$this->getBuidsByUserId(Session::get('userId'));
                $getaccessbuids=explode(',', $getaccessbuids);
                $getaccessbuids=min($getaccessbuids);
                if($getaccessbuids==0 && is_numeric($getaccessbuids)){
                        $buid=DB::table('business_units')
                              ->select('bu_id')
                              ->where('parent_bu_id',$getaccessbuids)
                              ->first();
                        $bu_id=isset($buid->bu_id) ? $buid->bu_id: '';
                }else{
                    $bu_id=$getaccessbuids;
                }
                Session::set('business_unitid', $bu_id);           
         }
            $hasAccess = $this->_roleRepo->checkPermissionByFeatureCode('ORD001');
            $openOrders = $this->_roleRepo->checkPermissionByFeatureCode('OP001');
            $pickingOrders = $this->_roleRepo->checkPermissionByFeatureCode('PP002');
            $pickingCmptd = $this->_roleRepo->checkPermissionByFeatureCode('RTB003');
            $invoiceOders = $this->_roleRepo->checkPermissionByFeatureCode('Inv004');
            $dCToHub = $this->_roleRepo->checkPermissionByFeatureCode('SIT005');
            $ordersReceived = $this->_roleRepo->checkPermissionByFeatureCode('St0006');
            $ordersDelivery = $this->_roleRepo->checkPermissionByFeatureCode('OFD007');
            $ordersHold = $this->_roleRepo->checkPermissionByFeatureCode('HOLD08');
            $returnedOrders  = $this->_roleRepo->checkPermissionByFeatureCode('PRAH09');
            $approvedByHub = $this->_roleRepo->checkPermissionByFeatureCode('RAH010');
            $returnOrders = $this->_roleRepo->checkPermissionByFeatureCode('SIT0011');
            $returnedOders = $this->_roleRepo->checkPermissionByFeatureCode('PRAD012');
            $customerOders = $this->_roleRepo->checkPermissionByFeatureCode('CC0013');
            $cancelledOders = $this->_roleRepo->checkPermissionByFeatureCode('CE0014');
            $partiallyOders = $this->_roleRepo->checkPermissionByFeatureCode('PC0015');
            $fullReturns = $this->_roleRepo->checkPermissionByFeatureCode('FR0016');
            $partiallyDelivered  = $this->_roleRepo->checkPermissionByFeatureCode('PD0017');
            $deliveredOrders = $this->_roleRepo->checkPermissionByFeatureCode('DO0018');
            $missingQty = $this->_roleRepo->checkPermissionByFeatureCode('MQ0019');
            $damagedQty = $this->_roleRepo->checkPermissionByFeatureCode('DQ0020');
            $shortCollections = $this->_roleRepo->checkPermissionByFeatureCode('OWS021');
            $approvedMissing = $this->_roleRepo->checkPermissionByFeatureCode('AMQ022');
            $approvedDamaged = $this->_roleRepo->checkPermissionByFeatureCode('ADQ023');
            $transactionOrders  = $this->_roleRepo->checkPermissionByFeatureCode('NT0024');
            $paymentApproval = $this->_roleRepo->checkPermissionByFeatureCode('C00025');
            $unpaidorders = $this->_roleRepo->checkPermissionByFeatureCode('UNP0026');
            $skipSit = $this->_roleRepo->checkPermissionByFeatureCode('SIT003');  
            $hideRetSit = $this->_roleRepo->checkPermissionByFeatureCode('HIDRET001');
            $ofddelivery = $this->_roleRepo->checkPermissionByFeatureCode('OFDD001');
            $openToInv = $this->_roleRepo->checkPermissionByFeatureCode('OPNINV');              
            $editOrder = $this->_roleRepo->checkPermissionByFeatureCode('EDITORDER01');
            $Json = json_decode($this->_roleModel->getFilterData(6), 1);
            $filters = json_decode($Json['sbu'], 1);
            $dc_acess_list = isset($filters['118001']) ? $filters['118001'] : 'NULL';
            $allDc = $this->_orderModel->getDcHubDataByAcess($dc_acess_list);
            $filter_options['dc_data'] = $allDc;
            $pp_reports = $this->_roleRepo->checkPermissionByFeatureCode('PPR0001');
            //$returnDataArr = $this->_returnModel->getReturnCountWithStatus($filters);
            //$returnDataRWMQ = $this->_returnModel->getReturnCount('totRWMQ',$filters);
            //$returnDataRWDQ = $this->_returnModel->getReturnCount('totRWDQ',$filters);
            //$returnDataOWSM = $this->_returnModel->getShortCollections($filters); 
           //$returnDataRAWMQ = $this->_returnModel->getReturnApproved('totRAWMQ',$filters);
            //$returnDataRAWDQ = $this->_returnModel->getReturnApproved('totRAWDQ',$filters);
            $checkersListFeature = $this->_roleRepo->checkPermissionByFeatureCode('REORDS01');

            if($hasAccess == false) {
            return View::make('Indent::error');
            }
			//$statusFilters = array(17001,17005,17007,17008,17009,17014,17015,17020,17021,17024,17025,17026);
            //$orderCountStatusWiseArr = $this->_orderModel->getOrderCountGroupByStatus($statusFilters);
            $orderCountStatusWiseArr=$this->_orderModel->getStatusCounts($sales_type);           
            $statusWiseOrderCount = array();
            if(is_array($orderCountStatusWiseArr)) {
                foreach($orderCountStatusWiseArr as $data) {
                    $statusWiseOrderCount[$data->order_status_id] = $data->total;
                }
            }


            /*$totUnpaidCnt = 0;
            $totUnpaid = $this->_paymentModel->getUnpaidOrderCount($filters);

            if(is_array($totUnpaid) && isset($totUnpaid[0]->totUnpaidOrders)) {
                $totUnpaidCnt = $totUnpaid[0]->totUnpaidOrders; 
            }*/

            #echo '<pre>';print_r($returnDataArr);die;
            #
            /**
             * Define all variables
             */
            $totUnpaidCnt=isset($statusWiseOrderCount['unpaid']) ? $statusWiseOrderCount['unpaid'] : 0;
            $returnDataOWSM=isset($statusWiseOrderCount['collections']) ? $statusWiseOrderCount['collections'] : 0;
            $totOpened = isset($statusWiseOrderCount[17001]) ? $statusWiseOrderCount[17001] : 0;
            $totPicklist = isset($statusWiseOrderCount[17020]) ? $statusWiseOrderCount[17020] : 0;
            $totRtoD = isset($statusWiseOrderCount[17005]) ? $statusWiseOrderCount[17005] : 0;
            $totInvoiced = isset($statusWiseOrderCount[17021]) ? $statusWiseOrderCount[17021] : 0;
            $totSitDCtoHub = isset($statusWiseOrderCount[17024]) ? $statusWiseOrderCount[17024] : 0;
            $totStockInHub = isset($statusWiseOrderCount[17025]) ? $statusWiseOrderCount[17025] : 0;
            $totOutForDelivery = isset($statusWiseOrderCount[17026]) ? $statusWiseOrderCount[17026] : 0;
            $totHold = isset($statusWiseOrderCount[17014]) ? $statusWiseOrderCount[17014] : 0;
            /*$totFullPRAH = isset($returnDataArr['17022_67002']) ? $returnDataArr['17022_67002'] : 0;
            $totPartialPRAH = isset($returnDataArr['17023_67002']) ? $returnDataArr['17023_67002'] : 0;
            $totRWMQ = isset($returnDataRWMQ[0]->total)? $returnDataRWMQ[0]->total:0;
            $totRWDQ = isset($returnDataRWDQ[0]->total)? $returnDataRWDQ[0]->total:0; 
            //$totRAWMQ = isset($returnDataRAWMQ[0]->total)? $returnDataRAWMQ[0]->total:0;
            //$totRAWDQ = isset($returnDataRAWDQ[0]->total)? $returnDataRAWDQ[0]->total:0;
            $totFullRAH = isset($returnDataArr['17022_57067']) ? $returnDataArr['17022_57067'] : 0;
            $totPartialRAH = isset($returnDataArr['17023_57067']) ? $returnDataArr['17023_57067'] : 0;

            $totFullSitHubToDc = isset($returnDataArr['17022_57067_17027']) ? $returnDataArr['17022_57067_17027'] : 0;
            $totPartialSitHubToDc = isset($returnDataArr['17023_57067_17027']) ? $returnDataArr['17023_57067_17027'] : 0;
            
            $totFullPRAD = isset($returnDataArr['17022_57067_17028']) ? $returnDataArr['17022_57067_17028'] : 0;
            $totPartialPRAD = isset($returnDataArr['17023_57067_17028']) ? $returnDataArr['17023_57067_17028'] : 0;*/
            $totFullPRAH = isset($statusWiseOrderCount['17022_67002']) ? $statusWiseOrderCount['17022_67002'] : 0;
            $totPartialPRAH = isset($statusWiseOrderCount['17023_67002']) ? $statusWiseOrderCount['17023_67002'] : 0;
            $totRWMQ = isset($statusWiseOrderCount['Missing'])? $statusWiseOrderCount['Missing']:0;
            $totRWDQ = isset($statusWiseOrderCount['Damaged'])? $statusWiseOrderCount['Damaged']:0; 
            $totFullRAH = isset($statusWiseOrderCount['17022_57067']) ? $statusWiseOrderCount['17022_57067'] : 0;
            $totPartialRAH = isset($statusWiseOrderCount['17023_57067']) ? $statusWiseOrderCount['17023_57067'] : 0;

            $totFullSitHubToDc = isset($statusWiseOrderCount['17022_57067_17027']) ? $statusWiseOrderCount['17022_57067_17027'] : 0;
            $totPartialSitHubToDc = isset($statusWiseOrderCount['17023_57067_17027']) ? $statusWiseOrderCount['17023_57067_17027'] : 0;
            
            $totFullPRAD = isset($statusWiseOrderCount['17022_57067_17028']) ? $statusWiseOrderCount['17022_57067_17028'] : 0;
            $totPartialPRAD = isset($statusWiseOrderCount['17023_57067_17028']) ? $statusWiseOrderCount['17023_57067_17028'] : 0;

            $totPRAH = ($totFullPRAH + $totPartialPRAH);
            $totRAH = ($totFullRAH + $totPartialRAH);
            $totSitHubToDc = ($totFullSitHubToDc + $totPartialSitHubToDc);
            $totPRAD = ($totFullPRAD + $totPartialPRAD);

            $cancelledByCust = isset($statusWiseOrderCount[17009]) ? $statusWiseOrderCount[17009] : 0;
            $cancelledByEbutor = isset($statusWiseOrderCount[17015]) ? $statusWiseOrderCount[17015] : 0;

            $totFullReturns = isset($statusWiseOrderCount['17022_57066']) ? $statusWiseOrderCount['17022_57066'] : 0;
            
            $totPartialDelivered = isset($statusWiseOrderCount['17023_57066']) ? $statusWiseOrderCount['17023_57066'] : 0;

            $totDelivered = isset($statusWiseOrderCount[17007]) ? $statusWiseOrderCount[17007] : 0;
            
            $totalCompletedOrders = isset($statusWiseOrderCount[17008]) ? $statusWiseOrderCount[17008] : 0;

            //$totalOrders = ($totOpened + $totPicklist + $totRtoD + $totInvoiced + $totSitDCtoHub + $totStockInHub + $totOutForDelivery + $totHold + $totPRAH + $totRAH + $totSitHubToDc + $totPRAD + $cancelledByCust + $cancelledByEbutor + $totFullReturns + $totPartialDelivered + $totDelivered + $totalCompletedOrders);
                        

            $status = empty($status) ? 'allorders' : $status;

            $filter = array();
            if(array_key_exists($status, $this->_filterStatus)) {
                $filter['order_status_id'] = $this->_filterStatus[$status];
            }

            //var_dump($filter);die;
            //$totOrderValue = $this->_orderModel->getStats($filter, 1);
            $totpartialcnt= isset($statusWiseOrderCount['total_partial_count']) ? $statusWiseOrderCount['total_partial_count'] : 0;//(int)$this->_orderModel->getPartialCancelCount($filters);
            $totalPendingPayments = isset($statusWiseOrderCount['total_pending_payments']) ? $statusWiseOrderCount['total_pending_payments'] : 0;//$this->_paymentModel->getNctOrder($filters);

            //$pickerUsers        =   $this->_orderModel->getUsersByRoleName(array('Picker'));
            $user=Session::get('userId');
            $dcList = $this->_roleModel->getWarehouseData($user, 6);
            $dcList = json_decode($dcList,true);
             if(isset($dcList['118002'])){
                $parentHubdata=explode(",",$dcList['118002']);
            }else{
                $parentHubdata=[];
            }

            $pickersListWhSpecific		=	$this->_roleRepo->getUsersByFeatureCodeWithoutLegalentity($parentHubdata,1);
           /* $pickersListWhSpecific=[];
            $pickerno=0;
            foreach($pickerUsers as $pickingUser){
                $hubList = $this->_roleModel->getWarehouseData($pickingUser->user_id, 6);

                $hubList = json_decode($hubList,true);
                if(isset($hubList['118002'])){
                    $childHubdata=explode(",",$hubList['118002']);
                }else{
                    $childHubdata=[];
                }
                $finalresult = array_intersect($parentHubdata, $childHubdata);
                if(count($finalresult)>0){
                    $pickersListWhSpecific[$pickerno++]=$pickingUser;
                }
            }*/
            //$allDocAreas        =   $this->_orderModel->getAllDocAreas();
            $paymentModesArr    =   $this->_masterLookup->getMasterLookupNamesByCategoryId(22);
            //$deliveryUsers      =   $this->_orderModel->getUsersByRoleName(array('Delivery Executive'));
            $deliveryUsers 		= 	$this->_roleRepo->getUsersByFeatureCodeWithoutLegalentity($parentHubdata,2);
            //$allUsers       =   $this->_orderModel->getUsersByRoleName(array('Field Force Associate','Field Force Manager'));

            $StDC_Hub = $this->_roleRepo->checkPermissionByFeatureCode('ORD013');
            $StHub_DC = $this->_roleRepo->checkPermissionByFeatureCode('ORD014');

            $confSTDC = $this->_roleRepo->checkPermissionByFeatureCode('ORD016');
            $confSTHub = $this->_roleRepo->checkPermissionByFeatureCode('ORD015');
            
            // Access to Check Generate Trip Sheet Feature
            $generateTripSheetFeature = $this->_roleRepo->checkPermissionByFeatureCode('ORDTS1');

            $Hubs_Assigned = array();
            $Dcs_Assigned = array();

            if(isset($filters['118001'])) {
                $Dcs_Assigned = explode(',',$filters['118001']);
            }

            if(isset($filters['118002'])) {
                $Hubs_Assigned = explode(',',$filters['118002']);
            }
            $hub_vehicles = $this->_orderModel->getUserVehicles($Hubs_Assigned,'Hub');

            $dc_vehicles = $this->_orderModel->getUserVehicles($Dcs_Assigned,'DC',$Hubs_Assigned);
            /*if(isset($filter)){
                $totalOrders = (int)$this->_gdsOrders->getOrderData('allorders', 1, 0, 0, $filter);
                $totRAWDQ = (int)$this->_gdsOrders->getOrderData('approvedDamagedquantities', 1, 0, 0, $filter);
                $totRAWMQ = (int)$this->_gdsOrders->getOrderData('approvedMissingquantities', 1, 0, 0, $filter);
            }else{
                $totalOrders =0;
                $totRAWDQ =0;
                $totRAWMQ =0;
            }*/

            //Check access to reasign orders
            $this->_AssignOrderModel = new AssignOrderModel();
            $userdata['user_id'] = Session::get('userId');
            $checkersList = $this->_AssignOrderModel->getCheckersListModal($userdata);
            $checkersList = isset($checkersList['data'])? $checkersList['data'] : [];
           
            $totalOrders =isset($statusWiseOrderCount['all']) ? $statusWiseOrderCount['all'] : 0;
            $totRAWDQ =isset($statusWiseOrderCount['rawd']) ? $statusWiseOrderCount['rawd'] : 0;
            $totRAWMQ =isset($statusWiseOrderCount['rawm']) ? $statusWiseOrderCount['rawm'] : 0;
            $primaryAccess = $this->_roleRepo->checkPermissionByFeatureCode('PRIMARY001');
            
            //Alert message for Dc about his pending orders
            $parent_access=$this->_orderModel->getParentUserAccess($userdata['user_id']);
            $order_count=0;
            if(isset($parent_access) && ($parent_access!=0)){
                $roleObj = new Role();
                $dc_acess_list = $roleObj->GetWareHouseByLeId(Session::get('legal_entity_id'));
                $dc_acess_list = json_decode(json_encode($dc_acess_list),true);
                $dc_acess_list_column = array_column($dc_acess_list, "le_wh_id");
                $dc_acess_list = implode(",", $dc_acess_list_column);
                $dc_acess_list = "'".$dc_acess_list."'";
                $no_of_days_pending=$this->_orderModel->getPendingOrderDays();
                if(isset($no_of_days_pending[0]['description']) && $no_of_days_pending[0]['description']!='' ){
                    $days=$no_of_days_pending[0]['description'];
                    $pending_orders_count=DB::select("CALL pending_orders_count_data($dc_acess_list,$days)");
                    $pending_orders=json_decode(json_encode($pending_orders_count),1);
                    $order_count=isset($pending_orders[0]['Order Count']) ? $pending_orders[0]['Order Count'] : 0;
                }
            }
            $global_access = $this->_roleRepo->checkPermissionByFeatureCode('GLB0001');
            if(empty($global_access))
                $global_access = 0;            
            $all_access = DB::table('user_permssion')->where('user_id',$user)->where('permission_level_id',6)->where('object_id',0)->get();
            $all_access = (count($all_access) > 0) ? 1 : 0;
            $dcFCSales = $this->_roleRepo->checkPermissionByFeatureCode('DCTOFCSAL');
            $apobSales = $this->_roleRepo->checkPermissionByFeatureCode('APOBTODCSAL');
            $retailerSales =$this->_roleRepo->checkPermissionByFeatureCode('RETAISAL');   
            return View::make('Orders::index')
                                        ->with('allaccess',$all_access)
                                        ->with('globalaccess', $global_access)
                                        ->with('totalOrders', $totalOrders)
                                        ->with('totOpened', $totOpened)
                                        ->with('totPicklist', $totPicklist)
                                        ->with('totRtoD', $totRtoD)
                                        ->with('totInvoiced', $totInvoiced)
                                        ->with('totSitDCtoHub', $totSitDCtoHub)
                                        ->with('totStockInHub', $totStockInHub)
                                        ->with('totOutForDelivery', $totOutForDelivery)
                                        ->with('totHold', $totHold)
                                        ->with('totPRAH', $totPRAH)
                                        ->with('totRAH', $totRAH)
                                        ->with('totSitHubToDc', $totSitHubToDc)
                                        ->with('totPRAD', $totPRAD)
                                        ->with('cancelledByCust', $cancelledByCust)
                                        ->with('cancelledByEbutor', $cancelledByEbutor)
                                        ->with('totFullReturns', $totFullReturns)
                                        ->with('totRWMQ',$totRWMQ)
                                        ->with('totRWDQ',$totRWDQ)
										->with('returnDataOWSM',$returnDataOWSM)
                                        ->with('totRAWMQ',$totRAWMQ)
                                        ->with('totRAWDQ',$totRAWDQ)
                                        ->with('totPartialDelivered', $totPartialDelivered)     
                                        ->with('totDelivered', $totDelivered)
                                        ->with('totalCompletedOrders', $totalCompletedOrders)
                                        ->with('pickerUsers', $pickersListWhSpecific)
                                        //->with('allDocAreas', $allDocAreas)
                                        ->with('paymentModesArr', $paymentModesArr)
                                        //->with('allUsers', $allUsers)
                                        ->with('deliveryUsers', $deliveryUsers)
                                        ->with('totalPendingPayments', $totalPendingPayments)
                                        ->with('generateTripSheet', $generateTripSheetFeature)
                                        ->with('confSTDC',$confSTDC)
                                        ->with('confSTHub',$confSTHub)
                                        ->with('stDCHub',$StDC_Hub)
                                        ->with('stHubDC',$StHub_DC)
                                        ->with('totpartialcnt',$totpartialcnt)
                                        ->with('totUnpaidCnt',$totUnpaidCnt)
                                        ->with('hub_vehicles', $hub_vehicles)
                                        ->with('dc_vehicles', $dc_vehicles)
                                        ->with('status', $status)
                                        ->with('openToInv', $openToInv)
                                        ->with('editOrder',$editOrder)
                                        ->with('skipSit', $skipSit)
                                        ->with('hideRetSit', $hideRetSit)
                                        ->with('order_count',$order_count)
                                        ->with('pp_reports',$pp_reports)
                                        ->with(['openOrders'=> $openOrders,'pickingOrders'=>$pickingOrders,'pickingCmptd'=>$pickingCmptd,'checkersList'=>$checkersList,'invoiceOders'=>$invoiceOders,'dCToHub'=>$dCToHub,'ordersReceived'=>$ordersReceived,'ordersDelivery'=>$ordersDelivery,'ordersHold'=>$ordersHold,'returnedOrders'=>$returnedOrders,'approvedByHub'=>$approvedByHub,'returnOrders'=>$returnOrders,'returnedOders'=>$returnedOders,'customerOders'=>$customerOders,'cancelledOders'=>$cancelledOders,'partiallyOders'=>$partiallyOders,'fullReturns'=>$fullReturns,'partiallyDelivered'=>$partiallyDelivered,'deliveredOrders'=>$deliveredOrders,'missingQty'=>$missingQty,'damagedQty'=>$damagedQty,'shortCollections'=>$shortCollections,'approvedMissing'=>$approvedMissing,'approvedDamaged'=>$approvedDamaged,'transactionOrders'=>$transactionOrders,'paymentApproval'=>$paymentApproval,'unpaidorders'=>$unpaidorders,'ofddelivery'=>$ofddelivery,'filter_options'=>$filter_options,'checkersListFeature'=>$checkersListFeature,'sales_type'=>$sales_type,'primaryAccess'=>$primaryAccess,'dcFCSales'=>$dcFCSales,'apobSales'=>$apobSales,'retailerSales'=>$retailerSales]);
        }
        catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    private function getPlateformIcon($platformId) {
        switch ($platformId) {
            case '5001':
                return '<i class="fa fa-desktop"></i>';
            break;

            case '5002':
                return '<i class="fa fa-windows"></i>';
            break;

            case '5004':
                return '<i class="fa fa-android"></i>';
            break;

            case '5005':
                return '<i class="fa fa-apple"></i>';
            break;

            case '5006':
                return '<i class="fa fa-windows"></i>';
            break;
            
            default:
                return '';
            break;
        }
    }
    
    public function filterOrdersAction(Request $request, $status = null,$sales_type=2,$filter_array=array()) {
        try{

            $allowInvoiceLinks = array('17021', '17008', '17022', '17023', '17007', '17014', '17024', '17025', '17026','17027', '17028');

            $Json = json_decode($this->_roleModel->getFilterData(6),1);
            $Json = json_decode($Json['sbu'],1);

            $filter = array();
            $getData = $request->all();
            if(count($filter_array) > 0){
                $getData = $filter_array;
                $filter = $filter_array;
            }
            #echo "<pre>";print_r($getData);die;
            if(isset($getData['$filter'])){
                $filter = $this->filterData($getData['$filter']);
            }

            if(isset($Json['118001'])) {
                $filter['Dcs_Assigned'] = $Json['118001'];
            }

            if(isset($Json['118002'])) {
                $filter['Hubs_Assigned'] = $Json['118002'];
            }

            //echo "<pre>";print_r($filter);die;
            
            /**
             * remote sorting
             */
            $sort = array();
            if (isset($getData['$orderby']) && !empty($getData['$orderby'])) {
                $sortArr  = explode(' ', $request->input('$orderby'));
                $sortBy   = isset($sortArr[1]) ? $sortArr[1] : 'desc';

                if (isset($this->grid_field_db_match[$sortArr[0]])) {
                    $orderBy = $this->grid_field_db_match[$sortArr[0]];
                }
                $sort['orderBy'] = $orderBy;
                $sort['sortBy'] = $sortBy;
            } else {
                if($status=='open' || $status=='hold' || $status=='ofd' || $status=='stocktransit' || $status=='stocktransitdc' || $status=='stockhub') {
                    $sort['orderBy'] = 'orders.order_date';
                    $sort['sortBy'] = 'asc';
                }
            }
            #echo '<pre>';print_r($sort);die;
            
            /*
             * for paging
             */
            $offset = (int)$request->input('page');
            $perpage = $request->input('pageSize');
            $ordersArr = $this->_gdsOrders->getOrderData($status, 0, $offset, $perpage, $filter, $sort,$sales_type);
            $totalOrders = (int)$this->_gdsOrders->getOrderData($status, 1, 0, 0, $filter,'',$sales_type);                        
            $dataArr = array();
            $ofddelivery = $this->_roleRepo->checkPermissionByFeatureCode('OFDD001');
            $businessunit=Session::get('business_unitid');
            if(count($ordersArr) && !empty($businessunit)) { 
                foreach($ordersArr as $order) {

                    $fillRate = isset($order->fillrate) ? round((float)$order->fillrate) : null;
                    
                    $chkDisabled = '<input type="checkbox" name="chk[]" value="'.$order->gds_order_id.'"><input type="hidden" id="'.$order->gds_order_id.'" name="orderStatus[]" value="'.$order->order_status_id.'">';
                    
                    if($order->order_status_id == '17001') {
                        $chkDisabled = '<input type="checkbox" name="chk[]" value="'.$order->gds_order_id.'"><input type="hidden" id="'.$order->gds_order_id.'" name="orderStatus[]" value="'.$order->order_status_id.'">';
                    }

                    $chkDisabled.= '<input type="hidden" name="hubIds[]" value="'.$order->hub_id.'">';
                                        
                    $plateformIcon = $this->getPlateformIcon($order->platform_id);
                    $sdt = '';
                    if(isset($order->scheduled_delivery_date)) {
                        $sdt = $order->scheduled_delivery_date;
                    }                   

                    $invoiceLink = '<a target="_blank" href="/salesorders/print/'.((int)$order->gds_order_id).'" title="Print Challan"><i class="fa fa-print"></i></a>&nbsp;';

                    $downloadLink = '<a href="/salesorders/pdf/'.((int)$order->gds_order_id).'" title="Download Challan"><i class="fa fa-download"></i></a>';
                    $paymentLink = '';
                    // download & print invoice
                    if(in_array($order->order_status_id, $allowInvoiceLinks)) {
                        
                        $invoiceGridId = (isset($order->gds_invoice_grid_id) ? $order->gds_invoice_grid_id : 0);

                        $invoiceLink = '<a target="_blank" title="Print Invoice" href="/salesorders/printinvoice/'.$invoiceGridId.'/'.((int)$order->gds_order_id).'/1"><i class="fa fa-print"></i></a>&nbsp;';

                        $downloadLink = '<a title="Download Invoice" href="/salesorders/invoicepdf/'.$invoiceGridId.'/'.((int)$order->gds_order_id).'/1"><i class="fa fa-download"></i></a>&nbsp;';                     
                    }

                    if($order->order_status_id == '17007' || $order->order_status_id == '17023') {
                        $paymentLink = '<a title="Collection" data-toggle="modal" href="#collection" class="collectionPopup" collection-order-attr="'.$order->gds_order_id.'"><i class="fa fa-rupee"></i></a>&nbsp;';
                    }

                    // disabled download & print link
                    if($order->order_status_id == '17009' || $order->order_status_id == '17015') {
                        $invoiceLink = '';
                        $downloadLink = '';
                    }

                    $returnLink = '';
                    $returnId = isset($order->return_grid_id) ? (int)$order->return_grid_id : 0;
                    if($returnId) {
                        $returnLink = '<a title="'.$order->orderStatus.'" href="/salesorders/returndetail/'.$returnId.'"><i class="fa fa-reply" aria-hidden="true"></i>';
                    }

                    $dataArr[] = array(
                            'chk'=>$chkDisabled,
                            'Hub'=>$order->lp_wh_name,
                            'spoke'=>(isset($order->spokeName) ? $order->spokeName : ''),
                            'ChannelName'=>$plateformIcon,
                            'OrderID'=>$order->order_code,
                            'OrderDate'=>$order->order_date,
                            'CancelledValue'=>(isset($order->cancelledValue) ? $order->cancelledValue : ''),
                            'CancelledQty'=>(isset($order->cancelledQty) ? $order->cancelledQty : ''),
                            'SDT'=>$sdt,
                            'SDS1'=>(isset($order->slot1) ? $order->slot1 : ''),
							'DueAmount'=>(isset($order->due) ? $order->due: ''),
                            'SDS2'=>(isset($order->slot2) ? $order->slot2 : ''),
                            'ADT'=>(isset($order->delivery_date) ? $order->delivery_date : ''),
                            'del_name'=>(isset($order->deliveredby) ? ucwords(strtolower($order->deliveredby)) : ''),
                            'InvoiceDate'=>(isset($order->invoice_date) ? $order->invoice_date : ''),
                            'beat'=>(ucwords(strtolower($order->beat))),
                            'pickno'=>(isset($order->pick_code) ? $order->pick_code : ''),
                            'picker'=>(isset($order->picker) ? $order->picker : ''),
                            'pickedDate'=>(isset($order->picked_date) ? $order->picked_date : ''),
                            'pickerdate'=>(isset($order->scheduled_piceker_date) ? $order->scheduled_piceker_date : ''),
                            'OrderExpDate'=>(isset($order->order_expiry_date) ? $order->order_expiry_date : ''),
                            'Customer'=>(ucwords(strtolower($order->shop_name))),
                            'custcode'=>(isset($order->le_code) ? $order->le_code : ''),
                            'shipmentDate'=>(isset($order->ship_date) ? $order->ship_date : ''),
                            'cartons' =>(isset($order->cfc_cnt) ? $order->cfc_cnt : ''),
                            'bags' =>(isset($order->bags_cnt) ? $order->bags_cnt : ''),
                            'crates' =>(isset($order->crates_cnt) ? $order->crates_cnt : ''),
                            'pickedby' =>(isset($order->pickedby) ? ucwords(strtolower($order->pickedby)) : ''),
                            'verifiedby' =>(isset($order->verifiedby) ? ucwords(strtolower($order->verifiedby)) : ''),
                            'custRating' =>(isset($order->custRating) ? ucwords(strtolower($order->custRating)) : ''),
                            'invoice_code' =>(isset($order->invoice_code) ? $order->invoice_code : ''),

                            'st_de_name' => (isset($order->st_de_name) ? $order->st_de_name : ''),  
                            'st_del_date' => (isset($order->st_del_date) ? $order->st_del_date : ''),   
                            'st_vehicle_no' => (isset($order->st_vehicle_no) ? $order->st_vehicle_no : ''), 
                            'st_driver_name' => (isset($order->st_driver_name) ? $order->st_driver_name : ''),  
                            'st_driver_mobile' => (isset($order->st_driver_mobile) ? $order->st_driver_mobile : ''),    
                            'st_docket_no' => (isset($order->st_docket_no) ? $order->st_docket_no : ''),    
                            'st_re_name' => (isset($order->st_re_name) ? $order->st_re_name : ''),  
                            'st_received_at' => (isset($order->st_received_at) ? $order->st_received_at : ''),  
                                            
                            'rt_de_name' => (isset($order->rt_de_name) ? $order->rt_de_name : ''),  
                            'rt_del_date' => (isset($order->rt_del_date) ? $order->rt_del_date : ''),   
                            'rt_vehicle_no' => (isset($order->rt_vehicle_no) ? $order->rt_vehicle_no : ''), 
                            'rt_driver_name' => (isset($order->rt_driver_name) ? $order->rt_driver_name : ''),  
                            'rt_driver_mobile' => (isset($order->rt_driver_mobile) ? $order->rt_driver_mobile : ''),    
                            'rt_docket_no' => (isset($order->rt_docket_no) ? $order->rt_docket_no : ''),    
                            'rt_re_name' => (isset($order->rt_re_name) ? $order->rt_re_name : ''),  
                            'rt_received_at' => (isset($order->rt_received_at) ? $order->rt_received_at : ''),

                            'User'=>(isset($order->created_by) ? ucwords(strtolower($order->created_by)) : ''),
                            'Area'=>(ucwords(strtolower($order->areaname))),
                            'OrderValue'=>$order->order_value,
                            'InvoiceValue'=>(isset($order->totInvoiceValue) ? $order->totInvoiceValue : ''),
                            'InvoiceQty'=>(isset($order->totInvoiceQty) ? $order->totInvoiceQty : ''),
                            'ReturnQty'=>(isset($order->totReturnQty) ? $order->totReturnQty : ''),
							'ReturnDate'=>(isset($order->returnDate) ? $order->returnDate : ''),
                                            
                            'CancelValue'=>(isset($order->totCancelValue) ? $order->totCancelValue : ''),
                            'ReturnValue'=>(isset($order->totReturnValue) ? $order->totReturnValue : ''),
                            'DamagedValue'=>(isset($order->totDamagedValue) ? $order->totDamagedValue : ''),
                            'MissingValue'=>(isset($order->totMissingValue) ? $order->totMissingValue : ''),
                            'DamagedQty'=>(isset($order->totDamagedQty) ? $order->totDamagedQty : ''),
                            'MissingQty'=>(isset($order->totMissingQty) ? $order->totMissingQty : ''),
                            'ExcessQty'=>(isset($order->totExcessQty) ? $order->totExcessQty : ''),
                            'ExcessValue'=>(isset($order->totExcessValue) ? $order->totExcessValue : ''),
                            'shippedValue'=>(isset($order->shippedValue) ? number_format($order->shippedValue, 2) : 0),
                            'orderedQty'=>$order->orderedQty,
                            'hold_count'=>(isset($order->hold_count) ? $order->hold_count : 0),
                            'skuCount'=>$order->totSku,
                            'FillRate'=>(isset($fillRate) ? $fillRate : 0),
                            'DisFRate'=>(isset($fillRate) ? $fillRate : 0),
                            'Status'=>(isset($order->orderStatus) ? ucwords(strtolower($order->orderStatus)) : ''),
                            'nctTracker'=>(isset($order->nctStatus) ? ucwords(strtolower($order->nctStatus)) : ''),
                            'hold_reason'=>(isset($order->hold_reason) ? $order->hold_reason : ''),
                            'canReason'=>(isset($order->cancelReason) ? $order->cancelReason : ''),
                            'nextschdate'=>(isset($order->delivery_date) ? $order->delivery_date : ''),
                            'remStat'=>(isset($order->remStat) ? ucwords(strtolower($order->remStat)) : ''),
                            'collection_code'=>(isset($order->collection_code) ? $order->collection_code : ''),
                            'collection_date'=>(isset($order->collection_date) ? $order->collection_date : ''),
                            'collected_amount'=>(isset($order->collected_amount) ? $order->collected_amount : ''),
                            'collected_by'=>(isset($order->collected_by) ? $order->collected_by : ''),
                            'remittance_code'=>(isset($order->remittance_code) ? $order->remittance_code : ''),
                            'remittance_date'=>(isset($order->remittance_date) ? $order->remittance_date : ''),
                            'collected_by'=>(isset($order->collected_by) ? $order->collected_by : ''),


                            'hub_appr_date'=>(isset($order->hub_appr_date) ? $order->hub_appr_date : ''),
                            'hub_appr_by'=>(isset($order->hub_appr_by) ? $order->hub_appr_by : ''),
                            'fin_appr_date'=>(isset($order->fin_appr_date) ? $order->fin_appr_date : ''),
                            'fin_appr_by'=>(isset($order->fin_appr_by) ? $order->fin_appr_by : ''),

                            'Actions'=>'<a title="View" href="/salesorders/detail/'.((int)$order->gds_order_id).'"><i class="fa fa-eye"></i></a>&nbsp;'.$invoiceLink.''.$downloadLink.'&nbsp;'.$paymentLink.$returnLink,
                            "deliveractions"=>($ofddelivery ==1)?'<select name="returnReasons" id="returnReason_'.$order->gds_order_id.'" gds_order_id="'.$order->gds_order_id.'" class="form-control" onchange="partialDeliver('.$order->gds_order_id.')">
                                    <option value="">Please Select</option>
                                    <option value="17007">Full Delivery</option>
                                    <option value="17023">Partial Delivery</option>
                                    <option value="17022">Full Return</option>
                                    </select>':"",
                        );
                }
            }
            return Response::json(array('data'=>$dataArr, 'totalOrders'=>$totalOrders));
        }
        catch(Exception $e) {
            return Response::json(array('data'=>array(), 'totalOrders'=>0));
        }
    }

    /*
     * filterData() method is used to prepare filters condition from string
     * @param $filter String
     * @return Array
     */

    private function filterData($filter) {
        try{
        $stringArr = explode(' and ', $filter);
        $filterDataArr = array();

        if(is_array($stringArr)) {
            foreach ($stringArr as $data) {
                 if(substr_count($data, 'OrderID')) {
                    $data = str_replace(array(' ', 'ge0'), '', $data);
                 }

                 $dataArr = explode(' ', $data);
                 #print_r($data);die;
                 
                 if(substr_count($data, 'OrderExpDate') && substr_count($data, 'DateTime')) {
                     $dateTimeValArr = explode(' ', str_replace(array('T23:59:59', "'", 'DateTime'), '', $data));
                     $dateArr = explode('-', $dateTimeValArr[2]);
                     $filterDataArr['OrderExpDate'] = array('0'=>$dateArr[2], '1'=>$dateArr[1], '2'=>$dateArr[0]);
                     $filterDataArr['OrderExpDate']['operator'] = $this->getCondOperator($dateTimeValArr[1]);
                 }
                 else if(substr_count($data, 'OrderExpDate')) {
                     $filterDataArr['OrderExpDate']['operator'] = $this->getCondOperator($dataArr[1]);
                     $filterDataArr['OrderExpDate'][] = $dataArr[2];
                 }   

                 if(substr_count($data, 'pickedDate') && substr_count($data, 'DateTime')) {
                     $dateTimeValArr = explode(' ', str_replace(array('T23:59:59', "'", 'DateTime'), '', $data));
                     $dateArr = explode('-', $dateTimeValArr[2]);
                     $filterDataArr['pickedDate'] = array('0'=>$dateArr[2], '1'=>$dateArr[1], '2'=>$dateArr[0]);
                     $filterDataArr['pickedDate']['operator'] = $this->getCondOperator($dateTimeValArr[1]);
                 }
                 else if(substr_count($data, 'CancelDate')) {
                     $filterDataArr['CancelDate']['operator'] = $this->getCondOperator($dataArr[1]);
                     $filterDataArr['CancelDate'][] = $dataArr[2];
                 }   

                 if(substr_count($data, 'OrderDate') && substr_count($data, 'DateTime')) {
                     $dateTimeValArr = explode(' ', str_replace(array('T23:59:59', "'", 'DateTime'), '', $data));
                     $dateArr = explode('-', $dateTimeValArr[2]);
                     $filterDataArr['OrderDate'] = array('0'=>$dateArr[2], '1'=>$dateArr[1], '2'=>$dateArr[0]);
                     $filterDataArr['OrderDate']['operator'] = $this->getCondOperator($dateTimeValArr[1]);
                 }
                 else if(substr_count($data, 'OrderDate')) {
                     $filterDataArr['OrderDate']['operator'] = $this->getCondOperator($dataArr[1]);
                     $filterDataArr['OrderDate'][] = $dataArr[2];
                 }   

                 if(substr_count($data, 'CancelDate') && substr_count($data, 'DateTime')) {
                     $dateTimeValArr = explode(' ', str_replace(array('T23:59:59', "'", 'DateTime'), '', $data));
                     $dateArr = explode('-', $dateTimeValArr[2]);
                     $filterDataArr['CancelDate'] = array('0'=>$dateArr[2], '1'=>$dateArr[1], '2'=>$dateArr[0]);
                     $filterDataArr['CancelDate']['operator'] = $this->getCondOperator($dateTimeValArr[1]);
                 }
                 else if(substr_count($data, 'CancelDate')) {
                     $filterDataArr['CancelDate']['operator'] = $this->getCondOperator($dataArr[1]);
                     $filterDataArr['CancelDate'][] = $dataArr[2];
                 }  
                   

                 if(substr_count($data, 'SDT') && substr_count($data, 'DateTime')) {
                     $dateTimeValArr = explode(' ', str_replace(array('T23:59:59', "'", 'DateTime'), '', $data));
                     $dateArr = explode('-', $dateTimeValArr[2]);
                     $filterDataArr['SDT'] = array('0'=>$dateArr[2], '1'=>$dateArr[1], '2'=>$dateArr[0]);
                     $filterDataArr['SDT']['operator'] = $this->getCondOperator($dateTimeValArr[1]);
                 }
                 else if(substr_count($data, 'SDT')) {
                     $filterDataArr['SDT']['operator'] = $this->getCondOperator($dataArr[1]);
                     $filterDataArr['SDT'][] = $dataArr[2];
                 }
                                 
                 if(substr_count($data, 'ADT') && substr_count($data, 'DateTime')) {
                     $dateTimeValArr = explode(' ', str_replace(array('T23:59:59', "'", 'DateTime'), '', $data));
                     $dateArr = explode('-', $dateTimeValArr[2]);
                     $filterDataArr['ADT'] = array('0'=>$dateArr[2], '1'=>$dateArr[1], '2'=>$dateArr[0]);
                     $filterDataArr['ADT']['operator'] = $this->getCondOperator($dateTimeValArr[1]);
                 }
                 else if(substr_count($data, 'ADT')) {
                     $filterDataArr['ADT']['operator'] = $this->getCondOperator($dataArr[1]);
                     $filterDataArr['ADT'][] = $dataArr[2];
                 }

                 
                 if(substr_count($data, 'nextschdate') && substr_count($data, 'DateTime')) {
                     $dateTimeValArr = explode(' ', str_replace(array('T23:59:59', "'", 'DateTime'), '', $data));

                     $dateArr = explode('-', $dateTimeValArr[2]);
                     $filterDataArr['nextschdate'] = array('0'=>$dateArr[2], '1'=>$dateArr[1], '2'=>$dateArr[0]);
                     $filterDataArr['nextschdate']['operator'] = $this->getCondOperator($dateTimeValArr[1]);
                 }
                 else if(substr_count($data, 'nextschdate')) {
                     $filterDataArr['nextschdate']['operator'] = $this->getCondOperator($dataArr[1]);
                     $filterDataArr['nextschdate'][] = $dataArr[2];
                 }
                                 
                 if(substr_count($data, 'InvoiceDate') && substr_count($data, 'DateTime')) {
                     $dateTimeValArr = explode(' ', str_replace(array('T23:59:59', "'", 'DateTime'), '', $data));
                     $dateArr = explode('-', $dateTimeValArr[2]);
                     $filterDataArr['InvoiceDate'] = array('0'=>$dateArr[2], '1'=>$dateArr[1], '2'=>$dateArr[0]);
                     $filterDataArr['InvoiceDate']['operator'] = $this->getCondOperator($dateTimeValArr[1]);
                 }
                 else if(substr_count($data, 'InvoiceDate')) {
                     $filterDataArr['InvoiceDate']['operator'] = $this->getCondOperator($dataArr[1]);
                     $filterDataArr['InvoiceDate'][] = $dataArr[2];
                 }

                 if(substr_count($data, 'ReturnDate') && substr_count($data, 'DateTime')) {
                     $dateTimeValArr = explode(' ', str_replace(array('T23:59:59', "'", 'DateTime'), '', $data));
                     $dateArr = explode('-', $dateTimeValArr[2]);
                     $filterDataArr['ReturnDate'] = array('0'=>$dateArr[2], '1'=>$dateArr[1], '2'=>$dateArr[0]);
                     $filterDataArr['ReturnDate']['operator'] = $this->getCondOperator($dateTimeValArr[1]);
                 }
                 else if(substr_count($data, 'ReturnDate')) {
                     $filterDataArr['ReturnDate']['operator'] = $this->getCondOperator($dataArr[1]);
                     $filterDataArr['ReturnDate'][] = $dataArr[2];
                 }
                                 
                 if(substr_count($data, 'pickerdate') && substr_count($data, 'DateTime')) {
                     $dateTimeValArr = explode(' ', str_replace(array('T23:59:59', "'", 'DateTime'), '', $data));
                     $dateArr = explode('-', $dateTimeValArr[2]);
                     $filterDataArr['pickerdate'] = array('0'=>$dateArr[2], '1'=>$dateArr[1], '2'=>$dateArr[0]);
                     $filterDataArr['pickerdate']['operator'] = $this->getCondOperator($dateTimeValArr[1]);
                 }
                 else if(substr_count($data, 'pickerdate')) {
                     $filterDataArr['pickerdate']['operator'] = $this->getCondOperator($dataArr[1]);
                     $filterDataArr['pickerdate'][] = $dataArr[2];
                 }
                                 
                 if(substr_count($data, 'shipmentDate') && substr_count($data, 'DateTime')) {
                     $dateTimeValArr = explode(' ', str_replace(array('T23:59:59', "'", 'DateTime'), '', $data));
                     $dateArr = explode('-', $dateTimeValArr[2]);
                     $filterDataArr['shipmentDate'] = array('0'=>$dateArr[2], '1'=>$dateArr[1], '2'=>$dateArr[0]);
                     $filterDataArr['shipmentDate']['operator'] = $this->getCondOperator($dateTimeValArr[1]);
                 }
                 else if(substr_count($data, 'shipmentDate')) {
                     $filterDataArr['shipmentDate']['operator'] = $this->getCondOperator($dataArr[1]);
                     $filterDataArr['shipmentDate'][] = $dataArr[2];
                 }
                
                if(substr_count($data, 'custRating') && !array_key_exists('custRating', $filterDataArr)) {
                     $statusValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'custRating'), '', $data));
                     $filterDataArr['custRating'] = array('operator'=>'LIKE', 'value'=>$statusValArr[0]);
                 }

                if(substr_count($data, 'remStat') && !array_key_exists('remStat', $filterDataArr)) {
                     $statusValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'remStat'), '', $data));
                     $filterDataArr['remStat'] = array('operator'=>'LIKE', 'value'=>$statusValArr[0]);
                 }

                 if(substr_count($data, 'Hub') && !array_key_exists('Hub', $filterDataArr)) {
                     $hubValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'Hub'), '', $data));
                     $filterDataArr['Hub'] = array('operator'=>'LIKE', 'value'=>$hubValArr[0]);
                 }


                 if(substr_count($data, 'User') && !array_key_exists('User', $filterDataArr)) {
                     $custValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'User'), '', $data));
                     $filterDataArr['User'] = array('operator'=>'LIKE', 'value'=>$custValArr[0]);
                 }
                                if(substr_count($data, 'Customer') && !array_key_exists('Customer', $filterDataArr)) {
                                        $custValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'Customer'), '', $data));
                                        $filterDataArr['Customer'] = array('operator'=>'LIKE', 'value'=>$custValArr[0]);
                                }

                 if(substr_count($data, 'custcode') && !array_key_exists('custcode', $filterDataArr)) {
                     $custValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'custcode'), '', $data));
                     $filterDataArr['custcode'] = array('operator'=>'LIKE', 'value'=>$custValArr[0]);
                 }
                 

                 if(substr_count($data, 'del_name') && !array_key_exists('del_name', $filterDataArr)) {
                     $custValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'del_name'), '', $data));
                     $filterDataArr['del_name'] = array('operator'=>'LIKE', 'value'=>$custValArr[0]);
                 }
                 if(substr_count($data, 'beat') && !array_key_exists('beat', $filterDataArr)) {
                     $custValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'beat'), '', $data));
                     $filterDataArr['beat'] = array('operator'=>'LIKE', 'value'=>$custValArr[0]);
                 }
                 if(substr_count($data, 'picker') && !array_key_exists('picker', $filterDataArr)) {
                     $custValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'picker'), '', $data));
                     $filterDataArr['picker'] = array('operator'=>'LIKE', 'value'=>$custValArr[0]);
                 }
                 if(substr_count($data, 'ChannelName') && !array_key_exists('ChannelName', $filterDataArr)) {
                     $channelValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'ChannelName'), '', $data));
                     $filterDataArr['ChannelName'] = array('operator'=>'LIKE', 'value'=>$channelValArr[0]);
                 }

                 if(substr_count($data, 'OrderID') && !array_key_exists('OrderID', $filterDataArr)) {
                    $orderValArr = explode(' ', str_replace(array('(', ')', "'", 'indexof', 'tolower', 'OrderID'), '', $data));
                     $filterDataArr['OrderID'] = array('operator'=>'LIKE', 'value'=>$orderValArr[0]);
                 }

                 if(substr_count($data, 'ChannelID') && !array_key_exists('ChannelID', $filterDataArr)) {
                     $filterDataArr['ChannelID'] = array('operator'=>$this->getCondOperator($dataArr[1]), 'value'=>$dataArr[2]);
                 }

                 if(substr_count($data, 'Status') && !array_key_exists('Status', $filterDataArr)) {
                     $statusValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'Status'), '', $data));
                     $filterDataArr['Status'] = array('operator'=>'LIKE', 'value'=>$statusValArr[0]);
                 }

                 if(substr_count($data, 'nctTracker') && !array_key_exists('nctTracker', $filterDataArr)) {
                     $statusValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'nctTracker'), '', $data));
                     $filterDataArr['nctTracker'] = array('operator'=>'LIKE', 'value'=>$statusValArr[0]);
                 }
                 

                 if(substr_count($data, 'Area') && !array_key_exists('Area', $filterDataArr)) {
                     $statusValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'Area'), '', $data));
                     $filterDataArr['Area'] = array('operator'=>'LIKE', 'value'=>$statusValArr[0]);
                 }

                 if(substr_count($data, 'spoke') && !array_key_exists('spoke', $filterDataArr)){
                    $custValArr = explode(' ', str_replace(array('(',')',"'",',','indexof','tolower', 'spoke'),'', $data));
                    $filterDataArr['spoke'] = array('operator' => 'LIKE', 'value'=>$custValArr[0]);
                 }               
                                 
                 if(substr_count($data, 'InvoiceQty') && !array_key_exists('InvoiceQty', $filterDataArr)) {
                     $statusValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'InvoiceQty'), '', $data));                   
                     $filterDataArr['InvoiceQty'] = array('operator'=>$this->getCondOperator($dataArr[1]), 'value'=>$dataArr[2]);
                 }                                               
                 if(substr_count($data, 'OrderValue') && !array_key_exists('OrderValue', $filterDataArr)) {
                     $filterDataArr['OrderValue'] = array('operator'=>$this->getCondOperator($dataArr[1]), 'value'=>$dataArr[2]);
                 }

                 if(substr_count($data, 'CancelledValue') && !array_key_exists('CancelledValue', $filterDataArr)) {
                     $filterDataArr['CancelledValue'] = array('operator'=>$this->getCondOperator($dataArr[1]), 'value'=>$dataArr[2]);
                 }
                 if(substr_count($data, 'CancelledQty') && !array_key_exists('CancelledQty', $filterDataArr))
                 {
                    $filterDataArr['CancelledQty'] = array('operator'=>$this->getCondOperator($dataArr[1]), 'value'=>$dataArr[2]);
                 }

                 if(substr_count($data, 'ReturnValue') && !array_key_exists('ReturnValue', $filterDataArr)) {
                     $filterDataArr['ReturnValue'] = array('operator'=>$this->getCondOperator($dataArr[1]), 'value'=>$dataArr[2]);
                 }

                 if(substr_count($data, 'DamagedValue') && !array_key_exists('DamagedValue', $filterDataArr)) {
                     $filterDataArr['DamagedValue'] = array('operator'=>$this->getCondOperator($dataArr[1]), 'value'=>$dataArr[2]);
                 }


                 if(substr_count($data, 'MissingValue') && !array_key_exists('MissingValue', $filterDataArr)) {
                     $filterDataArr['MissingValue'] = array('operator'=>$this->getCondOperator($dataArr[1]), 'value'=>$dataArr[2]);
                 }

                 if(substr_count($data, 'DamagedQty') && !array_key_exists('DamagedQty', $filterDataArr)) {
                     $filterDataArr['DamagedQty'] = array('operator'=>$this->getCondOperator($dataArr[1]), 'value'=>$dataArr[2]);
                 }


                 if(substr_count($data, 'MissingQty') && !array_key_exists('MissingQty', $filterDataArr)) {
                     $filterDataArr['MissingQty'] = array('operator'=>$this->getCondOperator($dataArr[1]), 'value'=>$dataArr[2]);
                 }

                 if(substr_count($data, 'ExcessQty') && !array_key_exists('ExcessQty', $filterDataArr)) {
                     $filterDataArr['ExcessQty'] = array('operator'=>$this->getCondOperator($dataArr[1]), 'value'=>$dataArr[2]);
                 }
                if(substr_count($data, 'ExcessValue') && !array_key_exists('ExcessValue', $filterDataArr)) {
                     $filterDataArr['ExcessValue'] = array('operator'=>$this->getCondOperator($dataArr[1]), 'value'=>$dataArr[2]);
                 }

                 if(substr_count($data, 'cartons') && !array_key_exists('cartons', $filterDataArr)) {
                     $filterDataArr['cartons'] = array('operator'=>$this->getCondOperator($dataArr[1]), 'value'=>$dataArr[2]);
                 }
                 if(substr_count($data, 'bags') && !array_key_exists('bags', $filterDataArr)) {
                     $filterDataArr['bags'] = array('operator'=>$this->getCondOperator($dataArr[1]), 'value'=>$dataArr[2]);
                 }
                 if(substr_count($data, 'crates') && !array_key_exists('crates', $filterDataArr)) {
                     $filterDataArr['crates'] = array('operator'=>$this->getCondOperator($dataArr[1]), 'value'=>$dataArr[2]);
                 }           
                                
                 if(substr_count($data, 'invoice_code') && !array_key_exists('invoice_code', $filterDataArr)) {
                     $statusValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'invoice_code'), '', $data));
                     $filterDataArr['invoice_code'] = array('operator'=>'LIKE', 'value'=>$statusValArr[0]);
                 }
                 
                    if (substr_count($data, 'rt_de_name') && !array_key_exists('rt_de_name', $filterDataArr)) {
                        $rtDeNameValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'rt_de_name'), '', $data));
                        $filterDataArr['rt_de_name'] = array('operator' => 'LIKE', 'value' => $rtDeNameValArr[0]);
                    }

                    if (substr_count($data, 'rt_re_name') && !array_key_exists('rt_re_name', $filterDataArr)) {
                        $rtReNameValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'rt_re_name'), '', $data));
                        $filterDataArr['rt_re_name'] = array('operator' => 'LIKE', 'value' => $rtReNameValArr[0]);
                    }
                    
                    if (substr_count($data, 'rt_del_date') && substr_count($data, 'DateTime')) {
                        $dateTimeValArr = explode(' ', str_replace(array('T23:59:59', "'", 'DateTime'), '', $data));
                        $dateArr = explode('-', $dateTimeValArr[2]);
                        $filterDataArr['rt_del_date'] = array('0' => $dateArr[2], '1' => $dateArr[1], '2' => $dateArr[0]);
                        $filterDataArr['rt_del_date']['operator'] = $this->getCondOperator($dateTimeValArr[1]);
                    } else if (substr_count($data, 'rt_del_date')) {
                        $filterDataArr['rt_del_date']['operator'] = $this->getCondOperator($dataArr[1]);
                        $filterDataArr['rt_del_date'][] = $dataArr[2];
                    }
                    
                    if (substr_count($data, 'rt_received_at') && substr_count($data, 'DateTime')) {
                        $dateTimeValArr = explode(' ', str_replace(array('T23:59:59', "'", 'DateTime'), '', $data));
                        $dateArr = explode('-', $dateTimeValArr[2]);
                        $filterDataArr['rt_received_at'] = array('0' => $dateArr[2], '1' => $dateArr[1], '2' => $dateArr[0]);
                        $filterDataArr['rt_received_at']['operator'] = $this->getCondOperator($dateTimeValArr[1]);
                    } else if (substr_count($data, 'rt_received_at')) {
                        $filterDataArr['rt_received_at']['operator'] = $this->getCondOperator($dataArr[1]);
                        $filterDataArr['rt_received_at'][] = $dataArr[2];
                    }
                    
                    if (substr_count($data, 'rt_vehicle_no') && !array_key_exists('rt_vehicle_no', $filterDataArr)) {
                        $rtVehNumValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'rt_vehicle_no'), '', $data));
                        $filterDataArr['rt_vehicle_no'] = array('operator' => 'LIKE', 'value' => $rtVehNumValArr[0]);
                    }
                    
                    if (substr_count($data, 'rt_driver_name') && !array_key_exists('rt_driver_name', $filterDataArr)) {
                        $rtDrNameValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'rt_driver_name'), '', $data));
                        $filterDataArr['rt_driver_name'] = array('operator' => 'LIKE', 'value' => $rtDrNameValArr[0]);
                    }
                    
                    if (substr_count($data, 'rt_driver_mobile') && !array_key_exists('rt_driver_mobile', $filterDataArr)) {
                        $rtDtNumValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'rt_driver_mobile'), '', $data));
                        $filterDataArr['rt_driver_mobile'] = array('operator' => 'LIKE', 'value' => $rtDtNumValArr[0]);
                    }
                    
                    if (substr_count($data, 'rt_docket_no') && !array_key_exists('rt_docket_no', $filterDataArr)) {
                        $rtDocNumValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'rt_docket_no'), '', $data));
                        $filterDataArr['rt_docket_no'] = array('operator' => 'LIKE', 'value' => $rtDocNumValArr[0]);
                    }

                 if(substr_count($data, 'st_de_name') && !array_key_exists('st_de_name', $filterDataArr)) {

                     $stDeNameValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'st_de_name'), '', $data));
                     $filterDataArr['st_de_name'] = array('operator'=>'LIKE', 'value'=>$stDeNameValArr[0]);
                 
                 }


                 if(substr_count($data, 'st_re_name') && !array_key_exists('st_re_name', $filterDataArr)) {

                     $stDeNameValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'st_re_name'), '', $data));
                     $filterDataArr['st_re_name'] = array('operator'=>'LIKE', 'value'=>$stDeNameValArr[0]);
                 
                 }

                 if(substr_count($data, 'st_del_date') && substr_count($data, 'DateTime')) {
                     $dateTimeValArr = explode(' ', str_replace(array('T23:59:59', "'", 'DateTime'), '', $data));
                     $dateArr = explode('-', $dateTimeValArr[2]);
                     $filterDataArr['st_del_date'] = array('0'=>$dateArr[2], '1'=>$dateArr[1], '2'=>$dateArr[0]);
                     $filterDataArr['st_del_date']['operator'] = $this->getCondOperator($dateTimeValArr[1]);
                 }
                 else if(substr_count($data, 'st_del_date')) {
                     $filterDataArr['st_del_date']['operator'] = $this->getCondOperator($dataArr[1]);
                     $filterDataArr['st_del_date'][] = $dataArr[2];
                 }

                 if(substr_count($data, 'st_received_at') && substr_count($data, 'DateTime')) {
                     $dateTimeValArr = explode(' ', str_replace(array('T23:59:59', "'", 'DateTime'), '', $data));
                     $dateArr = explode('-', $dateTimeValArr[2]);
                     $filterDataArr['st_received_at'] = array('0'=>$dateArr[2], '1'=>$dateArr[1], '2'=>$dateArr[0]);
                     $filterDataArr['st_received_at']['operator'] = $this->getCondOperator($dateTimeValArr[1]);
                 }
                 else if(substr_count($data, 'st_received_at')) {
                     $filterDataArr['st_received_at']['operator'] = $this->getCondOperator($dataArr[1]);
                     $filterDataArr['st_received_at'][] = $dataArr[2];
                 }

                 if(substr_count($data, 'st_vehicle_no') && !array_key_exists('st_vehicle_no', $filterDataArr)) {
                     $stVehNumValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'st_vehicle_no'), '', $data));
                     $filterDataArr['st_vehicle_no'] = array('operator'=>'LIKE', 'value'=>$stVehNumValArr[0]);
                 }
                 if(substr_count($data, 'st_driver_name') && !array_key_exists('st_driver_name', $filterDataArr)) {
                     $stDrNameValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'st_driver_name'), '', $data));
                     $filterDataArr['st_driver_name'] = array('operator'=>'LIKE', 'value'=>$stDrNameValArr[0]);

                 }
                 if(substr_count($data, 'st_driver_mobile') && !array_key_exists('st_driver_mobile', $filterDataArr)) {
                     $stDtNumValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'st_driver_mobile'), '', $data));
                     $filterDataArr['st_driver_mobile'] = array('operator'=>'LIKE', 'value'=>$stDtNumValArr[0]);
                 }
                 if(substr_count($data, 'st_docket_no') && !array_key_exists('st_docket_no', $filterDataArr)) {

                     $stDocNumValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'st_docket_no'), '', $data));
                     $filterDataArr['st_docket_no'] = array('operator'=>'LIKE', 'value'=>$stDocNumValArr[0]);

                 }



                 if(substr_count($data, 'pickedby') && !array_key_exists('pickedby', $filterDataArr)) {
                     $custValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'pickedby'), '', $data));
                     $filterDataArr['pickedby'] = array('operator'=>'LIKE', 'value'=>$custValArr[0]);
                 }

                 if(substr_count($data, 'verifiedby') && !array_key_exists('verifiedby', $filterDataArr)) {
                     $verValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'verifiedby'), '', $data));
                     $filterDataArr['verifiedby'] = array('operator'=>'LIKE', 'value'=>$verValArr[0]);
                 }

                 if(substr_count($data, 'hold_reason') && !array_key_exists('hold_reason', $filterDataArr)) {
                     $statusValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'hold_reason'), '', $data));
                     $filterDataArr['hold_reason'] = array('operator'=>'LIKE', 'value'=>$statusValArr[0]);
                 }

                 if(substr_count($data, 'canReason') && !array_key_exists('canReason', $filterDataArr)) {
                     $statusValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'canReason'), '', $data));
                     $filterDataArr['canReason'] = array('operator'=>'LIKE', 'value'=>$statusValArr[0]);
                 }
                            
                 
                 if(substr_count($data, 'InvoiceValue') && !array_key_exists('InvoiceValue', $filterDataArr)) {
                     $filterDataArr['InvoiceValue'] = array('operator'=>$this->getCondOperator($dataArr[1]), 'value'=>$dataArr[2]);
                 }
                                 
                 if(substr_count($data, 'ReturnQty') && !array_key_exists('ReturnQty', $filterDataArr)) {
                     $filterDataArr['ReturnQty'] = array('operator'=>$this->getCondOperator($dataArr[1]), 'value'=>$dataArr[2]);
                 }
                 
                 if(substr_count($data, 'FillRate') && !array_key_exists('FillRate', $filterDataArr)) {
                     $filterDataArr['FillRate'] = array('operator'=>$this->getCondOperator($dataArr[1]), 'value'=>$dataArr[2]);
                 }
                 
                 if(substr_count($data, 'DisFRate') && !array_key_exists('DisFRate', $filterDataArr)) {
                     $filterDataArr['DisFRate'] = array('operator'=>$this->getCondOperator($dataArr[1]), 'value'=>$dataArr[2]);
                 } 

                 if(substr_count($data, 'pickno') && !array_key_exists('pickno', $filterDataArr)) {
                     $filterDataArr['pickno'] = array('operator'=>$this->getCondOperator($dataArr[1]), 'value'=>$dataArr[2]);
                 }
                                                 
                  if(substr_count($data, 'hold_count') && !array_key_exists('hold_count', $filterDataArr)) {
                     $filterDataArr['hold_count'] = array('operator'=>$this->getCondOperator($dataArr[1]), 'value'=>$dataArr[2]);
                 }

                 if (substr_count($data, 'orderedQty') && !array_key_exists('orderedQty', $filterDataArr)) {
                    $filterDataArr['orderedQty'] = array('operator' => $this->getCondOperator($dataArr[1]), 'value' => $dataArr[2]);
                 }

                 if(substr_count($data, 'skuCount') && !array_key_exists('skuCount', $filterDataArr)) {
                     $filterDataArr['skuCount'] = array('operator'=>$this->getCondOperator($dataArr[1]), 'value'=>$dataArr[2]);
                 }
                 if(substr_count($data, 'SDS1') && !array_key_exists('SDS1', $filterDataArr)) {
                     $slot1ValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'SDS1', 'ge 0'), '', $data));

                     $slot1 = trim((isset($slot1ValArr[0]) ? $slot1ValArr[0] : '').' '.(isset($slot1ValArr[1]) ? $slot1ValArr[1] : '').' '.(isset($slot1ValArr[2]) ? $slot1ValArr[2] : ''));
                     $filterDataArr['SDS1'] = array('operator'=>'LIKE', 'value'=>$slot1);
                 }
                
                 if(substr_count($data, 'SDS2') && !array_key_exists('SDS2', $filterDataArr)) {
                     $slot2ValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'SDS2', 'ge 0'), '', $data));
                     $slot2 = trim((isset($slot2ValArr[0]) ? $slot2ValArr[0] : '').' '.(isset($slot2ValArr[1]) ? $slot2ValArr[1] : '').' '.(isset($slot2ValArr[2]) ? $slot2ValArr[2] : ''));

                     $filterDataArr['SDS2'] = array('operator'=>'LIKE', 'value'=>$slot2);
                 }


                 if(substr_count($data, 'collection_code') && !array_key_exists('collection_code', $filterDataArr)) {
                     $slot1ValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'collection_code', 'ge 0'), '', $data));

                     $slot1 = trim((isset($slot1ValArr[0]) ? $slot1ValArr[0] : '').' '.(isset($slot1ValArr[1]) ? $slot1ValArr[1] : '').' '.(isset($slot1ValArr[2]) ? $slot1ValArr[2] : ''));
                     $filterDataArr['collection_code'] = array('operator'=>'LIKE', 'value'=>$slot1);
                 }


                 if(substr_count($data, 'collected_amount') && !array_key_exists('collected_amount', $filterDataArr)) {
                     $filterDataArr['collected_amount'] = array('operator'=>$this->getCondOperator($dataArr[1]), 'value'=>$dataArr[2]);
                 }

                 if(substr_count($data, 'collected_by') && !array_key_exists('collected_by', $filterDataArr)) {
                     $slot1ValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'collected_by', 'ge 0'), '', $data));

                     $slot1 = trim((isset($slot1ValArr[0]) ? $slot1ValArr[0] : '').' '.(isset($slot1ValArr[1]) ? $slot1ValArr[1] : '').' '.(isset($slot1ValArr[2]) ? $slot1ValArr[2] : ''));
                     $filterDataArr['collected_by'] = array('operator'=>'LIKE', 'value'=>$slot1);
                 }

                 if(substr_count($data, 'collection_date') && substr_count($data, 'DateTime')) {
                     $dateTimeValArr = explode(' ', str_replace(array('T23:59:59', "'", 'DateTime'), '', $data));
                     $dateArr = explode('-', $dateTimeValArr[2]);
                     $filterDataArr['collection_date'] = array('0'=>$dateArr[2], '1'=>$dateArr[1], '2'=>$dateArr[0]);
                     $filterDataArr['collection_date']['operator'] = $this->getCondOperator($dateTimeValArr[1]);
                 }
                 else if(substr_count($data, 'collection_date')) {
                     $filterDataArr['collection_date']['operator'] = $this->getCondOperator($dataArr[1]);
                     $filterDataArr['collection_date'][] = $dataArr[2];
                 }

                 if(substr_count($data, 'remittance_code') && !array_key_exists('remittance_code', $filterDataArr)) {
                     $slot1ValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'remittance_code', 'ge 0'), '', $data));

                     $slot1 = trim((isset($slot1ValArr[0]) ? $slot1ValArr[0] : '').' '.(isset($slot1ValArr[1]) ? $slot1ValArr[1] : '').' '.(isset($slot1ValArr[2]) ? $slot1ValArr[2] : ''));
                     $filterDataArr['remittance_code'] = array('operator'=>'LIKE', 'value'=>$slot1);
                 }

                 if(substr_count($data, 'remittance_date') && substr_count($data, 'DateTime')) {
                     $dateTimeValArr = explode(' ', str_replace(array('T23:59:59', "'", 'DateTime'), '', $data));
                     $dateArr = explode('-', $dateTimeValArr[2]);
                     $filterDataArr['remittance_date'] = array('0'=>$dateArr[2], '1'=>$dateArr[1], '2'=>$dateArr[0]);
                     $filterDataArr['remittance_date']['operator'] = $this->getCondOperator($dateTimeValArr[1]);
                 }
                 else if(substr_count($data, 'remittance_date')) {
                     $filterDataArr['remittance_date']['operator'] = $this->getCondOperator($dataArr[1]);
                     $filterDataArr['remittance_date'][] = $dataArr[2];
                 }

                 if(substr_count($data, 'hub_appr_date') && substr_count($data, 'DateTime')) {
                     $dateTimeValArr = explode(' ', str_replace(array('T23:59:59', "'", 'DateTime'), '', $data));
                     $dateArr = explode('-', $dateTimeValArr[2]);
                     $filterDataArr['hub_appr_date'] = array('0'=>$dateArr[2], '1'=>$dateArr[1], '2'=>$dateArr[0]);
                     $filterDataArr['hub_appr_date']['operator'] = $this->getCondOperator($dateTimeValArr[1]);
                 }
                 else if(substr_count($data, 'hub_appr_date')) {
                     $filterDataArr['hub_appr_date']['operator'] = $this->getCondOperator($dataArr[1]);
                     $filterDataArr['hub_appr_date'][] = $dataArr[2];
                 }


                 if(substr_count($data, 'hub_appr_by') && !array_key_exists('hub_appr_by', $filterDataArr)) {
                     $slot1ValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'hub_appr_by', 'ge 0'), '', $data));

                     $slot1 = trim((isset($slot1ValArr[0]) ? $slot1ValArr[0] : '').' '.(isset($slot1ValArr[1]) ? $slot1ValArr[1] : '').' '.(isset($slot1ValArr[2]) ? $slot1ValArr[2] : ''));
                     $filterDataArr['hub_appr_by'] = array('operator'=>'LIKE', 'value'=>$slot1);
                 }

                 if(substr_count($data, 'fin_appr_date') && substr_count($data, 'DateTime')) {
                     $dateTimeValArr = explode(' ', str_replace(array('T23:59:59', "'", 'DateTime'), '', $data));
                     $dateArr = explode('-', $dateTimeValArr[2]);
                     $filterDataArr['fin_appr_date'] = array('0'=>$dateArr[2], '1'=>$dateArr[1], '2'=>$dateArr[0]);
                     $filterDataArr['fin_appr_date']['operator'] = $this->getCondOperator($dateTimeValArr[1]);
                 }
                 else if(substr_count($data, 'fin_appr_date')) {
                     $filterDataArr['fin_appr_date']['operator'] = $this->getCondOperator($dataArr[1]);
                     $filterDataArr['fin_appr_date'][] = $dataArr[2];
                 }



                 if(substr_count($data, 'fin_appr_by') && !array_key_exists('fin_appr_by', $filterDataArr)) {
                     $slot1ValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'fin_appr_by', 'ge 0'), '', $data));

                     $slot1 = trim((isset($slot1ValArr[0]) ? $slot1ValArr[0] : '').' '.(isset($slot1ValArr[1]) ? $slot1ValArr[1] : '').' '.(isset($slot1ValArr[2]) ? $slot1ValArr[2] : ''));
                     $filterDataArr['fin_appr_by'] = array('operator'=>'LIKE', 'value'=>$slot1);
                 }


            }
        }
        return $filterDataArr;
            }
            catch(Exception $e) {
                Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            }
    }

    /*
     * getCondOperator() method is used to get condition operator
     * @param $operator String
     * @return String
     */

    private function getCondOperator($operator) {
            try{
                $condOperator='';
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
            }
            catch(Exception $e) {
                Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            }
    }

    public function getVehicleByHub($hubId, $Hub_DC='hub') {
        try {
        
            $data = $this->_orderModel->getVehicleByHub(array($hubId),$Hub_DC);

            return Response::json(array('status' => 200, 'message' => 'Success', 'data'=>$data));

        
        } catch(Exception $e) {
                Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    public function getDriverByVehicleId($vehicleId) {
        try {

            $data = $this->_orderModel->getDriverByVehicleId(array($vehicleId));

            return Response::json(array('status' => 200, 'message' => 'Success', 'data'=>$data));

        } catch(Exception $e) {
                Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    } 

    public function getOFDDeliveryDetails(Request $request, $status = null,$sales_type=2) {

        try {

       
                $data = Input::all();
                if(isset($data['ids']) && !empty($data['ids'])) {
                    $filter['gds_order_id'] = $data['ids'];
                    $request = new Request();
                    $filter_data = array('page'=> 0,'pageSize'=> 50,'pk'=> 'OrderID','gds_order_id'=>$filter['gds_order_id']);
                    $griddata = $this->filterOrdersAction($request, $status = 'ofd',$sales_type,$filter_data);
                    // $griddata = $this->_gdsOrders->getOrderData('ofd',0,0,10,$filter);
                    return $griddata;
                }

            }           
                catch(Exception $ex) {
                  Log::error($ex->getMessage().' '.$ex->getTraceAsString());
                  return Response::json(array('status'=>400, 'message'=>'Failed'));
            }

    }

    public function getDeliveryDetails(Request $request, $status = null) {

        return View::make('Orders::Form.ofdDeliveryForm');
    }
  
    public function fulldeliver(Request $request){
        $orderdata = $request->input('ids');
        foreach ($orderdata as $key => $value) {
            # code...
            $orderId = $value;
            $this->_InvoiceModel = new Invoice();
            $invoiceInfo = $this->_InvoiceModel->getInvoiceGridOrderId(array($orderId), array('grid.gds_invoice_grid_id','grid.ecash_applied'));
            $userId = \Session::get('userId');
            $token = $this->_orderModel->getTokenByUserid($userId);

            if(!isset($invoiceInfo[0]->gds_invoice_grid_id)){
                $response = array('status' => 400, 'message' => 'Order not yet Invoiced!');
                return json_encode($response);
            }
            $invoiceId = $invoiceInfo[0]->gds_invoice_grid_id;
            $ecash_applied = $invoiceInfo[0]->ecash_applied;
            $invoiceAmt = $this->_InvoiceModel->getInvoicedPriceWithOrderID($orderId);
            
            $data = [
                'flag' => 2,
                'deliver_token' => $token,
                'module_id' => 1,
                'user_id' => $userId,
                'order_id' => $orderId,
                'invoice_id' => $invoiceId,
                'net_amount' => $invoiceAmt,
                 'amount' => $invoiceAmt,
                'amount_collected' => 0,
                'amount_credit' => 0,
                'collectable_amt' => 0,
                'amount_return' => 0,
                'payment_mode' => 22010,
                'reference_no' => '--NA--',
                'round_of_value' => $invoiceAmt - round($invoiceAmt,2),
                'discount_applied' => 0,
                'discount_deducted' => 0,
                'ecash_applied' => $ecash_applied,
                'returns' => [],
                'payments' => [],
            ];
            
            $pickObj = new pickController();
            $response = $pickObj->getInvoiceByReturn($data);
            $response = json_decode($response);
        }

        return array('message'=>"All Orders Deliverd!");
            
    }

    public function getOrderDeliveryData(Request $request){
        $gds_order_id = $request->input('gds_order_id');
        $this->_InvoiceModel = new Invoice();
        $invoiceInfo = $this->_InvoiceModel->getInvoiceGridOrderId(array($gds_order_id), array('grid.gds_invoice_grid_id','grid.ecash_applied'));
        $userId = \Session::get('userId');
            $token = $this->_orderModel->getTokenByUserid($userId);
        $invoiceId = $invoiceInfo[0]->gds_invoice_grid_id;

        $data= array('picker_token'=>$token,"invoice_id"=>$invoiceId,"module_id"=>1);

        $pickObj = new pickController();
        $response = $pickObj->getorderdetailbyinvoice($data);
        $products = $response['data']['products'];

        $OrderModel = new \App\Modules\Cpmanager\Models\OrderModel;
        $returnReasons = $OrderModel->returnReasons();

        $productsData = '<tr class="subhead">
                                <th width="45%" align="left" valign="middle">Product Name (SKU) </th>
                                <th width="15%" align="left" valign="middle">Invoice Qty</th>
                                <th width="15%" align="left" valign="middle">Return Qty</th>
                                <th width="15%" align="left" valign="middle">Deliver Qty</th>
                                <th width="10%" align="left" valign="middle">Return Reason</th>
                            </tr>';
        foreach ($products as $key => $value) {
            $returnHtml = '<select id="'.$response['data']['address']->order_id.'_return_reason_'.$value->product_id.'" name="returnReason"  class="form-control">';
            foreach ($returnReasons as $rkey => $revalue) {
                # code...
                $returnHtml .= '<option value="'.$revalue->value.'">'.$revalue->name.'</option>';
            }

            $returnHtml .= "</select>";

            $productsData .= '<tr class="subhead ">
                                    <td align="left" valign="middle"><b>'.$value->product_name.' <span style="color:blue"><b>('.$value->sku.')</b></span></b></td>
                                    <td style="color:red" align="left" valign="middle"><input type="number" name="invoiced_qty" id="'.$response['data']['address']->order_id.'_invoice_qty_'.$value->product_id.'" value='.$value->invoiced_qty.' disabled="disabled"></input></td>
                                    <td align="left" valign="middle"><input type="number" name="return_qty" value='.$value->return_qty.' id="'.$response['data']['address']->order_id.'_return_qty_'.$value->product_id.'" onchange="changeretrnqty('.$response['data']['address']->order_id.','.$value->product_id.')"></input></td>
                                    <td align="left" valign="middle"><input type="number" name="deliver_qtyArr" value="0" product_id="'.$value->product_id.'" id="'.$response['data']['address']->order_id.'_deliver_qty_'.$value->product_id.'" disabled="disabled"></input></td>
                                    <td>'.$returnHtml.'</td>
                                </tr>';
        }
        $returndata = array('data'=>$productsData,'message'=>'Order Details','status'=>200,'order_code'=>$response['data']['address']->order_code);
        return $returndata;
    }

    public function saveOrderdeliveryTemp(Request $request){

        $gds_order_id = $request->input('gds_order_id');
        $gds_order_status_flag = $request->input('gds_order_status_flag');

        $orderId = $gds_order_id;
        $this->_InvoiceModel = new Invoice();
        $invoiceInfo = $this->_InvoiceModel->getInvoiceGridOrderId(array($orderId), array('grid.gds_invoice_grid_id','grid.ecash_applied'));
        $invoiceId = $invoiceInfo[0]->gds_invoice_grid_id;
        $userId = \Session::get('userId');
        $token = $this->_orderModel->getTokenByUserid($userId);
        $data= array('picker_token'=>$token,"invoice_id"=>$invoiceId,"module_id"=>1);
        $pickObj = new pickController();
        $response = $pickObj->getorderdetailbyinvoice($data);
        $products = $response['data']['products'];
        $productPriceArr = array();
        foreach ($products as $key => $value) {
            # code...

            $productPriceArr[$value->product_id] = $value->singleUnitPriceWithtax;
        }
        if(!isset($invoiceInfo[0]->gds_invoice_grid_id)){
            $response = array('status' => 400, 'message' => 'Order not yet Invoiced!');
            return json_encode($response);
        }
        $invoiceId = $invoiceInfo[0]->gds_invoice_grid_id;
        $ecash_applied = ($invoiceInfo[0]->ecash_applied>0) ? $invoiceInfo[0]->ecash_applied : 0;
        $invoiceAmt = $this->_InvoiceModel->getInvoicedPriceWithOrderID($orderId);
        
        $returns = array();
        $returnArray = $request->input('productArr');
        $amount_return = 0;
        $productsArray = array();
        foreach ($products as $key => $value) {
            # code...
            $product_id = $value->product_id;
            $return_qty = 0;
            $deliver_qty = 0;
            $return_id = 0;
            $has_parent = $this->_orderModel->isFreebie($product_id);
            if(isset($returnArray[$product_id])){
                $amount_return += $returnArray[$product_id]['return_qty'] * $productPriceArr[$product_id];
                $return_qty = $returnArray[$product_id]['return_qty'];
                $deliver_qty = $returnArray[$product_id]['deliver_qty'];
                $return_id = $returnArray[$product_id]['return_id'];
                $temp = array(
                    "product_id"=> $product_id,
                    "return_qty"=> $return_qty,
                    "delivered_qty"=> $deliver_qty,
                    "return_reason"=> $return_id,
                    "has_parent"=> $has_parent
                    );
                array_push($returns, $temp);
            }
            $product = $this->_orderModel->getProductByOrderId($orderId, array($product_id));
            $product = $product[0];
            $tax_per_object = $this->_orderModel->getTaxPercentageOnGdsProductId($product->gds_order_prod_id);
            $tax_per = $tax_per_object->tax_percentage;
            $singleUnitPrice = (($product->total / (100+$tax_per)*100) / $product->qty);
            $singleUnitPriceWithtax = (($tax_per/100) * $singleUnitPrice) + $singleUnitPrice;
            //print_r($product);die;
            

            $temp = array(
                    "product_id"=> $product_id,
                    "invoiced_qty"=> $product->invoiced_qty,
                    "return_qty"=> $return_qty,
                    "ordered_qty"=>$product->qty,
                    "singleUnitPriceWithtax"=>$singleUnitPriceWithtax,
                    "has_parent"=> $has_parent
                    );

            array_push($productsArray, $temp);
        }
        $cust_le_id = $response['data']['address']->cust_le_id;
        $cust_user_id = DB::table('users')->select("user_id")->where("legal_entity_id",$cust_le_id)->where("is_parent",1)->first();
        $cust_user_id = isset($cust_user_id->user_id)?$cust_user_id->user_id:0;
        $ecash_data= $this->_paymentModel->calculateInstantCashback($orderId,$productsArray,$cust_user_id);
        if(count($ecash_data)){
            if(isset($ecash_data['cashbackFlag']) && $ecash_data['cashbackFlag'] == 1){
                $ecash_applied = $ecash_data['walletCashBack'];
            }else{
                $ecash_applied = 0;
            }
        }else{
            $ecash_applied = 0;
        }
        if(count($returns) == 0){
            return array("status"=>200,"message"=>'Data Not Saved');
        }

        $freeData = array("customer_token"=>$token,
                        "order_id"=>$gds_order_id,
                        "flag"=>2,
                        "products_info"=>$productsArray);
        $freeDataRes = $pickObj->checkFreeQty(json_encode($freeData));
        $freeDataRes = json_decode($freeDataRes);
        if(isset($freeDataRes->status) && ($freeDataRes->status == false || $freeDataRes->status == "false")){
            if(isset($freeDataRes->data)){
                if(isset($freeDataRes->data->product)){
                    $products = $freeDataRes->data->product;
                    $message = "";
                    foreach ($products as $key => $value) {
                        # code...
                        $return_text = "return ";
                        if($value->pick == 1){
                            $return_text = "dont return ";
                        }
                        $message .= "Please ".$return_text .$value->product_title . " of qty ".$value->product_qty."\n";
                    }
                    return array("status"=>400,"message"=>$message);
                }else{
                    return array("status"=>400,"message"=>'Error while checking Free Qty Data');
                }
            }else{
                return array("status"=>400,"message"=>'Error while checking Free Qty Data');
            }
        }

        $data = [
            'flag' => 2,
            'deliver_token' => $token,
            'module_id' => 1,
            'user_id' => $userId,
            'order_id' => $orderId,
            'invoice_id' => $invoiceId,
            'net_amount' => round($invoiceAmt - $ecash_applied - $amount_return),
            'amount' =>round($invoiceAmt - $ecash_applied - $amount_return),
            'amount_collected' => round($invoiceAmt - $ecash_applied - $amount_return),
            'collectable_amt' => round($invoiceAmt - $ecash_applied - $amount_return),
            'amount_credit' => 0,
            'amount_return' => ($amount_return),
            'payment_mode' => 22010,
            'reference_no' => '--NA--',
            'round_of_value' => $invoiceAmt -round($invoiceAmt),
            'discount_applied' => 0,
            'discount_deducted' => 0,
            'ecash_applied' => $ecash_applied,
            'returns' => $returns,
            'payments' => [],
            "products_info"=>$productsArray
        ];
        $gds_temp_data = array("gds_order_id"=>$orderId,"gds_order_status_id"=>17023,"gds_order_data"=>json_encode($data));
        $this->_orderModel->insertOrderTempData($gds_temp_data);
        return array("status"=>200,"message"=>'Saved Data');
    }

    public function deliverOrderDetails(Request $request){
        $orderArray = $request->input('orderArr');

        $total_ecash=0;
        $total_collected=0;
        $collection_ids = array();
        $wh_array = array();
        $hub_array = array();

        foreach ($orderArray as $key => $value) {
            $gds_order_id = $value['gds_order_id'];
            $order_status = $value['order_status'];
            $orderId = $gds_order_id;
            $orderData = $this->_grnModel->getOrderByOrderId($orderId);
            $wh_array[$orderData->le_wh_id] = 0;
            $hub_array[$orderData->hub_id] = 0;
            if(count($wh_array) > 1 || count($hub_array) > 1){
                return array("status"=>401,"message"=>"All orders should be same HUB and DC!");
            }
        }
        foreach ($orderArray as $key => $value) {
            # 17023 -- partial
            $gds_order_id = $value['gds_order_id'];
            $order_status = $value['order_status'];

            $orderId = $gds_order_id;

            $orderData = $this->_grnModel->getOrderByOrderId($orderId);

            $wh_array[$orderData->le_wh_id] = 0;
            $hub_array[$orderData->hub_id] = 0;
            $le_wh_id = $orderData->le_wh_id;
            $hub_id = $orderData->hub_id;
            if(count($wh_array) > 1 || count($hub_array) > 1){
                return array("status"=>401,"message"=>"All orders should be same HUB and DC!");
            }
            $this->_InvoiceModel = new Invoice();
            $invoiceInfo = $this->_InvoiceModel->getInvoiceGridOrderId(array($orderId), array('grid.gds_invoice_grid_id','grid.ecash_applied'));
            $userId = \Session::get('userId');
            $token = $this->_orderModel->getTokenByUserid($userId);

            if(!isset($invoiceInfo[0]->gds_invoice_grid_id)){
                $response = array('status' => 400, 'message' => 'Order not yet Invoiced!');
                return json_encode($response);
            }
            $invoiceId = $invoiceInfo[0]->gds_invoice_grid_id;
            $ecash_applied = ($invoiceInfo[0]->ecash_applied>0) ? $invoiceInfo[0]->ecash_applied : 0;
            $invoiceAmt = $this->_InvoiceModel->getInvoicedPriceWithOrderID($orderId);
            $pickObj = new pickController();
            $productsArray = array();
            if($order_status == 17023){
                
                $orderData = $this->_orderModel->getOrderTempData($gds_order_id);
                if(isset($orderData->gds_order_data)){
                    $deliverdata = json_decode($orderData->gds_order_data,true);
                    $invoiedata = $pickObj->getInvoiceByReturn($deliverdata);
                    $response = json_decode($invoiedata);
                    $amount_collected = $deliverdata['amount_collected'];
                    $amount_return = $deliverdata['amount_return'];
                    $ecash_applied = $deliverdata['ecash_applied'];
                    if($response->status == "success"){
                        $orderData = $this->_grnModel->getOrderByOrderId($orderId);
                        $collections = $this->_grnModel->collectionDetailsById($orderId);
                        $le_wh_id = $orderData->le_wh_id;
                        $hub_id = $orderData->hub_id;
                        array_push($collection_ids,$collections->collection_id);
                        $total_ecash += $ecash_applied;
                        $total_collected +=$invoiceAmt - $amount_return;
                        
                    }
                    

                }

            }else if($order_status == 17022){

                $data= array('picker_token'=>$token,"invoice_id"=>$invoiceId,"module_id"=>1);
                $response = $pickObj->getorderdetailbyinvoice($data);
                $products = $response['data']['products'];
                $returns = array();
                foreach ($products as $key => $value) {

                    $has_parent = $this->_orderModel->isFreebie($value->product_id);
                    $temp = array(
                        "product_id"=> $value->product_id,
                        "return_qty"=> $value->invoiced_qty,
                        "delivered_qty"=> 0,
                        "return_reason"=> 59001,
                        "has_parent"=> $has_parent
                        );
                    array_push($returns, $temp);
                    $temp = array(
                        "product_id"=> $value->product_id,
                        "invoiced_qty"=> $value->invoiced_qty,
                        "return_qty"=> $value->invoiced_qty,
                        "ordered_qty"=>$value->ordered_qty,
                        "singleUnitPriceWithtax"=>$value->singleUnitPriceWithtax,
                        "return_reason"=> 59001,
                        "has_parent"=> $has_parent
                        );
                    array_push($productsArray, $temp);
                }

            }else{
                $returns = array();
                $pdata= array('picker_token'=>$token,"invoice_id"=>$invoiceId,"module_id"=>1);
                $response = $pickObj->getorderdetailbyinvoice($pdata);
                $products = $response['data']['products'];
                foreach ($products as $key => $value) {

                    $has_parent = $this->_orderModel->isFreebie($value->product_id);
                    $temp = array(
                        "product_id"=> $value->product_id,
                        "invoiced_qty"=> $value->invoiced_qty,
                        "return_qty"=> 0,
                        "ordered_qty"=>$value->ordered_qty,
                        "singleUnitPriceWithtax"=>$value->singleUnitPriceWithtax,
                        "return_reason"=> 59001,
                        "has_parent"=> $has_parent
                        );
                    array_push($productsArray, $temp);
                }
            }

            if($order_status != 17023){
                $amount_return = 0;
                if($order_status == 17022){
                    $amount_return = 0.1;

                }

                    
                $data = [
                    'flag' => 2,
                    'deliver_token' => $token,
                    'module_id' => 1,
                    'user_id' => $userId,
                    'order_id' => $orderId,
                    'invoice_id' => $invoiceId,
                    'net_amount' => round($invoiceAmt - $ecash_applied),
                    'amount' => round($invoiceAmt - $ecash_applied),
                    'amount_collected' => round($invoiceAmt - $ecash_applied),
                    'amount_credit' => 0,
                    'collectable_amt' => round($invoiceAmt - $ecash_applied),
                    'amount_return' => round($amount_return,2),
                    'payment_mode' => 22010,
                    'reference_no' => '--NA--',
                    'round_of_value' => $invoiceAmt - round($invoiceAmt),
                    'discount_applied' => 0,
                    'discount_deducted' => 0,
                    'ecash_applied' => $ecash_applied,
                    'returns' => $returns,
                    'payments' => [],
                    'products_info'=>$productsArray
                ];
                
                if($order_status == 17022){
                    $data['amount_collected']=0;
                }
                $invoiedata = $pickObj->getInvoiceByReturn($data);

                $response = json_decode($invoiedata);
                if($response->status == "success"){
                    $orderData = $this->_grnModel->getOrderByOrderId($orderId);
                    $collections = $this->_grnModel->collectionDetailsById($orderId);
                    $le_wh_id = $orderData->le_wh_id;
                    $hub_id = $orderData->hub_id;
                    if($order_status !=17022){
                        $total_collected += $invoiceAmt - $amount_return; 
                        $total_ecash += $ecash_applied;
                    }
                    array_push($collection_ids,$collections->collection_id);
                }

            }


        }

        if(count($collection_ids)){
            $whdetails =$this->_roleRepo->getLEWHDetailsById($le_wh_id);
            $statecode  = isset($whdetails->state_code)?$whdetails->state_code:"TS";
            $remittance_code = Utility::getReferenceCode('RM',$statecode);
            // Log::info("remittance code".$remittance_code);
            $colremArray = array("collected_amt"=>round($total_collected),
                                        "remittance_code"=>$remittance_code,
                                        "acknowledged_by"=>$userId,
                                        "hub_id"=>$hub_id,
                                        "le_wh_id"=>$le_wh_id,
                                        "by_ecash"=>$total_ecash,
                                        "by_cash"=>round($total_collected)-$total_ecash,
                                        "by_cheque"=>0,
                                        "by_online"=>0, 
                                        "by_upi"=>0, 
                                        "by_pos"=>0,
                                        "submitted_at"=>date("Y-m-d h:i:s"),
                                        "submitted_by"=>$userId,
                                        "acknowledged_at"=>date("Y-m-d h:i:s"),
                                        "approval_status"=>57052);
            $collectionRemittanceMappingId = $this->_grnModel->collectionRemittanceMapping($colremArray);
        }
        $remarray = array();
        foreach ($collection_ids as $key => $value) {
            $remarray = array('collection_id' =>  $value, 
                            'remittance_id' => $collectionRemittanceMappingId);
            $this->_gdsOrders->insertMapping($remarray);
        }

        return array("status"=>200,"message"=>"All orders Delivered");


    }

    public function setBuidInSession(){
            $data=Input::all();
            Session::set('business_unitid', $data['buid']);
            return Session::get('business_unitid');
    }

    public function getBuidsByUserId($userid){
            try{
                $buids=DB::table('user_permssion')
                           ->select(DB::raw("GROUP_CONCAT(object_id) as object_id"))
                           ->where('user_id',$userid)
                           ->where('permission_level_id',6)
                           ->get()->all();
                 $buids=isset($buids[0]->object_id)?$buids[0]->object_id:'';
                 return $buids;
                }catch (\ErrorException $ex) {
                Log::error($ex->getMessage());
                Log::error($ex->getTraceAsString());
            }
        }

    public function reassignOrders(Request $request){
        $data = $request->input();
        $this->_AssignOrderModel = new AssignOrderModel();
        $result = $this->_AssignOrderModel->assignOrdersForCheckerModal($data['orders'],$data['checkerBy'],$data['date']);
        return $result;
    }

    public function getCenterList($centerTypeId, $all_access) {

        try {
            $current_user_id = Session::get('userId');
            $bu_access_list = DB::table('user_permssion')
                        ->where(['user_id' => $current_user_id, 'permission_level_id' => 6])
                        ->groupBy('object_id')
                        ->pluck('object_id')->all();

            switch ($centerTypeId) {
                case '1':
                    # DC list
                    $query = "SELECT bu_id,CONCAT(lp_wh_name,' ','(',le_wh_code,')') AS 'name' FROM legalentity_warehouses WHERE legal_entity_id IN(SELECT legal_entity_id FROM legal_entities WHERE legal_entity_type_id IN(1016)) and status=1  and dc_type IN(118001)";

                    if (!in_array(0,$bu_access_list)) {
                        $bu_ids = implode(",", $bu_access_list);
                        $query .=" and bu_id IN(".$bu_ids.")";
                    }

                    $results = DB::select(DB::raw($query));
                    $html = ($all_access) ? '<option value="0">ALL</option>' : '';
                    foreach ($results as $item) {
                        $html.='<option value='.$item->bu_id.'>'.$item->name.'</option>';
                    }
                    return $html;
                    break;

                case '2':
                    # FC list
                    $query = "SELECT bu_id,CONCAT(lp_wh_name,' ','(',le_wh_code,')') AS 'name' FROM legalentity_warehouses WHERE legal_entity_id IN(SELECT legal_entity_id FROM legal_entities WHERE legal_entity_type_id IN(1014)) and status=1  and dc_type IN(118001)";

                    if (!in_array(0,$bu_access_list)) {
                        $bu_ids = implode(",", $bu_access_list);
                        $query .=" and bu_id IN(".$bu_ids.")";
                    }

                    $results = DB::select(DB::raw($query));
                    $html = ($all_access) ? '<option value="0">ALL</option>' : '';

                    foreach ($results as $item) {
                        $html.='<option value='.$item->bu_id.'>'.$item->name.'</option>';
                    }
                    return $html;
                    break;

                case '3':
                    # FF list
                    $results = DB::select(DB::raw("CALL getFfByUserAccess('".$current_user_id."')"));
                    $html = ($all_access) ? '<option value="0">ALL</option>' : '';

                    foreach ($results as $item) {
                        $html.='<option value='.$item->user_id.'>'.$item->NAME.'</option>';
                    }
                    
                    return $html;
                    break;
                
                default:
                    return "";
                    break;
            }            
        }           
            catch(Exception $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
            return Response::json(array('status'=>400, 'message'=>'Failed'));
        }
    }     
}
