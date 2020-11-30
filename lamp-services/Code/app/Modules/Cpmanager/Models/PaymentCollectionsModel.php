<?php
namespace App\Modules\Cpmanager\Models;
use Illuminate\Database\Eloquent\Model;
use App\Modules\Cpmanager\Views\order;
use App\Modules\Roles\Models\Role;
use DB;
use views;
use view;
use Config;
use App\Central\Repositories\RoleRepo;
use App\Central\Repositories\CustomerRepo;
use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;
use App\Modules\Ledger\Models\LedgerModel;
use Log;
use Utility;

use App\Central\Repositories\ProductRepo;
use Response;



class PaymentCollectionsModel extends Model
{

    protected $_roleRepo;
    protected $_roleModel;
    protected $_approvalFlowModel;
    protected $_ledgerModel;

    public function __construct() {
        $this->_roleRepo = new RoleRepo();
        $this->_roleModel = new Role();
        $this->_approvalFlowModel= new CommonApprovalFlowFunctionModel();
        $this->_ledgerModel = new LedgerModel();
        $this->repo = new ProductRepo();         
    }
/* get remitence details */
  public function getRemittanceDetails($data) {
      try {


            $Json = json_decode($this->_roleModel->getFilterData(6,$data['user_id']),1);
            $Json = json_decode($Json['sbu'],1);

            $currentUserRole = $this->_roleRepo->getMyRoles($data['user_id']);

            $le_wh_id = isset($data['le_wh_id']) ? $data['le_wh_id'] : 2;

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
                                'remittance_history.collected_amt',
                                'remittance_history.approval_status',
                                'remittance_history.by_cash',
                                'remittance_history.by_cheque',
                                'remittance_history.by_online',
                                'remittance_history.by_upi',
                                'remittance_history.by_ecash',
                                'remittance_history.by_pos',
                                'remittance_history.coins_onhand',
                                'remittance_history.notes_onhand',
                                'remittance_history.used_expenses',
                                'remittance_history.due_amount as short_amount',
                                'remittance_history.denominations',
                                'remittance_history.fuel',
                                'remittance_history.vehicle as extra_vehicle',
                                'remittance_history.amount_deposited',
                                'remittance_history.arrears_deposited as due_deposited',
                                'legalentity_warehouses.lp_wh_name as hub_name',
                                DB::raw('GetUserName(remittance_history.acknowledged_by,2) as acknowledged_by'),
                                'remittance_history.acknowledged_at',
                                DB::raw('getLeWhName(remittance_history.le_wh_id) as DCName'),
                                DB::raw('getMastLookupValue(remittance_history.status) as remittance_status'),
                                DB::raw('GetUserName(remittance_history.submitted_by,2) as SubmittedByName'),
                                DB::raw('getMastLookupValue(remittance_history.approval_status) as StatusName'),
                                DB::raw('getNextRolePaymentApproval(remittance_history.approval_status,'.$awf_id.') as next_lbl_role')
                                );


            // prepare sql

            $query =  DB::table('collection_remittance_history as remittance_history')
                      ->select($fieldArr);

            if(isset($data['start_date']) && $data['start_date'] !='' && isset($data['end_date']) && $data['end_date']!='') {

                  
                  $Del_FDate = date('Y-m-d',strtotime($data['start_date']));
                  $Del_TDate = date('Y-m-d',strtotime($data['end_date']));

            } else {


                  $Del_FDate = date('Y-m-d');
                  $Del_TDate = date('Y-m-d');

            }         

            $query->join('remittance_mapping as mapping','mapping.remittance_id','=','remittance_history.remittance_id')
                  ->join('collections','collections.collection_id','=','mapping.collection_id')
                  ->join('gds_orders','gds_orders.gds_order_id','=','collections.gds_order_id')
                  ->leftjoin('legalentity_warehouses','gds_orders.hub_id','=','legalentity_warehouses.le_wh_id')
                  ->where('remittance_history.submitted_at','>=',$Del_FDate.' 00:00:00')
                  ->where('remittance_history.submitted_at','<=',$Del_TDate.' 23:59:59');

            if(isset($data['del_exec']) && $data['del_exec']!='') {

                $query->where('remittance_history.submitted_by','=',$data['del_exec']);

            }


            if(isset($data['appr_status']) && $data['appr_status']!='') {

                $query->where('remittance_history.approval_status','=',$data['appr_status']);

            }


            if(isset($Json['118001'])) {

                $Dcs_Assigned = implode(',',explode(',',$Json['118001']));

                $query->whereRaw("gds_orders.le_wh_id IN ($Dcs_Assigned)");
            }

            if(isset($Json['118002'])) {
                $Hubs_Assigned = implode(',',explode(',',$Json['118002']));

                $query->whereRaw("gds_orders.hub_id IN ($Hubs_Assigned)");
            }


            $query=$query->groupBy('remittance_history.remittance_id')->orderBy('remittance_history.remittance_id','desc');

            $query=$query->get()->all();

            foreach($query as $k=>$value) {

                    $query[$k]->acknowledge = '';

                    if(in_array($query[$k]->next_lbl_role,$currentUserRole)) {
                      
                        $approvalOptions = array();

                        $apprChk = $this->_approvalFlowModel->getApprovalFlowDetails('Payment', $query[$k]->approval_status, $data['user_id']);
                        
                        if(isset($apprChk['status']) && $apprChk['status']==1) {

                            foreach($apprChk['data'] as $apprArr) {
                              
                              $approvalOptions[] = array('nextStatusId'=>$apprArr['nextStatusId'], 'isFinalStep'=>$apprArr['isFinalStep'], 'condition'=>$apprArr['condition']);                              
                            }

                        }

                        $query[$k]->acknowledge = $approvalOptions;

                    }  

            }  


            return json_decode(json_encode($query),true);

      }
      catch(Exception $e) {
              Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
      }
  }

    public function getRemittanceCollectionDetails($remittenceId) {

      try {

          


          $selectArr = array(
                                  'collections.collection_id',
                                  'collections.gds_order_id as order_id',
                                  'collections.invoice_id',
                                  'collections.customer_name as shop_name',
                                  'collections.invoice_code',
                                  'collections.invoice_amount as invoice_total',

                                  'ml.master_lookup_name as payment_mode',
                                  'collections.return_id',
                                  'collections.return_code as sales_reference_no',
                                  'collections.return_total as sales_return_amount',
                                  'collections.le_wh_id',
                                  'collections.status',
                                  'collection_history.amount as collected_amount',
                                  'collection_history.ecash',
                                  'collections.order_code'
                                  );

          // $selectArr = array(
          //                         'collections.*',
          //                         'ml.master_lookup_name as payment_mode',
          //                         'collection_history.proof',
          //                         );

          $data = DB::table('remittance_mapping as mapping')
                  ->select($selectArr)
                  ->join('collections','collections.collection_id','=','mapping.collection_id')
                  ->join('collection_history','collection_history.collection_id','=','mapping.collection_id')
                  ->leftjoin('master_lookup as ml','ml.value','=','collection_history.payment_mode')
                  ->where('mapping.remittance_id','=',$remittenceId)
                  ->get()->all();

            return json_decode(json_encode($data),true);

    }              
      catch(Exception $e) {
              Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
      }
  }

  public function submitApprovalStatus($data) {

            $user_id = $data['user_id'];
            $approval_unique_id = $data['approval_unique_id'];
            $approval_status = $data['approval_status'];
            $approval_module = $data['approval_module'];
            $current_status = $data['current_status'];
            $approval_comment = $data['approval_comment'];
            $table = $data['table_name'];
            $unique_column = $data['unique_column'];
            $remDetail = $this->_ledgerModel->getCollRemHistDetail($approval_unique_id);
            $approval_status_array = explode(',',$approval_status);
            $fuel_image = '';
            $voucher_image = '';

            $remIds = explode(',',$approval_unique_id);

            if($current_status==57055) {

                $short = isset($data['short']) ? $data['short'] : 0;
                $due_amount = isset($data['due_amount']) ? $data['due_amount'] : 0;
                $coins_on_hand = isset($data['coins_on_hand']) ? $data['coins_on_hand'] : 0;
                $notes_on_hand = isset($data['notes_on_hand']) ? $data['notes_on_hand'] : 0;
                $used_expenses = isset($data['used_expenses']) ? $data['used_expenses'] : 0;
                $denominations = isset($data['denominations']) ? $data['denominations'] : array();

                $fuel = isset($data['fuel']) ? $data['fuel'] : 0;
                $extra_vehicle = isset($data['extra_vehicle']) ? $data['extra_vehicle'] : 0;

                $fuel_image = isset($data['fuel_image']) ? $data['fuel_image'] : '';
                $voucher_image = isset($data['voucher_image']) ? $data['voucher_image'] : '';

                $submitted_to_bank = isset($data['submitted_amount']) ? $data['submitted_amount'] : 0;

                $approval_comment.= ' (Total Amount:'.$remDetail->collected_amt.', Submitted To Bank:'.$submitted_to_bank.', Due Amount:'.$due_amount.', Coins:'.$coins_on_hand.', Notes:'.$notes_on_hand.', Expenses:'.$used_expenses.', Fuel:'.$fuel.' Other Vehicle:'.$extra_vehicle.')';
                $due_deposited = isset($data['arrears_deposited']) ? $data['arrears_deposited'] : 0;


                    if($fuel>0) {

                      if(isset($_FILES['fuel_image'])){
                          
                          $filepath2_move = date("Y-m-d-H-i-s")."_". $_FILES['fuel_image']['name'];
                          $fuel_image="uploads/cp/".$filepath2_move;
                          move_uploaded_file($_FILES['fuel_image']['tmp_name'], "uploads/cp/".$filepath2_move);
                          $fuel_image=$this->repo->uploadToS3($fuel_image,'fuel',2);
                      }

                      $request_array = array('ExpID'=>0,
                      'ExpDetActualAmount'=>$fuel,
                      'ExpDetType'=>'123005',
                      'ExpDetDate'=>date('Y-m-d'),
                      'Description'=>'Fuel',
                      'ExpDetProofKey'=>$fuel_image,
                      'UserID'=>$user_id,
                      'ExpDetRecordType'=>0
                      );                    

                      $url = env('EXP_LINE_API');

                      $this->addExpense($url,$request_array);
                      $path = $_SERVER['DOCUMENT_ROOT']."/uploads/cp/".$filepath2_move;
                      unlink($path);
//                      Log::info('deleting file'.$path);

                    }


                    if($extra_vehicle>0) {

                      if(isset($_FILES['voucher_image'])){
                          
                          $doc=$_FILES['voucher_image'];
                          $filepath2_move = date("Y-m-d-H-i-s")."_". $_FILES['voucher_image']['name'];
                          $voucher_image="uploads/cp/".$filepath2_move;
                          move_uploaded_file($_FILES['voucher_image']['tmp_name'], "uploads/cp/".$filepath2_move);
                          $voucher_image=$this->repo->uploadToS3($voucher_image,'vehicle',2);
                      }

                      $request_array = array('ExpID'=>0,
                      'ExpDetActualAmount'=>$extra_vehicle,
                      'ExpDetType'=>'123014',
                      'ExpDetDate'=>date('Y-m-d'),
                      'Description'=>'Vehicle Expense',
                      'ExpDetProofKey'=>$voucher_image,
                      'UserID'=>$user_id,
                      'ExpDetRecordType'=>0
                      );                    

                      $url = env('EXP_LINE_API');

                      $this->addExpense($url,$request_array);                      
                      $path = $_SERVER['DOCUMENT_ROOT']."/uploads/cp/".$filepath2_move;
                      unlink($path);
                    //  Log::info('deleting file'.$path);

                    }


                    $denominations = json_encode($denominations);
                    
                    $data = array('amount_deposited'=>$submitted_to_bank,'due_amount'=>$used_expenses,'fuel'=>$fuel,'vehicle'=>$extra_vehicle,'arrears_deposited'=>$due_deposited,'denominations'=>$denominations,'fuel_image'=>$fuel_image,'vehicle_image'=>$voucher_image);

                    $this->_ledgerModel->updateRemittanceDetail($approval_unique_id,$data);

                    if($used_expenses>0) {

                      $request_array = array('RequestFoID'=>'122004',
                      'RequestForTypeID'=>'122004',
                      'Subject'=>'Remittance Short',
                      'Amount'=>$used_expenses,
                      'SubmitDate'=>date('Y-m-d'),
                      'ReffIDs'=>$remDetail->remittance_code,
                      'SubmitedByID'=>$user_id
                      );                    

                      $url = env('ADV_EXP_API');

                      $this->addExpense($url, $request_array);


                  }  

            }
            if($current_status==57051) {

                $due_amount = isset($data['due_amount']) ? $data['due_amount'] : 0;
                $coins_on_hand = isset($data['coins_on_hand']) ? $data['coins_on_hand'] : 0;
                $notes_on_hand = isset($data['notes_on_hand']) ? $data['notes_on_hand'] : 0;
                $used_expenses = isset($data['used_expenses']) ? $data['used_expenses'] : 0;
                $denominations = isset($data['denominations']) ? $data['denominations'] : array();

                $fuel = isset($data['fuel']) ? $data['fuel'] : 0;
                $extra_vehicle = isset($data['extra_vehicle']) ? $data['extra_vehicle'] : 0;
                $arrears_deposited = isset($data['arrears_deposited']) ? $data['arrears_deposited'] : 0;

                $fuel_image = isset($data['fuel_image']) ? $data['fuel_image'] : '';
                $voucher_image = isset($data['voucher_image']) ? $data['voucher_image'] : '';

                $submitted_to_bank = isset($data['submitted_amount']) ? $data['submitted_amount'] : 0;

                $approval_comment.= ' (Total Amount:'.$remDetail->collected_amt.', Submitted To Bank:'.$submitted_to_bank.', Due Amount:'.$due_amount.', Coins:'.$coins_on_hand.', Notes:'.$notes_on_hand.', Expenses:'.$used_expenses.', Fuel:'.$fuel.' Other Vehicle:'.$extra_vehicle.')';

                $consolidatedRem = $this->_ledgerModel->getConsolidatedRemittanceDetail($remIds);
                $whresult = $this->_roleRepo->getLEWHDetailsById($consolidatedRem->le_wh_id);
                $state_code=isset($whresult->state_code)?$whresult->state_code:"TS";
                $new_remittance_code = Utility::getReferenceCode('RM',$state_code);

                $data = array(
                               'remittance_code'=>$new_remittance_code, 
                               'collected_amt'=>$consolidatedRem->collected_amt,
                               'amount_deposited'=>$submitted_to_bank,
                               'le_wh_id'=>$consolidatedRem->le_wh_id,
                               'hub_id'=>$consolidatedRem->hub_id,
                               'by_cash'=>$consolidatedRem->by_cash,
                               'by_cheque'=>$consolidatedRem->by_cheque,
                               'by_online'=>$consolidatedRem->by_online,
                               'by_upi'=>$consolidatedRem->by_upi,
                               'by_ecash'=>$consolidatedRem->by_ecash,
                               'fuel'=>$consolidatedRem->fuel,
                               'vehicle'=>$consolidatedRem->vehicle,
                               'due_amount'=>$consolidatedRem->due_amount,
                               'coins_onhand'=>$coins_on_hand,
                               'notes_onhand'=>$notes_on_hand,
                               'used_expenses'=>$used_expenses,
                               'submitted_by'=>$user_id


                            );
                
                $remId = $this->_ledgerModel->createConsolidatedRemittance($data);

                $this->_ledgerModel->updateParentRemittance($remId,$remIds);


                  if($used_expenses>0) {

                      $request_array = array('RequestFoID'=>'122004',
                      'RequestForTypeID'=>'122004',
                      'Subject'=>'Remittance Used For Expenses',
                      'Amount'=>$used_expenses,
                      'SubmitDate'=>date('Y-m-d'),
                      'ReffIDs'=>'',
                      'SubmitedByID'=>$user_id
                      );                    

                      $url = env('ADV_EXP_API');

                      $this->addExpense($url, $request_array);


                  }  


                $approval_comment.= ' (Total Amount:'.$remDetail->collected_amt.', Submitted To Bank:'.$submitted_to_bank.', Due Amount:'.$due_amount.', Coins:'.$coins_on_hand.', Notes:'.$notes_on_hand.', Expenses:'.$used_expenses.', Fuel:'.$fuel.' Other Vehicle:'.$extra_vehicle.')'; 

                $remIds[] = $remId;
              }


                foreach ($remIds as $Id) {
                    

                    $approval_flow_func= new CommonApprovalFlowFunctionModel();
                    $this->_ledgerModel->updateStatusAWF($table,$unique_column,$Id, $approval_status,\Session::get('userId'));
                    $approval_flow_func->storeWorkFlowHistory($approval_module, $Id, $current_status, $approval_status, $approval_comment, \Session::get('userId'));

                }


            if(isset($approval_status_array[1]) && $approval_status_array[1]=='1') {

          			//$this->_ledgerModel->saveCollectionVoucher($approval_unique_id);
          			//$this->_ledgerModel->checkOrderClosed($approval_unique_id);
          			//$this->_ledgerModel->completeOrdersByRemIdForApp($approval_unique_id,$user_id);

                $childRems = $this->_ledgerModel->getChildRemittanceDetail($approval_unique_id);

                foreach ($childRems as $childRem) {



                    $this->_ledgerModel->saveCollectionVoucher($childRem->remittance_id);
                    
                    $this->_ledgerModel->changeOrderPaymentStatusByRemId($childRem->remittance_id);
                    $this->_ledgerModel->completeReturnApprOrdersByRemId($childRem->remittance_id);



                    $orderIds = $this->_ledgerModel->getRemittanceReturnApprOrderIds($approval_unique_id);

                    foreach ($orderIds as $orderId) {

                            $this->_orderController->saveComment($orderId->gds_order_id, 'Order Status', array('comment'=>'Order completed and remittance # '.$childRem->remittance_id.' is approved', 'order_status_id'=>'17008'));
                    }    

                }


            }
  }

      public function getPaymentmodeByOrderId($orderId) {

      try {

          $fields = array('master_lookup.master_lookup_name','master_lookup.value','collections.collected_amount');
            
          $data = DB::table('collections')->select($fields)
                    ->join('collection_history as history','history.collection_id','=','collections.collection_id')
                    ->join('master_lookup','master_lookup.value','=','history.payment_mode')
                    ->where('collections.gds_order_id',$orderId)
                    ->first();

          return json_decode(json_encode($data),true);

      }              
      catch(Exception $e) {
              Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
      }
    }

    public function updatePaymentmodeByOrderId($orderId, $payment_mode) {

        try {

            $fields = array('collections.collection_id');
              
            $data = DB::table('collections')->select($fields)
                      ->join('collection_history as history','history.collection_id','=','collections.collection_id')
                      ->where('collections.gds_order_id',$orderId)
                      ->first();

            if(empty($data)) { // collection details for order_id is not found
              return false;
            }

            DB::table('collection_history')
                ->where('collection_id',$data->collection_id)
                ->update(array('payment_mode'=>$payment_mode));

            return true;

        }              
        catch(Exception $e) {
                Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }


    public function addExpense($url,$request_array) {

        try {


              $headers = array("cache-control: no-cache", "content-type: application/json", 'auth:E446F5E53AD8835EAA4FA63511E22');

              $response = Utility::sendcUrlRequest($url, $request_array, $headers);

              if(empty($response) || $response['code']!= 200) {

                  Log::error('Unable to save advances => '.json_encode($request_array));


                  return Response::json(array('status' => 200, 'message' => 'Cant save advance'));


              }

        }              
        catch(Exception $e) {
                Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    public function getTotalDueAmount() {
        try {
      
        }              
        catch(Exception $e) {
                Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }


}