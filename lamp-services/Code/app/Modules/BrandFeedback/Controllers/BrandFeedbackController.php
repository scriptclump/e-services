<?php

namespace App\Modules\BrandFeedback\Controllers;

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
use \App\Modules\BrandFeedback\Models\BrandFeedback;
use App\Modules\ApprovalEngine\Controllers\commonIgridController;

use Carbon\Carbon;
use Excel;

class BrandFeedbackController extends BaseController
{
	protected $brandFeedbackObj;
    protected $roleAccess;


    public function __construct(RoleRepo $roleAccess, BrandFeedback $brandFeedbackObj)
    {
        try{
            parent::Title(trans('dashboard.dashboard_title.company_name').' - '.'Brand Feedback');
            parent::__construct();
            $this->brandFeedbackObj = $brandFeedbackObj;
            $this->roleAccess = $roleAccess;
            $this->middleware(function ($request, $next) use ($roleAccess) {
                if(!$roleAccess->checkPermissionByFeatureCode('BFB001')){
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
    	try
    	{
    		if(!$this->roleAccess->checkPermissionByFeatureCode('BFB001')){
                return Redirect::to('/');
            }
            parent::Breadcrumbs(array('Home' => '/','Customers'=> '/retailers/index', 'Brand Feedback' => '#'));


            $statusInfo = $this->roleAccess->getMasterLookupData('Brand Feedback');
            $statusInfo=json_decode(json_encode($statusInfo),1);

            $brandfeedbackexport = $this->roleAccess->checkPermissionByFeatureCode('BFB004');
            $assignUsersByFeatureCode = $this->roleAccess->getUsersByMasterLookupValue('189004');
            $assignUsersByFeatureCode = json_decode(json_encode($assignUsersByFeatureCode),1);

            return view('BrandFeedback::BrandFeedback')
                    ->with("statusInfo",$statusInfo)
                    ->with("brandfeedbackexport", $brandfeedbackexport)
                    ->with("assignUsersByFeatureCode",$assignUsersByFeatureCode);

    	}
    	catch (\ErrorException $ex)
        {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return "Sorry Invalid Login. Please check log for More Details";
        }
    }	

    //grid function
    public function getList(Request $request)
    {
        $makeFinalSql = array();
        $filter = $request->input('%24filter');
        if( $filter == ''){
            $filter = $request->input('$filter');
        }
        $this->objCommonGrid=new commonIgridController();

        //make sql for Sales Rep
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("sales_rep",$filter,false);
        $fieldQuery = str_replace('sales_rep', 'IFNULL(GetUserName(bfb.ff_id,2),"All")', $fieldQuery);
        if($fieldQuery != ''){
            $makeFinalSql[] = $fieldQuery;
        }

        //make sql for Shop Name
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("shop_name",$filter,false);
        $fieldQuery = str_replace('shop_name', 'rf.`business_legal_name`', $fieldQuery);
        if($fieldQuery != ''){
            $makeFinalSql[] = $fieldQuery;
        }

		//make sql for city
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("beat",$filter,false);
        if($fieldQuery != ''){
            $makeFinalSql[] = $fieldQuery;
        }

        //make sql for DC/FC Name
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("dc_name",$filter,false);
        if($fieldQuery != ''){
            $makeFinalSql[] = $fieldQuery;
        }

        //make sql for city
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("city",$filter,false);
        if($fieldQuery != ''){
            $makeFinalSql[] = $fieldQuery;
        }

        //make sql for state
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("state",$filter,false);

        if($fieldQuery != ''){
            $makeFinalSql[] = $fieldQuery;
        }
        //make sql for buying price
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("buying_price",$filter,false);
        if($fieldQuery != ''){
            $makeFinalSql[] = $fieldQuery;
        }

        //make sql for selling price 
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("selling_price",$filter,false);
        if($fieldQuery != ''){
            $makeFinalSql[] = $fieldQuery;
        }

        //make sql for weekly sales value
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("weekly_sales_value",$filter,false);

        if($fieldQuery != ''){
            $makeFinalSql[] = $fieldQuery;
        }

        //make sql for status
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("status",$filter,false);
        $fieldQuery = str_replace('status', 'IFNULL(getMastLookupValue(bfb.status),"Open")', $fieldQuery);
        if($fieldQuery != ''){
            $makeFinalSql[] = $fieldQuery;
        }

        //make sql for comments
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("comments",$filter,false);
        if($fieldQuery != ''){
            $makeFinalSql[] = $fieldQuery;
        }

        //make sql for created by
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("created_by",$filter,false);
        $fieldQuery = str_replace('created_by', 'IFNULL(GetUserName(bfb.created_by,2),"All")', $fieldQuery);
        if($fieldQuery != ''){
            $makeFinalSql[] = $fieldQuery;
        }

        //make sql for created at         
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("created_at",$filter,true);
        $fieldQuery = str_replace('created_at', 'bfb.`created_at`', $fieldQuery);
        if($fieldQuery != ''){
            $makeFinalSql[] = $fieldQuery;
        }

		//make sql for updated by
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("updated_by",$filter,false);
        $fieldQuery = str_replace('updated_by', 'IFNULL(GetUserName(bfb.updated_by,2),"All")', $fieldQuery);
        if($fieldQuery != ''){
            $makeFinalSql[] = $fieldQuery;
        }

        //make sql for updated at 
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("updated_at",$filter,true);
        $fieldQuery = str_replace('updated_at', 'bfb.`updated_at`', $fieldQuery);
        if($fieldQuery != ''){
            $makeFinalSql[] = $fieldQuery;
        }

        //make sql for Assignee
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("assignee",$filter,false);
        $fieldQuery = str_replace('assignee', 'IFNULL(GetUserName(bfb.assignee,2),"All")', $fieldQuery);
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
        // $result = $this->WebEnquiryObj->getWebEnquiriesList($makeFinalSql, $orderBy, $page, $pageSize);
        $result = $this->brandFeedbackObj->getBrandFeedbackList($makeFinalSql, $orderBy, $page, $pageSize);
        $editPermission = $this->roleAccess->checkPermissionByFeatureCode('BFB002');
        $deletePermission = $this->roleAccess->checkPermissionByFeatureCode('BFB003');

        try {
            $i = 0;
            foreach ($result['results'] as $record) {

	            // if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $result['results'][$i]->image)) {
	            //     $result['results'][$i]->image = '/uploads/products/' . $result['results'][$i]->image;
	            // }

                $upload_img_arr = explode(",", $result['results'][$i]->image);
                if(count($upload_img_arr)>1)
                {
                    $result['results'][$i]->image = $upload_img_arr[0];
                }

                $brand_feedback_id = $result['results'][$i]->brand_feedback_id;
                $actions = '';
                if($editPermission)
                   $actions.= '<span class="actionsStyle" ><a onclick="editBrandFeedback('.$brand_feedback_id.')"</a><i class="fa fa-pencil"></i></span> ';
                if($deletePermission)
                   $actions.= '<span class="actionsStyle" ><a onclick="deleteWebEnquiry('.$brand_feedback_id.')"</a><i class="fa fa-trash-o"></i></span>';
                $result['results'][$i++]->actions = $actions;
            }
            return $result;
        }catch (Exception $e) {
            Log::error($e->getMessage()." ".$e->getTraceAsString());
            return ["Records" => [], "TotalRecordsCount" => 0];
        }
    }

    //Functon used to edit the brand feedback
    public function edit($id)
    {
        if($id < 0 or $id != null){
            $data = $this->brandFeedbackObj->getSingleRecord($id);
            if(!empty($data)){
                $result['status1'] 	= true;
                $result['sales_rep'] 	= $data[0]->sales_rep;
                $result['shop_name'] 	= $data[0]->shop_name;
                $result['city'] 	= $data[0]->city;
                $result['state'] 	= $data[0]->state;
                $result['beat']		= $data[0]->beat;
                $result['dc_name']	= $data[0]->dc_name;
                $result['buying_price'] 	= $data[0]->buying_price;
                $result['selling_price'] 	= $data[0]->selling_price;
                $result['weekly_sales_value'] 	= $data[0]->weekly_sales_value;
                $result['image'] = $data[0]->image;
                $result['status'] = $data[0]->status;
                $result['created_by'] = $data[0]->created_by;
                $result['created_at'] = $data[0]->created_at;
                $result['updated_by'] = $data[0]->updated_by;
                $result['updated_at'] = $data[0]->updated_at;
                $result['comments'] = $data[0]->comments;
                $result['assignee']    = $data[0]->assignee;
                return $result;
            }
        }
        // If it reaches here, then it return false
        return ["status1"=>false];
    }

    //Function used to delete the brand feedback
    public function delete($id)
    {
        $status = false;
        if($id < 0 or $id != null)
            $status = $this->brandFeedbackObj->deleteBrandFeedback($id);
        return ["status" => $status];
    }

    //Function used to update the status of brand feedback
    public function update()
    {
        $data = Input::all();
        $result['data'] = $data;
        $result['status1'] = false;
        // Validating Data
        if(empty($data['brand_feedback_id'])){
            return $result;
        }
       
        if($data == []) return $result;
        //Updating the record in the Table
        $userId = Session::get('userId');
        $result['status1'] = $this->brandFeedbackObj->updateBrandFeedback($data, $userId);
        return $result;
    }

    public function downloadBrandFeedbackExcel() {
        ini_set('max_execution_time', 1200);
        try
        {
            $headings = array('Sales Rep', 'Shop Name','Beat' ,'FC','City', 'State', 'Buying Price', 'Selling Price', 'Weekly Sales Value', 'Image','Status', 'Comments', 'Assignee', 'Created By', 'Created At' , 'Updated By', 'Updated At');

            $filterData = Input::get();

            $from_date  = (isset($filterData['from_date']) && !empty($filterData['from_date'])) ? $filterData['from_date'] : date('Y-m').'-01';
            $from_date  = date('Y-m-d', strtotime($from_date));

            $to_date    = (isset($filterData['to_date']) && !empty($filterData['to_date'])) ? $filterData['to_date'] : date('Y-m-d');
            $to_date    = date('Y-m-d', strtotime($to_date));

            $details    = $this->brandFeedbackObj->getFeedbackExportDetails($from_date,$to_date); 

            $details = json_decode(json_encode($details),1);

            $mytime = Carbon::now();

            Excel::create('Brand Feedback Sheet-'.$mytime->toDateTimeString(), function($excel) use($headings,$details) 
            {
                $excel->sheet("Brand Feedback", function($sheet) use($headings, $details)
                {
                    $sheet->setWidth(array('A'=>20,'B' => 20,'C' => 20,'D' => 20,'E' => 20,'F' => 20,'G' => 18,'H' => 18,'I' => 20,'J' => 12,'K' => 12,'L' => 30,'M' => 20,'N' => 20,'O' => 20,'P' => 20,'Q' => 20));
                    $sheet->loadView('BrandFeedback::BrandFeedbackExportTemplate', array('headers' => $headings,'data' => $details)); 
                });
            })->export('xlsx');

        }
        catch(Exception $e) {
                Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return Response::json(array('status' => 404, 'Message' => Lang::get('salesorders.errorInputData')));
        }
    }    
}