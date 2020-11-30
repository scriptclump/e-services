<?php
namespace App\Modules\Dashboard\Controllers;

use DB;
use Log;
use View;
use Cache;
use Input;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Central\Repositories\ReportsRepo;
use App\Central\Repositories\RoleRepo;
use App\Modules\Dashboard\Models\DPRModel;
use Session;
use Excel;
use Redirect;
use App\Modules\Dashboard\Controllers\DashboardController;


class DPRSheetController extends BaseController {

    public function __construct() {
        parent::__construct();
        $this->middleware(function ($request, $next) {
            if (!Session::has('userId')) {
                     Redirect::to('/login')->send();
            }
            $this->reports = new ReportsRepo();
            $this->roleAccess = new RoleRepo();
            $this->dprModel = new DPRModel();

            $this->tomorrow = new \DateTime('tomorrow');
            $this->yesterday = new \DateTime('yesterday');
            $this->dcObj=new DashboardController(); 
            $this->arr = [];
             return $next($request);
        });
    }


    public function indexAction() {
		try {


            $dashboardAccess = $this->roleAccess->checkPermissionByFeatureCode('DPR001');
            
            if( !$dashboardAccess){    
                    return View('Dashboard::index')->with([]); 
            }   
             
            return 
                View('Dashboard::dprSheet')->with([
                        
                    ]);



		} catch (\Exception $e) {
		    Log::info($e->getMessage());
		    Log::info($e->getTraceAsString());	
		}       

	}
  
    public function dcFcList()
    {
      try {
            $data=Input::all();
            $dashboardAccess = $this->roleAccess->checkPermissionByFeatureCode('DPR001');          
           
            $resultData = $this->roleAccess->getAllFcs($data['dc_fc_type']);
            $result = json_decode(json_encode($resultData), True);
            $resreturn='<option value="">Please Select</option>';
            foreach ($result as $data)
             {
               $resreturn.='<option value="'.$data['bu_id']. '"> '.$data['display_name'].'</option>';
             }
         
            return Array('status'=>200,'message'=>'success','res'=>$resreturn);          

          } catch (\Exception $e) {
              Log::info($e->getMessage());
              Log::info($e->getTraceAsString());  
          }  
    }


    public function excelDprReports() {
        try {

            $dashboardAccess = $this->roleAccess->checkPermissionByFeatureCode('DPR001');

            $source = 'uploads/dpr_sheets/DPR_Draft.xlsx';   
            $datefilters['filter_date']=Input::get('filter_date');

            if($datefilters['filter_date']=='custom')
            {
              $fromDate = Input::get('fromDate');
              $toDate = Input::get('toDate');
              $flag=1;    
            }elseif($datefilters['filter_date']=='quarter'){
              $toDate=date('Y-m-d');
              $fromDate=date("Y-m-d", strtotime("-3 months"));
              $flag=4;
            }else{
              $dateranges=$this->dcObj->getDateRange($datefilters);
              $fromDate=$dateranges["fromDate"];
              $toDate=$dateranges["toDate"];

                   if($datefilters['filter_date']=='wtd')
                   {
                      $flag=2;
                   }elseif($datefilters['filter_date']=='mtd')
                   {
                      $flag=3;
                   }elseif($datefilters['filter_date']=='ytd')
                   {
                      $flag=5;
                   }
            }
            
            $whid = Input::get('whid');
            $dc_fc_flag = Input::get('flag');
            $cellvalue=array();
            if($whid=='' || $fromDate=='') {
                return Redirect::to('/dprsheet');
            }

           
            $DataSet = $this->dprModel->getDprData($whid,$fromDate,$toDate,$flag,$dc_fc_flag); 
            $DataSet = json_decode(json_encode($DataSet),true);
            $File_Name = isset($DataSet[0]['DC_Code']) ? $DataSet[0]['DC_Code'] : 'DPR_Sheet';

           /* if(isset($DataSet) && !empty($DataSet)){

                               
                Excel::load($source, function($doc) use ($DataSet,$fromDate)
                {

                  for($i=0,$k=2,$a=1;$i<count($DataSet);$i++,$k++,$a++){

                    $DPR_Result = $DataSet[$i];                             
                    if($i==0)
                    {
                     $j=$i;
                    }
                  // Getting the cell value for excel input because it changes based on dataset input...             
                    $cellvalue=$this->getKeys();
                    $sheet = $doc -> getSheet(0);
                    if($i==0){
                    $sheet-> setCellValue($cellvalue[$i+1].($j+2),$DPR_Result['Location']);
                    $sheet-> setCellValue($cellvalue[$i+1].($j+3),$DPR_Result['DC_Code']);
                    $sheet-> setCellValue($cellvalue[$i+1].($j+4),$DPR_Result['Owner_Name']);

                    $sheet-> setCellValue($cellvalue[$i+1].($j+13),$DPR_Result['DFC_Margin(%)']);
                    $sheet-> setCellValue($cellvalue[$i+1].($j+14),$DPR_Result['DC_Margin(%)']);
                    $sheet-> setCellValue($cellvalue[$i+1].($j+15),$DPR_Result['FC_Margin(%)']);
                    $sheet-> setCellValue($cellvalue[$i+1].($j+20),$DPR_Result['Sale_Start_Date']);
                     }

                    $sheet = $doc -> getSheet(1);                    
                    $sheet-> setCellValue($cellvalue[$i+$a].($j+1),date('M Y',strtotime($fromDate)));
                    $sheet-> setCellValue($cellvalue[$i+$k].($j+4),$DPR_Result['Outlets_Onboarded(#)']);
                    $sheet-> setCellValue($cellvalue[$i+$k].($j+6),$DPR_Result['Three_Order_Outlets(#)']);
                    $sheet-> setCellValue($cellvalue[$i+$k].($j+8),$DPR_Result['Orders_Count(#)']);

                    $sheet-> setCellValue($cellvalue[$i+$k].($j+9),$DPR_Result['Self_Orders_Share(%)']);
                    $sheet-> setCellValue($cellvalue[$i+$k].($j+10),$DPR_Result['SKU_Coverage(#)']);

                    $sheet-> setCellValue($cellvalue[$i+$k].($j+12),$DPR_Result['Booked_Sales(₹)']);
                    $sheet-> setCellValue($cellvalue[$i+$k].($j+13),$DPR_Result['Delivered_Sales(₹)']);
                    $sheet-> setCellValue($cellvalue[$i+$k].($j+17),$DPR_Result['Delivered GM(₹)']);
                     
                    
                   } 

                })->setFilename($File_Name)->download('xlsx');
               

            } else {

                Excel::load($source, function($doc){})->download('xlsx');
            }

           */
            if(isset($DataSet[0]) && !empty($DataSet[0])){

                $DPR_Result = $DataSet[0];

                Excel::load($source, function($doc) use ($DPR_Result,$fromDate)
                {
                    $sheet = $doc -> getSheet(0);
                    $sheet-> setCellValue('B2',$DPR_Result['Location']);
                    $sheet-> setCellValue('B3',$DPR_Result['DC_Code']);
                    $sheet-> setCellValue('B4',$DPR_Result['Owner_Name']);

                    $sheet-> setCellValue('B13',$DPR_Result['DFC_Margin(%)']);
                    $sheet-> setCellValue('B14',$DPR_Result['DC_Margin(%)']);
                    $sheet-> setCellValue('B15',$DPR_Result['FC_Margin(%)']);
                    $sheet-> setCellValue('B20',$DPR_Result['Sale_Start_Date']);

                    $sheet = $doc -> getSheet(1);
                    $sheet-> setCellValue('B1',date('M Y',strtotime($fromDate)));
                    $sheet-> setCellValue('C4',$DPR_Result['Outlets_Onboarded(#)']);
                    $sheet-> setCellValue('C6',$DPR_Result['Three_Order_Outlets(#)']);
                    $sheet-> setCellValue('C8',$DPR_Result['Orders_Count(#)']);

                    $sheet-> setCellValue('C9',$DPR_Result['Self_Orders_Share(%)']);
                    $sheet-> setCellValue('C10',$DPR_Result['SKU_Coverage(#)']);

                    $sheet-> setCellValue('C12',$DPR_Result['Booked_Sales(₹)']);
                    $sheet-> setCellValue('C13',$DPR_Result['Delivered_Sales(₹)']);
                    $sheet-> setCellValue('C17',$DPR_Result['Delivered GM(₹)']);

                }) ->setFilename($File_Name)-> download('xlsx');


            } else {

                Excel::load($source, function($doc){}) -> download('xlsx');
            }





        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

   public function getKeys()
   {
          $names=['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
          $index=0;
          $keysnumber=array();
          $keys=array();
          $keysnumber=$this->nextnumber($names[$index++],$names,$index);
          $keysnumber =array_merge($names,$keysnumber);
              
          return $keysnumber;

          
    }
   
    public function nextnumber($key,$names,$index)
    {       
            for($i=0;$i<count($names);$i++)
            {
               $keysnumber[$i]=$key.$names[$i].',';
               $this->arr[] =  $key.$names[$i].'  ';
            }
            if($index!=count($names))
            {                 
                $this->nextnumber($names[$index++],$names,$index);
                 
             }
                return $this->arr;
        
     }


}   