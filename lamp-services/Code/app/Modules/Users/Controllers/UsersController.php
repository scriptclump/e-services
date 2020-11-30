<?php
namespace App\Modules\Users\Controllers;
use View;
use Session;
use Validator;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\BaseController;
use URL;
use Log;
use Excel;
use Illuminate\Http\Request;
use Redirect;
use \App\models\channels\channels;
use \App\Modules\Seller\Models\Sellers;
use \App\Modules\Users\Models\Users;
use \App\Central\Repositories\CustomerRepo;
use \App\Central\Repositories\RoleRepo;
use \App\Central\Repositories\GlobalRepo;
use Illuminate\Support\Facades\DB;
use \App\Modules\Reports\Controllers\ReportsController;
use App\Modules\Orders\Controllers\OrdersGridController;
use \App\Modules\Inventory\Models\Inventory;

Class UsersController extends BaseController {
    public $roleAccess;
    protected $user_grid_fields;
    
    public function __construct(RoleRepo $roleAccess, CustomerRepo $custRepoObj, Request $request) {
        parent::__construct();
        $this->middleware(function ($request, $next) {
            if (!Session::has('userId')) {
                return Redirect::to('/');
            }
            return $next($request);
        });

        $global = new GlobalRepo();

        $global->logRequest($request);
        $this->_orders = new OrdersGridController();
        $this->_Inventory = new Inventory();
        $this->reports = new ReportsController;
        $this->user_group=new Users();
        $this->roleAccess = $roleAccess;
        $this->custRepoObj = $custRepoObj;
        $this->user_grid_fields = [
                'rolename' => 'roles.name',
                'user_id' => 'users.user_id',
                'profile_picture' => 'users.profile_picture',
                'firstname' => 'users.firstname',
                'lastname' => 'users.lastname',
                'email_id' => 'users.email_id',
                'reporting_manager' => 'reporting_manager',
                'is_active' => 'users.is_active',
                'mobile_no' => 'users.mobile_no',
                'emp_code' => 'users.emp_code'
            ];
    }
    /**
     * [usersList Get Users list]
     * @return [view] [Redirect to users page]
     */
    public function usersList() {
        try {
            parent::Breadcrumbs(array('Home' => '/', 'Administration' => '#', 'Users' => '/users/index'));
            $addPermission = $this->roleAccess->checkPermissionByFeatureCode('USR002');
            $excelExportPermission = $this->roleAccess->checkPermissionByFeatureCode('USR005');
            $redeemPermission = $this->roleAccess->checkPermissionByFeatureCode('USRFF01');
            $roles = $this->roleAccess->getRole(1);
            $userId = Session::get('userId');
            $bu_id = $this->reports->getaccessbuids($userId);
            parent::Title(trans('dashboard.dashboard_title.company_name')." - ".trans('users.users_title.index_page_title'));
            return view('Users::index')->with(array(
                'addPermission' => $addPermission,
                'redeemPermission' => $redeemPermission,
                'roles' => $roles,
                'excelExportPermission' => $excelExportPermission,
                'bu_id' => $bu_id));
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    /**
     * [odersTabGetBuUnit To get Bu list in hierarchical order]
     * @return [array] [Business units list]
     */
    public function odersTabGetBuUnit(){
        $option[] = "<option value='0' class='bu1' >All Users</option>";
        $businessTreeData = $this->_Inventory->businessTreeData();
        $businessTreeData = array_merge($option,$businessTreeData);
        return $businessTreeData;
    }
    /**
     * [usersGrid To get User grid list]
     * @param  Request $request [ig grid related i/p like order by,page & page size]
     * @return [array]           [users list & count of users list]
     */
   public function usersGrid(Request $request) {
    try{
            $data = Input::all();
            $usersDisplayListTab = isset($data['showTab'])?$data['showTab']:"activeUsersTab";
            $userId = Session::get('userId');
            $filters = array();
            $orderby_array  = "";
            $full_results= array();
            $i = 0;
            $totalrecords=0;
            
            if(isset($data['$filter']))
            {
                $filters = $this->filterData($data['$filter']);
            }
             
            if (isset($data['$orderby'])) { //checking for sorting
                $orderby_array = explode(' ', $request->input('$orderby'));                        
            }
            $offset = (int)$request->input('page');
            $perpage = $request->input('pageSize');
            $bu_id[] = $data['bu_id'];
            $global[]= 0;
            $parentids = $this->roleAccess->getParentBU($bu_id);
            $allbuids = array_unique(array_merge($parentids,$global));
            $results = $this->roleAccess->getUsersList($orderby_array, $filters, 0,$this->user_grid_fields, $usersDisplayListTab, $offset, $perpage,$allbuids);
            $totalResults = $this->roleAccess->getUsersList($orderby_array, $filters, 1,$this->user_grid_fields, $usersDisplayListTab, $offset, $perpage,$allbuids);
             if(!empty($results))
            {
                $full_results = isset($results["results"]) ? $results["results"] : [];
                $user_access = isset($results["user_access"]) ? $results["user_access"] : 0;
                $totalrecords=isset($totalResults['results']) ? $totalResults['results'] : 0;
            }

            $editPermission = $this->roleAccess->checkPermissionByFeatureCode('USR003',$user_access);
            $switchPermission = $this->roleAccess->checkPermissionByFeatureCode('USR006');
            $DeletePermission = $this->roleAccess->checkPermissionByFeatureCode('USR004',$user_access);
            $activePermission = $this->roleAccess->checkPermissionByFeatureCode('USRA01',$user_access);
            $impersonatepermission=$this->roleAccess->checkPermissionByFeatureCode('USRIMP1');
            $full_results = json_decode(json_encode($full_results),1);
            if(!empty($full_results))
            {
                foreach ($full_results as $result){
                    $profilePictureLink = '';
                    $profilePicture = isset($result['profile_picture'])?$result['profile_picture']:'';
                    if ($profilePicture != '') {
                        $profileRootPath = '';
                        if (strpos($profilePicture, 'www') !== false || strpos($profilePicture, 'http') !== false) {
                            $profilePictureLink = '<img src="' . $profilePicture . '" class="img-circle" style="height: 50px; width: 50px;" />';
                        } else {
                            $profilePictureLink = '<img src="' . URL::to('/') . '/' . $profilePicture . '" class="img-circle" style="height: 50px; width: 50px;" />';
                        }
                    } else {
                        $profilePictureLink = '<img src="' . URL::to('/') . '/img/avatar5.png" class="img-circle"  style="height: 50px; width: 50px;" />';
                    }

                    $full_results[$i]['profile_picture'] = $profilePictureLink;
                    $full_results[$i]['email_id'] = trim($result['email_id']);

                    if($result['user_id'] != Session::get('userId'))
                    {
                        // The Active Permission is a Feature, which allows the User to Active / Inactive any User
                        if($activePermission){
                            $activeStatus='<label class="switch" style="float:right;"><input class="switch-input block_users" type="checkbox" ';
                            $activeStatus.=($full_results[$i]['is_active'] == "Active") ? 'checked="true" ' : 'check="false" ';
                            $activeStatus.='name="'.$full_results[$i]['user_id'].'" id="'.$full_results[$i]['user_id'].'" value="'.$full_results[$i]['user_id'].'" ><span class="switch-label" data-on="Yes" data-off="No"></span><span class="switch-handle"></span></label>';
                            $full_results[$i]['is_active'] = $activeStatus;
                        }
                    }else{
                        $full_results[$i]['is_active'] = '';
                    }
                    $impersonate = '';
                    if ($impersonatepermission) {                       
                        if ($result['user_id'] != Session::get('userId')) {
                            $impersonate .= '<span style="padding-left:20px;" <a onclick="impersonateusers('.$result['user_id'].')"><i class="fa fa-user-plus"></i></a></span>';
                        }
                    }
                    $actions = '';
                    if ($editPermission) {                        
                        if ($result['user_id'] != Session::get('userId')) {
                            $actions .= '<span style="padding-left:20px;" ><a href="/users/editusers/' . $this->roleAccess->encodeData($result['user_id']) . '"><i class="fa fa-pencil"></i></span>';
                        }
                    }
                    if ($DeletePermission) {
                        if ($result['user_id'] != Session::get('userId'))
                        {
                            if($result['is_active'] == 0 and $result['is_disabled'] == 1)
                                $actions .= '<span style="padding-left:20px;" ><a href="javascript:void(0)" onclick="deleteEntityType('. $result['user_id'] .',\'refresh\')"><i class="glyphicon glyphicon-refresh"></i></a></span>';
                            else
                                $actions .= '<span style="padding-left:20px;" ><a href="javascript:void(0)" onclick="deleteEntityType('. $result['user_id'] .',\'delete\')"><i class="fa fa-trash-o"></i></a></span>';
                        }
                    }
                    if ($switchPermission) {
                        $actions .= '<span style="padding-left:20px;" ><a href="javascript:void(0);" onclick="switchUser(' . $result['user_id'] . ')"><i class="fa fa-exchange"></i></span>';
                    }
                    $full_results[$i]['impersonate'] = $impersonate;
                    $full_results[$i]['actions'] = $actions;
                    $i++;
                }
            }  
        } catch (ErrorException $ex) {
            Log::error($ex->getMessage() . ' ' . $ex->getTraceAsString());
        }
        return json_encode(array(
            "results" => $full_results,
            "TotalRecordsCount" => $totalrecords,
        ));
       
            
    }

   /*Filtering for Users Grid*/
   private function filterData($filter) {
        try {
           
            $stringArr = explode(' and ', $filter);
            $filterDataArr = array();
            if (is_array($stringArr)) {
                foreach ($stringArr as $data) {
                    $dataArr = explode(' ', $data);
                    $sup=explode(' ge ', $data);
                    if (substr_count($data, 'full_name') && !array_key_exists('full_name', $filterDataArr)) {
                        $sup = explode(' ge ', $data);
                        $fulnm = strpos($data, 'eq');
                        $fullNameValArr = str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'full_name','eq '), '', $sup[0]);
                        $value = ($fulnm>0) ? trim($fullNameValArr,' ') : '%'.trim($fullNameValArr,' ').'%';
                        $operator = ($fulnm>0) ? '=' : 'LIKE';
                        $filterDataArr['full_name'] = array('operator' => $operator, 'value' => $value);
                    }
                    if (substr_count($data, 'rolename') && !array_key_exists('rolename', $filterDataArr)) {
                        $sup = explode(' ge ', $data);
                        $role = strpos($data, 'eq');
                        $roleValArr = str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'rolename','eq '), '', $sup[0]);
                        $value = ($role>0) ? trim($roleValArr,' ') : '%'.trim($roleValArr,' ').'%';
                        $operator = ($role>0) ? '=' : 'LIKE';
                        $filterDataArr['rolename'] = array('operator' => $operator, 'value' => $value);
                    }
                    if (substr_count($data, 'reporting_manager') && !array_key_exists('reporting_manager', $filterDataArr)) {
                        $reportingManagerValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'reporting_manager'), '', $data));
                        $value = (isset($reportingManagerValArr[1]) && $reportingManagerValArr[1] == 'eq' && isset($reportingManagerValArr[2])) ? $reportingManagerValArr[2] : '%'.$reportingManagerValArr[0].'%';
                        $operator = (isset($reportingManagerValArr[1]) && $reportingManagerValArr[1] == 'eq') ? '=' : 'LIKE';
                        $filterDataArr['reporting_manager'] = array('operator' => $operator, 'value' => $value);
                    }
                    if (substr_count($data, 'email_id') && !array_key_exists('email_id', $filterDataArr)) {
                        $sup = explode(' ge ', $data);
                        $email = strpos($data, 'eq');
                        $emailIdValArr = str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'email_id','eq '), '', $sup[0]);
                        $value = ($email>0) ? trim($emailIdValArr,' ') : '%'.trim($emailIdValArr,' ').'%';
                        $operator = ($email>0) ? '=' : 'LIKE';
                        $filterDataArr['email_id'] = array('operator' => $operator, 'value' => $value);
                    }
                    if (substr_count($data, 'mobile_no') && !array_key_exists('mobile_no', $filterDataArr)) {
                        $sup = explode('ge', $data);
                        $mblno = strpos($data, 'eq');
                        $mobileNoValArr = str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'mobile_no','eq '), '', $sup[0]);
                        $value = ($mblno>0) ? trim($mobileNoValArr,' ') : '%'.trim($mobileNoValArr,' ').'%';
                        $operator = ($mblno>0) ? '=' : 'LIKE';
                        $filterDataArr['mobile_no'] = array('operator' => $operator, 'value' => $value);
                    }
                    if (substr_count($data, 'emp_code') && !array_key_exists('emp_code', $filterDataArr)) {
                        $empCodeValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'emp_code'), '', $data));
                        $value = (isset($empCodeValArr[1]) && $empCodeValArr[1] == 'eq' && isset($empCodeValArr[2])) ? $empCodeValArr[2] : '%'.$empCodeValArr[0].'%';
                        $operator = (isset($empCodeValArr[1]) && $empCodeValArr[1] == 'eq') ? '=' : 'LIKE';
                        $filterDataArr['emp_code'] = array('operator' => $operator, 'value' => $value);
                    }
                    if (substr_count($data, 'otp') && !array_key_exists('otp', $filterDataArr)) {
                        $otpValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'otp'), '', $data));
                        $value = (isset($otpValArr[1]) && $otpValArr[1] == 'eq' && isset($otpValArr[2])) ? $otpValArr[2] : '%'.$otpValArr[0].'%';
                        $operator = (isset($otpValArr[1]) && $otpValArr[1] == 'eq') ? '=' : 'LIKE';
                        $filterDataArr['otp'] = array('operator' => $operator, 'value' => $value);
                    }               
                    
                                                        
                    }
                }
                return $filterDataArr;
            } catch (Exception $e) {
                Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            }
        }
       /**
        * [usersCount To get the users account]
        * @param  Request $request [business unit id]
        * @return [array]           [Total, active, Inactive users account]
        */
       public function usersCount(Request $request)
       {
        try 
        {
            $data = Input::all();
            $userId = Session::get('userId');
            $bu_id[] = $data['bu_id'];
            $global[]= 0;
            $parentids = $this->roleAccess->getParentBU($bu_id);
            $allbuids = array_unique(array_merge($parentids,$global));
            $allUsers     = $this->roleAccess->getUsersCount('All',$allbuids);
            $activeUsers  = $this->roleAccess->getUsersCount('Active',$allbuids);
            $inactiveUsers= $this->roleAccess->getUsersCount('Inactive',$allbuids);
            return  json_encode(array(
                "TotalRecordsCount"=>$allUsers,
                "activeUsersCount" => $activeUsers,
                "inActiveUsersCount" => $inactiveUsers,
               
            ));


        }
         catch (\Exception $ex) {
                Log::error($ex->getMessage());
                Log::error($ex->getTraceAsString());
                //return json_encode($result);
         }
       }

   
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
     * [blockUser To inactivate user]
     * @return [array] [Array with status(true/false) whether the process is success/failed]
     */
    public function blockUser() {
        try {
            $userId = Input::get('userId');
            $status = Input::get('status');
            $result['modal'] = false;
            $childUsers = $this->roleAccess->getAllChildIdsByUser($userId);
            if($childUsers != null and $status == 0)
            {
                // If there are children....
                $userRole = $this->roleAccess->getRolebyUserId($userId);
                $userLevelUsers = [];
                foreach ($userRole as $role)
                    $userLevelUsers = array_merge($userLevelUsers,$this->roleAccess->getUsersByRole($role->name));
                $optionText = '<input type="hidden" name="oldUserId" id="oldUserId" value="'.$userId.'"><select name="newUserToAssign" id="newUserToAssign" class="form-control">';
                foreach ($userLevelUsers as $user){
                    if($user->user_id != $userId)
                        $optionText.='<option value="'.$user->user_id.'">'.$user->username.' - '.$user->name.'</option>';
                }
                $optionText.= '</select>';
                # The Same Level Role Users are now in the userLevelUsers.
                $result['modal'] = true;
                $result['userLevelUsers'] = $optionText;
                return json_encode($result);
            }
            return $this->roleAccess->inactiveUser($userId, $status);   // To Block Single User Only...
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return json_encode($result);
        }
    }
    /**
     * [deleteUser To Delete user]
     * @return [array] [Array with status(true/false) whether the process is success/failed]
     */
    public function deleteUser()
    {
        $userId = Input::get('userId');
        $deleteType = Input::get('deleteType');
        $result['modal'] = false;
        $childUsers = $this->roleAccess->getAllChildIdsByUser($userId);
        // Log::info("Child Users ");
        // Log::info($childUsers);
        if($childUsers != null)
        {
            // If there are children....
            $userRole = $this->roleAccess->getRolebyUserId($userId);
            $userLevelUsers = [];
            foreach ($userRole as $role)
                $userLevelUsers = array_merge($userLevelUsers,$this->roleAccess->getUsersByRole($role->name));
            $optionText = '<input type="hidden" name="oldUserId" id="oldUserId" value="'.$userId.'"><select name="newUserToAssign" id="newUserToAssign" class="form-control">';
            foreach ($userLevelUsers as $user)
            {
                if($user->user_id != $userId)
                    $optionText.='<option value="'.$user->user_id.'">'.$user->username.' - '.$user->name.'</option>';
            }
            $optionText.= '</select>';
            # The Same Level Role Users are now in the userLevelUsers.
            $result['modal'] = true;
            $result['userLevelUsers'] = $optionText;
            return json_encode($result);
        }
        return $this->roleAccess->deleteUser($userId,$deleteType);   // To Block Single User Only...
    }
    /**
     * [assignChildUserToParentUser To update reporting manager]
     * @return [array] [Array with status(true/false) whether the process is success/failed]
     */
    public function assignChildUserToParentUser()
    {   
        $data = Input::all();
        $newUserId = isset($data['userId'])?$data['userId']:'';
        $oldUserId = isset($data['oldUserId'])?$data['oldUserId']:'';
        $user = new Users();
        $status = $user->updateReportingManagerByUserId($newUserId,$oldUserId);
        $result['status'] = $status;
        return json_encode($result);
    }
    /**
     * [exportUsers Export users list]
     * @return [excel file]           [Downloads excel file]
     */
    public function exportUsers(Request $request)
    {
        try{
            $excelarr = (new Users)->makeExportUsersExcelDownload();
            $newexcelarr = json_decode(json_encode($excelarr), true);
            $headers = isset($newexcelarr[0])?array_keys($newexcelarr[0]):null;

            $filename = "Users_".date("d-m-Y");
            Excel::create($filename, function($excel) use($headers,$newexcelarr) {
                $excel->sheet('Users', function($sheet) use($headers,$newexcelarr){
                    $sheet->loadView('Users::exportCommissionSheet', array('headers' => $headers, 'data' => $newexcelarr)); 
                });
            })->download('xls');

        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    /**
     * [validateEmail validate email]
     * @return [array] [sends status whether the email is valid or not]
     */
    public function validateEmail() {
        try {
            $response = [ "valid" => false ];
            $data = Input::all();
            $userId = isset($data['user_id']) ? $data['user_id'] : '';
            $emailId = isset($data['email_id']) ? $data['email_id'] : '';
            if($emailId != '')
            {
                if ($userId != '') {
                    $isEmailAvailble = DB::table('users')->where([['email_id',$emailId],['is_active',1],['user_id','<>',$userId]])->count();
                } else {
                    $isEmailAvailble = DB::table('users')->where([['email_id',$emailId],['is_active',1]])->count();
                }
                if($isEmailAvailble == 0)
                    $response = [ "valid" => true ];
            }
        } catch (\ErrorException $ex) {
            \Log::error($ex->getMessage() . ' ' . $ex->getTraceAsString());
        }
        return json_encode($response);
    }
    /**
     * [validateMobileno validate mobile no]
     * @return [array] [sends status whether the mobile no is valid or not]
     */
    public function validateMobileno() {
        try {
            $response = [ "valid" => false ];
            $data = Input::all();
            $userId = isset($data['user_id']) ? $data['user_id'] : '';
            $mobileNo = isset($data['mobile_no']) ? $data['mobile_no'] : '';
            if($mobileNo != '')
            {
                if ($userId != '') {
                    $isMobileAvailable = DB::table('users')->where([['mobile_no',$mobileNo],['is_active',1],['user_id','<>',$userId]])->count();
                } else {
                    $isMobileAvailable = DB::table('users')->where([['mobile_no',$mobileNo],['is_active',1]])->count();
                }
                if ($isMobileAvailable == 0)
                    $response = [ "valid" => true ];
            }
        } catch (\ErrorException $ex) {
            \Log::error($ex->getMessage() . ' ' . $ex->getTraceAsString());
        }
        return json_encode($response);
    }
    /**
     * [applyRedeem To redeem amount of ff]
     * @return [array] [sends status whether the transaction is success or not]
     */
    public function applyRedeem()
    {
        $data = Input::all();
        $user_id = isset($data['user_id'])?$data['user_id']:-1;
        $amount = isset($data['amount'])?floatval($data['amount']):0;
        $message = isset($data['message'])?$data['message']:'';
        
        if($amount != 0 or $amount != '' or $amount != null or $message != '' or $message != null or $user_id != -1 or $user_id != '' or $user_id != null)
        {
            // As the Redeem Can be done only for FF, the legal Entity Id is 2 By Default
            $users = new Users();
            $status = $users->updateEcashTransactionHistory($user_id,2,$amount,$message);
            return json_encode(["status" => $status]); 
        }
        return json_encode(["status" => 0]); 
    }
    /**
     * [importRedeem To redeem ff's amount using excel]
     * @param  Request $request [file]
     * @return [redirects]           [redirects to users view]
     */
    public function importRedeem(Request $request)
    {
        $file = Input::file('importFile');
        $path = $file->getRealPath();
        $extension = $file->getExtension();
        if(!$file->isFile())
            return redirect('users/index')->withFlashMessage('ERROR: File is Invalid. Please Check');
        
        Excel::load($path, function($reader) {
            $importedData = $reader->all();
            if(!empty($importedData))
                foreach ($importedData as $record) {
                    $user_id = isset($record["user_id"])?intval($record["user_id"]):-1;
                    $commission = isset($record["commission"])?floatval($record["commission"]):0;
                    $amount = isset($record["redeem_amount"])?floatval($record["redeem_amount"]):0;
                    $message = isset($record["message"])?$record["message"]:'';
                    if($amount != 0 or $amount != '' or $amount != null or $user_id != -1 or $user_id != '' or $user_id != null){
                        if($commission > $amount){
                            // As the Redeem Can be done only for FF, the legal Entity Id is 2 By Default
                            $users = new Users();
                            $status = $users->updateEcashTransactionHistory($user_id,2,$amount,$message);
                        }
                    }
                }
        });
        return redirect('users/index')->withFlashMessage('Import Done');
    }
    /**
     * [exportRedeem export redeem data]
     * @param  Request $request [from date & to date]
     * @return [redirects]           [Redirects to users view]
     */
    public function exportRedeem(Request $request)
    {
        $data = Input::all();
        $invalidFromDate = $invalidToDate = false;
        if(isset($data["fromDate"]) and $data["fromDate"] != null){
            $dateExplode = explode("/",$data["fromDate"]);
            if (!checkdate($dateExplode[0],$dateExplode[1],$dateExplode[2]))
                $invalidFromDate = true;
        }
        else
            $invalidFromDate = true;

        if(isset($data["toDate"]) and $data["toDate"] != null){
            $dateExplode = explode("/",$data["toDate"]);
            if (!checkdate($dateExplode[0],$dateExplode[1],$dateExplode[2]))
                $invalidToDate = true;
        }
        else
            $invalidToDate = true;

        if($invalidFromDate)
            return redirect('users/index')->withFlashMessage("ERROR: From Date is Invalid");
        if($invalidToDate)
            return redirect('users/index')->withFlashMessage("ERROR: To Date is Invalid");

        $fromDate = explode("/",$data["fromDate"]);
        $fromDate = $fromDate[2].'-'.$fromDate[0].'-'.$fromDate[1]." 00:00:00";
        
        $toDate = explode("/",$data["toDate"]);
        $toDate = $toDate[2].'-'.$toDate[0].'-'.$toDate[1]." 23:59:59";

        $users = new Users();
        $excelData = $users->getRedeemExportData($fromDate,$toDate);
        $excelData = json_decode(json_encode($excelData),true);
        $headers = isset($excelData[0])?array_keys($excelData[0]):null;
        
        if(empty($headers))
            return redirect('users/index')->withFlashMessage('No Data Availabe in between the Selected Dates. Try Again');
        
        array_push($headers, "Redeem Amount", "Message");
        if($excelData != null or $excelData != "" or $excelData != []){
            Excel::create("Commission_Report_".date('Y-m-d H:i:s'), function($excel) use($headers,$excelData) {
                $excel->sheet("Commission", function($sheet) use($headers, $excelData)
                {
                    $sheet->loadView('Users::exportCommissionSheet', array('headers' => $headers, 'data' => $excelData)); 
                });
            })->download('xls');
        }
        return redirect('users/index')->withFlashMessage('Redeem Exported Done');
    }
    /**
     * [addUsers To navigate to add user view]
     */
    public function addUsers() {
        try {
            $breadCrumbs = array('Home' => url('/'), 'Administration' => '#', 'Users' => url('users/index'), 'Add User' => '#');
            parent::Breadcrumbs($breadCrumbs);
            parent::Title(trans('dashboard.dashboard_title.company_name')." - ".trans('users.users_title.add_user_page_title'));
            $roles = $this->roleAccess->getRole(1);
            $reportingMangers = $this->roleAccess->getReportingMangers(0);
            $getDesignations = $this->roleAccess->getMasterLookupData('Designations');
            $getDepartments = $this->roleAccess->getMasterLookupData('Departments');
            $businessUnit = new Users();
            $getBusinessUnitCollection = $businessUnit->getBusinessUnitList();
            $buCollection = $businessUnit->getBusinessUnits();
            $getCategoriesCollection = $businessUnit->getCategoryList();
            $getBrandsCollection = $businessUnit->getBrandsList();

            $userGroups=$this->user_group->getUsersGroupList();
            $users = new Users();
            $businessUnitsData=$users->getBusinesUnitData();

            return view('Users::addusers')
                    ->with(['roles' => $roles, 
                        'getDepartments' => $getDepartments,
                        'getDesignations' => $getDesignations,
                        'reportingMangers' => $reportingMangers,
                        'getBusinessUnitCollection' => $getBusinessUnitCollection,
                        'getBrandsCollection' => $getBrandsCollection,
                        'getCategoriesCollection' => $getCategoriesCollection,
                        'buCollection' => $buCollection, 
                        'userGroupData'=> $userGroups,
                        'businessUnitsData' => $businessUnitsData
                        ]);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    /**
     * [getReportingManagers To get reporting managers list on the basis of role]
     * @return [array] [Reporting managers list]
     */
    public function getReportingManagers() {
        try
        {
            $data = Input::all();
            $users = new Users();
            return $users->getManagersList($data);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    /**
     * [getChannels get channel]
     * @return [array] [array with channelimages]
     */
    public function getChannels() {
        try {
            $data = new Users();
            $channels = $data->getChannels();
            $channelImages = '';
            $i = 0;
            foreach ($channels as $channel) {
                $img = url($channel->mp_logo);
                $channelImages = '<img src="' . $img . '" class="img-circle" style ="height:35px;margin-left: -3px;">' .
                        '&nbsp;&nbsp;&nbsp;' . ucfirst($channel->mp_name);
                $channels[$i]->channelimage = $channelImages;
                $i++;
            }
            return json_encode(array('result' => $channels));
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    /**
     * [getCompanys To get legalentity list]
     * @return [array] [legal entity list]
     */
    public function getCompanys() {
        try {
            $companys = new Users();
            $results = $companys->getCompanys();
            $i = 0;
            foreach ($results as $result) {
                $logo = '';
                if ($result->logo != '') {
                    if (preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $result->logo))
                        $logo = $result->logo;
                    else
                        $logo = URL::to('/') . $result->logo;
                } else {
                    $logo = URL::to('/') . $result->logo;
                }
                $bp = $logo;
                $date = date('d/m/Y', strtotime($result->created_at));
                $profile_pic = '<img src="' . $bp . '" class="img-circle">';
                $results[$i]->logo = $profile_pic;
                $results[$i]->created_at = $date;
                $results[$i]->Checked = true;
                $i++;
            }
            return json_encode(array('Result' => $results));
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    /**
     * [getProducts To get products]
     * @return [array] [Products list]
     */
    public function getProducts() {
        try {
            $companys = new Users();
            $results = $companys->getProducts();
            $i = 0;
            foreach ($results as $result) {
                $bp = url('uploads/profile_picture/' . $result->primary_image);
                $profile_pic = '<img src="' . $bp . '" class="img-circle">';
                $results[$i]->primary_image = $profile_pic;
                $i++;
            }
            return json_encode(array('Result' => $results));
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    /**
     * [getCategories Get categories list]
     * @return [array] [categories list]
     */
    public function getCategories() {
        try {
            $companys = new Users();
            $results = $companys->getCategories();
            return json_encode(array('Result' => $results));
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    /**
     * [getBusinessUnit To get bu list]
     * @param  [businessId] $businessId [bu id]
     * @return [array]             [bu list]
     */
    public function getBusinessUnit($businessId = null) {
        try {
            $data = Input::all();
            $businessUnit = new Users();            
            $results = $businessUnit->getBusinessUnit($data, $businessId);
            return $results;
//            $i = 0;
//            foreach ($results as $result) {
//                $date = date('d/m/Y', strtotime($result->created_at));
//                $results[$i]->created_at = $date;
//                $i++;
//            }
//            if ($results) {
//                return json_encode(array('Result' => $results));
//            } else {
//                echo '{"Result":[]}';
//                exit;
//            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    /**
     * [saveUser To save user]
     * @return [array] [with status,message & user id]
     */
    public function saveUser() {
        try {
            $messages = 'Oops. Something went wrong. Please try again.';
            $status = 0;
            $userId = 0;
            if (isset($data['user_id']) && $data['user_id'] == '') {
                $validator = Validator::make(
                            array(
                        'firstname' => Input::get('firstname'),
                        'lastname' => Input::get('lastname'),
                        'email_id' => Input::get('email_id'),
                        'password' => Input::get('password'),
                        'confirm_password' => Input::get('confirm_password'),
                        'mobile_no' => Input::get('mobile_no')
                            ), array(
                        'firstname' => 'required',
                        'lastname' => 'required',
                        'email_id' => 'required',
                        'password' => 'required',
                        'confirm_password' => 'required|same:password',
                        'mobile_no' => 'numeric|digits:10'
                            )
                );
                        // 'email_id' => 'required|email|unique:users',
            }else{
                $userId = Input::get('user_id');
                $validator = Validator::make(
                            array(
                        'firstname' => Input::get('firstname'),
                        'lastname' => Input::get('lastname'),
                        'email_id' => Input::get('email_id'),
                        'password' => Input::get('password'),
                        'confirm_password' => Input::get('confirm_password'),
                        'mobile_no' => Input::get('mobile_no')
                            ), array(
                        'firstname' => 'required',
                        'lastname' => 'required',
                        'email_id' => 'required',
                        'password' => 'required',
                        'confirm_password' => 'required|same:password',
                        'mobile_no' => 'numeric|digits:10'
                            )
                );
                    // |email|unique:users,email_id,' . $userId . ',user_id
            }            
            if ($validator->fails()) {
                $failureMessages = $validator->messages();
                $messageArr = json_decode($failureMessages);
                foreach($messageArr as $msg)
                {
                    $messages = $messages . (isset($msg[0]) ? $msg[0] : ''). '  ';
                }
            } else {
                $data = Input::get();
                $data['legal_entity_id'] = Session::get('legal_entity_id');
                $data['created_by'] = Session::get('userId');
                $data['created_at'] = date('Y-m-d H:i:s');
                $roleId = $data['role_id'];
                $password = $data['password']; //str_random(20);
                $data['password'] = md5($password);
                unset($data['confirm_password']);
                unset($data['role_id']);                
                if (isset($data['is_active']))
                    $data['is_active'] = 1;
                else
                    $data['is_active'] = 0;

                if (isset($data['_token'])) {
                    unset($data['_token']);
                }
                if(isset($data['legal_entity_bu'])){
                    $data['legal_entity_id']=$data['legal_entity_bu'];
                    unset($data['legal_entity_bu']);
                }
                $users = new Users();
                if (isset($data['user_id']) && $data['user_id'] > 0)
                {
                    unset($data['email_id']);
                    $userId = $users->saveUser($data, $data['user_id']);
                    $userFirstName = isset($data['firstname']) ? $data['firstname'] : '';
                    $userLastName = isset($data['lastname']) ? $data['lastname'] : '';
                    $userName = $userFirstName . ' ' . $userLastName;
                    $messages = 'User Updated Sucessfully';
//                    @\Notifications::addNotification(['note_code' => 'USR002', 'note_params' => ['USERNAME' => $userName]]);
                }else{
                    unset($data['user_id']);
                    $userId = $users->saveUser($data);

                    $group=$users->saveUserGroupData($userId,isset($data['user_group'])?$data['user_group']:0);

                    $messages = 'New User Created Sucessfully';
                    $userFirstName = isset($data['firstname']) ? $data['firstname'] : '';
                    $userLastName = isset($data['lastname']) ? $data['lastname'] : '';
                    $userName = $userFirstName . ' ' . $userLastName;
//                    @\Notifications::addNotification(['note_code' => 'USR001', 'note_params' => ['USERNAME' => $userName]]);                    
                }

                if (is_numeric($userId)) {

                    $this->roleAccess->mapRole($userId, $roleId);

                    $status = 1;
                }

                //===============================Mail to User =======================================
                /* if ($userId == 0) {

                  $template = EmailTemplate::where('Code', 'ET1000')->get();
                  $emailVariable = array('firstName' => $data['firstname'], 'lastName' => $data['lastname'], 'user_name' => $data['email'], 'password' => $password);
                  Mail::send(array('html' => 'emails.welcome_newuser'), $emailVariable, function($msg) use ($template, $data) {
                  $msg->from($template[0]->From, 'ebutor')->to($data['email'])->subject($template[0]->Subject);
                  });
                  } */
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        return json_encode(array('status' => $status, 'message' => $messages, 'user_id' => $userId));
    }
    /**
     * [updateUser To update user]
     * @return [array] [status and user id]
     */
    public function updateUser() {
        try {
            $status = 0;
            $userId = -1;
            $data = Input::get();
            $userGroup=isset($data['user_group'])?$data['user_group']:0;
            $data['updated_by'] = Session::get('userId');
            $data['updated_at'] = date('Y-m-d H:i:s');
            $roleId = $data['role_id'];
            $password = $data['password']; //str_random(20);
            $assignTempRolePermission = $this->roleAccess->checkPermissionByFeatureCode("USRTEMP1");
            if($assignTempRolePermission){
                //Temporary Roles (optional):
                $temp_role['roles'] = isset($data['temp_role_id'])?$data['temp_role_id']:NULL;
                $temp_role['date'] = (!empty($data['temp_role_expiry_date']))?$data['temp_role_expiry_date']:date("Y-m-d", strtotime("+7 day"));            
            }
            if ($password != '') {
                $data['password'] = md5($password);
            } else {
                unset($data['password']);
            }
            if (isset($data['_token'])) {
                unset($data['_token']);
            }
            if(isset($data['legal_entity_bu'])){
                $data['legal_entity_id']=$data['legal_entity_bu'];
                unset($data['legal_entity_bu']);

            }
            $userId = isset($data['user_id']) ? $data['user_id'] : 0;
            unset($data['confirm_password']);
            unset($data['role_id']);
            unset($data['ecash']);
            unset($data['temp_role_id']);
            unset($data['temp_role_expiry_date']);
            unset($data['user_group']);

            $users = new Users();
            $userId = $users->saveUser($data, $userId);

            $delGroup=$this->user_group->delUserGroupData($userId); 
            $group=$this->user_group->saveUserGroupData($userId,$userGroup);
            

            if (is_numeric($userId)) {
                $this->roleAccess->mapRole($userId, $roleId);
                $status = 1;
            }
            // Assigning Temp Roles after mapping the main roles
            if($assignTempRolePermission)
                $users->saveTempRoles($temp_role, $userId);
            @\Notifications::addNotification(['note_code' => 'USR002']);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        return json_encode(array('status' => $status, 'user_id' => $userId));
    }
    /**
     * [editUsers To get the user details to  redirect to edit user page]
     * @param  [int] $userId [user id]
     * @return [view]         [redirects to edit user view]
     */
    public function editUsers($userId) {
        try {
            if (is_numeric($userId)) {
                return redirect('users/index')->withFlashMessage('Invalid user id');
                exit;
            }
            $userId = $this->roleAccess->decodeData($userId);
            $breadCrumbs = array('Home' => url('/'), 'Administration' => '#', 'Users' => url('users/index'), 'Edit User' => '#');
            parent::Breadcrumbs($breadCrumbs);            
            $userData = [];
            $users = new Users();
            $userInfo = $users->where(['user_id' => $userId])->first();
            if (!empty($userInfo)) {
                $userData = $userInfo->toArray();
            }
            $firstName = isset($userData['firstname']) ? $userData['firstname'] : '';
            $lastName = isset($userData['lastname']) ? $userData['lastname'] : '';
            $name = '';
            if($lastName != '')
            {
                $name = $firstName.' '.$lastName;
            }else{
                $name = $firstName;
            }
            if($name != '')
            {
                parent::Title(trans('dashboard.dashboard_title.company_name')." - ".trans('users.users_title.edit_user_page_title')." (".$name.")");
            }else{
                parent::Title(trans('dashboard.dashboard_title.company_name')." - ".trans('users.users_title.edit_user_page_title'));
            }
            $userData['firstname'] = $firstName;
            $userData['lastname'] = $lastName;
            $userData['role_id'] = '';
            $userData['ecash'] = $users->getEcashAmount($userId);
            $userData['legal_entity_id'] = $users->getLegalEntityIdByUserId($userId);
            if (!empty($userData)) {
                $roleId = DB::table('user_roles')
                        ->where(['user_id' => $userId])
                        ->pluck(DB::raw('group_concat(role_id) as role_id'))->all();
                $userData['role_id'] = isset($roleId[0]) ? $roleId[0] : '';
            }
            if(!empty($userData)){
                $groupId=DB::table('chat_user_groups')
                            ->where (['user_id'=>$userId])
                            ->pluck(DB::raw('group_concat(group_id) as group_id'))->all();
                $userData['group_id']=isset($groupId[0])?$groupId[0]:'';

            }
            $customer = DB::table('retailer_flat')->where('mobile_no',$userData['mobile_no'])->get()->all();
            if(!empty($customer))
                $customer = 1;
            else
                $customer = 0;
            $roles = $this->roleAccess->getRole(1);
            $reportingMangers = $this->roleAccess->getReportingMangers($userId);
            $userPermissionsCollection = $this->custRepoObj->getUserPermissions($userId);
            $userPermissions = [];
            if (!empty($userPermissionsCollection)) {
                foreach ($userPermissionsCollection as $userPermission) {
                    $userPermissions[$userPermission->name] = $userPermission->object_id;
                }
            }
            $getDesignations = $this->roleAccess->getMasterLookupData('Designations');
            $getDepartments = $this->roleAccess->getMasterLookupData('Departments');
            $getuser_group=$this->user_group->getUserGroup('115');

            $businessUnit = new Users();
            $getBusinessUnitCollection = $businessUnit->getBusinessUnitList($userId);
            $getCategoriesCollection = $businessUnit->getCategoryList();
            $getBrandsCollection = $businessUnit->getBrandsList();
            
            $getManufBrandsCollection = $businessUnit->getManufacturesBrandsList();
            $buCollection = $businessUnit->getBusinessUnits();
            $assignTempRolePermission = $this->roleAccess->checkPermissionByFeatureCode("USRTEMP1");
            $tempRoles = $tempRolesExpiryDate = '';
            $tempRolesOptions = $permanentRolesOptions = '';
            if($assignTempRolePermission){
                // Temporary Roles Bro
                $tempRoles = $businessUnit->getTempRolesData($userId);

                // Expirt Date for Temporary Roles Bro
                $tempRolesExpiryDate = $businessUnit->getTempRolesExpiryDate($userId);
            }

            // Code to Format Temporary Roles Select List
            if(isset($roles) and !empty($roles)){
                $tempRoleIds = (isset($tempRoles)) ? explode(',', $tempRoles) : []; 
                $permanentRoleIds = (isset($userData['role_id'])) ? explode(',', $userData['role_id']) : []; 
                foreach ($roles as $role) {
                    $role_id_store = $role->role_id;
                    // Temporary Roles
                    if(in_array($role_id_store, $tempRoleIds))
                        $tempRolesOptions.= "<option value='".$role_id_store."' selected>".$role->name."</option>";
                    elseif (!in_array($role_id_store, $permanentRoleIds)) {
                        $tempRolesOptions.= "<option value='".$role_id_store."'>".$role->name."</option>";
                    }

                    // Permanent Roles
                    if(in_array($role_id_store, $permanentRoleIds)){
                        if(!in_array($role_id_store, $tempRoleIds))
                            $permanentRolesOptions.= "<option value='".$role_id_store."' selected>".$role->name."</option>";
                    }else
                        $permanentRolesOptions.= "<option value='".$role_id_store."'>".$role->name."</option>";
                }
            }

            $dataAccess = [];
            $userAccessPermission = $this->roleAccess->checkPermissionByFeatureCode("USR007");
            if($userAccessPermission){
                $userDataAccess = $businessUnit->getUserPermission('12',$userId);
                $userDataAccess = isset($userDataAccess[0])?$userDataAccess[0]:NULL;
                $dataAccess['userDataAccess'] = intval($userDataAccess);
            }
            
            $userAccessPermission = $this->roleAccess->checkPermissionByFeatureCode("USR008");
            if($userAccessPermission){
                $userRoleAccess = $businessUnit->getUserPermission('13',$userId);
                $userRoleAccess = isset($userRoleAccess[0])?$userRoleAccess[0]:NULL;
                $dataAccess['userRoleAccess'] = intval($userRoleAccess); 
            }
            
            $redeemPermission = $this->roleAccess->checkPermissionByFeatureCode('USRFF01');

            $selgrpData=$users->getSelectedUserGroup($userId);
          
            $businessUnitsData=$users->getBusinesUnitData();
            return view('Users::editusers')->with(array('roles' => $roles, 
                'userData' => $userData, 
                'tempRolesOptions' => $tempRolesOptions,
                'permanentRolesOptions' => $permanentRolesOptions,
                'tempRolesExpiryDate' => $tempRolesExpiryDate,
                'tempRolePermission' => $assignTempRolePermission,
                'userPermissions' => $userPermissions, 
                'reportingMangers' => $reportingMangers, 
                'getDesignations' => $getDesignations, 
                'getDepartments' => $getDepartments, 
                'getBusinessUnitCollection' => $getBusinessUnitCollection, 
                'getCategoriesCollection' => $getCategoriesCollection, 
                'buCollection' => $buCollection,
                'dataAccess' => $dataAccess,
                'redeemPermission' => $redeemPermission,
                'getBrandsCollection' => $getBrandsCollection,
                'getManufBrandsCollection'=>$getManufBrandsCollection,
                'grpdata'=>$selgrpData,
                'getGroupCollection'=>$getuser_group,
                'customer' => $customer,
                'businessUnitsData' => $businessUnitsData));
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    /**
     * [checkEmailExist To check whether the mail exists]
     * @return [string] [returns status true when mail doesn't exists else false]
     */
    public function checkEmailExist() {

        $email_id = strtolower(str_replace(' ', '', Input::get('email_id')));
        $mp = DB::select("SELECT email_id FROM users WHERE LOWER(REPLACE(email_id,' ',''))=?", array($email_id));
        $data = json_decode(json_encode($mp, true));
        if (count($data) == 0) {
            return '{"valid":true}';
        } else {
            return '{"valid":false}';
        }
    }
    /**
     * [saveUserAccess To save access level of user]
     * @return [array] [array with status & message]
     */
    public function saveUserAccess() {
        try {
            $result['status'] = true; 
            $data = \Input::get();
            if (!empty($data)) {
                $userId = isset($data['user_id']) ? $data['user_id'] : 0;
                if(!$userId)
                {
                    $result['status'] = false; 
                    $result['message'] = 'No user id';
                    return json_encode($result);
                }
                $sbu_data = isset($data['sbu_data']) ? $data['sbu_data'] : '';
                $role_access = isset($data['role_access']) ? $data['role_access'] : '';
                $sbu = isset($data['business_units']) ? $data['business_units'] : '';
                $category = isset($data['categories']) ? $data['categories'] : '';
                $permissionLevels = DB::table('permission_level')->get()->all();
                $supplier = isset($data['suppliers']) ? $data['suppliers'] : '';
                $products = isset($data['products']) ? $data['products'] : '';
                $brands = isset($data['brands']) ? $data['brands'] : '';
                $manufacturers = isset($data['manufacturers']) ? $data['manufacturers'] : '';
                DB::table('user_permssion')
                    ->where(['user_id' => $userId])
                    ->delete();
                foreach ($permissionLevels as $permission) {                
                    $permissionName = property_exists($permission, 'name') ? $permission->name : '';
                    $permissionLevelId = property_exists($permission, 'permission_level_id') ? $permission->permission_level_id : '';
                    switch ($permissionName) {
                        case 'supplier':
                            if($supplier != '')
                            {
                                $this->savePermissions($permissionLevelId, $userId, $supplier);
                            }else{
                                $this->emptyPermission($permissionLevelId, $userId);
                            }                            
                            break;
                        case 'sbu':
                            if($sbu != '')
                            {
                                $this->savePermissions($permissionLevelId, $userId, $sbu);
                            }else{
                                $this->emptyPermission($permissionLevelId, $userId);
                            }                                
                            break;
                        case 'sbu_data':
                            if($sbu_data != 0 or $sbu_data != '')
                            {
                                $this->savePermissions($permissionLevelId, $userId, $sbu_data);
                            }else{
                                $this->emptyPermission($permissionLevelId, $userId);
                            }                                
                            break;
                        case 'role_access':
                            if($role_access != 0 or $role_access != '')
                            {
                                $this->savePermissions($permissionLevelId, $userId, $role_access);
                            }else{
                                $this->emptyPermission($permissionLevelId, $userId);
                            }                                
                            break;
                        case 'category':
                            if($category != '')
                            {
                                $this->savePermissions($permissionLevelId, $userId, $category);
                            }else{
                                $this->emptyPermission($permissionLevelId, $userId);
                            }                            
                            break;
                        case 'products':
                            if($products != '')
                            {
                                $this->savePermissions($permissionLevelId, $userId, $products);
                            }else{
                                $this->emptyPermission($permissionLevelId, $userId);
                            }
                            break;
                        case 'brand':
                            if($brands != '')
                            {
                                $this->savePermissions($permissionLevelId, $userId, $brands);
                            }else{
                                $this->emptyPermission($permissionLevelId, $userId);
                            }
                            break;
                        case 'manufacturer':
                            if($manufacturers != '')
                            {
                                $this->savePermissions($permissionLevelId, $userId, $manufacturers);
                            }else{
                                $this->emptyPermission($permissionLevelId, $userId);
                            }
                            break;
                    }
                }
            }
        } catch (\ErrorException $ex) {
            $result['status'] = false; 
            $result['message'] = $ex->getMessage(); 
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        return json_encode($result);
    }
    /**
     * [savePermissions To save permission level]
     * @param  [int] $permissionLevelId [permission level id]
     * @param  [int] $userId            [user id]
     * @param  [array/string] $permissionData    [permission data either a comma seperated string or array]
     */
    function savePermissions($permissionLevelId, $userId, $permissionData) {
        try {
            $tableName = 'user_permssion';
            $insertArray['permission_level_id'] = $permissionLevelId;
            $insertArray['user_id'] = $userId;
            if(!is_array($permissionData))
            {
                $supplierArray = explode(',', $permissionData);
            }else{
                $supplierArray = $permissionData;
            }
            $finalArray = [];
            if (!empty($supplierArray)) {
                foreach ($supplierArray as $supplierId) {
                    $users = new Users();
                    $users->checkParentId($permissionLevelId, $supplierId, $insertArray, $userId);
                    $insertArray['object_id'] = $supplierId;
                    $finalInsertArray[] = $insertArray;
                }
                DB::table($tableName)->insert($finalInsertArray);
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    /**
     * [emptyPermission To remove the permission]
     * @param  [int] $permissionLevelId [permission level id]
     * @param  [int] $userId            [user id]
     */
    public function emptyPermission($permissionLevelId, $userId){
        try {
            DB::table('user_permssion')
                    ->where(['permission_level_id' => $permissionLevelId, 'user_id' => $userId])
                    ->delete();
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    /**
     * [getSegments To get segments list]
     * @return [array] [segment list]
     */
    public function getSegments() {
        try {
            $data = \Input::get();
            $segment = new Users();
            $results = $segment->getSegmentMapping($data);
            return $results;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    /**
     * [getParentCategory GEt parent category of a category]
     * @param  [int] $userId     [user id]
     * @param  [int] $categoryId [category Id]
     * @return [array]             [category id & name]
     */
    public function getParentCategory($userId, $categoryId) {
        try {
            $users = new Users();
            $data = Input::all();
            $results = $users->getParentCategories($userId, $categoryId, $data);
            return $results;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    /**
     * [getCategory description]
     * @param  [int] $userId     [user id]
     * @param  [int] $categoryId [category id]
     * @return [array]             [category lists]
     */
    public function getCategory($userId, $categoryId) {
        try {
            $users = new Users();
            $data = Input::all();
            $results = $users->getCategoryById($userId, $categoryId, $data);
            return $results;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    /**
     * [getCashBackHistory Get cashback history]
     * @param  [int] $legal_entity_id [legal entity id]
     * @param  [int] $user_id         [user id]
     * @return [array]                [cashback details]
     */
    public function getCashBackHistory($legal_entity_id,$user_id = null)
    {
        try
        {
            if($user_id == "-1")    $user_id = null;
            if(($legal_entity_id == null) or empty($legal_entity_id))
                return null;
            else
            {
                $users = new Users();
                if($user_id != null or $user_id != ''){
                    $results = $users->getCashBackHistoryById($legal_entity_id,$user_id); 
                }
                else
                    $results = $users->getCashBackHistoryById($legal_entity_id,null); 
            }
            

            $count=0;
            if(isset($results) and !empty($results) and $results != null)
            {
                foreach ($results as $record)
                {
                    if($record->order_id != null)
                        $record->order_details = "&nbsp;&nbsp;<a href='/salesorders/detail/".$record->order_id."' target='_blank'>".$record->order_code."</a>";
                    else
                        $record->order_details = "&nbsp;&nbsp;".$record->comment;

                    $record->delivery_amt = " &nbsp;".round($record->delivery_amt,2)."&nbsp; ";
                    $record->cash_back_amt = " &nbsp;".round($record->cash_back_amt,2)."&nbsp; ";
                    $record->transaction_type = " &nbsp;".$record->transaction_type."&nbsp; ";

                    $count++;
                }
            }

            return ["Records"=>$results,"totalRecCount"=>$count];
            

        }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return null;
        }
    }
    /**
     * [impersonateUsers to login as a specific user]
     * @param  Request $request [User id of a user as whom you wan to login]
     * @return [array]           [new user details]
     */
    public function impersonateUsers(Request $request){
        $data = Input::all();
        $result['status']=false;
        $getauthdetails=$this->user_group->getAuthDetails($data['user_id']);
        if($getauthdetails=='Invalid User'){
            $result['message']=$getauthdetails;
            return $result;
        }
        $result = $this->roleAccess->authenticateUser($getauthdetails['email_id'], $getauthdetails['password'],0);
        if(empty($result)){
            $result['message'] = 'Invalid email or password';
        }else{
            $role = $this->roleAccess->getRolebyUserId($data['user_id']);
            $cusomerLogo = '';
            if ($result[0]->legal_entity_id > 0) {
                $cusomerLogo = $this->custRepoObj->getCustomerLogo($result[0]->user_id);
                $cusomerLogo = isset($cusomerLogo[0]) ? $cusomerLogo[0]->profile_picture : '';
            }
            if(!empty($role)) {
                $rolesArray = [];
                foreach($role as $roleInfo){
                    $roleId = property_exists($roleInfo, 'role_id') ? $roleInfo->role_id : '';
                    $rolesArray[] = $roleId;
                }
                Session::put('userId', $result[0]->user_id);
                Session::put('userName', $result[0]->firstname.' '.$result[0]->lastname);
                Session::put('roleId', $role[0]->role_id);
                Session::put('roles', implode(',', $rolesArray));
                Session::put('fullname', $result[0]->firstname.' '.$result[0]->lastname);
                Session::put('legal_entity_id', $result[0]->legal_entity_id);
                Session::put('password', $getauthdetails['password']);
                date_default_timezone_set('Asia/Kolkata');
                $loginTime = date('Y-m-d H:i:s');
                Session::put('login_time', $loginTime);
                Session::put('customerLogoPath', 'uploads/customers/' . $cusomerLogo);
                Session::put('userLogoPath', $result[0]->profile_picture);
                $result['status']=true;
                $result['message']='Successfully logged in';
            }else{
                $result['message'] = 'You don`t have permission to access this page';
            }
        }
        return $result;
    }
    /**
     * [backToAdmin After impersonation when you go back to original user]
     */
    function backToAdmin(){

        Session::put('userId',Session::get('parentuser_id'));
        Session::put('userName', Session::get('parentuserName'));
        Session::put('roleId', Session::get('parentroleId'));
        Session::put('roles', Session::get('parentroles'));
        Session::put('fullname', Session::get('parentfullname'));
        Session::put('legal_entity_id', Session::get('parentlegal_entity_id'));
        Session::put('password', Session::get('parentpassword'));
        Session::put('customerLogoPath', Session::get('parentcustomerLogoPath'));
        Session::put('userLogoPath',Session::get('parentuserLogoPath'));
        date_default_timezone_set('Asia/Kolkata');
        $loginTime = date('Y-m-d H:i:s');
        $login=Session::put('login_time', $loginTime);

    } 
    /**
     * [getUserPassword get user password]
     * @return [string ] [return 1 when it matches with default password like ebutor@123 or Ebutor@123 else 0]
     */
    public function getUserPassword(){
        $user_id=Session::get('userId');
        $getuserpassword=$this->user_group->getUserPassword($user_id);
        return $getuserpassword;
    }  

}
