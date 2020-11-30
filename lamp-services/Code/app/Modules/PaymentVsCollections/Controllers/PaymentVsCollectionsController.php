<?php
namespace App\Modules\PaymentVsCollections\Controllers;

use DB;
use Log;
use View;
use Cache;
use Input;
use Session;
use Redirect;
use Excel;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Modules\Assets\Controllers\commonIgridController;
use App\Central\Repositories\RoleRepo;


class PaymentVsCollectionsController extends BaseController {

    public function __construct() {
        $this->roleAccess = new RoleRepo();
        $this->objCommonGrid = new commonIgridController();
        parent::Title('Ebutor - PaymentVsCollection Report');
        
    }

    /**
    * The GET method for the Index Dashboard Grid
    */
    public function index() {
            // Code to Check weather the User has  Page Access or not
            $gridAccess = $this->roleAccess->checkPermissionByFeatureCode('PVSC01');
            if(!$gridAccess){
                return Redirect::to('/');
            }else {
                return 
                View('PaymentVsCollections::index')->with([
                ]);
            }    
    }


     //used for date filter to get toand from date range
     public function getDateRange($inputDate='')
     {
         $fromDate = date('Y-m-d');
         $toDate = date('Y-m-d');
         try {
            // Converting the Date format from "dd/mm/yyyy" -to- "yyyy-mm-dd";
            if(isset($inputDate['PayfromDate']) and !empty($inputDate['PayfromDate'])){
                $fromDateSubArr = explode('/', $inputDate['PayfromDate']);
                $PayNewFromDate = $fromDateSubArr[2]."-".$fromDateSubArr[1]."-".$fromDateSubArr[0];
            }else
                $PayNewFromDate = date('Y-m-d');
            
            if(isset($inputDate['PaytoDate']) and !empty($inputDate['PaytoDate'])){
                $toDateSubArr = explode('/', $inputDate['PaytoDate']);
                $PayNewToDate = $toDateSubArr[2]."-".$toDateSubArr[1]."-".$toDateSubArr[0];
            }else
            $PayNewToDate = date('Y-m-d');

            //collection date formating 
            if(isset($inputDate['CollectfromDate']) and !empty($inputDate['CollectfromDate'])){
                $fromDateSubArr = explode('/', $inputDate['CollectfromDate']);
                $CollectNewFromDate = $fromDateSubArr[2]."-".$fromDateSubArr[1]."-".$fromDateSubArr[0];
            }else
                $CollectNewFromDate = date('Y-m-d');
            
            if(isset($inputDate['CollecttoDate']) and !empty($inputDate['CollecttoDate'])){
                $toDateSubArr = explode('/', $inputDate['CollecttoDate']);
                $CollectNewToDate = $toDateSubArr[2]."-".$toDateSubArr[1]."-".$toDateSubArr[0];
            }else
            $CollectNewToDate = date('Y-m-d');


            $PaymentFromDate =  $PayNewFromDate;
            $PaymentToDate =  $PayNewToDate;
            $CollectFromDate =  $CollectNewFromDate;
            $CollectToDate =  $CollectNewToDate;
         } catch (\Exception $e) {
             Log::info($e->getMessage());
             Log::info($e->getTraceAsString());
             return ["PayFromDate" =>  $PaymentFromDate, "PayToDate" => $PaymentToDate , "CollectFromDate" => $CollectFromDate , "CollectToDate" => $CollectToDate];
         }
         return ["PayFromDate" =>  $PaymentFromDate, "PayToDate" => $PaymentToDate , "CollectFromDate" => $CollectFromDate , "CollectToDate" => $CollectToDate];
     }
 

    public function getPaymentsDashBoardGridData(Request $request,$userId = 0,$flag=1)
    {
        try{
            $data = Input::all();
            $datesArr = $this->getDateRange($data);
            $PayFromDate = $datesArr["PayFromDate"];
            $PayToDate = $datesArr["PayToDate"];
            $CollectFromDate =  $datesArr["CollectFromDate"];
            $CollectToDate =  $datesArr["CollectToDate"];
           
            $makeFinalSql = array();            
            $filter = $request->input('%24filter');
            if( $filter=='' ){
                $filter = $request->input('$filter');
            }
            // make sql for  state name
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("State", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }
             // make sql for city name
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("City", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }

            // make sql for warehouse_name
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("warehouse_name", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }

             // make sql for payment_total
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("payment_total", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }

            //invoice_total filter 
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("invoice_total", $filter,true);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }

            
            //delivered_total filter 
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("net_sales", $filter,true);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }

            //return_total name
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("return_total", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
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

            if($orderBy!=''){
                $orderBy = ' ORDER BY ' . $orderBy;
            }

            $sqlWhrCls = '';
            $countLoop = 0;
            foreach ($makeFinalSql as $value) {
                if( $countLoop==0 ){
                    $sqlWhrCls .= ' AND ' . $value;
                }elseif(count($makeFinalSql)==$countLoop ){
                    $sqlWhrCls .= $value;
                }else{
                    $sqlWhrCls .= ' AND ' .$value;
                }
                $countLoop++;
            }

            $response = DB::select(DB::raw("CALL getPaymentVSCollections('".$PayFromDate."','".$PayToDate."','".$CollectFromDate."','".$CollectToDate."')"));
            //  echo "CALL getPaymentVSCollections('".$PayFromDate."','".$PayToDate."','".$CollectFromDate."','".$CollectToDate."')";exit;
            $result = json_decode(json_encode($response),true);
            return array("data" => $result);
            
        }catch(Exception $e) {
            $data=array();
            return array("data" => $data);
      }
    }

       /*
     * download po details() method is used to Download All sales details
     * @param NULL
     * @return Excell
     */ 

    public function getExportDetails(Request $request){
        try {   
            $data = Input::all();
            $datesArr = $this->getDateRange($data);
            $PayFromDate = $datesArr["PayFromDate"];
            $PayToDate = $datesArr["PayToDate"];
            $CollectFromDate =  $datesArr["CollectFromDate"];
            $CollectToDate =  $datesArr["CollectToDate"];
            $response = DB::select(DB::raw("CALL getPaymentVSCollections('".$PayFromDate."','".$PayToDate."','".$CollectFromDate."','".$CollectToDate."')"));
            $file_name = 'PaymentVsCollection'.date('Y-m-d-H-i-s').'.xlsx';
               //echo "CALL getPaymentVSCollections('".$PayFromDate."','".$PayToDate."','".$CollectFromDate."','".$CollectToDate."')";exit;
                $query=json_decode(json_encode($response),true);
                Excel::create($file_name, function($excel) use($query) {
                    $excel->sheet('Sheet1', function($sheet) use($query) {
                        $sheet->fromArray($query, null, 'A1', true , true );
                    });
                })->export('xlsx');
                exit;
           
        } 
        catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return Response::json(array('status' => 404, 'Message' => Lang::get('salesorders.errorInputData')));
        }
        
    }


}   