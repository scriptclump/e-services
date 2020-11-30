<?php
/*
FileName : uploadCashback
Author   : eButor
Description :
CreatedDate : 8/Aug/2017
*/
//defining namespace
namespace App\Modules\Pricing\Controllers;

//loading namespaces
use App\Http\Controllers\BaseController;
use App\Modules\Pricing\Controllers\pricingDashboadController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Config;
use App\Modules\Pricing\Models\uploadSlabProductsModel;
use App\Modules\Pricing\Models\pricingDashboardModel;


use Excel;
use Carbon\Carbon;
use Session;
use Notifications;
use UserActivity;
use Mail;

class uploadCashbackController extends BaseController{

    private $product_slab_details='';
    private $objPriginController='';

    //calling model 
    public function __construct() {
        $this->middleware(function ($request, $next) {
            if (!Session::has('userId')) {
                     Redirect::to('/login')->send();
            }
            return $next($request);
        });
        $this->objPriginController = new pricingDashboadController();
        $this->product_slab_details = new uploadSlabProductsModel();
        $this->cashback_details = new pricingDashboardModel();
    }


	public function readExcel($path) {
        try {
            $headerRowNumber = 1;
            Config::set('excel.import.startRow', $headerRowNumber);
            Config::set('excel.import.heading', 'numeric');
            $cat_data = Excel::selectSheetsByIndex(0)->load($path, function($reader) {
                        
                    })->first();
            $headerRowNumber = 1;
            Config::set('excel.import.startRow', $headerRowNumber);
            Config::set('excel.import.heading', 'slugged');
            $prod_data = Excel::selectSheetsByIndex(0)->load($path, function($reader) {
                        
                    })->get();
            $data['cat_data'] = $cat_data;
            $data['prod_data'] = $prod_data;
            return $data;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
        }
    }

    // Create the excel file as per the data selection
    public function downloadCashbackExcelWithData(Request $request)
    {
        $mdl_manufac = $request->input("mdl_manufac");
        $mdl_brand = $request->input("mdl_brand");
        $product_star = $request->input("product_star");
        $mdl_products = $request->input("mdl_products");
        $mdl_state = $request->input("mdl_state");
        $mdl_custgroup = $request->input("mdl_custgroup");


        $selectionQueryOuter = array();
        $selectionQueryInner = array();

        if($mdl_manufac!='' && $mdl_manufac!='all'){
            $selectionQueryOuter[] = "prd.manufacturer_id = '".$mdl_manufac."'";
        }
        if($mdl_brand!='' && $mdl_brand!='all'){
            $selectionQueryOuter[] = "prd.brand_id = '".$mdl_brand."'";
        }
        // if($product_star!='' && $product_star!='all'){
        //     $selectionQueryOuter[] = "prd.category_id = '".$product_star."'";
        // }
        if($mdl_products!='' && $mdl_products!='all'){
            $selectionQueryOuter[] = "prd.product_id = '".$mdl_products."'";
        }
        if($mdl_state!='' && $mdl_state!='all'){
            $selectionQueryInner[] = "pp.state_id = '".$mdl_state."'";
        }
        if($mdl_custgroup!='' && $mdl_custgroup!='all'){
            $selectionQueryInner[] = "pp.customer_type = '".$mdl_custgroup."'";
        }

        $mytime = Carbon::now();
        $headers = array('PRODUCT_ID','SKU','PRODUCT_TITLE', 'STATE','CUSTOMER_GROUP','WAREHOUSE','START_DATE(m/d/y)','END_DATE(m/d/y)','PRODUCT_STAR','BENIFICIARY','QUANTITY','CASHBACK','IS_PERCENT');
        $headers_second_page = array('STATE','CUSTOMER_GROUP','BENIFICIARY','WAREHOUSE','PRODUCT_STAR');

        $exceldata = json_decode($this->product_slab_details->getDataAsPerQuery($selectionQueryOuter, $selectionQueryInner), true);
        $stateDet = json_decode($this->product_slab_details->getAllState(), true);
        $customerDet = json_decode($this->product_slab_details->getAllCustomerType(), true);
        $BenificiaryDet = json_decode($this->cashback_details->getBenificiaryNameForExcel(), true);
        $ProductStarDet = json_decode($this->cashback_details->getProductStarsExcel(), true);

        $WarehouseDet = json_decode($this->cashback_details->getWarehousesForExcel(), true);
        $loopCounter = 0;
        $exceldata_second = array();
        foreach($stateDet as $val){
            $exceldata_second[$loopCounter]['state'] = $val['ItemName'];
            $exceldata_second[$loopCounter]['customer'] = isset($customerDet[$loopCounter]) ? $customerDet[$loopCounter]['ItemName'] : '';
            $exceldata_second[$loopCounter]['benificiary'] = isset($BenificiaryDet[$loopCounter]) ? $BenificiaryDet[$loopCounter]['name'] : '';
            $exceldata_second[$loopCounter]['warehouses'] = isset($WarehouseDet[$loopCounter]) ? $WarehouseDet[$loopCounter]['lp_wh_name'] : '';

            $exceldata_second[$loopCounter]['product_star'] = isset($ProductStarDet[$loopCounter]) ? $ProductStarDet[$loopCounter]['master_lookup_name'] : '';
            $loopCounter++;
        }
        
        $dummyData = array('priceExcelName'=>'Cashback Template Sheet-'.$mytime->toDateTimeString());
        UserActivity::userActivityLog('Cashback',$dummyData, 'Cashback Excel downloaded by user');

        Excel::create('Cashback Template Sheet-'.$mytime->toDateTimeString(), function($excel) use($headers, $exceldata, $headers_second_page, $exceldata_second) 
        {

            $excel->sheet("Cashback", function($sheet) use($headers, $exceldata)
            {
                $sheet->loadView('Pricing::cashbackTemplate', array('headers' => $headers, 'data' => $exceldata)); 
            });

            $excel->sheet("State_and_Customer_Data", function($sheet) use($headers_second_page, $exceldata_second)
            {
                $sheet->loadView('Pricing::CashBackstateAndCusomerSampleTemplate', array('headers' => $headers_second_page, 'data' => $exceldata_second)); 
            });
        })->export('xlsx');


    }

}