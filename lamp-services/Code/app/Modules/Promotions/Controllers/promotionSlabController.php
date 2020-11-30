<?php
/*
FileName :promotionSlabController
Author   :eButor
Description :
CreatedDate :29/May/2017
*/
//defining namespace
namespace App\Modules\Promotions\Controllers;

//loading namespaces
use App\Http\Controllers\BaseController;
use App\Modules\Promotions\Controllers\commonIgridController;
use App\Modules\Promotions\Models\promotionSlabModel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Central\Repositories\RoleRepo;
use Input;
use Log;
use Redirect;
use Excel;
use Carbon\Carbon;
use DateTime;

class promotionSlabController extends BaseController{
    
	//calling model 
    public function __construct() {
        $this->objCommonGrid = new commonIgridController();
        $this->slab_dashboard = new promotionSlabModel();
        
    }
    /**
     * [slabReportdashboard Slab promotion details]
     * @return [view] [Slab report dashboard]
     */
    public function slabReportdashboard(){

        try{
            $breadCrumbs = array('Home' => url('/'),'SlabReport' => '#');
            return view('Promotions::promotionSlabReport');
          }
        catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            Redirect::to('/')->send();

          }
    }
    /**
     * [slabReportData description]
     * @param  Request $request [ig grid i/p]
     * @return [view]           [Slab report dashboard]
     */
    public function slabReportData(Request $request){

        try{
        
            $makeFinalSql = array();
            $filter = $request->input('%24filter');
            if( $filter=='' ){
                $filter = $request->input('$filter');
            }        
            // make sql for HUB name
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("HUBName", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }
             // make sql for Soname information
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("SOName", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }
            // make sql for Beat Name
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("BeatName", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }
            // make sql for Area Name
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("AreaName", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }
            // make sql for shop name
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("shop_name", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }
            // make sql for order date 
            // Keeping different variable for date to keep order date as a default param for search
            $sqlForOrderDate = "";
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("order_date", $filter,true);
            if($fieldQuery!=''){
                $sqlForOrderDate = $fieldQuery;
            }

            // make sql for  Product Name
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("ProdutName", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }

             // make sql for Product Sku
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("ProductSKU", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }

             // make sql for MRP
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("mrp", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }

             // make sql for Order Qty
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("OrderQty", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }
             // make sql for ESU Qty

            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("ESU_qty", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }
            // make sql for Slab RATES 

            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("Slabrates", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }

             // make sql for promotions starts on
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("total", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }

            // Process data for Status Filter
            $statusFilter = '';
            if($request->input('filterStatusType')!='all'){
                $statusFilter = $request->input('filterStatusType')!='' ? "status='".$request->input('filterStatusType')."'" : '';
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
            return $this->slab_dashboard->slabReportDetails($makeFinalSql, $orderBy, $page, $pageSize, $sqlForOrderDate);

        }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            Redirect::to('/')->send();
        }
    }
    /**
     * [slabExceldataDownload Excel slabs download]
     * @param  Request $request [from date, to data]
     * @return [excel]           [Download excel sheet]
     */
    public function slabExceldataDownload(Request $request){
        
        try{
            $start_date =$request->input("from_date");
            $end_date =$request->input("to_date");

            // Taking dynamic date as per the selection
            if($start_date !="" && $end_date !=""){
                $FinalDate = 'BETWEEN '."'$start_date'".' AND '."'$end_date'".'';
            }elseif ($start_date !="" && $end_date == "") {
                $FinalDate = ' = ' ."'$start_date'";
            }elseif ($end_date !="" && $start_date == "") {
                $FinalDate = ' = '."'$end_date'";
            }elseif ($start_date =="" && $end_date == "") {
                $FinalDate = " = DATE(NOW())";
            }

            $headers_line_one = array('Promotion Information','','','','Customer Information','','', '', '', '', '', '','Order Information','','','','','','', '', '', '', '', '');

             $headers = array('Promotion Id','Promotion Start','Promotion End','Promotion Status','HUB NAME','SO Name','So Number','Area Name','Beat Name','Shop Name','Retailer Code','Order Code','Order Date','Product Name','Article Number','MRP','CFC Qty','ESU Qty','Slab Rate','Order Qty','Order Value','Order Status');

            $exceldata = json_decode($this->slab_dashboard->getDataAsPerQueryForSlabReport($FinalDate));

            $mytime = Carbon::now();

            Excel::create('Slab Report Sheet-'.$mytime->toDateTimeString(), function($excel) use($headers,$headers_line_one, $exceldata) 
            {
                $excel->sheet("slabReport", function($sheet) use($headers,$headers_line_one, $exceldata)
                {
                    $sheet->loadView('Promotions::downloadSlabReport', array('headers' => $headers, 'headers_line_one' =>$headers_line_one, 'data' => $exceldata)); 
                });
            })->export('xlsx');

        }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            Redirect::to('/')->send();
        }
    }	
}