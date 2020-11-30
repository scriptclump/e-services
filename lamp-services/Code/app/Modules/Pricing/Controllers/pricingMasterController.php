<?php
/*
FileName : pricingMasterDashboadController
Author   : eButor
Description :
CreatedDate :
*/
//defining namespace
namespace App\Modules\Pricing\Controllers;

//loading namespaces
use App\Http\Controllers\BaseController;
use App\Modules\ApprovalEngine\Controllers\commonIgridController;
use Illuminate\Http\Request;
use App\Central\Repositories\RoleRepo;
use App\Modules\Pricing\Models\pricingMasterDashboardModel;
use App\Modules\Inventory\Models\Inventory;
use Input;
use Log;
use Session;
use DB;
use Excel;
use Redirect;
use Notifications;
use UserActivity;


class pricingMasterController extends BaseController{

    private $pricingGrid = '';
    private $pricingModel = '';


       public function __construct(RoleRepo $roleAccess){
              $this->pricingGrid = new commonIgridController();
              $this->pricingModel = new pricingMasterDashboardModel();

        try{
            parent::Title(trans('priceMaster_Label.dashboard_title.company_name').' - '.trans('priceMaster_Label.dashboard_heads.heads_title'));
            parent::__construct();
            $this->middleware(function ($request, $next) {
                if (!Session::has('userId')) {
                    return Redirect::to('/');
                }
                return $next($request);
            });
           
            $this->roleAccess = new RoleRepo();
            $this->roleAccess = $roleAccess;
             
            if(!$this->roleAccess->checkPermissionByFeatureCode('PRM001')){
               return Redirect::to('/');
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return "Sorry Invalid Login. Please check log for More Details";
        }
    }

    

     public function Pricemanager()
     {
           
        $this->roleAccess = new RoleRepo();
        $user_id = Session::get('userId');
        $dashboardAccess = $this->roleAccess->checkPermissionByFeatureCode('PRM001');
        $exclude_apob = TRUE;
        $bu_data = $this->roleAccess->getBusinessUnitsByUserid($user_id, $exclude_apob);
        $bu_data =json_decode($bu_data,true);
        if(isset($bu_data['zonedata']))
        {
            $zones=$bu_data['zonedata'];
        }
        if(isset($bu_data['statesdata']))
        {
            $states=$bu_data['statesdata'];
        }
        if(isset($bu_data['dcsData']))
        {
            $dcs=$bu_data['dcsData'];
        }
        if(isset($bu_data['fcsData']))
        {
            $fcs=$bu_data['fcsData'];
        }      
   
        if(!$dashboardAccess){  
             return Redirect::to('/');        
                 
            }  
        $Product_type=$this->pricingModel->getAllProducttype();
        $defaultProduct_type=$this->pricingModel->getDefaultProducttype();
      
        $breadCrumbs = array('Home' => url('/'), 'Pricing' => '#','Price Manager' => '#');
        parent::Breadcrumbs($breadCrumbs);
        return view('Pricing::pricingMasterDashboard')->with([ 'zones' => $zones])
                                                      ->with([ 'states' => $states])
                                                      ->with([ 'dcs' => $dcs])
                                                      ->with([ 'fcs' => $fcs])
                                                      ->with(['Product_type'=> $Product_type])
                                                      ->with(['cust_tp'=>$defaultProduct_type]);
           
     }

     public function PricemanagerData(Request $request)
     {

        $makeFinalSql = array();
        $filter = $request->input('%24filter');
        if( $filter=='' ){
            $filter = $request->input('$filter');
        }
       $bu_id=$request->input('bu_id');
       $cust_tp=$request->input('cust_tp');
        
        // make sql for Warehouse
        $fieldQuery = $this->pricingGrid->makeIGridToSQL("Warehouse", $filter);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }
        // make sql for SKU        
        $fieldQuery = $this->pricingGrid->makeIGridToSQL("SKU", $filter);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }
        // make sql for Product name
        $fieldQuery = $this->pricingGrid->makeIGridToSQL("Product_ID", $filter);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }
        // make sql for state name
        $fieldQuery = $this->pricingGrid->makeIGridToSQL("Manufacturer", $filter,false);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }
        // make sql for customertype
        $fieldQuery = $this->pricingGrid->makeIGridToSQL("Product_Title", $filter);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }
        // make sql for Price
        $fieldQuery = $this->pricingGrid->makeIGridToSQL("Group_ID", $filter);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }

         // make sql for Ptr
        $fieldQuery = $this->pricingGrid->makeIGridToSQL("LAST", $filter);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }

        // make sql for brand name
        $fieldQuery = $this->pricingGrid->makeIGridToSQL("KVI", $filter);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }
        // make sql for manf name
        $fieldQuery = $this->pricingGrid->makeIGridToSQL("SOH", $filter);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }
        // make sql for category name
        $fieldQuery = $this->pricingGrid->makeIGridToSQL("Active", $filter);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }

        // make sql for ELP 
        $fieldQuery = $this->pricingGrid->makeIGridToSQL("CFC", $filter);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }
        // make sql for ELP 
        $fieldQuery = $this->pricingGrid->makeIGridToSQL("ESU", $filter);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }
        // make sql for ELP 
        $fieldQuery = $this->pricingGrid->makeIGridToSQL("MRP", $filter);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }
        // make sql for ELP 
        $fieldQuery = $this->pricingGrid->makeIGridToSQL("PTR", $filter);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }
        // make sql for ELP 
        $fieldQuery = $this->pricingGrid->makeIGridToSQL("PTR_PER", $filter);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }


        $fieldQuery = $this->pricingGrid->makeIGridToSQL("GST_PER", $filter);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }

         $fieldQuery = $this->pricingGrid->makeIGridToSQL("Base_Rate", $filter);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }

        $fieldQuery = $this->pricingGrid->makeIGridToSQL("Base_Rate-Sch_Amt", $filter);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }

        $fieldQuery = $this->pricingGrid->makeIGridToSQL("Net_Rate", $filter);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }

        $fieldQuery = $this->pricingGrid->makeIGridToSQL("ELP", $filter);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }
         $fieldQuery = $this->pricingGrid->makeIGridToSQL("ESP", $filter);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }
        $fieldQuery = $this->pricingGrid->makeIGridToSQL("ELP_PER", $filter);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }
        $fieldQuery = $this->pricingGrid->makeIGridToSQL("Ebutor_Margin_PER", $filter);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }
        // Arrange Grid Sort here
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
        $flag=1;
      
        return $this->pricingModel->PricingMasterDetailsData($makeFinalSql, $orderBy, $page, $pageSize, $bu_id,$cust_tp,$flag);




     }

    public function Exportpricingdata()
    {
        $bu_id= Input::get('bu_id');
        $cust_tp= Input::get('cust_tp');
         if($bu_id =='' || $cust_tp == '')
        {
            return redirect()->to('/');
        }
        else
        {
            $this->inventory = new Inventory(); 
            // $le_wh_id = $this->inventory->getWhByData($bu_id); // If need herirchical data
            $le_wh_id = $this->inventory->getWarehouseID($bu_id);
            //dd($test);

           // dd($le_wh_id);
            /*Adding flag to proc because of sending the wh_id in place of bu_id */
            $flag=2;
            $x=implode(',',$le_wh_id);
             $result=array();
             foreach ($le_wh_id as $index => $le_wh_ids) {
             $result[$le_wh_ids]=$this->pricingModel->getExportPricingMasterData($le_wh_ids,$cust_tp,$flag);
                               
             }
                $DataSet = json_decode(json_encode($result),true);
                 Excel::create('PriceMaster'.date('Y-m-d_H_i_s'), function($excel) use ($DataSet) {
                $excel->setTitle('PriceMaster');
                $excel->setDescription('Price Master Report');
                foreach ($DataSet as $key => $data) {
                    if(count($data) > 0)
                    {
                         $le_wh_name=$this->pricingModel->getWarehouseName($key);
                         $excel->sheet(substr($le_wh_name,0,30), function($sheet) use ($data) {
                            $i=1;
                            foreach ($data as $index => $value)
                            {
                                if(empty($value['ELP']) || $value['ELP'] ==0 ){ $value['ELP']=0;}
                                if($index ==0) {
                                    $sheet->setCellValue('A'.$i,'SKU')->cell('A'.$i, function($color){$color->setBackground('#fcf93d');});
                                    $sheet->setCellValue('B'.$i,'Product_ID')->cell('B'.$i, function($color){$color->setBackground('#fcf93d');});
                                    $sheet->setCellValue('C'.$i,'Manufacturer')->cell('C'.$i, function($color){$color->setBackground('#fcf93d');});
                                    $sheet->setCellValue('D'.$i,'Product_Title')->cell('D'.$i, function($color){$color->setBackground('#fcf93d');});
                                    $sheet->setCellValue('E'.$i,'Group_ID')->cell('E'.$i, function($color){$color->setBackground('#fcf93d');});
                                    $sheet->setCellValue('F'.$i,'LAST')->cell('F'.$i, function($color){$color->setBackground('#fcf93d');});
                                    $sheet->setCellValue('G'.$i,'KVI')->cell('G'.$i, function($color){$color->setBackground('#fcf93d');});
                                    $sheet->setCellValue('H'.$i,'SOH')->cell('H'.$i, function($color){$color->setBackground('#fcf93d');});
                                    $sheet->setCellValue('I'.$i,'Active')->cell('I'.$i, function($color){$color->setBackground('#fcf93d');});
                                    $sheet->setCellValue('J'.$i,'CFC')->cell('J'.$i, function($color){$color->setBackground('#fcf93d');});
                                    $sheet->setCellValue('K'.$i,'ESU')->cell('K'.$i, function($color){$color->setBackground('#fcf93d');});
                                    $sheet->setCellValue('L'.$i,'MRP')->cell('L'.$i, function($color){$color->setBackground('#fcf93d');});
                                    $sheet->setCellValue('M'.$i,'PTR')->cell('M'.$i, function($color){$color->setBackground('#fcf93d');});
                                    $sheet->setCellValue('N'.$i,'PTR Percentage')->cell('N'.$i, function($color){$color->setBackground('#fcf93d');});
                                    $sheet->setCellValue('O'.$i,'GST%')->cell('O'.$i, function($color){$color->setBackground('#fcf93d');});
                                    $sheet->setCellValue('P'.$i,'Base Rate')->cell('P'.$i, function($color){$color->setBackground('#fcf93d');});
                                    $sheet->setCellValue('Q'.$i,'Scheme%')->cell('Q'.$i, function($color){$color->setBackground('#fcf93d');});
                                    $sheet->setCellValue('R'.$i,'Base Rate-Sch Amt')->cell('R'.$i, function($color){$color->setBackground('#fcf93d');});
                                    $sheet->setCellValue('S'.$i,'Net Rate')->cell('S'.$i, function($color){$color->setBackground('#fcf93d');});
                                    $sheet->setCellValue('T'.$i,'Ebutor Margin%')->cell('T'.$i, function($color){$color->setBackground('#fcf93d');});
                                    $sheet->setCellValue('U'.$i,'Net % after PTR')->cell('U'.$i, function($color){$color->setBackground('#fcf93d');});
                                    $sheet->setCellValue('V'.$i,'Extra%')->cell('V'.$i, function($color){$color->setBackground('#fc563d');});
                                    $sheet->setCellValue('W'.$i,'ELP')->cell('W'.$i, function($color){$color->setBackground('#fcf93d');});
                                    $sheet->setCellValue('X'.$i,'ESP')->cell('X'.$i, function($color){$color->setBackground('#fc563d');});
                                    $sheet->setCellValue('Y'.$i,'ELP Percentage')->cell('Y'.$i, function($color){$color->setBackground('#fc563d');});
                                    $sheet->setCellValue('Z'.$i,'State')->cell('Z'.$i, function($color){$color->setBackground('#fcf93d');});
                                    $sheet->setCellValue('AA'.$i,'is_APOB')->cell('AA'.$i, function($color){$color->setBackground('#fc563d');});
                                    $sheet->setCellValue('AB'.$i,'ALL Dcs')->cell('AB'.$i, function($color){$color->setBackground('#fc563d');});
                                    $sheet->setCellValue('AC'.$i,'ALL Fcs')->cell('AC'.$i, function($color){$color->setBackground('#fc563d');});
                                    $sheet->setCellValue('AD'.$i,'ESP (INV)')->cell('AD'.$i, function($color){$color->setBackground('#fcf93d');});
                                    $sheet->setCellValue('AE'.$i,'ELP (ENV)')->cell('AE'.$i, function($color){$color->setBackground('#fcf93d');});
                                    $sheet->setCellValue('AF'.$i,'customer_group')->cell('AF'.$i, function($color){$color->setBackground('#fcf93d');});
                                    $sheet->setCellValue('AG'.$i,'dc_name')->cell('AG'.$i, function($color){$color->setBackground('#fcf93d');});
                                    $i++;
                                }
                                $sheet->setCellValue('A'.$i,$value['SKU']);//SKU
                                $sheet->setCellValue('B'.$i,$value['Product_ID']);//Product_ID
                                $sheet->setCellValue('C'.$i,$value['Manufacturer']);
                                $sheet->setCellValue('D'.$i,$value['Product_Title']);
                                $sheet->setCellValue('E'.$i,$value['Group_ID']);
                                $sheet->setCellValue('F'.$i,$value['LAST']);
                                $sheet->setCellValue('G'.$i,$value['KVI']);
                                $sheet->setCellValue('H'.$i,$value['SOH']);
                                $sheet->setCellValue('I'.$i,$value['Active']);
                                $sheet->setCellValue('J'.$i,$value['CFC']);
                                $sheet->setCellValue('K'.$i,$value['ESU']);
                                $sheet->setCellValue('L'.$i,$value['MRP']);
                                $sheet->setCellValue('M'.$i,$value['PTR']);
                                $sheet->setCellValue('N'.$i,'=(L'.($i).'-M'.($i).')/M'.($i));//PTR%
                                $sheet->setCellValue('O'.$i,$value['GST_PER']);
                                $sheet->setCellValue('P'.$i,'=M'.($i).'/(1+'.'O'.$i.')');//Base_Rate
                                $sheet->setCellValue('Q'.$i,$value['Scheme']);
                                $sheet->setCellValue('R'.$i,'=P'.$i.'-(P'.$i.'*Q'.$i.')');//Base Rate-Sch Amt
                                $sheet->setCellValue('S'.$i,'=(R'.($i).'*O'.($i).')+R'.($i));//Net rate
                                $sheet->setCellValue('T'.$i,'=(S'.($i).'-AE'.($i).')/AE'.($i));//Ebutor Margin%
                                $sheet->setCellValue('W'.$i,'=S'.($i).'/(1+T'.($i).')');//ELP
                                $sheet->setCellValue('U'.$i,'=(M'.($i).'-W'.($i).')/W'.($i));//Net%_after PTR
                                $sheet->setCellValue('V'.$i,'=(L'.($i).'-AD'.($i).'-(AD'.($i).'*N'.($i).'))/AD'.($i));//extra%
                                $sheet->setCellValue('X'.$i,'=L'.($i).'/(1+(N'.($i).'+V'.($i).'))');//ESP
                                $sheet->setCellValue('Y'.$i,'=(x'.($i).'-w'.($i).')/w'.($i));//ELP%
                                $sheet->setCellValue('Z'.$i,$value['State']);
                                $sheet->setCellValue('AA'.$i, '');
                                $sheet->setCellValue('AB'.$i, '');
                                $sheet->setCellValue('AC'.$i,'');
                                $sheet->setCellValue('AD'.$i,$value['ESP']);
                                $sheet->setCellValue('AE'.$i,$value['ELP']);
                                $sheet->setCellValue('AF'.$i,$value['Customer_Group']);
                                $sheet->setCellValue('AG'.$i,$value['Warehouse']);
                                $i++;                  
                            }   
                        });                     
                    }
                    else
                    {
                        $le_wh_name=$this->pricingModel->getWarehouseName($key);
                         $excel->sheet(substr($le_wh_name,0,30), function($sheet) use ($data,$le_wh_name) {
                            $sheet->mergeCells('D10:I10');
                            $sheet->setCellValue('D10','No Products available for-'.substr($le_wh_name,0,30))->cell('D10', function($color){$color->setBackground('#fc563d');});;
                            });
                    }
               }
            })->download('xls');

        }
    }
    /*Getting the BusinessUntis List based on the given input BU_ID*/
    public function BusinessunitList()
    {   
        $this->roleAccess = new RoleRepo();
        $user_id=session::get('userId');
        $bu_id=Input::get('bu_id');
        $bu_list=$this->roleAccess->getBusinessUnitList($bu_id,$user_id);
        $returnData='';
        if(count($bu_list)>0)
        {
            foreach ($bu_list as $result) {
              $returnData.='<option value="'.$result['bu_id']. '"> '.$result['bu_name'].'</option>';
            } 
        }

       return Array('status'=>200,'message'=>'success','result'=>$returnData);

    }

    /**
     * Get the list of ABOP & DC in dropdown
     */
    public function ApobDcList()
    {   
        $this->roleAccess = new RoleRepo();
        $user_id=session::get('userId');
        $bu_id=Input::get('bu_id');
        $bu_list=$this->roleAccess->getApobDcList($bu_id);
        $returnData='';
        if(count($bu_list)>0)
        {
            foreach ($bu_list as $result) {
              $returnData.='<option value="'.$result['bu_id']. '"> '.$result['display_name'].'</option>';
            } 
        }

       return Array('status'=>200,'message'=>'success','result'=>$returnData);

    }


    }   
    
        
