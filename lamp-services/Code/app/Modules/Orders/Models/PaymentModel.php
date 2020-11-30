<?php

namespace App\Modules\Orders\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Orders\Models\GdsLegalEntity;
use App\Modules\Orders\Models\Invoice;
use App\Modules\Orders\Models\OrderModel;
use App\Modules\NCTTracker\Models\nctTrackerModel;
use App\Central\Repositories\CustomerRepo;
use DB;
use Session;
use Log;
use Response;
use Utility;
use Lang;
use App\Modules\Notifications\Models\NotificationsModel;
use App\Modules\Cpmanager\Controllers\MasterLookupController;
use App\Central\Repositories\RoleRepo;
use App\Modules\Cpmanager\Models\EcashModel;

class PaymentModel extends Model
{
    protected $table = "gds_orders";
    public $timestamps = false;
    protected $_invoiceModel;
    protected $_orderModel;
    protected $_roleRepo;

    public function __construct() {
        $this->_roleRepo = new RoleRepo();
        $this->_ecash= new EcashModel();
    }


    public function saveCollection($collectionData, $createdBy = '') {


        $this->_invoiceModel = new Invoice();
        $this->_orderModel = new OrderModel();

        if(empty($createdBy)) {
            $createdBy = Session('userId');
        }
        
        $PaymentMode = DB::table('master_lookup')->where('value',$collectionData['mode_of_payment'])->pluck('master_lookup_name')->all();

        if(isset($PaymentMode[0])) { 
            $PaymentMode = $PaymentMode[0]; 
        } else {
            $PaymentMode = $collectionData['mode_of_payment'];
        }

            // If collection is newly inserted collection_id returned

            $collectionDetails = $this->checkInitialCollectionEntry($collectionData['invoice'],$createdBy);

            $collection_id = $collectionDetails['collection_id'];

            //if(!$collection_id) { // If collection already exist false returned
                
            //    $collection_id = isset($collectionDetails[0]->collection_id) ? $collectionDetails[0]->collection_id : '';

            //}    


            //$collectionHistory = $this->getCollHisDetByInvoiceId($collectionData['invoice']);

            // if(!empty($collectionHistory)) { // collection is created already
            //     return false;
            // }
            $collection_amount = $collectionData['collection_amount'];            

            $rounded_amount = (isset($collectionData['rounded'])) ? $collectionData['rounded'] : 0;
            $eCashApplied = isset($collectionData['ecash_applied']) ? $collectionData['ecash_applied'] : 0;


            //$collectionDetails = $this->getCollectionDetailsByCollectionId($collection_id);

            $saveData = array('collected_amount'=> $collection_amount,
                               'rounded_amount'=>$rounded_amount,
                               'status'=>108001,
                               'discount_amt'=>$collectionData['discount_applied'],
                               'discount_type'=>$collectionData['discount_type'],
                               'discount'=>$collectionData['discount'],
                               'collection_code'=>$collectionDetails['collection_code']
                            );  
            if(isset($collectionData['collectable_amt']) && $collectionData['collectable_amt'] ==0)
                $saveData['collectable_amt'] = 0;
                          
            $lastInsertId = DB::table('collections')->where('collection_id',$collection_id)->update($saveData);
            $historyArray = array('collection_id'=>$collection_id,
                                    'collected_by'=>$collectionData['collected_by'],
                                    'payment_mode'=>$collectionData['mode_of_payment'],
                                    'proof'=>(isset($collectionData['proof']) ? $collectionData['proof'] : ''),
                                    'collection_code'=>$collectionDetails['collection_code'],
                                    'amount'=>(isset($collectionData['collection_amount']) ? $collectionData['collection_amount']: 0),
                                    'reference_no'=>(isset($collectionData['reference_num']) ? $collectionData['reference_num'] : ''),
                                        'ecash'=>$eCashApplied,
                                    'discount_amt'=>$collectionData['discount_applied'],
                                    'discount_type'=>$collectionData['discount_type'],
                                    'discount'=>$collectionData['discount'],
                                    'data'=>json_encode($collectionData));
          //  Log::info(json_encode($historyArray));

            DB::table('collection_history')->insert($historyArray);
    	return $lastInsertId;

    }

     public function saveCollectionFromApp($collectionData, $createdBy = '') {


        $InvoiceDetails = $this->getInvoicedDetail($collectionData['invoice']);
        if(empty($createdBy)) {
            $createdBy = Session('userId');
        }


        $invoiceLedgerDtl = $this->getLedgerDetailsByInvoiceId($collectionData['invoice']);

        if(empty($invoiceLedgerDtl)) {


            $saveData = array(

                                'ledger_name'=>$InvoiceDetails[0]->shop_name,
                                'invoice_id'=>$collectionData['invoice'],
                                'party_id'=>$InvoiceDetails[0]->gds_cust_id,
                                'transaction_date'=>date('Y-m-d',strtotime($collectionData['collected_on'])),
                            'reference_no'=>'',
                                'particulars'=>$InvoiceDetails[0]->order_code.':'.$InvoiceDetails[0]->invoice_code,
                                'collected_by'=>'',
                                'payment_mode'=>'',
                                'dr_amt'=>$InvoiceDetails[0]->grand_total,
                                'legal_entity_id'=>$InvoiceDetails[0]->gds_cust_id,
                                'created_by'=>$createdBy,
                                'remarks'=>'',
                                'proof'=>'',
                                'le_wh_id'=>$InvoiceDetails[0]->le_wh_id,
                                'status'=>108001

                            );

        
            DB::table('ledger')->insertGetId($saveData);


        }


        return $this->saveCollection($collectionData, $createdBy);

     }   


     public function getInvoicedDetail($invoiceId) {
        $fieldArr = array('grid.*','orders.*');
        $query = DB::table('gds_invoice_grid as grid')->select($fieldArr);
        $query->where('grid.gds_invoice_grid_id', $invoiceId);
        $query->join('gds_orders as orders', 'grid.gds_order_id', '=', 'orders.gds_order_id');
        //$query->groupBy('grid.gds_invoice_grid_id');
        //echo $query->toSql();
        //die;
        $invqty = $query->get()->all();
        return $invqty;
    }

     public function getOrderPickerDetails($data) {

        $fieldArr = array('track.*');
        $query = DB::table('gds_order_track as track')->select($fieldArr);
        $query->wherein('track.gds_order_id', $data);
        $query->where('picker_id','!=',0);
        //$query->groupBy('grid.gds_invoice_grid_id');
        //echo $query->toSql();
        //die;
        $pickers = $query->get()->all();

        if(empty($pickers)) {

            echo json_encode(array('picker_id'=>'','date'=>''));
        } else {
            echo json_encode(array('picker_id'=>$pickers[0]->picker_id,'date'=>date('m/d/Y',strtotime($pickers[0]->scheduled_piceker_date))));
        }

    }

    
    public function getAllCollectionsByOrderId($orderId,$approveAccess=false) {


            $fieldArr = array(
                                        'history.history_id','history.collected_on','history.amount','history.proof','history.reference_no',
                                        'collections.collection_id','collections.collected_amount','collections.invoice_amount','collections.return_total','collections.cancel_total','collections.discount_amt as coll_disc','gds_order_invoice.discount_amt as inv_disc','crh.remittance_code','collections.collectable_amt',
                                        DB::raw("(IF(crh.approval_status=1,'Finance Approved', getMastLookupValue(crh.approval_status))) as status"),
                                        'crh.created_at',
                                        DB::raw('getMastLookupValue(history.payment_mode) as PaymentModeLookup'),
                                        DB::raw('GetUserName(history.collected_by,2) as CollectedByName'),
                                        DB::raw('GetUserName(collections.customer_id,2) as PaidByName')

                                    );

            // prepare sql

            $query = DB::table('collections')->select($fieldArr)

                            ->join('gds_invoice_grid','gds_invoice_grid.gds_invoice_grid_id','=','collections.invoice_id')
                            ->join('gds_order_invoice','gds_order_invoice.gds_invoice_grid_id','=','gds_invoice_grid.gds_invoice_grid_id')
                            ->leftjoin('collection_history as history','history.collection_id','=','collections.collection_id')
                            ->leftjoin('remittance_mapping as rm','rm.collection_id','=','collections.collection_id')
                            ->leftjoin('collection_remittance_history as crh','crh.remittance_id','=','rm.remittance_id')
                            ->where('gds_invoice_grid.gds_order_id',$orderId)->get()->all();

            
            $Credit_Sum = 0;                
            $Debit_Sum = 0;
            $collectedAmount = 0;                

            foreach($query as $k=>$value) {

                $query[$k]->collected_on = date('d-m-Y',strtotime($query[$k]->collected_on));

                if($query[$k]->proof!='') {
                    $query[$k]->proof = '<a download href="'.$query[$k]->proof.'">Proof</a>';    
                }


                if($approveAccess) {

                    $query[$k]->edit = '<a title="Edit Collection" data-toggle="modal" collection_id="'.$query[$k]->history_id.'" href="#editCollection" class="btn editCollectionPopup">Edit</a>';

                }
 
                $collectedAmount = $collectedAmount + round($query[$k]->collected_amount,2);

            }

            if(!empty($query)) {
                

                foreach($query as $k=>$value) {

                    $collectableAmount = round($query[0]->collectable_amt);


                    $collectionDiscountAmt = round($query[0]->coll_disc,2);

                    $invoiceDiscountAmt = round($query[0]->inv_disc,2);
                    

                    $query[$k]->BalanceAmt = round($collectableAmount-$collectedAmount+$invoiceDiscountAmt-$collectionDiscountAmt,2);
                }    

            }


            return $query;


    }

    public function getOrderMarkDeliveredDetails($data) {

        $fieldArr = array('track.*');
        $query = DB::table('gds_order_track as track')->select($fieldArr);
        $query->wherein('track.gds_order_id', $data);
        $query->where('delivered_by','!=',0);
        //$query->groupBy('grid.gds_invoice_grid_id');
        //echo $query->toSql();
        //die;
        $pickers = $query->get()->all();

        if(empty($pickers)) {

            echo json_encode(array('delivered_by'=>'','delivered_on'=>date('m/d/Y')));
        } else {
            
            $delivery_date = $pickers[0]->delivery_date;

            $delivery_date = ($delivery_date!='') ?  date('m/d/Y',strtotime($delivery_date)) : date('m/d/Y');

            echo json_encode(array('delivered_by'=>$pickers[0]->delivered_by,'delivered_on'=>$delivery_date));
        }


    }
	

    public function checkInitialCollectionEntry($Invoice_Id, $created_by='') {


            $this->_invoiceModel = new Invoice();
            
            //$Collection_Invoice = DB::table('collections')->select('invoice_id')->where('invoice_id',$Invoice_Id)->get();
        

            //if(empty($Collection_Invoice)){


            

                $details=   DB::selectFromWriteConnection(DB::raw("SELECT 
                      retrn.`cust_le_id`,
                        retrn.`gds_order_id`,
                        retrn.`shop_name`,
                        retrn.`order_code`,
                        retrn.`le_wh_id`,
                        retrn.`return_id`,
                        retrn.`reference_no`,
                        retrn.`gds_invoice_grid_id`,
                        retrn.`invoice_code`,
                        retrn.`grand_total`,
                        retrn.`ret_val`,
                        cancel.`cancel_grid_id`,
                        cancel.`cancel_code`,
                        cancel.`cancel_val` 
                    FROM
                      (SELECT 
                        `orders`.`cust_le_id`,
                        `orders`.`gds_order_id`,
                        `orders`.`shop_name`,
                        `orders`.`order_code`,
                        `orders`.`le_wh_id`,
                        `returns`.`return_grid_id` as return_id,
                        `returns`.`reference_no`,
                        `grid`.`gds_invoice_grid_id`,
                        `grid`.`invoice_code`,
                        `grid`.`grand_total`,
                        SUM(returns.total) AS ret_val 
                      FROM
                        gds_orders AS orders 
                        INNER JOIN `gds_invoice_grid` AS `grid` 
                          ON `grid`.`gds_order_id` = `orders`.`gds_order_id` 
                        LEFT JOIN `gds_returns` AS `returns` 
                          ON `returns`.`gds_order_id` = `orders`.`gds_order_id` 
                      WHERE `grid`.`gds_invoice_grid_id` = $Invoice_Id 
                      GROUP BY `returns`.`return_grid_id`) AS retrn 
                      LEFT JOIN 
                        (SELECT 
                          `orders`.`gds_order_id`,
                          `cancel_grid`.`cancel_grid_id`,
                          `cancel_grid`.`cancel_code`,
                          SUM(cancels.total_price) AS cancel_val 
                        FROM
                          gds_orders AS orders 
                          JOIN `gds_cancel_grid` AS `cancel_grid` 
                            ON `cancel_grid`.`gds_order_id` = `orders`.`gds_order_id` 
                          LEFT JOIN `gds_order_cancel` AS `cancels` 
                            ON `cancels`.`cancel_grid_id` = `cancel_grid`.`cancel_grid_id` 
                        GROUP BY `cancel_grid`.`cancel_grid_id`) AS cancel 
                        ON cancel.gds_order_id = retrn.gds_order_id")); 

                if($created_by=='') {

                    $created_by = Session('userId');

                }
                

                //if(!empty($details)) {
                    $whdetails =$this->_roleRepo->getLEWHDetailsById($details[0]->le_wh_id);
                    $statecode  = isset($whdetails->state_code)?$whdetails->state_code:"TS";
                    $collection_code = $this->_orderModel->getRefCode('RE',$statecode);
                    $amount_collectable=$details[0]->grand_total-$details[0]->ret_val;

                    $saveData = array(

                                    'customer_id'=>$details[0]->cust_le_id,
                                    'customer_name'=>$details[0]->shop_name,
                                    'collection_code'=>$collection_code,

                                    'invoice_id'=>$details[0]->gds_invoice_grid_id,
                                    'invoice_code'=>$details[0]->invoice_code,
                                    'invoice_amount'=>$details[0]->grand_total,

                                    'gds_order_id'=>$details[0]->gds_order_id,
                                    'order_code'=>$details[0]->order_code,

                                    'return_id'=>$details[0]->return_id,
                                    'return_total'=>$details[0]->ret_val,
                                    'return_code'=>$details[0]->reference_no,
                                    'collectable_amt'=>$amount_collectable,
                                    'cancel_id'=>$details[0]->cancel_grid_id,
                                    'cancel_total'=>$details[0]->cancel_val,
                                    'cancel_code'=>$details[0]->cancel_code,
                                    'le_wh_id'=>$details[0]->le_wh_id,


                                    'created_by'=>$created_by,
                                    'status'=>108001

                                );

                /*$saveData = array(

                                    'ledger_name'=>$InvoiceDetails[0]->shop_name,
                                    'invoice_id'=>$Invoice_Id->gds_invoice_grid_id,
                                    'party_id'=>$InvoiceDetails[0]->gds_cust_id,
                                    'transaction_date'=>date('Y-m-d'),
                                    'reference_no'=>$orderId,
                                    'particulars'=>$InvoiceDetails[0]->order_code.':'.$InvoiceDetails[0]->invoice_code,
                                    'payment_mode'=>'',
                                    'dr_amt'=>$Invoice_Amt,
                                    'legal_entity_id'=>$InvoiceDetails[0]->gds_cust_id,
                                    'created_by'=>$createdBy,
                                    'remarks'=>'',
                                    'proof'=>'',
                                    'le_wh_id'=>$InvoiceDetails[0]->le_wh_id,
                                    'status'=>108001

                                );

                DB::table('ledger')->insertGetId($saveData);*/

                $collection_id =  DB::table('collections')->insertGetId($saveData);

                return array('collection_id'=>$collection_id,'collection_code'=>$collection_code);
                //}


            //}

            return false;
    }

    public function getLedgerDetailsByInvoiceId_Old($invoiceId) {
            try {


                    $fieldArr = array('*');
                    $query = DB::table('ledger')->select($fieldArr);
                    $query->where('invoice_id',$invoiceId);
                    return $query->get()->all();



                } catch (Exception $e) {

                Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            }
    }

    public function getLedgerDetailsByInvoiceId($invoiceId) {
            try {


                    $fieldArr = array('*');
                    $query = DB::table('collections')->select($fieldArr);
                    $query->where('invoice_id',$invoiceId);
                    return $query->get()->all();



                } catch (Exception $e) {

                Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            }
    }

    public function getCollectionDetailsByCollectionId($collectionId) {
            try {

                    $fieldArr = array('*');
                    $query = DB::table('collections')->select($fieldArr,'',false);
                    $query->where('collection_id',$collectionId);
                    return $query->get()->all();

                } catch (Exception $e) {

                Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            }
    }

    public function getTotalPaymentsByOrderId($orderId) {
        $fieldArr = array(DB::raw('COUNT(collections.invoice_id) as totPayments'));
        $query = DB::table('collections')->select($fieldArr);
        $query->join('gds_invoice_grid as invoice', 'invoice.gds_invoice_grid_id','=','collections.invoice_id');
        $query->where('invoice.gds_order_id', $orderId);
        $row = $query->first();
        return isset($row->totPayments) ? (int)$row->totPayments : 0;
    }

    public function getCollectionByCollectionHistoryId($collectionHistoryId) {
        try {

                return DB::table('collections')->join('collection_history','collection_history.collection_id','=','collections.collection_id')->where(array('collection_history.history_id'=>$collectionHistoryId))->first();                

            } catch (Exception $e) {

            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function updateCollection($data) {
            try {

                    $updateData = array('payment_mode'=>$data['edit_coll_mode_of_payment'],'reference_no'=>$data['edit_coll_reference_num']);

                    $query = DB::table('collection_history')->where(array('history_id'=>$data['edit_coll_collection_history_id']))->update($updateData);


                } catch (Exception $e) {

                Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            }
    }

    public function getCollHisDetByInvoiceId($invoiceId) {

            try {

                    $query = DB::table('collections')->select('*')
                            ->join('collection_history as history','history.collection_id','=','collections.collection_id')
                            ->where('collections.invoice_id',$invoiceId);
                    return $query->get()->all();

                } catch (Exception $e) {

                Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            }

    }

    public function getNctTrackDetailsByOrderId($orderId) {
        try {

                $query = DB::table('collections')->select(array('nct.nct_id'));
                $query->join('collection_history as history','history.collection_id','=','collections.collection_id');
                $query->join('nct_transcation_tracking as nct','nct.nct_history_id','=','history.history_id');
                $query->where('collections.gds_order_id',$orderId);
                return $query->first();
            } 
            catch (Exception $e) {
                Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            }
    }

    public function getPaymentPendingHistoryByOrderId($orderId, $resultType='') {
        try {
            $_objNct = new nctTrackerModel();
            $nctData = $this->getNctTrackDetailsByOrderId($orderId);
            $nct_id = isset($nctData->nct_id) ? $nctData->nct_id : 0;
            $nctDataArr  = $_objNct->getNctTrackerHistoryDetails($nct_id);
            if($resultType == 'count') {
                return count($nctDataArr);
            }
            else {
                return $nctDataArr;
            }

        }
        catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }        
    }

    public function getUnpaidOrderCount($filters) {

    	$query = DB::table('gds_orders as orders')
    		->select(array(DB::raw("COUNT(DISTINCT orders.gds_order_id) as totUnpaidOrders")))
    		->leftJoin('collections', 'collections.gds_order_id', '=', 'orders.gds_order_id')
    		->leftJoin('collection_history', 'collection_history.collection_id', '=', 'collections.collection_id')
    		->leftJoin('remittance_mapping as mapping', 'mapping.collection_id', '=', 'collections.collection_id')
    		->leftJoin('collection_remittance_history as remittance', 'remittance.remittance_id', '=', 'mapping.remittance_id')
    		->leftJoin('appr_workflow_history as hub_awh_history', function ($join) {
		            $join->on('hub_awh_history.awf_for_id', '=', 'remittance.remittance_id')->on("hub_awh_history.awf_for_type_id", '=', DB::raw(56018))->on("hub_awh_history.status_to_id", '=', DB::raw(57052));
		        	})
		    ->leftJoin('appr_workflow_history as fin_awh_history', function ($join) {
		            $join->on('fin_awh_history.awf_for_id', '=', 'remittance.remittance_id')->on("fin_awh_history.awf_for_type_id", '=', DB::raw(56018))->on("fin_awh_history.status_to_id", '=', DB::raw(57053));
		        	})
		    ->leftJoin('gds_orders_payment as payment', 'payment.gds_order_id', '=', 'orders.gds_order_id')
		    ->where('payment.payment_status_id',32003)
		    ->whereIn('orders.order_status_id', array('17007','17023'))

		    ;

            if(!empty(Session::get('business_unitid')) && Session::get('business_unitid')!=0)
            {
                $bu_id=Session::get('business_unitid');
                $userID = Session('userId');
                $globalAccess = $this->_roleRepo->checkPermissionByFeatureCode("GLBWH0001",$userID);
                if($globalAccess){
                    $data = DB::select(DB::raw("call getAllBuHierarchyByID($bu_id)"));
                }
                else{
                    $data = DB::select(DB::raw("call getBuHierarchyByID($bu_id)"));
                }
                $le_wh_ids=isset($data[0]->le_wh_ids) ? $data[0]->le_wh_ids : 0;
                $array = explode(',', $le_wh_ids);
                $hubdata = DB::table('dc_hub_mapping')->select(DB::raw('GROUP_CONCAT(hub_id) as hubids'))->whereIn('dc_id',$array)->get()->all();
                $hubdata = isset($hubdata[0]->hubids) ? $hubdata[0]->hubids : 0;
            }
		    
            if(Session::get('business_unitid')!=0 && !empty(Session::get('business_unitid')) && count($array)>0 && $le_wh_ids != "")
            {
                
                $query->whereRaw("orders.le_wh_id IN (".$le_wh_ids.")");    
            }else{
                $query->whereRaw("orders.le_wh_id IN (0)");
            }
	      	
            if(Session::get('business_unitid')!=0 && !empty(Session::get('business_unitid')) && !empty($hubdata)){
                $query->whereRaw("orders.hub_id IN (".$hubdata.")");
            }
			

			return $query->get()->all();


    }

    public function getNctOrder($filters){
            $query = DB::table('gds_orders as orders')->select(array(DB::raw('COUNT(DISTINCT orders.gds_order_id) AS tot')));
    	    $query->leftJoin('collections', 'collections.gds_order_id', '=', 'orders.gds_order_id');
			$query->leftJoin('collection_history', 'collection_history.collection_id', '=', 'collections.collection_id');
			$query->leftJoin('nct_transcation_tracking as nct', 'nct.nct_history_id', '=', 'collection_history.history_id');

            if(!empty(Session::get('business_unitid')) && Session::get('business_unitid')!=0)
            {
                $bu_id=Session::get('business_unitid');
                $userID = Session('userId');
                $globalAccess = $this->_roleRepo->checkPermissionByFeatureCode("GLBWH0001",$userID);
                if($globalAccess){
                    $data = DB::select(DB::raw("call getAllBuHierarchyByID($bu_id)"));
                }
                else{
                    $data = DB::select(DB::raw("call getBuHierarchyByID($bu_id)"));
                }
                $le_wh_ids=isset($data[0]->le_wh_ids) ? $data[0]->le_wh_ids :0;
                $array = explode(',', $le_wh_ids);
                $hubdata = DB::table('dc_hub_mapping')->select(DB::raw('GROUP_CONCAT(hub_id) as hubids'))->whereIn('dc_id',$array)->get()->all();
                $hubdata = isset($hubdata[0]->hubids) ? $hubdata[0]->hubids :0;
            }

            if(Session::get('business_unitid')!=0 && !empty(Session::get('business_unitid')) && count($array)>0 && $le_wh_ids != ""){
   
              $query->whereRaw("orders.le_wh_id IN (".$le_wh_ids.")");    
            }else{
                $query->whereRaw("orders.le_wh_id IN (0)");
            }

            if(Session::get('business_unitid')!=0 && !empty(Session::get('business_unitid')) && !empty($hubdata)){
                 $query->whereRaw("orders.hub_id IN (".$hubdata.")");
            }

            //$query->whereIn('orders.order_status_id', array('17007', '17023'));
            $query->whereNotIn('collection_history.payment_mode', array('22010', '22005', '0'));
            $query->where(function ($query) {
                    $query->whereNotIn('nct.nct_status', array('11904'));
                    $query->orWhereNull('nct.nct_status');
                });


            $result = $query->first();
            return isset($result->tot) ? (int)$result->tot : 0;
    }
    /*
     * calculateCashback is function to calculate total cashback for users and update the wallet
     * @param $gds_order_id int,$data array
     * @return json
     */
    public function calculateCashback($orderId,$invoiceId,$data,$products=array(),$ord_status_id=17007){
        try{
            
        if($orderId>0 && $invoiceId >  0){
            // $url = env('CASHBACK_URL');
            // $post_feild = ["order_id"=>$orderId];
            // $headers = array("cache-control: no-cache","content-type: multipart/form-data");
            // $response = Utility::sendcUrlRequest($url, $post_feild, $headers,0);
            $response = $this->_ecash->getIncentives($orderId,2);  
            $redeemAmt = isset($data['ecash_applied'])?$data['ecash_applied']:0;
            $OrderModel = new OrderModel();
            $invoiceDetails = $OrderModel->getInvoiceProductsById($invoiceId,$orderId);
            $order_code = isset($invoiceDetails[0]->order_code)?$invoiceDetails[0]->order_code:0;
            
            $user_id = isset($invoiceDetails[0]->cust_user_id)?$invoiceDetails[0]->cust_user_id:0;
            $order_ecash_applied = isset($invoiceDetails[0]->ecash_applied)?$invoiceDetails[0]->ecash_applied:0;
            $order_status_id = isset($invoiceDetails[0]->order_status_id)?$invoiceDetails[0]->order_status_id:0;
            $pendingCashback = $this->getPendingCashback($user_id);
	        $response['delivered_amount'] = $invoiceDetails[0]->grand_total - $data['amount_return'];            
            
           // print_r($response);die;
            // here we are not readding cashback because order instant_wallet_cashback
            $orderInfo = $OrderModel->getOrderInfo(array($orderId),array('instant_wallet_cashback','cust_le_id','cashback_amount'));
            $instant_wallet_cashback = $orderInfo[0]->instant_wallet_cashback;
            $legal_entity_id = $orderInfo[0]->cust_le_id;
            $order_cashback_amount = isset($orderInfo[0]->cashback_amount)?$orderInfo[0]->cashback_amount:0;
            if(isset($response['status']) && $response['status']==200) {                
                $cachbackusers['Customers'] = isset($invoiceDetails[0]->cust_user_id)?$invoiceDetails[0]->cust_user_id:0;
                if((isset($invoiceDetails[0]->order_created_by) && $invoiceDetails[0]->is_self==0)){
                    if(isset($response['message']['Sales Agent'])){
                        $cachbackusers['Sales Agent'] = $invoiceDetails[0]->order_created_by;
                    }else{
                        $cachbackusers['Field Force Associate'] = $invoiceDetails[0]->order_created_by;
                    }
                }
                $cachbackusers['Delivery Executive'] = isset($data['delivered_by'])?$data['delivered_by']:0;
                $deliveredAmt = isset($response['delivered_amount'])?$response['delivered_amount']:0;
                foreach($cachbackusers as $usr=>$user_id){
                    $cashBackAmt = 0;
                    if(isset($response['message'][$usr]) && $response['message'][$usr]>0) {
                        $cashBackAmt = $response['message'][$usr];
                        $this->updateUserEcash($user_id,$cashBackAmt,$deliveredAmt,$orderId,'143002','Incentive added after delivery',$ord_status_id);
                    }
                    if(($usr=='Customers') && ($cashBackAmt>0||$redeemAmt>0)){
                        $this->sendEcashSMS($user_id, $cashBackAmt,$redeemAmt,$order_code);
                    }
                }

            }else{
                Log::info('Fail or No Response from cashback api to update walet');
                Log::info($response);
            }
            $invoice_grand_total = isset($invoiceDetails[0]->grand_total)?$invoiceDetails[0]->grand_total:0;
            $amount_return = $data['amount_return'];
            $final_grand_total = $invoice_grand_total - $amount_return - $order_ecash_applied;
            if($pendingCashback >= $final_grand_total){
                $pendingCashback = $final_grand_total;
            }
            if($instant_wallet_cashback == 0){

                if($redeemAmt>0){
                    //$this->deductUserEcash($user_id,$redeemAmt,$orderId,143001);
                    Log::info($order_ecash_applied . "order_ecash_applied");
                    if($order_status_id!=17022 && $order_ecash_applied > 0){
                        $ecashdiff=$order_ecash_applied-$redeemAmt;
                        if($ecashdiff>0){
                            $eCashh = ['applied_cashback' => DB::raw('(applied_cashback-' . $ecashdiff . ')')];
                            $this->updateEcash($user_id, $eCashh);
                            DB::table('gds_invoice_grid')->where('gds_order_id', $orderId)->update(array('ecash_applied' => DB::raw('(ecash_applied-' . $ecashdiff . ')')));
                        }
                        $this->updateUserEcash($user_id,$redeemAmt,0,$orderId,143001,"Order Delivered",$ord_status_id);
                        if(isset($response['status']) && $response['status']==200) {
                        }else{
                            $cust_type = $this->getCustomerType($legal_entity_id);
                            $send_po_to_sms = DB::select(DB::raw("select `getMastLookupDescByValue`(179002) as send_po_to_sms"));
                            $send_po_to_sms = isset($send_po_to_sms[0]->send_po_to_sms) ? $send_po_to_sms[0]->send_po_to_sms : 0;
                            if(($cust_type != 1014 || $cust_type != 1016) || (($cust_type == 1014 || $cust_type == 1016) && $send_po_to_sms == 1))
                                $this->sendEcashSMS($user_id, 0,$redeemAmt,$order_code);
                        }
                    }
                }
                if($order_status_id==17022){
                    $userEcash = $this->getUserEcash($user_id);
                    if(count($userEcash)>0 && isset($userEcash->cashback)){
                        $eCash = ['applied_cashback' => DB::raw('(applied_cashback-' . $order_ecash_applied . ')')];
                        $this->updateEcash($user_id, $eCash);
                        DB::table('gds_invoice_grid')->where('gds_order_id', $orderId)->update(array('ecash_applied' => 0));
                    }
                }
                
                // pending ecash usage
                if($pendingCashback > 0 and $order_status_id !=17022){
                    $eCashh = ['cashback' => DB::raw('(cashback-' . $pendingCashback . ')')];
                    $this->updateEcash($user_id, $eCashh);
                    DB::table('gds_invoice_grid')->where('gds_order_id', $orderId)->update(array('ecash_applied' => DB::raw('(ecash_applied+' . $pendingCashback . ')')));
                    $userEcash = $this->getUserEcash($user_id);
                    $ecashHistory = ['user_id'=>$user_id,
                                'legal_entity_id'=>$legal_entity_id,
                                'order_id'=>$orderId,
                                'delivered_amount'=>0,
                                'cash_back_amount'=>$pendingCashback,
                                'balance_amount'=>$userEcash->cashback,
                                'transaction_type'=>143001,
                                'transaction_date'=>date('Y-m-d H:i:s'),
                                'order_status_id'=>$ord_status_id,
                                'comment'=>"Extra E-Cash Used!"
                                ];
                    $this->saveEcashHistory($ecashHistory);
                }
                $response = array('status'=>200,'message'=>'eCash Updated Successfully');

                    
                return json_encode($response);

            }else{

                // if instant_wallet_cashback is 1 , then we need recheck the wallet ballance and need to be deducted,in this model only one cashback is applicable if multiple exists also

                //get cashback from gds order cashback data

                $getCashbackData = $this->getAllOrderCashbackData($orderId);
                $inviceGrdiaData = $this->getInvoiceGridDataByOrderId($orderId);
                $actualCashbackAdded = ($inviceGrdiaData[0]->ecash_applied) ? $inviceGrdiaData[0]->ecash_applied : 0;
                $user_id = DB::table('users')->select("user_id")->where("legal_entity_id",$legal_entity_id)->where("is_parent",1)->first();
                $userId = isset($user_id->user_id)?$user_id->user_id:0;
                $pendingCashback = $this->getPendingCashback($userId);

                $finalEcashData = $this->calculateInstantCashback($orderId,$products,$userId);
                $walletCashBack = $finalEcashData['walletCashBack'];
                $walletCashBack1 = $finalEcashData['walletCashBack1'];
                $orderBillTotalAfterReturns = $finalEcashData['orderBillTotalAfterReturns'];
                if($walletCashBack >= $orderBillTotalAfterReturns){
                    $pendingCashback = $orderBillTotalAfterReturns;
                }
                $walletCashBack = $walletCashBack1;
                $orderBillInvoiceTotal = $finalEcashData['orderBillInvoiceTotal'];
                $invoiceActualCashBack = $finalEcashData['invoiceActualCashBack'];
                $invoiced_qty = $finalEcashData['invoiced_qty'];
                $delivered_qty = $finalEcashData['delivered_qty'];
                $ordered_qty = $finalEcashData['ordered_qty'];
                $cashbackFlag = $finalEcashData['cashbackFlag'];
                $order_cashback_amount = $finalEcashData['order_cashback_amount'];

                $transType = 143001;
                $comment ="Cashback Deducted Due To partial or Full Returns";
                // removing from wallet and entry in ecash history
                $leObj = new GdsLegalEntity();
                $le = $leObj->getUserById($userId, ['user_id','legal_entity_id']);
                $leId = (isset($le->legal_entity_id))?$le->legal_entity_id:0;
                if($delivered_qty == $invoiced_qty && $cashbackFlag == 1 && $ordered_qty == $invoiced_qty){
                    // Full deliverd
                    $eCash = ['cashback'=>DB::raw('(cashback-' . $actualCashbackAdded . ')'),'applied_cashback'=>DB::raw('(applied_cashback-' . $actualCashbackAdded . ')')];
                    $this->updateEcash($userId, $eCash);
                    if($actualCashbackAdded > 0){
                        $userEcash = $this->getUserEcash($userId);
                        Log::info("pendingCashback");
                        Log::info($pendingCashback);
                        Log::info($userEcash->cashback);
                        Log::info($actualCashbackAdded);
                        Log::info($pendingCashback >= $orderBillTotalAfterReturns);
                        if($pendingCashback >= $orderBillTotalAfterReturns){
                            $cash_back_amount = $orderBillTotalAfterReturns;
                            $finalEcashAmount = ($userEcash->cashback + $actualCashbackAdded) - $pendingCashback;
                        }else{
                            $cash_back_amount = $actualCashbackAdded + $pendingCashback;
                            $finalEcashAmount = $userEcash->cashback - $pendingCashback;
                        }
                        Log::info($finalEcashAmount);
                        $ecashHistory = ['user_id'=>$userId,
                                        'legal_entity_id'=>$leId,
                                        'order_id'=>$orderId,
                                        'delivered_amount'=>($orderBillTotalAfterReturns),
                                        'cash_back_amount'=>$cash_back_amount,
                                        'balance_amount'=>$finalEcashAmount,
                                        'transaction_type'=>$transType,
                                        'transaction_date'=>date('Y-m-d H:i:s'),
                                        'order_status_id'=>17007,
                                        'comment'=>"Order Delivered"
                                        ];
                        $this->saveEcashHistory($ecashHistory);
                        if($pendingCashback > 0){
                            $gds_invoice_grid_id = $inviceGrdiaData[0]->gds_invoice_grid_id;
                            if($pendingCashback >= $orderBillTotalAfterReturns){
                                $pendingCashback = $orderBillTotalAfterReturns - $actualCashbackAdded;
                            }
                            $updateArray = ['ecash_applied'=>$actualCashbackAdded + $pendingCashback];
                            DB::table("gds_invoice_grid")->where("gds_invoice_grid_id",$gds_invoice_grid_id)->update($updateArray);
                        }
                    }

                }else if($delivered_qty == 0){
                    // full Returns
                    if($order_cashback_amount>0){
                        $applied_ecash = $actualCashbackAdded;
                    }else{
                        $applied_ecash = $invoiceActualCashBack;
                    }
                    $eCash = ['cashback'=>DB::raw('(cashback-' . $applied_ecash . ')'),'applied_cashback'=>DB::raw('(applied_cashback-' . $actualCashbackAdded . ')')];
                    $this->updateEcash($userId, $eCash);
                    if($applied_ecash > 0){
                        $userEcash = $this->getUserEcash($userId);
                        $finalEcashAmount = $userEcash->cashback;
                        $ecashHistory = ['user_id'=>$userId,
                                        'legal_entity_id'=>$leId,
                                        'order_id'=>$orderId,
                                        'delivered_amount'=>$orderBillTotalAfterReturns,
                                        'cash_back_amount'=>$applied_ecash,
                                        'balance_amount'=>$finalEcashAmount,
                                        'transaction_type'=>$transType,
                                        'transaction_date'=>date('Y-m-d H:i:s'),
                                        'order_status_id'=>17022,
                                        'comment'=>$comment
                                        ];
                        $this->saveEcashHistory($ecashHistory);
                    }

                }else if( ($delivered_qty < $invoiced_qty  || $ordered_qty > $invoiced_qty) && $cashbackFlag == 1){
                    // partial Returns/Cancels with cashback
                    // getting cashback column
                    //revert cashback which added at the time of order
//                    Log::info($invoiceActualCashBack."----invoiceActualCashBack");
  //                  Log::info($actualCashbackAdded."-----------actualCashbackAdded");
    //                Log::info($walletCashBack."-----------walletCashBack");
                    $ecash_used = ($walletCashBack>$actualCashbackAdded)?$actualCashbackAdded:$walletCashBack;
                    $currentEcash = 0;
                    $userEcash = $this->getUserEcash($userId);
                    if(count($userEcash)>0 && isset($userEcash->cashback)){
                        $currentEcash = $userEcash->cashback;
                    }
                    //$actual_ecash = ($currentEcash - $invoiceActualCashBack);
                    $ecash_diff = ($walletCashBack>$actualCashbackAdded)?0:($actualCashbackAdded - $walletCashBack);
                    $finalEcashupdate = $currentEcash - $actualCashbackAdded;

                    if($actualCashbackAdded > 0){ 
                        //$finalEcashupdate = ($walletCashBack-floor($walletCashBack)); //instead multiple update queries taking final amount and updating at single query
                        $eCash = ['cashback'=>$finalEcashupdate,'applied_cashback'=>DB::raw('(applied_cashback-' . $actualCashbackAdded . ')')];
                        $this->updateEcash($userId, $eCash);
                        if($ecash_diff>0){
                            $ecashHistory = ['user_id'=>$userId,
                                            'legal_entity_id'=>$leId,
                                            'order_id'=>$orderId,
                                            'delivered_amount'=>$orderBillTotalAfterReturns,
                                            'cash_back_amount'=>$ecash_diff,
                                            'balance_amount'=>($currentEcash-$ecash_diff),
                                            'transaction_type'=>$transType,
                                            'transaction_date'=>date('Y-m-d H:i:s'),
                                            'comment'=>'Cashback reverted due to partial return',
                                            'order_status_id'=>0,
                                            ];
                            $this->saveEcashHistory($ecashHistory);
                        }
                    }
                    //update new cashback 
                    if($walletCashBack > 0){
                        // $eCash = ['cashback'=>DB::raw('(cashback+' . $walletCashBack . ')'),'applied_cashback'=>DB::raw('(applied_cashback+' . floor($walletCashBack) . ')')];
                        // $this->updateEcash($userId, $eCash);
                        
                       /* $ecashHistory = ['user_id'=>$userId,
                                        'legal_entity_id'=>$leId,
                                        'order_id'=>$orderId,
                                        'delivered_amount'=>$orderBillTotalAfterReturns,
                                        'cash_back_amount'=>$walletCashBack - $actual_ecash,
                                        'balance_amount'=>$walletCashBack,
                                        'transaction_type'=>143002,
                                        'transaction_date'=>date('Y-m-d H:i:s'),
                                        'comment'=>'Revised Cashback added due to partial return'
                                        ];
                        $this->saveEcashHistory($ecashHistory); */                       
                        // $eCash = ['cashback'=>DB::raw('(cashback-' . floor($walletCashBack) . ')'),'applied_cashback'=>DB::raw('(applied_cashback-' . floor($walletCashBack) . ')')];
                        // $this->updateEcash($userId, $eCash);
                        if($ecash_used>0){
                            $ecash_used += $pendingCashback;
                            $ecashHistory = ['user_id'=>$userId,
                                            'legal_entity_id'=>$leId,
                                            'order_id'=>$orderId,
                                            'delivered_amount'=>$orderBillTotalAfterReturns,
                                            'cash_back_amount'=>$ecash_used,
                                            'balance_amount'=>$finalEcashupdate - $pendingCashback,
                                            'transaction_type'=>$transType,
                                            'transaction_date'=>date('Y-m-d H:i:s'),
                                            'order_status_id'=>17023,
                                            'comment'=>'Order Delivered'
                                            ];
                            $this->saveEcashHistory($ecashHistory);
                        }
                    }
                }else if($delivered_qty < $invoiced_qty || $cashbackFlag == 0){
                    // partial Returns with no cashback
                    $eCash = ['cashback'=>DB::raw('(cashback-' . $actualCashbackAdded . ')'),'applied_cashback'=>DB::raw('(applied_cashback-' . $actualCashbackAdded . ')')];
                    $this->updateEcash($userId, $eCash);

                    if($orderBillTotalAfterReturns > 0){
                        $userEcash = $this->getUserEcash($userId);
                        $finalEcashAmount = $userEcash->cashback;
                        $ecashHistory[] = ['user_id'=>$userId,
                                        'legal_entity_id'=>$leId,
                                        'order_id'=>$orderId,
                                        'delivered_amount'=>$orderBillTotalAfterReturns,
                                        'cash_back_amount'=>$actualCashbackAdded,
                                        'balance_amount'=>$finalEcashAmount,
                                        'transaction_type'=>$transType,
                                        'transaction_date'=>date('Y-m-d H:i:s'),
                                        'comment'=>$comment,
                                        'order_status_id'=>0,
                                        ];
                        if($pendingCashback >= $orderBillTotalAfterReturns){
                            $finalEcashAmount = ($userEcash->cashback + $actualCashbackAdded) - $pendingCashback;
                        }else{
                            $finalEcashAmount = $userEcash->cashback - $pendingCashback;
                        }
                        if($pendingCashback > 0)
                            $ecashHistory[] = ['user_id'=>$userId,
                                            'legal_entity_id'=>$leId,
                                            'order_id'=>$orderId,
                                            'delivered_amount'=>$orderBillTotalAfterReturns,
                                            'cash_back_amount'=>$pendingCashback,
                                            'balance_amount'=>$finalEcashAmount,
                                            'transaction_type'=>$transType,
                                            'transaction_date'=>date('Y-m-d H:i:s'),
                                            'comment'=>"Existing Ecash Used!",
                                            'order_status_id'=>0,
                                            ];
                        $this->saveEcashHistory($ecashHistory);
                    }
                }
                // removing from invoice grid table
                // partial with cashback ------- // full returns ------ // partial with no cashback
                if($delivered_qty < $invoiced_qty || $delivered_qty == 0 || $cashbackFlag == 0 || $ordered_qty > $invoiced_qty){
                    $ecash_applied = $actualCashbackAdded;
                    if( ($delivered_qty < $invoiced_qty  || $ordered_qty > $invoiced_qty) && $cashbackFlag == 1)
                        $ecash_applied = ($walletCashBack>$actualCashbackAdded)?$actualCashbackAdded:$walletCashBack;
                  //  Log::info($cashbackFlag."---------------cashbackFlag");
                   // Log::info($ecash_applied."---------------ecash_applied");
                    if($ecash_applied > 0 || $cashbackFlag == 0){
                        $gds_invoice_grid_id = $inviceGrdiaData[0]->gds_invoice_grid_id;
                        if($cashbackFlag == 0 || $delivered_qty == 0)
                            $ecash_applied = 0;
                        if($delivered_qty > 0 && $pendingCashback > 0){
                            $ecash_applied += $pendingCashback;
                            if($ecash_applied >= $orderBillTotalAfterReturns)
                                $ecash_applied = $orderBillTotalAfterReturns; 
                        }
                        $updateArray = ['ecash_applied'=>$ecash_applied];
                        DB::table("gds_invoice_grid")->where("gds_invoice_grid_id",$gds_invoice_grid_id)->update($updateArray);
                    }
                }
                if($delivered_qty > 0){
                    if($pendingCashback >= $orderBillTotalAfterReturns){
                        $pendingCashback = $pendingCashback - $actualCashbackAdded;
                    }
                    $eCashh = ['cashback' => DB::raw('(cashback-' . $pendingCashback . ')')];
                    $this->updateEcash($userId, $eCashh);
                }
                $response = array('status'=>200,'message'=>'eCash Updated Successfully');
                return json_encode($response);
            }
        }
        } catch (Exception $ex) {
            Log::info('Cashback '.$e->getMessage() . ' => ' . $e->getTraceAsString());
        }        
    }
    /*
     * getCashbackData is function to get applied cashback data for users
     * @param $gds_order_id int,$productid int
     * @return json
     */
    public function getOrderCashbackData($orderId,$productId){
        try{
            if($orderId>0 && $productId >  0){
                $query = DB::table('gds_order_cashback_data as cd')->select('*');
                $query->where('cd.gds_order_id',$orderId);
                $query->where('cd.product_id',$productId);
                $data = $query->get()->all();
                return $data;
            }
        } catch (Exception $ex) {

        }        
    }
    /*
     * updateUserEcash is function to Update user cashback 
     * @param 
     * @return 
     */
    public function updateUserEcash($userId,$cashBackAmt,$deliveredAmt,$orderId,$transType,$comment="",$order_status=0){ //transType 143001-Debit,143002-Credit
        try{
            if($userId>0 && $userId != '' && $cashBackAmt>0){
                $userEcash = $this->getUserEcash($userId);
                if(count($userEcash)>0 && isset($userEcash->cashback)){
                    $currentEcash = $userEcash->cashback;
                    $applied_cashback = $userEcash->applied_cashback;
                    if($transType==143002){
                        $eCash = ['cashback'=>DB::raw('(cashback+' . $cashBackAmt . ')')];
                    }else if($transType==143001){
                        $eCash = ['cashback'=>DB::raw('(cashback-' . $cashBackAmt . ')'),'applied_cashback'=>DB::raw('(applied_cashback-' . $cashBackAmt . ')')];
                    }
                    $this->updateEcash($userId, $eCash);
                }else{
                    $currentEcash = 0;
                    $eCash = ['user_id'=>$userId,
                        'cashback'=>($currentEcash+$cashBackAmt)
                        ];
                    $this->insertEcash($eCash);
                }
                $leObj = new GdsLegalEntity();
                $le = $leObj->getUserById($userId, ['user_id','legal_entity_id']);
                $leId = (isset($le->legal_entity_id))?$le->legal_entity_id:0;
                $userEcash = $this->getUserEcash($userId);
                $finalEcash = $userEcash->cashback;
                $ecashHistory = ['user_id'=>$userId,
                                'legal_entity_id'=>$leId,
                                'order_id'=>$orderId,
                                'delivered_amount'=>$deliveredAmt,
                                'cash_back_amount'=>$cashBackAmt,
                                'balance_amount'=>$finalEcash,
                                'transaction_type'=>$transType,
                                'transaction_date'=>date('Y-m-d H:i:s'),
                                'order_status_id'=>$order_status,
                                'comment'=>$comment
                                ];
                $this->saveEcashHistory($ecashHistory);
            }
        } catch (Exception $ex) {
            Log::info($e);
        }
        
    }
    /*
     * deductUserEcash is function to Update user cashback 
     * @param 
     * @return 
     */
    public function deductUserEcash($userId,$cashBackAmt,$orderId,$transType){ //transType 143001-Debit,143002-Credit
        try{
            if($userId>0 && $userId != '' && $cashBackAmt>0){
                $userEcash = $this->getUserEcash($userId);
                if(count($userEcash)>0 && isset($userEcash->cashback)){
                    $currentEcash = $userEcash->cashback;
                    $applied_cashback = $userEcash->applied_cashback;
                    if($transType==143001) {
                        $eCash = ['cashback'=>DB::raw('(cashback-' . $cashBackAmt . ')')];
                    }
                    $this->updateEcash($userId, $eCash);
                }else{
                    $currentEcash = 0;
                    $eCash = ['user_id'=>$userId,
                        'cashback'=>($currentEcash-$cashBackAmt)
                        ];
                    $this->insertEcash($eCash);
                }
                $leObj = new GdsLegalEntity();
                $le = $leObj->getUserById($userId, ['user_id','legal_entity_id']);
                $leId = (isset($le->legal_entity_id))?$le->legal_entity_id:0;
                $userEcash = $this->getUserEcash($userId);
                $finalEcash = $userEcash->cashback;
                $ecashHistory = ['user_id'=>$userId,
                                'legal_entity_id'=>$leId,
                                'order_id'=>$orderId,
                                'delivered_amount'=>0,
                                'cash_back_amount'=>$cashBackAmt,
                                'balance_amount'=>$finalEcash,
                                'transaction_type'=>$transType,
                                'transaction_date'=>date('Y-m-d H:i:s'),                                
                                ];
                $this->saveEcashHistory($ecashHistory);
            }
        } catch (Exception $ex) {

        }
        
    }
    
    public function sendEcashSMS($userId, $cashBackAmt,$redeemAmt,$order_code) {
        try {
            $leObj = new GdsLegalEntity();
            $le = $leObj->getUserById($userId, ['user_id', 'mobile_no']);
            $mobile_no = (isset($le->mobile_no)) ? $le->mobile_no : 0;
            $message = '';
            $custObj = new CustomerRepo();
            $message1 = Lang::get('sms.userWalletRedeem');
            $message2 = Lang::get('sms.userCashBackAmnt');
            $message3 = Lang::get('sms.userTotalEcash');

            $message1 = str_replace('{REDEEM_AMNT}', round($redeemAmt, 2), $message1);
            $message1 = str_replace('{ORDER_CODE}', $order_code, $message1);
            
            $message2 = str_replace('{CASHBACK_AMNT}', round($cashBackAmt,2), $message2);
            
            $userEcash = $this->getUserEcash($userId);
            $currentEcash = isset($userEcash->cashback)?($userEcash->cashback-$userEcash->applied_cashback):0;

            $message3 = str_replace('{CUR_BALANCE}', round(($currentEcash), 2), $message3);
            
            $message.=($redeemAmt>0)?$message1:'';
            $message.=($cashBackAmt>0)?$message2:'';
            $message.=(($redeemAmt>0)||($cashBackAmt>0))?$message3:'';
            
            if($message!=''){
                $custObj->sendSMS(0, $userId, $mobile_no, $message,'','','');
            }
            
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function sendLocSms($userId,$invoiceId,$type='',$amount_collected=0) {
        try {
            if($type!=''){
                $leObj = new GdsLegalEntity();
                $le = $leObj->getUserById($userId, ['user_id', 'mobile_no','legal_entity_id']);            
                $mobile_no = (isset($le->mobile_no)) ? $le->mobile_no : 0;
                $custleid = (isset($le->legal_entity_id)) ? $le->legal_entity_id : 0;
                $custle = $leObj->getLegalEntityById($custleid);
                $custbuname = isset($custle->business_legal_name)?$custle->business_legal_name:'';
                $message = '';
                $custObj = new CustomerRepo();            
                $message1 = Lang::get('sms.'.$type);
                if($type=='custLOC'){
                    $message1 = str_replace('{Payment}', $amount_collected, $message1);
                }
                $message1 = str_replace('{DATE}', date('Y-m-d'), $message1);
                $userEcash = $this->getUserEcash($userId);
                $currentEcash = isset($userEcash->cashback)?($userEcash->cashback-$userEcash->applied_cashback):0;
                $creditlimit = isset($userEcash->creditlimit)?$userEcash->creditlimit:0;
                $availble_limit = ($creditlimit>0)?($creditlimit+$currentEcash):$creditlimit;
                $message1 = str_replace('{CUR_BALANCE}', round(($currentEcash), 2), $message1);
                $message1 = str_replace('{AVL_LOC}', round(($availble_limit), 2), $message1);
                $message2 = str_replace('Your', $custbuname, $message1);
                $orderdata = $this->getOrderDetailByInvoiceId($invoiceId);
                $ff_user_id = isset($orderdata->ff_id)?$orderdata->ff_id:0;
                if($message1!=''){
                    $custObj->sendSMS(0, $userId, $mobile_no, $message1,'','','');
                    $notificationObj= new NotificationsModel();
                    $userIdData= $notificationObj->getUsersByCode('LOCSMS');
                    $userIdData=json_decode(json_encode($userIdData));
                    foreach($userIdData as $user_id){
                        $legal = $leObj->getUserById($user_id, ['user_id', 'mobile_no','legal_entity_id']);
                        $mobile_no1 = (isset($legal->mobile_no)) ? $legal->mobile_no : 0;
                        $custObj->sendSMS(0, $user_id, $mobile_no1, $message2,'','','');
                    }
                }
            }
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function updateEcash($userId, $dataArr) {
        try {
            $updated = DB::table('user_ecash_creditlimit')->where('user_id', $userId)->update($dataArr);
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function insertEcash($edata){
        try{
            DB::table('user_ecash_creditlimit')->insert($edata);
        } catch (Exception $ex) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function saveEcashHistory($edata){
        try{
           // Log::info("ecash_transaction_history".json_encode($edata));
            DB::table('ecash_transaction_history')->insert($edata);
        } catch (Exception $ex) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    /*
     * getUserEcash is function to Update user cashback 
     * @param 
     * @return 
     */
    public function getUserEcash($userId){
        try{
            if($userId>0 && $userId != ''){
                $data = DB::selectFromWriteConnection(DB::raw('select * from `user_ecash_creditlimit` as `uec` where `uec`.`user_id` = '.$userId.' limit 1'));                
                return isset($data[0])?$data[0]:[];
            }
        } catch (Exception $ex) {

        }
        
    }

    public function getOrderDetailByInvoiceId($invoiceId) {

        try{
            if($invoiceId>0 && $invoiceId != ''){
                $query = DB::table('gds_invoice_grid AS grid')->select(array(
                    DB::raw('(select user_id from users where users.legal_entity_id=orders.cust_le_id and users.is_parent=1 limit 1) as user_id'),
                    DB::raw('(select SUM(gds_returns.total) from gds_returns where `gds_returns`.`gds_order_id` = `grid`.`gds_order_id`) AS ret_val'),
                    DB::raw('(select payment_method_id from gds_orders_payment where `gds_orders_payment`.`gds_order_id` = `grid`.`gds_order_id`) AS payment_method_id'),
                    'grid.grand_total AS invioce_val',
                    'grid.ecash_applied',
                    'orders.order_status_id',
                    'orders.gds_order_id',
                    'orders.created_by as ff_id')
                        );
                $query->join('gds_orders AS orders', 'orders.gds_order_id', '=', 'grid.gds_order_id');
                //$query->join('users', 'users.legal_entity_id', '=', 'orders.cust_le_id');                
                $query->where('grid.gds_invoice_grid_id',$invoiceId);
                $data = $query->first();
                return $data;
            }
        } catch (Exception $ex) {

           Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
            return false;
 
        }

    }

    /*
     * getCashbackData is function to get applied cashback data for users
     * @param $gds_order_id int int
     * @return json
     */
    public function getAllOrderCashbackData($orderId){
        try{
            if($orderId>0){
                $query = DB::table('gds_order_cashback_data as cd')->select('*');
                $query->where('cd.gds_order_id',$orderId);
                $data = $query->get()->all();
                return $data;
            }else{
                return array();
            }
        } catch (Exception $ex) {
            Log::info($ex);
        }        
    }

    public function getInvoiceGridDataByOrderId($orderId){
        try{
            if($orderId>0){
                $query = DB::table('gds_invoice_grid as gd')->select('*');
                $query->where('gd.gds_order_id',$orderId);
                $data = $query->get()->all();
                return $data;
            }else{
                return array();
            }
        } catch (Exception $ex) {
            Log::info($ex);
        }        
    }

    public function calculateInstantCashback($orderId,$products,$userId){
        //get cashback from gds order cashback data

        //Log::info("Entered in instant_wallet_cashback");
        // print("Entered in instant_wallet_cashback");
        $getCashbackData = $this->getAllOrderCashbackData($orderId);
        $inviceGrdiaData = $this->getInvoiceGridDataByOrderId($orderId);
        $this->_orderModel = new OrderModel();
        $order_data = $this->_orderModel->getOrderInfo(array($orderId),array('gds_order_id','order_code','created_at','le_wh_id','is_self','cust_le_id','cashback_amount'));
        $actualCashbackAdded = ($inviceGrdiaData[0]->ecash_applied) ? $inviceGrdiaData[0]->ecash_applied : 0;
        // print_r($actualCashbackAdded . "-- actualCashbackAdded");
        $pendingCashback = $this->getPendingCashback($userId);
        if($actualCashbackAdded <= 0 || $actualCashbackAdded == "") {
            return array("finalEcash"=>0,
                "walletCashBack"=>$pendingCashback,
                "walletCashBack1"=>0,
                "actualCashbackAdded"=>0,
                "orderBillTotalAfterReturns"=>0,
                "invoiceActualCashBack"=>0,
                "orderBillInvoiceTotal"=>0,
                "delivered_qty"=>0,
                "invoiced_qty"=>0,
                "cashbackFlag"=>0,
                "cashbackToDeduct"=>$pendingCashback,
                "orderBillActualTotal"=>0,
                'order_cashback_amount'=>0,
                "ordered_qty"=>0,
                "pendingCashback"=>$pendingCashback);
        }
        $walletCashBack=0;
        $orderBillTotalAfterReturns = 0;
        $orderBillInvoiceTotal = 0;
        $orderBillActualTotal = 0;
        // $getCashbackData = (array)$getCashbackData[0];
        // $product_min_total = $getCashbackData['product_value'];
        // $range_from = $getCashbackData['range_from'];
        // $range_to = $getCashbackData['range_to'];
        // $cashbackBrandId = $getCashbackData['brand_id'];
        // $cbk_value = $getCashbackData['cbk_value'];
        // $cap_limit = $getCashbackData['cap_limit'];
        $invoiced_qty = 0;
        $delivered_qty = 0;
        $ordered_qty = 0;
        foreach ($products as $key => $product) {
            # code...
            $product_id = $product['product_id'];
            $brand_id = DB::table("products")->select("brand_id")->where("product_id",$product_id)->first();
            $brand_id = $brand_id->brand_id; 
            $deliver_qty = $product['invoiced_qty'] - $product['return_qty'];
            $invoiced_qty += $product['invoiced_qty'];
            $delivered_qty += $deliver_qty;
            $ordered_qty += $product['ordered_qty'];
            $orderBillTotalAfterReturns += $product['singleUnitPriceWithtax'] * ($deliver_qty);
            $orderBillActualTotal += $product['singleUnitPriceWithtax'] * ($product['ordered_qty']);
            if($product['invoiced_qty'] < $product['ordered_qty'])
                $orderBillInvoiceTotal += $product['singleUnitPriceWithtax'] * ($product['ordered_qty']);
            else{
                $orderBillInvoiceTotal += $product['singleUnitPriceWithtax'] * ($product['invoiced_qty']);
            }

            $product_subtotal = $product['singleUnitPriceWithtax'] * ($deliver_qty);
            $products[$key] += ["subtotal" => $product_subtotal];
            $products[$key] += ["deliver_qty" => $deliver_qty];
           //array_unshift($products[$key], array("ok"=>9999));
        }


        $invoiceActualCashBack = 0;
        $cashbackFlag = 0;

        $productArray = array();
        foreach ($products as $product) {
            # code...
            $product_id = $product['product_id'];
            // checking return_qty is there
            // calling ecash function
            $productArray[][$product_id] = $product['singleUnitPriceWithtax'] * ($product['deliver_qty']);
            $productOrderArray[][$product_id] = $product['singleUnitPriceWithtax'] * ($product['ordered_qty']);
        }
      //  Log::info(json_encode($productArray));
        $order_date = date('Y-m-d',strtotime($order_data[0]->created_at));
        $is_self = isset($order_data[0]->is_self) ? $order_data[0]->is_self : 0;
        $order_cashback_amount = isset($order_data[0]->cashback_amount) ? $order_data[0]->cashback_amount : 0;
        // checking customer type 
        $cust_le_id = isset($order_data[0]->cust_le_id) ? $order_data[0]->cust_le_id : 0;
        $customer_type_id = $this->getCustomerType($cust_le_id);
        $master_lookup = new MasterLookupController();
        $ecashCalculated = json_decode($master_lookup->getOrderEcashValue($productArray,$order_date,$order_data[0]->le_wh_id,$customer_type_id,$is_self,$cust_le_id));
        if(isset($ecashCalculated->data) && count($ecashCalculated->data)){
            if(isset($ecashCalculated->data[0]->applyCashback) && $ecashCalculated->data[0]->applyCashback){
                $walletCashBack = $ecashCalculated->data[0]->cashback_applied;
                $cashbackFlag = 1;
            }
        }else{
            $walletCashBack = 0;
            $cashbackFlag = 0;
        }
        Log::info(json_encode($cashbackFlag));
        if($walletCashBack<=0){
            $cashbackFlag = 0;
        }
        $ecashOrder = json_decode($master_lookup->getOrderEcashValue($productOrderArray,$order_date,$order_data[0]->le_wh_id,$customer_type_id,$is_self,$cust_le_id));
        if(isset($ecashOrder->data) && count($ecashOrder->data)){
            if(isset($ecashOrder->data[0]->applyCashback) && $ecashOrder->data[0]->applyCashback){
                $invoiceActualCashBack = $ecashOrder->data[0]->cashback_applied;
            }
        }else{
            $invoiceActualCashBack = 0;
        }
        $finalEcash = ceil($walletCashBack) - $actualCashbackAdded;

        /*$cahsback_column_db = DB::table('user_ecash_creditlimit')->select("cashback")->where("user_id",$userId)->first();
        $cahsback_column_db = $cahsback_column_db->cashback;
        if($invoiceActualCashBack >= $walletCashBack){
            $invoiceActualCashBack = $walletCashBack;
        }
        $gds_invoice_column = floor($walletCashBack + ($cahsback_column_db - $invoiceActualCashBack)); 
        if($gds_invoice_column >= $walletCashBack){
            $gds_invoice_column = $walletCashBack;
        }*/
        // $walletCashBack = round($walletCashBack,2);
        $currentEcash = 0;
        $walletCashBack1 = $walletCashBack; 
        $walletCashBack += $pendingCashback; 
        if($walletCashBack >= $orderBillTotalAfterReturns ){
            $walletCashBack = $orderBillTotalAfterReturns;
        }
        $returnArray = array("finalEcash"=>(double)$finalEcash,
                "walletCashBack"=>(double)$walletCashBack,
                "walletCashBack1"=>(double)$walletCashBack1,
                "pendingCashback"=>(double)$pendingCashback,
                "actualCashbackAdded"=>(double)$actualCashbackAdded, // ecash_applied in invoivce grid
                "invoiceActualCashBack"=>(double)$invoiceActualCashBack, // cashback calculated at order creation
                "orderBillTotalAfterReturns"=>(double)$orderBillTotalAfterReturns,
                "orderBillInvoiceTotal"=>(double)$orderBillInvoiceTotal,
                "delivered_qty"=>$delivered_qty,
                "invoiced_qty"=>$invoiced_qty,
                "cashbackToDeduct"=>$walletCashBack, // cashback after returns
                "cashbackFlag"=>$cashbackFlag,
                "orderBillActualTotal"=>$orderBillActualTotal,
                'order_cashback_amount'=>$order_cashback_amount,
                "ordered_qty"=>$ordered_qty);
        Log::info(json_encode($returnArray));
        return $returnArray;
        
    }

    public function getCustomerType($cust_le_id){
        $customer_type = DB::table("retailer_flat")
                        ->select("legal_entity_type_id")
                        ->where("legal_entity_id",$cust_le_id)
                        ->first();
        return isset($customer_type->legal_entity_type_id) ? $customer_type->legal_entity_type_id : 0 ;
    }

    public function checkFreeQty($flag,$orderId,$products,$userId,$legal_entity_id){
        // $flag = 1 -- Picking Time,$flag=2 -- Delivery Time
        $this->_orderModel = new OrderModel();
        $productPickedArray = array();
        $productOrderArray = array();
        $order_pick_total = 0;
        $order_all_total = 0;
        $order_data = $this->_orderModel->getOrderInfo(array($orderId),array('gds_order_id','order_code','created_at','total'));
        $order_all_data = $this->_orderModel->getAllOrderTotal($orderId);
        $order_all_total = $order_all_data[0]->total;
        $allProductIds = $order_all_data[0]->productIds;
        $pickedIds = array();
        $pickedIdData = array();
        foreach ($products as $key => $value) {
            # code...
            $product_data = $this->_orderModel->getProductByOrderId($orderId,array($value['product_id']));
            $product_data = $product_data[0];
            $unitPrice = $product_data->unit_price;
            if($flag == 1)
                $product_qty = $value['picked_qty'];
            else if($flag == 2)
                $product_qty = $value['invoiced_qty'] - $value['return_qty'];

            $order_qty = $value['ordered_qty'];
            $product_id = $value['product_id'];
            // Log::info($product_id . " aa".$product_qty);
            if($product_qty > 0){
                $order_pick_total += $unitPrice * ($product_qty);
                array_push($pickedIds, $value['product_id']);
                $pickedIdData[$value['product_id']] = $product_qty;
            }

        }
        // $retailer_data = DB::table("retailer_flat")->select("legal_entity_type_id")->where('legal_entity_id',$legal_entity_id)->first();
        // $customer_type = $retailer_data->legal_entity_type_id;
        // $order_data = $this->_orderModel->getOrderInfo(array($orderId),array('gds_order_id','order_code','le_wh_id'));
        // $le_wh_id = $order_data[0]->le_wh_id;
        // $master_lookup = new MasterLookupController();
        $pickedIds = implode(',', $pickedIds);
        $orderfreeQtyData = array();
        $pickedfreeQtyData = array();
        $allProducts = explode(',', $allProductIds);
        $pickedArray = explode(',', $pickedIds);
        // foreach ($allProducts as $key => $value) {
        //     # code...
        //     $is_sample = $this->checkFreeQtyType($value);
        //     $orderfreeQty = $this->getOrderFreeQtyData($orderId,$order_all_total,"",$value,$is_sample);
        //     if(count($orderfreeQty))
        //         array_push($orderfreeQtyData, $orderfreeQty);
        // }

        $orderfreeQtyData = $this->getOrderFreeQtyDataById($orderId,$order_all_total);
        $pickedfreeQtyData = $this->getOrderFreeQtyDataById($orderId,$order_pick_total);
        if(count($pickedfreeQtyData) == 0){
            foreach ($orderfreeQtyData as $key => $value) {
                # code...
                $orderfreeQtyData[$key]->pick = 0;
                $pickedQty = isset($pickedIdData[$value->product_id]) ? $pickedIdData[$value->product_id] : 0;
                if($pickedQty == 0){
                    unset($orderfreeQtyData[$key]);
                }
            }
            return array("data"=>$orderfreeQtyData,"pick"=>0,"pick_total"=>$order_pick_total);
        }
        $prdData = array();
        // Log::info($pickedIdData);
        foreach ($pickedfreeQtyData as $key => $freeData) {
            # code...
            $product_id = $freeData->product_id;
            $product_qty = $freeData->product_qty;
            $picked_qty = isset($pickedIdData[$product_id]) ? $pickedIdData[$product_id] : 0;
            // Log::info($product_id);
            // Log::info($product_qty);
            // Log::info($picked_qty);
            if($picked_qty > $product_qty){
                $pick = 0;
                $pickedfreeQtyData[$key]->pick = $pick;
                array_push($prdData, $pickedfreeQtyData[$key]);
                
            }else if($picked_qty < $product_qty){
                $pick = 1;
                $pickedfreeQtyData[$key]->pick = $pick;
                array_push($prdData, $pickedfreeQtyData[$key]);
            }else if(!in_array($product_id, $pickedArray)){
                $pick = 1;
                $pickedfreeQtyData[$key]->pick = $pick;
                array_push($prdData, $pickedfreeQtyData[$key]);

            }


        }

        return array("data"=>$prdData,"pick"=>0,"pick_total"=>$order_pick_total);

        foreach ($pickedArray as $key => $value) {
            # code...
            $is_sample = $this->checkFreeQtyType($value);
            $picked_qty = $pickedIdData[$value];
            // Log::info(json_encode($value));
            $pickedfreeQty = $this->getOrderFreeQtyData($orderId,$order_pick_total,"NOT",$value,$is_sample,1,$picked_qty);
            // Log::info(json_encode($pickedfreeQty));
            if(count($pickedfreeQty))
                array_push($pickedfreeQtyData, $pickedfreeQty);
        }

        $data = [];
        $pick = 0;
        if(isset($orderfreeQtyData[0])){
            if(count($pickedfreeQtyData)){
                $data = $pickedfreeQtyData;
                $pick = 0;
            }else{
                $data = [];
            }
        }else{
            $data = [];
        }
        if(!count($pickedfreeQtyData)){
            if(!in_array($allProducts, $pickedArray)){
                $missProductIds=array_diff($allProducts,$pickedArray);
                if(count($missProductIds)){
                    $missArray = array();
                    foreach ($missProductIds as $key => $value) {
                        # code...
                        $is_sample = $this->checkFreeQtyType($value);
                        $missfreeQtyData = $this->getOrderFreeQtyData($orderId,$order_pick_total,"",$value,$is_sample);
                        if(count($missfreeQtyData)){
                            array_push($missArray, $missfreeQtyData);
                        }else{
                            $missfreeQtyData = $this->getOrderFreeQtyData($orderId,$order_pick_total,"",$value,$is_sample,0);
                            if(count($missfreeQtyData)){
                                array_push($missArray, $missfreeQtyData);
                            }
                        }
                    }
                    $data = $missArray;
                    $pick = 1;
                }else{
                    $pick = 0;
                }
            }
        }
        return array("data"=>$data,"pick"=>$pick,"pick_total"=>$order_pick_total);
    }

    public function getOrderFreeQtyData($orderId,$order_total,$condition,$pickedIds,$is_sample=0,$is_applied=1,$picked_qty=NULL){
        $order_total = "'".$order_total."'";
        if($is_sample == 0){
            $where = $order_total." ".$condition." BETWEEN gdf.range_from AND gdf.range_to";
        }else{
            $gt = ">";
            if($condition == "NOT")
                $gt = "<";
            $where = $order_total." $gt gdf.range_from";
        }
        if($picked_qty!=NULL){
            $pqc = "=";
            if($condition=="NOT")
                $pqc = ">";
            $where .= " and gdf.product_qty".$pqc.$picked_qty;
        }
        $query = "select gdf.product_id,gdf.product_qty,p.`product_title`,gdf.`pack_type`,gdf.`pack_level`,p.`thumbnail_image`,gdf.is_sample,gdf.`range_from`,gdf.`range_to` FROM gds_free_qty_data gdf INNER JOIN products p ON p.`product_id` = gdf.`product_id` WHERE ( $where ) AND gdf.gds_order_id = $orderId AND gdf.product_id in ($pickedIds) AND gdf.is_applied = $is_applied";
        // Log::info($condition);
        // Log::info($query);
        $data = DB::select(DB::raw($query));
        return isset($data[0])?$data[0]:array();
    }

    public function getOrderFreeQtyDataById($orderId,$order_total){
        $query = "select gdf.product_id,gdf.product_qty,p.`product_title`,gdf.`pack_type`,gdf.`pack_level`,p.`thumbnail_image`,gdf.is_sample,gdf.`range_from`,gdf.`range_to`,gdf.`is_applied` FROM gds_free_qty_data gdf INNER JOIN products p ON p.`product_id` = gdf.`product_id` WHERE gdf.gds_order_id = $orderId and $order_total BETWEEN gdf.`range_from` and gdf.`range_to`";
        Log::info($query);
        $data = DB::select(DB::raw($query));
        return isset($data[0])?$data:array();
    }

    public function checkFreeQtyType($product_id){
        $check = DB::table('gds_free_qty_data')->select("is_sample")->where(['product_id'=>$product_id])->first();
        // is_sample =1 its a sample given by brand,0 means its free qty given by ebutor
        return isset($check->is_sample)?$check->is_sample:0;
    }
    public function getPendingCashback($user_id){
        // sending 0 for temporarily
        return 0;
        if($user_id != 0 && $user_id != ""){
            $leObj = new GdsLegalEntity();
            $legal_entity_id = $leObj->getUserById($user_id, ['user_id', 'mobile_no','legal_entity_id']);
            $legal_entity_id = $legal_entity_id->legal_entity_id;
            $allCbkData = DB::selectFromWriteConnection(DB::raw("SELECT ROUND(SUM(eh.`cash_back_amount`),5) as cbk_amount FROM gds_orders go  JOIN ecash_transaction_history eh ON eh.`order_id`=go.`gds_order_id` WHERE go.`order_status_id` in (17001,17020,17005) AND go.`cust_le_id`=$legal_entity_id"));
            $cashback = $this->getUserEcash($user_id);
            if(count($allCbkData) && count($cashback)){
                $allopencbk = $allCbkData[0]->cbk_amount;
                $balance_amount = $cashback->cashback - $cashback->applied_cashback;
                $balance_amount = $balance_amount - $allopencbk;
                $balance_amount = round($balance_amount,5);
                if($balance_amount == "" || $balance_amount <= 0 || $balance_amount == NULl){
                    return 0;
                }
                return $balance_amount;
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }
}
