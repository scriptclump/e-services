<?php

namespace App\Modules\LegalEntities\Controllers;

use DB;
use Log;
use View;
use Session;
use Request;
use Redirect;
use Response;
use Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Cache;
use App\Central\Repositories\RoleRepo;
use App\Http\Controllers\BaseController;
use \App\Modules\LegalEntities\Models\StateModel;


class StateController extends BaseController
{
	protected $StateObj;
    protected $roleAccess;

    public function __construct(RoleRepo $roleAccess, StateModel $StateObj)
    {
        try{
            parent::Title(trans('dashboard.dashboard_title.company_name').' - '.trans('statecodes.statecode_heads.title'));
            parent::__construct();
            $this->middleware(function ($request, $next) use ($roleAccess) {
                if (!Session::has('userId')) {
                    return Redirect::to('/');
                }
                if(!$this->roleAccess->checkPermissionByFeatureCode('SCC01')){
                    return Redirect::to('/');
                }
                return $next($request);
            });
            $this->StateObj = $StateObj;
            $this->roleAccess = $roleAccess;    

        }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return "Sorry Invalid Login. Please check log for More Details";
        }
	}
  

    public function index()
    {
        try{
            if(!$this->roleAccess->checkPermissionByFeatureCode('SCC01'))
            {
            return Redirect::to('/');
            }
            parent::Breadcrumbs(array('Home' => '/', 'States' => '#'));
            $addPermission = $this->roleAccess->checkPermissionByFeatureCode('SCC02');

            $statenameInfo = $this->StateObj->getstateInfo();
            $statenameInfo=json_decode(json_encode($statenameInfo),1);
            $statenameInfoArr = array();
           
            return view('LegalEntities::statecity')
                ->with("addPermission",$addPermission)
                ->with("statenameInfo",$statenameInfo);
        }
        catch (\ErrorException $ex)
        {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return "Sorry Invalid Login. Please check log for More Details";
        }
    }
   

    public function add()
    {
        $data = Input::all();
        $result['status'] = false; 
        $result['data'] = $data;
        $data = $this->validateStateData($data);
        //if($data == []){ return $result;}
        $st_code = $this->StateObj->getstatecode($data);
        $statecode=isset($st_code[0]->gst_state_code)?$st_code[0]->gst_state_code:"";
       
        // print_r($statecode);die;
      
        //$statecode = json_encode($statecode);
        //print_r($statecode);exit;
        $result['status'] = $this->StateObj->addNewStateRecord($data,$statecode);
        return $result;
    }
    

    public function edit($id)
    {
        if($id < 0 or $id != null){
            $data = $this->StateObj->getSingleRecord($id);
            if(!empty($data)){
                $result['status'] = true;
                $result['state_name'] = $data[0]->state_name;
                $result['state_code'] = $data[0]->state_code;
                $result['city_name'] = $data[0]->city_name;
                $result['city_code'] = $data[0]->city_code;
                $result['dc_inc_id'] = $data[0]->dc_inc_id;
                $result['fc_inc_id'] = $data[0]->fc_inc_id;
                $result['latitude']  = $data[0]->latitude;
                $result['longitude']  = $data[0]->longitude;
                $result['is_active'] = intval($data[0]->is_active);
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
        // Validating Data
        if(empty($data['scc_id'])){
            return $result;
        }
        $data = $this->validateStateData($data);
        $st_code = $this->StateObj->getstatecode($data);
        $statecode=isset($st_code[0]->gst_state_code)?$st_code[0]->gst_state_code:"";
       
        if($data == []) return $result;
        //Adding New Record in the Table
        $result['status'] = $this->StateObj->updateStateRecord($data,$statecode);
        return $result;
    }
  

    public function delete($id)
    {
        $status = false;
        if($id < 0 or $id != null)
            $status = $this->StateObj->deleteSingleRecord($id);
        return ["status" => $status];
    }


    public function validateStateData($data)
    {
        $result = [];
        //Server End Validations//if(empty($data['state_name'])) return $result;
        //if(empty($data['state_code'])) return $result;//if(empty($data['city_name'])) return $result;
        //if(empty($data['city_code'])) return $result;//if(empty($data['dc_inc_id'])) return $result;
        //if(empty($data['fc_inc_id'])) return $result;// if(empty($data['is_active'])) return $result;

        $data['is_active'] = ($data['is_active'] == 'true')?1:0;

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
        $result = $this->StateObj->getStateList($page,$pageSize,$orderByData,$filterData);
        $editPermission = $this->roleAccess->checkPermissionByFeatureCode('SCC03');
        $deletePermission = $this->roleAccess->checkPermissionByFeatureCode('SCC04');
        
        try {
            $i = 0;
            foreach ($result['data'] as $record) {
                $stateRecordId = $result['data'][$i]->scc_id;
                $actions = '';
                if($editPermission)
                   $actions.= '<span class="actionsStyle" ><a onclick="editStateRecord('.$stateRecordId.')"</a><i class="fa fa-pencil"></i></span> ';
                if($deletePermission)
                   $actions.= '<span class="actionsStyle" ><a onclick="deleteStateRecord('.$stateRecordId.')"</a><i class="fa fa-trash-o"></i></span>';
                $result['data'][$i++]->actions = $actions;
            	}
            	return ["Records" => $result['data'], "TotalRecordsCount" => $result['count']];
			} catch (Exception $e) {
           			Log::error($e->getMessage()." ".$e->getTraceAsString());
            		return ["Records" => [], "TotalRecordsCount" => 0];
        		}
    	}
 

    public function validateStateName()
    {
        $data = Input::all();
        $response["valid"] = FALSE;
        // This is a common variable for add and edit modal
        $stateCode = (isset($data['add_State_name']) and !empty($data['add_State_name']))?$data['add_State_name']:"";
        if($stateCode== "")
            $stateCode = (isset($data['edit_State_name']) and !empty($data['edit_State_name']))?$data['edit_State_name']:"";
        $scc_id = (isset($data['scc_id']) and !empty($data['scc_id']))?$data['scc_id']:"";
        if($stateCode != ""){
            $result = $this->StateObj->isStateCodeUnique($scc_id,$stateCode);
            $response["valid"] = $result;
        }
        return json_encode($response);
    }
    public function validateCityName()
    {
      $data = Input::all();
      
        $response["valid"] = FALSE;
        // This is a common variable for add and edit modal
        $cityCode = (isset($data['add_City_name']) and !empty($data['add_City_name']))?$data['add_City_name']:"";
       
        if($cityCode== "")
            $cityCode = (isset($data['edit_City_name']) and !empty($data['edit_City_name']))?$data['edit_City_name']:"";
        $scc_id = (isset($data['scc_id']) and !empty($data['scc_id']))?$data['scc_id']:"";
        if($cityCode != ""){
            $result = $this->StateObj->isCityCodeUnique($scc_id,$cityCode);
            $response["valid"] = $result; 
        }
            return json_encode($response); 
    }
    public function noaccess(){
        echo "You dont have access to this Page";}
   
}
?>

