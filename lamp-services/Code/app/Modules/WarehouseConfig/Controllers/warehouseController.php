<?php
namespace App\Modules\WarehouseConfig\Controllers;
use DB;
use Log;
use View;
use Request;
use Session;
use Response;
use Redirect;
use App\Modules\WarehouseConfig\Models\warehouseModel;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\BaseController;
use App\Central\Repositories\RoleRepo;

class warehouseController extends BaseController{
	protected $WarehouseObj;
    public function __construct(RoleRepo $roleAccess,warehouseModel $WarehouseObj){
        try{
            parent::Title(trans('dashboard.dashboard_title.company_name').' - '.trans('warehouse.warehouse_heads.title'));
            parent::__construct();
            $this->WarehouseObj = $WarehouseObj;

            $this->middleware(function ($request, $next) use ($roleAccess) {
                if (!Session::has('userId')) {
                    return Redirect::to('/');
                }
                $this->roleAccess = $roleAccess;
                    // Code to Check Access
                if(!$this->roleAccess->checkPermissionByFeatureCode('WHC01')){
                    return Redirect::to('/');
                }
                return $next($request);
            });    

        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return "Sorry Invalid Login. Please check log for More Details";
        }
    }
    public function index(){
        try
        {
            // Code to Check Access
            if(!$this->roleAccess->checkPermissionByFeatureCode('WHC01')){
               return Redirect::to('/');
            }
            parent::Breadcrumbs(array('Home' => '/','BeatConfig' => '#'));

          
            $usersInfo = $this->WarehouseObj->getusersInfo();
            $usersInfo=json_decode(json_encode($usersInfo),1);
            
            $warehouseInfo = $this->WarehouseObj->getwarehouseInfo();
            $warehouseInfo=json_decode(json_encode($warehouseInfo),1);
            $warehouseInfoArr = array();

            $spokeInfo = $this->WarehouseObj->getspokeInfo();
            $spokeInfo=json_decode(json_encode($spokeInfo),1);

            $AddWarehousePermission = $this->roleAccess->checkPermissionByFeatureCode('WHC02');
        	return view('WarehouseConfig::basic')
               ->with("usersInfo",$usersInfo)
               ->with("warehouseInfo",$warehouseInfo)
               ->with("spokeInfo",$spokeInfo)
               ->with("AddWarehousePermission",$AddWarehousePermission);

        } catch (\ErrorException $ex) {
            return "Sorry, something went wrong. Please check logs for more details";
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    public function add(){ 
       $data = Input::all();
       $result['status'] = false; 
       $result['data'] = $data;
       if($data == []) return $result;
        // Adding New Record in the Table
       $result['status'] = $this->WarehouseObj->addNewWarehouseRecord($data);  
       return $result;
    }
    public function delete($id)
    {
        $status = false;
        if($id < 0 or $id != null)
            $status = $this->WarehouseObj->deleteSingleRecord($id);
        return ["status" => $status];
    }
    public function edit($id)
    {
        if($id < 0 or $id != null){
            $data = $this->WarehouseObj->getSingleRecord($id);
            if(!empty($data)){
                $result['status'] = true;
                $result['pjp_name'] = $data[0]->pjp_name;
                $result['days'] = $data[0]->days;
                $result['pincode'] = $data[0]->default_pincode;
                $result['rm_id'] = $data[0]->rm_id;
                $result['le_wh_id'] = $data[0]->le_wh_id;
                $result['spoke_id'] = $data[0]->spoke_id;
                $result['spokes']=$this->WarehouseObj->getspokeInfo($data[0]->le_wh_id);
                $usersInfo = $this->WarehouseObj->getusersInfo($result['le_wh_id']);
                $usersInfo=json_decode(json_encode($usersInfo),1);
                $result['users']=$usersInfo;
                return $result;
            }
        }
        // If it reaches here, then it return false
        return ["status"=>false];
    }

    public function update()
    {
        $data = Input::all();
        $result['data'] = $data;
        $result['status'] = false;
         if(empty($data['pjp_pincode_area_id'])) return $result;
        // Adding New Record in the Table
        $result['status'] = $this->WarehouseObj->updateWarehouseRecord($data);

        return $result;
    }
    public function validateWarehouseData($data)
    {
        $result = [];
        // Server End Validations
        if(empty($data['pjp_name'])) return $result;
        if(empty($data['default_pincode'])) return $result;
        if(empty($data['rm_id'])) return $result;
        if(empty($data['spoke_id'])) return $result;
        if(empty($data['le_wh_id'])) return $result;
        return $data;
    }
   
    public function getList(Request $request)
    {
        $page="";
        $pageSize="";
            if( (Request::Input('page') || Request::Input('page')==0)  && Request::Input('pageSize') ){
                $page = Request::Input('page');
                $pageSize = Request::Input('pageSize');
            }
        
        $orderByData = Request::Input('$orderby');
        $filterData = Request::Input('$filter');

        $result = $this->WarehouseObj->getWarehouseList($page,$pageSize,$orderByData,$filterData);
        $EditWarehousePermission = $this->roleAccess->checkPermissionByFeatureCode('WHC03');
        $DeleteWarehousePermission = $this->roleAccess->checkPermissionByFeatureCode('WHC04');
      
        try {
            foreach ($result['data'] as $key=>$record) {  
                $warehouseRecordId = $record->pjp_pincode_area_id;
                $actions = '';
                if($EditWarehousePermission)
                    $actions.= '<span class="actionsStyle" ><a onclick="editWarehouseRecord('.$warehouseRecordId.')"</a><i class="fa fa-pencil"></i></span> ';
                 if($DeleteWarehousePermission)
                    $actions.= '<span class="actionsStyle" ><a onclick="deleteWarehouseRecord('.$warehouseRecordId.')"</a><i class="fa fa-trash-o"></i></span>';
                $result['data'][$key]->actions = $actions;
            }
            
            return ["Records" => $result['data'], "TotalRecordsCount" => $result['count']];

        } catch (Exception $e) {
            Log::error($e->getMessage()." ".$e->getTraceAsString());
            return ["Records" => [], "TotalRecordsCount" => 0];
        }
    }
    public function display($id){
        $data = $this->WarehouseObj->display($id);
        return array('status'=>true,'message'=>true,'data'=>$data);
    }
    public function access($le_wh_id){
        $usersInfo = $this->WarehouseObj->getusersInfo($le_wh_id);
        $usersInfo=json_decode(json_encode($usersInfo),1);
        $result['users']=$usersInfo;
        return $result;
    }
    
}
 