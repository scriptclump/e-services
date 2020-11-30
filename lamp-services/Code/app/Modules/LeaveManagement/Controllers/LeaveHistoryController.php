<?php

namespace App\Modules\LeaveManagement\Controllers;

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
use \App\Modules\LeaveManagement\Models\LeaveHistory;
use App\Modules\ApprovalEngine\Controllers\commonIgridController;


class LeaveHistoryController extends BaseController
{
	protected $LeaveObj;
    protected $roleAccess;


    public function __construct(RoleRepo $roleAccess, LeaveHistory $LeaveObj)
    {
        try{
            parent::Title(trans('dashboard.dashboard_title.company_name').' - '.trans('leavehistory.leavehistory_heads.title'));
                parent::__construct();
            $this->middleware(function ($request, $next) {
                if(!$this->roleAccess->checkPermissionByFeatureCode('LH01'))
                {
                    return Redirect::to('/');
                }
                return $next($request);
            });
            $this->LeaveObj = $LeaveObj;
            $this->roleAccess = $roleAccess;
        }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return "Sorry Invalid Login. Please check log for More Details";
        }
    }

    public function leavehistory()
    {
        try{
            if(!$this->roleAccess->checkPermissionByFeatureCode('LH01'))
            {
            return Redirect::to('/');
            }
            parent::Breadcrumbs(array('Home' => '/', 'Leave History' => '#'));
                return view('LeaveManagement::LeaveManagement');
        }
        catch (\ErrorException $ex)
        {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return "Sorry Invalid Login. Please check log for More Details";
        }

    }
    public function getList(Request $request)
    {
        $makeFinalSql = array();
        $filter = $request->input('%24filter');
        if( $filter == ''){
            $filter = $request->input('$filter');
        }
        $this->objCommonGrid=new commonIgridController();

        //make sql for from_date
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("from_date",$filter,true);
        if($fieldQuery != ''){
            $makeFinalSql[] = $fieldQuery;
        }

        //make sql for to_date
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("to_date",$filter,true);
        if($fieldQuery != ''){
            $makeFinalSql[] = $fieldQuery;
        }

        //make sql for emp_code
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("emp_ep_id",$filter,false);
        if($fieldQuery != ''){
            $makeFinalSql[] = $fieldQuery;
        }

        //make sql for emp_name
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("emp_name",$filter,false);

        if($fieldQuery != ''){
            $makeFinalSql[] = $fieldQuery;
        }
        //make sql for emp_type
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("emp_type",$filter,false);
        if($fieldQuery != ''){
            $makeFinalSql[] = $fieldQuery;
        }
        //make sql for contact_number
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("contact_number",$filter,false);
        if($fieldQuery != ''){
            $makeFinalSql[] = $fieldQuery;
        }

        //make sql for emergency_mail 
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("emergency_mail",$filter,false);
        if($fieldQuery != ''){
            $makeFinalSql[] = $fieldQuery;
        }

        //make sql for no_of_days
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("no_of_days",$filter,false);
        if($fieldQuery != ''){
           $makeFinalSql[] = $fieldQuery;
        }

        //make sql for leave_type
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("leave_type",$filter,false);
        if($fieldQuery != ''){
            $makeFinalSql[] = $fieldQuery;
        }

        //make sql for reason 
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("reason",$filter,false);
        if($fieldQuery != ''){
            $makeFinalSql[] = $fieldQuery;
        }

        //make sql for status
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("status",$filter,false);
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
        $result = $this->LeaveObj->getLeavehistoryList($makeFinalSql, $orderBy, $page, $pageSize);
        return $result;
    }
}


?>