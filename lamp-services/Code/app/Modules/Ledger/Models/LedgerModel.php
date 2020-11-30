<?php

namespace App\Modules\Ledger\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Modules\Roles\Models\Role;
use Session;
use App\Modules\Orders\Models\OrderModel;
use App\Modules\Orders\Models\GdsBusinessUnit;
use App\Central\Repositories\RoleRepo;
use Log;

class LedgerModel extends Model
{
    public $timestamps = false;
    protected $fillable = ['ledger_id','ledger_name','invoice_id','party_id','transaction_date','reference_no','particulars','dr_account','dr_amt','cr_account','cr_amt','collected_by','payment_mode','balance','remarks','sync_status','sync_ref_no','sync_date','created_at','created_by','is_sync_failed','fail_reason','legal_entity_id','proof','le_wh_id','status','is_authorized','authorized_by','authorized_at'];
    protected $table = "ledger";


    public function getCollectionsByExec($Del_Exec, $Del_FDate, $Del_TDate, $Status,$filterBy=array(),$order_query_field,$order_by_type,$page,$pageSize) {
        try{
            $roleModel = new Role();
            $Json = json_decode($roleModel->getFilterData(6,Session::get('userId')),1);
            $Json = json_decode($Json['sbu'],1);
            $currentUserRole = explode(',', \Session::get('roles'));

            $le_wh_id = Session::get('legal_entity_id');


            $AWF = DB::table('appr_workflow_status_new')->where(array('legal_entity_id'=>$le_wh_id,'awf_for_id'=>56018))->pluck('awf_id')->all();

            if(empty($AWF)) {
                $awf_id = 0;
            } else {
                $awf_id = $AWF[0];
            }


            $fieldArr = array(
                                        'remittance_history.remittance_id',
                                        'remittance_history.remittance_code',
                                        'remittance_history.status',
                                        'remittance_history.submitted_at',
                                        DB::raw("DATE_FORMAT(`remittance_history`.`submitted_at`,'%d-%m-%Y') as 'submit_on'"),
                                        DB::raw("DATE_FORMAT(`remittance_history`.`acknowledged_at`,'%d-%m-%Y') as 'ack_on'"),
                                        DB::raw('round(remittance_history.collected_amt,2) as collected_amt'),
                                        DB::raw('round(remittance_history.amount_deposited,2) as amount_deposited'),
                                        'remittance_history.approval_status',
                                        DB::raw('round(remittance_history.by_cash,2) as by_cash'),
                                        DB::raw('round(remittance_history.by_cheque,2) as by_cheque'),
                                        DB::raw('round(remittance_history.by_online,2) as by_online'),
                                        DB::raw('round(remittance_history.by_upi,2) as by_upi'),
                                        DB::raw('round(remittance_history.by_ecash,2) as by_ecash'),
                                        DB::raw('round(remittance_history.by_pos,2) as by_pos'),
                                        DB::raw('round(remittance_history.fuel,2) as fuel'),
                                        DB::raw('round(remittance_history.vehicle,2) as vehicle'),
                                        DB::raw('round(remittance_history.due_amount,2) as due_amount'),
                                        DB::raw('round(remittance_history.coins_onhand,2) as coins_onhand'),
                                        DB::raw('round(remittance_history.notes_onhand,2) as notes_onhand'),
                                        DB::raw('round(remittance_history.used_expenses,2) as used_expenses'),
                                        DB::raw('round(remittance_history.arrears_deposited,2) as arrears_deposited'),
                                        DB::raw('round(remittance_history.coins_notes_deposited,2) as coins_notes_deposited'),
                                        DB::raw('round(remittance_history.used_expenses,2) as used_expenses'),
                                        'legalentity_warehouses.lp_wh_name as hub_name',
                                        DB::raw('GetUserName(remittance_history.acknowledged_by,2) as acknowledged_by'),
                                        DB::raw('getMastLookupValue(remittance_history.remittance_mode) as rem_mode'),
                                        'remittance_history.acknowledged_at',
                                        DB::raw('getLeWhName(remittance_history.le_wh_id) as DCName'),
                                        DB::raw('getMastLookupValue(remittance_history.status) as remittance_status'),
                                        DB::raw('GetUserName(remittance_history.submitted_by,2) as SubmittedByName'),
                                        DB::raw('getMastLookupValue(remittance_history.approval_status) as StatusName'),
                                        DB::raw('getNextRolePaymentApproval(remittance_history.approval_status,'.$awf_id.') as next_lbl_role')
                                    );


            // prepare sql

            $Del_FDate = date('Y-m-d',strtotime($Del_FDate));
            $Del_TDate = date('Y-m-d',strtotime($Del_TDate));

            DB::enableQueryLog();    
            
            $query_new = DB::table('collection_remittance_history as remittance_history')
                    ->select($fieldArr)
                    ->leftjoin('legalentity_warehouses','remittance_history.hub_id','=','legalentity_warehouses.le_wh_id')
                    ->where('remittance_history.submitted_at','>=',$Del_FDate.' 00:00:00')
                    ->where('remittance_history.submitted_at','<=',$Del_TDate.' 23:59:59')
                    ->where('remittance_history.is_parent','=',0);



                   
            if (!empty($filterBy)) {                

               
                $filter_new = explode(' ', $filterBy[0]);


                if($filter_new[0]=="`remittance_history`.`submitted_at`"){

                    $query_new=$query_new->where(DB::raw('DATE('.$filter_new[0].')'),$filter_new[1],$filter_new[2]);

                }
                else if($filter_new[0]=="`remittance_history`.`acknowledged_at`"){

                    $query_new=$query_new->where(DB::raw('DATE('.$filter_new[0].')'),$filter_new[1],$filter_new[2]);

                }

                else{

                   // print_r($filterBy);
                    foreach ($filterBy as $filterByEach) {


                        $filterByEachExplode = explode(' ', $filterByEach);

                        $length = count($filterByEachExplode);
                        $filter_query_value = '';

                        //print_r($filterByEachExplode);echo $length;
                        if ($length > 3) {
                            $filter_query_field = $filterByEachExplode[0];
                            $filter_query_operator = $filterByEachExplode[1];
                            for ($i = 2; $i < $length; $i++){


                                 if($i==($length-1)){
                                        $filter_query_value .= $filterByEachExplode[$i];

                                    }
                                    else{
                                    $filter_query_value .= $filterByEachExplode[$i]." ";

                                    }
                            }


                        } else {
                            $filter_query_field = $filterByEachExplode[0];
                            $filter_query_operator = $filterByEachExplode[1];
                            $filter_query_value = $filterByEachExplode[2];
                        }

                        $operator_array = array('=', '!=', '>', '<', '>=', '<=');
                        if (in_array(trim($filter_query_operator), $operator_array)) {

                            $d=DB::raw($filter_query_field);
                            $query_new = $query_new->where(DB::raw($filter_query_field), $filter_query_operator,$filter_query_value);

                        } else {
                            $query_new = $query_new->where(DB::raw($filter_query_field), $filter_query_operator, $filter_query_value);
                        }
                    }
                }
            }

           


            if($Del_Exec!=0) {

                $query_new->where('remittance_history.submitted_by','=',$Del_Exec);

            }


            if($Status!=0) {

                $query_new->where('remittance_history.approval_status','=',$Status);

            }
            $Dcs_Assigned='';
            $Hubs_Assigned='';
            if(empty($Json)){
                $query_new->whereRaw("remittance_history.le_wh_id IN ($Dcs_Assigned)")->whereRaw("remittance_history.hub_id IN ($Hubs_Assigned)");;
            }else{
                if(isset($Json['118001'])) {

                    $Dcs_Assigned = implode(',',explode(',',$Json['118001']));

                    $query_new->whereRaw("remittance_history.le_wh_id IN ($Dcs_Assigned)");
                }

                if(isset($Json['118002'])) {
                    $Hubs_Assigned = implode(',',explode(',',$Json['118002']));

                    $query_new->whereRaw("remittance_history.hub_id IN ($Hubs_Assigned)");
                }
            }

            if(!empty($order_query_field)){
               
                $querydata= $query_new->groupBy('remittance_history.remittance_id')->orderBy($order_query_field,$order_by_type)
                ->get()->all();
            }

           

            else{

                $querydata=$query_new->groupBy('remittance_history.remittance_id')->orderBy('remittance_history.remittance_id','desc')
                ->get()->all();
            }

            $noOfRecords=count($querydata);


           // print_r(count($querydata));exit;


            $pageLimit = '';
            if($page!='' && $pageSize!=''){
                $Rows=$page*$pageSize;
                $query_new->skip($Rows)->take($pageSize);                
            }


             if(!empty($order_query_field)){
               
                $query= $query_new->groupBy('remittance_history.remittance_id')->orderBy($order_query_field,$order_by_type)
                ->get()->all();
            }

           

            else{

                $query=$query_new->groupBy('remittance_history.remittance_id')->orderBy('remittance_history.remittance_id','desc')
                ->get()->all();
            }




            //print_r($query);exit;

           // print_r(DB::getQueryLog());exit;    

            $total_collected_amt = 0;                
            $total_by_cash = 0;                
            $total_by_cheque = 0;                
            $total_by_online = 0;                
            $total_by_upi = 0;                
            $total_by_ecash = 0;                
            $total_by_pos = 0;                

            foreach($query as $k=>$value) {

                $query[$k]->chk = '';                
                
                $query[$k]->by_upi = ($query[$k]->by_upi=='') ? '0.00000' : $query[$k]->by_upi;

                $collection_detail = $this->getCollDateByRemId($value->remittance_id);
                
                $query[$k]->collection_date = date('d-m-Y',strtotime($collection_detail->collection_date));


                if($query[$k]->by_pos==''){

                    $query[$k]->by_pos=0.00;                
                }


                $total_collected_amt = $total_collected_amt+$query[$k]->collected_amt; 
                $total_by_cash = $total_by_cash+$query[$k]->by_cash; 
                $total_by_cheque = $total_by_cheque+$query[$k]->by_cheque; 
                $total_by_online = $total_by_online+$query[$k]->by_online;
                $total_by_upi = $total_by_upi+$query[$k]->by_upi;
                $total_by_ecash = $total_by_ecash+$query[$k]->by_ecash;
                $total_by_pos = $total_by_pos+$query[$k]->by_pos;

                $query[$k]->submitted_at = date('Y-m-d',strtotime($query[$k]->submitted_at));
                
                $query[$k]->remittance_status = $query[$k]->StatusName;

                if($query[$k]->approval_status=='1') {
                    $query[$k]->remittance_status = 'Finance Approved';                    
                }

                    $next_lbl_role = explode(',',$query[$k]->next_lbl_role);


                    if(empty($next_lbl_role) || count(array_intersect($next_lbl_role,$currentUserRole))>0) {

                        if($query[$k]->approval_status == 57051) {

                            $query[$k]->chk = '<input type="checkbox" name="chk[]" value="'.$query[$k]->remittance_id.'"/>';

                        } else {

                            $query[$k]->remittance_status.= '<br><button type="button" href="#pmtApprvlPopup" data-toggle="modal" data-remittace="'.$query[$k]->remittance_id.'" data-status="'.$query[$k]->approval_status.'" class="btn green-meadow pmtApprv">Acknowledge</button>';

                        }

                    }
                

            }


            if(is_array($query) && count($query)>0) {
                $query[0]->total_collected_amt = $total_collected_amt;
                $query[0]->total_by_cash = $total_by_cash;
                $query[0]->total_by_cheque = $total_by_cheque;
                $query[0]->total_by_online = $total_by_online;
                $query[0]->total_by_upi = $total_by_upi;
                $query[0]->total_by_ecash = $total_by_ecash;
                $query[0]->total_by_pos = $total_by_pos;
            }
            $query['noOfRecords'] = $noOfRecords;

            return $query;
        }            
        catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }

    }

    public function approvePayments($data) {

       DB::beginTransaction(); 
        
        try{

    		$Payment_Ids = $data['ids'];

    		DB::table('ledger')->whereIn('ledger_id',$Payment_Ids)->update(array('status'=>108002));
            DB::commit();
    		return 'success';
        }            
        catch(Exception $e) {
            DB::rollback(); 
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }

    }

    public function getRemittanceDetails($remittenceId) {

        try{

            // Getting child remittance count. If count is zero considered as consolidated remittance not submitted yet, If > zero considered as already submitted

            $childRemCount = DB::table('collection_remittance_history')
                            ->where('is_parent',$remittenceId)            
                            ->select(DB::raw('COUNT("remittance_id") as rem_count'))->first();

            if(isset($childRemCount->rem_count) && $childRemCount->rem_count==0) {

                $remittenceId = array($remittenceId);
            } else {
                $remittenceId = DB::table('collection_remittance_history')
                        ->where('is_parent',$remittenceId)            
                        ->pluck('remittance_id')->all();
            }            

            $data = DB::table('remittance_mapping as mapping')
                    ->select(array(
                                    'collections.*',
                                    'collections.created_on as collected_on',
                                    DB::raw('GetUserName(collections.created_by,2) as collected_by'),
                                    'ml.master_lookup_name as payment_mode',
                                    'collection_history.proof',
                                    'collection_history.amount',
                                    ))
                    ->join('collections','collections.collection_id','=','mapping.collection_id')
                    ->join('collection_history','collection_history.collection_id','=','mapping.collection_id')
                    ->leftjoin('master_lookup as ml','ml.value','=','collection_history.payment_mode')
                    ->whereIn('mapping.remittance_id',$remittenceId)
                    ->groupBy('collection_history.history_id')
                    ->get()->all();



            foreach ($data as $key => $value) {
                if($value->order_code!='') {
                    $data[$key]->order_code = '<a target="_blank" href="salesorders/detail/'.$data[$key]->gds_order_id.'">'.$data[$key]->order_code.'</a>';
                }
                if($value->invoice_code!='') {
                    $data[$key]->invoice_code = '<a target="_blank" href="salesorders/invoicedetail/'.$data[$key]->invoice_id.'/'.$data[$key]->gds_order_id.'">'.$data[$key]->invoice_code.'</a>';
                }
                if($value->return_code!='') {
                    $data[$key]->return_code = '<a target="_blank" href="salesorders/returndetail/'.$data[$key]->return_id.'">'.$data[$key]->return_code.'</a>';
                }
                if($value->cancel_code!='') {
                    $data[$key]->cancel_code = '<a target="_blank" href="salesorders/canceldetail/'.$data[$key]->cancel_id.'">'.$data[$key]->cancel_code.'</a>';
                }

                if($value->proof!='') {

                    $data[$key]->proof = '<a target="_blank" href="'.$data[$key]->proof.'">Proof</a>';
                }
            }

            return $data;
        }            
        catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }

    }

    public function updateStatusAWF($table,$unique_column,$approval_unique_id, $next_status_id, $acknowledged_by=''){
        DB::beginTransaction(); 
        try{
            $status = explode(',',$next_status_id);
            $new_status = ($status[1]==0)?$status[0]:$status[1];
            $invoice = array(
                'approval_status'=>$new_status,
                'acknowledged_by'=>$acknowledged_by,
                'acknowledged_at'=>date('Y-m-d H:i:s')
            );
            DB::table($table)->where($unique_column, $approval_unique_id)->update($invoice);
            DB::commit();
        } catch (Exception $e) {
            DB::rollback(); 
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }


     public function getApprovalHistory($module,$id) {

        try
        {

        $history=DB::table('appr_workflow_history as hs')
                        ->join('users as us','us.user_id','=','hs.user_id')
                        ->join('user_roles as ur','ur.user_id','=','hs.user_id')
                        ->join('roles as rl','rl.role_id','=','ur.role_id')
                        ->join('master_lookup as ml','ml.value','=','hs.status_to_id')
                        ->select('us.profile_picture','us.firstname','us.lastname',DB::raw('group_concat(rl.name) as name'),'hs.created_at','hs.status_to_id','hs.status_from_id','hs.awf_comment','ml.master_lookup_name')
                        ->where('hs.awf_for_id',$id)
                        ->where('hs.awf_for_type',$module)
                        ->groupBy('hs.created_at')
                        ->get()->all();
            return json_decode(json_encode($history),true);
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    public function getApprovalStatus() {

        try
        {
        $query = DB::table('master_lookup');
        return $query->where('mas_cat_id','57')->where('parent_lookup_id', function($sql) 
            {
                $sql->select('master_lookup_id')->from('master_lookup')->whereRaw("master_lookup_name='Payment'");
            })->pluck('master_lookup_name','value')->all();
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    public function saveCollectionVoucher($remittanceId) {
       DB::beginTransaction(); 
        try
        {

        $_BusinessUnit = new GdsBusinessUnit();

        $selectArr = array(
                            'grid.gds_invoice_grid_id',
                            'collections.collected_amount',
                            'collections.invoice_amount',
                            'collections.invoice_code',
                            'collections.rounded_amount',
                            'collections.collection_code',
                            'collections.created_on as collection_date',
                            'grid.invoice_code',
                            'orders.shop_name',
                            'orders.hub_id',
                            'orders.le_wh_id',
                            'le.le_code',
                            'payment_mode',
                            'collection_history.amount',
                            'collection_history.ecash'

                            );


        $query =    DB::table('remittance_mapping as mapping')
                    ->select($selectArr)
                    ->join('collections','collections.collection_id','=','mapping.collection_id')
                    ->join('collection_history','collection_history.collection_id','=','collections.collection_id')
                    ->join('gds_invoice_grid as grid','grid.gds_invoice_grid_id','=','collections.invoice_id')
                    ->join('gds_orders as orders','orders.gds_order_id','=','grid.gds_order_id')
                    ->leftjoin('legal_entities as le','le.legal_entity_id','=','orders.cust_le_id')
                    ->join('collection_remittance_history as history','history.remittance_id','=','mapping.remittance_id')

                    ->where('mapping.remittance_id',$remittanceId)->get()->all();




        if(!empty($query)){

            
            foreach($query as $Data) {


                $costCenterData = $_BusinessUnit->getBusinesUnitLeWhId($Data->hub_id, array('bu.cost_center','bu.bu_name'));
                $costCenterGroupData = $_BusinessUnit->getBusinesUnitLeWhId($Data->le_wh_id, array('bu.cost_center'));

                
                $cost_centre = (isset($costCenterData->cost_center)) ? $costCenterData->cost_center : '';
                $cost_centre_group = (isset($costCenterGroupData->cost_center)) ? $costCenterGroupData->cost_center : '';

                $bu_name = (isset($costCenterData->bu_name)) ? $costCenterData->bu_name : '';    

                $cost_centre = $cost_centre.' - '.$bu_name;

                $ecash = $Data->ecash;                

                $collectedInclEcash = $Data->amount + $ecash;


                if($Data->collected_amount!='0' && ($Data->payment_mode==22010 || $Data->payment_mode==22005 || $Data->payment_mode==22016 || $Data->payment_mode==22011 || $Data->payment_mode==22019)) {

                    $insertArray    = array();
                if($Data->payment_mode==22010 || $Data->payment_mode==22011) {
                    $ledger_group = env('CASH_LEDGER_GROUP');;
                    $ledger_account = env('CASH_LEDGER_ACCOUNT');;
                } else {
                    $ledger_group = env('NONCASH_LEDGER_GROUP');;
                    $ledger_account = env('NONCASH_LEDGER_ACCOUNT');;
                }                


                $ecash_ledger_group = env('ECASH_LEDGER_GROUP');;
                $ecash_ledger_account = env('ECASH_LEDGER_ACCOUNT');;

                if($ecash > 0) {
                    $Dr_Value = $Data->amount;
                    $Cr_Value = $collectedInclEcash;
                } else {
                    $Dr_Value = $collectedInclEcash;
                    $Cr_Value = $Data->amount;
                }

                $insertArray[]  = array('voucher_code'=>$Data->collection_code,
                                        'voucher_type'=>'Receipt',
                                        'voucher_date'=>$Data->collection_date,
                                        'ledger_group'=>$ledger_group,
                                        'ledger_account'=>$ledger_account,
                                        'tran_type'=>'Dr',
                                        'amount'=>$Dr_Value,
                                        'naration'=>'Being Collection received from Retailer : '.$Data->shop_name.'  Ief. Order/Inv. '.$Data->invoice_code,
                                        'cost_centre'=>$cost_centre,
                                        'cost_centre_group'=>$cost_centre_group,
                                        'reference_no'=>$Data->invoice_code,
                                        'is_posted'=>0,
                                        'Remarks'=>'Receipt Entry');

                $insertArray[]  = array('voucher_code'=>$Data->collection_code,
                                        'voucher_type'=>'Receipt',
                                        'voucher_date'=>$Data->collection_date,
                                        'ledger_group'=>'Sundry Debtors',
                                        'ledger_account'=>$Data->shop_name.' - '.$Data->le_code,
                                        'tran_type'=>'Cr',
                                        'amount'=>$Cr_Value,
                                        'naration'=>'0',
                                        'cost_centre'=>$cost_centre,
                                        'cost_centre_group'=>$cost_centre_group,
                                        'reference_no'=>$Data->invoice_code,
                                        'is_posted'=>0,
                                        'Remarks'=>'Receipt Entry');
            
                if($ecash > 0) {

                    $insertArray[]  = array('voucher_code'=>$Data->collection_code,
                                            'voucher_type'=>'Receipt',
                                            'voucher_date'=>$Data->collection_date,
                                            'ledger_group'=>$ecash_ledger_group,
                                            'ledger_account'=>$ecash_ledger_account,
                                            'tran_type'=>'Cr',
                                            'amount'=>'-'.$ecash,
                                            'naration'=>'0',
                                            'cost_centre'=>$cost_centre,
                                            'cost_centre_group'=>$cost_centre_group,
                                            'reference_no'=>$Data->invoice_code,
                                            'is_posted'=>0,
                                            'Remarks'=>'Receipt Entry');


                }


                DB::table('vouchers')->insert($insertArray);
                }
            }

        }
                DB::commit();
        } catch (Exception $e) {
          DB::rollback(); 
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }

    }


    public function checkOrderClosed($remittanceId) {
        DB::beginTransaction(); 
        try {
        $selectArr = array(DB::raw('SUM(collected_amount) as sum_collected_amount'),'collected_amt as remittance_amount');


        $query =    DB::table('remittance_mapping as mapping')->select($selectArr)
                    ->join('collections','collections.collection_id','=','mapping.collection_id')
                    ->join('collection_remittance_history as history','history.remittance_id','=','mapping.remittance_id')
                    ->where('mapping.remittance_id',$remittanceId)->get()->all();
                    


        if(!empty($query)) {

            if(isset($query[0]->sum_collected_amount) && ($query[0]->sum_collected_amount==$query[0]->remittance_amount)) {


                $data= DB::table('remittance_mapping as mapping')->select('inv.gds_order_id')
                       ->join('collections','collections.collection_id','=','mapping.collection_id') 
                ->leftJoin('gds_invoice_grid as inv','inv.gds_invoice_grid_id','=','collections.invoice_id')
                ->where('mapping.remittance_id','=',$remittanceId)->pluck('gds_order_id')->all();



                if(!empty($data)) {

                    DB::table('gds_orders')->whereIn('gds_order_id',$data)->update(array('order_status_id'=>17008));


                }

            }

        }            
                DB::commit();
        } catch (Exception $e) {
            DB::rollback(); 
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }

    }



    public function getFullCollectionDetailByExec($Del_Exec, $Del_FDate, $Del_TDate) {

            try {
            $currentUserRole = explode(',', \Session::get('roles'));

            $fieldArr = array(
                                        'remittance_history.remittance_id',
                                        'remittance_history.remittance_code',
                                        'remittance_history.status',
                                        'remittance_history.submitted_at',
                                        'remittance_history.collected_amt',
                                        'remittance_history.approval_status',
                                        'remittance_history.by_cash',
                                        'remittance_history.by_cheque',
                                        'remittance_history.by_online',
                                        DB::raw('GetUserName(remittance_history.acknowledged_by,2) as acknowledged_by'),
                                        'remittance_history.acknowledged_at',
                                        DB::raw('getLeWhName(remittance_history.le_wh_id) as DCName'),
                                        DB::raw('getMastLookupValue(remittance_history.status) as remittance_status'),
                                        DB::raw('GetUserName(remittance_history.submitted_by,2) as SubmittedByName'),
                                        DB::raw('getMastLookupValue(remittance_history.approval_status) as StatusName'),
                                        DB::raw('getNextRolePaymentApproval(remittance_history.remittance_id) as next_lbl_role')
                                        /*,
                                        DB::raw('max(wfh.created_at) as created_at')*/

                                    );
            $fieldArr = array(  'collections.*',
                                'ml.master_lookup_name as payment_mode',
                                'collection_history.proof');

            // prepare sql

            $Del_FDate = date('Y-m-d',strtotime($Del_FDate));
            $Del_TDate = date('Y-m-d',strtotime($Del_TDate));
            $roleModel = new Role();
            $Json = json_decode($roleModel->getFilterData(6,Session::get('userId')),1);
            $Json = json_decode($Json['sbu'],1);

            $Hubs_Assigned = '';

            if(isset($Json['118002'])) {
                $Hubs_Assigned = explode(',',$Json['118002']);
            }
            $query_new = DB::table('collections')
                    
                    ->select($fieldArr)
                    ->leftjoin('collection_history','collection_history.collection_id','=','collections.collection_id')
                    ->leftjoin('master_lookup as ml','ml.value','=','collection_history.payment_mode')                    
                    ->leftjoin('gds_orders as gd','gd.gds_order_id','=','collections.gds_order_id')
                    ->where('collections.created_on','>=',$Del_FDate.' 00:00:00')
                    ->where('collections.created_on','<=',$Del_TDate.' 23:59:59')
                    ->whereIn('gd.hub_id',$Hubs_Assigned);
            if($Del_Exec!=0) {

                $query_new->where('collections.created_by','=',$Del_Exec);

            }


            $query=$query_new->get()->all();



            foreach($query as $k=>$value) {

                $query[$k]->created_on = date('d-m-Y',strtotime($query[$k]->created_on));
                if($value->proof!='') {

                    $query[$k]->proof = '<a target="_blank" href="'.$query[$k]->proof.'">Proof</a>';
                }
                

            }


            return $query;

        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }

    }

    /**
     * checkOrdReturnApprComplete remittance orders return approval status is completed or not
     * @param  int  $remittanceId Holds Remittance ID
     * @return bool            
    */

    public function checkOrdReturnApprComplete($remittanceId) {

        try{
            
            $fieldArr = array('returns.gds_order_id');
            $query = DB::table('remittance_mapping as mapping')->select($fieldArr);
            $query->join('collections','collections.collection_id','=','mapping.collection_id');
            $query->join('gds_returns as returns','returns.gds_order_id','=','collections.gds_order_id');
            $query->where('mapping.remittance_id', $remittanceId);
            $query->where('returns.return_status_id', '!=','57066');

            if($query->count()==0) {
                return true;
            }

            return false;

        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }

    }

    /**
     * getRemittanceOrderIds get orders by remittanceId
     * @param  int  $orderId Holds order ID
     * @return bool            
    */

    public function getRemittanceReturnApprOrderIds($remittenceId) {


        try{
            
            $fieldArr = array('collections.gds_order_id');
            $query = DB::table('remittance_mapping as mapping')->select($fieldArr);
            $query->join('collections','collections.collection_id','=','mapping.collection_id');
            $query->join('collection_history as history','history.collection_id','=','collections.collection_id');
            $query->leftjoin('gds_returns as returns','returns.gds_order_id','=','collections.gds_order_id');
            $query->leftjoin('nct_transcation_tracking as nct_track','nct_track.nct_history_id','=','history.history_id');
            $query->where('mapping.remittance_id', $remittenceId);
            
//            $query->where('returns.return_status_id', '57066');
            
            $query->where(function($query) {

                $query->where('returns.return_status_id', '57066')
                      ->orWhereNull('returns.return_status_id');
            });
            $query->where(function($query) {

                $query->where(function($query) {
                    $query->where('history.payment_mode','!=','22010');
                    $query->where('history.payment_mode','!=','22011');
                    $query->where('nct_track.nct_status','11904');
                });
                $query->orWhere(function($query) {
                    $query->where('history.payment_mode','22010');
                    $query->orWhere('history.payment_mode','22011');
                    $query->orWhere('history.payment_mode','0');
                });
            });

            $query->groupBy('collections.gds_order_id');
            return $query->get()->all();
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }

    }



    /**
     * getRemittanceApprStatusByOrderId get orders remittance approval status
     * @param  int  $orderId Holds order ID
     * @return bool            
    */

    public function getRemittanceApprStatusByOrderId($orderId) {


        try{
            
            $fieldArr = array('history.approval_status');
            $query = DB::table('collections')->select($fieldArr);
            $query->join('remittance_mapping AS mapping','mapping.collection_id','=','collections.collection_id');
            $query->join('collection_remittance_history AS history','history.remittance_id','=','mapping.remittance_id');
            $query->join('collection_history AS coll_history','coll_history.collection_id','=','collections.collection_id');
            $query->leftjoin('nct_transcation_tracking as nct_track','nct_track.nct_history_id','=','coll_history.history_id');
            $query->where('collections.gds_order_id', $orderId);

            $query->where(function($query) {

                $query->where(function($query) {
                    $query->where('coll_history.payment_mode','!=','22010');
                    $query->where('coll_history.payment_mode','!=','22011');
                    $query->where('nct_track.nct_status','11904');
                });
                $query->orWhere(function($query) {
                    $query->where('coll_history.payment_mode','22010');
                    $query->orWhere('coll_history.payment_mode','22011');
                    $query->orWhere('coll_history.payment_mode','0');
                });
            });

            $result = $query->get()->all();

            if(is_array($result) && count($result)>0 && $result[0]->approval_status==1) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }

    }


    /**
     * completeOrdersByRemIdForApp closes orders when remittance submits
     * @param  int  $remittanceId Holds Remittance ID
     * @return bool            
    */


    public function completeOrdersByRemIdForApp($remittanceId, $userId) {
       DB::beginTransaction(); 
       try{

            $orderModel = new OrderModel();

            $this->changeOrderPaymentStatusByRemId($remittanceId);
            $query = DB::table('remittance_mapping as mapping');
            $query->join('collections','collections.collection_id','=','mapping.collection_id');
            $query->join('collection_history as history','history.collection_id','=','collections.collection_id');
            $query->leftjoin('gds_returns as returns','returns.gds_order_id','=','collections.gds_order_id');

            $query->leftjoin('nct_transcation_tracking as nct_track','nct_track.nct_history_id','=','history.history_id');
            
            $query->where('mapping.remittance_id', $remittanceId);
            $query->where(function($query) {

                $query->where('returns.return_status_id', '57066')
                      ->orWhereNull('returns.return_status_id');
            });
            $query->where(function($query) {

                $query->where(function($query) {
                    $query->where('history.payment_mode','!=','22010');
                    $query->where('history.payment_mode','!=','22011');
                    $query->where('nct_track.nct_status','11904');
                });
                $query->orWhere(function($query) {
                    $query->where('history.payment_mode','22010');
                    $query->orWhere('history.payment_mode','22011');
                    $query->orWhere('history.payment_mode','0');
                });
            });
            $query->groupBy('collections.gds_order_id');

            $orderIds = $query->pluck('collections.gds_order_id')->all();

            if(!empty($orderIds)) {

                DB::table('gds_orders')->whereIn('gds_order_id',$orderIds)->update(array('order_status_id'=>17008));


                foreach($orderIds as $orderId) {

                    $date = date('Y-m-d H:i:s');
                    $commentArr = array('entity_id'=>$orderId,
                                    'comment_type'=>17,
                                    'comment'=>'Order completed and remittance # '.$remittanceId.' is approved from App',
                                    'commentby'=>$userId,
                                    'created_by'=>$userId,
                                    'order_status_id'=>17008,
                                    'created_at'=>(string)$date,
                                    'comment_date'=>(string)$date
                                    );

                    $orderModel->saveComment($commentArr);
                }
                DB::commit();
                return true;

            }

        } catch (Exception $e) {
            DB::rollback(); 
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }

    }


    /**
     * completeReturnApprOrdersByRemId closes orders whose returns were approved
     * @param  int  $remittanceId Holds Remittance ID
     * @return bool            
    */


    public function completeReturnApprOrdersByRemId($remittanceId) {
       DB::beginTransaction(); 
       try{
            
            $query = DB::table('remittance_mapping as mapping');
            $query->join('collections','collections.collection_id','=','mapping.collection_id');
            $query->join('collection_history as history','history.collection_id','=','collections.collection_id');
            $query->leftjoin('gds_returns as returns','returns.gds_order_id','=','collections.gds_order_id');

            $query->leftjoin('nct_transcation_tracking as nct_track','nct_track.nct_history_id','=','history.history_id');
            
            $query->where('mapping.remittance_id', $remittanceId);
            $query->where(function($query) {

                $query->where('returns.return_status_id', '57066')
                      ->orWhereNull('returns.return_status_id');
            });
            $query->where(function($query) {

                $query->where(function($query) {
                    $query->where('history.payment_mode','!=','22010');
                    $query->where('history.payment_mode','!=','22011');
                    $query->where('nct_track.nct_status','11904');
                });
                $query->orWhere(function($query) {
                    $query->where('history.payment_mode','22010');
                    $query->orWhere('history.payment_mode','22011');
                    $query->orWhere('history.payment_mode','0');
                });
            });
            $query->groupBy('collections.gds_order_id');

            $orderIds = $query->pluck('collections.gds_order_id')->all();

            if(!empty($orderIds)) {

                foreach ($orderIds as $orderId) {

                    $res = DB::table('collections')->select(array(DB::raw('SUM(collected_amount) as collected_amount'),'collectable_amt'))->where('gds_order_id',$orderId)->get()->all();

                    if($res[0]->collected_amount==round($res[0]->collectable_amt)) {
                        DB::table('gds_orders')->where('gds_order_id',$orderId)->update(array('order_status_id'=>17008));
                    }


                }



            }

                DB::commit();
        }
        catch(Exception $e){
            DB::rollback(); 
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());

        }


    }

    public function getChequeStatusByHistoryId($historyId) {
       DB::beginTransaction(); 
       try{

            $orderModel = new OrderModel();

            $this->markPaymentStatusByCollHistId($historyId);
            $query = DB::table('collection_history as history');
            $query->join('collections','collections.collection_id','=','history.collection_id');
            $query->join('remittance_mapping as mapping','mapping.collection_id','=','collections.collection_id');
            $query->join('collection_remittance_history AS rem_history','rem_history.remittance_id','=','mapping.remittance_id');
            $query->leftjoin('gds_returns as returns','returns.gds_order_id','=','collections.gds_order_id');
            $query->leftjoin('nct_transcation_tracking as nct_track','nct_track.nct_history_id','=','history.history_id');
            
            $query->where('history.history_id', $historyId);
            $query->where(function($query) {

                $query->where('returns.return_status_id', '57066')
                      ->orWhereNull('returns.return_status_id');
            });
            $query->where(function($query) {

                $query->where(function($query) {
                    $query->where('history.payment_mode','!=','22010');
                    $query->where('history.payment_mode','!=','22011');
                    $query->where('nct_track.nct_status','11904');
                });
                $query->orWhere(function($query) {
                    $query->where('history.payment_mode','22010');
                    $query->orWhere('history.payment_mode','22011');
                    $query->orWhere('history.payment_mode','0');
                });
            });
            $query->where('rem_history.approval_status', 1);

            $query->groupBy('collections.gds_order_id');

            $orderIds = $query->select(array('collections.gds_order_id','rem_history.approval_status'))->get()->all();

            $orderIds = $query->pluck('collections.gds_order_id')->all();

            if(!empty($orderIds)) {

                DB::table('gds_orders')->whereIn('gds_order_id',$orderIds)->update(array('order_status_id'=>17008));

                foreach($orderIds as $orderId) {

                    $date = date('Y-m-d H:i:s');
                    $commentArr = array('entity_id'=>$orderId,
                                    'comment_type'=>17,
                                    'comment'=>'Order Completed and Payment cleared from NCT',
                                    'commentby'=>Session::get('userId'),
                                    'created_by'=>Session::get('userId'),
                                    'order_status_id'=>17008,
                                    'created_at'=>(string)$date,
                                    'comment_date'=>(string)$date
                                    );

                    $orderModel->saveComment($commentArr);
                }
            }           
                DB::commit();
                return true;
        }
        catch(Exception $e){
          DB::rollback(); 
          Log::error($e->getMessage().' '.$e->getTraceAsString());
          return false;
        }
    }


    public function changeOrderPaymentStatusByRemId($remId) {
        try { 

            $select = array('orders.gds_order_id',
                            'history.payment_mode',
                            'orders.order_status_id'
                            );

            $query = DB::table('remittance_mapping as mapping')->select($select);
            $query->join('collections','collections.collection_id','=','mapping.collection_id');
            $query->join('collection_history as history','history.collection_id','=','collections.collection_id');            
            $query->join('gds_orders as orders','orders.gds_order_id','=','collections.gds_order_id');            
            $query->where('mapping.remittance_id', $remId);
            $result=$query->get()->all();

            $chequeOrders   = array();
            $cod_upi_orders = array();
            $fullreturns    = array();       

            if(count($result)>0) {
                foreach($result as $order) {
                    if($order->payment_mode=='22004') {
                        $chequeOrders[] = $order->gds_order_id;
                    } else if($order->payment_mode=='22010' || $order->payment_mode=='22005' || $order->payment_mode=='22019') {
                        $cod_upi_orders[] = $order->gds_order_id;
                    } else if($order->payment_mode=='0' || $order->order_status_id=='17022') {
                        $fullreturns[] = $order->gds_order_id;
                    }
                }
                
                if(count($chequeOrders)>0) {

                    foreach ($chequeOrders as $chequeOrderId) {
                        if(!$this->checkOrderPaymentPaid($chequeOrderId)) {

                            DB::table('gds_orders_payment')->where('gds_order_id',$chequeOrderId)->update(array('payment_status_id'=>32010));

                        }                        

                    }
                }

                if(count($cod_upi_orders)>0) {

                    DB::table('gds_orders_payment')->whereIn('gds_order_id',$cod_upi_orders)->update(array('payment_status_id'=>32001));

                }

                if(count($fullreturns)>0) {

                    DB::table('gds_orders_payment')->whereIn('gds_order_id',$fullreturns)->update(array('payment_status_id'=>32011));

                }


            }
            
        }            
        catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    function checkOrderPaymentPaid($orderId) {
        try { 

            $Count = DB::table('gds_orders_payment')->where(array('gds_order_id'=>$orderId,'payment_status_id'=>32001))->count();

            return ($Count==1);
        }            
        catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    function markPaymentStatusByCollHistId($historyId) {

       DB::beginTransaction(); 
        try { 


            $query = DB::table('collection_history as history');
            $query->join('collections','collections.collection_id','=','history.collection_id');
            
            $orderIds = $query->where('history.history_id', $historyId)->pluck('collections.gds_order_id')->all();

            DB::table('gds_orders_payment')->whereIn('gds_order_id',$orderIds)->update(array('payment_status_id'=>32001));

            DB::commit();

        }            
        catch(Exception $e) {
            DB::rollback(); 
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }    

    public function insertReceiptVouchers($startDate,$endDate,$tableName) {

       DB::beginTransaction(); 
        try { 

        $sno = 1;    
        

        $_BusinessUnit = new GdsBusinessUnit();

        $selectArr = array(
                            'grid.gds_invoice_grid_id',
                            'collections.collected_amount',
                            'collections.invoice_amount',
                            'collections.invoice_code',
                            'collections.rounded_amount',
                            'collections.collection_code',
                            'grid.invoice_code',
                            'orders.shop_name',
                            'orders.hub_id',
                            'orders.le_wh_id',
                            'le.le_code',
                            'payment_mode',
                            'appr_history.created_at as voucher_entry_date'
                            );

        $query =    DB::table('remittance_mapping as mapping')
                    ->select($selectArr)
                    ->join('collections','collections.collection_id','=','mapping.collection_id')
                    ->join('collection_history','collection_history.collection_id','=','collections.collection_id')
                    ->join('gds_invoice_grid as grid','grid.gds_invoice_grid_id','=','collections.invoice_id')
                    ->join('gds_orders as orders','orders.gds_order_id','=','grid.gds_order_id')
                    ->leftjoin('legal_entities as le','le.legal_entity_id','=','orders.cust_le_id')
                    ->join('collection_remittance_history as history','history.remittance_id','=','mapping.remittance_id')

                    ->join('appr_workflow_history AS appr_history', function($join)
                    {
                        $join->on('appr_history.awf_for_id','=','mapping.remittance_id');
                        $join->on('appr_history.awf_for_type','=',DB::raw('"Payment"'));
                        $join->on('appr_history.status_to_id','=',DB::raw(57053));
                    })

                    ->whereBetween(DB::raw('DATE(appr_history.created_at)'),[$startDate.' 00:00:00',$endDate.' 23:23:59'])
                    ->groupBy('collections.collection_code')->get()->all();

        if(!empty($query)){

            
            foreach($query as $Data) {



                $costCenterData = $_BusinessUnit->getBusinesUnitLeWhId($Data->hub_id, array('bu.cost_center','bu.bu_name'));
    
                $costCenterGroupData = $_BusinessUnit->getBusinesUnitLeWhId($Data->le_wh_id, array('bu.cost_center'));
                    
    
                $cost_centre = (isset($costCenterData->cost_center)) ? $costCenterData->cost_center : '';
    
                $cost_centre_group = (isset($costCenterGroupData->cost_center)) ? $costCenterGroupData->cost_center : '';


                $bu_name = (isset($costCenterData->bu_name)) ? $costCenterData->bu_name : '';    



                $cost_centre = $cost_centre.' - '.$bu_name;

                if($Data->collected_amount!='0' && ($Data->payment_mode==22010 || $Data->payment_mode==22005)) {

                    $insertArray    = array();



                if($Data->payment_mode==22010) {

                    $ledger_group = env('CASH_LEDGER_GROUP');

                    $ledger_account = env('CASH_LEDGER_ACCOUNT');
     
                } else {
     
                    $ledger_group = env('NONCASH_LEDGER_GROUP');

                    $ledger_account = env('NONCASH_LEDGER_ACCOUNT');
     
                } 


                    $insertArray[]  = array('voucher_code'=>$Data->collection_code,
                                            'voucher_type'=>'Receipt',
                                            'voucher_date'=>$Data->voucher_entry_date,
                                            'ledger_group'=>$ledger_group,
                                            'ledger_account'=>$ledger_account,
                                            'tran_type'=>'Dr',
                                            'amount'=>$Data->collected_amount,
                                            'naration'=>'Being Collection received from Retailer : '.$Data->shop_name.'  Ief. Order/Inv. '.$Data->invoice_code,
                                            'cost_centre'=>$cost_centre,
                                            'cost_centre_group'=>$cost_centre_group,
                                            'reference_no'=>$Data->invoice_code,
                                            'is_posted'=>0,
                                            'Remarks'=>'Receipt Entry - By Script');

                    $insertArray[]  = array('voucher_code'=>$Data->collection_code,
                                            'voucher_type'=>'Receipt',
                                            'voucher_date'=>$Data->voucher_entry_date,
                                            'ledger_group'=>'Sundry Debtors',
                                            'ledger_account'=>$Data->shop_name.' - '.$Data->le_code,
                                            'tran_type'=>'Cr',
                                            'amount'=>$Data->collected_amount,
                                            'naration'=>'0',
                                            'cost_centre'=>$cost_centre,
                                            'cost_centre_group'=>$cost_centre_group,
                                            'reference_no'=>$Data->invoice_code,
                                            'is_posted'=>0,
                                            'Remarks'=>'Receipt Entry - By Script');
                
                    
                    DB::table($tableName)->insert($insertArray);
                    
                    echo $sno.' Receipt voucher for '.$Data->collection_code.'</br>';
                    $sno++;

                }
            }

        }
                DB::commit();


        }            
        catch(Exception $e) {
            DB::rollback(); 
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }

    }    


    function insertReceiptTallyVouchers() {

       DB::beginTransaction(); 
        try {



        $sno = 1;    
        

        $_BusinessUnit = new GdsBusinessUnit();

        $selectArr = array(
                            'grid.gds_invoice_grid_id',
                            'collections.collected_amount',
                            'collections.invoice_amount',
                            'collections.invoice_code',
                            'collections.rounded_amount',
                            'collections.collection_code',
                            'collections.created_on as collection_date',
                            'grid.invoice_code',
                            'orders.shop_name',
                            'orders.hub_id',
                            'orders.le_wh_id',
                            'le.le_code',
                            'payment_mode'
                            );



        $query =    DB::table('collections')
                    ->select($selectArr)
                    ->join('collection_history','collection_history.collection_id','=','collections.collection_id')
                    ->join('gds_invoice_grid as grid','grid.gds_invoice_grid_id','=','collections.invoice_id')
                    ->join('gds_orders as orders','orders.gds_order_id','=','grid.gds_order_id')
                    ->leftjoin('legal_entities as le','le.legal_entity_id','=','orders.cust_le_id')
                    ->whereIn('collections.invoice_code', array('TSIV17020015480'))
                    ->groupBy('collections.collection_code')->get()->all();


        if(!empty($query)){

            
            foreach($query as $Data) {



                $costCenterData = $_BusinessUnit->getBusinesUnitLeWhId($Data->hub_id, array('bu.cost_center','bu.bu_name'));
    
                $costCenterGroupData = $_BusinessUnit->getBusinesUnitLeWhId($Data->le_wh_id, array('bu.cost_center'));
                    
    
                $cost_centre = (isset($costCenterData->cost_center)) ? $costCenterData->cost_center : '';
    
                $cost_centre_group = (isset($costCenterGroupData->cost_center)) ? $costCenterGroupData->cost_center : '';


                $bu_name = (isset($costCenterData->bu_name)) ? $costCenterData->bu_name : '';    



                $cost_centre = $cost_centre.' - '.$bu_name;

                if($Data->collected_amount!='0' && ($Data->payment_mode==22010 || $Data->payment_mode==22005)) {

                    $insertArray    = array();



                if($Data->payment_mode==22010) {

                    $ledger_group = env('CASH_LEDGER_GROUP');

                    $ledger_account = env('CASH_LEDGER_ACCOUNT');
     
                } else {
     
                    $ledger_group = env('NONCASH_LEDGER_GROUP');

                    $ledger_account = env('NONCASH_LEDGER_ACCOUNT');
     
                } 

                    $insertArray[]  = array('voucher_code'=>$Data->collection_code,
                                            'voucher_type'=>'Receipt',
                                            'voucher_date'=>$Data->collection_date,
                                            'ledger_group'=>$ledger_group,
                                            'ledger_account'=>$ledger_account,
                                            'tran_type'=>'Dr',
                                            'amount'=>$Data->collected_amount,
                                            'naration'=>'Being Collection received from Retailer : '.$Data->shop_name.'  Ief. Order/Inv. '.$Data->invoice_code,
                                            'cost_centre'=>$cost_centre,
                                            'cost_centre_group'=>$cost_centre_group,
                                            'reference_no'=>$Data->invoice_code,
                                            'is_posted'=>0,
                                            'Remarks'=>'Receipt Entry - By Script - Repost');

                    $insertArray[]  = array('voucher_code'=>$Data->collection_code,
                                            'voucher_type'=>'Receipt',
                                            'voucher_date'=>$Data->collection_date,
                                            'ledger_group'=>'Sundry Debtors',
                                            'ledger_account'=>$Data->shop_name.' - '.$Data->le_code,
                                            'tran_type'=>'Cr',
                                            'amount'=>$Data->collected_amount,
                                            'naration'=>'0',
                                            'cost_centre'=>$cost_centre,
                                            'cost_centre_group'=>$cost_centre_group,
                                            'reference_no'=>$Data->invoice_code,
                                            'is_posted'=>0,
                                            'Remarks'=>'Receipt Entry - By Script - Repost');
                
                    
                    DB::table('vouchers')->insert($insertArray);
                    
                    echo $sno.' Receipt voucher for '.$Data->collection_code.'</br>';
                    $sno++;

                }
            }

        }
                DB::commit();






        }            
        catch(Exception $e) {
        
            DB::rollback(); 


            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());

            return false;
        }


    }    


    public function getCollRemHistDetail($remId) {
        try {

            $selectArr  = array('remittance_code','collected_amt','due_amount','coins_onhand','notes_onhand','used_expenses','denominations','fuel','vehicle','fuel_image','vehicle_image','by_cash','by_ecash','by_cheque','by_upi','amount_deposited');
            return DB::table('collection_remittance_history')->select($selectArr)->where('remittance_id',$remId)->first();


          }

        catch(Exception $e) {

            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
    
            return false;
        }

    }


    public function updateRemittanceDetail($remId,$data) {

        try {
        
            DB::table('collection_remittance_history')->where('remittance_id',$remId)->update($data);        

            return true;
        }
        catch(Exception $e) {

            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
    
            return false;
        }


    }


    public function getConsolidatedRemittanceDetail($remIds) {

        try {
        
            $selectArr  = array(DB::raw('sum(collected_amt) as collected_amt'),
                                'le_wh_id',
                                'hub_id',
                                DB::raw('sum(amount_deposited) as amount_deposited'),
                                DB::raw('sum(by_cash) as by_cash'),
                                DB::raw('sum(by_cheque) as by_cheque'),
                                DB::raw('sum(by_online) as by_online'),
                                DB::raw('sum(by_upi) as by_upi'),
                                DB::raw('sum(by_ecash) as by_ecash'),
                                DB::raw('sum(by_pos) as by_pos'),
                                DB::raw('sum(fuel) as fuel'),
                                DB::raw('sum(vehicle) as vehicle'),
                                DB::raw('sum(used_expenses) as used_expenses'),
                                DB::raw('sum(due_amount) as due_amount'),
                                DB::raw('sum(arrears_deposited) as arrears_deposited')
                                );

            return DB::table('collection_remittance_history')->select($selectArr)->whereIn('remittance_id',$remIds)->first();
        }
        catch(Exception $e) {

            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
    
            return false;
        }


    }


    public function createConsolidatedRemittance($data) {

        try {

                $remId = DB::table('collection_remittance_history')->insertGetId($data);

                return $remId;

        }
        catch(Exception $e) {

            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
    
            return false;
        }
    }    


    public function updateParentRemittance($remId,$Ids) {

        try {

                DB::table('collection_remittance_history')->whereIn('remittance_id',$Ids)->update(array('is_parent'=>$remId));

                return true;

        }
        catch(Exception $e) {

            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
    
            return false;
        }
    }


    public function getChildRemittanceDetail($parentId) {

        try {
        
            $selectArr  = array('remittance_id');

            return DB::table('collection_remittance_history')->select($selectArr)->where('is_parent',$parentId)->get()->all();
        }
        catch(Exception $e) {

            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
    
            return false;
        }


    }


    public function getCollDateByRemId($remId) {

        try {
        
            $childRemCount = DB::table('collection_remittance_history')
                            ->where('is_parent',$remId)            
                            ->select(DB::raw('COUNT("remittance_id") as rem_count'))->first();

            if(isset($childRemCount->rem_count) && $childRemCount->rem_count==0) {

                $remittenceId = array($remId);
            } else {
                $remittenceId = DB::table('collection_remittance_history')
                        ->where('is_parent',$remId)            
                        ->pluck('remittance_id')->all();
            }            


            $data = DB::table('collections')
                    ->select(array(
                                    DB::raw('group_concat(distinct DATE(collections.created_on)) as collection_date')
                                    ))
                    ->join('remittance_mapping as mapping','collections.collection_id','=','mapping.collection_id')
                    ->join('collection_remittance_history as history','history.remittance_id','=','mapping.remittance_id')
                    ->whereIn('history.remittance_id',$remittenceId)
                    ->first();
            

            return $data;
        }
        catch(Exception $e) {

            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
    
            return false;
        }


    }

    public function getUsersByRole($roleName) {
        $result = DB::table('users')
                    ->select('users.user_id','users.firstname','users.lastname','users.email_id','users.mobile_no')
                    ->join('user_roles', 'users.user_id', '=', 'user_roles.user_id')
                    ->join('roles', 'roles.role_id', '=', 'user_roles.role_id')
                    ->where(array('users.is_active'=>1))
                    ->whereIn('roles.short_code', $roleName);
            // Checking the Global Access to View & Assign all the Users
            $roleobj=new RoleRepo();
            $globalAccess = $roleobj->checkPermissionByFeatureCode("GLB0001");
            if(!$globalAccess){
                // If the logged in User doesnot have access then we
                // restrict him with specific legal entity users
                $legalEntityId = Session::get('legal_entity_id');
                $result = $result->where('users.legal_entity_id',$legalEntityId);
            }
            $result = $result
                    ->orderBy('users.firstname')
                    ->get()->all();
        return $result;
    }

    public function getDcHubDataByAcess($dc_hub_ids){
        $dc_hub_query ="select le_wh_id,legal_entity_id,le_wh_code,CONCAT(lp_wh_name,' ','(',le_wh_code,')') as 'name' from legalentity_warehouses where le_wh_id In(".$dc_hub_ids.")";  
        $queryResult=DB::select(DB::raw($dc_hub_query));
       return $queryResult;

    }
    public function getBrandDetailsByDC($dcNames,$brandNames,$manufacturerName,$fromDate,$toDate,$supplierId){
     $query = DB::selectFromWriteConnection(DB::raw("CALL getBrandDetailsByDC(".$dcNames.",".$brandNames.",".$manufacturerName.",'".$fromDate."','".$toDate."',$supplierId)")); 
     return $query;
    }
    public function getInventoryDataFromTable($dc_id,$brand_id,$manufacturer_id,$invSupplierID){
     $query = DB::selectFromWriteConnection(DB::raw("CALL getCurrentInventoryByDC(".$dc_id.",".$brand_id.",".$manufacturer_id.",".$invSupplierID.")")); 
     return $query;
    }
    public function getBrandsAndManufacture($user_id){
        $brandslist=DB::table('user_permssion')
                           ->where(['permission_level_id' => 7, 'user_id' => $user_id])
                         ->pluck('object_id')->all();
        $manufacturerlist=DB::table('user_permssion')
                       ->where(['permission_level_id' => 11, 'user_id' => $user_id])
                     ->pluck('object_id')->all();            
        
        // if(!empty($manufacturer)){
        //     $brandsFromManufacturer=DB::table('brands')
        //                         ->whereIn('mfg_id',$manufacturer)
        //                         ->lists('brand_id');
        //     $finalBrandsArray=implode(',',array_unique(array_merge($brands,$brandsFromManufacturer)));
        //     $finalBrandsArray=explode(',',$finalBrandsArray);
            
        // }else{
        //     if(!in_array(0, $brands)){
        //         $finalBrandsArray = $brands;
        //     }
        // }
        $brands=DB::table('brands');
        if(!in_array(0, $brandslist))
        {
            $brands=$brands->whereIn('brand_id',$brandslist);
        }
        $brands=$brands->pluck('brand_name','brand_id')->all();

        $manufacturerData = DB::table('legal_entities');
    // ->where(['parent_id' => $legalEntityId]) removing legal entity check for brands
    $manufacturerData =$manufacturerData ->where(['legal_entity_type_id' => 1006]);
    if(!in_array(0, $manufacturerlist))
    {
        $manufacturerData =$manufacturerData->whereIn('legal_entity_id',$manufacturerlist);
    }
    $manufacturerData =$manufacturerData->groupBy('legal_entity_id')
    ->pluck('business_legal_name', 'legal_entity_id')->all();                        
           //echo '<pre/>';print_r($manufacturerData);exit;
        return array(['brands'=>$brands,
            'manufacturer'=>$manufacturerData]);
    }
    public function dcFCMappingTable($ids){
        $query = DB::table('dc_fc_mapping')->select('fc_le_wh_id')->whereIn('dc_le_wh_id',explode(',', $ids))->get()->all();
        return $query;
    }
    public function suppliersName($userID){
        // $query = DB::table('legal_entities as le')->select(['le.legal_entity_id',DB::RAW("concat(le.business_legal_name,' (',s.erp_code,')') as business_legal_name"),'le.city'])->join('suppliers as s','le.legal_entity_id','=','s.legal_entity_id')->where('le.legal_entity_type_id',1002)->orderBy('le.business_legal_name','ASC')->get();
        $data = "SELECT`le`.`legal_entity_id`,CONCAT(IFNULL(le.business_legal_name,''),' (',IFNULL(s.erp_code,''),')') AS business_legal_name,`le`.`city`
                                FROM `legal_entities` AS `le`INNER JOIN `suppliers` AS `s`ON `le`.`legal_entity_id` = `s`.`legal_entity_id`WHERE `le`.`legal_entity_type_id` = 1002 ORDER BY `le`.`business_legal_name` ASC";
        $query = DB::select(DB::raw($data));
        return $query;
    }
    public function suppliersNamesByBrandsAccess($userID){
        $brandsList = $this->brandNames($userID);
        $brandJson_decode = json_decode(json_encode($brandsList), true);
        $brandArrayIndex = array_values(array_column($brandJson_decode, 'brand_id'));
        $brandID = implode(',',$brandArrayIndex);
        $query ="SELECT GROUP_CONCAT(supplier_id) as suppliers FROM `supplier_brand_mapping`";
        if(!in_array(0, $brandArrayIndex)){
            $brandID=$brandID.',0';
            $query.=" Where brand_id In (".$brandID.")";    
        }
         $query.=" limit 1";
        $result = DB::select(DB::raw($query));
        $suppliers = isset($result[0]->suppliers)?$result[0]->suppliers:'';
        $r =explode(',',$suppliers);
        $legalEntityQuery = DB::table('legal_entities as le')->select(['le.legal_entity_id','le.business_legal_name'])->whereIn('le.legal_entity_id',$r)->groupBy('le.legal_entity_id')->get();
        return $legalEntityQuery;
    }
    public function brandNames($user_id,$manufids=''){
        $user_permssion ="SELECT GROUP_CONCAT(object_id)as brand_id  FROM `user_permssion` WHERE user_id = $user_id AND permission_level_id =7 limit 1";
        $brandQuery = DB::select(DB::raw($user_permssion));
        $brandID = isset($brandQuery[0]->brand_id) ? $brandQuery[0]->brand_id:'';
        $explode=explode(',', $brandID);
        $query =DB::table('brands')->select(['brand_id','brand_name']);
        $user_permssion_manuf="SELECT GROUP_CONCAT(object_id)as manuf_id  FROM `user_permssion` WHERE user_id = $user_id AND permission_level_id =11 limit 1";
        $manufQuery = DB::select(DB::raw($user_permssion_manuf));
        $manufexplode=explode(',', $manufids);
        $manufArray=explode(',', $manufQuery[0]->manuf_id);
        if(!in_array(0, $manufexplode)){
            
            $query->whereIn('mfg_id',$manufexplode);
        }elseif(in_array(0, $manufexplode) && !in_array(0, $manufArray)){
                
                $query->where('mfg_id',$manufArray);
        }elseif(in_array(0, $manufArray)){

        }else{
            //print_r($manufQuery[0]->manuf_id);exit;
            $manufArray=explode(',', $manufQuery[0]->manuf_id);
            $query->whereIn('mfg_id',$manufArray);
        }
        if(!in_array(0, $explode)){
            $query->whereIn('brand_id',$explode);
        }
        $result = $query->get();

        return $result;
    }
    public function suppliersDataInsertions($supplier_id,$brand_id,$manufacturer){
        $date=date('Y-m-d H:i:s');
        $check = DB::table('supplier_brand_mapping')->select('supplier_brand_map_id','supplier_id','brand_id','manufacturer_id')->where('supplier_id','=',$supplier_id)->first();
        if (!empty($check->supplier_id)) {
            //$brand_id=$check->brand_id.','.$brand_id;
            $brand_id=explode(',', $brand_id);
            array_unique($brand_id);
            if(in_array(0, $brand_id)){
                $brand_id=array('0');
            }
            $brand_id=implode(',', $brand_id);
            //$manufacturer=$check->manufacturer_id.','.$manufacturer;
            $manufacturer=explode(',', $manufacturer);
            array_unique($manufacturer);
            if(in_array(0, $manufacturer)){
                $manufacturer=array('0');
            }
            $manufacturer=implode(',', $manufacturer);
            $query = DB::table('supplier_brand_mapping')
                    ->where('supplier_id','=',$check->supplier_id)
                    ->where('supplier_brand_map_id','=',$check->supplier_brand_map_id)
                    ->update(['brand_id'=>$brand_id,'manufacturer_id'=>$manufacturer,'updated_by'=>Session::get('userId'),'updated_at'=>$date]);
        }else{
            $brand_id=explode(',', $brand_id);
            array_unique($brand_id);
            if(in_array(0, $brand_id)){
                $brand_id=array('0');
            }
            $brand_id=implode(',', $brand_id);
            $manufacturer=explode(',', $manufacturer);
            array_unique($manufacturer);
            if(in_array(0, $manufacturer)){
                $manufacturer=array('0');
            }
            $manufacturer=implode(',', $manufacturer);
            $query = DB::table('supplier_brand_mapping')
                    ->insert(['supplier_id'=>$supplier_id,'brand_id'=>$brand_id,'manufacturer_id'=>$manufacturer,'created_by'=>Session::get('userId'),'created_at'=>$date]);    
        }
        if ($query==1) {
            return 1;
        }else{
            return 0;
            }
    }
    public function getSupplierGridData($makeFinalSql, $orderBy, $page, $pageSize){
        if($orderBy!=''){
            $orderBy = ' ORDER BY ' . $orderBy;
        }
        $allData=array();
        $sqlWhrCls = '';
        $countLoop = 0;
        foreach ($makeFinalSql as $value) {
            if( $countLoop==0 ){
                $sqlWhrCls .= 'WHERE ' . $value;
            }elseif(count($makeFinalSql)==$countLoop ){
                $sqlWhrCls .= $value;
            }else{
                $sqlWhrCls .= 'AND' .$value;
            }
            $countLoop++;
        }
        $roleobj=new RoleRepo();
        $suppliereditAccess = $roleobj->checkPermissionByFeatureCode("SUPLEDT001");
        $supplierdeleteAccess = $roleobj->checkPermissionByFeatureCode("SUPLDLT001");
        if($suppliereditAccess){
            $editsupplier='<a href=\'javascript:void(0)\' onclick=\'editGridData(",sp.supplier_brand_map_id,")\'>
              <i class=\'fa fa-pencil\'></i></a>';
          }else{
            $editsupplier='';
          }
         if($supplierdeleteAccess){     
            $deletesupplier='<a href=\"javascript:void(0)\" onclick=\"deleteData(",sp.supplier_brand_map_id,")\">
              <i class=\"fa fa-trash-o\"></i>
              </a>';     
          }else{
            $deletesupplier='';
          }
        //$concat_query = 'CONCAT("<center><code>","<a href=\'javascript:void(0)\' onclick=\'editGridData(",sp.supplier_brand_map_id,")\'>
          //    <i class=\'fa fa-pencil\'></i></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"javascript:void(0)\" onclick=\"deleteData(",sp.supplier_brand_map_id,")\">
            //  <i class=\"fa fa-trash-o\"></i>
              //</a></code> </center>") AS `CustomAction`';
              $concat_query = 'CONCAT("<center><code>","'.$editsupplier.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$deletesupplier.'</code> </center>") AS `CustomAction`';
        
        $data = "SELECT ".$concat_query.", le.`business_legal_name`,fn_getSupplierCode(le.legal_entity_id) as supplier_code, le.`legal_entity_id`,le.`city`,CASE sp.brand_id WHEN 0 THEN 'All' ELSE  (select group_concat(b.brand_name) from brands b where find_in_set(b.brand_id,sp.brand_id))  END  AS brand_name,  CASE sp.manufacturer_id WHEN 0 THEN 'All' ELSE (select group_concat(l.business_legal_name) from legal_entities l where find_in_set(l.legal_entity_id,sp.manufacturer_id) and l.legal_entity_type_id in (1006)) END as manuf_name,sp.`supplier_id` ,sp.`supplier_brand_map_id` FROM supplier_brand_mapping AS sp JOIN legal_entities AS le ON le.legal_entity_id = sp.`supplier_id` ".$sqlWhrCls." GROUP BY le.legal_entity_id ".$orderBy;
        $pageLimit = "";
        $allcount = DB::select(DB::raw($data));
        $allData['count']=count($allcount);
        if($page!='' && $pageSize!=''){
            $pageLimit = " LIMIT " . (int)($page*$pageSize) . ", " . $pageSize;
        }      
        $allData['records'] = DB::select(DB::raw($data.$pageLimit));
        return $allData;
    }
    public function getSupplierMappingID($fieldname,$id,$fieldname2='',$sid=''){
        
        $query = "SELECT  * FROM supplier_brand_mapping AS sp  ";
        if($fieldname!='' && ($id!='' || $id!='0')){
            $query.=" where sp.".$fieldname. " in (".$id.")";
        }
        if($fieldname!='' && $fieldname2!='' && $sid!=''){
            $query.=' and ';
        }elseif($fieldname=='' && $fieldname2!=''){
            $query.=' where ';
        }

        if($fieldname2!='' && $sid!=''){
            $query.="sp.".$fieldname2. " in (".$sid.")";
        }
        $result =DB::select(DB::raw($query));
        return $result;            
    }
    public function updateQuery($supplier_brand_map_id,$supplier_name_edit,$brand_name_edit,$manufacturer_name_edit){
        $date=date('Y-m-d H:i:s');
        $query = DB::table('supplier_brand_mapping')
                    ->where('supplier_brand_map_id','=',$supplier_brand_map_id)
                    ->update(['supplier_id'=>$supplier_name_edit,
                                'brand_id'=>$brand_name_edit,
                                'manufacturer_id'=>$manufacturer_name_edit,
                                'updated_by'=>Session::get('userId'),
                                'updated_at'=>$date,
                                'created_by'=>Session::get('userId'),
                                'created_at'=>$date
                            ]);
        return $query;
    }
    public function deleteSupplierMapping($id){
        $query = DB::table('supplier_brand_mapping')
                    ->where('supplier_brand_map_id','=',$id)
                    ->delete();
        return $query;
    }
    // public function getBrandId($id){
    //     $query = DB::table("brands")->select(['brand_id','brand_name'])->whereIn('mfg_id',$id)->get();
    //     return $query;
    // }
    public function getBrandForSuppliers($id,$mnID){
         $manufacturerlist=DB::table('user_permssion')
                       ->where(['permission_level_id' => 11, 'user_id' => Session::get('userId')])
                     ->pluck('object_id')->all();
         $brandlist=DB::table('user_permssion')
           ->where(['permission_level_id' => 7, 'user_id' => Session::get('userId')])
         ->pluck('object_id')->all();
        $query = DB::table('supplier_brand_mapping as sp')
                    ->select('sp.supplier_id','le.business_legal_name');
                    if(in_array(0, $mnID)){
                        $mnID=array_merge($manufacturerlist,$mnID);
                        array_push($mnID, 0);
                        $mnID=implode('|', $mnID);
                        $query = $query->whereRaw('CONCAT(",", manufacturer_id, ",") REGEXP ",('.$mnID.'),"');

                    }elseif(!in_array(0, $mnID)){
                        array_push($manufacturerlist, 0);
                        $manufacturerlist=implode('|', $manufacturerlist);
                        $query = $query->whereRaw('CONCAT(",", manufacturer_id, ",") REGEXP ",('.$manufacturerlist.'),"');
                    }else{

                    }
                    $id=explode(',', $id);
                    if(in_array(0, $id)){
                        $id=array_merge($brandlist,$id);
                        $id=implode('|', $id);
                        $query = $query->orwhereRaw('CONCAT(",", brand_id, ",") REGEXP ",('.$id.'),"');
                    }elseif(!in_array(0, $id)){
                        array_push($id,0);
                        $id=implode('|', $id);
                        $query = $query->orwhereRaw('CONCAT(",", brand_id, ",") REGEXP ",('.$id.'),"');
                    }else{

                    }
                $query = $query->join('legal_entities as le','le.legal_entity_id','=','sp.supplier_id')
                    ->get();
        return $query;
    }
    public function getSuppliersListForAllBrand($brandID){
        $query = DB::table('supplier_brand_mapping')->select('supplier_id');
        if($brandID!='0'){
            $brandID = trim($brandID,"'");
            $brandID=str_replace(',', '|', $brandID);
             $query =$query->orwhereRaw('CONCAT(",", brand_id, ",") REGEXP ",('.$brandID.'),"');
         }
        $query =$query->get()->all();
        return $query;
    }
    public function getBrandIDS($id,$flag=''){
        $query = DB::table('brands')->select(DB::raw('group_concat(brand_id) as brand_id','brand_name'));
        if($flag==1) {
            $query=$query->whereIn('mfg_id', $id)->first();
        }else{
            $query=$query->whereIn('mfg_id',explode(',',$id))->first();
            }
            // print_r($query);die();
        $query=$query->brand_id;
        return $query;
    }
}
