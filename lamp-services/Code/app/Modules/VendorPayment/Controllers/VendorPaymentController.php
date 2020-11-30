<?php
/*
 * VendorPaymentController is used to manage Vendor Payment Request
 * @author      Ebutor <info@ebutor.com>
 * @copyright   ebutor@2016
 * @package     PO
 * @version:    v1.0
 */

namespace App\Modules\VendorPayment\Controllers;

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
use App\Modules\VendorPayment\Models\VendorPaymentRequest;
use App\Modules\H2HAxis\Controllers\h2hAxisAPIController;


class VendorPaymentController extends BaseController {

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
    protected $_approvalStageStatuslist;
    protected $_ebutor_bank_account;
    protected $_bank_status;
    protected $_po_payment_status;

    /*
     * __construct() method is used to call model
     * @param Null
     * @return Null
     */
    public function __construct(\Illuminate\Http\Request $request, $forApi=0) {
        date_default_timezone_set('Asia/Kolkata');


        $this->middleware(function ($request, $next) use($forApi) {
            if (!Session::has('userId') && $forApi==0) {
                Redirect::to('/login')->send();
            }
            return $next($request);
        });

        $this->_poModel                   = new PurchaseOrder();
        $this->_vendorPaymentRequestModel = new VendorPaymentRequest();
        $this->_masterLookup              = new MasterLookup();
        $this->_indent                    = new IndentModel();
        $this->_roleRepo                  = new RoleRepo();
        $this->_LegalEntity               = new LegalEntity();
        $this->_productRepo               = new ProductRepo();
        $this->_gdsBus                    = new GdsBusinessUnit();
        $this->_orderModel                = new OrderModel();
        $this->_roleModel                 = new Role(); 
        $this->_reportsrepo               = new ReportsRepo();
        parent::Title('Vendor Payments - '.Lang::get('headings.Company'));
        $this->grid_field_db_match = array(
            'poId'             => 'po.po_code',
            'Supplier'         => 'legal_entities.business_legal_name',
            'le_code'          => 'legal_entities.le_code',
            'shipTo'           => 'lwh.lp_wh_name',
            'validity'         => 'po.po_validity',
            'poValue'          => 'poValue',
            'createdBy'        => 'user_name',
            'createdOn'        => 'po.po_date',
            'payment_mode'     => 'po.payment_mode',
            'tlm_name'         => 'po.tlm_name',
            'Status'           => 'lookup.master_lookup_name',
            'poValue'          => 'poValue',
            'grn_value'        => 'grn_value',
            'po_grn_diff'      => 'po_grn_diff',
            'grn_created'      => 'grn_created',
            'payment_status'   => 'po.payment_status',
            'payment_due_date' => 'payment_due_date',
            'po_so_order_link' => 'po_so_order_code',
            'duedays'          => 'duedays'
        );
        $this->_filterStatus = array('open'=>'87001', 'partial'=>'87005','closed'=>'87002', 'expired'=>'87003', 'canceled'=>'87004');
        $this->_approvalStatuslist = [
            'drafted'              => 57202,
            'initiated'            => 57203,
            'finance_approved'     => 57204,
            //'pim_approved'         => 57205,
            //'sales_approved'       => 57206,
            'processing_with_bank' => 57218,            
            'completed'            => 57219,
            'failed_at_bank'       => 57220,
            'rejected'             => 57221,
            'hold'                 => 57222,
            'not_to_pay'           => 57223,           
        ];
        $this->_approvalStageStatuslist = [
            'rejected'         => 58105,
            'approved'         => 58106,
            'submitted'        => 58107
        ];

        $this->_bank_status = [ '0' => 'Successful', '1' => 'Failed'];
        $this->_po_payment_status = [
            'full_paid' => 57032,
            'part_paid' => 57118,
            'hold'      => 57222,
            'not_to_pay'=> 57223,
            'pending'   => 57224,
        ];
    }

    /*
     * filterData() method is used to prepare filters condition from string
     * @param $filter String  Filter conditions for the list
     * @return Array    Array of filters
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
                    if (substr_count($data, 'city') && !array_key_exists('city', $filterDataArr)) {
                        $sup = explode('ge', $data);
                        $pos = strpos($data, 'eq');
                        $cityValArr = str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'city','eq '), '', $sup[0]);
                        $value = ($pos>0) ? trim($cityValArr,' ') : '%'.trim($cityValArr,' ').'%';
                        $operator = ($pos>0) ? '=' : 'LIKE';
                        $filterDataArr['city'] = array('operator' => $operator, 'value' => $value);
                    }

                    if (substr_count($data, 'state_name') && !array_key_exists('state_name', $filterDataArr)) {
                        $sup = explode('ge', $data);
                        $pos = strpos($data, 'eq');
                        $state_nameValArr = str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'state_name','eq '), '', $sup[0]);
                        $value = ($pos>0) ? trim($state_nameValArr,' ') : '%'.trim($state_nameValArr,' ').'%';
                        $operator = ($pos>0) ? '=' : 'LIKE';
                        $filterDataArr['state_name'] = array('operator' => $operator, 'value' => $value);
                    }
                    
                    if (substr_count($data, 'po_grn_diff')) {
                        $filterDataArr['po_grn_diff']['operator'] = $this->getCondOperator($dataArr[1]);
                        $filterDataArr['po_grn_diff']['value'] = $dataArr[2];
                    }
                    if (substr_count($data, 'requested_amount')) {
                        $filterDataArr['requested_amount']['operator'] = $this->getCondOperator($dataArr[1]);
                        $filterDataArr['requested_amount']['value'] = $dataArr[2];
                    }
                    if (substr_count($data, 'approved_amount')) {
                        $filterDataArr['approved_amount']['operator'] = $this->getCondOperator($dataArr[1]);
                        $filterDataArr['approved_amount']['value'] = $dataArr[2];
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
                    if (substr_count($data, 'po_code') && !array_key_exists('po_code', $filterDataArr)) {
                        $sup = explode('ge', $data);
                        $pos = strpos($data, 'eq');
                        $suppValArr = str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'po_code','eq '), '', $sup[0]);
                        $value = ($pos>0) ? trim($suppValArr,' ') : '%'.trim($suppValArr,' ').'%';
                        $operator = ($pos>0) ? '=' : 'LIKE';
                        $filterDataArr['po_code'] = array('operator' => $operator, 'value' => $value);
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

    /**
     * List of suppliers whose payment is due
     * @param  String $status Status for the payment due list
     * @return HTML   List of the payment dues from the vendor
     */
    public function index($status='') {
        try{
            $hasAccess = $this->_roleRepo->checkPermissionByFeatureCode('PO001');
            $poGSTReport = $this->_roleRepo->checkPermissionByFeatureCode('POGSTR');
            if($hasAccess == false) {
                return View::make('PurchaseOrder::error');
            }
            $input = Input::all();
            $from_date = isset($input['from_date'])?$input['from_date']:"";
            $to_date = isset($input['to_date'])?$input['to_date']:"";
            $sup_name =isset($input['sup_name'])?$input['sup_name']:"";
            $filter_dates = array();
            $from_date = date('Y-m-d', strtotime($from_date));
            $to_date = date('Y-m-d', strtotime($to_date));

            if($from_date != "1970-01-01" && $to_date != "1970-01-01" || $sup_name != ""){
                $filter_dates = array(
                    'from_date' => $from_date,
                    'to_date' => $to_date,
                    'sup_name' => $sup_name
                );
            } 
         
                  // Check the access
            $acl = array(
                'initiated'                         => $this->_roleRepo->checkPermissionByFeatureCode('VPI0001'),
                'approved'                          => $this->_roleRepo->checkPermissionByFeatureCode('VPA0001'),
                'processing_with_bank'              => $this->_roleRepo->checkPermissionByFeatureCode('VPUPB0001'),
                'rejected'                          => $this->_roleRepo->checkPermissionByFeatureCode('VPR0001'),                
                'completed'                         => $this->_roleRepo->checkPermissionByFeatureCode('VPC0001'),
                'pending'                           => $this->_roleRepo->checkPermissionByFeatureCode('VPP0001'),
                // Button Access
                'raise_payment_request_access'      => $this->_roleRepo->checkPermissionByFeatureCode('RPR0001'),
                'approve_payment_request_access'    => $this->_roleRepo->checkPermissionByFeatureCode('APR0001'),
                'reject_payment_request_access'     => $this->_roleRepo->checkPermissionByFeatureCode('RJPR0001'),
                'export_excel_bank_payment_access'  => $this->_roleRepo->checkPermissionByFeatureCode('ETBP0001'),
                'update_bank_payment_status_access' => $this->_roleRepo->checkPermissionByFeatureCode('UBPS0001')
            );
            $legalEntityId = Session::get('legal_entity_id');
            // Get the count as per status
            $pendingPaymentCount = $this->_poModel->getUpcomingPayments('', '', '', TRUE, '', '');
            $total_status = $this->_vendorPaymentRequestModel->getPaymentRequestStatusCount();
            $status_count = $this->addStatus($total_status);
            //$allStatusArr = $this->_masterLookup->getAllOrderStatus('PURCHASE_ORDER');
            if($filter_dates){
                $completed  =$this->_poModel->getUpcomingPayments($status, '', '', TRUE, '', '',$filter_dates['from_date'],$filter_dates['to_date'],$filter_dates['sup_name']);
            }else{
                $completed = $this->_vendorPaymentRequestModel->getPaymentRequestStatusCountComplete($status);
            }
            $suppliers = $this->_poModel->getSupplierByLEId($legalEntityId);
            $Json = json_decode($this->_roleModel->getFilterData(6), 1);
            $filters = json_decode($Json['sbu'], 1);
            $dc_acess_list = isset($filters['118001']) ? $filters['118001'] : 'NULL';
            $allDc = $this->_orderModel->getDcHubDataByAcess($dc_acess_list);
            $filter_options['dc_data'] = $allDc;
            //$allPOCountArr = $this->_poModel->getPoCountByStatus($legalEntityId);
            /*$allPOApprovalCountArr = $this->_poModel->getPoCountByStatus($legalEntityId,1);
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
            */
            $allpo = $this->_poModel->getUpcomingPayments('allpo', '', [], 1);
            $hold = $this->_poModel->getUpcomingPayments(57222, '', [], 1);
            $not_to_pay = $this->_poModel->getUpcomingPayments(57223, '', [], 1);
            $poCounts = array(
                            'all'          => $allpo,//array_sum($allPOCountArr),
                            'hold'       => $hold,
                            'not_to_pay'       => $not_to_pay,
                           /* 'opened'       => $opened,
                            'partial'      => $partial,
                            'closed'       => $closed,
                            'canceled'     => $canceled,
                            'expired'      => isset($allPOCountArr[87003]) ? (int)$allPOCountArr[87003] : 0,
                            'initiated'    => $initiated,
                            'created'      => $created,
                            'verified'     => $verified,
                            'approved'     => isset($allPOApprovalCountArr[57031]) ? (int)$allPOApprovalCountArr[57031] : 0,
                            'paid'         =>  $opened+$partial+$shelved,
                            'immediatepay' =>   array_sum($immediatePay),
                            'posit'        => isset($allPOApprovalCountArr[57033]) ? (int)$allPOApprovalCountArr[57033] : 0,
                            'receivedatdc' => ($inspected_part+$inspected_full),
                            'checked'      => $checked,
                            'grncreated'   => isset($allPOApprovalCountArr[57035]) ? (int)$allPOApprovalCountArr[57035] : 0,
                            'shelved'      => $shelved,*/
                            );
            $createFeature = $this->_roleRepo->checkPermissionByFeatureCode('PO002');
            $exportFeature = $this->_roleRepo->checkPermissionByFeatureCode('PO008');
            $featureAccess = array('createFeature'=>$createFeature,'exportFeature'=>$exportFeature);
            return view('VendorPayment::index')->with('poCounts', $poCounts)
                                                            ->with('filter_status', $status)
                                                            ->with('acl', $acl)
                                                            ->with('pendingPaymentCount', $pendingPaymentCount)
                                                            ->with('total_status', $status_count)
                                                            ->with('suppliers', $suppliers)
                                                            ->with('featureAccess', $featureAccess)
                                                            //->with('allStatusArr', $allStatusArr)
                                                            ->with('poGSTReport',$poGSTReport)
                                                            ->with('filter_options',$filter_options)
                                                            ->with('filter_dates',$filter_dates)
                                                            ->with('approvalStatuslist',$this->_approvalStatuslist)
                                                            ->with('completed',$completed)
                                                            ->with('approvalStageStatuslist',$this->_approvalStageStatuslist);
        }
        catch(Exception $e) {

        }
    }

    /*
     * Get the list due of the payment supplier
     * @param $status String, by default null
     * @return JSON
     */
    public function getPurchaseOrders($status = null, Request $request)
    {
        
        try {
            $status        = ($status == 'pending') ? '' : $status;
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
            if(isset($filterData['from_date']) && $filterData['from_date'] != ""){
                $filters['from_date'] = $filterData['from_date'];
            }

            if(isset($filterData['to_date']) && $filterData['to_date'] != ""){
                $filters['to_date'] = $filterData['to_date'];
            }
            if(isset($filterData['sup_name']) && $filterData['sup_name'] != ""){
                $filters['sup_name'] = $filterData['sup_name'];
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
            $poDataArr           = $this->_poModel->getUpcomingPayments($status, $orderby_array, $filters, 0, $offset, $perpage);

            $totalPurchageOrders = $this->_poModel->getUpcomingPayments($status, $orderby_array, $filters, 1);
            $dataArr = array();
            if (count($poDataArr)) {
                $detailFeature   = $this->_roleRepo->checkPermissionByFeatureCode('PO003');
                $printFeature    = $this->_roleRepo->checkPermissionByFeatureCode('PO004');
                $downloadFeature = $this->_roleRepo->checkPermissionByFeatureCode('PO005');
                $editFeature     = $this->_roleRepo->checkPermissionByFeatureCode('PO007');
                //print_r($poDataArr);die;
                foreach ($poDataArr as $po) {
                    $shipTo         = $po->lp_wh_name;
                    $poValidity     = $po->po_validity . ' ' . (($po->po_validity > 1) ? 'Days' : 'Day');
                    $po_status      = isset($allStatusArr[$po->po_status]) ? $allStatusArr[$po->po_status] : '';
                    $approvalStatus = isset($po->approval_status) ? $po->approval_status : '';
                    $paymentStatus  = isset($po->payment_status) ? $po->payment_status : '';
                    $poValue        = ($po->poValue != '') ? $po->poValue : 0;
                    $actions        = '';

                    if ($po->po_status == '87001' && $po->approval_status_val != '57117' && $editFeature && isset($po->request_id) && $po->request_id !="") {
                        $actions .= '<a href="/vendor/payment-request-update/'.$po->request_id.'" > <i class="fa fa-thumbs-o-up"></i></a>&nbsp;';
                    }


                    if (isset($po->request_id) && $po->request_id !="") {
                        $actions .= '&nbsp;<a href="#" title="PO History" onclick=viewhistory('. $po->po_id .',"PO")> <i class="fa fa-history"></i></a>&nbsp;';
                        $actions .= '&nbsp;<a href="#" title="Request History" onclick=viewhistory('. $po->request_id .',"Request")> <i class="fa fa-history"></i></a>&nbsp;';
                    }else{
                        $actions .= '<a href="#" title="PO History" onclick=viewhistory('. $po->po_id .',"PO")> <i class="fa fa-history"></i></a>&nbsp;';
                    }

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
                    $chk = '';
                    $comments = '';
                    $pendingAmt = '';
                    $pendingAmtVal = '';
                    if( $status == $this->_approvalStatuslist['initiated'] ){
                        $payable = ( $po->grn_value != "" ) ? $po->grn_value : $poValue;
                        $chk = '<input class="check_box" type="checkbox" name="chk[]" value="'.$po->request_id.'">';
                        $comments = '<textarea class="form-control" name="comments[]" id="comments'.$po->request_id.'" cols="25" rows="5"></textarea>';
                        $pendingAmt    = $po->requested_amount;
                        $pendingAmt    = number_format((float)$pendingAmt, 2, '.', '');
                        $pendingAmtVal = '<input class="form-control" type="text" id="pendingAmt'.$po->request_id.'" name="pendingAmt[]" value="'.$pendingAmt.'" min="1" class="inputBorder" onkeyup="this.value = validateMaximum(this.value, '.$pendingAmt.')" size="10" >';
                        $dataArr[] = array(
                            'chk'                 => $chk,
                            'poId'                => $po->po_id,
                            'po_code'             => $po->po_code,
                            'duedays'             => $po->duedays,
                            'le_code'             => $po->le_code,
                            'Supplier'            => $po->business_legal_name,
                            'payable'             => $payable,
                            'requested_amount'    => $po->requested_amount,
                            'approved_amount'     => $po->approved_amount,
                            'pending_amount'      => $pendingAmtVal,
                            'shipTo'              => $shipTo,
                            'validity'            => $poValidity,
                            'poValue'             => $poValue,
                            'payment_mode'        => $payment_mode,
                            'payment_due_date'    => $po->payment_due_date,
                            'tlm_name'            => $tlm_name,
                            'createdBy'           => $po->user_name,
                            'createdOn'           => ($po->po_date),
                            'Status'              => $po_status,
                            'approval_status'     => $approvalStatus,
                            'payment_status'      => $paymentStatus,
                            'Actions'             => $actions,
                            'grn_value'           => $po->grn_value,
                            'po_grn_diff'         => $po->po_grn_diff,
                            'grn_created'         => $po->grn_created,
                            'po_so_order_link'    => $po_so_order_link,
                            'comments'            => $comments,
                            'state_name'          => $po->state_name,
                            'city'                => $po->city,
                            'business_legal_name' => $po->business_legal_name
                        );
                    } elseif( $status == $this->_approvalStatuslist['finance_approved'] ) {
                        $chk = '<input class="check_box" type="checkbox" name="chk[]" value="'.$po->request_id.'">';
                        $pendingAmt    = number_format((float)$po->requested_amount, 2, '.', '');
                        $pendingAmtVal = $pendingAmt;
                        $dataArr[] = array(
                            'chk'              => $chk,
                            'poId'             => $po->po_id,
                            'po_code'          => $po->po_code,
                            'duedays'          => $po->duedays,
                            'le_code'          => $po->le_code,
                            'Supplier'         => $po->business_legal_name,
                            'requested_amount' => $po->requested_amount,
                            'approved_amount'  => $po->approved_amount,
                            'req_amt'          => $po->req_amt,
                            'appr_amt'         => $po->appr_amt,
                            'pending_amount'   => $pendingAmtVal,
                            'shipTo'           => $shipTo,
                            'validity'         => $poValidity,
                            'poValue'          => $poValue,
                            'payment_mode'     => $payment_mode,
                            'payment_due_date' => $po->payment_due_date,
                            'tlm_name'         => $tlm_name,
                            'createdBy'        => $po->user_name,
                            'createdOn'        => ($po->po_date),
                            'Status'           => $po_status,
                            'approval_status'  => $approvalStatus,
                            'payment_status'   => $paymentStatus,
                            'Actions'          => $actions,
                            'grn_value'        => ($po->grn_value),
                            'po_grn_diff'      => $po->po_grn_diff,
                            'grn_created'      => $po->grn_created,
                            'po_so_order_link' => $po_so_order_link,
                            'comments'         => $comments,
                            'state_name'          => $po->state_name,
                            'city'                => $po->city,
                            'business_legal_name' => $po->business_legal_name
                        );
                    } elseif( $status == $this->_approvalStatuslist['completed'] ) {
                        if($po->bank_payment_date != "NULL"){

                            if($po->bank_payment_date != "01-01-1970 05:30:00"){
                                $bank_payment_date = date('d-m-Y H:i:s', strtotime($po->bank_payment_date));
                            }
                        }
                        $chk = '<input class="check_box" type="checkbox" name="chk[]" value="'.$po->request_id.'">';
                        $dataArr[] = array(
                            'chk'              => $chk,
                            'poId'                => $po->po_id,
                            'po_code'             => $po->po_code,                           
                            'le_code'             => $po->le_code,
                            'Supplier'            => $po->business_legal_name,
                            'approved_amount'     => number_format((float)$po->appr_amt, 2, '.', ''),                            
                            'poValue'             => $poValue,                          
                            'grn_value'           => $po->grn_value,
                            'po_grn_diff'         => $po->po_grn_diff,
                            'comments'            => $po->bank_comment,
                            'bank_account'        => $po->ebutor_bank_account,
                            'payment_status'      => $po->bank_status,
                            'payment_date'        => $bank_payment_date,
                            'utr'                 => $po->utr_number,
                            'state_name'          => $po->state_name,
                            'city'                => $po->city,
                            'shipTo'              => $shipTo,
                            'business_legal_name' => $po->business_legal_name,
                            'Actions'             => $actions,
                        );
                    } elseif( $status == $this->_approvalStatuslist['failed_at_bank'] ) {
                        $chk = '<input class="check_box" type="checkbox" name="chk[]" value="'.$po->request_id.'">';
                        $pendingAmt    = number_format((float)$po->requested_amount, 2, '.', '');
                        $pendingAmtVal = $pendingAmt;
                        $dataArr[] = array(
                            'chk'              => $chk,
                            'poId'             => $po->po_id,
                            'po_code'          => $po->po_code,
                            'duedays'          => $po->duedays,
                            'le_code'          => $po->le_code,
                            'Supplier'         => $po->business_legal_name,
                            'requested_amount' => $po->requested_amount,
                            'approved_amount'  => $po->approved_amount,
                            'req_amt'          => $po->req_amt,
                            'appr_amt'         => $po->appr_amt,
                            'pending_amount'   => $pendingAmtVal,
                            'shipTo'           => $shipTo,
                            'validity'         => $poValidity,
                            'poValue'          => $poValue,
                            'payment_mode'     => $payment_mode,
                            'payment_due_date' => $po->payment_due_date,
                            'tlm_name'         => $tlm_name,
                            'createdBy'        => $po->user_name,
                            'createdOn'        => ($po->po_date),
                            'Status'           => $po_status,
                            'approval_status'  => $approvalStatus,
                            'payment_status'   => $paymentStatus,
                            'Actions'          => $actions,
                            'grn_value'        => ($po->grn_value),
                            'po_grn_diff'      => $po->po_grn_diff,
                            'grn_created'      => $po->grn_created,
                            'po_so_order_link' => $po_so_order_link,
                            'comments'         => $comments,
                            'state_name'          => $po->state_name,
                            'city'                => $po->city,
                            'business_legal_name' => $po->business_legal_name
                        );
                    } elseif( $status == $this->_approvalStatuslist['processing_with_bank'] ) {
                        // Get the dropdowns
                        $ledgerAccounts = $this->_poModel->getTallyLedgerAccounts();
                        $paymentType = $this->_masterLookup->getAllOrderStatus('Payment Type', [2, 3]);
                        $bank_account_select = '';
                        $payment_type_select = '';
                        if(count($ledgerAccounts) > 0){
                            $bank_account_select = '<select class="form-control select2me" data-live-search="true" id="bank_account'.$po->request_id.'" name="bank_accounts[]">';
                            foreach($ledgerAccounts as $account){
                                if($account->show_default == 1){
                                    $bank_account_select .= '<option value="'.$account->tlm_name.'==='.$account->tlm_group.'" selected="selected">'.$account->tlm_name.'</option>';
                                } else{
                                    $bank_account_select .= '<option value="'.$account->tlm_name.'==='.$account->tlm_group.'">'.$account->tlm_name.'</option>';
                                }                                
                            }           
                            $bank_account_select .= '</select>';
                        }
                        

                        if(count($paymentType) > 0){
                            $payment_type_select = '<select class="form-control select2me" data-live-search="true" id="payment_type'.$po->request_id.'" name="payment_types[]">';
                            foreach($paymentType as $key=>$payment){
                                $payment_type_select .= '<option value="'. $key.'">'.$payment.'</option>';
                            }           
                            $payment_type_select .= '</select>';
                        }


                        $chk = '<input class="check_box" type="checkbox" name="chk[]" value="'.$po->request_id.'">
                        <input type="hidden" name="po_id[]" id="po_id'.$po->request_id.'" value="'.$po->po_id.'">
                        <input type="hidden" name="approved_amount[]" id="approved_amount'.$po->request_id.'" value="'.$po->appr_amt.'" >';
                        $comments = '<textarea class="form-control" name="comments[]" id="comment'.$po->request_id.'" cols="25" rows="5"></textarea>';
                        
                        $bank_status_select = '<select class="form-control" name="bank_status" id="bank_status'.$po->request_id.'">';
                        foreach ($this->_bank_status as $key=>$bank_status) {
                            $bank_status_select .= '<option value="'.$key.'">'.$bank_status.'</option>';
                        }
                        $bank_status_select .= '</select>';

                        $payment_date_input = '<input class="form-control" name="payment_date[]" id="payment_date'.$po->request_id.'" value="'.date('d/m/Y').'">';
                        $utr_input = '<input class="form-control" name="utr[]"  id="utr'.$po->request_id.'">';
                        
                        $dataArr[] = array(
                            'chk'                 => $chk,
                            'poId'                => $po->po_id,
                            'po_code'             => $po->po_code,                           
                            'le_code'             => $po->le_code,
                            'Supplier'            => $po->business_legal_name,
                            'approved_amount'     => $po->approved_amount,
                            'appr_amt'            => number_format((float)$po->appr_amt, 2, '.', ''),                          
                            'poValue'             => $poValue,                          
                            'Actions'             => $actions,
                            'grn_value'           => $po->grn_value,
                            'po_grn_diff'         => $po->po_grn_diff,
                            'comments'            => $comments,
                            'bank_account'        => $bank_account_select,
                            'payment_type'        => $payment_type_select,
                            'payment_status'      => $bank_status_select,
                            'payment_date'        => $payment_date_input,
                            'utr'                 => $utr_input,
                            'state_name'          => $po->state_name,
                            'city'                => $po->city,
                            'shipTo'              => $shipTo,
                            'business_legal_name' => $po->business_legal_name
                        );
                    } elseif( $status == '' ){
                        $payable = ( $po->grn_value != "" ) ? $po->grn_value : $poValue;
                        $pendingAmt = 0;
                        $pendingAmt = $payable - ($po->requested_amount + $po->approved_amount);
                        if($pendingAmt < 1){
                            $pendingAmt = 0;
                            $chk        = '';
                            $pendingAmtVal = '<input class="form-control" type="text" id="pendingAmt'.$po->po_id.'" name="pendingAmt[]" value="'.$pendingAmt.'" min="1" class="inputBorder" onkeyup="this.value = validateMaximum(this.value, '.$pendingAmt.')" size="10" disabled="disabled" >';
                        } else{
                            $pendingAmt = number_format((float)$pendingAmt, 2, '.', '');
                            $chk = '<input class="check_box" type="checkbox" name="chk[]" value="'.$po->po_id.'">';
                            $pendingAmtVal = '<input class="form-control" type="text" id="pendingAmt'.$po->po_id.'" name="pendingAmt[]" value="'.$pendingAmt.'" min="1" class="inputBorder" onkeyup="this.value = validateMaximum(this.value, '.$pendingAmt.')" size="10" >';
                        }
                        
                        //$pendingAmt    = number_format((float)$pendingAmt, 2, '.', '');
                        
                        $dataArr[] = array(
                            'chk'              => $chk,
                            'poId'             => $po->po_id,
                            'po_code'          => $po->po_code,
                            'duedays'          => $po->duedays,
                            'le_code'          => $po->le_code,
                            'Supplier'         => $po->business_legal_name,
                            'requested_amount' => $po->requested_amount,
                            'approved_amount'  => $po->approved_amount,
                            //'rejected_amount'  => $po->req_amt,
                            'payable'          => $payable,
                            'pending_amount'   => $pendingAmtVal,
                            'shipTo'           => $shipTo,
                            'validity'         => $poValidity,
                            'poValue'          => $poValue,
                            'payment_mode'     => $payment_mode,
                            'payment_due_date' => $po->payment_due_date,
                            'tlm_name'         => $tlm_name,
                            'createdBy'        => $po->user_name,
                            'createdOn'        => ($po->po_date),
                            'Status'           => $po_status,
                            'approval_status'  => $approvalStatus,
                            'payment_status'   => $paymentStatus,
                            'Actions'          => $actions,
                            'grn_value'        => ($po->grn_value),
                            'po_grn_diff'      => $po->po_grn_diff,
                            'grn_created'      => $po->grn_created,
                            'po_so_order_link' => $po_so_order_link,
                            'comments'         => $comments,
                            'state_name'          => $po->state_name,
                            'city'                => $po->city,
                            'business_legal_name' => $po->business_legal_name
                        );
                    } else{
                        $payable = ( $po->grn_value != "" ) ? $po->grn_value : $poValue;
                        $chk = '<input class="check_box" type="checkbox" name="chk[]" value="'.$po->po_id.'">';

                        $dataArr[] = array(
                            'chk'              => $chk,
                            'poId'             => $po->po_id,
                            'po_code'          => $po->po_code,
                            'duedays'          => $po->duedays,
                            'le_code'          => $po->le_code,
                            'Supplier'         => $po->business_legal_name,
                            'requested_amount' => $po->requested_amount,
                            'approved_amount'  => $po->approved_amount,
                            //'rejected_amount'  => $po->req_amt,                           
                            'payable'          => $payable,
                            'shipTo'           => $shipTo,
                            'validity'         => $poValidity,
                            'poValue'          => $poValue,
                            'payment_mode'     => $payment_mode,
                            'payment_due_date' => $po->payment_due_date,
                            'tlm_name'         => $tlm_name,
                            'createdBy'        => $po->user_name,
                            'createdOn'        => ($po->po_date),
                            'Status'           => $po_status,
                            'approval_status'  => $approvalStatus,
                            'payment_status'   => $paymentStatus,
                            'Actions'          => $actions,
                            'grn_value'        => ($po->grn_value),
                            'po_grn_diff'      => $po->po_grn_diff,
                            'grn_created'      => $po->grn_created,
                            'po_so_order_link' => $po_so_order_link,
                            'comments'         => $comments,
                            'state_name'          => $po->state_name,
                            'city'                => $po->city,
                            'business_legal_name' => $po->business_legal_name
                        );
                    }
                }
                //echo '=='.time().'===';die;
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
    public function raisePaymentRequest(Request $request)
    {

        try {
            $approval_flow         = new CommonApprovalFlowFunctionModel();
            $currentStatusId       = $this->_approvalStatuslist['initiated'];
            $approvalWorkflowID    = 565;
            $approvalStatusDetails = $approval_flow->getApprovalFlowDetails('Vendor Payment Request',57202, Session::get('userId'));
            Log::info($approvalStatusDetails);
            $NextStatusId          = isset($approvalStatusDetails['data'][0]['nextStatusId'])?$approvalStatusDetails['data'][0]['nextStatusId']:57203;
            $poIds                 = $request->input('poIds');
            $requestAmt            = $request->input('requestAmt');
            $poData                = $this->_vendorPaymentRequestModel->getPoDetails($poIds);
         //   dd($poData);  
            $message = "";
            $message1 = "";
            if($poData){
                foreach ($poData as $po) {
                    $poId =  $po->po_id;
                    $bankdetails = $this->_vendorPaymentRequestModel->checkBankAcDetailsExist($poId);
                    $po_code = isset($po->po_code)?$po->po_code:"";
                    $business_legal_name = isset($po->business_legal_name)?$po->business_legal_name:"";
                    if($bankdetails){
                        $amount = $po->amount;
                        if($requestAmt[$poId] != ""){
                            $amount = $requestAmt[$poId];
                        }
                   // if( $this->_vendorPaymentRequestModel->checkRaiseRequestLimit($poId, $requestAmt) ){
                        $data = array(
                            'po_id'            => $poId,
                            'amount'           => (float)$po->amount,
                            'requested_amount' => (float)$amount,
                            'approval_status'  => $currentStatusId,
                            'created_by'       => \Session::get('userId'),
                            'created_at'       => Carbon::now()
                        );

                        VendorPaymentRequest::insert($data);
                        $paymentRequestId = DB::getPdo()->lastInsertId();
                        $approval_flow->storeWorkFlowHistory("Vendor Payment Request", $paymentRequestId, 57202, $NextStatusId, "New Payment Request raised !!", Session::get('userId'));
                        $message .= " Request submitted for Supplier Name: ".$business_legal_name." PO Code: ".$po_code."<br/>";
                   // }
                    }else{
                        $message1 .= " A/c details missing for Supplier Name: ".$business_legal_name." PO Code: ".$po_code."<br/>";
                    }
                }
                return Response::json(array("status"=>200,"message"=>$message.$message1));
            }
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
     * @return HTML         Render the view of payment request history
     */
    public function raisedPaymentRequestHistory($id){
        $history = $this->_poModel->getApprovalHistory('Vendor Payment Request', 19);
        return view('VendorPayment::paymentRequestHistory')->with('history', $history);
    }
  

    /**
     * Get all the request raised against the one purchase order
     * @param  Integer $poId Purchase Order Id
     * @return JSON    JSON array of all the request raised against one POid
     */
    public function vendorPaymentRequestRaised() {
        try{
            $postData = Input::all();
            $postData = explode(':',$postData['path']);
            $poIdArr = explode('&',$postData[1]);
            $poId = $poIdArr[0];
            $data = $this->_vendorPaymentRequestModel->getPORequestDetail($poId);
           
            return Response::json(array(
                'data' => $data
            ));
        }
        catch(Exception $e) {
            return Response::json(array(
                'data' => array()
            ));
        }
    }

    /*
     * Get the list of payment request raised against the PO order
     * @param $status String, by default null
     * @return JSON
     */
    public function getVendorPaymentRequestList($status = null, Request $request)
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
            $poDataArr           = $this->_poModel->getPaymentRequestRaisedList($orderby_array, $filters, 0, $offset, $perpage);

            $totalPurchageOrders = $this->_poModel->getPaymentRequestRaisedList($orderby_array, $filters, 1);
            
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
                        $actions .= '<a href="/vendor/payment-request-update/'.$po->request_id.'" > <i class="fa fa-thumbs-o-up"></i></a>&nbsp;';
                    }

                    if ($detailFeature ) {
                       $actions .= '<a href="#" onclick="viewhistory('. $po->request_id .')"> <i class="fa fa-eye"></i></a>&nbsp;';
                    }
                  
                    
                    
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
                        'chk'              => $chk,
                        'poId'             => $po->po_id,
                        'duedays'          => $po->duedays,
                        'le_code'          => $po->le_code,
                        'Supplier'         => $po->business_legal_name,
                        'shipTo'           => $shipTo,
                        'validity'         => $poValidity,
                        'poValue'          => $poValue,
                        'payment_mode'     => $payment_mode,
                        'payment_due_date' => $po->payment_due_date,
                        'tlm_name'         => $tlm_name,
                        'createdBy'        => $po->user_name,
                        'createdOn'        => $po->po_date,
                        'Status'           => $po_status,
                        'approval_status'  => $approvalStatus,
                        'payment_status'   => $paymentStatus,
                        'Actions'          => $actions,
                        'grn_value'        => $po->grn_value,
                        'po_grn_diff'      => $po->po_grn_diff,
                        'grn_created'      => $po->grn_created,
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
    
    /**
     * Get the balance amount for which Payment request
     * can be raised
     * @param  Integer $po_id  Purchase Order ID
     * @param  Integer $amount Amount request against the PO
     * @return integer $balance_amount Balance amount for raising request
     */
    public function getBalanceRaiseAmount($po_id, $amount){
        $po = $this->_poModel->getPoValue($po_id);
        $balance_amount = 0;
        if( $amount <= $po->poValue ){
            $balance_amount = $po->poValue - $amount;
        }
        return $balance_amount;
    }


    public function paymentRequestStatusUpdate($request_id){
        
        $approval_flow_func = new CommonApprovalFlowFunctionModel();
        $approval_requests = $this->_vendorPaymentRequestModel->getApprRequestDetail($request_id, array('approval_status'));
        $poDetail = $this->_vendorPaymentRequestModel->getPaymentRequestDetail($request_id);
        
        $payment_status=isset($approval_requests->approval_status) ? $approval_requests->approval_status : 0;
        if($approval_requests->approval_status=='' || $approval_requests->approval_status==0){
                $payment_status=$this->_approvalStatuslist['initiated'];
        }
        $module = 'Vendor Payment Request';
        $res_approval_flow_func = $approval_flow_func->getApprovalFlowDetails($module, $payment_status, \Session::get('userId'));
        $approvalOptions = array();
        $approvalVal = array();

            
        if (isset($res_approval_flow_func["currentStatusId"]) && isset($res_approval_flow_func["data"])) {
            foreach ($res_approval_flow_func["data"] as $options) {
                $approvalOptions[$options['nextStatusId'] . ',' . $options['isFinalStep']] = $options['condition'];
            }
        }

        $approvalVal = array('current_status' => $payment_status,
            'approval_unique_id' => $request_id,
            'approval_module'    => 'Vendor Payment Request',
            'table_name'         => 'vendor_payment_request',
            'unique_column'      => 'id',
            'approvalurl'        => '/vendor/approvalSubmit'
        );

        $approvalStatus = $this->_masterLookup->getAllOrderStatus('Vendor Payment Request');
        $approvedStatus = (isset($approvalStatus[$approval_requests->approval_status])) ? $approvalStatus[$approval_requests->approval_status] : '';
        if ($approval_requests->approval_status == 1) {
            $approvedStatus = 'Shelved';
        }
        return view('VendorPayment::vendorApproval')
                ->with('approvalOptions', $approvalOptions)
                ->with('poDetail', $poDetail)        
                ->with('approvedStatus', $approvedStatus)
                ->with('approvalVal', $approvalVal);
    }


    public function approvalSubmit(Request $request) {
        try {
            $data           = input::get();
            $requestIds     = $request->input('requestIds');
            $approvedAmtArr = $request->input('approvedAmt');
            $stageStatus    = $request->input('stageStatus');
            $commentsArr    = $request->input('comments');

            if( count( $requestIds ) > 0 ){
                for($i=0; $i<count($requestIds); $i++){
                    $request_id  = $requestIds[$i];
                    $approvedAmt = $approvedAmtArr[$request_id];
                    $comments    = $commentsArr[$request_id];                  
                     
                    $approval_flow_func = new CommonApprovalFlowFunctionModel();
                    $approval_requests = $this->_vendorPaymentRequestModel->getApprRequestDetail($request_id, array('approval_status'));                        
                    $payment_status=isset($approval_requests->approval_status) ? $approval_requests->approval_status : 0;
                    if($approval_requests->approval_status=='' || $approval_requests->approval_status==0){
                            $payment_status = $this->_approvalStatuslist['initiated'];
                    }
                    $module = 'Vendor Payment Request';
                   // $res_approval_flow_func = $approval_flow_func->getApprovalFlowDetails($module, $payment_status, \Session::get('userId'));
                   // $approvalOptions = array();
                    //$approvalVal = array();

                        
                   /* if (isset($res_approval_flow_func["currentStatusId"]) && isset($res_approval_flow_func["data"])) {
                        foreach ($res_approval_flow_func["data"] as $options) {
                            $approvalOptions[$options['nextStatusId'] . ',' . $options['isFinalStep']] = $options['condition'];
                        }
                    }*/

                    // $approvalVal = array('current_status' => $payment_status,
                    //     'approval_unique_id' => $request_id,
                    //     'approval_module'    => 'Vendor Payment Request',
                    //     'table_name'         => 'vendor_payment_request',
                    //     'unique_column'      => 'id',
                    //     'approvalurl'        => '/vendor/approvalSubmit'
                    // );

                    /*$approvalStatus = $this->_masterLookup->getAllOrderStatus('Vendor Payment Request');
                    $approvedStatus = (isset($approvalStatus[$approval_requests->approval_status])) ? $approvalStatus[$approval_requests->approval_status] : '';
                    if ($approval_requests->approval_status == 1) {
                        $approvedStatus = 'Shelved';
                    }*/

                    $approval_unique_id = $request_id;
                    $approval_status    = $stageStatus;
                    $approval_module    = 'Vendor Payment Request';
                    $current_status     = $payment_status;
                    $approval_comment   = $comments;
                    $table              = 'vendor_payment_request';
                    $unique_column      = 'id';
                    $approval_flow_func = new CommonApprovalFlowFunctionModel();
                    $status             = explode(',',$approval_status);
                    $nextStatus         = $status[0];
                    $isFinal            = $status[1];        
                    if($isFinal == 0){
                        DB::table('vendor_payment_request')->where('id', $request_id)->update(
                            ['approved_amount' => $approvedAmt, 'approval_status' => $nextStatus ]
                        );
                    }

                    $this->_vendorPaymentRequestModel->updateVendorPaymentAWF($table,$unique_column,$approval_unique_id, $approval_status);    
                    
                    $approval_flow_func->storeWorkFlowHistory($approval_module, $approval_unique_id, $current_status, $nextStatus, $approval_comment, \Session::get('userId'));   
                }
                // $response = array('status'=>200,'message'=>'Success');
                // return json_encode($response);
                return $stageStatus;
            } 

        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    public function approvalSubmit2() {
        try {
            $data               = input::get();
            $request_id         = Input::get('request_id');
            $approved_amount    = Input::get('approved_amount');
            $approval_unique_id = $data['approval_unique_id'];
            $approval_status    = $data['approval_status'];
            $approval_module    = $data['approval_module'];
            $current_status     = $data['current_status'];
            $approval_comment   = $data['approval_comment'];
            $table              = $data['table_name'];
            $unique_column      = $data['unique_column'];
            $approval_flow_func = new CommonApprovalFlowFunctionModel();
            $status             = explode(',',$approval_status);
            $nextStatus         = $status[0];
            $isFinal            = $status[1];        
            if($nextStatus == 57206 && $isFinal == 0){
                DB::table('vendor_payment_request')->where('id', $request_id)->update(['approved_amount' => $approved_amount]);
            }

            $this->_vendorPaymentRequestModel->updateVendorPaymentAWF($table,$unique_column,$approval_unique_id, $approval_status);    
            
            $approval_flow_func->storeWorkFlowHistory($approval_module, $approval_unique_id, $current_status, $nextStatus, $approval_comment, \Session::get('userId'));
            $response = array('status'=>200,'message'=>'Success');
            return json_encode($response);
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    /**
     * Export the payment request CSV for exporting with bank
     * @return File     CSV file of approved payment request
     */
    function exportExcel(Request $request){        
        try{
            $current_time = Carbon::now();
            $prIds = $request->input('prIds');
            if( count($prIds) > 0){
                $request->session()->put('export_vendor_payment_ids', $prIds);
            }

            // Call the send email function here            
            if( $request->get('download') == 'excel' ){
                $prIds = $request->session()->get('export_vendor_payment_ids');
                $fieldArr = array(
                    's.sup_account_name',
                    'vpr.id',
                    'vpr.approval_status', 
                    's.sup_account_no',
                    'vpr.approved_amount', 
                    's.sup_ifsc_code',
                    'po.po_id',
                    DB::raw('getBusinessLegalName(s.legal_entity_id) AS business_legal_name')
                );
                $query = DB::table('vendor_payment_request AS vpr')->select($fieldArr);
                $query->leftJoin('po', 'po.po_id', '=', 'vpr.po_id');
                $query->leftJoin('suppliers AS s', 's.legal_entity_id', '=', 'po.legal_entity_id');
                $query->whereRaw(' s.sup_account_no !="" ');
                if( count($prIds) > 0 ){
                    $query->whereIn('vpr.id', $prIds);  
                }
                $exceldata = $query->get()->all();
                if( count($exceldata) > 0 ){
                    $approval_flow_func= new CommonApprovalFlowFunctionModel();
                    // Updates the status
                    foreach ($exceldata as $data) {
                        VendorPaymentRequest::where('id', $data->id)->update(
                            array( 'approval_status' => $this->_approvalStatuslist['processing_with_bank'] )
                        );
                        $approval_flow_func->storeWorkFlowHistory('Vendor Payment Request', $data->id, $data->approval_status, $this->_approvalStatuslist['processing_with_bank'], 'downloaded excel data', \Session::get('userId'));
                    }
                    // Preparing the data for export
                    $headers = array();
                    Excel::create('Vendor Payment Requests - '.$current_time->toDateTimeString(), function($excel) use($headers, $exceldata) 
                    {
                        $excel->sheet("Sheet 1", function($sheet) use($headers, $exceldata)
                        {
                            $sheet->loadView('VendorPayment::export', array('exceldata' => $exceldata)); 
                        });
                    })->export('xlsx'); 
                }                  
            } else{
                $this->emailWithAttachment();
            }
        }catch(Exception $e) {
                Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return Response::json(array('status' => 404, 'Message' => Lang::get('salesorders.errorInputData')));
        }
    }
    //Complete export functionality
    function exportExcelComplete(Request $request){        
        try{
            $current_time = Carbon::now();
            $prIds = $request->input('prIds');
            if( count($prIds) > 0){
                $request->session()->put('export_vendor_payment_ids', $prIds);
            }

            // Call the send email function here            
            if( $request->get('download') == 'excel' ){
                $prIds = $request->session()->get('export_vendor_payment_ids');
                if( count($prIds) > 0 ){
                $fieldArr = array(
                    's.sup_account_name',
                    'vpr.id', 
                    's.sup_account_no',
                    'vpr.approved_amount', 
                    's.sup_ifsc_code',
                    'vpr.utr_number',
                    's.erp_code',
                    's.sup_add1',
                    's.sup_add2',
                    's.sup_bank_name',
                    'po.po_id',
                    'u.email_id',
                    'u.mobile_no',
                    DB::raw('getBusinessLegalName(s.legal_entity_id) AS business_legal_name')
                );
                $query = DB::table('vendor_payment_request AS vpr')->select($fieldArr);
                $query->leftJoin('po', 'po.po_id', '=', 'vpr.po_id');
                $query->leftJoin('suppliers AS s', 's.legal_entity_id', '=', 'po.legal_entity_id');
                $query->leftJoin('users as u','u.legal_entity_id','=','s.legal_entity_id');
                $query->whereRaw(' s.sup_account_no !="" ');
                $query->whereIn('vpr.id', $prIds);  
                }
                $exceldata = $query->get();                
                if( count($exceldata) > 0 ){
                    // Preparing the data for export
                    $headers = array();
                    Excel::create('Vendor Payment Requests - '.$current_time->toDateTimeString(), function($excel) use($headers, $exceldata) 
                    {
                        $excel->sheet("Sheet 1", function($sheet) use($headers, $exceldata)
                        {
                            $sheet->loadView('VendorPayment::completeExport', array('exceldata' => $exceldata)); 
                        });
                    })->export('xlsx'); 
                }                  
            } else{
                $this->emailWithAttachment();
            }
        }catch(Exception $e) {
                Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return Response::json(array('status' => 404, 'Message' => Lang::get('salesorders.errorInputData')));
        }
    }

    /**
     * Update the payment status as per bank
     * @return
     */
    function completePayment(Request $request){
        try {
            $data          = input::get();
            $requestIds    = $request->input('requestIds');
            $bank_accounts = $request->input('bank_accounts');
            $bank_statuses = $request->input('bank_statuses');
            $payment_dates = $request->input('payment_dates');
            $utrs          = $request->input('utrs');
            $comments      = $request->input('comments');
            $poIds         = $request->input('poIds');
            $payment_types = $request->input('payment_types');
            $approved_amounts = $request->input('approved_amounts');
            
            $approval_flow_func= new CommonApprovalFlowFunctionModel();

            if( count( $requestIds ) > 0 ){
                for($i=0; $i<count($requestIds); $i++){
                    $request_id   = $requestIds[$i];
                    $bank_account = $bank_accounts[$request_id]; 
                    $bank_status  = $bank_statuses[$request_id]; 
                    $payment_date = $payment_dates[$request_id]; 
                    $utr          = $utrs[$request_id]; 
                    $comment      = $comments[$request_id];
                    $po_id        = $poIds[$request_id];
                    $payment_type = $payment_types[$request_id];
                    $approved_amount = $approved_amounts[$request_id];       

                    $accountinfo    = explode('===', $bank_account);
                    $tlm_name       = (isset($accountinfo[0])) ? $accountinfo[0] : '';
                    $tlm_group      = (isset($accountinfo[1])) ? $accountinfo[1] : '';
                    $payment_date = date("Y-m-d H:i:s", strtotime( str_replace('/', '-', $payment_date) ));
                    if( isset($bank_status) ){                    
                        if($bank_status == 0){ // Payment Sucessful
                            $nextstatus = $this->_approvalStatuslist['completed'];
                            VendorPaymentRequest::where('id', $request_id)->update(
                                array( 
                                    'approval_status'     => $nextstatus,
                                    'ebutor_bank_account' => $tlm_name,
                                    'bank_status'         => $bank_status,
                                    'bank_payment_date'   => $payment_date,
                                    'utr_number'          => $utr,
                                    'bank_comment'        => $comment,
                                    'paid_through_group'  => $tlm_group,
                                    'transaction_type'    => $payment_type
                                )
                            );
                            $approval_flow_func->storeWorkFlowHistory('Vendor Payment Request', $request_id, 57218, $nextstatus, 'Payment completed', \Session::get('userId'));
                            // Update the PO table payment status for old database migration
                            $payment_status = $this->_vendorPaymentRequestModel->checkPaymentStatus( $po_id );
                            PurchaseOrder::where('po_id', $po_id)->update(
                                array( 'payment_status' => $payment_status )
                            );    

                            // Prepares the data for the Tally integration
                            $poDetailArr    = $this->_poModel->getPoCodeById($po_id);
                            $poCode         = explode('_',$poDetailArr->po_code);
                            $po_code        = isset($poCode[0])?$poCode[0]:0; 
                            $leId           = isset($poDetailArr->legal_entity_id) ? $poDetailArr->legal_entity_id : 0;
                            $supplierInfo   = $this->_poModel->getLegalEntityById($leId);
                          
                            
                            $leWhId         = isset($poDetailArr->le_wh_id) ? $poDetailArr->le_wh_id : 0;
                            $cost           = $this->_gdsBus->getBusinesUnitLeWhId($leWhId);
                            $parent_buId    = isset($cost->parent_bu_id)?$cost->parent_bu_id:0;
                            $costcenter     = isset($cost->cost_center)?$cost->cost_center:'Z1R1D1';
                            $cg             = $this->_gdsBus->getBusinesUnitByParentId($parent_buId);
                            $costcenter_grp = isset($cg->cost_center)?$cg->cost_center:'Z1R1';
                            $autoinit       = 0;
                            $state_code = ($supplierInfo->state_code != "") ? $supplierInfo->state_code : 'TS';

                            $data = [
                                'PayUTRCode'       => $utr,
                                'state_code'       => $state_code,
                                'TxnAmount'        => $approved_amount,
                                'TransmissionDate' => date('Y-m-d H:i:s', strtotime($payment_date)), //'2017-03-12 05-45-01',
                                'BeneName'         => (isset($supplierInfo->sup_account_name) && $supplierInfo->sup_account_name != '') ? $supplierInfo->sup_account_name : 'abc',
                                'BeneAccNum'       => (isset($supplierInfo->sup_account_no) && $supplierInfo->sup_account_no != '') ? $supplierInfo->sup_account_no : 'abc',
                                'BeneIFSCCode'     => (isset($supplierInfo->sup_ifsc_code) && $supplierInfo->sup_ifsc_code != '') ? $supplierInfo->sup_ifsc_code : 'abc',
                                'BeneBankName'     => (isset($supplierInfo->sup_bank_name) && $supplierInfo->sup_bank_name != '') ? $supplierInfo->sup_bank_name : 'abc',
                                'TxnReffIds'       => $po_id,
                                'ValueDate'        => date('Y-m-d', strtotime($poDetailArr->po_date)),
                                'TxnReffCode'      => $utr,
                                'LedgerGroup'      => $tlm_group,
                                'LedgerAccount'    => $tlm_name,
                                'CostCenter'       => $costcenter,
                                'CostCenterGroup'  => $costcenter_grp,
                                'TxnToLegalID'     => $leId,
                                'TxnToID'          => $leId,
                                'PayType'          => $payment_type,
                                'PayForModule'     => "PO",
                                'AutoInit'         => $autoinit,
                                'CreatedBy'        => \Session::get('userId')
                            ];
                            // print_r($data);
                            // echo '<br />';
                            // Inserts record in Tally Software
                            $response = $this->h2hAxisAPIController= new h2hAxisAPIController();                        
                            $response = $this->h2hAxisAPIController->sendPaymentRequestToAxis($request, $data);
                            if ($autoinit == 0 && $payment_type != 22014) {
                                $paycode = isset($response['response']) ? $response['response'] : '';
                                if ($paycode != '') {
                                    $pay_id = $response['p_pay_id'];
                                    //echo 'creating the voucher';
                                   //$this->createPaymentVoucher($paycode);
                                    app('App\Modules\PurchaseOrder\Controllers\PaymentController')->createPaymentVoucher($paycode);
                                }
                            }
                        } else{
                            $nextstatus = $this->_approvalStatuslist['failed_at_bank'];
                            VendorPaymentRequest::where('id', $request_id)->update(
                                array( 
                                    'approval_status'     => $nextstatus,
                                    'ebutor_bank_account' => $tlm_name,
                                    'bank_status'         => $bank_status,
                                    'bank_payment_date'   => $payment_date,
                                    'utr_number'          => $utr,
                                    'bank_comment'        => $comment,
                                    'paid_through_group'  => $tlm_group,
                                    'transaction_type'    => $payment_type
                                )
                            );
                            $approval_flow_func->storeWorkFlowHistory('Vendor Payment Request', $request_id, 57218, $nextstatus, 'bank status failed', \Session::get('userId'));
                        }
                        
                    }                   
                }
                $response = array('status'=>200,'message'=>'Success');
                return json_encode($response);
            }
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    /**
     * Creates a payment record in Tally
     * @param  Array $payCode Payment Code
     * @return Void
     */
 /*   public function createPaymentVoucher($payCode)
    {
        try
        {
            if($payCode !='')
            {
                $paymentDetails = $this->_poModel->getPaymentDetailsByCode($payCode);  
                if(!empty($paymentDetails))
                {
                        $grandTotal = $paymentDetails->pay_amount;
                        $voucher_type = 'Payment';
                        $voucherDate = $paymentDetails->pay_date;
                        $ref_no = ($paymentDetails->inward_code!='')?$paymentDetails->inward_code:$paymentDetails->po_code;
                        $voucher[] = array('voucher_code' => $paymentDetails->pay_code,
                            'voucher_type' => $voucher_type,
                            'voucher_date' => $voucherDate,
                            'ledger_group' => 'Sundry Creditors',
                            'ledger_account' => trim($paymentDetails->business_legal_name) . ' - ' . $paymentDetails->le_code,
                            'tran_type' => 'Dr',
                            'amount' => $grandTotal,
                            'naration' => 'Being the payment made to ' . $paymentDetails->business_legal_name
                            . ' PO No. ' . $paymentDetails->po_code . ' dated ' . $paymentDetails->poCreatedAt,
                            'cost_centre' => $paymentDetails->cost_center,
                            'cost_centre_group' => $paymentDetails->cost_center_group,
                            'reference_no' => $ref_no,
                            'is_posted' => 0,
                        );
                        $voucher[] = array('voucher_code' => $paymentDetails->pay_code,
                            'voucher_type' => $voucher_type,
                            'voucher_date' => $voucherDate,
                            'ledger_group' => $paymentDetails->ledger_group,
                            'ledger_account' => $paymentDetails->ledger_account,
                            'tran_type' => 'Cr',
                            'amount' => $grandTotal,
                            'naration' => '',
                            'cost_centre' => $paymentDetails->cost_center,
                            'cost_centre_group' => $paymentDetails->cost_center_group,
                            'reference_no' => $ref_no,
                            'is_posted' => 0,
                        );
                        $inwardModel = new Inward();
                        $inwardModel->saveVoucher($voucher);
                        //\Log::info('Voucher Created Successfully for payment '.$payCode);
                }
            }
            return;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }*/

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
     * Add the status count as zero if status in not avilable in DB
     * @param Array $data List of status with count
     */
    function addStatus($data){
        $appr_status = $this->_approvalStatuslist;
        $apprStageStatuslist = $this->_approvalStageStatuslist;
        foreach ($appr_status as $key => $value) {
            if( ! array_key_exists($value, $data) ){
                $data[$value] = 0;
            }
        }
        foreach ($apprStageStatuslist as $key => $value) {
            if( ! array_key_exists($value, $data) ){
                $data[$value] = 0;
            }
        }
        return $data;
    }

    /**
     * [emailWithAttachment description]
     * @param  [type] $poId     [description]
     * @param  [type] $po_code  [description]
     * @param  [type] $mailData [description]
     * @return [type]           [description]
     */
    public function emailWithAttachment() {
        try{
            $current_time       = Carbon::now();
            $instance           = env('MAIL_ENV');
            $subject            = 'Ebutor - Process the payment with bank';
            $body['attachment'] = array('nameSpace' => '\App\Modules\VendorPayment\VendorPaymentController','functionName'=>'downloadExcel');
            $body['file_name']  = 'Vendor Payment Requests - '.$current_time->toDateTimeString();
            $body['template']   = 'emails.po';
            $body['name']       = 'Hello All';
            $body['comment']    = 'Please check the attachment for approved payment request.';
            $toEmails           = array('basant.sharma@ebutor.com');        
            Utility::sendEmail($toEmails, $subject, $body);
        } catch (Exception $ex) {

        }
    }

    /**
     * Download the excel for the payment
     * @return file
     */
    function downloadExcel(){
        $current_time = Carbon::now();
        $prIds = $request->session()->get('export_vendor_payment_ids');
        $fieldArr = array(
            's.sup_account_name',
            'vpr.id', 
            's.sup_account_no',
            'vpr.approved_amount', 
            's.sup_ifsc_code',
            DB::raw('getBusinessLegalName(s.legal_entity_id) AS business_legal_name')
        );
        $query = DB::table('vendor_payment_request AS vpr')->select($fieldArr);
        $query->leftJoin('po', 'po.po_id', '=', 'vpr.po_id');
        $query->leftJoin('suppliers AS s', 's.legal_entity_id', '=', 'po.legal_entity_id');
        $query->whereRaw(' s.sup_account_no !="" ');
        if( count($prIds) > 0 ){
            $query->whereIn('vpr.id', $prIds);  
        }
        $exceldata = $query->get();              
        if( count($exceldata) > 0 ){           
            $headers = array();
            return Excel::create('Vendor Payment Requests - '.$current_time->toDateTimeString(), function($excel) use($headers, $exceldata) 
                {
                    $excel->sheet("Sheet 1", function($sheet) use($headers, $exceldata)
                    {
                        $sheet->loadView('VendorPayment::export', array('exceldata' => $exceldata)); 
                    });
                })->export('xlsx'); 
        }   
    }
    function exportExcelProcess(Request $request){        
        try{
            $current_time = Carbon::now();
            $prIds = $request->input('prIds');
            if( count($prIds) > 0){
                $request->session()->put('export_vendor_payment_ids', $prIds);
            }

            // Call the send email function here            
            if( $request->get('download') == 'excel' ){
                $prIds = $request->session()->get('export_vendor_payment_ids');
                if( count($prIds) > 0 ){
                    $fieldArr = array(
                         's.sup_account_name',
                        'vpr.id', 
                        's.sup_account_no',
                        'vpr.approved_amount', 
                        's.sup_ifsc_code',
                        'po.po_id',
                        DB::raw('getBusinessLegalName(s.legal_entity_id) AS business_legal_name')
                    );
                    $query = DB::table('vendor_payment_request AS vpr')->select($fieldArr);
                    $query->leftJoin('po', 'po.po_id', '=', 'vpr.po_id');
                    $query->leftJoin('suppliers AS s', 's.legal_entity_id', '=', 'po.legal_entity_id');
                    $query->whereRaw(' s.sup_account_no !="" ');
                    $query->whereIn('vpr.id', $prIds);
                    $exceldata = $query->get()->all();                
                    if( count($exceldata) > 0 ){
                        // Preparing the data for export
                        $headers = array();
                        Excel::create('Vendor Payment Requests - '.$current_time->toDateTimeString(), function($excel) use($headers, $exceldata) 
                        {
                            $excel->sheet("Sheet 1", function($sheet) use($headers, $exceldata)
                            {
                                $sheet->loadView('VendorPayment::export', array('exceldata' => $exceldata)); 
                            });
                        })->export('xlsx'); 
                    }                  
                }
            } /*else{
                $this->emailWithAttachment();
            }*/
        }catch(Exception $e) {
                Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return Response::json(array('status' => 404, 'Message' => Lang::get('salesorders.errorInputData')));
        }
    }
    public function poPaymentStatusUpdate() {
        try {
            $data = input::all();
            $requestIds = $data['requestIds'];
            $approval_status = $data['approval_status'];
            $approval_comment = $data['approval_comment'];
            $table = 'po';
            $unique_column = 'po_id';
            $approval_module = 'Purchase Order';

            $approval_flow_func= new CommonApprovalFlowFunctionModel();
            $status = explode(',',$approval_status);
            $nextStatus = $status[0];

            if( count($requestIds) > 0 ){
                foreach($requestIds as $i=>$poid){
                    $po_details = $this->_poModel->getPoCodeById($poid);    
                    $current_status = ($po_details->payment_status!="")?$po_details->payment_status:57224;

                    Log::info($approval_module.','. $poid.','. $current_status.','. $nextStatus.','. $approval_comment.','. \Session::get('userId'));

                    $this->_poModel->updateStatusAWF($table,$unique_column,$poid, $approval_status);    
                    $approval_flow_func->storeWorkFlowHistory($approval_module, $poid, $current_status, $nextStatus, $approval_comment, \Session::get('userId'));
                }
            }
            $response = array('status'=>200,'message'=>'Success');
            return json_encode($response);
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
 
 public function downloadCompleteReport(){
        try{
            $filterData = Input::get();
            $data=Input::all();
            
            $fromdate=isset($data['fdate'])?date('Y-m-d',strtotime($data['fdate'])):date('Y-m-d');
            $todate=isset($data['tdate'])?date('Y-m-d',strtotime($data['tdate'])):date('Y-m-d');
            $sup_name  = isset($data['sup_name']) ? $data['sup_name'] : ' ';
           if($sup_name == 'all'){
                $sup_name = 'null';
            }else{
                 $sup_name  = isset($data['sup_name']) ? $data['sup_name'] : ' ';
            }
            $query = "CALL  getVendorPaymentDetails('".$fromdate."','".$todate."',".$sup_name.")";
            //print_r($query);exit;
            $file_name = 'Complete_Payment_Report_' .date('Y-m-d-H-i-s').'.csv';
            $this->exportToCsv($query, $file_name);die;

        }catch(Exception $e) {
                Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return Response::json(array('status' => 404, 'Message' => Lang::get('salesorders.errorInputData')));

        }
    }
    public function exportToCsv($query, $filename, $host=null) {
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', -1);
        $host = !empty($host) ? $host : env('READ_DB_HOST');
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



}
