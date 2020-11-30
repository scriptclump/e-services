<?php

namespace App\Modules\HSN\Controllers;

use DB;
use Log;
use View;
use Request;
use Session;
use Response;
use Redirect;
use App\Modules\HSN\Models\HSNModel;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Cache;
use App\Central\Repositories\RoleRepo;
use App\Http\Controllers\BaseController;

class HSNController extends BaseController{

    protected $HSNObj;
    protected $roleAccess;

    public function __construct(RoleRepo $roleAccess, HSNModel $HSNObj){
        try{
            parent::Title(trans('dashboard.dashboard_title.company_name').' - '.trans('hsn.hsn_heads.title'));
            parent::__construct();
            $this->middleware(function ($request, $next) use ($roleAccess) {
                if (!Session::has('userId')) {
                    return Redirect::to('/');
                }
                if(!$this->roleAccess->checkPermissionByFeatureCode('HSN000')){
                    return Redirect::to('/');
                }
                return $next($request);
            });
            $this->HSNObj = $HSNObj;
            $this->roleAccess = $roleAccess;

        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return "Sorry Invalid Login. Please check log for More Details";
        }

    }

    public function index()
    {
        try
        {
            parent::Breadcrumbs(array('Home' => '/', 'Products' => '/products', 'HSN' => '#'));
            
            $addPermission = $this->roleAccess->checkPermissionByFeatureCode('HSN001');
            $taxPercentPermission = $this->roleAccess->checkPermissionByFeatureCode('HSN0021');
            $taxDataPermission = $this->roleAccess->checkPermissionByFeatureCode('HSN0022');
            
            return view('HSN::index')
                    ->with("addPermission",$addPermission)
                    ->with("taxPercentPermission",$taxPercentPermission)
                    ->with("taxDataPermission",$taxDataPermission);
            
        } catch (\ErrorException $ex) {
            return "Sorry, something went wrong. Please check logs for more details";
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function add()
    {
        $data = Input::all();
        $result['status'] = false; 
        $result['data'] = $data;

        // Validating Data
        $data = $this->validateHSNData($data);
        if($data == []) return $result;

        // Check to Insert New Record in the Table
        if($this->HSNObj->isHSNCodeUnique(0,$data['ITC_HSCodes'])){
            // Adding New Record in the Table
            $result['status'] = $this->HSNObj->addNewHSNRecord($data);    
        }

        return $result;
    }

    public function edit($id)
    {
        if($id < 0 or $id != null){
            $data = $this->HSNObj->getSingleRecord($id);
            if(!empty($data)){
                $result['status'] = true;
                $result['Chapter'] = $data[0]->Chapter;
                $result['ITC_HSCodes'] = $data[0]->ITC_HSCodes;
                $result['HSC_Desc'] = $data[0]->HSC_Desc;
                $result['tax_percent'] = $data[0]->tax_percent;
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
        if(empty($data['HSN_id'])) return $result;
        $data = $this->validateHSNData($data);
        if($data == []) return $result;

        // Adding New Record in the Table
        $result['status'] = $this->HSNObj->updateHSNRecord($data);

        return $result;
    }

    public function delete($id)
    {
        $status = false;
        if($id < 0 or $id != null)
            $status = $this->HSNObj->deleteSingleRecord($id);
        return ["status" => $status];
    }

    public function validateHSNData($data)
    {
        $result = [];
        // Server End Validations
        if(empty($data['Chapter'])) return $result;
        if(empty($data['ITC_HSCodes'])) return $result;
        if(empty($data['HSC_Desc'])) return $result;
        if(empty($data['is_active'])) return $result;

        $data['is_active'] = ($data['is_active'] == 'true')?1:0;
        
        // Optional
        if(empty($data['tax_percent'])) $data['tax_percent'] = NULL;

        return $data;
    }

    public function getList(Request $request)
    {
        $page = !empty(Request::Input('page'))?Request::Input('page'):1;   //Page number
        $pageSize = !empty(Request::Input('pageSize'))?Request::Input('pageSize'):10;
        
        $orderByData = Request::Input('$orderby');
        $filterData = Request::Input('$filter');

        $result = $this->HSNObj->getHSNList($page,$pageSize,$orderByData,$filterData);

        $editPermission = $this->roleAccess->checkPermissionByFeatureCode('HSN002');
        $deletePermission = $this->roleAccess->checkPermissionByFeatureCode('HSN003');
        
        try {
            $i = 0;
            foreach ($result['data'] as $record) {
                $hsnRecordId = $result['data'][$i]->HSNid;
                $actions = '';
                if($editPermission)
                    $actions.= '<span class="actionsStyle" ><a onclick="editHSNRecord('.$hsnRecordId.')"</a><i class="fa fa-pencil"></i></span> ';
                if($deletePermission)
                    $actions.= '<span class="actionsStyle" ><a onclick="deleteHSNRecord('.$hsnRecordId.')"</a><i class="fa fa-trash-o"></i></span>';
                $result['data'][$i++]->actions = $actions;
            }
            
            return ["Records" => $result['data'], "TotalRecordsCount" => $result['count']];

        } catch (Exception $e) {
            Log::error($e->getMessage()." ".$e->getTraceAsString());
            return ["Records" => [], "TotalRecordsCount" => 0];
        }
    }

    public function validateHsnCode()
    {
        $data = Input::all();
        $response["valid"] = FALSE;
        // This is a common variable for add and edit modal
        $hsnCode = (isset($data['add_ITC_HSCodes']) and !empty($data['add_ITC_HSCodes']))?$data['add_ITC_HSCodes']:0;
        if($hsnCode==0)
            $hsnCode = (isset($data['edit_ITC_HSCodes']) and !empty($data['edit_ITC_HSCodes']))?$data['edit_ITC_HSCodes']:0;
        $hsnId = (isset($data['hsn_id']) and !empty($data['hsn_id']))?$data['hsn_id']:0;
        if($hsnCode){
            $result = $this->HSNObj->isHSNCodeUnique($hsnId,$hsnCode);
            $response["valid"] = $result;
        }
        return json_encode($response);
    }
}