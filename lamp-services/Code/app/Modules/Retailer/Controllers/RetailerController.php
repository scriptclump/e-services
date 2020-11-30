<?php

namespace App\Modules\Retailer\Controllers;

use App\Central\Repositories\RoleRepo;
use App\Central\Repositories\ReportsRepo;
use App\Central\Repositories\CustomerRepo;
use App\Central\Repositories\ProductRepo;
use App\Http\Controllers\BaseController;
use Session;
use View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Log;
use DB;
use Redirect;
use App\Modules\Retailer\Models\BrandModel;
use \App\Modules\Users\Models\Users;
use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;
use \App\Modules\LegalEntities\Models\Legalentity;
use \App\Modules\Retailer\Models\Retailer;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;
use Config;
use Excel;
use ZipArchive;
use Carbon\Carbon;
use Response;
use App\Modules\Cpmanager\Models\RegistrationModel;
use App\Modules\ApprovalEngine\Controllers\commonIgridController;
use App\Modules\Orders\Models\PaymentModel;
use Lang;

Class RetailerController extends BaseController {

    protected $roleAccessObj;
    protected $retailerObj;
    protected $roleid;
    protected $_roleRepo;
    protected $_paymentObj;
    protected $_docTypes;


    public function __construct(RoleRepo $roleAccessObj, Retailer $retailerObj) {
        try {
            $this->roleAccessObj = new RoleRepo();
            $this->retailerObj = $retailerObj;
            $this->docTypes = array();
            $this->middleware(function ($request, $next) {
                if (!Session::has('userId')) {
                    return Redirect::to('/');
                }
                $this->roleid = $this->roleAccessObj->getRole();

            $this->produc_grid_field_db_match = [
                'business_legal_name' => 'business_legal_name',
                'rank_pct' => 'rank_pct',
                'legal_entity_type' => 'legal_entity_type',
                'business_type' => 'business_type',
                'volume_class' => 'volume_class',
                'name' => 'retailer_flat.name',
                'beat_rm_name' => 'beat_rm_name',
                'le_code' => 'retailer_flat.le_code',
                'mobile_no' => 'retailer_flat.mobile_no',
                'No_of_shutters' => 'No_of_shutters',
                'area' => 'AREA',
                'beat' => 'retailer_flat.beat',
                'orders' => 'orders',
                'created_at' => 'retailer_flat.created_at',
                'created_by' => 'retailer_flat.created_by',
                'updated_at' => 'retailer_flat.updated_at',
                'updated_by' => 'retailer_flat.updated_by',
                'city' => 'city',
                'address' => 'address',
                'state' => 'retailer_flat.state',
                'pincode' => 'retailer_flat.pincode',
                'last_order_date' => 'last_order_date',
                'is_approved' => 'is_approved',
                'business_start_time' => 'business_start_time',
                'business_end_time' => 'business_end_time',
                'preference_value' => 'preference_value',
                'DC' => 'DC'
                ];
               
                return $next($request);
            }); 

        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function retailersList() {
        try {
            if (!Session::has('userId')) {
                return Redirect::to('/');
            }
            $customerAccess = $this->roleAccessObj->checkPermissionByFeatureCode("CUS001");
            if($customerAccess){
                $breadCrumbs = array(trans('retailers.breadcrumbs.home') => url('/'), trans('retailers.breadcrumbs.retailers') => '#', trans('retailers.breadcrumbs.retailers') => '#');
                parent::Breadcrumbs($breadCrumbs);
                parent::Title(trans('dashboard.dashboard_title.company_name').' - '.trans('retailers.title.index_page_title'));
                $buttonPermissions = $this->retailerObj->getButtonPermissions();
                $this->_roleRepo = new RoleRepo();
                $creditlimitPermissions = $this->_roleRepo->checkPermissionByFeatureCode('UPCL01');
                $creditlimitDownloadPermissions = $this->_roleRepo->checkPermissionByFeatureCode('CRTLTDW01');
                return View::make('Retailer::retailerlist')->with([
                            'buttonPermissions'=>$buttonPermissions,
                            'creditlimitPermissions'=>$creditlimitPermissions,
                            'creditlimitDownloadPermissions'=>$creditlimitDownloadPermissions]);
            }else{
                return Redirect::to('/');                
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getRetailers(Request $request) {
        try {
            $results = $this->retailerObj->filterRetailersData($request, $this->produc_grid_field_db_match);
            return json_encode($results);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function dashboardCustomers() {
        try {
            $data = Input::all();
            $results = json_decode($this->retailerObj->getDashboardRetailers($data));
            foreach ($results as $record) {
                $record->le_code='<a href="/retailers/edit/' . $this->roleAccessObj->encodeData($record->legal_entity_id) . '" >'.$record->le_code.'</a>';
            }
            return $results;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function exportCustomers()
    {
        try
        {
            $data = Input::all();
            $fromDate = date('Y-m-01');
            $toDate = date('Y-m-d');
            if(!empty($data))
            {                
                $tempFromDate = (isset($data['fdate']) && $data['fdate'] != '') ? $data['fdate'] : $fromDate;
                $tempToDate = (isset($data['tdate']) && $data['tdate'] != '') ? $data['tdate'] : $toDate;
                $fromDate = date("Y-m-d", strtotime($tempFromDate));
                $toDate = date("Y-m-d", strtotime($tempToDate));
            }
            $id=Session::get('legal_entity_id');
            $userId=Session::get('userId');
            $customerCollection = "CALL getLegalEntitiesExportData('$fromDate','$toDate','$id','$userId')";
            $file_name = 'customer_list_' .date('Y-m-d-H-i-s').'.csv';
            $this->exportToCsv($customerCollection, $file_name);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function exportToCsv($query, $filename) {
        $host = env('READ_DB_HOST');
        $port = env('DB_PORT');
        $dbname = env('DB_DATABASE');
        $uname = env('DB_USERNAME');
        $pwd = env('DB_PASSWORD');
        $filePath = public_path().'/download/CustomersList/'.$filename;
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
    
    public function editAction($retId) {
        try {
            if(is_numeric($retId)) {
                return redirect('retailers/index')->withFlashMessage('Invalid retailer id');
                exit;
            }
            $retId = $this->roleAccessObj->decodeData($retId);
            $docTypes = $this->retailerObj->getDocumentTypes();
            $docsArr = $this->retailerObj->legalEntityDoc($retId);
            $leDocDetails = $this->retailerObj->getleDocDetails($retId);
            $parentLeId = DB::table('legal_entities')->select('parent_le_id')->where('legal_entity_id',$retId)->first();
            $parentLeId = $parentLeId->parent_le_id;
             Session::put('custRt_Id',$retId);
            $breadCrumbs = array(trans('retailers.breadcrumbs.home') => url('/'), trans('retailers.breadcrumbs.retailers') => '#',
                trans('retailers.breadcrumbs.retailers') => url('/') . '/retailers/index', trans('retailers.title.edit_page_title') => '#');
            parent::Breadcrumbs($breadCrumbs);
            $response = $this->retailerObj->getRetailerData($retId,$parentLeId);
            $leCode = isset($response['retailers']) ? (property_exists($response['retailers'], 'le_code') ? $response['retailers']->le_code :'') : '';
            parent::Title(trans('dashboard.dashboard_title.company_name').' - '.trans('retailers.title.edit_page_title').' ('.$leCode.')');

            $rank = isset($response['retailers']) ? (property_exists($response['retailers'], 'rank_pct') ? intval($response['retailers']->rank_pct) :'') : '';
            if($rank >= 0 and $rank <= 50)  $rank_pct = "Silver (".$rank.")";
            else if($rank >= 51 and $rank <= 75)  $rank_pct = "Gold (".$rank.")";
            else if($rank >= 76)  $rank_pct = "Platinum (".$rank.")";
            else $rank_pct = "Rank ".$rank;


            $response['retailers']->rank_pct = $rank_pct;
            $response['retailers']->retid = $retId;
            if($response['retailers']->business_type_id == 47001)
                $response['dcfc_access'] = 1;
            else
                $response['dcfc_access'] = 0;
            $businessNames = $this->retailerObj->businessNames();
            $AddfeedbackPermission = $this->roleAccessObj->checkPermissionByFeatureCode('RETF01');
            $feedbackGroup=$this->retailerObj->getFeedbackGroup();
            $feedbackComments=$this->retailerObj->getFeedbackComments();
            return View::make('Retailer::retaileredit', $response)
                        ->with('businessNames',$businessNames)
                        ->with('docTypes',$docTypes)
                        ->with('docsArr',$docsArr)
                        ->with('le_id',$retId)
                        ->with('leDocDetails',$leDocDetails)
                        ->with("AddfeedbackPermission",$AddfeedbackPermission)
                        ->with("feedbackGroup",$feedbackGroup)
                        ->with("feedbackComments",$feedbackComments);

        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function approveAction($retId) {
        try {
            if(is_numeric($retId)) {
                return redirect('retailers/index')->withFlashMessage('Invalid retailer id');
                exit;
            }
            $breadCrumbs = array(trans('retailers.breadcrumbs.home') => url('/'), trans('retailers.breadcrumbs.retailers') => '#',
                trans('retailers.breadcrumbs.retailers') => url('/') . '/retailers/index', trans('retailers.title.approve_page_title') => '#');
            parent::Breadcrumbs($breadCrumbs);            
            $retId = $this->roleAccessObj->decodeData($retId);
            $response = $this->retailerObj->getRetailerData($retId);
            parent::Title(trans('dashboard.dashboard_title.company_name').' - '.trans('retailers.title.approve_page_title'));
            return View::make('Retailer::retailer_approve', $response);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    public function updateecash(){
        $data = Input::all();
        $_paymentObj = new PaymentModel();
        $userId = DB::table('users')->select('user_id')->where('legal_entity_id',$data['legal_entity_id'])->first();
        $userId = $userId->user_id;
        $loggedUser = Session::get('userId');
        if($userId>0 && $userId != ''){
                $eCash = ['cashback'=>$data['ecash_available']];
                $_paymentObj->updateEcash($userId, $eCash);
                $ecashHistory = ['user_id'=>$userId,
                                'legal_entity_id'=>$data['legal_entity_id'],
                                'order_id'=>0,
                                'delivered_amount'=>0,
                                'cash_back_amount'=>$data['ecash_available'],
                                'balance_amount'=>$data['ecash_available'],
                                'transaction_type'=>143002,
                                'transaction_date'=>date('Y-m-d H:i:s'),
                                'order_status_id'=>0,
                                'comment'=>"Cashback added from web end!",
                                'created_by'=>$loggedUser,
                                'created_at'=>date('Y-m-d H:i:s')
                                ];
                $_paymentObj->saveEcashHistory($ecashHistory);
            }
        $ecash = $_paymentObj->getUserEcash($userId);
        $ecash = $ecash->cashback;
        return $ecash;
    }

    public function update($retailerDetails=[]) {
        DB::beginTransaction();
        try {
            date_default_timezone_set('Asia/Kolkata');
            $currentTime = date( 'Y-m-d h:i:s', time () );
            $status = 0;
            $message = 'Unable to save';
            $data = count($retailerDetails)>0?$retailerDetails:Input::get();
            $file = Input::file();
            //$legal_entity_check= $this->retailerObj->checkPincodeLegalentity($data['org_pincode']);
            if(isset($data['parent_le'])){
                $parent_le_id=$data['parent_le'];
            }else{
                $legal_entity_check=$this->retailerObj->checkHubLegalentity($data['hub_id']);
                $retailer_legal_entity=$this->retailerObj->getParentLeId($data['legalEntityId']);
                $parent_le_id=$retailer_legal_entity;
                if($legal_entity_check != $retailer_legal_entity){
                    $parent_le_id=$legal_entity_check;
                } 
            }
            $address2 = [];
            $address2 = isset($data['org_address2']) ? $data['org_address2'] : '';
            $legalEntityId = isset($data['legalEntityId']) ? $data['legalEntityId'] : 0;
            $retailers = array('parent_le_id'=>$parent_le_id,'business_legal_name' => $data['retailer_name'], 
                'business_type_id' => $data['business_type_id'], 
                'legal_entity_type_id' => $data['legal_entity_type_id'], 
                'gstin' => $data['gstin'],
                'fssai' => $data['fssai'],
                'address1' => $data['org_address1'],
                'address2' => $address2, 'country' => $data['org_country'],
                'state_id' => $data['org_state'], 'city' => $data['org_city'],
                'pincode' => $data['org_pincode'],'locality' => $data['locality'],
                'latitude' => $data['latitude'],'longitude' => $data['longitude'],
                'updated_by' => Session::get('userId'), 'landmark' => $data['landmark'],
                'updated_at' => $currentTime);
            // Log::info($legalEntityId);
                DB::table('legal_entities')->where('legal_entity_id', $legalEntityId)->update($retailers);

            $volume = isset($data['volume']) ? $data['volume'] : 0;
            $mobile_no= isset($data['mobile_no'])? $data['mobile_no'] : 0;
            $shutters = isset($data['shutters']) ? $data['shutters'] : 0;
            $area_id = isset($data['area_id']) ? $data['area_id'] : 0;
            $masterManufacturer = isset($data['master_manf']) ? (!empty($data['master_manf']) ? implode(',', $data['master_manf']) : '') : 0;
            $network = isset($data['network']) ? (($data['network'] == 'on') ? 1 : 0) : 0;
            $smartphone = isset($data['smartphone']) ? $data['smartphone'] : 0;
            $beatValue = isset($data['beat']) ? $data['beat'] : 0;
            $hubValue = isset($data['hub_id']) ? $data['hub_id'] : 0;
            $spokeValue = isset($data['spoke_id']) ? $data['spoke_id'] : 0;
            $is_fridge = isset($data['is_fridge']) ? (($data['is_fridge'] == 'on') ? 1 : 0) : 0;
            $is_visicooler = isset($data['is_visicooler']) ? (($data['is_visicooler'] == 'on') ? 1 : 0) : 0;
            $is_deepfreezer = isset($data['is_deepfreezer']) ? (($data['is_deepfreezer'] == 'on') ? 1 : 0) : 0;
            $is_milk = isset($data['is_milk']) ? (($data['is_milk'] == 'on') ? 1 : 0) : 0;
            $is_icecream = isset($data['is_icecream']) ? (($data['is_icecream'] == 'on') ? 1 : 0) : 0;
            $is_vegetables = isset($data['is_vegetables']) ? (($data['is_vegetables'] == 'on') ? 1 : 0) : 0;
            $sms_notification = isset($data['sms_notification']) ? (($data['sms_notification'] == 'on') ? 1 : 0) : 0;

            $customersData = ['volume_class' => $volume, 'No_of_shutters' => $shutters, 
                'area_id' => $area_id, 'network' => $network, 'smartphone' => $smartphone,
                'updated_by' => Session::get('userId'),
                'master_manf' => $masterManufacturer, 
                'beat_id' => $beatValue, 'hub_id' => $hubValue,'spoke_id' => $spokeValue,
                'is_fridge'=>$is_fridge,'is_visicooler'=>$is_visicooler,'is_deepfreezer'=>$is_deepfreezer,
                'is_milk'=>$is_milk,'is_icecream'=>$is_icecream,'is_vegetables'=>$is_vegetables];
            // Log::info($customersData);
            $customerInfo = DB::table('customers')->where('le_id', $legalEntityId)->pluck('id')->all();
            if(!empty($customerInfo))
            {
                DB::table('customers')->where('le_id', $legalEntityId)
                    ->update($customersData);
            }else{
                $customersData['le_id'] = $legalEntityId;
                $customersData['created_by'] = Session::get('userId');
                DB::table('customers')->insert($customersData);
            }
            
            $businessStartTime = isset($data['business_start_time']) ? date("H:i", strtotime($data['business_start_time'])) : 0;
            $businessEndTime = isset($data['business_end_time']) ? date("H:i", strtotime($data['business_end_time'])) : 0;
            $preferrenceValue = isset($data['preference_value']) ? $data['preference_value'] : 0;              
            $preferrenceData = ['business_start_time' => $businessStartTime, 
                'business_end_time' => $businessEndTime, 
                'preference_value' => $preferrenceValue,
                'sms_subscription' => $sms_notification];
            $usersList = $this->roleAccessObj->getUsersByLegalEntityId($legalEntityId);
            $userId = $this->roleAccessObj->getUserIdByLegalEntityId($legalEntityId);
            if(isset($data['aadhar_number'])){
                $aadharArray=array('aadhar_id' => $data['aadhar_number'],'mobile_no' => $mobile_no);
                DB::table('users')->where('legal_entity_id',$data['legalEntityId'])
                ->update($aadharArray);
            }else if($mobile_no!=0){
                $mobileData=array('mobile_no' => $mobile_no);
                DB::table('users')->where('legal_entity_id',$data['legalEntityId'])
                ->update($mobileData);
            }
            DB::table('retailer_flat')
                ->where('legal_entity_id',$data['legalEntityId'])
                ->update(['fssai' => $data['fssai']]);
            if(isset($data['name'])){
                DB::table('users')
                ->where('legal_entity_id',$data['legalEntityId'])
                ->update(['firstname' => $data['name']]);
                DB::table('retailer_flat')
                ->where('legal_entity_id',$data['legalEntityId'])
                ->update(['name'=>$data['name']]);
            }
            if(!empty($usersList))
            {
                foreach($usersList as $user)
                {
                    $preferenceId = DB::table('user_preferences')
                            ->where('user_id', $user->user_id)
                            ->first(['preference_id']);
                    if(!empty($preferenceId))
                    {
                        DB::table('user_preferences')->where('user_id', $user->user_id)
                        ->update($preferrenceData);
                    }else{
                        $preferrenceData['user_id'] = $user->user_id;
                        DB::table('user_preferences')->insert($preferrenceData);
                    }
                    // Log::info($preferrenceData);
                }
            }
            $legalentity = new Legalentity();
            if(!empty($file))
            {
                $legalentity->saveProfilePic($file, $legalEntityId, $userId);
            }
            $this->retailerObj->updateFlatTable($legalEntityId,$parent_le_id);
            $status = 1;
            $message = "Successfully updated";
            $result = ['status' => $status, 'message' => $message];
            DB::table('users')->where('legal_entity_id',$data['legalEntityId'])->update(['password_token' => '']);
            DB::commit();
            return json_encode($result);
//            return \Redirect::to('retailers/index');
        } catch (\ErrorException $ex) {
            DB::rollback();
            
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            $result = ['status' => 400, 'message' => "Failed to update!"];
            return json_encode($result);
            //$result = ['status' => $status, 'message' => $ex->getMessage()];
        }
    }

    public function destroy(Request $request) {
        try {
            $data=$request->all();
            $returnData=array();
            if (isset($data['retId']) && $data['retId']!=0){
                $result=$this->retailerObj->deleteRetailer($data['retId']);
                $returnData['status']=$result;
            }else{
                $returnData['status']=false;
            }
            return $returnData;           
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function blockUsers() {
        try {
            $legalEntityId = Input::get('retId');
            $status = Input::get('status');
            return $this->roleAccessObj->inactiveUsers($legalEntityId, $status);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function approvalSubmitAction()
    {
        try
        {
            $status = 0;
            $message = '';
            $data = Input::all();
//            echo "<pre>";print_R($data);die;
            $userID = Session::get('userId');
            $flowType = 'Retailer';
            $retailerId = isset($data['legal_entity_id']) ? $data['legal_entity_id'] : 0;
            $isApprovedResult = DB::table('legal_entities')
                    ->where(['legal_entity_id' => $retailerId])
                    ->first(['status_id']);
            $currentStatusId = property_exists($isApprovedResult, 'status_id') ? $isApprovedResult->status_id : 0;
            $nextStatusId = isset($data['status_id']) ? $data['status_id'] : 0;
            $flowComment = isset($data['comments']) ? $data['comments'] : '';
            $isApproved = isset($data['is_approved']) ? $data['is_approved'] : 0;
            $approvalCommonFlow = new CommonApprovalFlowFunctionModel();
            $approvalResponse = $approvalCommonFlow->storeWorkFlowHistory($flowType, $retailerId, $currentStatusId, $nextStatusId, $flowComment, $userID);
            if($approvalResponse)
            {
                DB::table('legal_entities')
                        ->where(['legal_entity_id' => $retailerId])
                        ->update(['status_id' => $nextStatusId, 'is_approved' => $isApproved]);
                $mobileDetails = DB::table('legal_entities')
                        ->leftJoin('users', 'users.legal_entity_id', '=', 'legal_entities.legal_entity_id')
                        ->where(['legal_entities.legal_entity_id' => $retailerId])
                        ->first(['users.mobile_no']);
                $mobileNumber = property_exists($mobileDetails, 'mobile_no') ? $mobileDetails->mobile_no : 0;
                if($mobileNumber > 0)
                {
                    $retailerMessage = 'Your account is active now and can place orders from EBUTOR.';
                    $customerRepo = new CustomerRepo();
                    $customerRepo->sendSMS(0,0,$mobileNumber, $retailerMessage, 1, 0, 0);
                    $message = $retailerMessage; 
                }else{
                    $message = 'Mobile number of retailer is null, unable to send sms';
                    Log::info($message);
                }
                $status = 1;
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            $message = $ex->getMessage();
        }
        return json_encode(['status' => $status, 'message' => $message]);
    }
    
    public function getUsersList(Request $request)
    {
        try
        {
            $data = $request->input();
            $data = $data['data'];
            $data=json_decode($data,1);
            $legalEntityId=$data['le_id'];
            $mobile_no=$data['mobile_no'];
            $business_type=$data['business_type'];

            $results['Records'] = [];
            $le_type=[1014,1016];
            if($legalEntityId > 0 && !in_array($business_type, $le_type))
            {
                $results['Records'] = $this->roleAccessObj->getUsersByLegalEntityId($legalEntityId);
            }else{
                $results['Records'] = $this->retailerObj->getUsersByLegalEntityIdAndMobileno($legalEntityId,$mobile_no);
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        return json_encode($results);
    }    
    
    public function getAreaList()
    {
        try
        {
            $results = [];
            $data = Input::all();
            $pincode = isset($data['pincode']) ? $data['pincode'] : 0;
            if($pincode)
            {
                $results = $this->roleAccessObj->getAreaData($pincode);
            }            
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        return json_encode($results);
    }
    
    public function getBeatList()
    {
        try
        {
            $results = [];
            $data = Input::all();
            $hubId = isset($data['spoke_id']) ? $data['spoke_id'] : 0;
            if($hubId)
            {
                $results = $this->roleAccessObj->getBeatData($hubId);
            }            
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        return json_encode($results);
    }
    public function getBeatsforLeId(){
        try{
            $results = [];
            $data = Input::all();
            $beats = $this->retailerObj->getBeatDataForLeId($data['parentId']);
            return $beats;
        }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function getSpokesList()
    {
        try
        {
            $results = [];
            $data = Input::all();
            $hubId = isset($data['hub_id']) ? $data['hub_id'] : 0;
            if($hubId)
            {
                $results = $this->roleAccessObj->getSpokeData($hubId);
            }            
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        return json_encode($results);
    }
    
    public function getServicableList($pincode)
    {
        try
        {
            $results['Records'] = [];
            if($pincode)
            {
                $results['Records'] = $this->roleAccessObj->getServicableList($pincode);
            }            
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        return json_encode($results);
    }
    
    public function getCollectionDetails($legalEntityId)
    {
        try
        {
            $results['Records'] = [];
            $results['Records'] = $this->roleAccessObj->getCollectionDetails($legalEntityId);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        return json_encode($results);
    }

    public function getOrdersList($legalEntityId)
    {
        try
        {
            $results['Records'] = [];
            $results['Records'] = $this->roleAccessObj->getOrdersList($legalEntityId);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        return json_encode($results);
    }
    
    public function importRetailers()
    {
        try
        {
            $file = Input::file('import_retailers');
            //$fileName = $file->getClientOriginalName();
            $pathName = $file->getPathName();
            $retailerResult = [];
            global $finalmessage;
            \Excel::selectSheetsByIndex(0)->load($pathName, function($reader)
            {
                global $finalmessage;
                $retailerResult = $reader->all();
                if (!empty($retailerResult))
                {
                    $import=true;
                    $errorMessage='';
                    $i=1;
                    $duplicateRow=1;

                    foreach($retailerResult as $keyArray){
                        $keyArray=$keyArray->toArray();
                        $keyno=$keyArray['mobile'];
                        $keyaadhar=$keyArray['aadhar_number'];
                        $keycode=$keyArray['retailer_code'];
                        $noMatch=0;
                        $aadharMatch=0;
                        $codeMatch=0;
                        $duplicateRecord=1;
                        if($import){
                            foreach($retailerResult as $totalArray){
                                $totalArray=$totalArray->toArray();
                                if($keyArray['mobile']!=''){
                                    if($keyArray['mobile']==$totalArray['mobile']){
                                        $noMatch=$noMatch+1;
                                        if($noMatch>1){
                                            $errorMessage.='Duplicate mobile no exist at this line'.$duplicateRecord." and ".$duplicateRow."\r\n";
                                            $import = false;
                                        }
                                    }
                                }

                                if($keyArray['aadhar_number']!=''){
                                    if($keyArray['aadhar_number']==$totalArray['aadhar_number']){
                                        $aadharMatch=$aadharMatch+1;
                                        if($aadharMatch>1){
                                            $errorMessage.='Duplicate Aadhar no exist at this line'.$duplicateRecord." and ".$duplicateRow."\r\n";
                                            $import = false;
                                        }
                                    }
                                }
                                if($keyArray['retailer_code']!=''){
                                    if($keyArray['retailer_code']==$totalArray['retailer_code']){ $codeMatch=$codeMatch+1;
                                        if($codeMatch>1){
                                            $errorMessage.='Duplicate retailer_code no exist at this line'.$duplicateRecord." and ".$duplicateRow."\r\n";
                                            $import = false;
                                        }
                                    }
                                }
                                $duplicateRecord++;
                            }
                        }
                        $duplicateRow++;
                    }
                    if($import){
                        foreach ($retailerResult as $details) {
                            $detailsArray = $details->toArray();
                            $count=$i-1;

                            $recordResult[$count] = $this->retailerObj->recordResult($detailsArray);

                            $errorMessage.=$i++;
                            $errorMessage.=' ';
                            if(array_key_exists('error',$recordResult[$count])){
                                $errorMessage.=$recordResult[$count]['error'];
                            }else{
                                $errorMessage.='correct';
                            }
                            $errorMessage.="\r\n";
                            if($import){
                                if(array_key_exists('error',$recordResult[$count])){
                                    $import=false;
                                }
                            }

                        }
                    }

                    if($import){
                        foreach ($recordResult as $retailerDetails)
                        {
                            $i = 0;
                            $emptyData='';
                            $bstart='';
                            $bend='';
                            $smartphone=strtolower($retailerDetails['smart_phone'])=='yes'?1:0;
                            $internet_availability=strtolower($retailerDetails['internet_availability'])=='yes'?1:0;

                            /*if($retailerDetails['business_start_time']!=''){

                                $bstart=array_key_exists('date', $retailerDetails['business_start_time']);
                                if($bstart){
                                    $data=json_decode(json_encode($retailerDetails['business_start_time']));
                                    $bstart=substr($data->date,11,8);
                                }
                            }else{
                                $bstart='';
                            }
                            if($retailerDetails['business_end_time']!=''){

                                $bend=array_key_exists('date', $retailerDetails['business_end_time']);

                                if($bend){
                                    $data=json_decode(json_encode($retailerDetails['business_end_time']));
                                    $bend=substr($data->date,11,8);
                                }
                            }else{
                                $bend='';
                            }
                            $retailerDetails['new_bstart']=$bstart;
                            $retailerDetails['new_bend']=$bend;*/
                            $retailerDetails['new_network']=$internet_availability;
                            $retailerDetails['new_smartphone']=$smartphone;
                            $retailerDetails['firstname']=$retailerDetails['name'];
                            $registerObj=new RegistrationModel();                  

                            if($retailerDetails['createCustomer']==1){
                                $result=$registerObj->address($retailerDetails['shop_name'],
                                $retailerDetails['segment_id'],$emptyData,
                                $retailerDetails['address'],$emptyData,$retailerDetails['locality'],$retailerDetails['landmark'],$retailerDetails['city'],$retailerDetails['pin_code'],
                                $retailerDetails['name'],$emptyData,
                                $retailerDetails['mobile'],$emptyData,$emptyData,$retailerDetails['latitude'],
                                $retailerDetails['longitude'],$emptyData,
                                $emptyData,$emptyData,$emptyData,$emptyData,
                                $retailerDetails['new_bstart'],$retailerDetails['new_bend'],
                                $retailerDetails['state_id'],$retailerDetails['no_of_shutters'],
                                $retailerDetails['volume_class'],$emptyData,$emptyData,$emptyData,
                                $emptyData,$retailerDetails['name'],$emptyData,$retailerDetails['area'],$emptyData,$smartphone,
                                $internet_availability,$emptyData,
                                $retailerDetails['beat_id'],$retailerDetails['customer_id'],
                                $retailerDetails['gstin'],$retailerDetails['fssai'],$emptyData,0,0,0,0,0,0,'',0,0,$retailerDetails['aadhar_number']);
                                $finalmessage=$result['message'];

                            }else{
                                $updateArray['_token'] = Session::token();
                                $updateArray['_Token'] = Session::token();
                                $updateArray['legalEntityId']=$retailerDetails['legal_entity_id'];
                                $updateArray['retailer_name']=$retailerDetails['shop_name'];
                                $updateArray['legal_entity_type_id']=$retailerDetails['customer_id'];
                                $updateArray['business_type_id']=$retailerDetails['segment_id'];
                                $updateArray['volume']=$retailerDetails['volume_class'];
                                $updateArray['shutters']=$retailerDetails['no_of_shutters'];
                                $updateArray['business_start_time']=$retailerDetails['new_bstart'];
                                $updateArray['business_end_time']=$retailerDetails['new_bend'];
                                $updateArray['smartphone']=$retailerDetails['new_smartphone'];
                                if($retailerDetails['new_network']==1){
                                    $updateArray['network']='on';
                                }
                                $updateArray['latitude']=$retailerDetails['latitude'];
                                $updateArray['longitude']=$retailerDetails['longitude'];
                                $updateArray['gstin']=$retailerDetails['gstin'];
                                $updateArray['fssai']=$retailerDetails['fssai'];
                                $updateArray['org_address1']=$retailerDetails['address'];
                                $updateArray['org_pincode']=$retailerDetails['pin_code'];
                                $updateArray['ret_id']=$retailerDetails['legal_entity_id'];
                                $updateArray['hub_id']=$registerObj->getHub($retailerDetails['beat_id']);
                                $updateArray['spoke_id']=$this->retailerObj->getSpoke($retailerDetails['beat_id']);
                                $updateArray['beat']=$retailerDetails['beat_id'];
                                $updateArray['area_id']=$retailerDetails['area_id'];
                                $updateArray['locality']=$retailerDetails['locality'];
                                $updateArray['landmark']=$retailerDetails['landmark'];
                                $updateArray['org_state']=$retailerDetails['state_id'];
                                $updateArray['org_city']=$retailerDetails['city'];
                                $updateArray['org_country']=99;
                                $updateArray['aadhar_number']=$retailerDetails['aadhar_number'];
                                $updateArray['mobile_no']=$retailerDetails['mobile'];
                                $updateArray['name']=$retailerDetails['name'];
                                
                                $data=$this->update($updateArray);
                                $data=json_decode($data);
                                $finalmessage=$data->message;
                                if($data->status == 400){
                                    $finalmessage = "First ".$i." customers are uploaded successfully!";
                                    return Response::json(array('message' => $finalmessage));
                                }
                              //  echo 'final';exit;
                            }
                            $i++;

                        }
                    }
                    else{
                        $timeStamp=md5(microtime(true));
                        $txtFileName="Retailer-Import-Status-".$timeStamp.'.txt';
                        $filePath='download'. DIRECTORY_SEPARATOR . 'pricing_log' . DIRECTORY_SEPARATOR . $txtFileName;
                        $file=fopen($filePath,"w+");
                        fwrite($file,$errorMessage);
                        fclose($file);
                        $productRepo = new ProductRepo();
                        $url = $productRepo->uploadToS3($filePath,'inventory',2);
                        $finalmessage= "Please check the file for Details!".'<a href='.$url.'>View Details </a>';
                    }

                }
            });
            return Response::json(array('message' => $finalmessage));
            //return redirect('retailers/index');
        } catch (\ErrorException $ex)
        {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function sendSms(Request $request)
    {
        try
        {
            $tokens = array();
            $platformId = 5004;
            $returnResponse = '';
            $minimumFields = 1;
            $data = Input::all();
            $state = 1;
            $ishex = 0;
            $dcs = 0;
            $results = $this->retailerObj->filterData($request, $this->produc_grid_field_db_match, $minimumFields);
            $message = isset($data['sms_message']) ? $data['sms_message'] : '';
            //$this->pushNotifications($message);
            if(ctype_xdigit($message))
            {
                $state = 4;
                $ishex = 1;
                $dcs = 245;
            }
            $retailersResult = isset($results['Records']) ? $results['Records'] : [];
            $retailersResultCount = isset($results['totalCustomerCount']) ? $results['totalCustomerCount'] : [];
            if($retailersResultCount > 0 && !empty($retailersResult))
            {
                $retailersData = array_chunk($retailersResult, 10);
                foreach($retailersData as $retailerDetails)
                {
                    $temp = json_decode((json_encode($retailerDetails)), true);
                    $leWhIds = array_column($temp, 'legal_entity_id');
                    $numbers = array_column($temp, 'mobile_no');
                    $customerRepo = new CustomerRepo();
                    $userIds = DB::table('users')->whereIn('legal_entity_id',$leWhIds)->where('is_active',1)->pluck('user_id')->all();
                    $subs_users = DB::table('user_preferences')->whereIn('user_id',$userIds)->where('sms_subscription',1)->pluck('user_id')->all();
                    $count = count($subs_users);
                    $numbers = DB::table('users')->whereIn('user_id',$subs_users)->pluck('mobile_no')->all();
                    $RegId = DB::table('device_details')->select('registration_id','platform_id')->whereIn('user_id',$subs_users)->get()->all();
                    $tokenDetails = json_decode((json_encode($RegId)), true);

                    $temp = $customerRepo->sendSMS(0, 0, $numbers, $message, $state, $ishex, $dcs);
                }   
                //$productRepo = new ProductRepo();            
                //$productRepo->pushNotifications($message,$tokenDetails);
                if($count == $retailersResultCount)
                    $returnResponse = 'SMS sent to '.$retailersResultCount.' retailers.';
                else
                    $returnResponse = 'SMS sent to '.$count.' active retailers out of '.$retailersResultCount.' retailers.';
            }else{
                $returnResponse = 'No records found';
            }
        } catch (\ErrorException $ex) {
            $returnResponse = $ex->getMessage();
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return redirect('retailers/index')->withFlashMessage($returnResponse);
        }
        return redirect('retailers/index')->withFlashMessage($returnResponse);
    }
    
    public function editUser($userId, Request $request)
    {
        try{
            if($userId > 0)
            {
                $users = new Users();
                $userInfo = $users->where(['user_id' => $userId])->first();
//                return View::make('Retailer::userInfo')
//                        ->with('userData', $userInfo)
//                        ->render();
                return json_encode($userInfo);
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    public function editOrder($orderId,Request $request){
        if(!empty($orderId)){
            $orderInfo = $this->retailerObj->getOrderInfo($orderId);
            if(!Empty($orderInfo)){
                $orderInfo['gds_order_id'] = $orderInfo[0]->gds_order_id;
                $orderInfo['order_code'] = $orderInfo[0]->order_code;
                $orderInfo['gds_order_status'] = $orderInfo[0]->master_lookup_name;
                $orderInfo['order_status_id'] = $orderInfo[0]->order_status_id;
                $orderInfo['cust_le_id'] = $orderInfo[0]->cust_le_id;
                $orderInfo['user_id'] = $orderInfo[0]->user_id;
            }
            return $orderInfo;
        }
    }
    public function addcashback()
    {
        DB::beginTransaction();
        try{
            $data = Input::all();
            if($data['cashback'] <= 0 || (is_numeric($data['cashback']) != 1)){
                return 3;
            }
            $result = $this->retailerObj->updateCashback($data);
            DB::commit();
            return $result;
        }catch(\Exception $ex){
            DB::rollback();
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            $message = $ex->getMessage();
            return 4;
        }
    }
    public function getcashback()
    {
        $data = Input::all();
        $result = $this->retailerObj->getCashback($data);
        return $result;
    }
    
    public function updateUser()
    {
        try
        {
            $status = 0;
            $message = '';
            $data = Input::all();      
            $userId = isset($data['user_id']) ? $data['user_id'] : 0;
            if($userId > 0)
            {
                $users = new Users();
                $updateArray['firstname'] = isset($data['firstname']) ? $data['firstname'] : '';
                $updateArray['lastname'] = isset($data['lastname']) ? $data['lastname'] : '';
                $updateArray['mobile_no'] = isset($data['mobile_no']) ? $data['mobile_no'] : '';
                $updateArray['aadhar_id'] = isset($data['aadhar_id']) ? $data['aadhar_id'] : '';
                $updateArray['is_active'] = (isset($data['user_is_active']) && $data['user_is_active'] == 'on') ? 1 : 0;
                if(isset($data['password']) && $data['password'] != '')
                {
                    $updateArray['password'] = isset($data['password']) ? md5($data['password']) : '';
                }
                $updateArray['updated_by'] = Session::get('userId');
//                Log::info($updateArray);
                $users->where('user_id', $userId)->update($updateArray);
                $status = 1;
                $message = 'Updated Successfully';
                $legalEntityId = isset($data['legal_entity_id']) ? $data['legal_entity_id'] : 0;
                if($legalEntityId > 0){
                    $this->retailerObj->updateFlatTable($legalEntityId);
                }
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            $message = $ex->getMessage();
        }
        return json_encode(['status' => $status, 'message' => $message]);
    }
    
    public function getSelfOrdersPlaced(){

        $data = \Input::all();
        $fromDate = date('Y-m-d');
        $datetime = new \DateTime('tomorrow');
        $toDate = $datetime->format('Y-m-d');
        if(!empty($data))
        {
            $filterDate = isset($data['filter_date']) ? $data['filter_date'] : '';
            if($filterDate != '')
            {
                switch($filterDate)
                {
                    case 'wtd':
                        $currentWeekSunday = strtotime("last sunday");
                        $sunday = date('w', $currentWeekSunday)==date('w') ? $currentWeekSunday + 7*86400 : $currentWeekSunday;
                        $lastSunday = date("Y-m-d",$sunday);
                        $fromDate = $lastSunday;
                        break;
                    case 'mtd':
                        $fromDate = date('Y-m-01');
                        break;
                    case 'ytd':
                        $fromDate = date('Y-01-01');
                        break;
                    default:
                        break;
                }
            }
        }
        $reports = new ReportsRepo();
        $result = $reports->getSelfOrders($fromDate, $toDate);
        return $result;
    }
    
    public function creditDetails($Id) {
        try {
            
            $creditDetails = $this->retailerObj->getUserCreditDetails($Id);
            //echo '<pre/>';print_r($creditDetails);die;
            $status = isset($creditDetails->approval_status)?$creditDetails->approval_status:'';
            $approval_flow_func = new CommonApprovalFlowFunctionModel();
            if($status=='' || $status==0){
                $status=57197;
            }
            $module = 'Credit Limit';
            $res_approval_flow_func = $approval_flow_func->getApprovalFlowDetails($module, $status, \Session::get('userId'));
            $approvalOptions = array();
            $approvalVal = array();
            if (isset($res_approval_flow_func["currentStatusId"]) && isset($res_approval_flow_func["data"])) {
                foreach ($res_approval_flow_func["data"] as $options) {
                    $approvalOptions[$options['nextStatusId'] . ',' . $options['isFinalStep']] = $options['condition'];
                }
            }
            //$approvalOptions['57146,0,58076'] = 'Comment';
            $approvalVal = array('current_status' => $status,
                'approval_unique_id' => $Id,
                'approval_module' => $module,
                'table_name' => 'user_ecash_creditlimit',
                'unique_column' => 'user_ecash_id',
                'approvalurl' => '/po/approvalSubmit',
            );
            return View('Retailer::creditdetails')
                    ->with('creditDetails', $creditDetails)
                    ->with('approvalOptions', $approvalOptions)
                    ->with('approvalVal', $approvalVal);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    public function updateCreditLimit() {
        try {
            $data = Input::all();
            $legalEntityId = $data['legal_entity_id'];
            $creditlimit = $data['creditlimit'];
            // Code to Update User Credit Limit 
            $creditlimitEditAccess = $this->roleAccessObj->checkPermissionByFeatureCode("LOCE001");
            if($creditlimitEditAccess){
                $creditlimit = (isset($data['creditlimit']) and !empty($data['creditlimit'])) ? $data['creditlimit'] : -1;
                if($creditlimit >= 0){
                    $userdata = DB::table('users as u')
                        ->leftJoin('user_ecash_creditlimit as e','u.user_id','=','e.user_id')
                        ->where('u.legal_entity_id',$legalEntityId)
                        ->where('u.is_parent',1)
                        ->select('u.user_id','e.user_ecash_id','e.pre_approve_limit','e.creditlimit','e.approval_status')->first();
                    $user_id = isset($userdata->user_id)?$userdata->user_id:0;
                    $cur_approval_status = $userdata->approval_status;
                    $pre_approve_limit = $userdata->pre_approve_limit;
                    $dbcreditlimit = $userdata->creditlimit;                        
                    if($userdata->user_ecash_id=='' && $user_id>0){
                        DB::table('user_ecash_creditlimit')
                            ->insert([
                                'user_id' => $user_id
                                ]);
                    }
                    $check_limit = ($cur_approval_status==1)?$dbcreditlimit:$pre_approve_limit;
                    $created_by = \Session::get('userId');
                    //echo $creditlimit.'==='.$check_limit;die;
                    if($check_limit!=$creditlimit){
                        $approval_flow_func = new CommonApprovalFlowFunctionModel();
                        $res_approval_flow_func = $approval_flow_func->getApprovalFlowDetails('Credit Limit', '57197', $created_by);
                        $current_status_id = 0;
                        $next_status_id = 0;
                        if (isset($res_approval_flow_func["currentStatusId"]) && isset($res_approval_flow_func["data"])) {
                            $current_status_id = $res_approval_flow_func["currentStatusId"];
                            $next_status_id = $res_approval_flow_func["data"][0]["nextStatusId"];
                        }
                        if($next_status_id!=0 && $next_status_id!=57199){
                            DB::table('user_ecash_creditlimit as e')
                                ->leftJoin('users as u','u.user_id','=','e.user_id')
                                ->where('u.legal_entity_id',$legalEntityId)
                                ->where('u.is_parent',1)
                                ->update([
                                    'e.pre_approve_limit' => $creditlimit,
                                    'e.approval_status' => ($next_status_id!=0)?$next_status_id:57197,
                                    'e.approved_at' => date("Y-m-d H:i:s"),
                                    'e.approved_by' => Session::get('userId'),
                                    'e.updated_by' => Session::get('userId'),
                                    'e.updated_at' => date("Y-m-d H:i:s"),
                                ]);
                        }
                        if (isset($res_approval_flow_func["currentStatusId"]) && isset($res_approval_flow_func["data"])) {
                            $current_status_id = $res_approval_flow_func["currentStatusId"];
                            $next_status_id = $res_approval_flow_func["data"][0]["nextStatusId"];
                            $appr_comment = (isset($data['approval_comments']))?$data['approval_comments']:'Credit Limit Review Request from INR '.$check_limit.' to INR '.$creditlimit.'.';
                            $approval_flow_func->storeWorkFlowHistory('Credit Limit', $user_id, $current_status_id, $next_status_id, $appr_comment, $created_by);
                        }
                    }
                }
            }
            $status = 200;
            $message = "Successfully updated";
            $result = ['status' => $status, 'message' => $message];
            return json_encode($result);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function mfcGridDetails(){
       $getId =  Session::get('custRt_Id');

      $gridData = $this->retailerObj->gridData($getId);

      return  json_encode($gridData);
    }

    public function mappingMfcDetails(Request $request){

      $rt_Id = Session::get('custRt_Id');

      $data = $request->all();

      $records = $this->retailerObj->saveDetailsIntoMappingTable($data,$rt_Id);

      return json_encode(array('status'=>$records));
    }

    public function editDetails($id){
        $editData = $this->retailerObj->getGridEditData($id);  
        $data = json_decode(json_encode($editData), true);
        return $data[0];
    }
    public function mfcBussinessNamesDropDown(){
        $businessNames = $this->retailerObj->businessNames();
        $businessNames = json_decode(json_encode($businessNames), true);
        $html_entities ="";
        foreach ($businessNames as $bussiness_value) {
            $html_entities.= "<option value ='".$bussiness_value['legal_entity_id']."'>".$bussiness_value['business_legal_name']."</option>";
        }
        return $html_entities;
    }

    public function updateDatailsSave(Request $request){
        try{
         $update = $this->retailerObj->updateLenderData($request);
         return json_encode(array('status'=>$update));
     }catch(\ErrorException $ex) {
            Log::error($ex->getMessage());
            return Response::json(array('status' => 500));
   }
  }

    public function uploadCreditlimit(Request $request){
           try{
            DB::beginTransaction();
            $file_data      =  Input::file('creditlimit_data');
            $file_extension  = $file_data->getClientOriginalExtension();
            $msg = "";

            if( $file_extension != 'xlsx'){
                $msg .= "Please upload valid file";

            }elseif(Input::hasFile('creditlimit_data')){
                    $path                           = Input::file('creditlimit_data')->getRealPath();
                    $data                           = $this->readExcelForStocktransfer($path);
                    $file_data                      = Input::file('creditlimit_data');
                    $results                        = json_decode(json_encode($data['prod_data']), true);
                    $headers                        = json_decode(json_encode($data['cat_data']), true);
                    $headers1                       = array('MFC Code','Retailer Code','Credit Limit','Aadhar Number');
                    $recordDiff                     = array_diff($headers,$headers1);

                    // if ($headers != $headers1) {
                    //     $msg .=  "Please upload valid file";
                    //     return $msg;
                    //     die;
                    // }

                    // if(empty($result)){
                    //     $msg .=  "Please upload valid data";
                    //     return $msg;
                    //     die;
                    // }
                    $responseFlag=1;
                    $errorMessage='';
                    foreach ($results as $result){
                        $response = $this->retailerObj->checkLeId($result);
                        $responseArray[]=$response;
                        if($responseFlag==1){
                            if($response=='correct'){
                                $responseFlag=1;
                            }else{
                                $responseFlag=0;
                            }
                        }                    
                    }
                   // echo $responseFlag;
                    if($responseFlag==0){
                        for($i=0;$i<count($responseArray);$i++){
                            $errorMessage.=$i+1;
                            $errorMessage.=' ';
                            $errorMessage.=$responseArray[$i];
                            $errorMessage.="\r\n";
                        }
                       // print_r($responseArray);                       

                        $timestamp = md5(microtime(true));
                        $txtFileName = 'MFC-Import-Status-' . $timestamp . '.txt';
                        $file_path = 'download' . DIRECTORY_SEPARATOR . 'pricing_log' . DIRECTORY_SEPARATOR . $txtFileName;
                        $file = fopen($file_path, "w+")  ;
                        fwrite($file, $errorMessage);
                        fclose($file);
                        $productRepo = new ProductRepo();
                        $ImportUrl = $productRepo->uploadToS3($file_path,'inventory',2);               
                        return  "Please check the file for Details!".'<a href='.$ImportUrl.' target="_blank"> View Details </a>';
                    }
                    else if($responseFlag==1){
                        foreach ($results as $result){
                            $response = $this->retailerObj->insertLeId($result);
                        }
                        DB::commit();
                        return 'Updated Successfully';
                    }
                }
            }catch(\ErrorException $ex) {
            DB::rollback();
            Log::error($ex->getTraceAsString());
            Log::error($ex->getMessage());
            return "Failed to upload creditlimit!";

         }


    }

    // read excel file

    public function readExcelForStocktransfer($path) {
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
            Redirect::to('/')->send();
        }
    }


    public function downloadCreditLimitTemplate(Request $request){
         try{
            $headers = array('MFC Code','Retailer Code','Credit Limit', 'Aadhar Number');
            $mytime = Carbon::now();
            Excel::create('Credit Limit Template Sheet-'.$mytime->toDateTimeString(), function($excel) use($headers) 
            {
                $excel->sheet("Credit Limit", function($sheet) use($headers)
                {
                    $sheet->loadView('Retailer::downloadCreditLimitemplate', array('headers' => $headers)); 
                });
            })->export('xlsx');

        }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            Redirect::to('/')->send();
        }
    }
    public function getBeatDataPincode(){
        $data=Input::all();
        $pincode=isset($data['pincode'])?$data['pincode']:0;
        if($pincode!=0){
            $beatData=$this->retailerObj->getBeatDataForPincode($pincode);
        }
        $beatDataCode='<option value="">'."Select Beat".'</option>'; 
        for($i=0; $i< count($beatData);$i++){
            $beatDataCode=$beatDataCode.'<option value='.$beatData[$i]->pjp_pincode_area_id.' selected="">'.$beatData[$i]->pjp_name.'</option>';
        }
        //echo $beatDataCode;exit;
        return $beatDataCode;
    }

    public function validateaadharno(){
        try{
            $response = [ "valid" => false ];
            $data = Input::all();
            $userId = isset($data['user_id']) ? $data['user_id'] : '';
            $aadharId = isset($data['aadhar_id']) ? $data['aadhar_id'] : '';
            if($aadharId != '')
            {
                if ($userId != '') {
                    $isAadharAvailable = DB::table('users')->where([['aadhar_id',$aadharId],['is_active',1],['user_id','<>',$userId]])->count();
                }
                else{
                $isAadharAvailable = DB::table('users')->where([['aadhar_id',$aadharId],['is_active',1]])->count();
                }
            }
            if ($isAadharAvailable == 0)
                $response = [ "valid" => true ];
            return json_encode($response);

        }
        catch (\ErrorException $ex) {
            \Log::error($ex->getMessage() . ' ' . $ex->getTraceAsString());
        }

    }

    public function downloadImportTemplate(){
        try{
            $headers =  array('Retailer Code','Aadhar Number','Shop Name','Customer Type','Segment Type','Name','Mobile','GSTIN','FSSAI','Volume Class','No of shutters','Business Start Time','Business End Time','Address','Area','Beat','City','State','PIN Code','Smart Phone','Internet Availability','Latitude','Longitude','Locality','Landmark');

            $headers_second_page=array('Customer Type','Segment Type','Volume Class');
            $customerdata=$this->roleAccessObj->getMasterLookupData('Customer Types');
            $segmentData=$this->roleAccessObj->getMasterLookupData('Business Segments'); 
            $volumeclassdata=$this->roleAccessObj->getMasterLookupData('Volume Classes');
            $second_page_data=array();
            $customerdata=json_encode($customerdata,true);
            $customerdata=json_decode($customerdata,1);

            $segmentData=json_encode($segmentData,true);
            $segmentData=json_decode($segmentData,1);
            $volumeclassdata=json_encode($volumeclassdata,true);
            $volumeclassdata=json_decode($volumeclassdata,1);
            $counter=0;
            foreach($segmentData as $val){
                $second_page_data[$counter]['customer_type']=isset($customerdata[$counter])?$customerdata[$counter]['master_lookup_name']:'';
                 $second_page_data[$counter]['segment_type']=isset($segmentData[$counter])?$segmentData[$counter]['master_lookup_name']:'';                 
                $second_page_data[$counter]['volume_class']=isset($volumeclassdata[$counter])?$volumeclassdata[$counter]['master_lookup_name']:'';
               // $second_page_data[$counter]['pincode_data']=isset($addressData[$counter])?$addressData[$counter]['data']:'';
                $counter++;
            }
            $mytime = Carbon::now();

            Excel::create('Import Retailer Template-'.$mytime->toDateTimeString(),function($excel) use ($headers,$headers_second_page, $second_page_data){
                $excel->sheet("Import Retailer",function($sheet) use ($headers){
                    $sheet->loadView('Retailer::downloadCreditLimitemplate',array('headers' => $headers));
                });
                $excel->sheet("reference",function($sheet) use ($headers_second_page,$second_page_data){
                    $sheet->loadView('Retailer::importretailer',array('headers' => $headers_second_page,'data' => $second_page_data));
                });
            })->export('xlsx');

        }catch(\ErrorException $ex){
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            Redirect::to('/')->send();
        }
    }
    public function creditLimitDonwload(Request $request){
        try {
        // $fromDate = date('Y-m-d 00:00:00', strtotime($request->get('creditlimit_date_from')));
        // $toDate = date('Y-m-d 23:59:59', strtotime($request->get('creditlimit_date_to')));
        $details = json_decode(json_encode($this->retailerObj->generateCreditLimitReport()), true);
        Excel::create('Creditlimit Report - '. date('Y-m-d'),function($excel) use($details) {
            $excel->sheet('Creditlimit Report', function($sheet) use($details) {          
            $sheet->fromArray($details);
            });      
        })->export('xls');
           
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getfeedback(Request $request,$legalEntityId)
    {
        $check=$request;
        $makeFinalSql = array();
        $filter = $request->input('%24filter');
        if( $filter == ''){
            $filter = $request->input('$filter');
        }
        $this->objCommonGrid=new commonIgridController();

        //make sql for Business legal name 
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("legal_entity_id",$filter,false);
        $fieldQuery =str_replace('legal_entity_id', 'getBusinessLegalName(legal_entity_id)', $fieldQuery);
        if($fieldQuery != ''){
            $makeFinalSql[] = $fieldQuery;
        }
        //make sql for Feedback Group
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("feedback_group_type",$filter,false);
        $fieldQuery =str_replace('feedback_group_type', 'getMastLookupValue(feedback_group_type)', $fieldQuery);
        if($fieldQuery != ''){
            $makeFinalSql[] = $fieldQuery;
        }
         //make sql for Feedback Type
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("feedback_type",$filter,false);
        $fieldQuery =str_replace('feedback_type', 'getMastLookupValue(feedback_type)', $fieldQuery);
        if($fieldQuery != ''){
            $makeFinalSql[] = $fieldQuery;
        }
         //make sql for Comments
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("comments",$filter,false);
        if($fieldQuery != ''){
            $makeFinalSql[] = $fieldQuery;
        }
        //make sql for Created At
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("created_at",$filter,true);
        if($fieldQuery != ''){
            $makeFinalSql[] = $fieldQuery;
        }
        
        //make sql for Created By 
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("created_by",$filter,false);
        $fieldQuery =str_replace('created_by', 'GetUserName (created_by, 2)', $fieldQuery);
        if($fieldQuery != ''){
            $makeFinalSql[] = $fieldQuery;
        }
         //make sql for Media
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("picture",$filter,false);
        $fieldQuery =str_replace('picture', 'IF(picture != "","Link1","")', $fieldQuery);
        if($fieldQuery != ''){
            $makeFinalSql[] = $fieldQuery;
        }
         //make sql for Audio
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("audio",$filter,false);
        $fieldQuery =str_replace('audio', 'IF(audio != "","Link2","")', $fieldQuery);
        if($fieldQuery != ''){
            $makeFinalSql[] = $fieldQuery;
        }
        //Arrange data for sorting
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

        $result = $this->retailerObj->getfeedback($makeFinalSql,$orderBy,$page,$pageSize,$legalEntityId);
        $DeletefeedbackPermission = $this->roleAccessObj->checkPermissionByFeatureCode("RETF02");
        try {
            $i = 0;
            foreach ($result['results'] as $record) {
                $record->created_at=date("d-m-Y H:i:s", strtotime($record->created_at));
                $fidrecord = $result['results'][$i]->fid;
                $actions = '';
                $link1='';
                $link2='';
                $actions.='<a class="" href="/retailers/feedbackview/' . $fidrecord . '"> <i class="fa fa-eye"></i></a>&nbsp;';
                if($DeletefeedbackPermission)
                $actions.= '<span class="actionsStyle" ><a onclick="deletefeedbackrecord('.$fidrecord.')"</a><i class="fa fa-trash-o"></i></span>';
                if(!empty($record->picture))
                $link1="<a href=".$record->picture." target=_blank>Link1</a>";
                if(!empty($record->audio))
                $link2="<a href=".$record->audio." target=_blank>Link2</a>";
                $j=$i++;
                $result['results'][$j]->picture=$link1;
                $result['results'][$j]->audio=$link2;
                $result['results'][$j]->actions = $actions;
            }
            return $result;
        } catch (Exception $e) {
            Log::error($e->getMessage()." ".$e->getTraceAsString());
            return ["Records" => [], "TotalRecordsCount" => 0];
        }
    }
    
    public function addfeedback(){
        $data = Input::all();
        $productRepo = new ProductRepo();
        if(isset($_FILES['feedbackimage'])){  
           $feedback_pic_move = $data['legal_entity_id']."_".date("Y-m-d-H-i-s")."_". $_FILES['feedbackimage']['name'];
           $feedback_pic_path="uploads/feedback/picture/".$feedback_pic_move;
           move_uploaded_file($_FILES['feedbackimage']['tmp_name'], $feedback_pic_path);
           $feedback_pic=$productRepo->uploadToS3($feedback_pic_path,'feedback',2);
           unlink($feedback_pic_path);
        }else{
            $feedback_pic ='';

        }
        if(isset($_FILES['feedbackaudio'])){

          $feedback_audio_move = $data['legal_entity_id']."_".date("Y-m-d-H-i-s")."_". $_FILES['feedbackaudio']['name'];
          $feedback_audio_path="uploads/feedback/audio/".$feedback_audio_move;
           move_uploaded_file($_FILES['feedbackaudio']['tmp_name'], $feedback_audio_path);
           $feedback_audio=$productRepo->uploadToS3($feedback_audio_path,'feedback',2);
           unlink($feedback_audio_path);
        }else{
           $feedback_audio ='';
        }    

        $result['status'] = false; 
        $result['data'] = $data;
        if($data == []) return $result;
        // Adding New Record in the Table
       $result['status'] = $this->retailerObj->addnewfeedback($data,$feedback_pic,$feedback_audio);  
       return $result;
    }

    public function viewfeedback($id)
    {
        try {
            if (!Session::has('userId')) {
                Redirect::to('/login')->send();
            }
            if($id != null){
                $data = $this->retailerObj->getSingleRecord($id);
                if(!empty($data)){
                    $result['status'] = true;
                    $result['legal_entity_id'] = $data[0]->legal_entity_id;
                    $result['feedback_group_type'] = $data[0]->feedback_group_type;
                    $result['feedback_type'] = $data[0]->feedback_type;
                    $result['comments'] = $data[0]->comments;
                    $result['picture'] = $data[0]->picture;
                    $result['audio'] = $data[0]->audio;
                    $result['created_at'] = $data[0]->created_at;
                    $result['created_by'] = $data[0]->created_by;
                }
                return View::make('Retailer::feedbackview')
                        ->with('result',$result);
            }
            return ["status"=>false];
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }    
    }

    public function deletefeedback($id)
    {
        $status = false;
        if($id < 0 or $id != null)
            $status = $this->retailerObj->deleteSingleRecord($id);
        return ["status" => $status];
    }

    public function groupspecific($id){
        $data = $this->retailerObj->feedbacktype($id);
        return array('status'=>true,'message'=>true,'data'=>$data);
    }

    public function validatefssai($id){
        try{

            $response = [ "valid" => false ];
            $data = Input::all();
            $le_id = isset($id) ? $id : '';
            $fssaino = isset($data['fssai']) ? $data['fssai'] : '';
            if($fssaino != '')
            {
                if ($le_id != '') {
                    $isFssaiAvailable = DB::table('retailer_flat')->where([['fssai',$fssaino],['legal_entity_id','<>',$le_id]])->count();
                }else{
                    $isFssaiAvailable = DB::table('retailer_flat')->where(['fssai',$fssaino])->count();
                }
            }
            if ($isFssaiAvailable == 0)
                $response = [ "valid" => true ];
            return json_encode($response);

        }
        catch (\ErrorException $ex) {
            \Log::error($ex->getMessage() . ' ' . $ex->getTraceAsString());
        }

    }

    public function uploadDoc(Request $request){
        $data = $request->all();
        $legalID = $data['le_id'];
        $documentType = isset($data['documentType']) ? $data['documentType'] : '';
        $docName = DB::select(DB::raw("select description,master_lookup_name from master_lookup where value = ".$documentType.""));
        $master_doc_name = $docName[0]->master_lookup_name;
        $docName = $docName[0]->description;
        $url = "";
        $EntityType="products";
        $type=1;
        if ($request->hasFile('upload_file')) {
            $extension = Input::file('upload_file')->getClientOriginalExtension();
            if(!in_array($extension, array('pdf', 'doc', 'docx', 'png', 'jpg', 'jpeg','jfif','PNG','JPG','JPEG'))) {
                return Response::json(array('status'=>400, 'message'=>Lang::get('inward.alertExtension')));
            }
            $imageObj = $request->file('upload_file');
            $productRepo = new ProductRepo();
            $url = $productRepo->uploadToS3($imageObj,$EntityType,$type);
                if($url!='') {
                    $docsArr = array(
                        'legal_entity_id'=>$legalID,
                        'doc_name'=>$docName,
                        'doc_url'=>$url,
                        'doc_type'=>$master_doc_name,
                        'created_by'=>Session('userId'), 
                        'created_at'=>date('Y-m-d H:i:s')
                    );
                    $doc_id=$this->retailerObj->upLoadPathInToDB($docsArr);
                    Session::push('ledocs_'.$legalID, $doc_id);
                    $docTypes = $this->retailerObj->getDocumentTypes();
                    $userInfo = $this->retailerObj->getLoginUserInfo();
                    $firstname = isset($userInfo->firstname)?$userInfo->firstname:'';
                    $lastname = isset($userInfo->lastname)?$userInfo->lastname:'';
                    $createdBy = $firstname.' '.$lastname;
                    
                    $docType = (isset($docTypes[$docsArr['doc_type']]))?$docTypes[$docsArr['doc_type']]:'';
                    $docText='<tr>
                            <td><input type="hidden" name="docs[]" value="'.$doc_id.'">'.$docType.'</td>
                            <td>'.$docsArr['doc_name'].'</td>
                            <td>'.$createdBy.'</td>
                            <td>'.$docsArr['created_at'].'</td>
                            <td align="center"><a href="'.$url.'" target="_blank"><i class="fa fa-download"></i></a></td>
                            <td align="center">
                            <a class="delete le-del-doc" id="'.$doc_id.'" href="javascript:void(0);"><i class="fa fa-remove"></i></a>
                            </td>
                        </tr>';
                    return Response::json(array('status'=>200, 'message'=>Lang::get('inward.successUploaded'),'docText'=>$docText,'doc_type' => $documentType));
                }
            }
            else {
                return Response::json(array('status'=>200, 'message'=>Lang::get('salesorders.errorInputData')));
            }
    }

    public function deleteDoc(){
        try {
            $id = Input::get('id');
            $docArr = $this->retailerObj->getDocumentById($id);
            $leId = isset($docArr->legal_entity_id) ? $docArr->legal_entity_id : '';
            $filename = isset($docArr->doc_url) ? $docArr->doc_url : '';
            if(!empty($filename) && file_exists(public_path().'/'.$filename)) {
                unlink(public_path().'/'.$filename);
            }

            $this->retailerObj->deleteRecord($id);
            $sessioncheck = Session::get('ledocs_'.$leId) ;
                if(is_array($sessioncheck)) {    
                    Session::put('ledocs_'.$leId, array_diff(Session::get('ledocs_'.$leId), [$id]));
            }
            return Response::json(array('status'=>200, 'message'=>Lang::get('inward.successDelete'),'doc_type' => $docArr->doc_type));
        } catch (Exception $e) {
            return Response::json(array('status'=>200, 'message'=>'Failed'));
        }
    }

    public function checkGstStateCode(Request $request) {
        $status = (\Utility::check_gst_state_code($request->gstin_number)) ? true : false;
        return json_encode(array('valid' => $status));
    }
}
