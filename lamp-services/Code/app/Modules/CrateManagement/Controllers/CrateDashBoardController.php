<?php

namespace App\Modules\CrateManagement\Controllers;

use App\Http\Controllers\BaseController;
use View;
use Illuminate\Http\Request;
use Log;
use App\Modules\CrateManagement\Models\CrateManagement;
use App\Central\Repositories\RoleRepo;
use Carbon\Carbon;
use Excel;
use Input;
use UserActivity;
use DB;
use Session;
use Illuminate\Support\Facades\Config;
use File;
use App\Modules\Categories\Controllers\CategoryController;

class CrateDashBoardController extends BaseController {

    public function __construct() {
        try {
            parent::Title('Crate Dashboard');
            $this->grid_field_db_match = array(
                'crate_code' => 'crate_code',
                'status' => 'status',
                'transaction_status' => 'transaction_status',
                'warehouse_name' => 'le_wh_id',
                'hub_name' => 'hub_id',
                'last_order_id' => 'last_order_id',
                'last_order_code' => 'last_order_code',
                'last_order_status' => 'last_order_status',
                'picker_name' => 'picker_name',
                'de_name' => 'de_name'
            );
            $this->_crateManagement = new CrateManagement();
            $this->_category = new CategoryController();
            $this->_roleRepo = new RoleRepo();
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function indexAction() {
        $breadCrumbs = array('Home' => url('/'), 'Inventory' => url('/inventory/index'), 'Crate Dashboard' => url('#'));
        parent::Breadcrumbs($breadCrumbs);
        $access = $this->_roleRepo->checkPermissionByFeatureCode('CRATE001');
        $wareHouseList = $this->_crateManagement->wareHouseList();
        $impertAccess = $this->_roleRepo->checkPermissionByFeatureCode('CRATIMP001');
        $transferAccess = $this->_roleRepo->checkPermissionByFeatureCode('CRTR01');
        return View::make('CrateManagement::crateDashBoard')->with(['warehouseName'=>$wareHouseList,'access'=>$access,'impertAccess'=>$impertAccess,'transferAccess'=>$transferAccess]);
    }

    public function getByTransactionStatus(Request $request) {
        $orderby_array = "";
        if ($request->input('$orderby')) {
            $order = explode(' ', $request->input('$orderby'));
            $order_query_field = $order[0];
            $order_query_type = $order[1]; //type
            $order_by_type = 'desc';

            if ($order_query_type == 'asc') {
                $order_by_type = 'asc';
            }
            if (isset($this->grid_field_db_match[$order_query_field])) {
                $order_by = $this->grid_field_db_match[$order_query_field];
                $orderby_array = $order_by . " " . $order_by_type;
            }
        }
        $ediAccess = 1;//$this->_roleRepo->checkPermissionByFeatureCode('EDCRATE02');
        $type = $request->input('type');
        $filtered_data = $this->_crateManagement->getCratesList($type, $orderby_array);
        foreach ($filtered_data['result'] as $key => $value) {
            $editbutton='';
            if(isset($ediAccess) && $ediAccess==1){
                 $editbutton="<a  data-crateid = '" . $value['crate_id'] . "' data-toggle='modal' data-target='#crate_edit_Details'><span  style='padding-left:15px;'><i class='fa fa-pencil'></i></span></a> ";
                }  
             $filtered_data['result'][$key]['actions'] = "<a  data-cratecode = '" . $value['crate_code'] . "' data-toggle='modal' data-target='#crate_details'><span  style='padding-left:15px;'><i class='fa fa-eye'></i></span></a>".$editbutton;
        }
        echo json_encode(array('results' => $filtered_data["result"]));
    }

    public function getCrateDetails(Request $request) {
        $crate_code = $request->input('crate_code');
        $response = json_decode($this->_crateManagement->getCurlResponse($crate_code), true);
        return $response;
    }

    public function statusCount() {
        $response = json_decode(json_encode($this->_crateManagement->getStatusCount()), true);
        return $response[0];
    }
    public function createCrate(Request $request){
        $data = $request->all();
        $le_wh_id = $data['warehouse_id'];
        $crate = $data['crate_code_val'];
        if (!preg_match("/^(CRT)+[A-Z0-9]{6}+-[0-9]{5}$/",$crate)) {
            return json_encode(['status'=>'404','message'=>'Improper Crate Code']);
        }
        $carte_no = explode("-", $crate);
        if (count($carte_no)){
            $s_no = isset($carte_no[1])?(int)$carte_no[1]:'';
            if(empty($s_no)){
                return json_encode(['status'=>'404','message'=>'Please Enter Valid Crate Code']);    
            }
            $containerMaster = $this->_crateManagement->containerMasterCodeGen($le_wh_id,$crate,$s_no);
            if($containerMaster==0){
                return json_encode(['status'=>'404','message'=>'Crate Code Already Exists']);    
            }  
            $success_msg='SuccessFully Created';
            return json_encode(['status'=>'200','message'=>$success_msg]);  
        }else{
            $success_msg='Something Went Wrong Please Try Again';
            return json_encode(['status'=>'400','message'=>$success_msg]);
        }       
    }
    public function crateEditDetails(Request $request){
        $data = $request->all();
        $id = $data['crate_id'];
        $crate_id = $this->_crateManagement->crateEditDetails($id);
        return $crate_id;
    }
    public function updateCreateCrate(Request $request){
        $data =$request->all();
        $id = $data['crate_id'];
        $le_wh_id = $data['up_le_wh_id'];
        $updateCrate_id = $this->_crateManagement->updateCrateEditDetails($id,$le_wh_id);
        if ($updateCrate_id) {
            $success_msg='SuccessFully Updated';
            return json_encode(['status'=>'200','message'=>$success_msg]);
        }else{
            $success_msg='Something Went Wrong Please Try Again';
            return json_encode(['status'=>'400','message'=>$success_msg]);
        }
    }
    public function downloadExcel(){
        $mytime = date('Y-m-d');
        $headers['headers'] = array('Crate Code','WareHouse Code');
        $dummyData = array('Crate_Code_Template'=>'Crate Code Template Sheet-'.$mytime);
        $headings = array('Warehouse ID', 'Warehouse Name',  'Warehouse Code');
        $data['warehouse'][]=$headings;
        $warehouselist=DB::table('legalentity_warehouses')->select('le_wh_id','display_name','le_wh_code')->where('dc_type','118001')->get()->all();
        $warehouselist=json_decode(json_encode($warehouselist),true);
        foreach ($warehouselist as $warehouselist) {
            $warehouselist=array(
                'Warehouse ID'=>$warehouselist['le_wh_id'],
                'Warehouse Name'=>$warehouselist['display_name'],
                'Warehouse Code'=>$warehouselist['le_wh_code'],
            );
            $data['warehouse'][]=$warehouselist;
        }
        Excel::create('Crate Code Template-'.$mytime, function($excel) use($headers,$data) {
            $excel->sheet('Crate Code', function($sheet) use($headers) {
                $sheet->fromArray($headers['headers']);
            });
            $excel->sheet('Sheet2', function($sheet) use($data) {
                        $sheet->fromArray($data['warehouse'], null, 'A1', false, false);
                    });
        })->export('xls');
    }
   public function uploadCrateCodeExcel(){
        // try{
                    DB::beginTransaction();
                    ini_set('max_execution_time', 1200);
                    $message = '';
                    $msg = '';
                    $mail_msg = '';
                    $report_table = '';
                    $status = 'failed';
                    $required_data = array('crate_code' => 'required', 'warehouse_code' => 'required'); 
                    if (Input::hasFile('upload_crate_code_template')) {
                        $path = Input::file('upload_crate_code_template')->getRealPath();
                        $data = $this->readExcelCpEnable($path);
                        $data = json_decode(json_encode($data), 1);
                        if (isset($data['crate_data'])) {
                            $prod_data = $data['crate_data'];
                                $pr_scount = 0;
                                $pr_fcount = 0;
                                foreach ($prod_data as $product) {
                                    $required_check_msg = array();
                                    
                                    foreach($required_data as $required_data_key=>$required) {
                                        if($required=='required')
                                        {
                                            if(!isset($product[$required_data_key]) && $product[$required_data_key] == '')
                                            {

                                                $required_check_msg[]=$required_data_key;
                                            }                                  
                                        }
                                        
            
                                    }
                                    if (count($required_check_msg) == 0) {

                                        $timestamp = md5(microtime(true));
                                        $txtFileName = 'create-import-' . $timestamp . '.html';
                                        $file_path = 'download' . DIRECTORY_SEPARATOR . 'cratestatus_logs' . DIRECTORY_SEPARATOR . $txtFileName;
                                        $files_to_delete = File::files('download' . DIRECTORY_SEPARATOR . 'cratestatus_logs/');
                                        File::delete($files_to_delete);

                                            $crate_code = $product['crate_code'];
                                            $dcid = $product['warehouse_code'];
                                            if (!preg_match("/^(CRT)+[A-Z0-9]{6}+-[0-9]{5}$/",$crate_code)) {

                                                     $message .= $crate_code.' Improper Crate Code  ';
                                                     $pr_fcount++;
                                                }else{
                                                       $crate_code_qry=DB::table('container_master')->select('crate_code')->where('crate_code',$crate_code)->first();
                                                       $wareHouseCode ="select le_wh_id from legalentity_warehouses where le_wh_code='".trim($dcid)."' limit 1";
                                                       $wareHouseCode = DB::select(DB::raw($wareHouseCode));
                                                       if(count($wareHouseCode)==0){
                                                             $message .="Invalid Warehouse Code ".$dcid." for crate ".$product['crate_code'];
                                                             $pr_fcount++;
                                                       }elseif (count($crate_code_qry) == 0) {
                                                                $carte_no = explode("-", $crate_code);
                                                                $s_no = isset($carte_no[1])?(int)$carte_no[1]:'';
                                                            if(empty($s_no)){
                                                                $message .="Invalid Crate Code";    
                                                            }else{
                                                                $containerMaster = $this->_crateManagement->containerMasterCodeGen($wareHouseCode[0]->le_wh_id,$crate_code,$s_no);
                                                                if($containerMaster==0)
                                                                {
                                                                    $message .="Crate Code Already Exists";        
                                                                }
                                                                $message .= $crate_code.' Crate Code Created Successfully';
                                                                $pr_scount++;    
                                                            }
                                                        } else {
                                                            $message .= $crate_code.' already exists';
                                                            $pr_fcount++;
                                                            
                                                        }
                                                }                                
                                    } else {
                                        $message .= 'All mandatory fields need to be filled for Crate Import Sheet';
                                        $pr_fcount++;
                                    }
                                    $message .='<br/><br/>';
                                  
                                }
                                $msg .= "Data Imported Successfully.<br>Added : ".$pr_scount." || Error : ".$pr_fcount." || ";
                                $msg .= $pr_scount . ' Crates Created Successfully and ' . $pr_fcount . ' Crates failed to Create';
                                $status = 'success';
                                }
                                DB::commit();
                    } else {
                        DB::rollback();
                        $msg = 'Please upload file';
                    }

                            $message .= PHP_EOL;
                            $status = 400;
                            $url = "";
                            //create the log file as per the excel sheet
                            if(isset($file_path)){
                                $file = fopen($file_path, "w");
                                fwrite($file, $message);
                                fclose($file);
                                $url = $file_path;
                                $message = "Click <a href=".'/'.$file_path." target='_blank'> View Details </a> to view details.";
                            }
                    /* } catch (\ErrorException $ex) {
                      Log::error($ex->getMessage());
                      } */
                    if(!empty($message)){
                        Session::flash('test', $message);
                    }  
                    //$messg = json_encode(array('status' => $status, 'message' => $msg, 'status_messages' => $message));
                    return $msg.$message;
        // }catch (\ErrorException $ex) {
        //     DB::rollback();
        //     $messg = json_encode(array('status' => 400, 'message' =>'', 'status_messages' => "Sorry Failed to Upload Sheet,Reverting all Records. Please check log for More Details"));
        //     return $messg;
        //     Log::error($ex->getMessage());
        //     Log::error($ex->getTraceAsString());
        // }
    }

    public function readExcelCpEnable($path) {
        //try {
            $headerRowNumber = 1;
            Config::set('excel.import.startRow', $headerRowNumber);
            Config::set('excel.import.heading', 'numeric');
            $cat_data = Excel::selectSheetsByIndex(0)->load($path, function($reader) {
                        
                    })->first();
            //print_r($cat_data);die;
            $headerRowNumber = 1;
            Config::set('excel.import.startRow', $headerRowNumber);
            Config::set('excel.import.heading', 'slugged');
            $prod_data = Excel::selectSheetsByIndex(0)->load($path, function($reader) {
                        
                    })->get();
            $data['cat_data'] = $cat_data;
            $data['crate_data'] = $prod_data;//echo "<pre/>";print_r($data);exit;
            return $data;
        // } catch (\ErrorException $ex) {
        //     Log::error($ex->getMessage());
        // }
    }

    public function downloadCrateTransfer(Request $request){
        $data =$request->all();
        $mytime = Carbon::now();
        $headers = array('CRATE CODE');
        $headers_second_page = array('CRATE ID','CRATE CODE','WAREHOUSE NAME');
        $crateDet = $this->_crateManagement->getCrateCodes($data['from_le_wh_id']);
        $loopCounter = 0;
        $exceldata_second = array();
        foreach($crateDet as $val){
            $le_wh_id = DB::table('legalentity_warehouses')->where('le_wh_id',$val['le_wh_id'])->select('display_name')->first();
            $val['le_wh_id'] = $le_wh_id->display_name;
            $exceldata_second[$loopCounter]['crate_id'] = $val['crate_id'];
            $exceldata_second[$loopCounter]['crate_code'] = $val['crate_code'];
            $exceldata_second[$loopCounter]['le_wh_id'] = $val['le_wh_id'];
            $loopCounter++;
        }
        $dummyData = array('crateExcelName'=>'Crate Transfer Sheet-'.$mytime->toDateTimeString());
        UserActivity::userActivityLog('Crate Transfer',$dummyData, 'Crate Transfer Excel downloaded by user');

        Excel::create('Crate Transfer Sheet-'.$mytime->toDateTimeString(), function($excel) use($headers, $headers_second_page, $exceldata_second) 
        {
            $excel->sheet("CrateTransfer", function($sheet) use($headers)
            {
                $sheet->fromArray($headers);
            });

            $excel->sheet("Category&DC Data", function($sheet) use($headers_second_page, $exceldata_second)
            {
                $sheet->loadView('CrateManagement::CrateCodesTemplate', array('headers' => $headers_second_page, 'data' => $exceldata_second)); 
            });
        })->export('xlsx');
    }
     public function uploadCrateTransfer(Request $request){
        $data =$request->all();
        $le_wh_id = $data['to_le_wh_id'];
        DB::beginTransaction();
        $name = Session::all();
        $environment    = env('APP_ENV');
        $file_data                      = Input::file('upload_crate_transfer_template');
        
        if (Input::hasFile('upload_crate_transfer_template')) {
            $path                           = Input::file('upload_crate_transfer_template')->getRealPath();
            $data                           = $this->_category->readExcel($path);
            $result                         = json_decode(json_encode($data['prod_data']), true);
            $headers                        = json_decode(json_encode($data['cat_data']), true);
            $headers1                       = array('CRATE CODE');
            $recordDiff                     = array_diff($headers,$headers1);
            if(empty($recordDiff) && count($recordDiff)==0){
                if(!empty($le_wh_id)){
                    if(!empty($result)){
                        $timestamp = md5(microtime(true));
                        $txtFileName = 'crateTransfer-import-' . $timestamp . '.txt';
                        $file_path = 'download' . DIRECTORY_SEPARATOR . 'cratestatus_logs' . DIRECTORY_SEPARATOR . $txtFileName;
                        $msg = '';
                        $message = '';
                        $updateCnt = $errorCnt = 0;
                        $excelRowcounter = 2;
                        ini_set('max_execution_time', 0);
                        foreach($result as $key => $data){
                            $crate_code = isset($data['crate_code'])?$data['crate_code']:NULL;
                            $msg .= "#".$excelRowcounter." : ";
                            $validFlag = 0;
                            if($crate_code == NULL && empty($crate_code)){
                                $msg .= "Please enter the Crate Code";
                                $validFlag = 1;
                                $errorCnt++;
                            }
                            if($validFlag==0){
                                $warehouse = DB::table('container_master')->where('crate_code',$crate_code)->first();
                                if(!empty($warehouse)){
                                    if($warehouse->le_wh_id != $le_wh_id){
                                        $update = DB::table('container_master')->where('crate_code',$crate_code)->update(['le_wh_id' => $le_wh_id, 'status' => 136001, 'transaction_status' => 137001]);
                                        $msg .= 'Transferred Successfully' . PHP_EOL;
                                        $updateCnt++;
                                    }else{
                                        //$msg .= $crate_code.'  Crate Code Does Not Exist' . PHP_EOL;
                                        $msg .= $crate_code.' is already assigned to same warehouse.' . PHP_EOL;
                                    }
                                }else{
                                    $msg .= $crate_code.'  Crate Does Not Exist' . PHP_EOL;
                                    $errorCnt++; 
                                }
                            }else{
                                $msg .= PHP_EOL;
                            }
                            $excelRowcounter++;
                        }
                        DB::commit();
                        $message .= $updateCnt . ' Crate(s) transferred successfully || ' . $errorCnt . ' Crate(s) failed to transfer.';
                    }else{
                        $message = "File is empty!";
                    }
                }else{
                    $message = "Please select a warehouse!";
                }
            }else{
                DB::rollback();
                $message = 'Invalid Data';
            }
        }else{
            DB::rollback();
            $message = 'Please upload file';
        }
        $message .= PHP_EOL;
        $url = "";
        if(isset($file_path)){
            $file = fopen($file_path, "w");
            fwrite($file, $msg);
            fclose($file);
            $url = $file_path;
            $message .= '<a href=/'.$file_path.' target="_blank"> View Details </a>';
        }
        if(!empty($msg)){
            Session::flash('test', $msg);
        }
        return $message;
    }
}
