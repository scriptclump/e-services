<?php

namespace App\Modules\Ledger\Controllers;


use App\Http\Controllers\BaseController;
use Response;
use Session;
use View;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Modules\Roles\Models\Role;
use URL;
use Log;
use DB;
use Hash;
use Carbon\Carbon;
use Utility;
use Excel;
use Redirect;

use App\Modules\Orders\Controllers\OrdersController;
use App\Modules\Ledger\Models\LedgerModel;
use App\Modules\Orders\Models\OrderModel;

use Illuminate\Support\Facades\Config;
//use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;
use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;
use App\Modules\Orders\Models\MasterLookup;
use App\Central\Repositories\ProductRepo;
use App\Central\Repositories\RoleRepo;
use App\Modules\Assets\Controllers\commonIgridController;
class LedgerController extends BaseController {


    protected $_orderModel;
    protected $_orderController;
    protected $_masterLookup;
    protected $_roleModel;
    protected $_roleRepo;


    public function __construct() {   
        try
        {
            $this->middleware(function ($request, $next) {
                if (!Session::has('userId')) {
                         Redirect::to('/login')->send();
                }
                return $next($request);
            });
            
            $this->_orderController = new OrdersController();
            $this->_orderModel = new OrderModel();
            $this->_ledgerModel = new LedgerModel();
            $this->_approvalFlowMethod= new CommonApprovalFlowFunctionModel();
            $this->_masterLookup = new MasterLookup();
            $this->repo = new ProductRepo();
            $this->_roleModel = new Role(); 
            $this->_roleRepo = new RoleRepo();
            $this->objCommonGrid = new commonIgridController();
            $this->grid_field_db_match = array(
                'remittance_code' => 'remittance_history.remittance_code',
                'submitted_at' => 'remittance_history.submitted_at',
                'SubmittedByName' => DB::raw('GetUserName(remittance_history.submitted_by,2)'),
                'DCName' => DB::raw('getLeWhName(remittance_history.le_wh_id)'),
                'hub_name' => 'legalentity_warehouses.lp_wh_name',
                'collected_amt' => 'remittance_history.collected_amt',
                'by_cash' => 'remittance_history.by_cash',
                'by_cheque' => 'remittance_history.by_cheque',
                'by_online' => 'remittance_history.by_online',
                'acknowledged_by' => DB::raw('GetUserName(remittance_history.acknowledged_by,2)'),
                'acknowledged_at' => 'remittance_history.acknowledged_at',
                'remittance_status' => DB::raw('getMastLookupValue(remittance_history.approval_status)'),
                'amount_deposited' => 'remittance_history.amount_deposited',
                'by_upi' => 'remittance_history.by_upi',
                'by_pos' => 'remittance_history.by_pos',
                'by_ecash' => 'remittance_history.by_ecash',
                'fuel' => 'remittance_history.fuel',
                'vehicle' => 'remittance_history.vehicle',
                'due_amount' => 'remittance_history.due_amount',
                'coins_onhand' => 'remittance_history.coins_onhand',
                'notes_onhand' => 'remittance_history.notes_onhand',
                'used_expenses' => 'remittance_history.used_expenses',
                'arrears_deposited' => 'remittance_history.arrears_deposited',
                'notes_onhand' => 'remittance_history.notes_onhand',
                'coins_notes_deposited' => 'remittance_history.coins_notes_deposited',
                'acknowledged_by' => DB::raw('GetUserName(remittance_history.acknowledged_by,2)'),
                'acknowledged_at'=>'remittance_history.acknowledged_at',
                'remittance_status'=>DB::raw('getMastLookupValue(remittance_history.status)'),
                'submit_on'=>'submit_on',
                'ack_on'=>'ack_on'
            );
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }        
    }
	
	
    public function indexAction()
    {
        try
        {

            parent::Title('Ebutor - Account Receivables');
            
            $deliveryUsers      =   $this->_ledgerModel->getUsersByRole(array('LDLO','LHBL'));
            /*$approvalStatusDetails = $this->_approvalFlowMethod->getApprovalFlowDetails('Payment', 'drafted', Session::get('userId'));
            
            if(isset($approvalStatusDetails["data"])){
                foreach($approvalStatusDetails["data"] as $eachData){
                    $approvalOptions[$eachData["nextStatusId"].",".$eachData["isFinalStep"]] = $eachData["condition"];
                }


                return View::make('Ledger::paymentsApproval',['deliveryUsers'=>$deliveryUsers, 'approvalOptions' => $approvalOptions]);            

            } else {
                Redirect::to('/')->send();
                die();
            }*/


            $remStatusArr    =   $this->_ledgerModel->getApprovalStatus();

            return View::make('Ledger::paymentsApproval',['deliveryUsers'=>$deliveryUsers,'remStatusArr'=>$remStatusArr]);

           
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

	public function getPaymentsByDelExec($Del_Exec, $Del_FDate, $Del_TDate, $Status) {

        try
        {


            $request_input = Input::all();

           // print_r($request_input);exit;

            // Arrange data for pagination
            $page="";
            $pageSize="";
            if( ($request_input['page'] || $request_input['page']==0)  && $request_input['pageSize'] ){
                $page = $request_input['page'];
                $pageSize = $request_input['pageSize'];
            }
            
            $filter_by = '';
            if (isset($request_input['$filter'])) {
                $filterBy = $request_input['$filter'];

            } elseif (isset($request_input['%24filter'])) {
                $filterBy = urldecode($request_input['%24filter']);
            }

            $orderby='';
            $order_query_field='';
            $order_query_type='';
            $order_by_type='';
            $order_by='';

             if (isset($request_input['$orderby'])){
                 //checking for sorting
                $order = explode(' ', $request_input['$orderby']);


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


            if (isset($filterBy)) {


                if (strpos($filterBy, 'submit_on') !== false) {


                    if(strpos($filterBy,'and')!== false){

                        $filter_explode = explode(' and ', $filterBy);


                         $findSymbol = strpos($filterBy, "month");
                        if($findSymbol>0){

                            $day = sprintf('%02d',substr($filter_explode[0],-2));
                            $month = sprintf('%02d',substr($filter_explode[1],-2));
                            $year = substr($filter_explode[2],-4);
                            $filterDate = $year.'-'.$month.'-'.$day;

                            $filter_query_field="`remittance_history`.`submitted_at`";
                            $filter_query_operator=substr($filter_explode[0],-5,-3);
                            $filter_query_value=$filterDate;
                        }

                    }

                    elseif (strpos($filterBy,'or')!== false) {
                         $filter_explode = explode(' or ', $filterBy);

                         $findSymbol = strpos($filterBy, "month");
                        //print_r($filterBy);
                        if($findSymbol>0){


                            $day = substr($filter_explode[0],-2);

                            $month = substr($filter_explode[1],-2);
                            $year = substr($filter_explode[2],-4);
                            $filterDate = $year.'-'.$month.'-'.$day;

                            $filter_query_field="`remittance_history`.`submitted_at`";
                            $filter_query_operator=substr($filter_explode[0],-5,-3);
                            $filter_query_value=$filterDate;
                        }


                    }

                    elseif(strpos($filterBy,'lt')!== false){

                        $date=substr($filterBy,-11,-1);
                        $filter_query_field="`remittance_history`.`submitted_at`";
                        $filter_query_operator='lt';
                        $filter_query_value=$date;

                    }

                    elseif(strpos($filterBy,'gt')!== false){

                        $date=substr($filterBy,-20,-10);
                        $filter_query_field="`remittance_history`.`submitted_at`";
                        $filter_query_operator='gt';
                        $filter_query_value=$date;

                    }
                    switch ($filter_query_operator) {
                            case 'eq' :
                                $filter_operator = ' = ';
                                break;

                            case 'ne':
                                $filter_operator = ' != ';
                                break;

                            case 'gt' :
                                $filter_operator = ' > ';
                                break;

                            case 'lt' :
                                $filter_operator = ' < ';
                                break;

                            case 'ge' :
                                $filter_operator = ' >= ';
                                break;

                            case 'le' :
                                $filter_operator = ' <= ';
                                break;
                        }
                    $filter_by[] = $filter_query_field . $filter_operator . $filter_query_value;          

                }
                else if (strpos($filterBy, 'ack_on') !== false) {


                    if(strpos($filterBy,'and')!== false){

                        $filter_explode = explode(' and ', $filterBy);


                         $findSymbol = strpos($filterBy, "month");
                        if($findSymbol>0){

                            $day = sprintf('%02d',substr($filter_explode[0],-2));
                            $month = sprintf('%02d',substr($filter_explode[1],-2));
                            $year = substr($filter_explode[2],-4);
                            $filterDate = $year.'-'.$month.'-'.$day;

                            $filter_query_field="`remittance_history`.`acknowledged_at`";
                            $filter_query_operator=substr($filter_explode[0],-5,-3);
                            $filter_query_value=$filterDate;
                        }

                    }

                    elseif (strpos($filterBy,'or')!== false) {
                         $filter_explode = explode(' or ', $filterBy);

                         $findSymbol = strpos($filterBy, "month");
                        //print_r($filterBy);
                        if($findSymbol>0){


                            $day = substr($filter_explode[0],-2);

                            $month = substr($filter_explode[1],-2);
                            $year = substr($filter_explode[2],-4);
                            $filterDate = $year.'-'.$month.'-'.$day;

                            $filter_query_field="`remittance_history`.`acknowledged_at`";
                            $filter_query_operator=substr($filter_explode[0],-5,-3);
                            $filter_query_value=$filterDate;
                        }


                    }

                    elseif(strpos($filterBy,'lt')!== false){

                        $date=substr($filterBy,-11,-1);
                        $filter_query_field="`remittance_history`.`acknowledged_at`";
                        $filter_query_operator='lt';
                        $filter_query_value=$date;

                    }

                    elseif(strpos($filterBy,'gt')!== false){

                        $date=substr($filterBy,-20,-10);
                        $filter_query_field="`remittance_history`.`acknowledged_at`";
                        $filter_query_operator='gt';
                        $filter_query_value=$date;

                    }
                    switch ($filter_query_operator) {
                            case 'eq' :
                                $filter_operator = ' = ';
                                break;

                            case 'ne':
                                $filter_operator = ' != ';
                                break;

                            case 'gt' :
                                $filter_operator = ' > ';
                                break;

                            case 'lt' :
                                $filter_operator = ' < ';
                                break;

                            case 'ge' :
                                $filter_operator = ' >= ';
                                break;

                            case 'le' :
                                $filter_operator = ' <= ';
                                break;
                        }
                    $filter_by[] = $filter_query_field . $filter_operator . $filter_query_value;          

                }  

                


                else{


                    $filter_explode = explode(' and ', $filterBy);

                    foreach ($filter_explode as $filter_each) {
                        $filter_each_explode = explode(' ', $filter_each);

                        $length = count($filter_each_explode);
                       
                        $filter_query_field = '';
                        if ($length > 3) {

                            if(strpos($filter_each_explode[0],'indexof')!== false){

                                for ($j = 0; $j < $length - 2; $j++)
                                $filter_query_field .= $filter_each_explode[$j]." ";
                                $filter_query_field = trim($filter_query_field);
                                $filter_query_operator = $filter_each_explode[$length - 2];
                                $filter_query_value = $filter_each_explode[$length - 1];



                            }


                            else{

                                $filter_query_field = $filter_each_explode[0];
                                $filter_query_operator = $filter_each_explode[1];
                                $filter_query_val='';
                                for($i = 2; $i < $length; $i++){

                                    if($i==($length-1)){
                                        $filter_query_val .= $filter_each_explode[$i];

                                    }
                                    else{

                                        $filter_query_val .= $filter_each_explode[$i]." ";  
                                    }

                                }
                              
                                $filter_query_value = $filter_query_val;
                            }
                           

                        } else {
                            $filter_query_field = $filter_each_explode[0];
                            $filter_query_operator = $filter_each_explode[1];
                            $filter_query_value = $filter_each_explode[2];
                        }


                        $filter_query_field_substr = substr($filter_query_field, 0, 7);


                        if ($filter_query_field_substr == 'startsw' || $filter_query_field_substr == 'endswit' || $filter_query_field_substr == 'indexof' || $filter_query_field_substr == 'tolower') {
                            //Here we are checking the filter is of which type startwith, endswith, contains, doesn't contain, equals, doesn't equal

                            if ($filter_query_field_substr == 'startsw') {
                                $filter_query_field_value_array = explode("'", $filter_query_field);
                                //extracting the input filter value between single quotes, example: 'value'

                                $filter_value = $filter_query_field_value_array[1] . '%';

                                foreach ($this->grid_field_db_match as $key => $value) {
                                    if (strpos($filter_query_field, '(' . $key . ')') != 0) {
                                        //getting the filter field name
                                        $starts_with_value = $this->grid_field_db_match[$key] . ' like ' . $filter_value;
                                        $filter_by[] = $starts_with_value;
                                    } else {
                                        $starts_with_value = "";
                                    }
                                }
                            }

                            if ($filter_query_field_substr == 'endswit') {
                                $filter_query_field_value_array = explode("'", $filter_query_field);
                                //extracting the input filter value between single quotes, example: 'value'

                                $filter_value = '%' . $filter_query_field_value_array[1];

                                foreach ($this->grid_field_db_match as $key => $value) {
                                    if (strpos($filter_query_field, '(' . $key . ')') != 0) {
                                        //getting the filter field name
                                        $ends_with_value = $this->grid_field_db_match[$key] . ' like ' . $filter_value;
                                        $filter_by[] = $ends_with_value;
                                    } else {
                                        $ends_with_value = "";
                                    }
                                }
                            }

                            if ($filter_query_field_substr == 'tolower') {
                                $filter_query_value_array = explode("'", $filter_query_value);
                                //extracting the input filter value between single quotes, example: 'value'

                                $filter_value = $filter_query_value_array[1];
                                if ($filter_query_operator == 'eq') {
                                    $like = ' = ';
                                } else {
                                    $like = ' != ';
                                }
                                foreach ($this->grid_field_db_match as $key => $value) {
                                    if (strpos($filter_query_field, '(' . $key . ')') != 0) {
                                        //getting the filter field name
                                        $to_lower_value = $this->grid_field_db_match[$key] . $like . $filter_value;
                                        $filter_by[] = $to_lower_value;
                                    } else {
                                        $to_lower_value = "";
                                    }
                                }
                            }

                            if ($filter_query_field_substr == 'indexof') {
                                $filter_query_value_array = explode("'", $filter_query_field);
                                //extracting the input filter value between single quotes ex 'value'

                                $filter_value = '%'. $filter_query_value_array[1] .'%';

                                if ($filter_query_operator == 'ge') {
                                    $like = ' like ';
                                } else {
                                    $like = ' not like ';
                                }
                                foreach ($this->grid_field_db_match as $key => $value) {
                                    if (strpos($filter_query_field, '(' . $key . ')') != 0) {
                                        //getting the filter field name
                                        $indexof_value = $this->grid_field_db_match[$key] . $like . $filter_value;
                                        $filter_by[] = $indexof_value;
                                    } else {
                                        $indexof_value = "";
                                    }
                                }
                            }
                        } else {

                            switch ($filter_query_operator) {
                                case 'eq' :
                                    $filter_operator = ' = ';
                                    break;

                                case 'ne':
                                    $filter_operator = ' != ';
                                    break;

                                case 'gt' :
                                    $filter_operator = ' > ';
                                    break;

                                case 'lt' :
                                    $filter_operator = ' < ';
                                    break;

                                case 'ge' :
                                    $filter_operator = ' >= ';
                                    break;

                                case 'le' :
                                    $filter_operator = ' <= ';
                                    break;
                            }
                            if (isset($this->grid_field_db_match[$filter_query_field])) ;{
                                //getting appropriate table field based on grid field


                                $filter_field = $this->grid_field_db_match[$filter_query_field];
                            }

                            $filter_by[] = $filter_field . $filter_operator . $filter_query_value;
                        }
                    }
                }
            }

           // print_r($filter_by);exit;

        

            $Payments = $this->_ledgerModel->getCollectionsByExec($Del_Exec, $Del_FDate, $Del_TDate, $Status, $filter_by,$order_by,$order_by_type,$page,$pageSize);
           // print_r($Payments);exit;

            $totalOrders =$Payments['noOfRecords'] ;

            unset($Payments['noOfRecords']);

            echo json_encode(array('data'=>$Payments, 'TotalRecordsCount'=>$totalOrders));


        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }

    }

    public function approvePayments() {

        try
        {

            $postData = Input::all();

            $this->_ledgerModel->approvePayments($postData);

            return Response::json(array('status' => 200, 'message' => 'Payments updated successfully.'));


        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }


    }

	public function getRemittanceDetails() {

        try
        {

            $postData = Input::all();
            $postData = explode(':',$postData['path']);

            $remittenceId = $postData[1];


            $details = $this->_ledgerModel->getRemittanceDetails($remittenceId);

            $totalOrders = count($details);

            echo json_encode(array('data'=>$details, 'TotalRecordsCount'=>$totalOrders));

//            return Response::json(array('status' => 200, 'message' => 'Payments updated successfully.'));


        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }	

    public function getDataApprovalForm() {


        try
        {
            
            $request = Input::all();

            $Id = explode(',',$request['remittance_id']);
            $status = $request['status'];



            if(!empty($Id) && $status!='') {

            $approval_flow_func= new CommonApprovalFlowFunctionModel();
            $res_approval_flow_func = $approval_flow_func->getApprovalFlowDetails('Payment', $status, \Session::get('userId'));

           $approvalOptions = array();
            $approvalVal = array();
            if(isset($res_approval_flow_func["currentStatusId"]) && isset($res_approval_flow_func["data"])){
                foreach($res_approval_flow_func["data"] as $options){
                    $approvalOptions[$options['nextStatusId'].','.$options['isFinalStep']] = $options['condition'];
                }
            }

             $approvalVal = array('current_status'=>$status,
                'approval_unique_id'=>implode(',',$Id),
                'approval_module'=>'Payment',
                'table_name'=>'collection_remittance_history',
                'unique_column'=>'remittance_id',
                );

            
            if($status==57055 || $status==57052) {
            
                $remDetail = $this->_ledgerModel->getCollRemHistDetail($Id);
            }

            if($status==57051) {
            
                $remDetail = $this->_ledgerModel->getConsolidatedRemittanceDetail($Id);
            }
                

                $approvalVal['total_amount'] = $remDetail->collected_amt;

                $approvalVal['rem_details'] = $remDetail;


            return View('Ledger::approvalForm')->with('approvalOptions', $approvalOptions)->with('approvalVal', $approvalVal)
;            

            }

        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }


    public function approvalSubmit(Request $request) {
        try {
            $data = input::get();

            $approval_unique_id = $data['approval_unique_id'];
            $approval_status = $data['approval_status'];
            $approval_module = $data['approval_module'];
            $current_status = $data['current_status'];
            $approval_comment = $data['approval_comment'];
            $table = $data['table_name'];
            $unique_column = $data['unique_column'];
            $approval_status_array = explode(',',$approval_status);
            $remDetail = $this->_ledgerModel->getCollRemHistDetail($approval_unique_id);
            $fuel_image = '';
            $voucher_image = '';

            $remIds = explode(',',$approval_unique_id);


            if($current_status==57055) {

                $submitted_to_bank = isset($data['submitted_amount']) ? $data['submitted_amount'] : 0;
                $fuel = isset($data['fuel']) ? $data['fuel'] : 0;
                $extra_vehicle = isset($data['extra_vehicle']) ? $data['extra_vehicle'] : 0;
                $short = isset($data['short']) ? $data['short'] : 0;
                $due_amount = isset($data['due_amount']) ? $data['due_amount'] : 0;
                $due_deposited = isset($data['due_deposited']) ? $data['due_deposited'] : 0;

                $fuel_image = isset($data['fuel_image']) ? $data['fuel_image'] : '';
                $voucher_image = isset($data['voucher_image']) ? $data['voucher_image'] : '';
                $denominations = isset($data['denominations']) ? $data['denominations'] : array();


                if($fuel>0) {



                    $destinationPath = public_path() . '/uploads/expenses/fuel/picture/';
                    $imageObj = $request->file('fuel_image');
                    $fileName = Input::file('fuel_image')->getClientOriginalName();
                    $fuel_image = $this->repo->uploadToS3($imageObj, 'fuel', 1);


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
                }


                if($extra_vehicle>0) {


                    $destinationPath = public_path() . '/uploads/expenses/vehicle/picture/';
                    $imageObj = $request->file('voucher_image');
                    $fileName = Input::file('voucher_image')->getClientOriginalName();
                    $voucher_image = $this->repo->uploadToS3($imageObj, 'vehicle', 1);

                    $request_array = array('ExpID'=>0,
                    'ExpDetActualAmount'=>$extra_vehicle,
                    'ExpDetType'=>'123014',
                    'ExpDetDate'=>date('Y-m-d'),
                    'Description'=>'Vehicle Expense',
                    'ExpDetProofKey'=>$vehicle_image,
                    'UserID'=>$user_id,
                    'ExpDetRecordType'=>0
                    );                    

                    $url = env('EXP_LINE_API');

                    $this->addExpense($url,$request_array);

                }


                if($short>0) {

                    $url = env('ADV_EXP_API');

                    $request_array = array('RequestFoID'=>'122001',
                        'RequestForTypeID'=>'122004',
                        'Subject'=>'Payment Due',
                        'Amount'=>$short,
                        'SubmitDate'=>date('Y-m-d'),
                        'ReffIDs'=>$remDetail->remittance_code,
                        'SubmitedByID'=>\Session::get('userId')
                        );                    

                    $headers = array("cache-control: no-cache", "content-type: application/json", 'auth:E446F5E53AD8835EAA4FA63511E22');

                    $response = Utility::sendcUrlRequest($url, $request_array, $headers);


                    if(empty($response) || $response['code']!= 200) {

                        Log::error('Unable to same advances => '.json_encode($request_array));

                    }
                }


                $data = array('amount_deposited'=>$submitted_to_bank,'fuel'=>$fuel,'vehicle'=>$extra_vehicle,'short'=>$short,'arrears_deposited'=>$due_deposited,'denominations'=>json_encode($denominations),'fuel_image'=>$fuel_image,'vehicle_image'=>$voucher_image,'denominations'=>json_encode($denominations));

                $this->_ledgerModel->updateRemittanceDetail($approval_unique_id,$data);

            }    

                    /*$data = array('amount_deposited'=>$submitted_to_bank,'coins_onhand'=>$coins_on_hand,'due_amount'=>$due_amount,'notes_onhand'=>$notes_on_hand,'used_expenses'=>$used_expenses,'fuel'=>$fuel,'vehicle'=>$extra_vehicle,'denominations'=>json_encode($denominations),'fuel_image'=>$fuel_image,'vehicle_image'=>$voucher_image);

                    $this->_ledgerModel->updateRemittanceDetail($approval_unique_id,$data);*/


            if($current_status==57051) {

                $submitted_amount = isset($data['submitted_amount']) ? $data['submitted_amount'] : 0;
                $submittable_amount = isset($data['submittable_amount']) ? $data['submittable_amount'] : 0;
                $due_amount = isset($data['due_amount']) ? $data['due_amount'] : 0;
                $coins_on_hand = isset($data['coins_on_hand']) ? $data['coins_on_hand'] : 0;
                $notes_on_hand = isset($data['notes_on_hand']) ? $data['notes_on_hand'] : 0;
                $used_expenses = isset($data['used_expenses']) ? $data['used_expenses'] : 0;
                $denominations = isset($data['denominations']) ? $data['denominations'] : array();
                
                $fuel = isset($data['fuel']) ? $data['fuel'] : 0;
                $extra_vehicle = isset($data['extra_vehicle']) ? $data['extra_vehicle'] : 0;

                $fuel_image = isset($data['fuel_image']) ? $data['fuel_image'] : '';
                $voucher_image = isset($data['voucher_image']) ? $data['voucher_image'] : '';


                $consolidatedRem = $this->_ledgerModel->getConsolidatedRemittanceDetail($remIds);
                
                $whdetails =$this->_roleRepo->getLEWHDetailsById($consolidatedRem->le_wh_id);
                $statecode  = isset($whdetails->state_code)?$whdetails->state_code:"TS";

                $new_remittance_code = Utility::getReferenceCode('RM',$statecode);

                $data = array(
                               'remittance_code'=>$new_remittance_code, 
                               'collected_amt'=>$consolidatedRem->collected_amt,
                               'amount_deposited'=>$submitted_amount,
                               'le_wh_id'=>$consolidatedRem->le_wh_id,
                               'hub_id'=>$consolidatedRem->hub_id,
                               'by_cash'=>$consolidatedRem->by_cash,
                               'by_cheque'=>$consolidatedRem->by_cheque,
                               'by_online'=>$consolidatedRem->by_online,
                               'by_upi'=>$consolidatedRem->by_upi,
                               'by_ecash'=>$consolidatedRem->by_ecash,
                               'by_pos'=>$consolidatedRem->by_pos,
                               'fuel'=>$consolidatedRem->fuel,
                               'vehicle'=>$consolidatedRem->vehicle,
                               'due_amount'=>$consolidatedRem->due_amount,
                               'coins_onhand'=>$coins_on_hand,
                               'notes_onhand'=>$notes_on_hand,
                               'used_expenses'=>$used_expenses,
                               'submitted_by'=>Session::get('userId')
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
                      'SubmitedByID'=>Session::get('userId')
                      );                    

                      $url = env('ADV_EXP_API');

                      $this->addExpense($url, $request_array);


                  }  


                $submitted_to_bank = isset($data['submitted_amount']) ? $data['submitted_amount'] : 0;

                $approval_comment.= ' (Total Amount:'.$remDetail->collected_amt.', Submitted To Bank:'.$submitted_to_bank.', Due Amount:'.$due_amount.', Coins:'.$coins_on_hand.', Notes:'.$notes_on_hand.', Expenses:'.$used_expenses.', Fuel:'.$fuel.' Other Vehicle:'.$extra_vehicle.')';                
                $remIds[] = $remId;

                }


                foreach ($remIds as $Id) {
                    

                    $approval_flow_func= new CommonApprovalFlowFunctionModel();
                    $this->_ledgerModel->updateStatusAWF($table,$unique_column,$Id, $approval_status,\Session::get('userId'));
                    $approval_flow_func->storeWorkFlowHistory($approval_module, $Id, $current_status, $approval_status, $approval_comment, \Session::get('userId'));

                }
            

            if(isset($approval_status_array[1]) && $approval_status_array[1]=='1') {
                
                $childRems = $this->_ledgerModel->getChildRemittanceDetail($approval_unique_id);

                if(empty($childRems)) {
                    $temp = (object)array('remittance_id'=>$approval_unique_id);
                    $childRems = array($temp);
                }

                foreach ($childRems as $childRem) {



                    $this->_ledgerModel->saveCollectionVoucher($childRem->remittance_id);
                    
                    $this->_ledgerModel->changeOrderPaymentStatusByRemId($childRem->remittance_id);
                    $this->_ledgerModel->completeReturnApprOrdersByRemId($childRem->remittance_id);



                    $orderIds = $this->_ledgerModel->getRemittanceReturnApprOrderIds($childRem->remittance_id);

                    foreach ($orderIds as $orderId) {

                            $this->_orderController->saveComment($orderId->gds_order_id, 'Order Status', array('comment'=>'Order completed and remittance # '.$childRem->remittance_id.' is approved', 'order_status_id'=>'17008'));
                    }    

                }

            }

            $response = array('status'=>200,'message'=>'Success');
            return json_encode($response);
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    public function getApprovalHistory($module, $id) {

            $approvalHistory = $this->_ledgerModel->getApprovalHistory($module,$id);

            return view('Ledger::approvalHistory')
                            ->with('history', $approvalHistory);

    }    
    public function paymentReport() {
        try {
            $filterData = Input::get();
            $fdate = (isset($filterData['fdate']) && !empty($filterData['fdate'])) ? $filterData['fdate'] : date('Y-m').'-01';
            $executive = (isset($filterData['executive']) && !empty($filterData['executive'])) ? $filterData['executive'] : '';
            $fdate = date('Y-m-d', strtotime($fdate));
            $tdate = (isset($filterData['tdate']) && !empty($filterData['tdate'])) ? $filterData['tdate'] : date('Y-m-d');
            $tdate = date('Y-m-d', strtotime($tdate));
            $exequery='';
            if($executive!=''){
                $exequery = 'AND remittance_history.submitted_by='.$executive;
            }
            /*$query = "SELECT `remittance_history`.`remittance_code` AS 'Remittance code',
                `remittance_history`.`submitted_at` AS 'Submitted date', 
                GetUserName(`remittance_history`.`submitted_by`,2) AS 'Submitted by',
                getLeWhName(`remittance_history`.`le_wh_id`) AS 'DC Name',
                `remittance_history`.`collected_amt` as 'Collected Amount',
                `remittance_history`.`by_cash` AS 'By Cash', 
                `remittance_history`.`by_cheque` AS 'By Cheque',
                `remittance_history`.`by_online` AS 'By Online',
                `orders`.`shop_name` AS 'Retailer Name',
                getOrderArea(`ordersaddr`.`area`) AS 'Area',
                getOrderBeat(`orders`.`beat`) AS 'Beat',
                GetUserName(`orderstrack`.`delivered_by`,2) AS 'Delivery person',
                `orders`.`order_code` as 'Order Code',
                `orders`.`total` as 'Order Value',
                `inv`.`invoice_code` AS 'Invoice Code', 
                `inv`.`grand_total` as 'Invoice Value',                
                `returns`.`reference_no` as 'Return Code',
                `returns`.`qty` as 'Return Qty',
                getMastLookupValue(`returns`.`return_reason_id`) as 'Return Reason',
                (`returns`.`total`) as 'Return Value',
                `cancels`.`cancel_code` AS 'Cancel Code', 
                (`order_cancels`.total_price) AS 'Cancel Value',
                `remittance_history`.acknowledged_by AS 'Acknowledged By',
                `remittance_history`.acknowledged_at AS 'Acknowledged Date',
                `remittance_history`.`status` as 'Remitance Status',
                `le`.`remarks` AS 'Remarks'
                FROM `collection_remittance_history` AS `remittance_history` 
                INNER JOIN `remittance_mapping` AS mapping ON `remittance_history`.`remittance_id` = `mapping`.`remittance_id`
                INNER JOIN `ledger` AS `le` ON `le`.`ledger_id` = `mapping`.`ledger_id` 
                LEFT JOIN `gds_invoice_grid` AS `inv` ON `inv`.`gds_invoice_grid_id` = `le`.`invoice_id` 
                LEFT JOIN `gds_orders` AS `orders` ON `orders`.`gds_order_id` = `inv`.`gds_order_id` 
                LEFT JOIN `gds_orders_addresses` AS `ordersaddr` ON `orders`.`gds_order_id` = `ordersaddr`.`gds_order_id`  AND `address_type` = 'shipping'
                LEFT JOIN `gds_order_track` AS `orderstrack` ON `orders`.`gds_order_id` = `orderstrack`.`gds_order_id` 
                LEFT JOIN `gds_returns` AS `returns` ON `returns`.`gds_order_id` = `orders`.`gds_order_id` 
                LEFT JOIN `gds_cancel_grid` AS `cancels` ON `cancels`.`gds_order_id` = `orders`.`gds_order_id` 
                LEFT JOIN `gds_order_cancel` AS `order_cancels` ON `order_cancels`.`cancel_grid_id` = `cancels`.`cancel_grid_id`
                WHERE DATE(`remittance_history`.submitted_at)>='$fdate' AND DATE(`remittance_history`.submitted_at)<='$tdate' $exequery 
                ORDER BY `remittance_history`.`created_at` DESC";*/
            $legal_entity=Session::get('legal_entity_id');
            $roleModel = new Role();
            $Json = json_decode($roleModel->getFilterData(6,Session::get('userId')),1);
            $Json = json_decode($Json['sbu'],1);

            $Hubs_Assigned = '';
            $dc_Assigned='';
            if(isset($Json['118002'])) {
                $Hubs_Assigned = implode(',',explode(',',$Json['118002']));
            }
            if(isset($Json['118001'])){
                $dc_Assigned = implode(',',explode(',',$Json['118001']));
            }
            $query = "CALL getRemitancereports('$executive','$fdate', '$tdate','$dc_Assigned','$Hubs_Assigned')";
            $file_name = 'Payment_Report_' .date('Y-m-d-H-i-s').'.csv';
            $this->exportToCsv($query, $file_name);die;
            
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
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
        //echo '<pre>'. $exportCommand;die;
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

    public function collectionDetail() {
        try
        {

            parent::Title('Collection Details');

            $deliveryUsers      =   $this->_orderModel->getUsersByRoleName(array('Delivery Executive'));
            $remStatusArr    =   $this->_ledgerModel->getApprovalStatus();

            return View::make('Ledger::collectionDetails',['deliveryUsers'=>$deliveryUsers,'remStatusArr'=>$remStatusArr]);

           
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }

    }


    public function getCollectionsByDelExec($Del_Exec, $Del_FDate, $Del_TDate) {

        try
        {

            $Payments = $this->_ledgerModel->getFullCollectionDetailByExec($Del_Exec, $Del_FDate, $Del_TDate);

            $totalOrders = count($Payments);

            echo json_encode(array('data'=>$Payments, 'TotalRecordsCount'=>$totalOrders));


        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }

    }


    public function collectionReport() {
        try {
            $filterData = Input::get();
            $fdate = (isset($filterData['fdate']) && !empty($filterData['fdate'])) ? $filterData['fdate'] : date('Y-m').'-01';
            $executive = (isset($filterData['executive']) && !empty($filterData['executive'])) ? $filterData['executive'] : '';
            $fdate = date('Y-m-d', strtotime($fdate));
            $tdate = (isset($filterData['tdate']) && !empty($filterData['tdate'])) ? $filterData['tdate'] : date('Y-m-d');
            $tdate = date('Y-m-d', strtotime($tdate));
            $exequery='';
            if($executive!=''){
                $exequery = 'AND collections.created_by='.$executive;
            }

            $roleModel = new Role();
            $Json = json_decode($roleModel->getFilterData(6,Session::get('userId')),1);
            $Json = json_decode($Json['sbu'],1);

            $Hubs_Assigned = '';
            if(isset($Json['118002'])) {
                $Hubs_Assigned = implode(',',explode(',',$Json['118002']));
            }
            $query = "CALL getCollectionreports('$executive','$fdate', '$tdate','$Hubs_Assigned')";
   
            $file_name = 'Collection_Report_' .date('Y-m-d-H-i-s').'.csv';
            
            $this->exportToCsv($query, $file_name);die;
            
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

public function getLOCReport() {
        try {

            $query = "select * from vw_loc_order_report";
   
            $file_name = 'LOC_Report_' .date('Y-m-d-H-i-s').'.csv';
            
            $this->exportToCsv($query, $file_name);die;

        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
        public function insertReceiptVouchers($startDate,$endDate,$tableName) {
            try {            

                $this->_ledgerModel->insertReceiptVouchers($startDate,$endDate,$tableName);

                echo 'Success';
            } catch (Exception $e) {
                echo 'Something went wrong';
                Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            }

        }

	public function insertExtraReceiptVocuhers() {
		try {
		        
		    $this->_ledgerModel->insertReceiptTallyVouchers();


		} catch (Exception $e) {
		    Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}

	public function addExpense($url,$request_array) {

        try {

              $url = env('ADV_EXP_API');

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
  
   public function getBrandDetails(){
        $user_id = Session::get('userId');
        $hasAccess = $this->_roleRepo->checkPermissionByFeatureCode('BRAND1');
        $inventory = $this->_roleRepo->checkPermissionByFeatureCode('INVENT1');
         if($hasAccess == false && $inventory== false) {
                return View::make('Indent::error');
            }
        $Json = json_decode($this->_roleModel->getFilterData(6), 1);
        $filters = json_decode($Json['sbu'], 1);
        $accessLevel = $this->_ledgerModel->getBrandsAndManufacture($user_id);
        $brandData = isset($accessLevel[0]['brands']) ? $accessLevel[0]['brands'] : array();
        $mnData = isset($accessLevel[0]['manufacturer']) ? $accessLevel[0]['manufacturer'] : array();
        // if ($mnData!=0 && in_array(0, $accessLevel[0]['brands'])) {
        //     $mnData=array_keys($mnData);
        //     $brandData = $this->_ledgerModel->getBrandIDS($mnData,1);
        //     //print_r($brandData);die();
        //     }
        $dc_acess_list = isset($filters['118001']) ? $filters['118001'] : 'NULL';
        $allDc = $this->_orderModel->getDcHubDataByAcess($dc_acess_list);
        $filter_options['dc_data'] = $allDc;
        $supplierNames = $this->_ledgerModel->suppliersNamesByBrandsAccess($user_id);
        return view('Ledger::getBrandDetails')->with(['filter_options'=>$filter_options,'mnData'=>$mnData,'brandData'=>$brandData,'hasAccess'=>$hasAccess,'inventory'=>$inventory,'names'=>$supplierNames]);
   }
   public function getBrandDetailsDownload(Request $request){
        $user_id = Session::get('userId');
        $fvar = $request->get('fdate');
        $fdate = str_replace('/', '-', $fvar);
        $fromDate=  date('Y-m-d', strtotime($fdate));
        $var = $request->get('tdate');
        $date = str_replace('/', '-', $var);
        $toDate=  date('Y-m-d', strtotime($date));     
        $dc_id  = $request->input("loc_dc_id");
        $dcNames = implode(',',$dc_id);
        $supplierId = $request->input("supplier_id");
        if ($dcNames==0){
            $Json = json_decode($this->_roleModel->getFilterData(6), 1);
            $filters = json_decode($Json['sbu'], 1);
            $dcNames = 'NULL';
            $dcNames = isset($filters['118001']) ? $filters['118001'] : 'NULL';
            $idS = $this->_ledgerModel->dcFCMappingTable($dcNames);
            $arrayPush = array();
            foreach ($idS as $key => $value) {
                 array_push($arrayPush, $value->fc_le_wh_id);
            }
            if($dcNames!='NULL'){
                $dcNames = implode(',',$arrayPush);    
            }
            
            $dcNames = "'".$dcNames."'";
        }else{
            $idS = $this->_ledgerModel->dcFCMappingTable($dcNames);
            $arrayPush = array();
            $arrayPush=array_merge($dc_id);
            foreach ($idS as $key => $value) {
                 array_push($arrayPush, $value->fc_le_wh_id);
            }
            $dcNames = implode(',',$arrayPush);
            $dcNames =  "'".$dcNames."'";
        }
        $brand_id=$request->input("brand_id");
        $brandNames = implode(',',$brand_id);
        if ($brandNames==0){
            $accessLevel = $this->_ledgerModel->getBrandsAndManufacture($user_id);
        }
        // if ($brandNames==0){
        //     $suppIDS = $this->_ledgerModel->getSuppliersListForAllBrand($brandNames);
            
        // }else{
            $brandNames =  "'".$brandNames."'";

       // }
        $brandNames ='NULL';
        $brands = isset($accessLevel[0]['brands']) ? $accessLevel[0]['brands'] : array();
        $brandArr = array();
        foreach ($brands as $key => $value) {
            array_push($brandArr, $key);
        }
            array_push($brandArr,'0');
        if(count($brandArr)){
            $brandNames = implode(',',$brandArr);
            $brandNames = "'".$brandNames."'";
        }
        else{
            $brandNames = 'NULL';
        }
        $manf_id=$request->input("manufacture_id");
        $manufacturerName =implode(',',$manf_id);
        if ($manufacturerName==0) {
            $manufacturerName='NULL';
            $names = isset($accessLevel[0]['manufacturer']) ? $accessLevel[0]['manufacturer'] : array();
            $manufaArr = array();
            foreach ($names as $key => $value) {
                array_push($manufaArr, $key);
            }
                array_push($manufaArr,'0');
            if (count($manufaArr)) {
                $manufacturerName = implode(',',$manufaArr);
                $manufacturerName = "'".$manufacturerName."'";
            }else{
                $manufacturerName = 'NULL';
            }
        }
        else{
            $manufacturerName =  "'".$manufacturerName."'";
        }
        $suppIDS = $this->_ledgerModel->getSuppliersListForAllBrand($brandNames);
        $suppIDS=json_decode(json_encode($suppIDS),1);
        $supplierId = array();
        foreach ($suppIDS as $key => $value) {
            array_push($supplierId, $value['supplier_id']);
        }
        $suppIDS = implode(',', $supplierId);
        if (!empty($suppIDS)) {
            $suppIDS = "'".$suppIDS."'";
        }else{
            $suppIDS = 'NULL';
        }
        $details = json_decode(json_encode($this->_ledgerModel->getBrandDetailsByDC($dcNames,$brandNames,$manufacturerName,$fromDate,$toDate,$suppIDS)), true);
        Excel::create(' Brand Sales Report - '. date('Y-m-d'),function($excel) use($details) {
            $excel->sheet('Brand Sales Report', function($sheet) use($details) {          
            $sheet->fromArray($details);
            });      
        })->export('xls');
   }
   // public function getInventoryData(Request $request){
   //      $user_id = Session::get('userId');
   //      $dc_id  = $request->input("loc_dc_id");
   //      $dcNames=implode(',',$dc_id);
   //      $Json = json_decode($this->_roleModel->getFilterData(6), 1);
   //      $filters = json_decode($Json['sbu'], 1);
   //      $accessLevel = $this->_ledgerModel->getBrandsAndManufacture($user_id);
   //      if ($dcNames==0){
   //          $dcNames='NULL';
   //          $dcNames = isset($filters['118001']) ? $filters['118001'] : 'NULL';
   //          $dcNames = "'".$dcNames."'";
   //      }
   //      else{
   //          $dcNames =  "'".$dcNames."'";
   //      }
   //      $brand_id=$request->input("inv_brand_name");
   //      $brandNames =implode(',',$brand_id);
   //      if ($brandNames==0){
   //          $brandNames='NULL';
   //          $brands = isset($accessLevel[0]['brands']) ? $accessLevel[0]['brands'] : array();
   //          $brandArr = array();
   //          foreach ($brands as $key => $value) {
   //              array_push($brandArr, $key);
   //          }
   //          if(count($brandArr)){
   //              $brandNames = implode(',',$brandArr);
   //              $brandNames = "'".$brandNames."'";
   //          }
   //          else{
   //              $brandNames = 0;
   //          }
   //      }else{
   //          $brandNames =  "'".$brandNames."'";
   //      }
   //      $manf_id=$request->input("inv_manufacture_name");
   //      $manufacturerName=implode(',', $manf_id);
   //      if ($manufacturerName==0){
   //          $manufacturerName='NULL';
   //          $names = isset($accessLevel[0]['manufacturer']) ? $accessLevel[0]['manufacturer'] : array();
   //          $manufaArr = array();
   //          foreach ($names as $key => $value) {
   //              array_push($manufaArr, $key);
   //          }
   //          if (count($manufaArr)) {
   //              $manufacturerName = implode(',',$manufaArr);
   //              $manufacturerName = "'".$manufacturerName."'";
   //          }else{
   //              $manufacturerName = 0;
   //          }
   //      }
   //      else{

   //          $manufacturerName =  "'".$manufacturerName."'";
   //      }
   //      $invSupplier = $request->input('supplier_name');
   //      $invSupplier = implode(',',$invSupplier);
   //      $details = json_decode(json_encode($this->_ledgerModel->getInventoryDataFromTable($dcNames,$brandNames,$manufacturerName,$invSupplier)), true);
   //      Excel::create('Current Inventory Report - '. date('Y-m-d'),function($excel) use($details) {
   //          $excel->sheet('Current Inventory Report', function($sheet) use($details) {          
   //          $sheet->fromArray($details);
   //          });      
   //      })->export('xls');
   // }
   public function getSupplierMapping(){
    try{
    $suppGridaccess  = $this->_roleRepo->checkPermissionByFeatureCode('SUPPLIERMAN01');
        if (!$suppGridaccess){
        return Redirect::to('/');
         }
    $user_id = Session::get('userId');  
    $breadCrumbs = array('Home' => url('/'),'Supplier' => '#','Supplier Brand Mapping' => '#');
    parent::Breadcrumbs($breadCrumbs);
    $supplierNames = $this->_ledgerModel->suppliersName($user_id);
    $brands = $this->_ledgerModel->brandNames($user_id);
    $manufacturer = json_decode($this->_roleModel->getFilterData(11), 1);
    $suppAccess = $this->_roleRepo->checkPermissionByFeatureCode('SUPPMP01');
    return view('Ledger::getSupplierMapping')->with(['names'=>$supplierNames,'brandNames'=>$brands,'manufacturer'=>$manufacturer,'suppAccess'=>$suppAccess]);
        }
    catch(Exception $e) {
      return 'Message: ' .$e->getMessage();
    }
   }
   public function getSupplierMappingData(Request $request){
    $data = $request->all();
    $supplier_name = isset($data['supplier_name'])?$data['supplier_name']:'';//(!empty($data['supplier_name']));
    $brand_name =isset($data['brand_name'])?implode(',', $data['brand_name']):'';
    $manuf_name =isset($data['manufacturer_name'])?implode(',', $data['manufacturer_name']):'';
    if ($manuf_name!=0 && in_array(0, $data['brand_name'])) {
        $brand_name = $this->_ledgerModel->getBrandIDS($manuf_name);
    }
    $insertQuery = $this->_ledgerModel->suppliersDataInsertions($supplier_name,$brand_name,$manuf_name);
    if($insertQuery) {
        $message ="Supplier Mapped Successfully";
        return json_encode(["status"=>"200","message"=>$message]);
    }else{
        $message="Something Went Wrong";
        return json_encode(["status"=>"205","message"=>$message]);
    }
   }
   public function getSuppliersGridData(Request $request){
            $makeFinalSql = array();            
            $filter = $request->input('%24filter');
            if( $filter=='' ){
                $filter = $request->input('$filter');
            }
            // make sql for firstname
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("business_legal_name", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }
            // make sql for phone_no
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("brand_name", $filter);
            $fieldQuery =str_replace('brand_name', " CASE sp.brand_id WHEN 0 THEN 'All' ELSE  (select group_concat(b.brand_name) from brands b where find_in_set(b.brand_id,sp.brand_id))  END", $fieldQuery);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }
            // make sql for email
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("manuf_name", $filter);
            $fieldQuery =str_replace('manuf_name', " CASE sp.manufacturer_id WHEN 0 THEN 'All' ELSE (select group_concat(l.business_legal_name) from legal_entities l where find_in_set(l.legal_entity_id,sp.manufacturer_id) and l.legal_entity_type_id in (1006)) END", $fieldQuery);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("city", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("supplier_code", $filter);
            $fieldQuery = str_replace('supplier_code','fn_getSupplierCode(le.legal_entity_id)', $fieldQuery);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }
            $orderBy = "";
            $orderBy = $request->input('%24orderby');
            if($orderBy==''){
                $orderBy = $request->input('$orderby');
            }
            // Arrange data for pagination
            $page="";
            $pageSize="";
            if( ($request->input('page') || $request->input('page')==0)  && $request->input('pageSize') ){
                $page = $request->input('page');
                $pageSize = $request->input('pageSize');
            }
            $userdata =  $this->_ledgerModel->getSupplierGridData($makeFinalSql, $orderBy, $page, $pageSize);
          return json_encode(array('results'=>$userdata['records'],'TotalRecordsCount'=>$userdata['count'])); 
   }
   public function getUpdateDetails($id){
    $request = $this->_ledgerModel->getSupplierMappingID('supplier_brand_map_id',$id);
    return json_encode($request);
   }
   public function updateSuppliersMapping(Request $request){
    $data = $request->all();
    $supplier_brand_map_id = isset($data['supplier_brand_map_id'])? $data['supplier_brand_map_id']:'';
    $supplier_name_edit = isset($data['supplier_name_edit'])? $data['supplier_name_edit']:'';
    $brand_name_edit =isset($data['brand_name_edit'])?implode(',', $data['brand_name_edit']):'';
    $manufacturer_name_edit =isset($data['manufacturer_name_edit'])?implode(',', $data['manufacturer_name_edit']):'';
    if ($manufacturer_name_edit!=0 && in_array(0, $data['brand_name_edit'])) {
        $supplier_brand_map_id = $this->_ledgerModel->getBrandIDS($manufacturer_name_edit);
    }
    $request = $this->_ledgerModel->updateQuery($supplier_brand_map_id,$supplier_name_edit,$brand_name_edit,$manufacturer_name_edit);
    if ($request) {
        $message ="Updated Successfully";
        return json_encode(["status"=>"200","message"=>$message]);
    }else{
        $message="Something Went Wrong";
        return json_encode(["status"=>"205","message"=>$message]);
    }
   }
   public function deleteSupplierDetails($id){
    $data = $this->_ledgerModel->deleteSupplierMapping($id);
    if ($data==1) {
        $message ="Successfully Deleted";
        return json_encode(["status"=>"200","message"=>$message]);
    }else{
        $message="Something Went Wrong";
        return json_encode(["status"=>"205","message"=>$message]);
    }
   }

   public function getBrandmanufBySupplierId($sid){
    $request = $this->_ledgerModel->getSupplierMappingID('supplier_id',$sid);
    return json_encode($request);
   }
   public function getBrandsForManufacture(){
    $data=Input::all();
    $id=$data['id'];
    $sid=isset($data['sid'])?$data['sid']:'';
    //$manufacturerId = $this->_ledgerModel->getBrandId($id);

    if(is_array($id)){
        $id=implode(',', $id);
    }
    $resreturn='';
    $permssionbrandids=json_decode(json_encode($this->_ledgerModel->getSupplierMappingID('manufacturer_id',$id,'supplier_id',$sid)),1);
    $brandNames=json_decode(json_encode($this->_ledgerModel->brandNames(Session::get('userId'), $id)),1);
    //echo '<pre/>';print_r($brandNames);exit;
    //echo '<pre/>';print_r();exit;
    $permssionbrandids=array_column($permssionbrandids, 'brand_id');//print_r($permssionbrandids);exit;
    $permssionbrandids=isset($permssionbrandids[0])?$permssionbrandids[0]:'';
    $permssionbrandids=explode(',', $permssionbrandids);
    $resreturn.='<option value="0">ALL Brands</option>';
    for ($b=0;$b<count($brandNames);$b++) {
             // print_r($value);exit;
                $selected = (in_array($brandNames[$b]['brand_id'], $permssionbrandids)) ?"selected":"";
                 $resreturn.='<option value="'.$brandNames[$b]['brand_id'].'" '.$selected.'>'.$brandNames[$b]['brand_name'].'</option>';
           }
    return $resreturn;
   }
   public function getBrandsForSupplier(){
    $data =Input::all();
    $brandID = isset($data['id'])?$data['id']:'';
    $manufacturer_id = isset($data['manufacture_id'])?$data['manufacture_id']:'';
    if (is_array($brandID)) {
        $brandIDImplode = implode(',', $brandID);
    }
    $brandForSupplier = $this->_ledgerModel->getBrandForSuppliers($brandIDImplode,$manufacturer_id);
    $supplier_name='';
    $supplier_name.='<option value="0" selected>ALL Suppliers</option>';
    foreach ($brandForSupplier as $value) {
        $supplier_name.='<option value="'.$value->supplier_id.'" >'.$value->business_legal_name.'</option>';
    }
    return $supplier_name;
    // return json_encode((json_decode($supplier_name)));die();
   }

    public function brandsForManufactureController(){
    $data=Input::all();
    $id=$data['id'];
    $sid=isset($data['sid'])?$data['sid']:'';
    //$manufacturerId = $this->_ledgerModel->getBrandId($id);

    if(is_array($id)){
        $id=implode(',', $id);
    }
    $resreturn='';
    //$permssionbrandids=json_decode(json_encode($this->_ledgerModel->getSupplierMappingID('manufacturer_id',$id,'supplier_id',$sid)),1);
    $brandNames=json_decode(json_encode($this->_ledgerModel->brandNames(Session::get('userId'), $id)),1);
    //echo '<pre/>';print_r($brandNames);exit;
    //echo '<pre/>';print_r();exit;
    // $permssionbrandids=array_column($permssionbrandids, 'brand_id');//print_r($permssionbrandids);exit;
    // $permssionbrandids=isset($permssionbrandids[0])?$permssionbrandids[0]:'';
    // $permssionbrandids=explode(',', $permssionbrandids);
    $resreturn.='<option value="0">ALL Brands</option>';
    for ($b=0;$b<count($brandNames);$b++) {
             // print_r($value);exit;
                 $resreturn.='<option value="'.$brandNames[$b]['brand_id'].'">'.$brandNames[$b]['brand_name'].'</option>';
           }
    return $resreturn;
   }

    // public function getInventoryData(Request $request){
    //     $data = $request->all();
    //     $user_id = Session::get('userId');
    //     $dc_id  = $data["loc_dc_id"];
    //     $dcNames=implode(',',$dc_id);
    //     $Json = json_decode($this->_roleModel->getFilterData(6), 1);
    //     $filters = json_decode($Json['sbu'], 1);
    //     if ($dcNames==0){
    //         $dcNames='NULL';
    //         $dcNames = isset($filters['118001']) ? $filters['118001'] : 'NULL';
    //         $dcNames = "'".$dcNames."'";
    //     }
    //     else{
    //         $dcNames =  "'".$dcNames."'";
    //     }
    //     $brand_id = $data['inv_brand_name'];
    //     $brandNames =implode(',',$brand_id);
    //     if ($brandNames==0){
    //         $brandNames='NULL';
    //     }
    //     else{
    //         $brandNames =  "'".$brandNames."'";
    //     }
    //     $manufacturer_id = $data['inv_manufacture_name'];
    //     $manufacturerName =implode(',',$manufacturer_id);
    //     if ($manufacturerName==0){
    //         $manufacturerName='NULL';
    //     }
    //     else{
    //         $manufacturerName =  "'".$manufacturerName."'";
    //     }
    //     $supplier_id = $data['supplier_name'];
    //     $invSupplier = implode(',',$supplier_id);
    //     if ($invSupplier==0){
    //         $invSupplier='NULL';
    //     }
    //     else{
    //         $invSupplier =  "'".$invSupplier."'";
    //     }
    //     $details = json_decode(json_encode($this->_ledgerModel->getInventoryDataFromTable($dcNames,$brandNames,$manufacturerName,$invSupplier,$user_id)), true);
    //     Excel::create('Current Inventory Report - '. date('Y-m-d'),function($excel) use($details) {
    //         $excel->sheet('Current Inventory Report', function($sheet) use($details) {          
    //         $sheet->fromArray($details);
    //         });      
    //     })->export('xls');
    // }

      public function getInventoryData(Request $request){
        $user_id = Session::get('userId');
        $dc_id  = $request->input("loc_dc_id");
        $dcNames=implode(',',$dc_id);
        $Json = json_decode($this->_roleModel->getFilterData(6), 1);
        $filters = json_decode($Json['sbu'], 1);
        $accessLevel = $this->_ledgerModel->getBrandsAndManufacture($user_id);
        if ($dcNames==0){
            $dcNames='NULL';
            $dcNames = isset($filters['118001']) ? $filters['118001'] : 'NULL';
            $dcNames = "'".$dcNames."'";
        }
        else{
            $dcNames =  "'".$dcNames."'";
        }
        $brand_id=$request->input("inv_brand_name");
        $brandNames =implode(',',$brand_id);
        if ($brandNames==0){
            $brandNames='NULL';
            $brands = isset($accessLevel[0]['brands']) ? $accessLevel[0]['brands'] : array();
            $brandArr = array();
            foreach ($brands as $key => $value) {
                array_push($brandArr, $key);
            }
                array_push($brandArr,'0');
            if(count($brandArr)){
                $brandNames = implode(',',$brandArr);
                $brandNames = "'".$brandNames."'";
            }
            else{
                $brandNames = 0;
            }
        }else{
            $brandNames =  "'".$brandNames."'";
        }
        $manf_id=$request->input("inv_manufacture_name");
        $manufacturerName=implode(',', $manf_id);
        if ($manufacturerName==0){
            $manufacturerName='NULL';
            $names = isset($accessLevel[0]['manufacturer']) ? $accessLevel[0]['manufacturer'] : array();
            $manufaArr = array();
            foreach ($names as $key => $value) {
                array_push($manufaArr, $key);
            }
                array_push($manufaArr,'0');
            if (count($manufaArr)) {
                $manufacturerName = implode(',',$manufaArr);
                $manufacturerName = "'".$manufacturerName."'";
            }else{
                $manufacturerName = 0;
            }
        }
        else{

            $manufacturerName =  "'".$manufacturerName."'";
        }
        $supplierId = $request->input("supplier_name");
        $suppIDS = $this->_ledgerModel->getSuppliersListForAllBrand($brandNames);
        $suppIDS=json_decode(json_encode($suppIDS),1);
        $supplierId = array();
        foreach ($suppIDS as $key => $value) {
            array_push($supplierId, $value['supplier_id']);
        }
        $suppIDS = implode(',', $supplierId);
        if (!empty($suppIDS)) {
            $suppIDS = "'".$suppIDS."'";
        }else{
            $suppIDS = 'NULL';
        }
        $details = json_decode(json_encode($this->_ledgerModel->getInventoryDataFromTable($dcNames,$brandNames,$manufacturerName,$suppIDS)), true);
        Excel::create('Current Inventory Report - '. date('Y-m-d'),function($excel) use($details) {
            $excel->sheet('Current Inventory Report', function($sheet) use($details) {          
            $sheet->fromArray($details);
            });      
        })->export('xls');
    }
}
