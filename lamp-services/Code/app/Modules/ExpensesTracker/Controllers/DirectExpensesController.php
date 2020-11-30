<?php
namespace App\Modules\ExpensesTracker\Controllers;
use App\Http\Controllers\BaseController;
use App\Modules\ExpensesTracker\Controllers\commonIgridController;
use App\Modules\ExpensesTracker\Models\expensesTrackModel;
use App\Modules\ExpensesTracker\Models\expensesAPIModel;
use App\Modules\ExpensesTracker\Models\directexpensesModel;
use App\Central\Repositories\ProductRepo;
use App\Central\Repositories\RoleRepo;
use Log;
use Illuminate\Http\Request;
use Response;
use Redirect;
use Session;
use Input;
use Excel;
use Carbon\Carbon;

date_default_timezone_set('Asia/Kolkata');


class DirectExpensesController extends BaseController {

	private $objExpensesTracker='';
	private $objCommonGrid = '';
	private $makeFinalSql = '';
	private $sqlForSession = '';
	private $objAPIModel = '';

	public function __construct(){
        $this->_roleRepo = new RoleRepo();
        $this->objExpensesTracker = new expensesTrackModel();
        $this->objCommonGrid = new commonIgridController();
        $this->_roleRepo = new RoleRepo();
        $this->objAPIModel = new expensesAPIModel();
        $this->objDirectExp = new directexpensesModel();

        $this->objPushNotification      = new ProductRepo();

        try {
           parent::Title('Direct Expenses Tracker');
            $this->middleware(function ($request, $next) {
                if (!Session::has('userId'))
                {
                    return Redirect::to('/');
                }
                $access = $this->_roleRepo->checkPermissionByFeatureCode('EXP001');
                if (!$access && Session::get('legal_entity_id')!=0) {
                    Redirect::to('/')->send();
                    die();
                }
                return $next($request);

                });

            } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            Redirect::to('/')->send();
           }
    }


	// Expenses Tracker DashBoard / Index Controller
	public function directexpensesDashboard(){
		try{
			$breadCrumbs = array('Home' => url('/'),'Expenses' => '#', 'Direct Expenses Dashboard' => '#');
			parent::Breadcrumbs($breadCrumbs);


			$getTotals = $this->objDirectExp->getTotalsDb();
			$downloadAccess=1;
  			$dashboardAccess=1;
  			$userAccess=1;

  			if(Session::get('legal_entity_id')!=0){
             $downloadAccess = $this->_roleRepo->checkPermissionByFeatureCode('EXP002');
             $dashboardAccess = $this->_roleRepo->checkPermissionByFeatureCode('EXP003');
             $userAccess = $this->_roleRepo->checkPermissionByFeatureCode('EXP005');
         	}
			return view('ExpensesTracker::Directexpensestrack',['userAccess'=>$userAccess,'downloadAccess'=>$downloadAccess,'dashboardAccess'=>$dashboardAccess,'Totals'=>$getTotals]);
		}
		catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
			Redirect::to('/')->send();
		}
	}

	public function directexpensesTrackerDashboard(Request $request){
		// try{

		$request->session()->put('expGlobalQuery', "");
		$this->makeFinalSql = array();
        $filter = $request->input('%24filter');
        if( $filter=='' ){
            $filter = $request->input('$filter');
        }

        $sqlForSession = array();


        // make sql for Expense submitted by
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("SubmittedBy", $filter);
        if($fieldQuery!=''){
            $this->makeFinalSql[] = $fieldQuery;
            $this->sqlForSession[] = $fieldQuery;
        }
        // make sql for BuName
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("BuName", $filter);
        if($fieldQuery!=''){
            $this->makeFinalSql[] = $fieldQuery;
            $this->sqlForSession[] = $fieldQuery;
        }
         // make sql for Advance Total Amount
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("AdvanceTotalAmount", $filter);
        if($fieldQuery!=''){
            $this->makeFinalSql[] = $fieldQuery;
        }

        // make sql for Rem Total Amount
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("RemTotalAmount", $filter);
        if($fieldQuery!=''){
            $this->makeFinalSql[] = $fieldQuery;
            $this->sqlForSession[] = $fieldQuery;
        }
        
        // make sql for Rem Total Balance
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("balance", $filter);
        if($fieldQuery!=''){
            $this->makeFinalSql[] = $fieldQuery;
            $this->sqlForSession[] = $fieldQuery;
        }

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

		$request->session()->put('expGlobalQuery',$this->sqlForSession);

		return $this->objDirectExp->showdirectexpensesDetails($this->makeFinalSql, $orderBy, $page, $pageSize);
		// }
		// catch (\ErrorException $ex) {
		// 	Log::error($ex->getMessage());
		// 	Log::error($ex->getTraceAsString());
		// 	Redirect::to('/')->send();
		// }
	}
    
    public function getDirectExpensesData($submited_by_id){

        $expensesData = $this->objDirectExp->getDirectExpenses($submited_by_id);

        $historyHTML = "";
        $loopCounter = 1;
        $balance = 0;
        $Count = count($expensesData); 
        $Totaldata = "";
        $AdvanceAmountTot = 0;
        $RemAmountTot = 0;
        foreach ($expensesData as $value) {

                $Direct =  ($value->is_direct_advance == 1) ? " (Direct)" : "";
                if($value->exp_req_type == '122001'){
                    // for balance update 
                    $balance += $value->AdvanceAmount;
                    // for total advance amount
                    $AdvanceAmountTot += $value->AdvanceAmount; 
                }else if($value->exp_req_type == '122002'){
                    // for balance update
                    $balance -= $value->RemAmount;
                    // for total reiumbersement balance
                    $RemAmountTot += $value->RemAmount; 
                }

                // for last total row in view
                if($Count == $loopCounter){
                    $Totaldata ='<tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><b>Totals</b></td>
                                        <td align="right"><b>'.$AdvanceAmountTot.'</b></td>
                                        <td align="right"><b>'.$RemAmountTot.'</b></td>
                                        <td align="right"><b>'.$balance.'</b></td>
                                </tr>'; 
                }

                $historyHTML .= '<tr>
                                        <td>'.$value->ExpSubmittedDate.'</td>
                                        <td>'.$value->exp_code.'</td>
                                        <td>'.$value->exp_subject.'</td>
                                        <td>'.$value->RequestFor. $Direct .'</td>
                                        <td align="right">'.$value->AdvanceAmount.'</td>
                                        <td align="right">'.$value->RemAmount.'</td>
                                        <td align="right">'.$balance.'</td>
                                </tr>'.$Totaldata.'';

                $loopCounter++;
            }

            $returnDataArray = array(
                'historyHTML'   => $historyHTML,
                'expensesData' => $expensesData,
                'RemAmountTot'=>'&#8377; '.$RemAmountTot,
                'AdvanceAmountTot'=>'&#8377; '.$AdvanceAmountTot,
                'balance'=>'&#8377; '.$balance
            );
            return $returnDataArray;
    }

    public function downloadDirectExpensesData(Request $request){

            $submited_by_id = $request->input('submited_by_id');
            $mytime = Carbon::now();

            $headers_two = array('Date','Exp Code', 'Description', 'Trans Type', 'Advance (&#8377;)', 'Reiumbersement (&#8377;)', 'Balance (&#8377;)');

            $expensesData = $this->objDirectExp->getDirectExpenses($submited_by_id);
            $excelHTML = "";
            $loopCounter = 1;
            $balance = 0;
            $Count = count($expensesData); 
            $Totaldata = "";
            $AdvanceAmountTot = 0;
            $RemAmountTot = 0;
            $SubmittedBy = "";
            foreach ($expensesData as $value) {
                $SubmittedBy = $value->SubmittedByName;
                $Direct =  ($value->is_direct_advance == 1) ? " (Direct)" : "";
                if($value->exp_req_type == '122001'){
                    // for balance update 
                    $balance += $value->AdvanceAmount;
                    // for total advance amount
                    $AdvanceAmountTot += $value->AdvanceAmount; 
                }else if($value->exp_req_type == '122002'){
                    // for balance update
                    $balance -= $value->RemAmount;
                    // for total reiumbersement balance
                    $RemAmountTot += $value->RemAmount; 
                }

                // for last total row in view
                if($Count == $loopCounter){
                    $Totaldata ='<tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><b>Totals</b></td>
                                        <td align="right"><b>'.$AdvanceAmountTot.'</b></td>
                                        <td align="right"><b>'.$RemAmountTot.'</b></td>
                                        <td align="right"><b>'.$balance.'</b></td>
                                </tr>'; 
                }

                $excelHTML .= '<tr>
                                        <td>'.$value->ExpSubmittedDate.'</td>
                                        <td>'.$value->exp_code.'</td>
                                        <td>'.$value->exp_subject.'</td>
                                        <td>'.$value->RequestFor. $Direct .'</td>
                                        <td align="right">'.$value->AdvanceAmount.'</td>
                                        <td align="right">'.$value->RemAmount.'</td>
                                        <td align="right">'.$balance.'</td>
                                </tr>'.$Totaldata.'';
                $loopCounter++;
                
            }
            $headers_one = array('','','','Ledger Details of '.$SubmittedBy.'','','','');

            Excel::create('Direct Expenses - '.$SubmittedBy.' -'.$mytime->toDateTimeString(), function($excel) use($headers_one,$headers_two, $excelHTML){

                $excel->sheet("Direct Expenses Details", function($sheet) use($headers_one,$headers_two, $excelHTML)
                {
                    $sheet->loadView('ExpensesTracker::directexpensesexcel', array('headers_one' => $headers_one,'headers_two'=>$headers_two, 'data' => $excelHTML)); 
                });

            })->export('xlsx');
        
    }
}