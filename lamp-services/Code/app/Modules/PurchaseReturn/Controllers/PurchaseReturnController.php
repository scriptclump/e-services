<?php

/*
 * Filename: PurchaseReturnController.php
 * Description: This file is used for manage purchase returns
 * Author: Ebutor <info@ebutor.com>
 * Copyright: ebutor@2016
 * Version: v1.0
 * Created date: 30 August 2016
 * Modified date: 30 August 2016
 */

/*
 * PurchaseReturnController is used to manage purchase returns
 * @author		Ebutor <info@ebutor.com>
 * @copyright	ebutor@2016
 * @package		PR
 * @version: 	v1.0
 */

namespace App\Modules\PurchaseReturn\Controllers;

use App\Http\Controllers\BaseController;
use Session;
use View;
use Illuminate\Http\Request;
use Response;
use Log;
use DB;
use Auth;
use Input;
use PDF;
use Lang;
use Illuminate\Support\Facades\Redirect;
use App\Modules\PurchaseReturn\Models\PurchaseReturn;
use App\Modules\PurchaseOrder\Models\PurchaseOrder;
use App\Modules\Orders\Models\MasterLookup;
use App\Modules\Indent\Models\IndentModel;
use App\Modules\Grn\Models\Inward;
use Illuminate\Support\Facades\Route;
use App\Central\Repositories\RoleRepo;
use App\Modules\Indent\Models\LegalEntity;
use App\Central\Repositories\ProductRepo;
use App\Modules\Orders\Models\OrderModel;
use Utility;
use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;
use Notifications;
use App\Modules\Notifications\Models\NotificationsModel;
use App\Central\Repositories\CustomerRepo;
use Carbon\Carbon;
use Excel;
use Config;
use File;
class PurchaseReturnController extends BaseController {

    protected $_poModel;
    protected $_prModel;
    protected $_masterLookup;
    protected $_indent;
    protected $_roleRepo;
    protected $_LegalEntity;
    protected $_productRepo;
    protected $_inwardModel;
    protected $_orderModel;

    /*
     * __construct() method is used to call model
     * @param Null
     * @return Null
     */

    public function __construct() {
        $this->middleware(function ($request, $next) {
            if (!Session::has('userId')) {
                Redirect::to('/login')->send();
            }
            return $next($request);
        });
        parent::Title('Purchase Returns - '.Lang::get('headings.Company'));
        $this->_poModel = new PurchaseOrder();
        $this->_prModel = new PurchaseReturn();
        $this->_masterLookup = new MasterLookup();
        $this->_indent = new IndentModel();
        $this->_roleRepo = new RoleRepo();
        $this->_LegalEntity = new LegalEntity();
        $this->_productRepo = new ProductRepo();
        $this->_inwardModel = new Inward();
        $this->_orderModel = new OrderModel();
        $this->_filterStatus = ['initiated'=>57036,'picklist'=>57037,'rtd'=>57139,'verification'=>57140,'dispatch'=>57136,'finance'=>57137,'cancelled'=>57138,'completed'=>1];
        
        $this->produc_grid_field_db_match = array(
            'prId' => 'pr_code',
            'inwardCode'   => 'inward_code',
            'Supplier' => 'business_legal_name',
            'shipTo' => 'lp_wh_name',
            'prValue' => 'pr_grand_total',
            'createdBy' => 'user_name',
            'picker_name' => 'picker_name',
            'createdOn' => 'created_at',
            'Status' => 'approval_status_name',
        );
    }

    /*
     * indexAction() method is used to list of purchase returns
     * @param Null
     * @return String
     */

    public function indexAction($status=null) {

        try {

            $hasAccess = $this->_roleRepo->checkPermissionByFeatureCode('PR001');
            if ($hasAccess == false) {
                return View::make('Indent::error');
            }

            $legalEntityId = Session::get('legal_entity_id');
            $roleId = Session::get('roles');
            
            $suppliers = $this->_poModel->getSupplierByLEId($legalEntityId);
            $allPRCountArr = $this->_prModel->getPrCountByStatus($legalEntityId);
            $allPRCountArr = json_decode(json_encode($allPRCountArr),true);
            $initiated = isset($allPRCountArr[0]['Initiated']) ? (int) $allPRCountArr[0]['Initiated'] : 0;
            $created = isset($allPRCountArr[0]['Created']) ? (int) $allPRCountArr[0]['Created'] : 0;
            $picklist = isset($allPRCountArr[0]['Picklist']) ? (int) $allPRCountArr[0]['Picklist'] : 0;
            $RTD = isset($allPRCountArr[0]['RTD']) ? (int) $allPRCountArr[0]['RTD'] : 0;
            $verified = isset($allPRCountArr[0]['Verified']) ? (int) $allPRCountArr[0]['Verified'] : 0;
            $dispatched = isset($allPRCountArr[0]['Dispatched']) ? (int) $allPRCountArr[0]['Dispatched'] : 0;
            $canceled = isset($allPRCountArr[0]['Cancelled']) ? (int) $allPRCountArr[0]['Cancelled'] : 0;
            $completed = isset($allPRCountArr[0]['Completed']) ? (int) $allPRCountArr[0]['Completed'] : 0;
            $poCounts = array(
                                        'Total'=>array_sum($allPRCountArr[0]),                                        
                                        'initiated'=>$initiated,
                                        'created'=>$created,
                                        'picklist'=>$picklist,
                                        'RTD'=>$RTD,
                                        'verified'=>$verified,
                                        'dispatched'=>$dispatched,
                                        'canceled'=>$canceled,
                                        'completed'=>$completed,
                                        );
            //$pickerUsers = $this->_orderModel->getUsersByRoleName(array('Picker'));
            $pickerUsers=$this->_roleRepo->getUsersByFeatureCode('PICKR002');
            $createFeature = $this->_roleRepo->checkPermissionByFeatureCode('PR002');
            $assignPickerFeature = $this->_roleRepo->checkPermissionByFeatureCode('PR007');
            $printPicklistFeature = $this->_roleRepo->checkPermissionByFeatureCode('PR008');
            $exportFeature = $this->_roleRepo->checkPermissionByFeatureCode('PR009');
            $featureAccess = array('createPrFeature'=>$createFeature,
                                    'assignPickerFeature'=>$assignPickerFeature,
                                    'printPicklistFeature'=>$printPicklistFeature,
                                    'exportFeature'=>$exportFeature);
            $filter_status = empty($status) ? 'total' : $status;
            return view::make('PurchaseReturn::index')
                            ->with('filter_status', $filter_status)
                            ->with('poCounts', $poCounts)
                            ->with('suppliers', $suppliers)
                            ->with('featureAccess', $featureAccess)
                            ->with('pickerUsers',$pickerUsers);
        } catch (Exception $e) {
            
        }
    }

    /**
     * [printPrAction description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function printPrAction($id) {

        try {

            $hasAccess = $this->_roleRepo->checkPermissionByFeatureCode('PR004');
            if ($hasAccess == false) {
                return View::make('Indent::error');
            }

            $prDetailArr = $this->_prModel->getPrDetailById($id);
            if (count($prDetailArr) == 0) {
                Redirect::to('/pr/index')->send();
                die();
            }

            $leWhId = isset($prDetailArr[0]->le_wh_id) ? $prDetailArr[0]->le_wh_id : 0;
            $leId = isset($prDetailArr[0]->legal_entity_id) ? $prDetailArr[0]->legal_entity_id : 0;
            $leParentId = $this->_LegalEntity->getLeParentIdByLeId($leId);
            $leDetail = $this->_LegalEntity->getLegalEntityById($leParentId);
            $whDetail = $this->_LegalEntity->getWarehouseById($leWhId);
            if($leParentId)
                $leDetail = $this->_LegalEntity->getLegalEntityById($leParentId);
            else{
                $leDetail = $this->_LegalEntity->getLegalEntityById($whDetail->legal_entity_id);
            }
            $userInfo = $this->_poModel->getUserByLeId($leId);
            $supplierInfo = $this->_poModel->getLegalEntityById($leId);
            $taxBreakup = $this->getTaxBreakup($prDetailArr);
            $packTypes = $this->_masterLookup->getAllOrderStatus('Levels');
            return view('PurchaseReturn::printpr')->with('productArr', $prDetailArr)
                            ->with('supplier', $supplierInfo)
                            ->with('leDetail', $leDetail)
                            ->with('whDetail', $whDetail)
                            ->with('taxBreakup', $taxBreakup)
                            ->with('userInfo', $userInfo)
                            ->with('packTypes', $packTypes);
        } catch (Exception $e) {
            
        }
    }

    /**
     * [downloadPOAction description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function downloadPRAction($id, $forEmail = 0) {
        try {

            $hasAccess = $this->_roleRepo->checkPermissionByFeatureCode('PR005');
            if ($hasAccess == false && $forEmail == 0) {
                return View::make('Indent::error');
            }
            $prDetailArr = $this->_prModel->getPrDetailById($id);
            if (count($prDetailArr) == 0) {
                Redirect::to('/pr/index')->send();
                die();
            }
            $leWhId = isset($prDetailArr[0]->le_wh_id) ? $prDetailArr[0]->le_wh_id : 0;
            $leId = isset($prDetailArr[0]->legal_entity_id) ? $prDetailArr[0]->legal_entity_id : 0;

            $leParentId = $this->_LegalEntity->getLeParentIdByLeId($leId);
            $leDetail = $this->_LegalEntity->getLegalEntityById($leParentId);
            $whDetail = $this->_LegalEntity->getWarehouseById($leWhId);
            if($leParentId)
                $leDetail = $this->_LegalEntity->getLegalEntityById($leParentId);
            else{
                $leDetail = $this->_LegalEntity->getLegalEntityById($whDetail->legal_entity_id);
            }
            $userInfo = $this->_poModel->getUserByLeId($leId);
            $supplierInfo = $this->_poModel->getLegalEntityById($leId);

            $taxBreakup = $this->getTaxBreakup($prDetailArr);
            $data = array('supplier' => $supplierInfo, 'productArr' => $prDetailArr, 'taxBreakup' => $taxBreakup
                , 'whDetail' => $whDetail,'leDetail'=>$leDetail, 'userInfo' => $userInfo);
            $pdf = PDF::loadView('PurchaseReturn::downloadPR', $data);
            return $pdf->download('pr_' . $id . '.pdf');
        } catch (Exception $e) {
            
        }
    }

    public function createAction() {
        try {
            $hasAccess = $this->_roleRepo->checkPermissionByFeatureCode('PR002');
            if ($hasAccess == false) {
                return View::make('Indent::error');
            }
            Session::put('prdocs', array());
            //$activeInwards = $this->_inwardModel->getActiveInwards();
            //$activeInwards = json_decode(json_encode($activeInwards), true);
            $reasonsArr = $this->_prModel->getRemarkReasons(0);
            return view('PurchaseReturn::create')
                        //->with('activeInwards', $activeInwards)
                        ->with('reasonsArr', $reasonsArr);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage() . ' ' . $ex->getTraceAsString());
        }
    }

    public function getSuppliers() {
        try {

            $supplierData = [];
            $data = \Input::all();
            $inwardId = isset($data['inward_id']) ? $data['inward_id'] : 0;
            $supplierData = $this->_prModel->getSuppliers($inwardId);
            //echo '<pre/>';print_r($supplierData);die;
            $response['supplierList'] = [];
            $legal_entity_id = \Session::get('legal_entity_id');
            $supOptions = '<option value="">Select Supplier</option>';
            $warehouseOptions = '<option value="">Select Dispatch Location</option>';
            $supOptions = $supplierData['supplierList'];
            $warehouseOptions = $supplierData['warehouseList'];
            $products = $supplierData['products'];
            $productList = view('PurchaseReturn::Form.inwardDetail')->with('products', $products)->render();
            $response['supplierList'] = $supOptions;
            $response['warehouseList'] = $warehouseOptions;
            $response['productList'] = $productList;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage() . ' ' . $ex->getTraceAsString());
        }
        return json_encode($response);
    }

    public function getWarehouseBySupplierId() {
        try {
            $data = \Input::all();
            $supplierId = $data['supplier_id'];
            $warehouseList = $this->_LegalEntity->getWarehouseBySupplierId($supplierId);
            $warehouseOptions = '<option value="">Select Dispatch Location</option>';
            foreach ($warehouseList as $warehouse) {
                $warehouseOptions .= '<option value=' . $warehouse->le_wh_id . '>' . $warehouse->lp_wh_name . '</option>';
            }
            return json_encode(array('warehouses' => $warehouseOptions));
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage() . ' ' . $ex->getTraceAsString());
        }
    }

    /*
     * savePurchaseReturnAction() method is used to save the purchase return information
     * @param Null
     * @return String
     */

    public function savePurchaseReturnAction() {
        try {
            $data = Input::all();
            #print_r($data);die;
            if (empty($data)) {
                return json_encode(array('status' => 400, 'message' => 'Please input valid data.', 'pr_id' => ''));
            } else if (empty($data['supplier_list']) || empty($data['warehouse_list'])) {
                return json_encode(array('status' => 400, 'message' => 'Please select warehouse and supplier.', 'pr_id' => ''));
            } else if (!isset($data['pr_product_id'])) {
                return json_encode(array('status' => 400, 'message' => 'Please select products', 'pr_id' => ''));
            } else {
                $saveData = $this->_prModel->savePurchaseReturnData($data);
                $serialNumber = $saveData['serialNumber'];
                $prId = $saveData['pr_id'];
                /**
                * default approval status
                */
                if($prId!='' && $prId>0){
                    $approval_flow_func = new CommonApprovalFlowFunctionModel();
                    $created_by = (isset($data['created_by']) && $data['created_by']!='')?$data['created_by']:\Session::get('userId');
                    $res_approval_flow_func = $approval_flow_func->getApprovalFlowDetails('Purchase Return', '57036', $created_by);
                    if (isset($res_approval_flow_func["currentStatusId"]) && isset($res_approval_flow_func["data"])) {
                        $current_status_id = $res_approval_flow_func["currentStatusId"];
                        $next_status_id = $res_approval_flow_func["data"][0]["nextStatusId"];
                        $table = 'purchase_returns';
                        $unique_column = 'pr_id';
                        $this->_prModel->updateStatusAWF($table, $unique_column, $prId, $next_status_id . ",0");
                        $appr_comment = (isset($data['approval_comments']))?$data['approval_comments']:'System approval at the time of insertion';
                        $approval_flow_func->storeWorkFlowHistory('Purchase Return', $prId, $current_status_id, $next_status_id, $appr_comment, $created_by);
                    }

                    Notifications::addNotification(['note_code' => 'PR001','note_message'=>'PR #PRID Created Successfully', 'note_priority' => 1, 'note_type' => 1, 'note_params' => ['PRID' => $serialNumber], 'note_link' => '/pr/details/'.$prId]);
                    $mailData['subject'] = 'New PR#'.$serialNumber.' Created';
                    $mailData['message'] = '<p>New PR created!</p>';
                    $subject = $mailData['subject'];//;
                    $this->emailWithAttachment($prId,$serialNumber,$mailData);
                    $this->prDocUpdate($prId);
                }
                return json_encode($saveData);
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage() . ' ' . $ex->getTraceAsString());
        }
    }
    public function emailWithAttachment($prId,$pr_code,$mailData) {
        
        try{            
            $purchaseOrder = new PurchaseOrder();
            $instance = env('MAIL_ENV');
            $subject = $instance.$mailData['subject'];
            $body['attachment'] = array('nameSpace' => '\App\Modules\PurchaseReturn\Controllers\PurchaseReturnController','functionName'=>'downloadPRAction','args'=>array($prId,1));
            $body['file_name'] = 'PR_'.$pr_code.'.pdf';
            $body['template'] = 'emails.po';
            $body['name'] = 'Hello All';
            $body['comment'] = $mailData['message'];
            
            $notificationObj= new NotificationsModel();
            $userIdData= $notificationObj->getUsersByCode('PRN001');
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

    /*
     * ajaxAction() method is used to get all purchase order for grid
     * @param $status String, by default null
     * @return JSON
     */

    public function ajaxAction($status = null,$inward_id=null, Request $request) {

        try {
            $status = empty($status) ? 'total' : $status;
            $allStatusArr = $this->_masterLookup->getAllOrderStatus('PURCHASE RETURN');
            $filters = array();
            $filterData = $request->all();
            if (isset($filterData['$filter'])) {
                $filters = $this->filterData($filterData['$filter']);
            }
            if(array_key_exists($status, $this->_filterStatus)) {
                $filters['pr_status_id'] = $this->_filterStatus[$status];
            }
            if(isset($inward_id) && $inward_id>0) {
                $filters['inward_id'] = $inward_id;
            }
            //echo '<pre/>';print_r($filters);
            //die;
            /*
             * for paging
             */
            $offset = (int) $request->input('page');
            $perpage = $request->input('pageSize');
            $orderby_array = "";
            if ($request->input('$orderby')) {  
                //checking for sorting
                $order = explode(' ', $request->input('$orderby'));
                $order_query_field = $order[0]; //on which field sorting need to be done
                $order_query_type = $order[1]; //sort type asc or desc
                $order_by_type = 'desc';

                if ($order_query_type == 'asc') {
                    $order_by_type = 'asc';
                }

                if (isset($this->produc_grid_field_db_match[$order_query_field])) { //getting appropriate table field based on grid field
                    $order_by = $this->produc_grid_field_db_match[$order_query_field];
                }

                $orderby_array = $order_by . " " . $order_by_type;
            }
            
            $prDataArr = $this->_prModel->getAllPurchasedReturns($filters, 0, $orderby_array,$offset, $perpage);
            $totalPurchageReturns = $this->_prModel->getAllPurchasedReturns($filters, 1);

            $dataArr = array();
            if (count($prDataArr)) {
                foreach ($prDataArr as $pr) {
                    $shipTo = $pr->lp_wh_name;
                    $pr_status = isset($pr->approval_status_name) ? $pr->approval_status_name : '';
                    
                    $detailFeature = $this->_roleRepo->checkPermissionByFeatureCode('PR003');
                    $printFeature = $this->_roleRepo->checkPermissionByFeatureCode('PR004');
                    $downloadFeature = $this->_roleRepo->checkPermissionByFeatureCode('PR005');
                    $editFeature = $this->_roleRepo->checkPermissionByFeatureCode('PR006');
                    $actions = '';
                    if($detailFeature){
                        $actions.='<a class=""  href="/pr/details/' . $pr->pr_id . '"> <i class="fa fa-eye"></i></a>&nbsp;';
                    }                                        
                    if($printFeature){
                        $actions.= '<a target="_blank" class="" href="/pr/printpr/' . $pr->pr_id . '"> <i class="fa fa-print"></i></a>&nbsp;';
                    }
                    if($downloadFeature){
                        $actions.= '<a class="" href="/pr/downloadpr/' . $pr->pr_id . '"> <i class="fa fa-download"></i></a>&nbsp;';
                    }
                    if($pr->pr_status == '103001' && $editFeature){
                        $actions.= '<a class="" href="/pr/edit/'.$pr->pr_id.'"> <i class="fa fa-pencil"></i></a>&nbsp;';
                    }
                    
                    $chkDisabled = '<input type="checkbox" name="chk[]" value="'.$pr->pr_id.'"><input type="hidden" id="'.$pr->pr_id.'" name="orderStatus[]" value="'.$pr->pr_status.'">';
                    $dataArr[] = array(
                        'chk' => $chkDisabled,
                        'inwardCode' => $pr->inward_code,
                        'prId' => $pr->pr_code,
                        'invoiceId' => $pr->sr_invoice_code,
                        'Supplier' => $pr->business_legal_name,
                        'picker_name' => $pr->picker_name,
                        'shipTo' => $shipTo,
                        'prValue' => $pr->prValue,
                        'createdBy' => $pr->user_name,
                        'createdOn' => $pr->created_at,
                        'Status' => $pr_status,
                        'Actions' => $actions);
                }
                return Response::json(array('data' => $dataArr, 'totalPurchageReturns' => $totalPurchageReturns));
            } else {
                return Response::json(array('data' => array(), 'totalPurchageReturns' => 0));
            }
        } catch (Exception $e) {
            return Response::json(array('data' => array(), 'totalPurchageReturns' => 0));
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
            //print_r($filter);die;
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

                    if (substr_count($data, 'prId') && !array_key_exists('prId', $filterDataArr)) {
                        $prId = explode('ge', $data);
                        $pos = strpos($data, 'eq');
                        $prIdValArr = str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'prId','eq '), '', $prId[0]);
                        $value = ($pos>0) ? trim($prIdValArr,' ') : '%'.trim($prIdValArr,' ').'%';
                        $operator = ($pos>0) ? '=' : 'LIKE';
                        $filterDataArr['pr_code'] = array('operator' => $operator, 'value' => $value);
                    }
                    if (substr_count($data, 'invoiceId') && !array_key_exists('invoiceId', $filterDataArr)) {
                        $invoiceId = explode('ge', $data);
                        $pos = strpos($data, 'eq');
                        $invoiceIdValArr = str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'invoiceId','eq '), '', $invoiceId[0]);
                        $value = ($pos>0) ? trim($invoiceIdValArr,' ') : '%'.trim($invoiceIdValArr,' ').'%';
                        $operator = ($pos>0) ? '=' : 'LIKE';
                        $filterDataArr['sr_invoice_code'] = array('operator' => $operator, 'value' => $value);
                    }
                    if (substr_count($data, 'inwardCode') && !array_key_exists('inwardCode', $filterDataArr)) {
                        $grnId = explode('ge', $data);
                        $pos = strpos($data, 'eq');
                        $suppcodeValArr = str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'inwardCode','eq '), '', $grnId[0]);
                        $value = ($pos>0) ? trim($suppcodeValArr,' ') : '%'.trim($suppcodeValArr,' ').'%';
                        $operator = ($pos>0) ? '=' : 'LIKE';
                        $filterDataArr['inwardCode'] = array('operator' => $operator, 'value' => $value);
                    }
                    if (substr_count($data, 'Supplier') && !array_key_exists('Supplier', $filterDataArr)) {
                        $sup = explode('ge', $data);
                        $pos = strpos($data, 'eq');
                        $suppValArr = str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'Supplier','eq '), '', $sup[0]);
                        $value = ($pos>0) ? trim($suppValArr,' ') : '%'.trim($suppValArr,' ').'%';
                        $operator = ($pos>0) ? '=' : 'LIKE';
                        $filterDataArr['Supplier'] = array('operator' => $operator, 'value' => $value);
                    }
                    if (substr_count($data, 'picker_name') && !array_key_exists('picker_name', $filterDataArr)) {
                        $sup = explode('ge', $data);
                        $pos = strpos($data, 'eq');
                        $suppValArr = str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'picker_name','eq '), '', $sup[0]);
                        $value = ($pos>0) ? trim($suppValArr,' ') : '%'.trim($suppValArr,' ').'%';
                        $operator = ($pos>0) ? '=' : 'LIKE';
                        $filterDataArr['picker_name'] = array('operator' => $operator, 'value' => trim($value, ' '));
                    }
                    if (substr_count($data, 'shipTo') && !array_key_exists('shipTo', $filterDataArr)) {
                        $shipTo = explode('ge', $data);
                        $pos = strpos($data, 'eq');
                        $shipToValArr = str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'shipTo','eq '), '', $shipTo[0]);
                        $value = ($pos>0) ? trim($shipToValArr,' ') : '%'.trim($shipToValArr,' ').'%';
                        $operator = ($pos>0) ? '=' : 'LIKE';
                        $filterDataArr['shipTo'] = array('operator' => $operator, 'value' => $value);
                    }
                    if (substr_count($data, 'prValue') && !array_key_exists('prValue', $filterDataArr)) {
                        $prValueValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'prValue'), '', $data));
                        $filterDataArr['prValue']['operator'] = $this->getCondOperator($prValueValArr[1]);
                        $filterDataArr['prValue']['value'] = $prValueValArr[2];
                    }

                    if (substr_count($data, 'Status') && !array_key_exists('Status', $filterDataArr)) {
                        $status = explode('ge', $data);
                        $pos = strpos($data, 'eq');
                        $poStatusValArr = str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'Status','eq '), '', $status[0]);
                        $value = ($pos>0) ? trim($poStatusValArr,' ') : '%'.trim($poStatusValArr,' ').'%';
                        $operator = ($pos>0) ? '=' : 'LIKE';
                        $filterDataArr['Status'] = array('operator' => $operator, 'value' => $value);
                    }
                    if (substr_count($data, 'approval_status') && !array_key_exists('approval_status', $filterDataArr)) {
                        $status = explode('ge', $data);
                        $pos = strpos($data, 'eq');
                        $poStatusValArr = str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'approval_status','eq '), '', $status[0]);
                        $value = ($pos>0) ? trim($poStatusValArr,' ') : '%'.trim($poStatusValArr,' ').'%';
                        $operator = ($pos>0) ? '=' : 'LIKE';
                        $filterDataArr['approval_status'] = array('operator' => $operator, 'value' => $value);
                    }
                    if (substr_count($data, 'createdBy') && !array_key_exists('createdBy', $filterDataArr)) {
                        $created = explode('ge', $data);
                        $pos = strpos($data, 'eq');
                        $createdByValArr = str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'createdBy','eq '), '', $created[0]);
                        $pos = strpos($data, 'eq');
                        $value = ($pos>0) ? trim($createdByValArr,' ') : '%'.trim($createdByValArr,' ').'%';
                        $operator = ($pos>0) ? '=' : 'LIKE';
                        $filterDataArr['createdBy'] = array('operator' => 'LIKE', 'value' => $value);
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
            try{
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
    public function detailsAction($returnId) {
        try {

            $hasAccess = $this->_roleRepo->checkPermissionByFeatureCode('PR003');
            if ($hasAccess == false) {
                return View::make('Indent::error');
            }
            Session::set('pr_id', $returnId);
            $prDetailArr = $this->_prModel->getPrDetailById($returnId);

//echo '<pre/>';//print_r($prDetailArr);die;
            if (count($prDetailArr) == 0) {
                Redirect::to('/pr/index')->send();
                die();
            }            
            $leWhId = isset($prDetailArr[0]->le_wh_id) ? $prDetailArr[0]->le_wh_id : 0;
            $leId = isset($prDetailArr[0]->legal_entity_id) ? $prDetailArr[0]->legal_entity_id : 0;

            $whDetail = $this->_LegalEntity->getWarehouseById($leWhId);
            $userInfo = $this->_poModel->getUserByLeId($leId);
            if(empty($userInfo)){
                $userInfo['mobile_no']='';
                $userInfo['email_id']='';
                $userInfo=json_decode(json_encode($userInfo));

            }
            $supplierInfo = $this->_poModel->getLegalEntityById($leId);

            $taxBreakup = $this->getTaxBreakup($prDetailArr);
            
            /*             * * data required for Approval Workflow ** */
            $approval_flow_func = new CommonApprovalFlowFunctionModel();
            $status = (isset($prDetailArr[0]->approval_status) && $prDetailArr[0]->approval_status != 0) ? $prDetailArr[0]->approval_status : 'drafted';
            $res_approval_flow_func = $approval_flow_func->getApprovalFlowDetails('Purchase Return', $status, \Session::get('userId'));
            $approvalOptions = array();
            $approvalVal = array();
            $isApprovalFinalStep = 0;
            if (isset($res_approval_flow_func["currentStatusId"]) && isset($res_approval_flow_func["data"])) {
                foreach ($res_approval_flow_func["data"] as $options) {
                    if ($options['isFinalStep'] == 1) {
                        $isApprovalFinalStep = $options['isFinalStep'];
                    }
                    //if($options['nextStatusId']==57139 || $options['nextStatusId']==57140){                        
                    //}else{
                        $approvalOptions[$options['nextStatusId'] . ',' . $options['isFinalStep']] = $options['condition'];
                    //}
                }
            }
            $approvalOptions['57154,0,58080'] = 'Comment';
            $approvalVal = array('current_status' => $status,
                'approval_unique_id' => $returnId,
                'approval_module' => 'Purchase Return',
                'table_name' => 'purchase_returns',
                'unique_column' => 'pr_id',
                'approvalurl' => '/return/approvalSubmit',
            );
            $approvalStatus = $this->_masterLookup->getAllOrderStatus('Approval Status');
            $approvedStatus = (isset($approvalStatus[$prDetailArr[0]->approval_status])) ? $approvalStatus[$prDetailArr[0]->approval_status] : '';
            if ($prDetailArr[0]->approval_status == 1) {
                $approvedStatus = 'Approved';
            }
            $approvalHistory = $this->_poModel->getApprovalHistory('Purchase Return', $returnId);
            $prDocs = $this->_prModel->getprDocs($returnId);
            /*             * * data required for Approval Workflow** */
            $packTypes = $this->_masterLookup->getAllOrderStatus('Levels');
            return view('PurchaseReturn::details')->with('productArr', $prDetailArr)
                            ->with('supplier', $supplierInfo)
                            ->with('whDetail', $whDetail)
                            ->with('userInfo', $userInfo)
                            ->with('taxBreakup', $taxBreakup)
                            ->with('packTypes', $packTypes)
                            ->with('approvalOptions', $approvalOptions)
                            ->with('approvalVal', $approvalVal)
                            ->with('isApprovalFinalStep', $isApprovalFinalStep)
                            ->with('history', $approvalHistory)
                            ->with('prDocs',$prDocs);
        } catch (Exception $e) {
            
        }
    }
    public function getTaxBreakup($products) {
        $finalTaxArr = array();
        $gst_taxes = ['GST','IGST','CGST','SGST','UTGST'];
        if(is_array($products)) {
            $taxKey = 0;
            foreach ($products as $product) {
                $taxName = strtoupper($product->tax_type);
                if(in_array($taxName, $gst_taxes) && $product->tax_data!=''){
                    $tax_data = json_decode($product->tax_data, true);
                    foreach($tax_data as $key=>$val){
                        $cgst = isset($val['CGST'])?$val['CGST']:0;
                        $sgst = isset($val['SGST'])?$val['SGST']:0;
                        $igst = isset($val['IGST'])?$val['IGST']:0;
                        $utgst = isset($val['UTGST'])?$val['UTGST']:0;
                        $cgst_val = ($product->tax_total*$cgst)/100;
                        $sgst_val = ($product->tax_total*$sgst)/100;
                        $igst_val = ($product->tax_total*$igst)/100;
                        $utgst_val = ($product->tax_total*$utgst)/100;
                        $finalTaxArr['CGST'][] = array('tax'=>'', 'name'=>'CGST', 'tax_amt'=>$cgst_val);
                        $finalTaxArr['SGST'][] = array('tax'=>'', 'name'=>'SGST', 'tax_amt'=>$sgst_val);
                        $finalTaxArr['IGST'][] = array('tax'=>'', 'name'=>'IGST', 'tax_amt'=>$igst_val);
                        $finalTaxArr['UTGST'][] = array('tax'=>'', 'name'=>'UTGST', 'tax_amt'=>$utgst_val);
                    }
                }else{
                $taxKey = (float)$product->tax_type.'-'.$product->tax_per;
                if($taxKey != '0-0') {
                    $finalTaxArr[$taxKey][] = array('tax'=>$product->tax_per, 'name'=>$product->tax_type, 'tax_amt'=>$product->tax_total);
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

    private function getAPOBStateCodes($leWhId,$leId) {
        $states = array();
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
        $wh_state_code = isset($whDetail->state) ? $whDetail->state : 4033;
        $seller_state_code = isset($supplierInfo->state_id) ? $supplierInfo->state_id : 4033;
        $states = ['wh_state_code'=>$wh_state_code,'seller_state_code'=>$seller_state_code];
        return $states;
    }

    private function getTaxInfo($prArr) {
        $taxArr = array();
        if (is_array($prArr)) {
            $leWhId = isset($prArr[0]->le_wh_id) ? $prArr[0]->le_wh_id : 0;
            $leId = isset($prArr[0]->legal_entity_id) ? $prArr[0]->legal_entity_id : 0;
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
            $wh_state_code = isset($whDetail->state) ? $whDetail->state : 4033;
            $seller_state_code = isset($supplierInfo->state_id) ? $supplierInfo->state_id : 4033;
            foreach ($prArr as $product) {
                $prodTaxArr = $this->getProductTaxClass($product->product_id, $wh_state_code, $seller_state_code);
                $taxArr[$product->product_id] = $prodTaxArr;
            }
        }
        return $taxArr;
    }

    public function getProductTaxClass($product_id, $wh_state_code = 4033, $seller_state_code = 4033) {
        try {
            $url = env('APP_TAXAPI');
            $data['product_id'] = (int) $product_id;
            $data['buyer_state_id'] = (int) $wh_state_code;
            $data['seller_state_id'] = (int) $seller_state_code;
            $taxData = Utility::sendRequest($url,$data);
            return $taxData;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    public function getProductInfo() {
        try {
            $data = \Input::all();
            $product_id = $data['product_id'];
            $supplier_id = $data['supplier_id'];
            $warehouse_id = $data['warehouse_id'];
            $productsAdded = isset($data['products']) ? $data['products'] : array();
            $addfrom = isset($data['addfrom']) ? $data['addfrom'] : '';
            $freebieParent = $this->_poModel->getFreebieParent($product_id);
            $parent_id = (isset($freebieParent->main_prd_id)) ? $freebieParent->main_prd_id : 0;
            $productTextArr = $this->getPOProductRow($product_id, $parent_id, $supplier_id, $warehouse_id, $addfrom);
            $productList = '';
            if (is_array($productTextArr) && isset($productTextArr['status'])) {
                if ($productTextArr['status'] == 200) {
                    $productList = (isset($productTextArr['productList'])) ? $productTextArr['productList'] : '';
                    $freebieProducts = $this->_poModel->getFreebieProducts($product_id);
                    foreach ($freebieProducts as $freeproduct) {
                        if ($freeproduct->main_prd_id != $freeproduct->free_prd_id && !in_array($freeproduct->free_prd_id, $productsAdded)) {
                            $productarr = $this->_prModel->getProductInfoByID($freeproduct->free_prd_id, $supplier_id, $warehouse_id);
                            $freeProductTextArr = array();
                            if (count($productarr) > 0) {
                                if ($productarr->is_sellable == 0 && $productarr->KVI == 'Q9') {
                                    $freeProductTextArr = $this->getPOProductRow($freeproduct->free_prd_id, $product_id, $supplier_id, $warehouse_id, $addfrom);
                                }
                            }
                            if (is_array($freeProductTextArr) && isset($freeProductTextArr['status']) && $freeProductTextArr['status'] == 200) {
                                $productList.=$freeProductTextArr['productList'];
                            }
                        }
                    }
                }
                $prd_data = isset($productTextArr["prd_data"])?$productTextArr["prd_data"]:[];
                $response = array('status' => $productTextArr['status'], 'message' => $productTextArr['message'], 'productList' => $productList,"prd_data"=>$prd_data);
            } else {
                $response = array('status' => 400, 'message' => 'Something went wrong', 'productList' => $productList,"prd_data"=>[]);
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage() . ' ' . $ex->getTraceAsString());
            $response = array('status' => 400, 'message' => $ex->getMessage());
        }
        return $response;
    }

    public function getPOProductRow($product_id, $parent_id, $supplier_id, $warehouse_id, $addfrom, $indent_id = 0) {
        try {
            $product = $this->_prModel->getProductInfoByID($product_id, $supplier_id, $warehouse_id);
            if (count($product) > 0) {
                $prArr[] = (object) array('product_id' => $product_id, 'le_wh_id' => $warehouse_id, 'legal_entity_id' => $supplier_id);
                $taxArr = $this->getTaxInfo($prArr);
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
                        $free_qty = 0;
                        if ($parent_id != 0 && $product->is_sellable == 0 && $product->KVI == 'Q9') {
                            $free_qty = 0;
                        } else {
                            $parent_id = 0;
                        }
                        $defltUOMEaches = 1;//(isset($packs[0]->no_of_eaches) && $packs[0]->no_of_eaches != 0) ? $packs[0]->no_of_eaches : 1;
                        foreach ($packs as $pack) {
                            $uom .= '<option value="' . $pack->pack_id . '" data-noofeach="' . $pack->no_of_eaches . '">' . $pack->packname . '(' . $pack->no_of_eaches . ')</option>';
                        }
                        $cur_symbol = (isset($product->symbol) && $product->symbol != '') ? $product->symbol : 'Rs.';
                        $mrp = (isset($product->mrp) && $product->mrp != '') ? $product->mrp : 0;
                        $current_elp = (isset($product->dlp) && $product->dlp != '') ? $product->dlp : 0;
                        $prev_elp = (isset($product->prev_elp) && $product->prev_elp != '') ? $product->prev_elp : 0;
                        $avilInv = ($product->soh-$product->order_qty);
                        $qty = 0;
                        $dlp = $current_elp;
                        $packPrice = $dlp * $defltUOMEaches;

                        $total = $packPrice * ($qty - $free_qty);

                        $sumTax = 0;
                        $taxText = '';
                        $taxper = 0;
                        $taxname = '';
                        $hsn_code = '';
                        $tax_code = '';
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
                        }
                        $tax_data = base64_encode(json_encode($taxArr[$product_id]));
                        $base_price = ($total / (100 + $taxper)) * 100;
                        $taxAmt = $total - $base_price;                        
                        $productList = '';
                        $productList .= '<tr>';
                        $productList .= '<td><span>' . $product->sku . '</span><input type="hidden" name="pr_product_id[]" value="' . $product_id . '">
                            <input type="hidden" id="product_sku' . $product_id . '" value="' . $product->sku . '">
                                <input type="hidden" name="parent_id[]" value="' . $parent_id . '"></td>';
                        $productList .= '<td><span>' . $product->pname . '</span></td>'; //<br><strong>EAN:</strong> '.(!empty($product->upc) ? $product->upc : $product->seller_sku).'
                        $productList .= '<td>
                                        <div style="width:90px">
               <div style="float:left"> 
               <input class="form-control" size="3" type="number" id="soh_qty' . $product_id . '" min="0" value="0" name="soh_qty[]" style=" width:85px">
               <input class="form-control" type="hidden" id="packsize' . $product_id . '" min="1" value="1"></div>';
               //<div style="float:right"> <select class="form-control packsize' . $product_id . '" name="packsize[]" style="width:100px" required="required">' . $uom . '</select></div>
             $productList .= '</div></td>';
             $productList .= '<td><div style="width:90px">
               <div style="float:left"> 
               <input class="form-control" size="3" type="number" id="dit_qty' . $product_id . '" min="0" value="' . (int) $qty . '" name="dit_qty[]" style=" width:85px">
               </div>';
             $productList .= '</div></td>';
             $productList .= '<td><div style="width:90px">
               <div style="float:left"> 
               <input class="form-control" size="3" type="number" id="dnd_qty' . $product_id . '" min="0" value="0" name="dnd_qty[]" style=" width:85px">
               </div>';
             $productList .= '</div></td>';
                       /* $productList .= '<td>
                                        <div style="width:175px">
               <div style="float:left"> 
               <input class="form-control" id="freeqty' . $product_id . '" min="0" type="number" size="3" style=" width:70px  " value="' . (int) $free_qty . '" name="freeqty[]">
                   </div>
               <div style="float:right"><select class="form-control freepacksize' . $product_id . '" name="freepacksize[]" required="required" style="width:100px">' . $uom . '</select></div>
               </div>
                                        </td>'; */
                        $newproduct = '';
                        if (isset($addfrom) && $addfrom == 'edit_pr') {
                            $newproduct = '<input id="newproductadd' . $product_id . '" name="newproductadd[]" type="hidden" value="' . $product_id . '">';
                        } else {
                            
                        }
                        $productList .= '<td>' . number_format($mrp, 5) . '</td>';
                        $productList .= '<td><span id="curelptext' . $product_id . '">' . number_format($current_elp, 5) . '</span></td>';
                        $productList .= '<td class="prtypeshow">
                                                                                <div style="width:170px">
               <div style="float:left"> <input class="form-control prbaseprice" min="0" id="baseprice' . $product_id . '"  name="pr_baseprice[]" type="number" value="' . ($packPrice) . '" style="width:100px"></div>
                    <div style="float:left"><input class="pretax pretax' . $product_id . '" checked="checked" data-id="' . $product_id . '" name="pretax[' . $product_id . ']" type="checkbox" value="1" style="margin:7px 10px 0px 10px;"></div>
                    <div style="float:left"><span style="margin-top:8px; font-size:9px;">Incl.Tax </span>  </div>
                  </div>
                                        <input id="currsoh' . $product_id . '" name="currsoh[' . $product_id . ']" type="hidden" value="' . $avilInv . '">
                                        <input id="currdit' . $product_id . '" name="currdit[' . $product_id . ']" type="hidden" value="' . $product->dit_qty . '">
                                        <input id="currdnd' . $product_id . '" name="currdnd[' . $product_id . ']" type="hidden" value="' . $product->dnd_qty . '">
                                        <input id="taxname' . $product_id . '" name="pr_taxname[' . $product_id . ']" type="hidden" value="' . $taxname . '">
                                        <input id="taxdata' . $product_id . '" name="pr_taxdata[' . $product_id . ']" type="hidden" value="' . $tax_data . '">
                                        <input id="hsn_code' . $product_id . '" name="hsn_code[' . $product_id . ']" type="hidden" value="' . $hsn_code . '">
                                        <input id="tax_code' . $product_id . '" name="tax_code[' . $product_id . ']" type="hidden" class="tax_code" value="' . $tax_code . '">
                                        <input id="mrp' . $product_id . '" type="hidden" value="' . $mrp . '">
                                        <input id="unit_price' . $product_id . '" name="unit_price[' . $product_id . ']" class="unitPrice" data-product_id="' . $product_id . '" type="hidden" value="' . $dlp . '">
                                        <input id="taxper' . $product_id . '" name="pr_taxper[' . $product_id . ']" type="hidden" value="' . $taxper . '">
                                        <input name="pr_taxvalue[' . $product_id . ']" id="taxval' . $product_id . '" type="hidden" value="' . $taxAmt . '">' . $newproduct . '
                                </td>';
                        $productList .= '<td class="prtypeshow" align="right"><span id="totalPriceText' . $product_id . '">' . number_format($total, 5) . '</span></td>';
                        $productList .= '<td class="prtypeshow">' . $taxText . '</td>';
                        $productList .= '<td class="prtypeshow" align="right"><span id="taxtext' . $product_id . '">' . number_format($taxAmt, 5) . '</span></td>';
                        /*
                        $productList .= '<td class="prtypeshow" align="right">'
                                . '<div style="width:170px">'
                                .'<div style="float:left"><input class="item_disc_tax_type" id="item_disc_tax_type' . $product_id . '" name="item_disc_tax_type[]" type="checkbox" value="1">'
                                .'<span style="margin-top:8px; font-size:9px;"><strong>Incl.Tax</strong></span></div><br/>'
                                . '<div style="float:left"><input class="form-control item_discount" min="0" id="item_discount' . $product_id . '" style="width:100px;" name="item_discount[]" type="number" value="0"></div>'
                                . '<div style="float:left"><input class="item_discount_type" id="item_discount_type' . $product_id . '" name="item_discount_type[]" type="checkbox" value="1" style="margin:7px 10px 0px 10px;"></div>
                                <div style="float:left"><span style="margin-top:8px; font-size:9px;"><strong>%</strong></span>  </div>'
                                . '</div></td>';
                        $productList .= '<td class="prtypeshow" align="right">'
                                .'<input id="item_discount_amt' . $product_id . '" name="item_discount_amt[' . $product_id . ']" type="hidden">'
                                . '<span  id="item_discount_text' . $product_id . '">0</span>
                                </td>'; */
                        $productList .= '<td align="right" style="background:#fbfcfd !important;position:absolute;right: 60px;">
                                        <input class="form-control prtotprice" min="0" id="totprice' . $product_id . '" style="width:100px;" name="pr_totprice[]" type="number" value="' . ($total) . '">
                                        </td>';
                        $productList .= '<td align="center" style="background:#fbfcfd !important;width: 60px !important;"><a class="fa fa-trash-o delete_product" data-id="' . $product_id . '"></a></td>';
                        $productList .= '</tr>';
                        $prd_data  = array("mrp"=>$product->mrp,
                                        "avilInv"=>$avilInv,
                                        "dit_qty"=>$product->dit_qty,
                                        "dnd_qty"=>$product->dnd_qty,
                                        "pname"=>$product->pname);
                        $response = array('status' => 200, 'message' => 'Success', 'productList' => $productList,"prd_data"=>$prd_data);
                    } else {
                        $response = array('status' => 400, 'message' => 'Please add pack configuration', 'productList' => '');
                    }
                } else {
                    if (isset($taxArr[$product_id])) {
                        $msg = $taxArr[$product_id];
                    } else {
                        $msg = 'please check tax information could not find';
                    }
                    $response = array('status' => 400, 'message' => $msg, 'productList' => '');
                }
            } else {
                $response = array('status' => 400, 'message' => 'Product Info not found', 'productList' => '');
            }
        } catch (Exception $ex) {
            Log::error($ex->getMessage() . ' ' . $ex->getTraceAsString());
            $response = array('status' => 400, 'message' => $ex->getMessage());
        }
        return $response;
    }
    public function editAction($id) {
        try {
            $hasAccess = $this->_roleRepo->checkPermissionByFeatureCode('PR006');
            if ($hasAccess == false) {
                return View::make('PurchaseOrder::error');
            }
            $prDetailArr = $this->_prModel->getPrDetailById($id);
            if (count($prDetailArr) == 0) {
                Redirect::to('/pr/index')->send();
                die();
            }
           /* if ($prDetailArr[0]->prparentid != 0) {
                Redirect::to('/pr/index')->send();
                die();
            }*/
            Session::set('pr_id', $id);
            Session::put('prdocs', array());
            $leWhId = isset($prDetailArr[0]->le_wh_id) ? $prDetailArr[0]->le_wh_id : 0;
            $leId = isset($prDetailArr[0]->legal_entity_id) ? $prDetailArr[0]->legal_entity_id : 0;

            $whDetail = $this->_LegalEntity->getWarehouseById($leWhId);
            $userInfo = $this->_poModel->getUserByLeId($leId);
            $supplierInfo = $this->_poModel->getLegalEntityById($leId);
            $taxArr = $this->getTaxInfo($prDetailArr);
            $uom = array();
            $freeuom = array();
            $prDetailArr1 = array();
            foreach($prDetailArr as $key=>$products){
                //echo '<pre/>';print_r($products);die;
                $product_id = $products->product_id;                
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
                $prDetailArr1[] = $products;
                
                $pr_status = isset($prDetailArr[$key]->pr_status) ? $prDetailArr[$key]->pr_status : 0;
                $approval_status = isset($prDetailArr[$key]->approval_status) ? $prDetailArr[$key]->approval_status : 0;
            }
            if(count($prDetailArr1)<=0){
                Redirect::to('/pr/index')->send();
                die();
            }
            $prDocs = $this->_prModel->getprDocs($id);
            $prStatusArr = $this->_masterLookup->getAllOrderStatus('PURCHASE RETURN');
            $prStatus = isset($prStatusArr[$pr_status]) ? $prStatusArr[$pr_status] : '';
            
            $approvalStatus = $this->_masterLookup->getAllOrderStatus('Approval Status');
            $approvedStatus = (isset($approvalStatus[$approval_status])) ? $approvalStatus[$approval_status] : '';
            if ($approval_status == 1) {
                $approvedStatus = 'Approved';
            }
                      
            return view('PurchaseReturn::edit')->with('productArr', $prDetailArr1)
                            ->with('supplier', $supplierInfo)
                            ->with('whDetail', $whDetail)
                            ->with('userInfo', $userInfo)
                            ->with('uom', $uom)
                            ->with('freeuom', $freeuom)
                            ->with('taxArr', $taxArr)
                            ->with('pr_status', $prStatus)
                            ->with('prDocs', $prDocs)
                            ->with('approvedStatus', $approvedStatus);
        } catch (Exception $e) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }

    public function savePickerDetails() {
        try {
            $data = Input::all();
            Session::set('picklistdata', []);
            Session::set('picklistdata', $data['ids']);
            $picker_id = $data['pickedBy'];
            $user_id = \Session::get('userId');
            $pr_id = $data['ids'];
            return $this->_prModel->assignPickerToPR($pr_id, $user_id, $picker_id);
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    public function printPicklist() {
        try {            
            $ids = Session::get('picklistdata');
            if(!isset($ids) && !is_array($ids)){
                Redirect::to('/pr/index')->send();
                die();
            }
            $pr_details = $this->_prModel->getPicklistPRDetails($ids);
            $ledetails = $leDetail = $this->_LegalEntity->getLegalEntityById(2);
            return view::make('PurchaseReturn::picklist')
                        ->with('leDetail', $leDetail)
                        ->with('prDetails', $pr_details);
            
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    /**
     * [updatePOAction description]
     * @return [type] [description]
     */
    
    public function updatePRAction($data=array())
    {
        DB::beginTransaction();
        try{
            if(empty($data) && count($data)==0){
                $data = Input::all();
            }
            if(empty($data))
            {
                return json_encode(array('status'=>400, 'message'=>Lang::get('salesorders.errorInputData'), 'pr_id'=>''));
            }
            else if(!isset($data['pr_product_id'])) {
                return json_encode(array('status'=>400, 'message'=>'Please select products', 'pr_id'=>''));
            }
            else {
                #echo '<pre/>';print_r($data);die;
                $prId = $data['pr_id'];
                if($prId)
                {   
                    $productInfo = isset($data['pr_product_id']) ? $data['pr_product_id'] : [];
                    $supplier_id = isset($data['supplier_id']) ? $data['supplier_id'] : 0;
                    $warehouse_id = isset($data['warehouse_id']) ? $data['warehouse_id'] : 0;
                    $sr_invoice_code = isset($data['sr_invoice_code']) ? $data['sr_invoice_code'] : "";
                    if($sr_invoice_code !=""){
                        $sr_data = array("supplier_id"=>$supplier_id,
                                "sr_inv_no"=>$sr_invoice_code,
                                "pr_totprice"=>$data['pr_totprice'],
                                "pr_le_wh_id"=>$warehouse_id);
                        $sr_data = $this->_prModel->checkSrInvoice($sr_data);
                        if(isset($sr_data['status']) && $sr_data['status'] == 400){
                            return json_encode($sr_data);
                        }
                    }
                    //$packsize = $data['packsize'];
                    $mailMsg = '';
                    $bill_discount = (isset($data['bill_discount']))?$data['bill_discount']:0;
                    $bill_discount_type = (isset($data['bill_discount_type']))?$data['bill_discount_type']:0;
                    $billDiscAmt = 0;
                    $grand_total = array_sum($data['pr_totprice']);
                    if($bill_discount_type==1){
                        $billDiscAmt = ($grand_total*$bill_discount)/100;
                    }else{
                        $billDiscAmt = $bill_discount;
                    }
                    if($billDiscAmt>$grand_total){
                        return array('status'=>400, 'message'=>'Bill discount amount can not be more than total', 'pr_id'=>'','serialNumber'=>'');
                    }else{
                        $grand_total = ($grand_total - $billDiscAmt);
                    }
                        
                    if(isset($data['delete_product']) && !empty($data['delete_product']))
                    {
                        foreach($data['delete_product'] as $key=>$delproduct_id)
                        {
                            $product = $this->_prModel->getProductInfoByID($delproduct_id,$supplier_id,$warehouse_id);
                            $product = json_decode(json_encode($product),true);
                            $this->_prModel->deletePRProducts($prId,$delproduct_id);
                            $mailMsg .= '<p><strong>'.$product['pname'].' Deleted</strong></p>';
                        } 
                    }
                    if(!empty($productInfo))
                    {   
                        $updated_by = \Session::get('userId');
                        $totalQty = 0;
                        foreach($productInfo as $key=>$product_id)
                        {
                            $product = $this->_prModel->getProductInfoByID($product_id,$supplier_id,$warehouse_id);
                            $product = json_decode(json_encode($product),true);
                            $pr_product = array();
                            
                            $qty = (isset($data['soh_qty'][$key]))?$data['soh_qty'][$key]:0;                            
                            $dit_qty = (isset($data['dit_qty'][$key]))?$data['dit_qty'][$key]:0;                            
                            $dnd_qty = (isset($data['dnd_qty'][$key]))?$data['dnd_qty'][$key]:0;
                            if(isset($product['soh']) && isset($product['dit_qty']) && isset($product['dnd_qty'])){
                                $avilInv = $product['soh']-$product['order_qty'];
                                if($qty>$avilInv){
                                    DB::rollback();
                                    return json_encode(array('status'=>400, 'message'=>'SOH Qty should not be more than Current SOH for <strong>'.$product['sku'].'</strong>', 'pr_id'=>'','serialNumber'=>''));
                                }
                                if($dit_qty>$product['dit_qty']){
                                    DB::rollback();
                                    return json_encode(array('status'=>400, 'message'=>'DIT Qty should not be more than Current DIT for <strong>'.$product['sku'].'</strong>', 'pr_id'=>'','serialNumber'=>''));
                                }
                                if($dnd_qty>$product['dnd_qty']){
                                    DB::rollback();
                                    return json_encode(array('status'=>400, 'message'=>'DND Qty should not be more than Current DND for <strong>'.$product['sku'].'</strong>', 'pr_id'=>'','serialNumber'=>''));
                                }
                            }else{
                                DB::rollback();
                                return array('status'=>400, 'message'=>'Could not find inventory details', 'pr_id'=>'','serialNumber'=>'');
                            }
                            /*
                            $pack_id = (isset($packsize[$key]) && $packsize[$key]!='')?$packsize[$key]:'';
                            $uomPackinfo = $this->_poModel->getProductPackUOMInfoById($pack_id);                            
                            $no_of_eaches = (isset($uomPackinfo->no_of_eaches))?$uomPackinfo->no_of_eaches:0;
                            
                            $free_qty = (isset($data['freeqty'][$key]))?$data['freeqty'][$key]:0;                            
                            $free_pack_id=(isset($data['freepacksize'][$key]) && $data['freeqty'][$key]!=0)?$data['freepacksize'][$key]:'';
                            $freeUOMPackinfo = $this->_poModel->getProductPackUOMInfoById($free_pack_id);                            
                            $free_no_of_eaches = (isset($freeUOMPackinfo->no_of_eaches))?$freeUOMPackinfo->no_of_eaches:0;
                            */
                            $pr_product['qty'] = $qty;
                            $pr_product['dit_qty'] = $dit_qty; //damage qty
                            $pr_product['dnd_qty'] = $dnd_qty; //missing qty
                            $pr_product['no_of_eaches'] = 1;//$no_of_eaches;
                           /* $pr_product['uom'] = (isset($uomPackinfo->value))?$uomPackinfo->value:0;
                            $pr_product['free_qty'] = $free_qty;
                            $pr_product['free_eaches'] = $free_no_of_eaches;
                            $pr_product['free_uom'] = (isset($freeUOMPackinfo->value) && $data['freeqty'][$key]!=0)?$freeUOMPackinfo->value:0;
                            */
                            $pr_product['is_tax_included'] = (isset($data['pretax'][$product_id]))?$data['pretax'][$product_id]:0;
                            
                            //$pr_product['discount_inc_tax'] = (isset($data['item_disc_tax_type'][$key]))?$data['item_disc_tax_type'][$key]:0;
                            //$pr_product['discount_type'] = (isset($data['item_discount_type'][$key]))?$data['item_discount_type'][$key]:0;
                            //$pr_product['discount'] = (isset($data['item_discount'][$key]))?$data['item_discount'][$key]:0;
                            //$pr_product['discount_amt'] = (isset($data['item_discount_amt'][$product_id]))?$data['item_discount_amt'][$product_id]:0;
                            $unit_price = (isset($data['unit_price'][$product_id]))?$data['unit_price'][$product_id]:0;
                            $pr_product['unit_price'] = number_format($unit_price,5,'.','');
                            $pr_product['price'] = (isset($data['pr_baseprice'][$key]))?$data['pr_baseprice'][$key]:0;
                            $totQty = $qty+$dit_qty+$dnd_qty;//(($qty * $no_of_eaches) - ($free_qty * $free_no_of_eaches));
                            
                            $tax_total = (isset($data['pr_taxvalue'][$product_id]))?$data['pr_taxvalue'][$product_id]:0;
                            $tax_amt = $tax_total / $totQty;
                        
                            $pr_product['sub_total'] = number_format(($unit_price * $totQty),5,'.','');
                            $pr_product['total'] = (isset($data['pr_totprice'][$key]))?number_format($data['pr_totprice'][$key],5,'.',''):0;
                            $pr_product['tax_type'] = (isset($data['pr_taxname'][$product_id]))?$data['pr_taxname'][$product_id]:'';
                            $pr_product['tax_per'] = (isset($data['pr_taxper'][$product_id]))?number_format($data['pr_taxper'][$product_id],2,'.',''):0;
                            $pr_product['tax_amt'] = number_format($tax_amt,5,'.','');
                            $pr_product['tax_total'] = number_format($tax_total,5,'.','');
                            #print_r($pr_product);die;
                            
                            $productExist = $this->_prModel->checkPRProductExist($prId,$product_id);
                            $preUpdatePrProducts = $this->_prModel->getPreUpdatePRProducts($prId,$product_id);
                            $updated_by = (isset($data['updated_by']) && $data['updated_by']!='')?$data['updated_by']:\Session::get('userId');                            
                            if($productExist==1){
                                $flagdata['updated_by']=$updated_by;
                                $changeResult=array_diff($pr_product,$preUpdatePrProducts);
                                if(!empty($changeResult) && count($changeResult)>0){
                                    $mailMsg .= '<p><strong>'.$product['pname'].' Updated</strong></p>';
                                }
                                $pr_product['tax_data'] = (isset($data['pr_taxdata'][$product_id]))?base64_decode($data['pr_taxdata'][$product_id],true):'';
                                $pr_product['hsn_code'] = (isset($data['hsn_code'][$product_id]))?$data['hsn_code'][$product_id]:'';
                                $this->_prModel->updatePRProducts($pr_product,$product_id,$prId,$flagdata);
                            }else{
                                $pr_product['pr_id'] = $prId;
                                $pr_product['product_id'] = $product_id;
                                $pr_product['mrp'] = (isset($product['mrp']) && $product['mrp']!='')?$product['mrp']:0;
                                $pr_product['parent_id'] = (isset($data['parent_id'][$key]))?$data['parent_id'][$key]:0;
                                $pr_product['tax_data'] = (isset($data['pr_taxdata'][$product_id]))?base64_decode($data['pr_taxdata'][$product_id],true):'';
                                $pr_product['hsn_code'] = (isset($data['hsn_code'][$product_id]))?$data['hsn_code'][$product_id]:'';
                                $this->_prModel->savePrProducts($pr_product);
                                $mailMsg .= '<p><strong>'.$product['pname'].' Added</strong></p>';
                            }
                            $totalQty = $totalQty + $totQty;
                        }
                        $prArr=array('updated_by'=>$updated_by,'updated_at'=>date('Y-m-d H:i:s'));                       
                        
                        //$prArr['discount_amt'] = $billDiscAmt;
                        //$prArr['discount'] = (isset($data['bill_discount']))?$data['bill_discount']:0;
                        //$prArr['discount_type'] = (isset($data['bill_discount_type']))?$data['bill_discount_type']:0;
                        $prArr['pr_grand_total'] = $grand_total;
                        $prArr['pr_total_qty'] = $totalQty;                        
                        $prArr['pr_remarks'] = (isset($data['pr_remarks']))?$data['pr_remarks']:'';
                        $this->_prModel->updatePR($prId, $prArr);
                    }                    
                    $prCode = $this->_prModel->getPrCodeById($prId);
                    $pr_code = (isset($prCode->pr_code))?$prCode->pr_code:'';
                    if($mailMsg!=''){
                        $mailData['subject'] = 'PR#'.$pr_code.' Updated';
                        $mailData['message'] = $mailMsg;
                        $this->emailWithAttachment($prId,$pr_code,$mailData);
                        $this->_prModel->updatePR($prId, ['approval_status'=>57036,'approved_by'=>\Session::get('userId'),'approved_at'=>date('Y-m-d H:i:s')]);
                        $current_status=(isset($prCode->approval_status))?$prCode->approval_status:'';
                        $approval_flow_func = new CommonApprovalFlowFunctionModel();
                        $approval_flow_func->storeWorkFlowHistory('Purchase Return', $prId, $current_status, 57036, 'PR has been modified hence moving to intiated', \Session::get('userId'));
                    }
                    $this->prDocUpdate($prId);
                    DB::commit();
                    //$this->poDocUpdate($poId);
                    return json_encode(array('status'=>200, 'message'=>'PR Updated Successfully', 'pr_id'=>$prId, 'pr_code'=>$pr_code));
                }else{
                    return json_encode(array('status'=>400, 'message'=>'PR ID should not be empty', 'pr_id'=>'', 'pr_code'=>''));
                }
            }
        }
        catch(\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
            return json_encode(array('status'=>400, 'message'=>$ex->getMessage(), 'pr_id'=>''));
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
            DB::beginTransaction();
            $approval_flow_func= new CommonApprovalFlowFunctionModel();
            $status = explode(',',$approval_status);
            $nextStatus = $status[0];
            $nextstatuses =[57154];

            $pr_data = DB::table("purchase_returns")->select("sr_invoice_code","legal_entity_id","le_wh_id","pr_grand_total")->where("pr_id",$approval_unique_id)->first();
            $sale_return_inv_no = isset($pr_data->sr_invoice_code)?$pr_data->sr_invoice_code:"";
            $supplier_id = isset($pr_data->legal_entity_id)?$pr_data->legal_entity_id:"";
            $warehouse_id = isset($pr_data->le_wh_id)?$pr_data->le_wh_id:"";
            $pr_grand_total = isset($pr_data->pr_grand_total)?$pr_data->pr_grand_total:0;
            if($sale_return_inv_no !="" && $nextStatus == 57137){
                $sr_data = array("supplier_id"=>$supplier_id,
                        "sr_inv_no"=>$sale_return_inv_no,
                        "pr_totprice"=>array($pr_grand_total),
                        "pr_le_wh_id"=>$warehouse_id);
                $sr_data = $this->_prModel->checkSrInvoice($sr_data);
                if(isset($sr_data['status']) && $sr_data['status'] == 400){
                    $sr_data['serialNumber'] = "";
                    $sr_data['pr_id'] = "";
                    return $sr_data;
                }
            }
            if(in_array($nextStatus, $nextstatuses)){
                $res = array('status'=>200,'message'=>'Success');
            }else{
                $res = $this->_prModel->updateStatusAWF($table,$unique_column,$approval_unique_id, $approval_status);
            }            
            if(isset($res['status']) && $res['status']==200){
                $approval_flow_func->storeWorkFlowHistory($approval_module, $approval_unique_id, $current_status, $nextStatus, $approval_comment, \Session::get('userId'));
                DB::commit();
                $response = array('status'=>200,'message'=>'Success');
            }else{
                $response = $res;
                DB::rollback();
            }            
            return json_encode($response);
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    /*
     * downloadPRExcel() method is used to Download All Purchase Returns
     * @param NULL
     * @return Excell
     */
    public function downloadPRExcel() {
        try{
            $filterData = Input::get();
            $fdate = (isset($filterData['fdate']) && !empty($filterData['fdate'])) ? $filterData['fdate'] : date('Y-m').'-01';
            $fdate = date('Y-m-d', strtotime($fdate));
            $tdate = (isset($filterData['tdate']) && !empty($filterData['tdate'])) ? $filterData['tdate'] : date('Y-m-d');
            $tdate = date('Y-m-d', strtotime($tdate));         
            $query = "CALL getPurchaseReturnReport('$fdate','$tdate')";        
            $file_name = 'PR_Report_' .date('Y-m-d-H-i-s').'.csv';
            $this->exportToCsv($query, $file_name);         
        }catch(Exception $e) {
                Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return Response::json(array('status' => 404, 'Message' => Lang::get('salesorders.errorInputData')));
        }
    }
     public function exportToCsv($query, $filename) {
        $host = env('DB_HOST');
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

    public function salesReturnByPrId($pr_id,$gds_order_id,$le_wh_id){
        $res = $this->_prModel->salesReturnByPrId($pr_id,$gds_order_id,$le_wh_id);
        
    }

    public function checkSrInvoice(Request $request){

        return json_encode($this->_prModel->checkSrInvoice($request->input()));

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
                        
                    })->get()->all();
            $data['pr_data'] = $cat_data;
            $data['header_data'] = $headres;
            $data['prod_data'] = $prod_data;
            return $data;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
        }
    }
    public function importPRExcel() {
        try{
            ini_set('max_execution_time', 0);
            $msg = '';
            if (Input::hasFile('prfile')) {                
                $path = Input::file('prfile')->getRealPath();
                $data = $this->readImportExcel($path);
                $data = json_decode(json_encode($data), 1);
                $msg = '';
                if(isset($data['prod_data']) && !empty($data['prod_data']) && count($data['prod_data'])>0){
                    $supCode = isset($data['pr_data'][0]) ? $data['pr_data'][0] : '';
                    $dcName = isset($data['pr_data'][1]) ? $data['pr_data'][1] : '';
                    $sale_return_inv_no = isset($data['pr_data'][2]) ? $data['pr_data'][2] : '';
                    $sale_return_inv_no = trim($sale_return_inv_no);
                    $sup = $this->_poModel->getSupplierIDCode($supCode);
                    $supId = (isset($sup->legal_entity_id)) ? $sup->legal_entity_id : 0;
                    $dc = $this->_poModel->getWarehouseIDByCode($dcName);
                    $dcId = (isset($dc->le_wh_id)) ? $dc->le_wh_id : 0;
                    $validFlag =1;
                    if($supId!=0 && $dcId!=0){
                        $state_id = (isset($dc->state)) ? $dc->state : 4033;
                        $_cusRepo = new CustomerRepo();
                        $refNoArr = $_cusRepo->getRefCode('PR',$state_id);
                        $serialNumber = $refNoArr;
                        $prDetails['legal_entity_id'] = $supId;
                        $prDetails['le_wh_id'] = $dcId;

                        $podate = isset($data['prod_data'][0]['po_date']['date']) ? $data['prod_data'][0]['po_date']['date'] : date('Y-m-d');
                        if(!empty($podate)) {
                            $prDetails['po_date'] = date('Y-m-d', strtotime($podate)).' '.date('H:i:s');
                        }
                        $prDetails['po_validity'] = isset($data['prod_data'][0]['validitydays']) ? $data['prod_data'][0]['validitydays'] : 7;
                        $po_date = date('d-m-Y',strtotime($podate));
                        $date = new \DateTime($po_date);
                        if($prDetails['po_validity']!=0){
                            $date->add(new \DateInterval('P'.$prDetails['po_validity'].'D')); //new DateInterval('P7Y5M4DT4H3M2S')
                            $delivery_date = $date->format('Y-m-d H:i:s');
                        }else{
                            $delivery_date = date('Y-m-d H:i:s');
                        }
                        $prDetails['delivery_date'] = $delivery_date;
                        $prDetails['po_type'] = 0;
                        $prDetails['created_by'] = \Session::get('userId');
                        $prDetails['pr_remarks'] = '';
                        $prDetails['pr_code'] = $serialNumber;
                        $prDetails['approval_status'] = 57037;
                        $prDetails['pr_status'] = 103001;
                        if(!empty($expDeliveryDate)) {
                            $prDetails['exp_delivery_date'] = $expDeliveryDate;
                        }
                        $skulistfromexcelsheet=array_column($data['prod_data'], 'sku');
                        //echo "<pre>";print_r($prDetails);die;
                        DB::beginTransaction();
                        $prId = $this->_prModel->create($prDetails);
                        $prId = $prId->pr_id;
                        if($prId!='' && $prId>0){
                            $uomArr = $this->_masterLookup->getAllOrderStatus('Levels');
                            $uomArr=array_flip($uomArr);
                            $skuArray = array();
                            $productArr = array();
                            $timestamp = md5(microtime(true));
                            $txtFileName = 'pr-import-' . $timestamp . '.html';
                            $file_path = 'download' . DIRECTORY_SEPARATOR . 'po_log' . DIRECTORY_SEPARATOR . $txtFileName;
                            $files_to_delete = File::files('download' . DIRECTORY_SEPARATOR . 'po_log/');
                            File::delete($files_to_delete);
                            $msg = '';
                            $excelRowcounter = 1;
                            $errorCnt = 0;
                            $pr_total_qty = 0;
                            $pr_total_price = 0;

                            $stateCodes = $this->getAPOBStateCodes($dcId,$supId);
                            $wh_state_code=isset($stateCodes['wh_state_code'])?$stateCodes['wh_state_code']:'';
                            $seller_state_code=isset($stateCodes['seller_state_code'])?$stateCodes['seller_state_code']:'';
                            Log::info('PR time ');
                            Log::info(time());
                            foreach($data['prod_data'] as $poproducts){
                                $msg .= "#".$excelRowcounter." SKU (".$poproducts['sku'].") ";
                                if($poproducts['sku']!=''){
                                    
                                    $product_id = $this->_poModel->getProductIdbySku($poproducts['sku']);
                                    if($product_id != 0){
                                        $freebieParent = $this->_poModel->getFreebieParent($product_id);
                                        $parent_id = (isset($freebieParent->main_prd_id)) ? $freebieParent->main_prd_id : 0;
                                        $checkProduct = $this->getPOProductRow($product_id, $parent_id, $supId, $dcId, "");
                                        if ($checkProduct['status'] == 200) {
                                            $prd_data = $checkProduct['prd_data'];
                                            if($poproducts['soh_qty'] <= $prd_data["avilInv"]){

                                                if($poproducts['base_price'] <= $prd_data["mrp"]){
                                                    $product = $this->_prModel->getProductInfoByID($product_id,$supId,$dcId);
                                                    $product = json_decode(json_encode($product),true);
                                                    //considering excel product as main product and trying to get child products if any is configured and checking if that freebie is uploaded in excel sheet if freebie is not present we will throw error
                                                    $isFreebieproduct=0;
                                                    $checkFreebieeforproduct = $this->_poModel->getFreebieProducts($product_id);
                                                    if(count($checkFreebieeforproduct)>0 && count($product)>0)
                                                    {
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
                                                    //this is to check if product is freebie then get main product and check if main product is in excel sheet else throw the error main product is missing
                                                    $checkParentforproduct = $this->_poModel->getFreebieParent($product_id);
                                                    if(isset($checkParentforproduct->main_prd_id) && $checkParentforproduct->main_prd_id>0 && count($product)>0){
                                                            $isFreebieproduct=1;//if is freebie then base price is zero irrespective of baseprice uploaded in sheet
                                                            $getparentskuname=$this->_poModel->getSKUByProductId($checkParentforproduct->main_prd_id);
                                                            if(!in_array($getparentskuname->sku, $skulistfromexcelsheet))
                                                            {
                                                                $errorCnt++;
                                                                $msg .= "Main Product Missing(".$getparentskuname->sku.")";
                                                                break;
                                                            }

                                                    }
                                                    //$whDetail = $this->_LegalEntity->getWarehouseById($dcId);
                                                    //$supplierInfo = $this->_poModel->getLegalEntityById($supId);
                                                    //$wh_state_code = isset($whDetail->state)?$whDetail->state:4033;
                                                    //$seller_state_code = isset($supplierInfo->state_id)?$supplierInfo->state_id:4033;
                                                    // product should exist,tax should found,same sku checking
                                                    if(count($product)>0 && !in_array($poproducts['sku'], $skuArray)){
                                                        // pushing sku to checking duplicate sku
                                                        $poproducts['sku'] = trim($poproducts['sku']);
                                                        array_push($skuArray, $poproducts['sku']);
                                                        $pr_product = array();
                                                        $product = json_decode(json_encode($product),1);
                                                        $product_id = $product['product_id'];
                                                        $txArr=[];
                                                        Log::info($product_id);
                                                        Log::info("tax api start==".time());
                                                        $tax_data = $this->getProductTaxClass($product_id,$wh_state_code,$seller_state_code);
                                                        Log::info("tax api end".time());
                                                        //$txArr[] =(object)array('product_id'=>$product_id,'le_wh_id'=>$dcId,'legal_entity_id'=>$supId);
                                                        //$tax_data = $this->getTaxInfo($txArr);
                                                        //$tax_data = isset($tax_data[$product_id]) ? $tax_data[$product_id] : [];
                                                        $pr_product['no_of_eaches'] = 1;
                                                        $pr_product['product_id'] = $product_id;
                                                        $pr_product['parent_id'] = $parent_id;
                                                        $pr_product['mrp'] = (isset($product['mrp']) && $product['mrp']!='')?$product['mrp']:0;
                                                        $pr_product['qty'] = (isset($poproducts['soh_qty']))?$poproducts['soh_qty']:1;
                                                        if(is_numeric($poproducts['soh_qty']) && $poproducts['soh_qty'] !='' && $poproducts['soh_qty'] >=1){
                                                            if(is_numeric($poproducts['base_price']) && $poproducts['base_price'] !==''){
                                                                if($isFreebieproduct){
                                                                    $pr_product['unit_price'] =0;
                                                                    $pr_product['price'] = 0;    
                                                                }else{
                                                                    $pr_product['unit_price'] = ($poproducts['base_price']!='')?$poproducts['base_price']:0;
                                                                    $pr_product['price'] = 1 * $poproducts['base_price'];
                                                                }
                                                                $pr_product['sub_total'] = $pr_product['qty'] * $pr_product['price'];
                                                                $pr_product['is_tax_included'] = 1;
                                                                $pr_product['uom'] = 16001;
                                                                $pr_product['tax_data'] = json_encode(isset($tax_data[0])?$tax_data:array());
                                                                if(isset($tax_data[0]) && count($tax_data[0]) && is_array ($tax_data[0])){
                                                                    $tax_data = isset($tax_data[0])?$tax_data[0]:[];
                                                                    $tax_amt = 0;
                                                                    $pr_product['tax_type'] = (isset($tax_data['Tax Type']) && $tax_data['Tax Type'] != '')?$tax_data['Tax Type']:'';
                                                                    $pr_product['tax_per'] = (isset($tax_data['Tax Percentage']))?$tax_data['Tax Percentage']:0.00;
                                                                    $pr_product['hsn_code'] = (isset($tax_data['HSN_Code']))?$tax_data['HSN_Code']:0;
                                                                    $price_excltax = ($pr_product['sub_total']/(1+(($pr_product['tax_per'])/100)));
                                                                    $taxAmt = $pr_product['sub_total']-$price_excltax;
                                                                    $pr_product['tax_amt'] = $taxAmt / $pr_product['qty'];
                                                                    $pr_product['tax_total'] = $taxAmt; 
                                                                    $pr_product['pr_id'] = $prId;
                                                                    $pr_product['total'] = $pr_product['sub_total'];
                                                                    $pr_total_price += $pr_product['total'];
                                                                    $pr_total_qty += $pr_product['qty'];
                                                                    $pr_product['created_by'] = Session::get('userId');
                                                                    $this->_prModel->savePrProducts($pr_product);
                                                                    array_push($productArr, $pr_product);
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
                                                        $msg .= "Product for given SKU not exist! or Inventory details not exist!";
                                                    }
                                                }else{
                                                    $errorCnt++;
                                                    $msg .= "Given Base Price(".$poproducts['base_price'].") cannot be greater than MRP(".$prd_data["mrp"].")";
                                                }
                                            }else{
                                                $errorCnt++;
                                                $msg .= "Given SOH Qty(".$poproducts['soh_qty'].") cannot be greater than ".$prd_data["avilInv"];
                                            }
                                        }else{
                                            $errorCnt++;
                                            $msg .= $checkProduct['message'];
                                        }
                                    }else{
                                        $errorCnt++;
                                        $msg .= "Invalid SKU";
                                    }
                                }else{
                                    $errorCnt++;
                                    $msg .= "SKU is empty";
                                }
                                $excelRowcounter++;
                                $msg .= "</br></br>";
                            }

                            if($errorCnt == 0){
                                if(count($productArr)){
                                    $arr = ['pr_total_qty'=>$pr_total_qty,'pr_grand_total'=>$pr_total_price,"sr_invoice_code"=>$sale_return_inv_no];
                                    if($sale_return_inv_no !=""){
                                        $pr_total_price_arr[] = $pr_total_price;
                                        $sr_data = array("supplier_id"=>$supId,
                                                "sr_inv_no"=>$sale_return_inv_no,
                                                "pr_totprice"=>$pr_total_price_arr,
                                                "pr_le_wh_id"=>$dcId);
                                        $sr_data = $this->_prModel->checkSrInvoice($sr_data);
                                        if(isset($sr_data['status']) && $sr_data['status'] == 400){
                                            $errorCnt = 1;
                                            $msg .= $sr_data['message'];
                                            $returnArray = array('status'=>400, 'message'=>$sr_data['message'],"url"=>"");
                                            return Response::json($returnArray);
                                        }
                                    }
                                    $this->_prModel->updatePR($prId,$arr);
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
                $url = "pr/details/".$prId;
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
            Log::info(time());
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
                    })->get()->all();
            $data['cat_data'] = $cat_data;
            $data['prod_data'] = $prod_data;
            return $data;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
        }
    }

    public function downloadPRImportExcel(){

        $mytime = Carbon::now();
        $headers = array('supplier_code','dc_code','sale_return_inv_no');
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

        Excel::create('PR Template Sheet-'.$mytime->toDateTimeString(), function($excel) use($headers, $exceldata, $headers_second_page, $exceldata_second) 
        {

            $excel->sheet("PR", function($sheet) use($headers, $exceldata)
            {
                $sheet->loadView('PurchaseReturn::primportproductdata', array('headers' => $headers, 'data' => $exceldata)); 
            });

            $excel->sheet("Supplier_and_Warehouse_Data", function($sheet) use($headers_second_page, $exceldata_second)
            {
                $sheet->loadView('PurchaseOrder::poimportsupplierdata', array('headers' => $headers_second_page, 'data' => $exceldata_second)); 
            });
        })->export('xlsx');

    }
    /**
    * uploadDocumentAction() method is use upload document
    * @param  $request Object
    * @return JSON
    */
    public function uploadDocumentAction(Request $request) {
        try{
            $postData = Input::all();
            $pr_id = isset($postData['pr_id']) ? $postData['pr_id'] : 0;
            if ($request->hasFile('upload_file')) {
                $extension = Input::file('upload_file')->getClientOriginalExtension();
                if(!in_array($extension, array('pdf', 'doc', 'docx', 'png', 'jpg', 'jpeg'))) {
                    return json_encode(array('status'=>400, 'message'=>Lang::get('inward.returnAlertExtension')));
                }
                $imageObj = $request->file('upload_file');
                $url = $this->_productRepo->uploadToS3($imageObj,'pr_returnack',1);
                if($url!='') {
                    $docsArr = array(
                        'pr_id'=>$pr_id,
                        'file_path'=>$url,
                        'created_at'=>date('Y-m-d H:i:s')
                    );
                    $doc_id=$this->_prModel->saveDocument($docsArr);
                    Session::push('prdocs', $doc_id);
                    $docText='<div><span><i class="fa fa-close downloadclose" data-doc_id="'.$doc_id.'"></i></span>'
                            . '<a href="'.$url.'" class="closedownload"><i class="fa fa-download"></i></a></div>';
                    return json_encode(array('status'=>200, 'message'=>Lang::get('inward.returnacksuccessUploaded'),'docText'=>$docText));
                }
            }
            else {
                return json_encode(array('status'=>200, 'message'=>Lang::get('inward.errorInputData')));
            }
        }
        catch(Exception $e) {
            return json_encode(array('status'=>400, 'message'=>Lang::get('inward.errorInputData')));
        }
    }

    public function deleteDoc($doc_id) {
        try {
            $this->_prModel->deleteDoc($doc_id);
            return json_encode(['status'=>'200','message'=>'doc deleted successfully']);
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function prDocUpdate($pr_id) {
        try {
            $prdocid = Session::get('prdocs');
            if (isset($prdocid) && is_array($prdocid) && count($prdocid) > 0) {
                foreach ($prdocid as $docid) {
                    $this->_prModel->prDocUpdate($pr_id, $docid);
                    Session::put('prdocs', array());
                }
            }
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function excelAction($orderId){
        try{
            $pr_excel = array();
            $pr_excel = $this->printPr($orderId);
            Excel::create('pr_excel', function($excel) use($pr_excel) {  
            $excel->sheet('pr_excel', function($sheet) use($pr_excel) {  
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
                                'height'    => 150
                                 )
                           ));
                    $sheet->loadView('PurchaseReturn::prExcelSheet')->with('loadSheet', $pr_excel);
                });
            })->export('xls');

        }catch(\ErrorException $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());

        }

    }
    public function printPr($pr_id){

        try{
            $hasAccess = $this->_roleRepo->checkPermissionByFeatureCode('PR0010');
            if ($hasAccess == false) {
                return View::make('Indent::error');
            }
            $prDetailArr = $this->_prModel->getPrDetailById($pr_id);
            if (count($prDetailArr) == 0) {
                Redirect::to('/pr/index')->send();
                die();
            }
            $leWhId = isset($prDetailArr[0]->le_wh_id) ? $prDetailArr[0]->le_wh_id : 0;
            $leId = isset($prDetailArr[0]->legal_entity_id) ? $prDetailArr[0]->legal_entity_id : 0;
            $leParentId = $this->_LegalEntity->getLeParentIdByLeId($leId);
            $leDetail = $this->_LegalEntity->getLegalEntityById($leParentId);
            $whDetails = $this->_LegalEntity->getWarehouseById($leWhId);
            if($leParentId)
                $leDetail = $this->_LegalEntity->getLegalEntityById($leParentId);
            else{
                $leDetail = $this->_LegalEntity->getLegalEntityById($whDetails->legal_entity_id);
            }
            if($prDetailArr[0]->pr_address==''){
               $whDetail = $billingDetail =  $whDetails;
               $supplierInfo = $this->_poModel->getLegalEntityById($leId);

            }else{
                $pr_Address=json_decode($prDetailArr[0]->pr_address,true);
                $billingDetail=isset($pr_Address['billing'])?(object)$pr_Address['billing']:(object)array();
                $whDetail=isset($pr_Address['shipping'])?(object)$pr_Address['shipping']:(object)array();
                $supplierInfo=isset($pr_Address['supplier'])?(object)$pr_Address['supplier']:(object)array();
                
            }
            $userInfo = $this->_poModel->getUserByLeId($leId);
            //$supplierInfo = $this->_poModel->getLegalEntityById($leId);
            $taxBreakup = $this->getTaxBreakup($prDetailArr);
            $packTypes = $this->_masterLookup->getAllOrderStatus('Levels');
            $loadSheet = array();
            $loadSheet['productArr'] = $prDetailArr;
            $loadSheet['supplier'] = $supplierInfo;
            $loadSheet['leDetail'] = $leDetail;
            $loadSheet['whDetail'] = $whDetail;
            $loadSheet['taxBreakup'] = $taxBreakup;
            $loadSheet['userInfo'] = $userInfo;
            $loadSheet['packTypes'] =$packTypes;
            return $loadSheet;

        }catch (\ErrorException $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());

        }

    }

}
