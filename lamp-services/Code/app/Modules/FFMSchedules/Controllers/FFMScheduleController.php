<?php

namespace App\Modules\FFMSchedules\Controllers;

use App\Http\Controllers\BaseController;
use Session;
use View;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use URL;
use Log;
use DB;
use Excel;
use App\Modules\Roles\Models\Role;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;
use App\Central\Repositories\RoleRepo;
use UserActivity;
use App\Modules\FFMSchedules\Models\FFMScheduleModel;
use Notifications;
use App\Modules\Notifications\Models\NotificationsModel;
use App\Modules\Pricing\Models\uploadSlabProductsModel;
use App\Modules\Categories\Controllers\CategoryController;

class FFMScheduleController extends BaseController
{
    private $roleRepo;
    public function __construct(){
        try
        {
            $roleRepo = new RoleRepo;
            $this->SchedulesObj = new FFMScheduleModel();
            $this->category = new CategoryController();
            $this->product_slab_details = new uploadSlabProductsModel();
            $this->roleRepo = $roleRepo;
            $this->roleObj = new Role();
            $this->middleware(function ($request, $next) {
                if (!Session::has('userId')){
                    return \Redirect::to('/');
                }
                return $next($request);
            });
            parent::Title('FFM Schedules- Ebutor');
        }
        catch (\ErrorException $ex)
        {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function indexAction(){
        try
        {
            $userId = Session::get('userId');
            $rolerepo = new RoleRepo();
            $access = $rolerepo->checkActionAccess($userId, 'FFS001');
            if (!$access){
                return redirect()->to('/');
            }
            parent::Title('Ebutor - Sales Team Schedules');
            parent::Breadcrumbs(array('Home' => '/', 'Time Management' => '#', 'Sales Team Schedules' => '#'));
            $addPermission = $this->roleRepo->checkPermissionByFeatureCode('FFSA02');
            $editPermission = $this->roleRepo->checkPermissionByFeatureCode('FFSE03');
            $importPermission = $this->roleRepo->checkPermissionByFeatureCode('FFSI05');
            $exportPermission = $this->roleRepo->checkPermissionByFeatureCode('FFSE06');
            return View::make('FFMSchedules::index', ['addPermission' => $addPermission,'editPermission' => $editPermission,'importPermission'=> $importPermission,'exportPermission' => $exportPermission ,'ffms'=> [] ,'cities'=>[],'pincodes'=>[],'dcs'=>[]]);
        }
        catch (\ErrorException $ex)
        {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    public function getSchedules(Request $request){
        $page="";
        $pageSize="";
        if( ($request->Input('page') || $request->Input('page')==0)  && $request->Input('pageSize') ){
            $page = $request->Input('page');
            $pageSize = $request->Input('pageSize');
        }
        $orderByData = $request->Input('$orderby');
        $filterData = $request->Input('$filter');
        $this->SchedulesObj = new FFMScheduleModel();
        $result = $this->SchedulesObj->getSchedules($page,$pageSize,$orderByData,$filterData);
        $editPermission = $this->roleRepo->checkPermissionByFeatureCode('FFSE03');
        $deletePermission = $this->roleRepo->checkPermissionByFeatureCode('FFSD04');
        $loginwh = $this->SchedulesObj->getaccessWarehouse(Session::get('userId'),6);
        try {
            $i = 0;
            foreach ($result['data'] as $record) {
                $cur_date = date_create(date("Y-m-d"));
                $date = date_create($record->date);
                $scheduleId = $result['data'][$i]->fps_id;
                $actions = '';
                if($date >= $cur_date && $editPermission){
                    $actions.= '<span class="actionsStyle" ><a onclick="editSchedule('.$scheduleId.')"</a><i class="fa fa-pencil"></i></span> ';
                }
                if($deletePermission){
                   $actions.= '<span class="actionsStyle" ><a onclick="deleteSchedule('.$scheduleId.')"</a><i class="fa fa-trash-o"></i></span>';
                }
                $result['data'][$i++]->actions = $actions;
                }
                return ["Records" => $result['data'], "TotalRecordsCount" => $result['count']];
            } catch (Exception $e) {
                    Log::error($e->getMessage()." ".$e->getTraceAsString());
                    return ["Records" => [], "TotalRecordsCount" => 0];
                }
        }
    public function downloadExcel(){
        $mytime = Carbon::now();
        $headers = array('FFM Name','Date(dd/mm/yyyy)','Mobile No','City','PIN Code','DC Code');
        $headers_second_page = array('FFM Name','Mobile Number','DC Name','DC Code');
        $ffmDet = DB::table('users')->leftJoin('user_roles','user_roles.user_id','=','users.user_id')
                    ->select(DB::raw('concat(users.firstname," ",users.lastname) as name'),'mobile_no')
                    ->where('user_roles.role_id',52)
                    ->where('users.is_active',1)->get()->all();
        $ffmDet = json_decode(json_encode($ffmDet),1);
        $dcDet = json_decode($this->product_slab_details->getAllDCType(), true);
        $loopCounter = 0;
        $exceldata_second = array();
        foreach($ffmDet as $val){
            $exceldata_second[$loopCounter]['name'] = $val['name'];
            $exceldata_second[$loopCounter]['mobile_no'] = $val['mobile_no'];
            $loopCounter++;
        }
        $loopCounter = 0;
        foreach($dcDet as $val){
            $exceldata_second[$loopCounter]['dc_name'] = $val['lp_wh_name'];
            $exceldata_second[$loopCounter]['dc_code'] = $val['le_wh_code'];
            $loopCounter++;
        }
        $file_name = 'FFM Schedules Sheet_' . $mytime->toDateTimeString();
        $result = Excel::create($file_name, function($excel) use($headers, $headers_second_page, $exceldata_second) {
            $excel->sheet('FFM Schedules', function($sheet) use($headers) {
                    $sheet->fromArray($headers);
                    $sheet->setColumnFormat(array(
                        'B' => 'dd/mm/yyyy'));
                });
            $excel->sheet("FFM Data", function($sheet) use($headers_second_page, $exceldata_second){
                $sheet->loadView('FFMSchedules::SampleTemplate',array('headers' => $headers_second_page,'data' => $exceldata_second)); 
            });
        })->export('xlsx');
    }
    
    public function uploadffmschedules(Request $request){
        try{
            DB::beginTransaction();
            $name = Session::all();
            $environment    = env('APP_ENV');
            $file_data                      = Input::file('schedules_data');
            $file_extension                 = $file_data->getClientOriginalExtension();

            if( $file_extension != 'xlsx'){
                return 'Invalid file type';
            }else{
                if (Input::hasFile('schedules_data')) {
                    $path                           = Input::file('schedules_data')->getRealPath();
                    $data                           = $this->category->readExcel($path);
                    $result                         = json_decode(json_encode($data['prod_data']), true);
                    $headers                        = json_decode(json_encode($data['cat_data']), true);
                    $headers[1]                     = 'Date(dd/mm/yyyy)';
                    $headers1                       = array('FFM Name','Date(dd/mm/yyyy)','Mobile No','City','PIN Code','DC Code');
                    $recordDiff                     = array_diff($headers,$headers1);
                    if(empty($recordDiff) && count($recordDiff)==0){
                        $timestamp = md5(microtime(true));
                        $txtFileName = 'schedules-import-' . $timestamp . '.txt';
                        $file_path = 'download' . DIRECTORY_SEPARATOR . 'schedules_log' . DIRECTORY_SEPARATOR . $txtFileName;
                        $msg = '';
                        $updateCnt = $insertCnt = $errorCnt = 0;
                        $excelRowcounter = 2;
                        ini_set('max_execution_time', 0);
                        $loginwh = $this->SchedulesObj->getaccessWarehouse(Session::get('userId'),6);
                        foreach($result as $key => $data){
                            $msg .= "#".$excelRowcounter." FFM(".$data['ffm_name'].") : ";
                            $date = is_array($data) ? $data['dateddmmyyyy'] :'1970-01-01' ;
                            $date = date("Y-m-d", strtotime($date['date']));
                            // Check for valid data
                            $validFlag = 0;
                            if($date=="" || $date=='1970-01-01'|| (strpos($date,'1900') !== false)){
                                $msg .= " : Date is not valid, please check date format (dd/mm/yyyy)!";
                                $validFlag = 1;
                            }
                            /*chk num & ffm assigned to it, chk ffm name related to that number
                              if name and no match -> chk if logged_in user has access to edit that ffm */
                            if($data['mobile_no']=='' || strlen($data['mobile_no']) != 10){
                                $msg .= " : Please enter valid mobile number!";
                                $validFlag = 1; 
                            }else{
                                $details = DB::table('users')->leftJoin('user_roles','user_roles.user_id','=','users.user_id')->where('user_roles.role_id',52)->where('users.mobile_no',$data['mobile_no'])->where('users.is_active',1)->get()->all();
                                if(empty($details)){
                                    $msg .= " : There is no active FFM for that mobile number.Please check again!";
                                    $validFlag = 1;
                                }
                                else{
                                    $data['ff_id'] = $details[0]->user_id;
                                    if($data['ffm_name'] != $details[0]->firstname.' '.$details[0]->lastname){
                                        if($data['ffm_name'] == '')
                                            $msg .= " : Please enter FFM Name!";
                                        else
                                            $msg .= " : There is another FFM assigned to that mobile number.Please check FFM name in sheet2!";
                                        $validFlag = 1;
                                    }else{
                                        $ffmwh = $this->SchedulesObj->getaccessWarehouse($data['ff_id'],6);
                                        $wh = array_intersect($loginwh, $ffmwh);
                                        if(empty($wh)){
                                            $msg .= " : You don't have access to add/edit schedules for ".$data['ffm_name'];
                                            $validFlag = 1;
                                        }
                                    }
                                }
                            }
                            /*chk if given dc_code is apt, chk if ffm has acces to that warehouse, chk if pincode is related to that warehouse */
                            if(!empty($data['dc_code'])){
                                $business_unit= DB::table('legalentity_warehouses')->where('le_wh_code',$data['dc_code'])->select('le_wh_id')->get()->all();
                                if(empty($business_unit)){
                                    $msg .= " : Please select a valid warehouse !";
                                    $validFlag = 1;
                                }else{
                                    if(isset($data['ff_id'])){
                                        $warehouses = $this->SchedulesObj->getaccessWarehouse($data['ff_id'],6);
                                        if(in_array($business_unit[0]->le_wh_id,$warehouses)){
                                            $data['dc_code'] = $business_unit[0]->le_wh_id;
                                        }else{
                                            $msg .= " : Please select a warehouse that FFM has access to!";
                                            $validFlag = 1;
                                        }
                                    }
                                }
                            }
                            if(empty($data['city'])){
                                $msg .= " : Please select a City !";
                                $validFlag = 1;
                            }else{
                                $statecity = DB::table('state_city_codes')->where('city_name',$data['city'])->select('city_name','state_name')->first();
                                if(empty($statecity)){
                                    $msg .= " : Please select a valid city !";
                                    $validFlag = 1;
                                }else{
                                    $data['state'] = $statecity->state_name;
                                    $pin = DB::table('cities_pincodes')->where('city',$data['city'])->where('pincode',$data['pin_code'])->select('city')->get()->all();
                                    if(empty($pin)){
                                        $msg .= " : Please enter a pincode related to given city !";
                                        $validFlag = 1;
                                    }
                                }
                            }
                            if($validFlag==0){
                                $slab_data = array(
                                    'mobile_no' => $data['mobile_no'],
                                    'ff_id' => $data['ff_id'],
                                    'ff_name' => $data['ffm_name'],
                                    'city' => $data['city'],
                                    'pincode' => $data['pin_code'],
                                    'le_wh_id' => $data['dc_code'],
                                    'state' => $data['state'],
                                    'date' => $date,
                                    'created_by' => Session::get('userId')
                                );
                                $uploadResponse = $this->SchedulesObj->insertUploadSchedules($slab_data);
                                //write for the Text File
                                $msg .= $uploadResponse['message'] . PHP_EOL;
                                if($uploadResponse['counter_flag']==1){
                                    $updateCnt++;
                                }elseif($uploadResponse['counter_flag']==2){
                                    $insertCnt++;
                                }elseif($uploadResponse['counter_flag']==3){
                                    $errorCnt++;
                                }
                            }else{
                                $msg .= PHP_EOL;
                                $errorCnt++;
                            }
                            $excelRowcounter++;
                        }
                        //create the log file as per the excel sheet
                        $file = fopen($file_path, "w");
                        fwrite($file, $msg);
                        fclose($file);
                        DB::commit();
                        return "Data Imported successfully.<br>Added : ".$insertCnt." || Updated :".$updateCnt." || Error : ".$errorCnt.' <a href="/'.$file_path.'" target="_blank"> View Details </a>';
                    }else{
                        DB::rollback();
                        return "Invalid Data";
                    }
                }else{
                    return "Invalid Data!";
                }
            }
        }catch (\ErrorException $ex) {
            DB::rollback();
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return "Sorry Failed to Upload Sheet,Reverting all Records.";
        } 
    }
    public function addSchedule(){
        $data = Input::all();
        $result['status'] = false;
        $result['status'] = $this->SchedulesObj->addNewSchedule($data);
        return $result;
    }
    public function editSchedule($id){
        if($id > 0 || $id != null){
            $data = $this->SchedulesObj->getSchedule($id);
            if(!empty($data)){
                $result['fps_id'] = $data[0]->fps_id;
                $result['ff_id'] = $data[0]->ff_id;
                $result['ff_name'] = $data[0]->ff_name;
                $result['mobile_no'] = $data[0]->mobile_no;
                $result['le_wh_id'] = $data[0]->le_wh_id;
                $result['city'] = $data[0]->city;
                $result['pincode'] = $data[0]->pincode;
                $result['date'] = $data[0]->date;
                
            }
            $warehouses = $this->getWarehouse($data[0]->ff_id);
            $pincodes = DB::table('cities_pincodes')->where('city',$data[0]->city)->select('pincode')->groupby('pincode')->get()->all();
            $pincodes = json_decode(json_encode($pincodes,1));
            $sel_city = DB::table('state_city_codes')->where('city_name',$data[0]->city)->select('city_name')->first();
            $data['sel_city'] = $sel_city;
            $data['le_wh_id'] = $warehouses;
            $data['pincodes'] = $pincodes;
            $data['result'] = $result;
            return $data;
        }
        // If it reaches here, then it return false
        return '';
    }
 
    public function updateSchedule(){
        $data = Input::all();        
        $result['status'] = false;
        if(empty($data['fps_id'])){
            return $result;
        }       
        if($data == []) return $result;
        $result['status'] = $this->SchedulesObj->updateSchedule($data);
        return $result;
    }
    
    public function deleteSchedule($id){
        $status = false;
        if($id < 0 or $id != null)
            $status = $this->SchedulesObj->deleteSchedule($id);
        return ["status" => $status];
    }
    public function getWarehouse($ffm){
        $data = $this->SchedulesObj->getWarehouse($ffm);
        return $data;
    }
    public function getPincodes(Request $request){

        $city = $request->get('city');
        $pincodes = DB::table('cities_pincodes')->where('city',$city)->select('pincode')->groupby('pincode');
        $pincodes = json_decode(json_encode($pincodes->get()->all()),1);
        return $pincodes;
    }
    public function exportSchedules(){
        try {
            $ff_id = Input::get('ff_id');
            $fdate = Input::get('fdate');
            $tdate = Input::get('tdate');
            $reportDate = Carbon::now();                        
            $report_excel = $this->SchedulesObj->excelFFMschedules($fdate, $tdate,$ff_id);
            Excel::create('FFMSchedules_' . $reportDate, function($excel) use($report_excel) {
                $excel->sheet('schedulesData', function($sheet) use($report_excel) {          
                $sheet->fromArray($report_excel);
                }); 
            })->export('xls');       
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    public function getFFMList(){
        $loginwh = $this->SchedulesObj->getaccessWarehouse(Session::get('userId'),6);
        $column = 'user_id';
        $saleslead = '';
        $managers = $this->SchedulesObj->getlist($column);
        $parentWiseArr = array();
        $parentWiseArr[count($parentWiseArr)]="<option value=''"." disabled='disabled' selected='selected' ></option>";
        foreach($managers as $key=>$manager){
            $wh = $this->SchedulesObj->getaccessWarehouse($manager['user_id'],6);
            $cmwh = array_intersect($loginwh, $wh);
            if(!empty($cmwh)){
                $column = 'reporting_manager_id';
                $lead = $this->SchedulesObj->getlist($column,$manager['user_id']);
                if($saleslead != $lead[0]['reporting_manager_id']){
                    $parentWiseArr[count($parentWiseArr)]="<option value='".$lead[0]['reporting_manager_id']."' disabled='disabled'"." class='bu1' >".$lead[0]['name']."</option>";
                    $saleslead = $lead[0]['reporting_manager_id'];
                }
                $parentWiseArr[count($parentWiseArr)]="<option value='".$manager['user_id']."' class='bu2' >".$manager['ffm']."</option>";
            }
        }
        return $parentWiseArr;
    }
    public function getCitiesList(Request $request){
        try{
            $dc = $request->get('dc');
            $city = $request->get('city');
            $namesList = $this->SchedulesObj->getAllNames($city,$dc);
            return $namesList;
        }
        catch (\ErrorException $ex){
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
}
