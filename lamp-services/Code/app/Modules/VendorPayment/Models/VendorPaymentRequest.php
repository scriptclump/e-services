<?php

namespace App\Modules\VendorPayment\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Grn\Models\Grn;
use App\Modules\Indent\Models\IndentModel;
use App\Modules\SerialNumber\Models\SerialNumber;
use App\Modules\Roles\Models\Role;
use Log;
use DB;
use Response;
use Session;
use Notifications;
use App\Modules\Indent\Models\LegalEntity;
use Mail;
use Utility;
use Lang;
use  App\Central\Repositories\CustomerRepo;
date_default_timezone_set('Asia/Kolkata');

class VendorPaymentRequest extends Model {
    protected $table = "vendor_payment_request";
    protected $primaryKey = 'id';
     
    /**
     * Save the payment request in the database
     * @param  IntArray $poIds Array of the purchase order ids
     * @return Array  Return Array of poIds & amount 
     */
    public function getPoDetails( $poIds ){
    	$activeStatus = 87001;
    	$fieldArr = array(
    		'po.po_id',
            'po.po_code',
            DB::raw('getBusinessLegalName(legal_entity_id) AS business_legal_name'),
            DB::raw('(select sum(po_products.sub_total) from po_products where po_products.po_id=po.po_id) as amount')            
        );
        $query = DB::table('po')->select($fieldArr);
        $query->whereIn('po.po_id', $poIds);
        //$query->where('po.po_status', $activeStatus);
        $po = $query->get()->all();
		return $po;
    }

    /**
     * Get the detail of payment request
     * @param  Integer $request_id      Request ID
     * @param  Array   $fieldArr        Fields of request ID
     * @return Array   $appr_requests   Assosiative array from request table
     */
    public function getApprRequestDetail($request_id, $fieldArr){
        $query = DB::table('vendor_payment_request')->select($fieldArr);
        $query->where('vendor_payment_request.id', $request_id);
        $appr_requests = $query->first();
        return $appr_requests;
    }

    /**
     * Get all the request raised against one PO
     * @param  Integer $poId     Purchase Order ID
     * @return Array             Array of the fields from vendor payment Request
     */
    public function getPORequestDetail($poId){
        $actions='CONCAT("<center><a href=\'javascript:void(0)\' onclick=\'viewhistory(",vpr.id,",\"Request\")\'><i class=\'fa fa-history\'></i></a></center>")';
        $data = "SELECT vpr.requested_amount,vpr.approved_amount,getMastLookupValue(vpr.approval_status) as approval_status 
                    ,CASE
                        WHEN vpr.bank_status = 0 THEN 'Successful'
                        WHEN vpr.bank_status = 1 THEN 'Failed'
                        ELSE ' '
                    END AS bank_status,
                    DATE_FORMAT(vpr.created_at, '%d/%m/%Y %h:%i:%s') AS requested_at,
                    DATE_FORMAT(vpr.approved_at, '%d/%m/%Y %h:%i:%s') AS approved_at,
                    GetUserName(vpr.created_by,2) AS requested_by,
                    GetUserName(vpr.approved_by,2) AS approved_by,
                    ".$actions." AS `Actions`
                    FROM vendor_payment_request AS vpr WHERE vpr.po_id= ".$poId;
        $requestData = DB::select(DB::raw($data));

        //$query = DB::table('vendor_payment_request as vpr')->select($fieldArr);
        //$query->where('vendor_payment_request.po_id', $poId);
        //$query->leftJoin('master_lookup', 'vendor_payment_request.approval_status', '=', 'master_lookup.value');
        //$requestData = $query->get();
        return $requestData;
    }

    /**
     * Update the vendor payment status in approval flow
     * @param  String $table              Name of the table
     * @param  String $unique_column      Name of the unique column
     * @param  Integer $approval_unique_id Name of the approval unique ID
     * @param  Integer $next_status_id    Next status ID
     * @return Void
     */
    public function updateVendorPaymentAWF($table,$unique_column,$approval_unique_id, $next_status_id){
        try{
            $status     = explode(',',$next_status_id);
            $new_status = ($status[1]==0)?$status[0]:$status[1];
            $invoice = array(
                'approval_status' => $new_status,
                'approved_by'     => \Session::get('userId'),
                'approved_at'     => date('Y-m-d H:i:s')
            );   
            
            DB::table($table)->where($unique_column, $approval_unique_id)->update($invoice);
            
        } catch (Exception $ex) {
            Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
        }
    }

    /**
     * Get the payment reqest raised records
     * @param  Integer $payment_request_id Request ID
     * @return Array                       Mixed array of raised request
     */
    public function getPaymentRequestDetail($payment_request_id){
        $fieldArr = array(
            'po.le_wh_id',
            'po.legal_entity_id',
            'vendor_payment_request.requested_amount',
            'vendor_payment_request.id AS request_id',
            'po.po_id',
            'po.po_code',
            'po.parent_id',
            'po.po_validity',
            'po.payment_mode',
            'po.payment_due_date',
            'po.tlm_name',
            'po.po_status',
            'po.approval_status as approval_status_val',
            'po.po_so_order_code',
            DB::raw('IF(po.approval_status=1,"Shelved", getMastLookupValue(po.approval_status)) as approval_status'),
            DB::raw('getMastLookupValue(po.payment_status) as payment_status'),
            'po.created_at',
            'po.po_date',
            DB::raw('(select sum(po_products.sub_total) from po_products where po_products.po_id=po.po_id) as poValue'),
            DB::raw('GetUserName(po.created_by,2) AS user_name'),
            DB::raw('(select SUM(inward.grand_total) from inward where inward.po_no=po.po_id) as grn_value'),
            DB::raw('((select sum(po_products.sub_total) from po_products where po_products.po_id=po.po_id)-(select SUM(inward.grand_total) from inward where inward.po_no=po.po_id)) as po_grn_diff'),
            DB::raw('(select inward.created_at from inward where inward.po_no=po.po_id ORDER BY created_at DESC LIMIT 1) as grn_created'),
            'currency.code as currency_code',
            'currency.symbol_left as symbol',
            'legal_entities.business_legal_name',
            'legal_entities.le_code',
            'lwh.lp_wh_name',
            'lwh.city',
            'lwh.pincode',
            'lwh.address1'
        );
        $query = DB::table('po')->select($fieldArr);
        $query->join('legal_entities', 'legal_entities.legal_entity_id', '=', 'po.legal_entity_id');
        $query->join('legalentity_warehouses as lwh', 'lwh.le_wh_id', '=', 'po.le_wh_id');
        $query->leftJoin('currency', 'currency.currency_id', '=', 'po.currency_id');
        $query->leftJoin('vendor_payment_request', 'vendor_payment_request.po_id', '=', 'po.po_id');
        $query->where('vendor_payment_request.id', $payment_request_id);
        $po = $query->first();       
        return $po;
    }

    /**
     * Get total numbers of records as per status 
     * @return Array Key value pair of status & count
     */
    function getPaymentRequestStatusCount(){
        $fieldArr = array(
            DB::raw('vendor_payment_request.approval_status, COUNT(vendor_payment_request.id) AS total_rec')
        );
        $query = DB::table('vendor_payment_request')->select($fieldArr); 
        $query->leftJoin('po','po.po_id','=','vendor_payment_request.po_id');
        $query->leftJoin('legal_entities', 'legal_entities.legal_entity_id', '=', 'po.legal_entity_id');
        
        $query->where('legal_entities.legal_entity_type_id', 1002);
        $query->whereNotIn('po.legal_entity_id', array(19980, 24766, 71976));
        $query->whereNotIn('po.approval_status', [57117]);
        $query->where(function ($query) {
            $query->whereIn('po.payment_status', [57118,57224]);
            $query->orWhereNull('po.payment_status');
        });
        $query->groupBy('vendor_payment_request.approval_status');
        $appr_requests = $query->pluck('total_rec', 'vendor_payment_request.approval_status')->all();
        return $appr_requests;
    }
       /**
     * Get total numbers of records as per status 
     * @return Array Key value pair of status & count
     */
    function getPaymentRequestStatusCountComplete($status){
        $status = 57219;
        $fieldArr = array(
            DB::raw('vendor_payment_request.approval_status, COUNT(*) AS total_rec')
        );
        $query = DB::table('vendor_payment_request')->select($fieldArr);        
        $query->groupBy('vendor_payment_request.approval_status');
        $query->where('vendor_payment_request.approval_status',$status);
        $completed = $query->pluck('total_rec', 'vendor_dpayment_request.approval_status')->first();
        return $completed;
    }

    /**
     * Check user can raise the request for certain amount or not
     * @param  Integer $po_id  Purchase Order ID
     * @param  Float $amount   Amount request for the payment
     * @return Boolean         TRUE for allowed & FALSE for not allowed
     */
    function checkRaiseRequestLimit($po_id, $amount){
        $fieldArr = array(            
            DB::raw('(select sum(po_products.sub_total) from po_products where po_products.po_id=po.po_id) as poValue'),
            DB::raw('(select SUM(inward.grand_total) from inward where inward.po_no=po.po_id) as grn_value'),
            DB::raw('SUM(vpr.requested_amount) AS total_requested_amount')
        );
        $query = DB::table('po')->select($fieldArr);
        $query->leftJoin('vendor_payment_request as vpr', 'vpr.po_id', '=', 'po.po_id');
        $query->where('po.po_id', $po_id);
        $po = $query->first(); 
        // var_dump((float)$amount);
        // var_dump((float)$po->total_requested_amount);
        // dd($po);
        $total_requested_amount = (float)$po->total_requested_amount;
        if($total_requested_amount != 0 || $total_requested_amount != ""){
            $total_requested_amount = $total_requested_amount + (float)$amount;
        }
        if( ($po->poValue > $total_requested_amount ) || ($po->grn_value > $total_requested_amount) ){
            return TRUE;
        }
        return FALSE;
    }


    /**
     * Check the payment is completed against a PO ID
     * @param  int $po_id  Purchase Order ID
     * @return int $payment_status        Payment status ID
     */
    function checkPaymentStatus($po_id){       
        $payment_status = 0;
        $fieldArr = array(            
            DB::raw('(select sum(po_products.sub_total) from po_products where po_products.po_id=po.po_id) as poValue'),
            DB::raw('(select SUM(inward.grand_total) from inward where inward.po_no=po.po_id) as grn_value'),
            DB::raw('SUM(vpr.approved_amount) AS total_approved_amount')
        );
        $query = DB::table('po')->select($fieldArr);
        $query->leftJoin('vendor_payment_request as vpr', 'vpr.po_id', '=', 'po.po_id');
        $query->where('po.po_id', $po_id);
        $po = $query->first(); 
        if(count($po) > 0){
            $amount =  ($po->grn_value == '') ? $po->poValue : 0;
            if(floor($amount) > floor($po->total_approved_amount)){
                $payment_status = 57118;
            } else{
                $payment_status = 57032;
            }        
        }        
        return $payment_status;
    }
    public function checkBankAcDetailsExist($po_id){
        try{
            $fieldArr = array(
                's.sup_account_name', 
                's.sup_account_no',
                's.sup_ifsc_code',
                'po.po_id',
                'po.po_code',
            );
            $query = DB::table('po')->select($fieldArr);
            $query->leftJoin('suppliers AS s', 's.legal_entity_id', '=', 'po.legal_entity_id');
            $query->where('po.po_id',$po_id);
            $query->whereRaw(' s.sup_account_no !="" ');
            return $query->count();
        }catch(Exception $e){

        }
    }

}
