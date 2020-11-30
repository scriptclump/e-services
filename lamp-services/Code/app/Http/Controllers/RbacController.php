<?php
namespace App\Http\Controllers;
use Session;
use View;
use Validator;
use Illuminate\Support\Facades\Input;
use DB;
use URL;
use Log;
use Redirect;
use App\Central\Repositories\CustomerRepo;
use App\Central\Repositories\RoleRepo;
use App\Http\Controllers\Controller;
use App\models\MasterLookup\MasterLookup;
use App\models\Locations\Locations;
use App\models\BusinessUnit\BusinessUnit;
use App\models\User\User;
use App\models\EmailTemplate\EmailTemplate;
use Illuminate\Support\Facades\Mail;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class RbacController extends Controller {

    protected $CustomerObj;
    protected $roleAccessObj;
    protected $roleid; 
    function __construct(CustomerRepo $CustomerObj, RoleRepo $roleAccessObj) {
        $this->CustomerObj = $CustomerObj;
        $this->roleAccessObj = $roleAccessObj;
        $this->roleid = $this->roleAccessObj->getRole();
    }

    function index() {
       
            //parent::Title('Roles');
            $breadCrumbs = array('Dashboard' => url('/'), 'Roles' => url('/index'), 'Roles' => '#');
//            parent::Breadcrumb($breadCrumbs);       
        return View::make('roles.index');
    }

    function getRoles($mfg_id) {        
        $editRole = $this->roleAccessObj->checkPermissionByFeatureCode('RLE003');
        $deleteRole = $this->roleAccessObj->checkPermissionByFeatureCode('RLE004');

        $results = $this->roleAccessObj->getRole($mfg_id);
        //print_r($results); die;
        $i = 0;
        $actions = '';
        foreach ($results as $result) {
            $name = '<span style="padding-left:20px;"><a href="/rbac/edit/' . $result->role_id . '">' . $result->name . '</a></span><span style="padding-left:10px;" ></span>';

            $actions = '<span style="padding-left:20px;"><a href="/rbac/edit/' . $result->role_id . '" title="Edit Role"><i class="fa fa-pencil"></i></a></span>';

            $actions .= '<span style="padding-left:20px;" ><a href="javascript:void(0);" title="Delete Role" onclick="if(confirm(\'Are you sure you want to delete this role ?\')) { location.href=\'/rbac/delete/' . $result->role_id . '\' }"><i class="fa fa-trash-o"></i></a></span>';
            $results[$i]->name = $name;
            $results[$i]->actions = $actions;
            $i++;
        }
        return json_encode(array("Records" => $results));        
    }

    function create() {
        $customers = $this->CustomerObj->getAllCustomers();
        // parent::Breadcrumbs(array('Home' => '/', 'RBAC' => 'rbac', 'Add New' => '#'));
        //$modules = $this->roleAccessObj->getModuleFeatures();
        $lookups = MasterLookup::where('mas_cat_id', 7)->get()->all();
        /* $features = $this->roleAccessObj->getPermissionFeature(); 
          echo "<pre>"; print_r($features); die; */
        $modules = $this->roleAccessObj->getPermissionFeature();

        $inheritRoles = $this->roleAccessObj->getRole();
        if (Session::get('legal_entity_id') > 0) {

            $locationsall = Locations::where(array('manufacturer_id' => Session::get('legal_entity_id'), 'is_deleted' => 0))->get()->all();
            $businessunits = BusinessUnit::where('legal_entity_id', Session::get('legal_entity_id'))->get()->all();
        } else {
            $locationsall = array();
            $businessunits = array();
        }
        $addPermission = $this->roleAccessObj->checkPermissionByFeatureCode('USR002');
        //$users = $this->roleAccessObj->getUsers(Session::get('legal_entity_id'));
        
        $users = $this->roleAccessObj->getUsers(Session::get('userId'));
        //$users = json_decode($users);
        //echo "<pre>"; print_r($users); die;
        return View::make('roles/add')->with(array('customers' => $customers, 'modules' => $modules, 'users' => $users, 'lookups' => $lookups, 'inheritRoles' => $inheritRoles, 'locationsall' => $locationsall, 'businessunits' => $businessunits, 'addPermission' => $addPermission));
    }

    function saveRole($key_id) {
      
        $validator = Validator::make(
                        array(
                    'role_name' => Input::get('role_name'),
                    'customer_type' => Input::get('customer_type')
                        ), array(
                    'role_name' => 'required'
                    //'customer_type' => 'required'
                        )
        );

        if ($validator->fails()) {
            $messages = $validator->messages();
            $messageArr = json_decode($messages);            
            $message = isset($messageArr->role_name[0]) ? $messageArr->role_name[0] : '';
            $message .= isset($messageArr->customer_type[0]) ? $messageArr->customer_type[0] : '';
            //print_r($messageArr); die;
            return Redirect::to('rbac/add')->with(array('errorMsgArr' => $message, 'row' => Input::get()));
        } else {
             
           $data = Input::get();
           if(isset($data['manufacture_id']))
               $data['legal_entity_id'] = $data['manufacture_id'];
           unset($data['manufacture_id']);           
            //echo '<pre>'; print_r($data); die;
            $role_id = $this->roleAccessObj->SaveRole($data, $key_id);            
            if ($key_id == 0 && $role_id > 0)
                $message = 'Role added successfully';
            elseif ($key_id > 0 && $role_id > 0)
                $message = 'Role updated successfully';
            return Redirect::to('roles/index')->with('successMsg', $message);
        }
    }

    function uploadProfilePic() {
        // echo "<pre>";        print_r(Input::file('file')); die;
        $filename = Input::file('file')->getClientOriginalName();
        $destinationPath = $_SERVER['DOCUMENT_ROOT'] . '/uploads/profile_picture/';
        $filename = date('YmdHis') . $filename;
        Input::file('file')->move($destinationPath, $filename);
        echo $filename;
        die;
        //echo $files['name']; die;
        //print_r($files); die;
    }

    public function getRoleforInherit($role_id) {
        $roles = $this->roleAccessObj->getRoleById($role_id);
        return json_encode($roles);
        exit;
    }

    function edit($key_id) {
        //parent::Breadcrumbs(array('Home' => '/', 'RBAC' => 'rbac', 'Edit' => '#'));
        $roles = $this->roleAccessObj->getRoleById($key_id);
        $roles[0]->feature_id = explode(',', $roles[0]->feature_id);
        $roles[0]->user_id = explode(',', $roles[0]->user_id);
        //echo '<pre>';print_r($roles); die;
        $customers = $this->CustomerObj->getAllCustomers();
        //$modules = $this->roleAccessObj->getModuleFeatures();
        $modules = $this->roleAccessObj->getPermissionFeature();
        $lookups = MasterLookup::where('mas_cat_id', 7)->get()->all();
        
        //return $roles[0]->role_id;
        $userinfo = db::table('user_roles')
                ->leftjoin('users', 'users.user_id', '=', 'user_roles.user_id')
                ->select('user_roles.user_id', 'user_roles.role_id', 'users.firstname', 'users.lastname', 'users.email_id')
                ->where('user_roles.role_id', '=', $roles[0]->role_id)
                ->get()->all();
        if (Session::get('legal_entity_id') > 0) {
            $customerId = Session::get('legal_entity_id');
            $locationsall = array();
            $businessunits = array();
        } else {
            $customerId = $roles[0]->manufacturer_id;
            $locationsall = Locations::where(array('manufacturer_id' => $customerId, 'is_deleted' => 0))->get()->all();
            $businessunits = BusinessUnit::where('legal_entity_id', $customerId)->get()->all();
        }
        $users = $this->roleAccessObj->getUsers($customerId);

        $addPermission = $this->roleAccessObj->checkPermissionByFeatureCode('USR002');

        return View::make('roles/edit')->with(array('row' => $roles[0], 'customers' => $customers, 'modules' => $modules, 'users' => $users, 'lookups' => $lookups, 'locationsall' => $locationsall, 'businessunits' => $businessunits, 'addPermission' => $addPermission, 'userinfo' => $userinfo));
    }

    public function getUser($customerId = 0) {
        $users = $this->roleAccessObj->getUsers($customerId);
        $i = 0;
        foreach ($users as $result) {
            $users[$i]->is_active = ($result->is_active == 1) ? 'Active' : 'In-Active';
            $i++;
        }
        $locations = Locations::where('manufacturer_id', $customerId)->get()->all();
        $businessunits = BusinessUnit::where('legal_entity_id', $customerId)->get()->all();
        $results = array();
        $results['users'] = $users;
        $results['locations'] = $locations;
        $results['businessunits'] = $businessunits;

        return json_encode($results);
        exit;
    }

    public function delete($key_id) {
        $this->roleAccessObj->DeleteRole($key_id);
        return Redirect::to('roles/index');
    }

    public function saveUser() {
        $validator = Validator::make(
                        array(
                    'firstname' => Input::get('firstname'),
                    'lastname' => Input::get('lastname'),
                    'customer_type' => Input::get('customer_type1'),
                    'email_id' => Input::get('email'),
                    'password' => Input::get('password'),
                    'confirm_password' => Input::get('confirm_password'),
                    'mobile_no' => Input::get('phone_no')
                        ), array(
                    'firstname' => 'required',
                    'lastname' => 'required',
                    'customer_type' => 'required',
                    'email_id' => 'required|email|unique:users',
                    'password' => 'required',
                    'confirm_password' => 'required|same:password',
                    'mobile_no' => 'numeric|digits:10'
                        )
        );

        if ($validator->fails()) {
            $messages = $validator->messages();
            return 'fail|' . $messages;
            exit;
        } else {
            $data = Input::get();
            // print_R($data);exit;
            /*if ($data['legal_entity_id'] == '') {
                $data['legal_entity_id'] = 0;
            }*/
            if (isset($data['phone_no'])) {
                $data['mobile_no'] = $data['phone_no'];
                unset($data['phone_no']);
            }
            if (isset($data['email'])) {
                $data['email_id'] = $data['email'];
                unset($data['email']);
            }            
           
            $customer_type = $data['customer_type1'];
            $data['legal_entity_id'] = $customer_type;
            //print_r($customer_type);exit;
            unset($data['customer_type1']);
            

            $data['created_by'] = Session::get('userId');
            /* $mytime = Carbon\Carbon::now();
              $data['created_on'] = $mytime->toDateTimeString(); */
            $data['created_at'] = date('Y-m-d H:i:s');
            $password = $data['password']; //str_random(20);
            $data['password'] = md5($password);

            if (empty($data['location_id']))
                unset($data['location_id']);

            if (empty($data['business_unit_id']))
                unset($data['business_unit_id']);

            unset($data['confirm_password']);
            unset($data['_method']);
            unset($data['_token']);
            //unset($data['customer_type']);  
            
            $user_id = $this->roleAccessObj->saveUser($data);
            $template = EmailTemplate::where('Code', 'ET1000')->get()->all();            
            $emailVariable = array('firstName' => $data['firstname'], 'lastName' => $data['lastname'], 'username' => $data['email_id'], 'password' => $password);
            
            //mail($data['email'], $template[0]->Subject, $message);
            /*Mail::send('emails.welcome', $emailVariable, function($msg) use ($template, $data) {
                $msg->from($template[0]->From, 'eSealinc')->to($data['email_id'])->subject($template[0]->Subject);
            });
             * 
             */
            if (is_numeric($user_id)) {
                return 'success|' . json_encode(array('user_id' => $user_id));
                exit;
            } else {
                return 'fail|' . json_encode(array('messge' => 'Please try again'));
            }
        }
    }

    public function features() {
        parent::Breadcrumbs(array('Home' => '/', 'Features' => '#'));
        $addFeature = $this->roleAccessObj->checkPermissionByFeatureCode('FRE002');
        $modules = DB::table('master_lookup')
                ->join('lookup_categories', 'lookup_categories.id', '=', 'master_lookup.category_id')
                ->select('master_lookup.value as module_id', 'master_lookup.name')
                ->where('lookup_categories.name', 'Modules')
                ->get()->all();
        $parents = DB::table('features as a')
                ->Leftjoin('features as b', 'a.parent_id', '=', 'b.feature_id')
                ->select('a.feature_id', 'a.name as featurename', 'b.name as parentname', 'b.feature_id as parent_id')
                ->get()->all();
        return View::make('features.index')->with('modules', $modules)->with('parents', $parents)->with('addFeature', $addFeature);
        //return View::make('features.list');
    }

    public function getFeatures() {
        $modules = $this->roleAccessObj->getPermissionFeature();
        foreach ($modules as $module) {
            
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
        return json_encode($modules);
        exit;
    }

    public function store() {
        $data = Input::all();
        /*        if(!empty($data['name'])&&!empty($data['master_lookup_id'])&&!empty($data['feature_code'])){ */
        // return 'hi';
        //validator
        $validator = \Validator::make(
                        array(
                    'name' => isset($data['name']) ? $data['name'] : '',
                    'master_lookup_id' => isset($data['master_lookup_id']) ? $data['master_lookup_id'] : '',
                    'feature_code' => isset($data['feature_code']) ? $data['feature_code'] : ''
                        //'url'=> isset($data['url']) ? $data['url'] : ''
                        ), array(
                    'name' => 'required',
                    'master_lookup_id' => 'required',
                    'feature_code' => 'required'
                        //'url'=>'required'
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
            //return Response::back()->withErrors([$errorMessage]);
            return Response::json([
                        'status' => false,
                        'message' => $errorMessage
            ]);
        }
        //validator
        DB::Table('features')->insert([
            'master_lookup_id' => Input::get('master_lookup_id'),
            'name' => Input::get('name'),
            'description' => Input::get('description'),
            'feature_code' => Input::get('feature_code'),
            'is_active' => Input::get('is_active'),
            'sort_order' => Input::get('sort_order'),
            'parent_id' => Input::get('parent_id'),
            'icon' => Input::get('icon'),
            'url' => Input::get('url')
        ]);
        //DB::Table('features')->insert($data);
        return Response::json(['status' => true, 'message' => 'Feature added successfully']);
        /*      }
          return Response::json(['status'=>false, 'message'=>'Please Fill the fields']); */
    }

    public function editFeature($feature_id) {

        $feature = Feature::where('feature_id', $feature_id)->first();
        return Response::json($feature);
    }

    public function update($feature_id) {
        $data = Input::all();
        //return $data;
        //validator
        $validator = \Validator::make(
                        array(
                    'name' => isset($data['name']) ? $data['name'] : '',
                    'master_lookup_id' => isset($data['master_lookup_id']) ? $data['master_lookup_id'] : '',
                    'feature_code' => isset($data['feature_code']) ? $data['feature_code'] : ''
                        //'url'=> isset($data['url']) ? $data['url'] : ''
                        ), array(
                    'name' => 'required',
                    'master_lookup_id' => 'required',
                    'feature_code' => 'required'
                        //'url'=>'required'
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
            //return Response::back()->withErrors([$errorMessage]);
            return Response::json([
                        'status' => false,
                        'message' => $errorMessage
            ]);
        }
        //validator
        //added for Group Modifications
        $Parentfeature = DB::table('features')->where('parent_id', $feature_id)->get(array(DB::raw('group_concat(feature_id) as feature_id')))->all();
        if (isset($Parentfeature[0]->feature_id)) {
            $parent = array($Parentfeature[0]->feature_id);
            $parent = implode(',', $parent);
            $feature = DB::select(DB::raw("SELECT group_concat(feature_id) feature_id FROM features 
                where parent_id in ($parent) 
                or feature_id = $feature_id or parent_id = $feature_id"));
            $feature = implode(',', array($feature[0]->feature_id));
            //return $feature; 
            $module_id = Input::get('master_lookup_id');
            DB::select(DB::raw("Update features set  master_lookup_id =  $module_id            
                            where feature_id in ($feature)"));
        }
        //group modifications

        DB::Table('features')
                ->where('feature_id', $feature_id)
                ->update(array('master_lookup_id' => Input::get('master_lookup_id'),
                    'name' => Input::get('name'),
                    'description' => Input::get('description'),
                    'feature_code' => Input::get('feature_code'),
                    'is_active' => Input::get('is_active'),
                    'is_menu' => Input::get('is_menu'),
                    'parent_id' => Input::get('parent_id'),
                    'icon' => Input::get('icon'),
                    'url' => Input::get('url'),
                    'sort_order' => Input::get('sort_order')));
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
            return 1;
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
                DB::select(DB::raw("DELETE FROM features 
                        where feature_id in ($feature)"));
                return 1;
            } else {
                $feature = DB::table('features')
                                ->where('feature_id', $feature_id)
                                ->orWhere('parent_id', $feature_id)->delete();
                return 1;
            }
        } else {
            return "You have entered incorrect password !!";
        }
    }

    public function getdata() {
        $addFeature = $this->roleAccessObj->checkPermissionByFeatureCode('FRE002');
        $editFeature = $this->roleAccessObj->checkPermissionByFeatureCode('FRE003');
        $deleteFeature = $this->roleAccessObj->checkPermissionByFeatureCode('FRE004');
        $modarr = array();
        $finalmodarr = array();
        $mods = DB::table('master_lookup')
                ->join('lookup_categories', 'lookup_categories.id', '=', 'master_lookup.category_id')
                ->select('master_lookup.value as module_id', 'master_lookup.name as modulename')
                ->where('lookup_categories.name', 'Modules')
                ->get()->all();
        foreach ($mods as $mod) {
            $featarr = array();
            $finalfeatarr = array();
            $feats = DB::table('master_lookup')
                    ->join('lookup_categories', 'lookup_categories.id', '=', 'master_lookup.category_id')
                    ->join('features', 'features.master_lookup_id', '=', 'master_lookup.value')
                    ->select('master_lookup.name as module', 'features.master_lookup_id as module_id', 'features.name as featurename', 'features.feature_id', 'features.parent_id as parentid', 'features.feature_code', 'features.description', 'features.is_active')
                    ->where(array('lookup_categories.name' => 'Modules', 'features.master_lookup_id' => $mod->module_id, 'features.parent_id' => 0))
                    ->get()->all();

            foreach ($feats as $feat) {
                $ccfeatarr = array();
                $ccfinalfeatarr = array();
                $ccfeats = DB::table('master_lookup')
                        ->join('lookup_categories', 'lookup_categories.id', '=', 'master_lookup.category_id')
                        ->join('features', 'features.master_lookup_id', '=', 'master_lookup.value')
                        ->select('master_lookup.name as module', 'features.name as featurename', 'features.feature_id', 'features.parent_id as parentid', 'features.feature_code', 'features.description', 'features.is_active')
                        ->where(array('lookup_categories.name' => 'Modules', 'features.master_lookup_id' => $feat->module_id, 'features.parent_id' => $feat->feature_id))
                        ->get()->all();
                foreach ($ccfeats as $ccfeat) {
                    $cfeatarr = array();
                    $cfinalfeatarr = array();
                    $cfeats = DB::table('master_lookup')
                            ->join('lookup_categories', 'lookup_categories.id', '=', 'master_lookup.category_id')
                            ->join('features', 'features.master_lookup_id', '=', 'master_lookup.value')
                            ->select('master_lookup.name as module', 'features.name as featurename', 'features.feature_id', 'features.parent_id as parentid', 'features.feature_code', 'features.description', 'features.is_active')
                            ->where(array('lookup_categories.name' => 'Modules', 'features.master_lookup_id' => $feat->module_id, 'features.parent_id' => $ccfeat->feature_id))
                            ->get()->all();

                    foreach ($cfeats as $cfeat) {
                        if ($cfeat->is_active == 1)
                            $status = 'Active';
                        else
                            $status = 'In-Active';
                        $cfeatarr['feature_id'] = $cfeat->feature_id;
                        $cfeatarr['featurename'] = str_repeat("&nbsp", 10) . $cfeat->featurename;
                        $cfeatarr['feature_code'] = $cfeat->feature_code;
                        $cfeatarr['is_active'] = $status;
                        $actions = '';
                        if ($editFeature) {
                            $actions = $actions . '<span style="padding-left:10px;" ><a data-href="editfeature/' . $cfeat->feature_id . '" data-toggle="modal" data-target="#basicvalCodeModal1"><span class="badge bg-light-blue"><i class="fa fa-pencil"></i></span></a></span>';
                        }
                        if ($deleteFeature) {
                            $actions = $actions . '<span style="padding-left:10px;" ><a onclick="deleteEntityType(' . $cfeat->feature_id . ')"><span class="badge bg-red"><i class="fa fa-trash-o"></i></span></a></span>';
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
                    $ccfeatarr['is_active'] = $status;
                    $actions = '';
                    if ($addFeature) {
                        $actions = $actions . '<span style="padding-left:10px;" ><a data-href="features" data-toggle="modal" onclick="getModuleId(' . $mod->module_id . ',' . $ccfeat->feature_id . ');" data-target="#basicvalCodeModal"><span class="badge bg-green"><i class="fa fa-plus"></i></span></a></span>';
                    }
                    if ($editFeature) {
                        $actions = $actions . '<span style="padding-left:10px;" ><a data-href="editfeature/' . $ccfeat->feature_id . '" data-toggle="modal" data-target="#basicvalCodeModal1"><span class="badge bg-light-blue"><i class="fa fa-pencil"></i></span></a></span>';
                    }
                    if ($deleteFeature) {
                        $actions = $actions . '<span style="padding-left:10px;" ><a onclick="deleteParent(' . $ccfeat->feature_id . ')"><span class="badge bg-red"><i class="fa fa-trash-o"></i></span></a></span>';
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
                $featarr['is_active'] = $status;
                $actions = '';
                if ($addFeature) {
                    $actions = $actions . '<span style="padding-left:10px;" ><a data-href="features" data-toggle="modal" onclick="getModuleId(' . $mod->module_id . ',' . $feat->feature_id . ');" data-target="#basicvalCodeModal"><span class="badge bg-green"><i class="fa fa-plus"></i></span></a></span>';
                }
                if ($editFeature) {
                    $actions = $actions . '<span style="padding-left:10px;" ><a data-href="editfeature/' . $feat->feature_id . '" data-toggle="modal" data-target="#basicvalCodeModal1"><span class="badge bg-light-blue"><i class="fa fa-pencil"></i></span></a></span>';
                }
                if ($deleteFeature) {
                    $actions = $actions . '<span style="padding-left:10px;" ><a onclick="deleteParent(' . $feat->feature_id . ')"><span class="badge bg-red"><i class="fa fa-trash-o"></i></span></a></span>';
                }
                $featarr['actions'] = $actions;
                $featarr['children'] = $ccfinalfeatarr;
                $finalfeatarr[] = $featarr;
            }

            $modarr['modulename'] = $mod->modulename;
            if ($addFeature) {
                $modarr['actions'] = '<span style="padding-left:10px;" ><a data-href="features" data-toggle="modal" onclick="getModuleId(' . $mod->module_id . ');" data-target="#basicvalCodeModal"><span class="badge bg-green"><i class="fa fa-plus"></i></span></a></span>';
            }
            $modarr['children'] = $finalfeatarr;
            $finalmodarr[] = $modarr;
        }
        //echo "<pre>";  print_r($finalmodarr); die;  
        return json_encode($finalmodarr);
    }

}
