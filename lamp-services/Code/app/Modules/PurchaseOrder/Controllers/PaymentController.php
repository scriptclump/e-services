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
use App\Modules\Grn\Models\Inward;
use Utility;
use Lang;
use Excel;
use Config;
use Notifications;
use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;
use App\Modules\Users\Models\Users;
use App\Modules\Orders\Models\GdsBusinessUnit;
use App\Central\Repositories\RoleRepo;
use App\Modules\H2HAxis\Controllers\h2hAxisAPIController;

class PaymentController extends BaseController {
    
    protected $_poModel;
    protected $_masterLookup;
    protected $_gdsBus;
    /*
     * __construct() method is used to call model
     * @param Null
     * @return Null
     */
      
    public function __construct($forApi=0) {
                date_default_timezone_set('Asia/Kolkata');
                $this->middleware(function ($request, $next) use($forApi) {
                    if (!Session::has('userId') && $forApi==0) {
                        Redirect::to('/login')->send();
                    }
                    return $next($request);
                });
        $this->_poModel = new PurchaseOrder();
        $this->_masterLookup = new MasterLookup();
        $this->_gdsBus =new GdsBusinessUnit();
        $this->roleAccess = new RoleRepo();
        parent::Title('Purchase Orders - '.Lang::get('headings.Company'));
        $this->grid_field_db_match = array(
            'pay_code'   => 'payment.pay_code',
            'pay_for'        => 'pay_for_name',
            'pay_type'        => 'payment_type',
            'ledger_account'      => 'payment.ledger_account',
            'pay_amount'      => 'payment.pay_amount',
            'pay_date'       => 'payment.pay_date',
            'txn_reff_code'      => 'payment.txn_reff_code',
            'pay_utr_code'      => 'payment.pay_utr_code',
            'createdBy'      => 'createdBy',
            'created_at'      => 'payment.created_at',
            'approval_status' => 'approval_status_name'
        );
    }
    
    /*
     * filterPaymentData() method is used to prepare filters condition from string
     * @param $filter String
     * @return Array
     */
    private function filterPaymentData($filter) {
        try {
            $stringArr = explode(' and ', $filter);
            $filterDataArr = array();
            if (is_array($stringArr)) {
                foreach ($stringArr as $data) {
                    $dataArr = explode(' ', $data);
                    if(isset($dataArr[2]) && (strlen($dataArr[2])>1 && strlen($dataArr[2])!=0)){
                        $Arrkey = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower'), '', $data));
                        $oprArr = ['eq','ne','gt','lt','ge','le'];
                        if(isset($Arrkey[0]) && in_array($dataArr[1],$oprArr)){
                            $filterDataArr[$Arrkey[0]] = array('operator' => $this->getCondOperator($dataArr[1]), 'value' => $Arrkey[2]);
                        }
                    }else{
                    if (substr_count($data, 'pay_code') && !array_key_exists('pay_code', $filterDataArr)) {
                        $poIdValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'pay_code'), '', $data));                        
                        $filterDataArr['pay_code'] = array('operator' => 'LIKE', 'value' => '%'.$poIdValArr[0].'%');
                    }
                    if (substr_count($data, 'po_code') && !array_key_exists('po_code', $filterDataArr)) {
                        $suppcodeValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'po_code'), '', $data));
                        $filterDataArr['po_code'] = array('operator' => 'LIKE', 'value' => '%'.$suppcodeValArr[0].'%');
                    }
                    if (substr_count($data, 'pay_type') && !array_key_exists('pay_type', $filterDataArr)) {
                        $suppValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'pay_type'), '', $data));
                        $filterDataArr['pay_type'] = array('operator' => 'LIKE', 'value' => '%'.$suppValArr[0].'%');
                    }
                    if (substr_count($data, 'pay_for') && !array_key_exists('pay_for', $filterDataArr)) {
                        $suppValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'pay_for'), '', $data));
                        $filterDataArr['pay_for'] = array('operator' => 'LIKE', 'value' => '%'.$suppValArr[0].'%');
                    }
                    if (substr_count($data, 'approval_status') && !array_key_exists('approval_status', $filterDataArr)) {
                        $suppValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'approval_status'), '', $data));
                        $filterDataArr['approval_status'] = array('operator' => 'LIKE', 'value' => '%'.$suppValArr[0].'%');
                    }
                    if (substr_count($data, 'ledger_account') && !array_key_exists('ledger_account', $filterDataArr)) {
                        $shipToValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'ledger_account'), '', $data));
                        $filterDataArr['ledger_account'] = array('operator' => 'LIKE', 'value' => '%'.$shipToValArr[0].'%');
                    }
                    if (substr_count($data, 'txn_reff_code') && !array_key_exists('txn_reff_code', $filterDataArr)) {
                        $tlm_nameValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'txn_reff_code'), '', $data));
                        $filterDataArr['txn_reff_code'] = array('operator' => 'LIKE', 'value' => '%'.$tlm_nameValArr[0].'%');
                    }
                    if (substr_count($data, 'pay_utr_code') && !array_key_exists('pay_utr_code', $filterDataArr)) {
                        $poStatusValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'pay_utr_code'), '', $data));
                        $filterDataArr['pay_utr_code'] = array('operator' => 'LIKE', 'value' => '%'.$poStatusValArr[0].'%');
                    }
                    if (substr_count($data, 'createdBy') && !array_key_exists('createdBy', $filterDataArr)) {
                        $createdByValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'createdBy'), '', $data));
                        $filterDataArr['createdBy'] = array('operator' => 'LIKE', 'value' => '%'.$createdByValArr[0].'%');
                    }
                    }
                    if (substr_count($data, 'pay_amount') && !array_key_exists('pay_amount', $filterDataArr)) {
                        $validityValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'pay_amount'), '', $data));
                        $filterDataArr['pay_amount'] = array('operator' => $this->getCondOperator($validityValArr[1]), 'value' => $validityValArr[2]);
                    }
                    if (substr_count($data, 'po_value') && !array_key_exists('po_value', $filterDataArr)) {
                        $validityValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'po_value'), '', $data));
                        $filterDataArr['po_value'] = array('operator' => $this->getCondOperator($validityValArr[1]), 'value' => $validityValArr[2]);
                    }
                    if (substr_count($data, 'grn_value') && !array_key_exists('grn_value', $filterDataArr)) {
                        $validityValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'grn_value'), '', $data));
                        $filterDataArr['grn_value'] = array('operator' => $this->getCondOperator($validityValArr[1]), 'value' => $validityValArr[2]);
                    }
                    if (substr_count($data, 'pay_date')) {
                        $filterDataArr['pay_date']['operator'] = $this->getCondOperator($dataArr[1]);
                        $filterDataArr['pay_date'][] = $dataArr[2];
                    }                    
                    if (substr_count($data, 'created_at')) {
                        $filterDataArr['created_at']['operator'] = $this->getCondOperator($dataArr[1]);
                        $filterDataArr['created_at'][] = $dataArr[2];
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
      
    /**
     * addPayment() method is use post entry to payments
     * @param  
     * @return JSON
     */
    public function addPayment() {
        try {
            $data = Input::all();
            //print_r($data);die;
            $po_id = $data['po_id'];
            if ($po_id != '' && $po_id > 0) {
                if (isset($data['payment_amount']) && $data['payment_amount'] > 0) {
                    $poDetailArr = $this->_poModel->getPoCodeById($po_id);
                    $poCode = explode('_',$poDetailArr->po_code);
                    $po_code = isset($poCode[0])?$poCode[0]:0;
                    $allposdata = $this->_poModel->getAllPODetailsByCode($po_code);
                    $valid_amount = 0;
                    $payed_amount = 0;
                    foreach ($allposdata as $po) {
                        $podata = $this->_poModel->getPOGRNValueByPoId($po->po_id);
                        $grnval = (isset($podata->grn_value) && $podata->grn_value != '') ? $podata->grn_value : 0;
                        $poval = (isset($podata->po_value)) ? $podata->po_value : 0;
                        if ($podata->po_status == 87002) {
                            $valid_amount += $grnval;
                        } else if ($podata->po_status == 87001) {
                            $valid_amount += $poval;
                        }
                        $payedamount = $this->_poModel->getPayedAmount($po->po_id);
                        $payed_amount +=isset($payedamount->totAmount) ? $payedamount->totAmount : 0;
                    }
                        $txn_data=$data['payment_ref'];
                        $txn_list = $this->_poModel->gettxnslist($txn_data);
                        $check_dupli = isset($data['check_dupli']) ? $data['check_dupli'] : 0;
                        if ($txn_list > 0 && $check_dupli==0) {
                            $failure_msg='Transaction has duplicate';
                            return json_encode(['status'=>'200','message'=>$failure_msg]);
                        }
                    
                    $payment_mode = isset($podata->payment_mode) ? $podata->payment_mode : 0; //1-post paid, 2-prepaid
                    
                    if ($payment_mode != 0) {
                        $amount = $payed_amount + $data['payment_amount'];
                        $payment_type = $data['payment_type'];
                        $check_payment = isset($data['check_payment']) ? $data['check_payment'] : 0;
                        if($check_payment == 0){
                            if ($amount > round($valid_amount,2) && $payment_mode==1 && $payment_type != 22014) {
                                $msg = 'Amount should not be more than Invoice/PO value';
                                $response = ['message' => $msg, 'status' => 'failed', 'code' => 400, 'response' => ''];
                                return json_encode($response);
                            }
                        }


                        $leId = isset($poDetailArr->legal_entity_id) ? $poDetailArr->legal_entity_id : 0;
                        $supplierInfo = $this->_poModel->getLegalEntityById($leId);
                        //print_r($supplierInfo);die;
                        $paid_through = isset($data['paid_through']) ? $data['paid_through'] : '';
                        $autoinit = isset($data['autoinit']) ? $data['autoinit'] : 0;
                        $accountinfo = explode('===', $paid_through);
                        $tlm_name = (isset($accountinfo[0])) ? $accountinfo[0] : '';
                        $tlm_group = (isset($accountinfo[1])) ? $accountinfo[1] : '';
                        //$url = env('H2HAxis_API');

                        $leWhId = isset($poDetailArr->le_wh_id) ? $poDetailArr->le_wh_id : 0;
                        $cost = $this->_gdsBus->getBusinesUnitLeWhId($leWhId);
                    $parent_buId = isset($cost->parent_bu_id)?$cost->parent_bu_id:0;
                    $costcenter = isset($cost->cost_center)?$cost->cost_center:'Z1R1D1';
                    $cg = $this->_gdsBus->getBusinesUnitByParentId($parent_buId);
                    $costcenter_grp = isset($cg->cost_center)?$cg->cost_center:'Z1R1';
                    
                    $whdetails =$this->roleAccess->getLEWHDetailsById($leWhId);
                    $statecode  = isset($whdetails->state_code)?$whdetails->state_code:"TS";
                        $data = [
                            'PayUTRCode' => '',
                            'TxnAmount' => $data['payment_amount'],
                            'TransmissionDate' => date('Y-m-d H-i-s', strtotime($data['transmission_date'])), //'2017-03-12 05-45-01',
                            'BeneName' => (isset($supplierInfo->sup_account_name) && $supplierInfo->sup_account_name != '') ? $supplierInfo->sup_account_name : 'abc',
                            'BeneAccNum' => (isset($supplierInfo->sup_account_no) && $supplierInfo->sup_account_no != '') ? $supplierInfo->sup_account_no : 'abc',
                            'BeneIFSCCode' => (isset($supplierInfo->sup_ifsc_code) && $supplierInfo->sup_ifsc_code != '') ? $supplierInfo->sup_ifsc_code : 'abc',
                            'BeneBankName' => (isset($supplierInfo->sup_bank_name) && $supplierInfo->sup_bank_name != '') ? $supplierInfo->sup_bank_name : 'abc',
                            'TxnReffIds' => $po_id,
                            'ValueDate' => date('Y-m-d', strtotime($poDetailArr->po_date)),
                            'TxnReffCode' => $data['payment_ref'],
                            'LedgerGroup' => $tlm_group,
                            'LedgerAccount' => $tlm_name,
                            'CostCenter' => $costcenter,
                            'CostCenterGroup' => $costcenter_grp,
                            'TxnToLegalID' => $leId,
                            'TxnToID' => $leId,
                            'PayType' => $payment_type,
                            'PayForModule' => "PO",
                            'AutoInit' => $autoinit,
                            'state_code' => $statecode,
                            'CreatedBy' => \Session::get('userId')
                        ];
                        $headers = array("cache-control: no-cache", "content-type: application/json", 'auth:E446F5E53AD8835EAA4FA63511E22');
                        //$response = Utility::sendcUrlRequest($url, $data, $headers);
                        $response = $this->h2hAxisAPIController= new h2hAxisAPIController();
                        $request = new Request();
                        $response = $this->h2hAxisAPIController->sendPaymentRequestToAxis($request, $data);
                        if ($autoinit == 0 && $payment_type != 22014) {
                            $paycode = isset($response['response']) ? $response['response'] : '';
                            if ($paycode != '') {
                                $this->createPaymentVoucher($paycode);
                            }
                        }
                    }
                } else {
                    $msg = 'Amount should not be lessthan or eqaual to zero';
                    $response = ['message' => $msg, 'status' => 'failed', 'code' => 400, 'response' => ''];
                }
            } else {
                $msg = 'PO id should not be empty';
                $response = ['message' => $msg, 'status' => 'failed', 'code' => 400, 'response' => ''];
            }
            return json_encode($response);
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    /**
     * addPayment() method is use post entry to payments
     * @param  
     * @return JSON
     */
    public function addLegalPayment() {
        try {
            $data = Input::all();
            #print_r($data);die;
            $leId = isset($data['le_id'])?$data['le_id']:0;
            if(isset($data['item'])){
                $clicks=$data['clicks'];
                $clicks_cost=$data['clicks_cost'];
                $clicks_amt=$data['clicks_amt'];
                $impressions=$data['impressions'];
                $impressions_cost=$data['impressions_cost'];
                $impressions_amt=$data['impressions_amt'];
                $config_mapping_id=$data['banner_name'];
                $item=$data['item'];
                $payment_amount=$data['payment_amount'];
            }
            if ($leId != '' && $leId > 0) {
                if (isset($data['payment_amount']) && $data['payment_amount'] > 0) {
                    
                    $payment_type = $data['payment_type'];
                     $txn_data=$data['payment_ref'];
                        $txn_list = $this->_poModel->getTxnsList($txn_data);
                        $check_dupli = isset($data['check_dupli']) ? $data['check_dupli'] : 0;
                        if ($txn_list > 0 && $check_dupli==0) {
                            $failure_msg='Transaction has duplicate';
                            return json_encode(['status'=>'200','message'=>$failure_msg]);
                        }
                    $supplierInfo = $this->_poModel->getLegalEntityById($leId);
                    //print_r($supplierInfo);die;
                    $paid_through = isset($data['paid_through']) ? $data['paid_through'] : '';
                    $autoinit = isset($data['autoinit']) ? $data['autoinit'] : 0;
                    $accountinfo = explode('===', $paid_through);
                    $tlm_name = (isset($accountinfo[0])) ? $accountinfo[0] : '';
                    $tlm_group = (isset($accountinfo[1])) ? $accountinfo[1] : '';
                    //$url = env('H2HAxis_API');

                    $cost_center = isset($data['cost_center']) ? $data['cost_center'] : 0;
                    $cost = $this->_gdsBus->getBusinesUnitByCostcenter($cost_center);
                    $parent_buId = isset($cost->parent_bu_id) ? $cost->parent_bu_id : 0;
                    $costcenter = isset($cost->cost_center) ? $cost->cost_center : 'Z1R1D1';
                    $cg = $this->_gdsBus->getBusinesUnitByParentId($parent_buId);
                    $costcenter_grp = isset($cg->cost_center) ? $cg->cost_center : 'Z1R1';
                    $approval_flow_func = new CommonApprovalFlowFunctionModel();
                    if (isset($data['edit_payid']) && $data['edit_payid'] > 0) {
                        $editpayid = $data['edit_payid'];
                        $payData = array(
                            'pay_date' => date('Y-m-d H-i-s', strtotime($data['transmission_date'])),
                            'pay_amount' => $data['payment_amount'],
                            'pay_for' => $data['payment_for'],
                            'pay_type' => $payment_type,
                            'txn_reff_code' => $data['payment_ref'],
                            'ledger_group' => $tlm_group,
                            'ledger_account' => $tlm_name,
                            'cost_center' => $costcenter,
                            'cost_center_group' => $costcenter_grp,
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updated_by' => trim(\Session::get('userId'))
                        );
                        $this->_poModel->updatePaymentdata($payData, $editpayid);
                        if(isset($item) && $item!=''){
                            $this->_poModel->brandPaymentDetailsInsert($clicks,$clicks_cost,$clicks_amt,$impressions,$impressions_cost,$impressions_amt,$config_mapping_id,$leId,1,$payment_amount,$editpayid,$item);
                        }elseif(isset($data['config_mapping_id']) && $data['config_mapping_id']!=''){
                            $this->_poModel->deleteBannerPopupPayments($data['config_mapping_id'],$editpayid);
                        } 
                        $paymentDetails = $this->_poModel->getPaymentDetailsById($editpayid);
                        $current_status_id = $paymentDetails->approval_status;
                        $next_status_id = 57141;
                        $table = 'payment_details';
                        $unique_column = 'pay_id';
                        $this->_poModel->updateStatusAWF($table, $unique_column, $editpayid, $next_status_id . ",0");
                        $created_by = (isset($data['created_by']) && $data['created_by'] != '') ? $data['created_by'] : \Session::get('userId');                            
                        $appr_comment = 'Payment has been modified hence moving to initiated';
                        $approval_flow_func->storeWorkFlowHistory('Other Payment', $editpayid, $current_status_id, '57141', $appr_comment, $created_by);
                        $msg = 'Updated successfully';
                        $response = ['message' => $msg, 'status' => 'success', 'code' => 200, 'response' => ''];
                    } else {
                        $data = [
                            'PayUTRCode' => '',
                            'TxnAmount' => $data['payment_amount'],
                            'TransmissionDate' => date('Y-m-d H-i-s', strtotime($data['transmission_date'])), //'2017-03-12 05-45-01',
                            'BeneName' => (isset($supplierInfo->sup_account_name) && $supplierInfo->sup_account_name != '') ? $supplierInfo->sup_account_name : 'abc',
                            'BeneAccNum' => (isset($supplierInfo->sup_account_no) && $supplierInfo->sup_account_no != '') ? $supplierInfo->sup_account_no : 'abc',
                            'BeneIFSCCode' => (isset($supplierInfo->sup_ifsc_code) && $supplierInfo->sup_ifsc_code != '') ? $supplierInfo->sup_ifsc_code : 'abc',
                            'BeneBankName' => (isset($supplierInfo->sup_bank_name) && $supplierInfo->sup_bank_name != '') ? $supplierInfo->sup_bank_name : 'abc',
                            'TxnReffIds' => $leId,
                            'ValueDate' => date('Y-m-d'),
                            'TxnReffCode' => $data['payment_ref'],
                            'LedgerGroup' => $tlm_group,
                            'LedgerAccount' => $tlm_name,
                            'CostCenter' => $costcenter,
                            'CostCenterGroup' => $costcenter_grp,
                            'TxnToLegalID' => $leId,
                            'TxnToID' => $leId,
                            'TxnForID' => $data['payment_for'],
                            'PayType' => $payment_type,
                            'PayForModule' => "",
                            'AutoInit' => $autoinit,
                            'CreatedBy' => \Session::get('userId')
                        ];
                        $headers = array("cache-control: no-cache", "content-type: application/json", 'auth:E446F5E53AD8835EAA4FA63511E22');
                        //$response = Utility::sendcUrlRequest($url, $data, $headers);
                        $response = $this->h2hAxisAPIController= new h2hAxisAPIController();
                        $request = new Request();
                        $response = $this->h2hAxisAPIController->sendPaymentRequestToAxis($request, $data);
                        $paycode = isset($response['response']) ? $response['response'] : '';
                        if ($paycode != '') {
                            $paymentDetails = $this->_poModel->getPaymentDetailByCode($paycode);
                            $payId = $paymentDetails->pay_id;
                            $approval_status = isset($paymentDetails->approval_status)?$paymentDetails->approval_status:'';
                            $created_by = (isset($data['created_by']) && $data['created_by'] != '') ? $data['created_by'] : \Session::get('userId');
                            if(isset($item) && $item!=''){
                            $this->_poModel->brandPaymentDetailsInsert($clicks,$clicks_cost,$clicks_amt,$impressions,$impressions_cost,$impressions_amt,$config_mapping_id,$leId,1,$payment_amount,$payId,$item);
                            }
                            $res_approval_flow_func = $approval_flow_func->getApprovalFlowDetails('Other Payment', '57141', $created_by);
                            if (isset($res_approval_flow_func["currentStatusId"]) && isset($res_approval_flow_func["data"])) {
                                $current_status_id = $res_approval_flow_func["currentStatusId"];
                                $next_status_id = $res_approval_flow_func["data"][0]["nextStatusId"];
                                $table = 'payment_details';
                                $unique_column = 'pay_id';
                                $this->_poModel->updateStatusAWF($table, $unique_column, $payId, $next_status_id . ",0");
                                $appr_comment = (isset($data['approval_comments'])) ? $data['approval_comments'] : 'System approval at the time of insertion';
                                $approval_flow_func->storeWorkFlowHistory('Other Payment', $payId, $current_status_id, $next_status_id, $appr_comment, $created_by);
                            }else if($approval_status==''){
                                $this->_poModel->updatePaymentdata(['approval_status'=>57141], $payId);
                            }
                        }
                    }
                } else {
                    $msg = 'Amount should not be lessthan or eqaual to zero';
                    $response = ['message' => $msg, 'status' => 'failed', 'code' => 400, 'response' => ''];
                }
            } else {
                $msg = 'LeId should not be empty';
                $response = ['message' => $msg, 'status' => 'failed', 'code' => 400, 'response' => ''];
            }
            return json_encode($response);
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    /**
     * paymentsAjaxAction() method is use to get payments list
     * @param  
     * @return JSON
     */
    public function paymentsAjaxAction($id,$module='PO') {
        try {
            $filters = array();
            $orderby_array  = "";
            $order_by  = "";
            $filterData = Input::all();
            $perpage = $filterData['pageSize'];
            if(isset($filterData['$filter'])){
                $filters = $this->filterPaymentData($filterData['$filter']);
            }
            //echo '<pre/>';print_r($filters);die;
            if (isset($filterData['$orderby'])) {             //checking for sorting
                $order = explode(' ', $filterData['$orderby']);
                $order_query_field = $order[0]; //on which field sorting need to be done
                $order_query_type = $order[1]; //sort type asc or desc
                $order_by_type = 'desc';

                if ($order_query_type == 'asc') {
                    $order_by_type = 'asc';
                }
                if (isset($this->grid_field_db_match[$order_query_field])) { //getting appropriate table field based on grid field
                    $order_by = $this->grid_field_db_match[$order_query_field];
                }
            }
            if (empty($order_by)) {
                $order_by = 'pay_id';
                $order_by_type = 'desc';
            }
            $orderby_array = $order_by . " " . $order_by_type;
            $offset = $filterData['page'];
            $paymentArr = $this->_poModel->getAllPayments($id,0,$offset, $perpage,$filters,$orderby_array,$module);
            $totalPayments = (int) $this->_poModel->getAllPayments($id,1,$offset, $perpage,$filters,$orderby_array,$module);
            //$commentStatusArr = $this->_masterLookup->getMasterLookupByCategoryName('PO Invoice Status');
            $dataArr = array();
            
            if (is_array($paymentArr)) {
                foreach ($paymentArr as $payment) {
                    $appr_status = $payment->approval_status_name;
                    
                $approval_flow_func = new CommonApprovalFlowFunctionModel();
                $res_approval_flow_func = $approval_flow_func->getApprovalFlowDetails('Other Payment', $payment->approval_status, \Session::get('userId'));
                $approvalOptions = array();
                if (isset($res_approval_flow_func["currentStatusId"]) && isset($res_approval_flow_func["data"])) {
                    foreach ($res_approval_flow_func["data"] as $options) {
                        $approvalOptions[$options['nextStatusId'] . ',' . $options['isFinalStep']] = $options['condition'];
                    }
                }
                //$approvalOptions['57146,0,58076'] = 'Comment';
                if(isset($approvalOptions) && count($approvalOptions)>0){
                    $appr_status.= '<br><button type="button" href="#pmtApprvlPopup" data-toggle="modal" data-pid="'.$payment->pay_id.'" class="btn green-meadow pmtApprv">Acknowledge</button>';
                }
                $actions='';  
                $checkfeaturefordeletepo=$this->roleAccess->checkPermissionByFeatureCode('DPOPY001'); 
                if($checkfeaturefordeletepo==1){
                    $actions = 
                        // "<a href='/payment/paymentdetails/" . $payment->pay_id . "' target='_blank'><i class='fa fa-eye' style='cursor:pointer'></i></a>&nbsp;"
                        //             . "<a href='#pmtHistory' data-toggle='modal' data-pid='" . $payment->pay_id . "' class='historyDetail'><i class='fa fa-history' style='cursor:pointer'></i></a>&nbsp;"
                        // . 
                        "<a href='javascript:void(0);' data-pid='" . $payment->pay_id . "' class='Delete deletePOPayment'><i class='fa fa-trash-o' ></i></a>&nbsp;";
                }            
                $editstatus = [57141, 57142, 57143];
                if (in_array($payment->approval_status, $editstatus)) {
                    $actions .='<a class="editpayment" href="#addPaymentModel" data-payid="' . $payment->pay_id . '" data-toggle="modal"> <i class="fa fa-pencil"></i></a>&nbsp;';
                }
                $dataArr[] = array(
                        'pay_id' => $payment->pay_id,
                        'pay_type' => $payment->payment_type,
                        'pay_code' => $payment->pay_code,
                        'po_code' => isset($payment->po_code) ? $payment->po_code : '',
                        'po_value' => isset($payment->po_value) ? $payment->po_value : '',
                        'grn_value' => isset($payment->grn_value) ? $payment->grn_value : '',
                        'ledger_account' => $payment->ledger_account,
                        'pay_amount' => round($payment->pay_amount, 2),
                        'pay_date' => $payment->pay_date,
                        'pay_utr_code' => $payment->pay_utr_code,
                        'txn_reff_code' => $payment->txn_reff_code,
                        'createdBy' => $payment->createdBy,
                        'created_at' => $payment->created_at,
                        'approval_status' => $appr_status,
                        'pay_for' => $payment->pay_for_name,
                        'actions' => $actions,
                    );
                }
            }
            return json_encode(array('data' => $dataArr, 'totalPayments' => $totalPayments));
            die();
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    /**
     * createPaymentVoucher() method is use to create payment voucher
     * @param  
     * @return JSON
     */
    public function createPaymentVoucher($payCode)
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
    }
    /**
     * createPaymentVoucher() method is use to create payment voucher
     * @param  
     * @return JSON
     */
    public function createVendorPaymentVoucher($payId)
    {
        try
        {
            if($payId !='')
            {
                $paymentDetails = $this->_poModel->getPaymentDetailsById($payId);
                //print_r($paymentDetails);die;
                if(!empty($paymentDetails))
                {
                        $grandTotal = $paymentDetails->pay_amount;
                        $voucher_type = 'Payment';
                        $voucherDate = $paymentDetails->pay_date;
                        $ref_no = $paymentDetails->pay_code;//($paymentDetails->inward_code!='')?$paymentDetails->inward_code:$paymentDetails->po_code;
                        $voucher[] = array('voucher_code' => $paymentDetails->pay_code,
                            'voucher_type' => $voucher_type,
                            'voucher_date' => $voucherDate,
                            'ledger_group' => 'Sundry Creditors',
                            'ledger_account' => trim($paymentDetails->business_legal_name) . ' - ' . $paymentDetails->le_code,
                            'tran_type' => 'Dr',
                            'amount' => $grandTotal,
                            'naration' => 'Being the payment made to ' . $paymentDetails->business_legal_name,
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
                        //\Log::info('Voucher Created Successfully for payment '.$payId);
                }
            }
            return;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }
    public function vendorPaymentDetails($Id) {
        try{
            parent::Title('Vendor Payments - '.Lang::get('headings.Company'));
            $paymentDetails = $this->_poModel->getPaymentDetailsById($Id);
            //print_r($paymentDetails);die;
            $module = 'Other Payment';
            $status = isset($paymentDetails->approval_status)?$paymentDetails->approval_status:'';
            $approvalFrom = $this->getDataApprovalForm($module, $Id, $status);
            //echo $approvalFrom;die;
            return view('PurchaseOrder::Form.paymentDetails')
                            ->with('paymentDetails', $paymentDetails)
                            ->with('approvalFrom', $approvalFrom);
        } catch (Exception $ex) {

        }            
    }
    public function vendorPaymentData($Id) {
        try{
            parent::Title('Vendor Payments - '.Lang::get('headings.Company'));
            $paymentDetails = $this->_poModel->getPaymentDetailsById($Id);
            return json_encode($paymentDetails);
        } catch (Exception $ex) {

        }            
    }
    public function getDataApprovalForm($module, $Id) {
        try {
            $paymentDetails = $this->_poModel->getPaymentDetailsById($Id);
            $status = isset($paymentDetails->approval_status)?$paymentDetails->approval_status:'';
            $approval_flow_func = new CommonApprovalFlowFunctionModel();
            if($status==''){
                $status=57141;
            }
            $res_approval_flow_func = $approval_flow_func->getApprovalFlowDetails($module, $status, \Session::get('userId'));
            $approvalOptions = array();
            $approvalVal = array();
            if (isset($res_approval_flow_func["currentStatusId"]) && isset($res_approval_flow_func["data"])) {
                foreach ($res_approval_flow_func["data"] as $options) {
                    $approvalOptions[$options['nextStatusId'] . ',' . $options['isFinalStep']] = $options['condition'];
                }
            }
            $approvalOptions['57146,0,58076'] = 'Comment';
            $approvalVal = array('current_status' => $status,
                'approval_unique_id' => $Id,
                'approval_module' => $module,
                'table_name' => 'payment_details',
                'unique_column' => 'pay_id',
            );
            return View('PurchaseOrder::Form.approvalForm')->with('approvalOptions', $approvalOptions)->with('approvalVal', $approvalVal);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    public function getApprovalHistory($module, $id) {
        try{
            $approvalHistory = $this->_poModel->getApprovalHistory($module, $id);
            
            return view('PurchaseOrder::Form.approvalHistory')
                            ->with('history', $approvalHistory);
        } catch (Exception $ex) {

        }            
    }

    public function deletePOPayment($Id) {
        try {
            $msg=$this->_poModel->deletePOPayment($Id);
            if($msg){
                $success_msg='Payment deleted successfully';
              return json_encode(['status'=>'200','message'=>$success_msg]);
            }else{
                 $success_msg='Payment record cannot be deleted';
                return json_encode(['status'=>'200','message'=>$success_msg]);
            }
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
}
