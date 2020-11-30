<?php 

namespace App\Modules\Features\Controllers;

use App\Modules\Features\Models\Feature;
use App\Central\Repositories\CustomerRepo;
use App\Central\Repositories\RoleRepo;
use App\Http\Controllers\BaseController;
use Session;
use View;
use Validator;
use Illuminate\Support\Facades\Input;
use Log;
use Request;
use Redirect;
use DB;  
use Excel;
use Caching;
use Response;
use App\Modules\Roles\Models\Role;
use Illuminate\Support\Facades\Cache;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class RbacController extends BaseController{
    
    protected $CustomerObj;
    
    protected $roleid;
   

    protected $errorLog;   
     protected $roleAccessObj;

     function __construct(CustomerRepo $CustomerObj, RoleRepo $roleAccessObj) {
        $this->middleware(function($request,$next) use ($roleAccessObj){
            $this->roleAccessObj = $roleAccessObj;
            $this->roleid = $this->roleAccessObj->getRole();
            return $next($request);
        });
        $this->CustomerObj = $CustomerObj;

        $this->errorLog = [];
            
    }
    
    function index()
    {
        $addPermission = $this->roleAccessObj->checkPermissionByFeatureCode('RLE002');
        parent::Title(trans('dashboard.dashboard_title.company_name')." - ".trans('users.users_title.index_page_title'));
        parent::Breadcrumbs(array('Home'=>'/','RBAC'=>'#')); 
        return View::make('roles.list')->with('addPermission',$addPermission);
    }
    
    function getRoles($mfg_id)
    {
        $editRole = $this->roleAccessObj->checkPermissionByFeatureCode('RLE003');
        $deleteRole = $this->roleAccessObj->checkPermissionByFeatureCode('RLE004');
        
        $results = $this->roleAccessObj->getRole($mfg_id);
       //print_r($results); die;
        $i=0;
        foreach($results as $result)
        {   
            $actions = '';
            $name = '';
            $name = '<span style="padding-left:20px;"><a href="rbac/edit/'.$result->role_id.'">'.$result->name.'</a></span><span style="padding-left:10px;" ></span>';
            if($editRole){        
                $actions .= '<span style="padding-left:20px;"><a href="rbac/edit/'.$result->role_id.'" title="Edit Role"><span class="badge bg-light-blue"><i class="fa fa-pencil"></i></span></a></span><span style="padding-left:10px;" ></span>';
            }
            
            if($deleteRole){
                $actions .= '<span style="padding-left:20px;" ><a href="javascript:void(0);" title="Delete Role" onclick="if(confirm(\'Are you sure you want to delete this role ?\')) { location.href=\'rbac/delete/'.$result->role_id.'\' }"><span class="badge bg-red"><i class="fa fa-trash-o"></i></span></a></span>';
            }
            $results[$i]->name = $name;
            $results[$i]->actions = $actions;
            
            $i++;
        }
        
        return json_encode($results);exit;
        
    }
    
    function create()
    {
       $customers = $this->CustomerObj->getAllCustomers();
       parent::Breadcrumbs(array('Home'=>'/','RBAC'=>'rbac','Add New'=>'#'));
//       $modules = $this->roleAccessObj->getModuleFeatures();
      
       $lookups = MasterLookup::where('category_id',7)->get()->all();
       /*$features = $this->roleAccessObj->getPermissionFeature(); 
       echo "<pre>"; print_r($features); die;*/
       $modules = $this->roleAccessObj->getPermissionFeature();
       $inheritRoles = $this->roleAccessObj->getRole();
       
      
       
//       $i=0;
//        
//        $temp = array();
//        foreach($modules as $module)
//        {  
//            $j=0;
//            $chilidCount = 0;
//            $feature_id = explode(',',$module->feature_id);
//            $feature_name = explode(',', $module->feature_name);
//            $parent_id = explode(',', $module->parent_id);
//            foreach($parent_id as $parentid)
//            {
//                if($parentid==0){
//                    if($j > 0)
//                        $temp[$j-1]=$feature_id[$chilidCount];
//                    //$chilidCount=0;
//                    $j++;
//                }elseif ($chilidCount==count($parent_id)-1) {
//                    $temp[$j-1]=$feature_id[$chilidCount];
//                }
//                $chilidCount++;
//            }
//            //echo $chilidCount. '='.count($feature_id);
//            $modules[$i]->feature_id = $feature_id; 
//            $modules[$i]->feature_name = $feature_name;
//            $modules[$i]->parent_id = $parent_id;
//            $modules[$i]->chileCount = $temp;
//            $i++;
//        } 
        
        //echo '<pre>'; print_r($modules); die;
        if(Session::get('legal_entity_id') > 0) {
            $locationsall=Location::where(array('manufacturer_id'=>Session::get('legal_entity_id'),'is_deleted'=>0))->get()->all();
            $businessunits=BusinessUnit::where('manufacturer_id',Session::get('legal_entity_id'))->get()->all();
        }else{
            $locationsall = array();
            $businessunits = array();
        }    
        $addPermission = $this->roleAccessObj->checkPermissionByFeatureCode('USR002');
        
        $users = $this->roleAccessObj->getUsers(Session::get('legal_entity_id'));
        //$users = json_decode($users);
        //echo "<pre>"; print_r($users); die;
        return View::make('roles/add')->with(array('customers'=>$customers,'modules'=>$modules,'users'=>$users,'lookups'=>$lookups,'inheritRoles'=>$inheritRoles,'locationsall'=>$locationsall,'businessunits'=>$businessunits,'addPermission'=>$addPermission));
    }

    function saveRole($key_id)
    { 
        $validator = Validator::make(
            array(
                'role_name'=>Input::get('role_name'),
                'customer_type'=>Input::get('customer_type')
            ),
            array(
                'role_name'=>'required',
                'customer_type' => 'required'
            )
        );
        
        if ($validator->fails())
        {
            $messages = $validator->messages();
            $messageArr = json_decode($messages);
            
            $message = isset($messageArr->role_name[0 ]) ? $messageArr->role_name[0] : '';
            $message .= isset($messageArr->customer_type[0]) ? $messageArr->customer_type[0] : '';
            //print_r($messageArr); die;
            return Redirect::to('rbac/add')->with(array('errorMsgArr'=>$message,'row'=>Input::get()));
        } else {   
            $data = Input::get();
            //echo '<pre>'; print_r($data); die;
            $role_id = $this->roleAccessObj->SaveRole($data,$key_id);
            if($key_id == 0 && $role_id > 0)
                $message = 'Role added successfully';
            elseif($key_id > 0 && $role_id > 0)
                $message = 'Role updated successfully';
            return Redirect::to('rbac')->with('successMsg',$message);
        } 
    }
    
    function uploadProfilePic()
    {
       // echo "<pre>";        print_r(Input::file('file')); die;
        $filename = Input::file('file')->getClientOriginalName();
        $destinationPath = $_SERVER['DOCUMENT_ROOT'].'/uploads/profile_picture/'; 
        $filename = date('YmdHis').$filename;
        Input::file('file')->move($destinationPath, $filename);
        echo $filename; die;
        //echo $files['name']; die;
        //print_r($files); die;
    }

    public function getRoleforInherit($role_id)
    {
        $roles = $this->roleAccessObj->getRoleById($role_id);
        return json_encode($roles); exit;
    }
            
    function edit($key_id){ 
    
        parent::Breadcrumbs(array('Home'=>'/','RBAC'=>'rbac','Edit'=>'#'));
        $roles = $this->roleAccessObj->getRoleById($key_id);
        $roles[0]->feature_id = explode(',', $roles[0]->feature_id);
        $roles[0]->user_id = explode(',', $roles[0]->user_id);
        //echo '<pre>';print_r($roles); die;
        $customers = $this->CustomerObj->getAllCustomers();
        //$modules = $this->roleAccessObj->getModuleFeatures();
        $modules = $this->roleAccessObj->getPermissionFeature();
        $lookups = MasterLookup::where('category_id',7)->get()->all();
        //return $roles[0]->role_id;
        $userinfo=db::table('user_roles')
              ->leftjoin('users','users.user_id','=','user_roles.user_id')
              ->select('users.username','user_roles.user_id','user_roles.role_id','users.firstname',
                'users.lastname','users.email')
              ->where('user_roles.role_id','=',$roles[0]->role_id)
              ->get()->all();
              //return $userinfo;
              //print_r($userinfo);die;
         //$a=db::getQuerylog();
         //return $a;    
//        $i=0;
//        foreach($modules as $module)
//        {  
//            $j=0;
//            $chilidCount = 0;
//            $feature_id = explode(',',$module->feature_id);
//            $feature_name = explode(',', $module->feature_name);
//            $parent_id = explode(',', $module->parent_id);
//            foreach($parent_id as $parentid)
//            {
//                if($parentid==0){
//                    if($j > 0)
//                        $temp[$j-1]=$feature_id[$chilidCount];
//                    
//                    $j++;
//                }elseif ($chilidCount==count($parent_id)-1) {
//                    $temp[$j-1]=$feature_id[$chilidCount];
//                }
//                $chilidCount++;
//            }
//            
//            $modules[$i]->feature_id = $feature_id; 
//            $modules[$i]->feature_name = $feature_name;
//            $modules[$i]->parent_id = $parent_id;
//            $modules[$i]->chileCount = $temp;
//
//            $i++;
//        } 
        if(Session::get('legal_entity_id') > 0 ){
           $customerId = Session::get('legal_entity_id');
           $locationsall = array();
           $businessunits = array();
        }else{
           $customerId = $roles[0]->manufacturer_id;
           $locationsall=Location::where(array('manufacturer_id'=>$customerId,'is_deleted'=>0))->get()->all();
           $businessunits=BusinessUnit::where('manufacturer_id',$customerId)->get()->all();
        }
        $users = $this->roleAccessObj->getUsers($customerId);
        
        $addPermission = $this->roleAccessObj->checkPermissionByFeatureCode('USR002');
        //echo '<pre>'; print_r($roles[0]); die;
        
        return View::make('roles/edit')->with(array('row'=>$roles[0],'customers'=>$customers,'modules'=>$modules,'users'=>$users,'lookups'=>$lookups,'locationsall'=>$locationsall,'businessunits'=>$businessunits,'addPermission'=>$addPermission,'userinfo'=>$userinfo));
    }
    
    public function getUser($customerId=0)
    {  
        $users = $this->roleAccessObj->getUsers($customerId);
        $i=0;
        foreach ($users as $result)
        {
            $users[$i]->is_active = ($result->is_active==1) ? 'Active' : 'In-Active';
            $i++;
        }
        $locations=Location::where('manufacturer_id',$customerId)->get()->all();
        $businessunits=BusinessUnit::where('manufacturer_id',$customerId)->get()->all();
        $results = array();
        $results['users'] = $users;
        $results['locations'] = $locations;
        $results['businessunits'] = $businessunits;
        
        return json_encode($results);exit;
        
    }
    
    public function delete($key_id)
    {
        $this->roleAccessObj->DeleteRole($key_id);
        return Redirect::to('rbac');
    }
    
    public function saveUser()
    { //echo "<pre>"; print_r(Input::get()); die;
        $validator = Validator::make(
            array(
                'firstname'=>Input::get('firstname'),
                'lastname'=>Input::get('lastname'),
                'customer_type'=>Input::get('customer_type1'),
                'email'=>Input::get('email'),
                'username'=>Input::get('username'),
                'password'=>Input::get('password'),
                'confirm_password'=>Input::get('confirm_password'),
                'phone_no'=>Input::get('phone_no')
            ),
            array(
                'firstname'=>'required',
                'lastname' => 'required',
                'customer_type' => 'required',
                'email' => 'required|email|unique:users',
                'username' => 'required|unique:users',
                'password' => 'required',
                'confirm_password' => 'required|same:password',
                'phone_no'=>'numeric|digits:10'
            )
        );
        
        if ($validator->fails())
        {
            $messages = $validator->messages();
            return 'fail|'.$messages;exit;
        }else {
            $data = Input::get();
            if($data['customer_id']==''){
                $data['customer_id']=0;
            }
            $customer_type = $data['customer_type1'];
            unset($data['customer_type1']);
            $data['customer_type'] = $customer_type;
            
            $data['created_by']=Session::get('userId');
            $mytime = Carbon\Carbon::now();

            $data['created_on']=$mytime->toDateTimeString();
            $password = $data['password']; //str_random(20);
            $data['password']=md5($password);
            
            if(empty($data['location_id']))
                unset($data['location_id']);
            
            if(empty($data['business_unit_id']))
                unset($data['business_unit_id']);
            
            unset($data['confirm_password']);
            unset($data['_method']);
            unset($data['_token']);
            //unset($data['customer_type']);
            //print_r($data); die;
            $user_id = $this->roleAccessObj->saveUser($data);
            
            $template = EmailTemplate::where('Code','ET1000')->get()->all();
         
            $emailVariable = array('firstName'=>$data['firstname'],'lastName'=>$data['lastname'],'username'=>$data['email'],'password'=>$password);
            //mail($data['email'], $template[0]->Subject, $message);
            Mail::send('emails.welcome',$emailVariable,function($msg) use ($template,$data) {
                $msg->from($template[0]->From,'eSealinc')->to($data['email'])->subject($template[0]->Subject);
                        
            });
            if(is_numeric($user_id)){    
                return 'success|'.json_encode(array('user_id'=>$user_id,'username'=>$data['username'])); exit;
            }else{
                return 'fail|'.json_encode(array('messge'=>'Please try again'));
            }    
        }    
    }
    
    public function features()
    {        
        parent::Breadcrumbs(array('Home'=>'/','Features'=>'#'));
        $addFeature = $this->roleAccessObj->checkPermissionByFeatureCode('FRE002');
        $modules=DB::table('master_lookup')
                ->join('master_lookup_categories','master_lookup_categories.mas_cat_id','=','master_lookup.mas_cat_id')
                ->select('master_lookup.value as module_id','master_lookup.master_lookup_name as name')
                ->where('master_lookup_categories.mas_cat_name','Modules')
                ->get()->all();
        $parents=DB::table('features as a')
                    ->Leftjoin('features as b','a.parent_id','=','b.feature_id')
                    ->select('a.feature_id','a.name as featurename','b.name as parentname','b.feature_id as parent_id')
                    ->get()->all();
        return View::make('Features::index')
                ->with('modules',$modules)
                ->with('parents',$parents)
                ->with('addFeature',$addFeature);
        //return View::make('features.list');
    }
    
    public function getFeatures()
    {
        $modules = $this->roleAccessObj->getPermissionFeature();
        foreach($modules as $module)
        {
            
        }
//        $i=0;
//        foreach($modules as $module)
//        {  
//            $j=0;
//            $chilidCount = 0;
//            $feature_id = explode(',',$module->feature_id);
//            $feature_name = explode(',', $module->feature_name);
//            $parent_id = explode(',', $module->parent_id);
//            foreach($parent_id as $parentid)
//            {
//                if($parentid==0){
//                    if($j > 0)
//                        $temp[$j-1]=$feature_id[$chilidCount];
//                    
//                    $j++;
//                }elseif ($chilidCount==count($parent_id)-1) {
//                    $temp[$j-1]=$feature_id[$chilidCount];
//                }
//                $chilidCount++;
//            }
//            
//            $modules[$i]->feature_id = $feature_id; 
//            $modules[$i]->feature_name = $feature_name;
//            $modules[$i]->parent_id = $parent_id;
//            $modules[$i]->chileCount = $temp;
//
//            $i++;
//        } 
       // echo "<pre>"; print_r($modules); die;
        return json_encode($modules); exit;
    }
    
    public function store() {
        $data = Input::all();
        $validator = \Validator::make(
                        array(
                    'name' => isset($data['name']) ? $data['name'] : '',
                    'master_lookup_id' => isset($data['master_lookup_id']) ? $data['master_lookup_id'] : '',
                    'feature_code' => isset($data['feature_code']) ? $data['feature_code'] : ''
                        ), array(
                    'name' => 'required',
                    'master_lookup_id' => 'required',
                    'feature_code' => 'required|unique:features'
        ));
        if ($validator->fails()) {
            //$data = $this->_product->getProductFields($this->_manufacturerId);
            $errorMessages = json_decode($validator->messages());
            $errorMessage = '';
            if (!empty($errorMessages)) {
                foreach ($errorMessages as $field => $message) {
                    $errorMessage = implode(',', $message);
                }
            }
            return Response::json([
                        'status' => false,
                        'message' => $errorMessage
            ]);
        }
        //validator
        $isMenu = (isset($data['is_menu'])) ? 1 : 0;
        $isActive = (isset($data['is_active'])) ? 1 : 0;
        $featureId = DB::Table('features')->insertGetId([
            'master_lookup_id' => Input::get('master_lookup_id'),
            'name' => Input::get('name'),
            'description' => Input::get('description'),
            'feature_code' => Input::get('feature_code'),
            'is_active' => $isActive,
            'is_menu' => $isMenu,
            'sort_order' => Input::get('sort_order'),
            'parent_id' => Input::get('add_parent_id'),
            'icon' => Input::get('icon'),
            'url' => Input::get('url'),
            'wiki_url' => Input::get('wiki_url'),
            'wiki_description' => Input::get('wiki_description')
        ]);
        $this->updateRoleWithNewFeature($featureId);

        $userId = Session::get('userId');
        return Response::json([
          'status' => true,
          'message' => 'Feature added successfully',
          'new_parent_id' => $featureId,
          'new_parent_value' => Input::get('name')
          ]);   
    }

    public function updateRoleWithNewFeature($featureId)
    {
        try
        {
            if($featureId > 0)
            {
                $this->roleAccessObj->updateNewFeature($featureId);
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function editFeature($feature_id)
    {

        $feature = Feature::where('feature_id',$feature_id)->first();
        $arrFeature = json_decode($feature,true);

        // Code to get all the Roles asigned for the Feature
        $roles = $this->roleAccessObj->getRole(1);
        $rolesForFeature = $this->roleAccessObj->getRolesByFeatureId($feature_id);
        
        $rolesForFeatureOptions = '';
        if(isset($roles) and !empty($roles)){
            // Bringing all the Roles in the System
            foreach ($roles as $role) {
                $selected = '';
                // Checking whether the role is Selected or Not
                foreach ($rolesForFeature as $key => $value) {
                    if($value->role_id == $role->role_id){
                        $selected = "selected";
                        break;
                    }
                }
                $rolesForFeatureOptions.='<option value="'.$role->role_id.'" '.$selected.'>'.$role->name.'</option>';
            }
            $arrFeature = array_merge($arrFeature,["role_feature_id[]" => $rolesForFeatureOptions]);
        }

        return Response::json($arrFeature);
    }

    public function update($feature_id) {
        $data = Input::all();
        // Parent should be selected
        if(!isset($data['edit_parent_id']) and $data['edit_parent_id'] == -1)   
            return Response::json([
                'status' => false,
                'message' => "Error: Parent is mandatory.",
            ]);

        //validator
        $validator = 
            \Validator::make(
                array(
                'name' => isset($data['name']) ? $data['name'] : '',
                'master_lookup_id' => isset($data['master_lookup_id']) ? $data['master_lookup_id'] : '',
                'feature_code' => isset($data['feature_code']) ? $data['feature_code'] : ''
                    ),
                array(
                'name' => 'required',
                'master_lookup_id' => 'required',
                'feature_code' => 'required'
            ));
        if ($validator->fails()) {
            $errorMessages = json_decode($validator->messages());
            $errorMessage = '';
            if (!empty($errorMessages)) {
                foreach ($errorMessages as $field => $message) {
                    $errorMessage = implode(',', $message);
                }
            }

            return Response::json([
                    'status' => false,
                    'message' => $errorMessage
            ]);

        }
        //end validator

        // DB Transaction Starts here
        //DB::beginTransaction();
        //added for Group Modifications
        try {
            
            $Parentfeature = DB::table('features')  
                ->where('parent_id', $feature_id)
                ->get(array(DB::raw('group_concat(feature_id) as feature_id')))->all();
            if (isset($Parentfeature[0]->feature_id)) {   
                $parent = array($Parentfeature[0]->feature_id);
                $parent = implode(',', $parent);
                
                $feature = DB::SELECT('
                    SELECT group_concat(feature_id) feature_id FROM features 
                    WHERE parent_id in (?) 
                    or feature_id = ?
                    or parent_id = ?',
                    [$parent,$feature_id,$feature_id]);

                $feature = implode(',', array($feature[0]->feature_id));
                $feature = explode(',', $feature);
            
                $module_id = Input::get('master_lookup_id');
                DB::table('features')
                    ->whereIn('feature_id', $feature)
                    ->update(['master_lookup_id' => $module_id]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return Response::json([
                    'status' => false,
                    'message' => 'Failed to update. Try again']);
        }


        try {    
            
            $updateData = [
                'master_lookup_id' => Input::get('master_lookup_id'),
                'name' => Input::get('name'),
                'description' => Input::get('description'),
                'feature_code' => Input::get('feature_code'),
                'parent_id' => Input::get('edit_parent_id'),
                'icon' => Input::get('icon'),
                'url' => Input::get('url'),
                'sort_order' => Input::get('sort_order'),
                'wiki_url' => Input::get('wiki_url'),
                'wiki_description' => Input::get('wiki_description')
              ];

            DB::Table('features')
                ->where('feature_id', $feature_id)
                ->update($updateData);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return Response::json([
                    'status' => false,
                    'message' => 'Failed to update. Try again']);
        }

        try {
            // Updating the Roles of the Features
            $rolesForFeature = isset($data["rolesForFeature"])?$data["rolesForFeature"]:'';
            $rolesForFeature = json_decode($rolesForFeature);
            if(!empty($rolesForFeature)){
                // Previous Records are deleted here...
                if(!is_null($rolesForFeature[0])){
                  $status = DB::table('role_access')->where('feature_id',$feature_id)->delete();
                  // Unique Check of the Roles Array
                  $rolesForFeature = array_unique($rolesForFeature);
                  foreach ($rolesForFeature as $role) {
                      if(!is_null($role))
                      {
                        DB::table('role_access')->insert(
                            ['role_id' => $role, 'feature_id' => $feature_id]
                        );
                      }
                  }
                }
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return Response::json([
                    'status' => false,
                    'message' => 'Failed to update. Try again']);    
        }

        // Finally Commiting the Above DB changes
        //DB::commit();

        // Updating Roles with 
        (new Role)->flushCache('features');    
        return Response::json([
                    'status' => true,
                    'message' => 'Sucessfully updated.']);
    }

    public function destroy($feature_id) {
        $password = Input::get();
        $userId = Session::get('userId');
        $verifiedUser = $this->roleAccessObj->verifyUser($password['password'], $userId);
        if ($verifiedUser >= 1) {
            $feature = Feature::find($feature_id)->delete();   
            
            return Response::json([
              'status' => 1,
              'deleted_parent_id' => $feature_id
              ]);  
            
        } else {
            return "You have entered incorrect password !!";
        }
    }

    public function FeatureDelete($feature_id) {
        $password = Input::get();
        $userId = Session::get('userId');
        $verifiedUser = $this->roleAccessObj->verifyUser($password['password'], $userId);
        if ($verifiedUser >= 1) {
             $Parentfeature = DB::table('features')->where('parent_id', $feature_id)->get(array(DB::raw('group_concat(feature_id) as feature_id')))->all();
            if (isset($Parentfeature[0]->feature_id)) {
                $parent = array($Parentfeature[0]->feature_id);
                $parent = implode(',', $parent);
                $feature = DB::select(DB::raw("SELECT group_concat(feature_id) feature_id FROM features 
                    where parent_id in ($parent) 
                    or feature_id = $feature_id or parent_id = $feature_id"));
                $feature = implode(',', array($feature[0]->feature_id));

                if(is_array($feature))
                  $status = DB::table('features')->whereIn($feature)->delete();
                else
                  $status = DB::table('features')->where('feature_id',$feature)->delete();
                // DB::select(DB::raw("DELETE FROM features where feature_id in ($feature)"));
                $userId = Session::get('userId');

                return Response::json([
                  'status' => $status,
                  'featuresList' => $feature
                  ]);
            } else {
                $userId = Session::get('userId');
               
                $feature = DB::table('features')
                                ->where('feature_id', $feature_id)
                                ->orWhere('parent_id', $feature_id)->delete();   
                return Response::json([
                  'status' => 1,
                  'featuresList' => $feature_id
                ]);
            }
        } else {
            return "You have entered incorrect password !!";
        }
    }

    public function getdata() {
        $userId = Session::get('userId');

        $features = '';
//        $features = Caching::getElement('features', $userId);
        if($features != '')
        {
            return $features;
        }else{
            $addFeature = $this->roleAccessObj->checkPermissionByFeatureCode('FRE002');
            //echo "<pre>"; print_r($addFeature); die();
            $editFeature = $this->roleAccessObj->checkPermissionByFeatureCode('FRE003');
            $deleteFeature = $this->roleAccessObj->checkPermissionByFeatureCode('FRE004');
            $modarr = array();
            $finalmodarr = array();
            $mods = DB::table('master_lookup')
                    ->join('master_lookup_categories', 'master_lookup_categories.mas_cat_id', '=', 'master_lookup.mas_cat_id')
                    ->select('master_lookup.value as module_id', 'master_lookup.master_lookup_name as modulename')
                    ->where('master_lookup_categories.mas_cat_name', 'Modules')
                    ->get()->all();
            foreach ($mods as $mod) {
                $featarr = array();
                $finalfeatarr = array();
                $feats = DB::table('master_lookup')
                        ->join('master_lookup_categories', 'master_lookup_categories.mas_cat_id', '=', 'master_lookup.mas_cat_id')
                        ->join('features', 'features.master_lookup_id', '=', 'master_lookup.value')
                        ->select('master_lookup.master_lookup_name as module', 'features.master_lookup_id as module_id', 'features.name as featurename', 'features.feature_id', 'features.parent_id as parentid', 'features.feature_code', 'features.description', 'features.is_active', 'features.is_menu', 'features.sort_order')
                        ->where(array('master_lookup_categories.mas_cat_name' => 'Modules', 'features.master_lookup_id' => $mod->module_id, 'features.parent_id' => 0))
                        ->orderBy('features.sort_order')
                        ->get()->all();

                foreach ($feats as $feat) {
                    $ccfeatarr = array();
                    $ccfinalfeatarr = array();
                    $ccfeats = DB::table('master_lookup')
                            ->join('master_lookup_categories', 'master_lookup_categories.mas_cat_id', '=', 'master_lookup.mas_cat_id')
                            ->join('features', 'features.master_lookup_id', '=', 'master_lookup.value')
                            ->select('master_lookup.master_lookup_name as module', 'features.name as featurename', 'features.feature_id', 'features.parent_id as parentid', 'features.feature_code', 'features.description', 'features.is_active', 'features.is_menu', 'features.sort_order')
                            ->where(array('master_lookup_categories.mas_cat_name' => 'Modules', 'features.master_lookup_id' => $feat->module_id, 'features.parent_id' => $feat->feature_id))
                            ->orderBy('features.sort_order')
                                ->get()->all();
                    foreach ($ccfeats as $ccfeat) {
                        $cfeatarr = array();
                        $cfinalfeatarr = array();
                        $cfeats = DB::table('master_lookup')
                                ->join('master_lookup_categories', 'master_lookup_categories.mas_cat_id', '=', 'master_lookup.mas_cat_id')
                                ->join('features', 'features.master_lookup_id', '=', 'master_lookup.value')
                                ->select('master_lookup.master_lookup_name as module', 'features.name as featurename', 'features.feature_id', 'features.parent_id as parentid', 'features.feature_code', 'features.description', 'features.is_active', 'features.is_menu', 'features.sort_order')
                                ->where(array('master_lookup_categories.mas_cat_name' => 'Modules', 'features.master_lookup_id' => $feat->module_id, 'features.parent_id' => $ccfeat->feature_id))
                                ->orderBy('features.sort_order')
                                  ->get()->all();

                        foreach ($cfeats as $cfeat) {
                            if ($cfeat->is_active == 1)
                                $status = 'Active';
                            else
                                $status = 'In-Active';
                            $cfeatarr['feature_id'] = $cfeat->feature_id;
                            $cfeatarr['featurename'] = str_repeat("&nbsp", 10) . $cfeat->featurename;
                            $cfeatarr['feature_code'] = $cfeat->feature_code;
                            if($cfeat->is_active)
                            {
                                $cfeatarr['is_active'] = '<label class="switch">'
                                        . '<input class="switch-input ' . $cfeat->feature_id . '_is_active" type="checkbox" checked="true" onclick="updateIsActive(' . $cfeat->feature_id . ')" name="' . $cfeat->feature_code . '" value="' . $cfeat->feature_id . '" />'
                                        . '<span class="switch-label" data-on="Yes" data-off="No"></span>'
                                        . '<span class="switch-handle"></span></label>';
                            }else{
                                $cfeatarr['is_active'] = '<label class="switch">'
                                        . '<input class="switch-input ' . $cfeat->feature_id . '_is_active" type="checkbox" check="false" onclick="updateIsActive(' . $cfeat->feature_id . ')" name="' . $cfeat->feature_code . '" value="' . $cfeat->feature_id . '" />'
                                        . '<span class="switch-label" data-on="Yes" data-off="No"></span>'
                                        . '<span class="switch-handle"></span></label>';
                            }
//                            $cfeatarr['is_active'] = $status;
                            $cfeatarr['sort_order'] = $cfeat->sort_order;
                            if($cfeat->is_menu)
                            {
                                $cfeatarr['is_menu'] = '<label class="switch">'
                                        . '<input class="switch-input notify_rm ' . $cfeat->feature_id . '" type="checkbox" checked="true" onclick="updateIsMenu(' . $cfeat->feature_id . ')" name="' . $cfeat->feature_code . '" value="' . $cfeat->feature_id . '" />'
                                        . '<span class="switch-label" data-on="Yes" data-off="No"></span>'
                                        . '<span class="switch-handle"></span></label>';
                            }else{
                                $cfeatarr['is_menu'] = '<label class="switch">'
                                        . '<input class="switch-input notify_rm ' . $cfeat->feature_id . '" type="checkbox" check="false" onclick="updateIsMenu(' . $cfeat->feature_id . ')" name="' . $cfeat->feature_code . '" value="' . $cfeat->feature_id . '" />'
                                        . '<span class="switch-label" data-on="Yes" data-off="No"></span>'
                                        . '<span class="switch-handle"></span></label>';
                            }
//                            $cfeatarr['is_menu'] = ($cfeat->is_menu == 1) ? 'Yes' : 'No';
                            $actions = '';
                            if ($editFeature) {
                                $actions = $actions . '<span style="padding-left:10px;" ><a data-href="editfeature/' . $cfeat->feature_id . '" data-toggle="modal" data-target="#basicvalCodeModal1"><i class="fa fa-pencil"></i></a></span>';
                            }
                            if ($deleteFeature) {
                                $actions = $actions . '<span style="padding-left:10px;" ><a onclick="deleteEntityType(' . $cfeat->feature_id . ')"><i class="fa fa-trash-o"></i></a></span>';
                            }
                            $cfeatarr['actions'] = $actions;
                            $cfinalfeatarr[] = $cfeatarr;
                        }
                        if ($ccfeat->is_active == 1)
                            $status = 'Active';
                        else
                            $status = 'In-Active';
                        $ccfeatarr['feature_id'] = $ccfeat->feature_id;
                        $ccfeatarr['featurename'] = str_repeat("&nbsp", 5) . $ccfeat->featurename;
                        $ccfeatarr['feature_code'] = $ccfeat->feature_code;
                        if($ccfeat->is_active)
                        {
                            $ccfeatarr['is_active'] = '<label class="switch">'
                                    . '<input class="switch-input ' . $ccfeat->feature_id . '_is_active" type="checkbox" checked="true" onclick="updateIsActive(' . $ccfeat->feature_id . ')" name="' . $ccfeat->feature_code . '" value="' . $ccfeat->feature_id . '" />'
                                    . '<span class="switch-label" data-on="Yes" data-off="No"></span>'
                                    . '<span class="switch-handle"></span></label>';
                        }else{
                            $ccfeatarr['is_active'] = '<label class="switch">'
                                    . '<input class="switch-input ' . $ccfeat->feature_id . '_is_active" type="checkbox" check="false" onclick="updateIsActive(' . $ccfeat->feature_id . ')" name="' . $ccfeat->feature_code . '" value="' . $ccfeat->feature_id . '" />'
                                    . '<span class="switch-label" data-on="Yes" data-off="No"></span>'
                                    . '<span class="switch-handle"></span></label>';
                        }
//                        $ccfeatarr['is_active'] = $status;
                        $ccfeatarr['sort_order'] = $ccfeat->sort_order;
                        if($ccfeat->is_menu)
                        {
                            $ccfeatarr['is_menu'] = '<label class="switch">'
                                    . '<input class="switch-input notify_rm ' . $ccfeat->feature_id . '" type="checkbox" checked="true" onclick="updateIsMenu(' . $ccfeat->feature_id . ')" name="' . $ccfeat->feature_code . '" value="' . $ccfeat->feature_id . '" />'
                                    . '<span class="switch-label" data-on="Yes" data-off="No"></span>'
                                    . '<span class="switch-handle"></span></label>';
                        }else{
                            $ccfeatarr['is_menu'] = '<label class="switch">'
                                    . '<input class="switch-input notify_rm ' . $ccfeat->feature_id . '" type="checkbox" check="false" onclick="updateIsMenu(' . $ccfeat->feature_id . ')" name="' . $ccfeat->feature_code . '" value="' . $ccfeat->feature_id . '" />'
                                    . '<span class="switch-label" data-on="Yes" data-off="No"></span>'
                                    . '<span class="switch-handle"></span></label>';
                        }
//                        $ccfeatarr['is_menu'] = ($ccfeat->is_menu == 1) ? 'Yes' : 'No';
                        $actions = '';
                        if ($addFeature) {
                            $actions = $actions . '<span style="padding-left:10px;" ><a data-href="features" data-toggle="modal" onclick="getModuleId(' . $mod->module_id . ',' . $ccfeat->feature_id . ');" data-target="#basicvalCodeModal"><i class="fa fa-plus"></i></a></span>';
                        }
                        if ($editFeature) {
                            $actions = $actions . '<span style="padding-left:10px;" ><a data-href="editfeature/' . $ccfeat->feature_id . '" data-toggle="modal" data-target="#basicvalCodeModal1"><i class="fa fa-pencil"></i></a></span>';
                        }
                        if ($deleteFeature) {
                            $actions = $actions . '<span style="padding-left:10px;" ><a onclick="deleteParent(' . $ccfeat->feature_id . ')"><i class="fa fa-trash-o"></i></a></span>';
                        }
                        $ccfeatarr['actions'] = $actions;
                        $ccfeatarr['children'] = $cfinalfeatarr;
                        $ccfinalfeatarr[] = $ccfeatarr;
                    }
                    if ($feat->is_active == 1)
                        $status = 'Active';
                    else
                        $status = 'In-Active';
                    $featarr['feature_id'] = $feat->feature_id;
                    $featarr['featurename'] = $feat->featurename;
                    $featarr['feature_code'] = $feat->feature_code;
                    if($feat->is_active)
                    {
                        $featarr['is_active'] = '<label class="switch">'
                                . '<input class="switch-input ' . $feat->feature_id . '_is_active" type="checkbox" checked="true" onclick="updateIsActive(' . $feat->feature_id . ')" name="' . $feat->feature_code . '" value="' . $feat->feature_id . '" />'
                                . '<span class="switch-label" data-on="Yes" data-off="No"></span>'
                                . '<span class="switch-handle"></span></label>';
                    }else{
                        $featarr['is_active'] = '<label class="switch">'
                                . '<input class="switch-input ' . $feat->feature_id . '_is_active" type="checkbox" check="false" onclick="updateIsActive(' . $feat->feature_id . ')" name="' . $feat->feature_code . '" value="' . $feat->feature_id . '" />'
                                . '<span class="switch-label" data-on="Yes" data-off="No"></span>'
                                . '<span class="switch-handle"></span></label>';
                    }
//                    $featarr['is_active'] = $status;
                    $featarr['sort_order'] = $feat->sort_order;
                    if($feat->is_menu)
                    {
                        $featarr['is_menu'] = '<label class="switch">'
                                . '<input class="switch-input notify_rm ' . $feat->feature_id . '" type="checkbox" checked="true" onclick="updateIsMenu(' . $feat->feature_id . ')" name="' . $feat->feature_code . '" value="' . $feat->feature_id . '" />'
                                . '<span class="switch-label" data-on="Yes" data-off="No"></span>'
                                . '<span class="switch-handle"></span></label>';
                    }else{
                        $featarr['is_menu'] = '<label class="switch">'
                                . '<input class="switch-input notify_rm ' . $feat->feature_id . '" type="checkbox" check="false" onclick="updateIsMenu(' . $feat->feature_id . ')" name="' . $feat->feature_code . '" value="' . $feat->feature_id . '" />'
                                . '<span class="switch-label" data-on="Yes" data-off="No"></span>'
                                . '<span class="switch-handle"></span></label>';
                    }
//                    $featarr['is_menu'] = ($feat->is_menu == 1) ? 'Yes' : 'No';
                    $actions = '';
                    if ($addFeature) {
                        $actions = $actions . '<span style="padding-left:10px;" ><a data-href="features" data-toggle="modal" onclick="getModuleId(' . $mod->module_id . ',' . $feat->feature_id . ');" data-target="#basicvalCodeModal"><i class="fa fa-plus"></i></a></span>';
                    }
                    if ($editFeature) {
                        $actions = $actions . '<span style="padding-left:10px;" ><a data-href="editfeature/' . $feat->feature_id . '" data-toggle="modal" data-target="#basicvalCodeModal1"><i class="fa fa-pencil"></i></a></span>';
                    }
                    if ($deleteFeature) {
                        $actions = $actions . '<span style="padding-left:10px;" ><a onclick="deleteParent(' . $feat->feature_id . ')"><i class="fa fa-trash-o"></i></a></span>';
                    }
                    $featarr['actions'] = $actions;
                    $featarr['children'] = $ccfinalfeatarr;
                    $finalfeatarr[] = $featarr;
                }

                $modarr['modulename'] = $mod->modulename;
                if ($addFeature) {
                    $modarr['actions'] = '<span style="padding-left:10px;" ><a data-href="features" data-toggle="modal" onclick="getModuleId(' . $mod->module_id . ');" data-target="#basicvalCodeModal"><i class="fa fa-plus"></i></a></span>';
                }
                $modarr['children'] = $finalfeatarr;
                $finalmodarr[] = $modarr;
            }
            $expiresAt = 10;
            Caching::setElement('features', $finalmodarr, $userId);
            return json_encode($finalmodarr);
        }
    }
    
    public function updateMenu()
    {
        try
        {
            $data = Input::all();
            $response = 0;
            $featureId = isset($data['featureId']) ? $data['featureId'] : 0;
            $status = isset($data['status']) ? ($data['status'] == 'true' ? 1 : 0) : 0;
            if($featureId > 0)
            {
                DB::table('features')
                        ->where('feature_id', $featureId)
                        ->update(['is_menu' => $status]);
                $response = 1;
            }
            return $response;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function updateActive()
    {
        try
        {
            $data = Input::all();
            $response = 0;
            $featureId = isset($data['featureId']) ? $data['featureId'] : 0;
            $status = isset($data['status']) ? ($data['status'] == 'true' ? 1 : 0) : 0;
            if($featureId > 0)
            {
                //DB::enableQueryLog();
                DB::table('features')
                        ->where('feature_id', $featureId)
                        ->update(['is_active' => $status]);
//                Log::info(DB::getQueryLog());
                $response = 1;
            }
            return $response;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
}

