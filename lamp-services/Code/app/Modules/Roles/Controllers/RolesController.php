<?php	
namespace App\Modules\Roles\Controllers;
use Session;
use View;
use Validator;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\BaseController;
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
use \App\Central\Repositories\GlobalRepo;
use Illuminate\Http\Request;
use App\Modules\Roles\Models\Role;
use App\Lib\Queue;
use \App\Modules\Users\Models\Users;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
Class RolesController extends BaseController {
    protected $CustomerObj;
    protected $roleAccessObj;
    protected $roleid;

    function __construct(CustomerRepo $CustomerObj, RoleRepo $roleAccessObj, Request $request) {
        parent::__construct();
        $global = new GlobalRepo();
        $global->logRequest($request);
        $this->CustomerObj = $CustomerObj;
        $this->roleAccessObj = $roleAccessObj;
        $this->middleware(function ($request, $next) {
            $this->roleid = $this->roleAccessObj->getRole();
            return $next($request);
        });
        
    }

    public function index() {	
        parent::Title(trans('dashboard.dashboard_title.company_name')." - ".trans('roles.roles_title.index_page_title'));
        $breadCrumbs = array('Home' => url('/'), 'Administration' => '#', 'Roles' => url('/roles/index'));
        parent::Breadcrumbs($breadCrumbs);
        $addRole = $this->roleAccessObj->checkPermissionByFeatureCode('RLE003');		
        return view('Roles::index')->with(['addRole' => $addRole]);
    }

    public function getRoles($mfg_id) {
        try {
            $results = $this->roleAccessObj->getRole($mfg_id);
            $userId = Session::get('userId');
            // User Access to Edit and Delete Feature
            $user_access = 0;
            $user_access = DB::select("select count(object_id) as status from user_permssion where object_id = 1 and permission_level_id = 13 and user_id = ".$userId);
            $user_access = isset($user_access[0]->status)?$user_access[0]->status:0;
            $editRole = $this->roleAccessObj->checkPermissionByFeatureCode('RLE003',$user_access);
            $deleteRole = $this->roleAccessObj->checkPermissionByFeatureCode('RLE004',$user_access);
            //print_r($results); die;
            $i = 0;
            $actions = '';
            foreach ($results as $result) {
                $userscount = $this->roleAccessObj->getRolesCount($result->role_id);
                if ($editRole) {
                    $actions = '<span style="padding-left:20px;"><a href="/roles/edit/' . $this->roleAccessObj->encodeData($result->role_id) . '" title="Edit Role"><i class="fa fa-pencil"></i></a></span>';
                }
                if ($deleteRole) {
                    $actions .= '<span style="padding-left:20px;" ><a href="javascript:void(0);" title="Delete Role" onclick="if(confirm(\'Are you sure you want to delete this role ?\')) { location.href=\'/roles/delete/' . $result->role_id . '\' }"><i class="fa fa-trash-o"></i></a></span>';
                }
                $parentRoleName = $this->roleAccessObj->getParentRoleName($result->parent_role_id);
                $results[$i]->userscount = $userscount;
                $results[$i]->parent_role = $parentRoleName;
                $results[$i]->actions = $actions;
                $i++;
            }
            return json_encode(array("Records" => $results));
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function create() {
        try {
            $breadCrumbs = array('Home' => url('/'), 'Administration' => '#', 'Roles' => url('/roles/index'), 'Add' => '#');
            parent::Breadcrumbs($breadCrumbs);
            parent::Title(trans('dashboard.dashboard_title.company_name')." - ".trans('roles.roles_title.add_role_page_title'));
            $customers = $this->CustomerObj->getAllCustomers();
            $lookups = MasterLookup::where('mas_cat_id', 7)->get()->all();
            $modules = $this->roleAccessObj->getPermissionFeature();
            $inheritRoles = $this->roleAccessObj->getRole(1);
            if (Session::get('customerId') > 0) {
                $locationsall = Locations::where(array('manufacturer_id' => Session::get('customerId'), 'is_deleted' => 0))->get()->all();
                $businessunits = BusinessUnit::where('legal_entity_id', Session::get('customerId'))->get()->all();
            } else {
                $locationsall = array();
                $businessunits = array();
            }
            $addPermission = $this->roleAccessObj->checkPermissionByFeatureCode('USR002');
            $users = $this->roleAccessObj->getUsers();
            return view('Roles::add')->with(array('customers' => $customers, 'modules' => $modules, 'users' => $users, 
                'lookups' => $lookups, 'inheritRoles' => $inheritRoles, 'locationsall' => $locationsall, 'businessunits' => $businessunits, 
                'addPermission' => $addPermission));
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getIgGridUsers() {
        try {
            $roleId = Input::get('roleId');
  //          DB::enableQueryLog();
            $users = $this->roleAccessObj->getUsers($roleId);
//            Log::info(DB::getQueryLog());
            $fullname = '';
            $profile_pic = '';
            $i = 0;
            foreach ($users as $user) {
                if (empty($user->profile_picture)) {
                    $bp = url('img/avatar5.png');
                    $profile_pic = '<img src="' . $bp . '" class="img-circle" style ="height:35px;margin-left: -3px;">';
                } else {
                    $bp = url('uploads/profile_picture/' . $user->profile_picture);
                    $profile_pic = '<img src="' . $bp . '" class="img-circle" style ="height:35px;margin-left: -3px;">';
                }
                $fullname = '<p><strong>Name:</strong>&nbsp;' . $user->firstname . ' ' . $user->lastname . '</br><strong>Email:</strong>&nbsp;' . $user->email_id . '</br><strong>Mobile</strong>:&nbsp;' . $user->mobile_no . '</p>';
                $users[$i]->fullname = $fullname;
                $users[$i]->profile_pic = $profile_pic;
                $i++;
            }

            return json_encode($users);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function saveRole($key_id) {
        try {
            $data = Input::get();
            $data['legal_entity_id'] = Session::get('legal_entity_id');
            $count = 0;
            if (empty($data['updateroleId'])) {
                $count = DB::table('roles')
                        ->where('legal_entity_id', $data['legal_entity_id'])
                        ->where('name', $data['role_name'])
                        ->count();
            }
            if ($count == 0) {
                $data['created_by'] = Session::get('userId');
                $role_id = $this->roleAccessObj->SaveRole($data, $key_id);
                if ($key_id == 0 && $role_id > 0)
                {
                    $roleName = isset($data['role_name']) ? $data['role_name'] : '';
                    @\Notifications::addNotification(['note_code' => 'ROL001', 'note_params' => ['ROLENAME' => $roleName]]);
                    $message = 'Role added successfully';
                    
                }elseif ($key_id > 0 && $role_id > 0)
                {
                    $message = 'Role updated successfully';
                }                
                return $role_id;
            }
            else {
                return 'exit';
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function insertRoleperMission() {
        try {
            $data = Input::get();
            $this->queue = new Queue();
            $encoded = base64_encode(json_encode($data));
            
            $args = array("ConsoleClass" => 'RolesUpdate', 'arguments' => array('data'=>$encoded)); 
            $job = $this->queue->enqueue('default', 'ResqueJobRiver', $args);
           
            //return $job;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function insertUsersroles() {
        try {
            $data = Input::get();
            $userRolse = $this->roleAccessObj->insertUsersroles($data);
            return $userRolse;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getPermissionIds() {
        try {
            $data = Input::get();
            $userRolse = $this->roleAccessObj->getPermissionIds($data);
            $result = array();
            foreach ($userRolse as $userRols) {
                $result[] = $userRols->feature_id;
            }
            return $result;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function uploadProfilePic() {
        try {
            $filename = Input::file('file')->getClientOriginalName();
            $destinationPath = $_SERVER['DOCUMENT_ROOT'] . '/uploads/profile_picture/';
            $filename = date('YmdHis') . $filename;
            Input::file('file')->move($destinationPath, $filename);
            echo $filename;
            die;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getRoleforInherit($role_id) {
        try {
            $roles = $this->roleAccessObj->getRoleById($role_id);
            return json_encode($roles);
            exit;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function edit($roleId) {
        try {
            if(is_numeric($roleId)) { 
                //Session::flash('invalidrole', 'Invalid role id');
                return redirect('roles/index')->withFlashMessage('Invalid role');
                exit;
            }
            $roleId = $this->roleAccessObj->decodeData($roleId);
            $breadCrumbs = array('Home' => url('/'), 'Administration' => '#', 'Roles' => url('/roles/index'), 'Edit Role' => '#');
            parent::Breadcrumbs($breadCrumbs);            
            $legalEntityId = Session::get('legal_entity_id');
            $userId = Session::get('userId');
            $currentRoleId = Session::get('roleId');
            $roles = $this->roleAccessObj->getRoleById($roleId);
            $roleName = property_exists($roles, 'name') ? $roles->name : '';
            parent::Title(trans('dashboard.dashboard_title.company_name')." - ".trans('roles.roles_title.edit_role_page_title'). " (".$roleName.")");
//            $roles[0]->feature_id = explode(',', $roles[0]->feature_id);
//            $roles[0]->user_id = explode(',', $roles[0]->user_id);
            $modules = $this->roleAccessObj->getPermissionFeature();
            $lookups = MasterLookup::where('mas_cat_id', 7)->get()->all();
            if ($currentRoleId == 1) {
                $userinfo = db::table('user_roles')
                        ->leftJoin('users', 'users.user_id', '=', 'user_roles.user_id')
                        ->select('users.mobile_no', 'user_roles.user_id', 'user_roles.role_id', 'users.firstname', 'users.lastname', 'users.email_id')
                        ->where(['user_roles.role_id' => $roleId])
                        ->whereNotIn('user_roles.user_id', [$userId])
                        ->groupBy('users.user_id')
                        ->get()->all();
            } else {
                $userinfo = db::table('user_roles')
                        ->leftJoin('users', 'users.user_id', '=', 'user_roles.user_id')
                        ->select('users.mobile_no', 'user_roles.user_id', 'user_roles.role_id', 'users.firstname', 'users.lastname', 'users.email_id')
                        ->where(['user_roles.role_id' => $roleId, 'users.legal_entity_id' => $legalEntityId])
                        ->whereNotIn('user_roles.user_id', [$userId])
                        ->groupBy('users.user_id')
                        ->get()->all();
            }
            $rolesList = $this->roleAccessObj->getRolesList($legalEntityId, $roleId);
            $hasChilds = $this->roleAccessObj->hasChildRoles($roleId);            
            $selectedUsers = $this->roleAccessObj->secoundGridInUsers($roleId);            
            $fullname = '';
            $profile_pic = '';
            $i = 0;
            foreach ($selectedUsers as $user) {
                if (empty($user->profile_picture)) {
                    $bp = url('img/avatar5.png');
                    $profile_pic = '<img src="' . $bp . '" class="img-circle" style ="height:35px;margin-left: -3px;">';
                } else {
                    $bp = url('uploads/profile_picture/' . $user->profile_picture);
                    $profile_pic = '<img src="' . $bp . '" class="img-circle" style ="height:35px;margin-left: -3px;">';
                }
                $fullname = '<p><strong>Name:</strong>&nbsp;' . $user->firstname . ' ' . $user->lastname . '</br><strong>Email:</strong>&nbsp;' . $user->email_id . '</br><strong>Mobile</strong>:&nbsp;' . $user->mobile_no . '</p>';
                $selectedUsers[$i]->fullname = $fullname;
                $selectedUsers[$i]->profile_pic = $profile_pic;
                $i++;
            }
            $secoundGridData = json_encode($selectedUsers);
            $addPermission = $this->roleAccessObj->checkPermissionByFeatureCode('USR002');
            $users = new Users();
            $businessUnitsData=$users->getBusinesUnitData();
            return view('Roles::edit')->with(array('row' => $roles,
                        'addPermission' => $addPermission,
                        'modules' => $modules,
                        'userinfo' => $userinfo,
                        'rolesList' => $rolesList,
                        'hasChilds' => $hasChilds,
                        'secoundGridData' => $secoundGridData,
                        'businessUnitsData' => $businessUnitsData));
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getUser($customerId = 0) {
        try {
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
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function delete($roleId) {
        try {
            $test = $this->roleAccessObj->DeleteRole($roleId);
            return Redirect::to('/roles/index');
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function saveUser() {
        try {
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
                if (isset($data['phone_no'])) {
                    $data['mobile_no'] = $data['phone_no'];
                    unset($data['phone_no']);
                }
                if (isset($data['email'])) {
                    $data['email_id'] = $data['email'];
                    unset($data['email']);
                }
                $customer_type = $data['customer_type1'];
                $data['legal_entity_id'] = Session::get('legal_entity_id');
                unset($data['customer_type1']);


                $data['created_by'] = Session::get('userId');
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

                $user_id = $this->roleAccessObj->saveUser($data);
                $template = EmailTemplate::where('Code', 'ET1000')->get()->all();
                $emailVariable = array('firstName' => $data['firstname'], 'lastName' => $data['lastname'], 'username' => $data['email_id'], 'password' => $password);

                //mail($data['email'], $template[0]->Subject, $message);
                /* Mail::send('emails.welcome', $emailVariable, function($msg) use ($template, $data) {
                  $msg->from($template[0]->From, 'eSealinc')->to($data['email_id'])->subject($template[0]->Subject);
                  });
                 * 
                 */
                if (is_numeric($user_id)) {
                    //return 'success|' . json_encode(array('user_id' => $user_id]));
                    return 'success|' . json_encode(array('user_id' => $user_id, 'firstname' => $data['firstname'], 'lastname' => $data['lastname']));
                    exit;
                } else {
                    return 'fail|' . json_encode(array('messge' => 'Please try again'));
                }
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function features() {
        try {
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
            return view('features/index')->with('modules', $modules)->with('parents', $parents)->with('addFeature', $addFeature);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        //return View::make('features.list');
    }

    public function getFeatures() {
        try {
            $modules = $this->roleAccessObj->getPermissionFeature();
            foreach ($modules as $module) {
                
            }
            return json_encode($modules);
            exit;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function store() {
        try {
            $data = Input::all();
            $validator = \Validator::make(
                            array(
                        'name' => isset($data['name']) ? $data['name'] : '',
                        'master_lookup_id' => isset($data['master_lookup_id']) ? $data['master_lookup_id'] : '',
                        'feature_code' => isset($data['feature_code']) ? $data['feature_code'] : ''
                            ), array(
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
            return Response::json(['status' => true, 'message' => 'Feature added successfully']);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function editFeature($feature_id) {
        try {
            $feature = Feature::where('feature_id', $feature_id)->first();
            return Response::json($feature);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function update($feature_id) {
        try {
            $data = Input::all();
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
            $Parentfeature = DB::table('features')->where('parent_id', $feature_id)->get(array(DB::raw('group_concat(feature_id) as feature_id')))->all();
            if (isset($Parentfeature[0]->feature_id)) {
                $parent = array($Parentfeature[0]->feature_id);
                $parent = implode(',', $parent);
                $feature = DB::select(DB::raw("SELECT group_concat(feature_id) feature_id FROM features 
					where parent_id in ($parent) 
					or feature_id = $feature_id or parent_id = $feature_id"));
                $feature = implode(',', array($feature[0]->feature_id));
                $module_id = Input::get('master_lookup_id');
                DB::select(DB::raw("Update features set  master_lookup_id =  $module_id            
								where feature_id in ($feature)"));
            }
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
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function destroy($feature_id) {
        try {
            $password = Input::get();
            $userId = Session::get('userId');
            $verifiedUser = $this->roleAccessObj->verifyUser($password['password'], $userId);
            if ($verifiedUser >= 1) {
                $feature = Feature::find($feature_id)->delete();
                return 1;
            } else {
                return "You have entered incorrect password !!";
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function FeatureDelete($feature_id) {
        try {
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
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getdata() {
        try {
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
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function Updaterole($key_id) {
        try {
            $data = Input::get();
            // $data['legal_entity_id'] = Session::get('legal_entity_id');
            $count = 0;
            if (!empty($data['updateroleId'])) {
                $count = DB::table('roles')
                        ->where('legal_entity_id', $data['legal_entity_id'])
                        ->where('name', $data['role_name'])
                        ->count();
                $count1 = DB::table('roles')
                        ->where('legal_entity_id', $data['legal_entity_id'])
                        ->where('role_id', $data['updateroleId'])
                        ->where('name', $data['role_name'])
                        ->count();
            }     //echo $count1,$count;exit;       
            if ($count == 1 && $count1 == 1 || $count == 0 && $count1 == 1 || $count == 0 && $count1 == 0) {
                $data['created_by'] = Session::get('userId');
                $role_id = $this->roleAccessObj->SaveRole($data, $key_id);
                if ($key_id == 0 && $role_id > 0)
                    $message = 'Role added successfully';
                elseif ($key_id > 0 && $role_id > 0)
                    $message = 'Role updated successfully';
                return $role_id;
            }
            else {
                return 'exit';
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function getFilterData()
    {
        try
        {
            $data = Input::all();
            $userId = isset($data['user_id']) ? $data['user_id'] : 0;
            $permissionLevelId = isset($data['permission_level_id']) ? $data['permission_level_id'] : 0;
            $roles = new Role();
            $response = $roles->getFilterData($permissionLevelId, $userId);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        return $response;
    }   
    
    public function getAllFilters()
    {
        try
        {
            $roles = new Role();
            $response = $roles->getAllFilterData();
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        return $response;
    }    
    
    public function getUsersByLeId()
    {
        try
        {
            $data = Input::all();
            $roles = new Role();
            $response = $roles->getUsersByLeId($data);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        return $response;
    }    
}
