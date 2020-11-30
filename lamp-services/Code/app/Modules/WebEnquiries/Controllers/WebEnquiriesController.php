<?php

namespace App\Modules\WebEnquiries\Controllers;

use DB;
use Log;
use View;
use Session;
use Illuminate\Http\Request;
use Redirect;
use Response;
use Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Cache;
use App\Central\Repositories\RoleRepo;
use App\Http\Controllers\BaseController;
use \App\Modules\WebEnquiries\Models\WebEnquiries;
use App\Modules\ApprovalEngine\Controllers\commonIgridController;


class WebEnquiriesController extends BaseController
{
	protected $WebEnquiryObj;
    protected $roleAccess;


    public function __construct(RoleRepo $roleAccess, WebEnquiries $WebEnquiryObj)
    {
        try{
            parent::Title(trans('dashboard.dashboard_title.company_name').' - '.'Web Enquiries');
            parent::__construct();
            $this->WebEnquiryObj = $WebEnquiryObj;
            $this->roleAccess = $roleAccess;
            $this->middleware(function ($request, $next) use ($roleAccess) {
                if(!$roleAccess->checkPermissionByFeatureCode('WE01')){
                    return Redirect::to('/');
                }
                // All the code related to the session will come here
                return $next($request);
            });  
        }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return "Sorry Invalid Login. Please check log for More Details";
        }
    }

    public function index()
    {
        try{
            if(!$this->roleAccess->checkPermissionByFeatureCode('WE01')){
                return Redirect::to('/');
            }
            parent::Breadcrumbs(array('Home' => '/', 'Web Enquiries' => '#'));
            $statusInfo = $this->WebEnquiryObj->getstatusInfo();
            $statusInfo=json_decode(json_encode($statusInfo),1);

                return view('WebEnquiries::WebEnquiries')
                    ->with("statusInfo",$statusInfo);
        }
        catch (\ErrorException $ex)
        {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return "Sorry Invalid Login. Please check log for More Details";
        }
    }

    public function edit($id)
    {
        if($id < 0 or $id != null){
            $data = $this->WebEnquiryObj->getSingleRecord($id);
            if(!empty($data)){
                $result['status1'] = true;
                $result['name'] = $data[0]->name;
                $result['type'] = $data[0]->type;
                $result['address'] = $data[0]->address;
                $result['phone'] = $data[0]->phone;
                $result['email'] = $data[0]->email;
                $result['purpose'] = $data[0]->purpose;
                $result['status'] = $data[0]->status;
                $result['comments'] = $data[0]->comments;
                return $result;
            }
        }
        // If it reaches here, then it return false
        return ["status1"=>false];
    }
 

    public function update()
    {
        $data = Input::all();
        $result['data'] = $data;
        $result['status1'] = false;
        // Validating Data
        if(empty($data['enquiry_no'])){
            return $result;
        }
       
        if($data == []) return $result;
        //Updating the record in the Table
        $result['status1'] = $this->WebEnquiryObj->updateWebEnquiry($data);
        return $result;
    }

    public function delete($id)
    {
        $status = false;
        if($id < 0 or $id != null)
            $status = $this->WebEnquiryObj->deleteWebEnquiry($id);
        return ["status" => $status];
    }


    public function getList(Request $request)
    {
        $makeFinalSql = array();
        $filter = $request->input('%24filter');
        if( $filter == ''){
            $filter = $request->input('$filter');
        }
        $this->objCommonGrid=new commonIgridController();

        //make sql for name
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("name",$filter,false);
        if($fieldQuery != ''){
            $makeFinalSql[] = $fieldQuery;
        }

        //make sql for type
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("type",$filter,false);
        if($fieldQuery != ''){
            $makeFinalSql[] = $fieldQuery;
        }

        //make sql for address
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("address",$filter,false);
        if($fieldQuery != ''){
            $makeFinalSql[] = $fieldQuery;
        }

        //make sql for phone
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("phone",$filter,false);

        if($fieldQuery != ''){
            $makeFinalSql[] = $fieldQuery;
        }
        //make sql for purpose
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("purpose",$filter,false);
        if($fieldQuery != ''){
            $makeFinalSql[] = $fieldQuery;
        }

        //make sql for email 
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("email",$filter,false);
        if($fieldQuery != ''){
            $makeFinalSql[] = $fieldQuery;
        }

        //make sql for comments
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("comments",$filter,false);

        if($fieldQuery != ''){
            $makeFinalSql[] = $fieldQuery;
        }
        //make sql for status
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("status",$filter,false);
        if($fieldQuery != ''){
            $makeFinalSql[] = $fieldQuery;
        }

        //make sql for date 
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("created_on",$filter,true);
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
        $result = $this->WebEnquiryObj->getWebEnquiriesList($makeFinalSql, $orderBy, $page, $pageSize);
        $editPermission = $this->roleAccess->checkPermissionByFeatureCode('WE02');
        $deletePermission = $this->roleAccess->checkPermissionByFeatureCode('WE03');
        try {
            $i = 0;
            foreach ($result['results'] as $record) {
                $WebEnquiryId = $result['results'][$i]->enquiry_no;
                $actions = '';
                if($editPermission)
                   $actions.= '<span class="actionsStyle" ><a onclick="editWebEnquiry('.$WebEnquiryId.')"</a><i class="fa fa-pencil"></i></span> ';
                if($deletePermission)
                   $actions.= '<span class="actionsStyle" ><a onclick="deleteWebEnquiry('.$WebEnquiryId.')"</a><i class="fa fa-trash-o"></i></span>';
                $result['results'][$i++]->actions = $actions;
            }
            return $result;
        }catch (Exception $e) {
            Log::error($e->getMessage()." ".$e->getTraceAsString());
            return ["Records" => [], "TotalRecordsCount" => 0];
        }
    }
}


?>