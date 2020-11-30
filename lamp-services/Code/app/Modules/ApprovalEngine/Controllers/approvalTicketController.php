<?php
/*
FileName : approvalCartOperationController
Author   :eButor
Description :Approval workflow related functions are here
CreatedDate :28/jul/2016
*/
//defining namespace
namespace App\Modules\ApprovalEngine\Controllers;

//loading namespaces
use App\Http\Controllers\BaseController;
use App\Modules\ApprovalEngine\Controllers\commonIgridController;
use App\Modules\ApprovalEngine\Models\approvalTicketModel;
use Illuminate\Http\Request;
use Input;
use Redirect;
use Session;
use Notifications;
use Log;

class approvalTicketController extends BaseController{

    private $objCommonGrid = '';


     public function __construct() {
        $this->middleware(function ($request, $next) {
            if (!Session::has('userId')) {
                Redirect::to('/login')->send();
            }
            return $next($request);
        });
        // get common controller reff
        $this->objCommonGrid = new commonIgridController();
        $this->objTicketModel = new approvalTicketModel();
    }

    public function approvalTicketIndex(){

        try{
            $breadCrumbs = array('Home' => url('/'),'Approval Ticket' => '#');
            parent::Breadcrumbs($breadCrumbs);
            return view('ApprovalEngine::approvalTicketIndex', ['allCount'=>0, 'openCount'=>0, 'closeCount'=>0]);
        }
        catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
          }
    }  

    public function approvalTicketData(Request $request){
        $data = Input::all();
        $DisplayListTab = isset($data['showTab'])?$data['showTab']:"openTicketsTab";
        // Arrange data for pagination
        $makeFinalSqlOuter = array();

        $filter = $request->input('%24filter');
        if( $filter=='' ){
            $filter = $request->input('$filter');
        }


        // make sql for Assigned On
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("created_at", $filter, true);
        if($fieldQuery!=''){
            $makeFinalSqlOuter[] = $fieldQuery;
        }

        // make sql for version name
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("TicketDetails", $filter);
        $fieldQuery =str_replace('TicketDetails','CONCAT(TicketType'.','.'awf_for_id)', $fieldQuery);
        if($fieldQuery!=''){
            $makeFinalSqlOuter[] = $fieldQuery;
        }

        // make sql for version name
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("TicketNumber", $filter);
        if($fieldQuery!=''){
            $makeFinalSqlOuter[] = $fieldQuery;
        }

        // make sql for version name
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("TicketPendingOn", $filter);
        if($fieldQuery!=''){
            $makeFinalSqlOuter[] = $fieldQuery;
        }

        // arrange Order By
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
        return $this->objTicketModel->viewAprovalTicketdata($makeFinalSqlOuter, $orderBy, $page, $pageSize, $DisplayListTab);
    }

    // This function will return the number of open ticket for the current user
    public function getUserTicketCount(){
        $role = Session::get('roles');
        if($role!=""){
            $TicketsCount     = $this->objTicketModel->getTicketCount();
            return  json_encode(array(
                "allTicketsCount"=>$TicketsCount['TotalCount'],
                "openTicketsCount" => $TicketsCount['opentickets'],
                "closedTicketsCount" => $TicketsCount['closed'],
               
            ));
        }
    }
    public function approvalTicketHistoryData($type,$historyid){
        
        $data=$this->objTicketModel->historyData($type,$historyid);


        $historyHTML = "";
        $loopCounter = 1;
        $bp = url('uploads/LegalEntities/profile_pics');
        $base_path = $bp."/";   
        $img = $base_path."avatar5.png";

        foreach ($data as $value) {
            
            $timeLineCSS = "";
            if( $loopCounter==count($data) ){
                $timeLineCSS = "timeline_last";
            }else{
                $timeLineCSS="timeline";
            }

            $historyHTML .= '
            <div class="'.$timeLineCSS.'"  >
                <div class="timeline-item timline_style">  
                    <div class="timeline-badge">
                         <img class="timeline-badge-userpic" src="'.$img.'" style = "width:60px;position:relative;z-index:999 !important">
                    </div>
                    <div class="timeline-body">
                        <div class="row">
                            <div class="col-lg-12 " >
                                <div class="col-md-2 changedByName" id = "changedByName" style="word-wrap:break-word !important">'.$value->UserNameLstAction.'
                                    <p>
                                    <span id="recordAddedByName"></span>
                                    </p>
                                </div>
                                <div class="col-md-2" id = "hist_date">'.$value->created_at.'</div>
                                <div class="col-md-2" id="prev_status">'.$value->PreviousStatus.'</div> 
                                <div class="col-md-2" id="Role">'.$value->Condition.'</div>
                                <div class="col-md-2" id="Role">'.$value->CurrentStatus.'</div>
                                <div class="col-md-2" id="comment">'.$value->awf_comment.'</div>
                                
                            </div>             
                        </div>
                    </div>
                </div>
            </div>
            ';
            $loopCounter++;
        }

        $returnDataArray = array(
            'historyHTML' => $historyHTML
        );

        $data = json_decode(json_encode($returnDataArray),true);
       
        return $data;
    }
}

?>