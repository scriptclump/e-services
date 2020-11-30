<?php
namespace App\Modules\TallyConnector\Controllers;
use DB;
use Log;
use View;
use Request;
use Session;
use Response;
use Redirect;
use App\Modules\TallyConnector\Models\tallyModel;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\BaseController;
use App\Central\Repositories\RoleRepo;

class tallyController extends BaseController{
    protected $TallyObj;
    protected $roleAccess;
    public function __construct(RoleRepo $roleAccess,tallyModel $TallyObj){
        try{
            $this->middleware(function ($request, $next) use($roleAccess, $TallyObj) {
                parent::Title(trans('dashboard.dashboard_title.company_name').' - '.trans('tally.tally_heads.title'));
                parent::__construct();
                if (!Session::has('userId')) {
                    return Redirect::to('/');
                }
                $this->TallyObj = $TallyObj;
                $this->roleAccess = $roleAccess;
                  // Code to Check Access
                if(!$this->roleAccess->checkPermissionByFeatureCode('TAL01')){
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
            if(!$this->roleAccess->checkPermissionByFeatureCode('TAL01')){
               return Redirect::to('/');
            }
            parent::Breadcrumbs(array('Home' => '/','Tally Codes' => '#'));
            $AddTallyPermission = $this->roleAccess->checkPermissionByFeatureCode('TAL02');
            return view('TallyConnector::index')
                ->with("AddTallyPermission",$AddTallyPermission);
        } catch (\ErrorException $ex) {
            return "Sorry, something went wrong. Please check logs for more details";
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    public function add(){
        $data = Input::all();
        $result['status'] = false; 
        $result['data'] = [];
        // Validating Data
        $data = $this->validateTallyData($data);
        if($data == []) return $result;
          // Check to Insert New Record in the Table
            // Adding New Record in the Table
            $result['status'] = $this->TallyObj->addNewTallyRecord($data);
            $result['message'] = trans('tally.message.success_new');
        return $result;
    }
    public function delete($id)
    {
        $status = false;
        if($id < 0 or $id != null)
            $status = $this->TallyObj->deleteSingleRecord($id);
        return ["status" => $status];
    }
    public function edit($id)
    {
        if($id < 0 or $id != null){
            $data = $this->TallyObj->getSingleRecord($id);
            if(!empty($data)){
                $result['status'] = true;
                $result['cost_centre'] = $data[0]->cost_centre;
                $result['cost_centre_group'] = $data[0]->cost_centre_group;
                $result['sync_url'] = $data[0]->sync_url;
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
        if(empty($data['sync_id'])) return $result;
        $data = $this->validateTallyData($data);
        if($data == []) return $result;
            $result['status'] = $this->TallyObj->updateTallyRecord($data);
            $result['message'] = trans('tally.message.success_updated');
        return $result;
    }
    public function validateTallyData($data)
    {
        $result = [];
        // Server End Validations
        if(empty($data['cost_centre'])) return $result;
        if(empty($data['is_active'])) return $result;
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

        $result = $this->TallyObj->getTallyList($page,$pageSize,$orderByData,$filterData);
        $EditTallyPermission = $this->roleAccess->checkPermissionByFeatureCode('TAL03');
        $DeleteTallyPermission = $this->roleAccess->checkPermissionByFeatureCode('TAL04');

        try {
            foreach ($result['data'] as $key=>$record) {                 
                $tallyRecordId = $record->sync_id;
                $actions = '';
                if($EditTallyPermission)
                    $actions.= '<span class="actionsStyle" ><a onclick="editTallyRecord('.$tallyRecordId.')"</a><i class="fa fa-pencil"></i></span> ';
                if($DeleteTallyPermission)
                    $actions.= '<span class="actionsStyle" ><a onclick="deleteTallyRecord('.$tallyRecordId.')"</a><i class="fa fa-trash-o"></i></span>';
                $result['data'][$key]->actions = $actions;
            }
            
            return ["Records" => $result['data'], "TotalRecordsCount" => $result['count']];

        } catch (Exception $e) {
            Log::error($e->getMessage()." ".$e->getTraceAsString());
            return ["Records" => [], "TotalRecordsCount" => 0];
        }
    }
    public function validateTallyCode()
    {
        $data = Input::all();
        $response["valid"] = FALSE;
        // This is a common variable for add and edit modal
        $tallyCode = (isset($data['add_cost_centre']) and !empty($data['add_cost_centre']))?$data['add_cost_centre']:"";
        if($tallyCode == "")
        $tallyCode = (isset($data['edit_cost_centre']) and !empty($data['edit_cost_centre']))?$data['edit_cost_centre']:"";
        $sync_id = (isset($data['edit_sync_id']) and !empty($data['edit_sync_id']))?$data['edit_sync_id']:0;

        if($tallyCode != ""){
            $result = $this->TallyObj->isTallyCodeUnique($sync_id,$tallyCode);
            $response["valid"] = $result;
            $response["message"] = 'Cost Centre already exist';
            $response['valid_for']='unique';

            if($result){
                $response["valid"] = $this->verifyTallyCode($tallyCode);
                $response["message"] = 'Please enter valid cost centre';
                $response['valid_for']='foreignkey';
            }
        }
        return json_encode($response);
    }
    public function verifyTallyCode($tallyCode)
    {
        $data = Input::all();
        $tallyCode = (isset($data['add_cost_centre']) and !empty($data['add_cost_centre']))?$data['add_cost_centre']:"";
        if($tallyCode == "")
        $tallyCode = (isset($data['edit_cost_centre']) and !empty($data['edit_cost_centre']))?$data['edit_cost_centre']:"";
        if($tallyCode != ""){
            $words = explode('-', $tallyCode);
            $tallyCode=isset($words[0])?$words[0]:'';
            $result = $this->TallyObj->isTallyCodeValid($tallyCode);
            return $result;
        }
        return false;
    }

}