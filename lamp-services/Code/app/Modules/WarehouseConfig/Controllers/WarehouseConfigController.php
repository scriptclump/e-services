<?php

namespace App\Modules\WarehouseConfig\Controllers;

use App\Http\Controllers\BaseController;
use Session;
use View;
use Log;
use DB;
use Excel;
use Redirect;
use App\Modules\WarehouseConfig\Models\WarehouseConfig;
use App\Central\Repositories\RoleRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Config;
use App\Modules\WarehouseConfig\Models\ProductMongoMessage;
use UserActivity;
Class WarehouseConfigController extends BaseController
{
    private  $warehouseList;
    private  $selectOption;
    private  $selectOption2;

    public function __construct() {   
        try
        {
            $this->_roleRepo = new RoleRepo();
            $this->WarehouseConfig = new WarehouseConfig();
            $this->selectOption= '<option value="0">Please Select ....</option>';
            $this->selectOption2= '';

            $this->middleware(function ($request, $next) {
                if (!Session::has('userId'))
                {
                    return Redirect::to('/');
                }
                return $next($request);
            });                

        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }        
    }                     
    public function indexAction()
    {
        try
        {
            if (!Session::has('userId'))
            {
                return  Redirect::to('/');
            }
            $roleRepo = new RoleRepo();
            $userId = Session::get('userId');
            $approveAccess = $roleRepo->checkActionAccess($userId, 'WHCN001');
            if (!$approveAccess)
            {
                return Redirect::to('/');
            }
            parent::Title('Warehouse Configuration - Ebutor');
            $breadCrumbs = array('Dashboard' => url('/'), 'Warehouse Configuration' => '#');
            parent::Breadcrumbs($breadCrumbs);
            $getLocationType = $this->WarehouseConfig->locationTypes();
            $grp_products= $this->WarehouseConfig->getGroupedProductsList();
            $warehouseList = $this->WarehouseConfig->warehouseList();
            $warehouseList= json_decode(json_encode($warehouseList), true);
            $getLocationType=json_decode(json_encode($getLocationType), true);
            $lenghtUom=$this->WarehouseConfig->masterLookUpData('Length UOM');
            $weightUom=$this->WarehouseConfig->masterLookUpDataWithWeightUOM('Weight UoM');
            $binType=$this->WarehouseConfig->masterLookUpData('Bin Types');
            return View::make('WarehouseConfig::index',['warehouseList'=>$warehouseList,'locationType'=>$getLocationType,'lenghtUom'=>$lenghtUom,'weightUom'=>$weightUom,'binType'=>$binType,'grp_products'=>$grp_products]);
        }
        catch (\ErrorException $ex)
        {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    /* public function getProductGrpByWh($wh_id)
    {
        $grpOptions='<option value="0">Please select...</option>';
         $grp_products= $this->WarehouseConfig->getGroupedProductsList($wh_id);
         if(!empty($grp_products))
        {
            foreach ($grp_products as $grpValue){
            $grpOptions.= '<option value="'.$grpValue['product_group_id'].'">'.$grpValue['product_title'].'</option>';
            }   
        }         
        return $grpOptions;
    }*/
    public function getWarehouseConfig()
    {
        try
        {
            $reports = $this->WarehouseConfig->getWarehouseConfig();
            $report_result = json_encode(array('Records' => $reports));
            return $report_result;
        }
        catch (\ErrorException $ex)
        {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    public function getWarehouseDetails($wh_id)
    {
        $wh_details=$this->WarehouseConfig->getWarehouseDetails($wh_id);
        return $wh_details;
    }
    public function saveWarehouseData(Request $request)
    {
        $Data= $request->all();
        $wh_rs=$this->WarehouseConfig->saveWarehouseDetails($Data);
        return $wh_rs;
    }
    public function getWarehouseName($wh_id)
    {
        $wh_details=$this->WarehouseConfig->getWarehouseName($wh_id);
        return $wh_details;
    }
    public function editWarehouseData(Request $request)
    {
        $Data= $request->all();
        $wh_rs=$this->WarehouseConfig->saveEditWarehouse($Data);
        return $wh_rs;
    }
    public function getGroupedProducts()
    {   
       $data= $_GET[ "term" ];
        $groupProducts_data=$this->WarehouseConfig->getGroupedProducts($data);
        return $groupProducts_data;
    }
    public function deleteWarehouseLevel($wh_id)
    {
        $rs=$this->WarehouseConfig->deleteWarehouseLevels($wh_id);
        return $rs;
    }
    public function getLevelWiseDetails($level_type,$wh_id)
    {
        if($level_type==120006)
        {
            $level_type=120005;
        }
        return $this->whNestedLoop($wh_id,0,1,$level_type);
    }
    public function whNestedLoop($wh_id,$parent_id,$level,$level_type)
    {
        $wh =DB::table('warehouse_config')
            ->where('parent_loc_id', $parent_id)
            ->where('le_wh_id', $wh_id)
            ->get()->all(); 
        if (!empty($wh)) 
        {
            foreach($wh as  $cat1)
            { 
                $css_class='';
                 $disabled='';
                 if($cat1->wh_location_types!=$level_type-1 && $level_type!=120006)
                 {
                    $disabled="disabled";
                 }else if($cat1->wh_location_types!=$level_type-2 && $level_type==120006) 
                 {
                    $disabled="disabled";
                 }
                switch ($level) {
                    case 1:
                        $css_class='level_class1';
                        break;
                    case 2:
                        $css_class='level_class2';
                        break;
                    case 3:
                        $css_class='level_class3';
                        break;
                    case 3:
                        $css_class='level_class4';
                        break;
                    default:
                        $css_class='level_class'.$level;
                        break;
                }
                if($level_type>$cat1->wh_location_types)
                {
                     $this->selectOption.= '<option value="'.$cat1->wh_loc_id.'"  class="'.$css_class.'" '.$disabled.'> '.$cat1->wh_location.'</option>';
                }
                $this->whNestedLoop($wh_id,$cat1->wh_loc_id,$level+1,$level_type);                
            }
        }
        return $this->selectOption;
    }
    public function saveBinDimensionsCong(Request $request)
    {
        $Data= $request->all();
        $wh_rs=$this->WarehouseConfig->saveBinDimensionsCongData($Data);
        return $wh_rs;
    }
    public function getBinDimensionsData($grp_id,$wh_id)
    {
        $wh_rs=$this->WarehouseConfig->getBinDimensionsData();
        $grp_bin_dim_id='';
       /* if($grp_id!='test')
        {
            $grp_bin_dim_id=$this->WarehouseConfig->getProductGrpBinId($grp_id,$wh_id);
        }
        if(!empty($grp_bin_dim_id))
        {
            $wh_rs=$grp_bin_dim_id;
        }*/
        foreach ($wh_rs as $valueBinDim)
        {           
           $this->selectOption.='<option value="'.$valueBinDim["bin_type_dim_id"].'">'.$valueBinDim['bin_dim_name'].'</option>';  
        }
        return $this->selectOption;
    }
    public function getProductsByProdutGrp($grp_id,$dc)
    {   
       
        $grp_rs=$this->WarehouseConfig->getProductsByProdutGrpData($grp_id,$dc);
        foreach ($grp_rs as $valueGrp)
        {
            $this->selectOption.='<option value="'.$valueGrp["product_id"].'" >'.$valueGrp['product_title'].'  [ '.$valueGrp['sku'].' ]</option>';
        }
        return $this->selectOption;
    }  
    public  function binLevelWiseTransffer($wh_id)
    {
          return $this->BinTranfferedNestedLoop($wh_id,0,1,'120006');
    }
     public function BinTranfferedNestedLoop($wh_id,$parent_id,$level,$level_type)
    {
        $wh =DB::table('warehouse_config')
            ->where('parent_loc_id', $parent_id)
            ->where('le_wh_id', $wh_id)
            ->get()->all(); 
        if (!empty($wh)) 
        {
            foreach($wh as  $cat1)
            { 
                $css_class='';
                 $disabled='';
                 if($cat1->wh_location_types!=$level_type)
                 {
                    $disabled="disabled";
                 }
                switch ($level) {
                    case 1:
                        $css_class='bin_level_class1';
                        break;
                    case 2:
                        $css_class='bin_level_class2';
                        break;
                    case 3:
                        $css_class='bin_level_class3';
                        break;
                    case 3:
                        $css_class='bin_level_class4';
                        break;
                    default:
                        $css_class='bin_level_class'.$level;
                        break;
                }
                if($level_type>=$cat1->wh_location_types)
                {
                     $this->selectOption2.= '<option value="'.$cat1->wh_loc_id.'"  class="'.$css_class.'" '.$disabled.'> '.$cat1->wh_location.'</option>';
                }
                $this->BinTranfferedNestedLoop($wh_id,$cat1->wh_loc_id,$level+1,$level_type);                
            }
        }
        return $this->selectOption2;
    } 
    public function multiBinLevelConfig(Request $request)
    {
        $Data= $request->all();
        return $this->WarehouseConfig->multiBinLevelConfigData($Data);

    }
    public function checkRackCapacity(Request $request)
    {
        $data= $request->all();
        return $this->WarehouseConfig->checkRackCapacityData($data);
    }
      public function binInvDashBoard()
    {
        $checkPPermissions=$this->_roleRepo->checkPermissionByFeatureCode('WMSDB002');
        return View::make('WarehouseConfig::bininvdashboard',['binexportPermissions'=>$checkPPermissions]);
    }
    public function getInvdata(Request $request)
    {       
        DB::enablequerylog();
        $this->grid_field_db_match_reports = array(
            'warehouse_name' => 'wh_config.wh_location',
            'bin_code' => 'warehouse_config.wh_location',
            'bin_inv' => 'bin_inventory.qty',
            'bin_length' => 'bin_type_dimensions.length',
            'bin_breadth' => 'bin_type_dimensions.breadth',
            'bin_height' => 'bin_type_dimensions.heigth',
            'mrp' => 'products.mrp',
            'product_title' => 'products.product_title',
            'sku' => 'products.sku',
            'bin_max_qty' => 'product_bin_config.max_qty',
            'bin_min_qty' => 'product_bin_config.min_qty',
            'bin_type'=>'master_lookup.master_lookup_name',
            'aisle_type'=>'wh_aisel.wh_location',
            'pack_type_name'=>'pack_master.master_lookup_name'
        );
        $page = $request->input('page');   //Page number
        $pageSize = $request->input('pageSize');
        $query = DB::table('warehouse_config')
                ->join('bin_inventory','bin_inventory.bin_id','=','warehouse_config.wh_loc_id')
                ->join('warehouse_config as wh_config','wh_config.le_wh_id','=','warehouse_config.le_wh_id')
                ->leftjoin('putaway_allocation','putaway_allocation.bin_id','=','warehouse_config.wh_loc_id')                
                ->leftjoin('products','products.product_id','=','warehouse_config.pref_prod_id')
                ->leftjoin('product_bin_config', function($queryJoin){
                    $queryJoin->on('product_bin_config.bin_type_dim_id','=','warehouse_config.bin_type_dim_id');
                     $queryJoin->on('product_bin_config.prod_group_id','=','products.product_group_id');
                    $queryJoin->on('product_bin_config.wh_id','=','warehouse_config.le_wh_id'); 
                })
                ->join('bin_type_dimensions','bin_type_dimensions.bin_type_dim_id','=','warehouse_config.bin_type_dim_id')
                ->join('warehouse_config as wh_aisel','wh_aisel.wh_loc_id','=','warehouse_config.parent_loc_id')
                ->join('master_lookup','master_lookup.value','=','bin_type_dimensions.bin_type')
                 ->leftjoin('master_lookup as pack_master','pack_master.value','=','product_bin_config.pack_conf_id')
                ->select('warehouse_config.wh_loc_id AS bin_id','wh_config.wh_location AS warehouse_name','warehouse_config.wh_location AS bin_code','bin_inventory.qty AS bin_inv','products.product_title',DB::raw('FORMAT(products.mrp,2) as mrp'),'products.sku','product_bin_config.min_qty as bin_min_qty','wh_aisel.wh_location as aisle_type','product_bin_config.max_qty as bin_max_qty','warehouse_config.created_at','bin_type_dimensions.length as bin_length','bin_type_dimensions.breadth as bin_breadth','bin_type_dimensions.heigth as bin_height','master_lookup.master_lookup_name as bin_type','product_bin_config.pack_conf_id as pro_pack_id')
                ->groupby('warehouse_config.wh_loc_id');
            if ($request->input('$filter'))
            {           //checking for filtering                
                $post_filter_query = explode(' and ', $request->input('$filter')); //multiple filtering seperated by 'and'
                foreach ($post_filter_query as $post_filter_query_sub)
                {    //looping through each filter                    
                    $filter = explode(' ', $post_filter_query_sub);
                    $length = count($filter);
                    $filter_query_field = '';
                    if ($length > 3)
                    {
                        for ($i = 0; $i < $length - 2; $i++)
                            $filter_query_field .= $filter[$i] . " ";
                        $filter_query_field = trim($filter_query_field);
                        $filter_query_operator = $filter[$length - 2];
                        $filter_query_value = $filter[$length - 1];
                    }
                    else
                    {
                        $filter_query_field = $filter[0];
                        $filter_query_operator = $filter[1];
                        $filter_query_value = $filter[2];
                    }
                    $filter_query_substr = substr($filter_query_field, 0, 7);
                    if ($filter_query_substr == 'startsw' || $filter_query_substr == 'endswit' || $filter_query_substr == 'indexof' || $filter_query_substr == 'tolower')
                    {
                        if ($filter_query_substr == 'startsw')
                        {
                            $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'
                            $filter_value = $filter_value_array[1] . '%';
                            foreach ($this->grid_field_db_match_reports as $key => $value)
                            {
                                if (strpos($filter_query_field, '(' . $key . ')') != 0)
                                {  //getting the filter field name
                                    $query->where($this->grid_field_db_match_reports[$key], 'like', $filter_value);
                                }
                            }
                        }
                        if ($filter_query_substr == 'endswit')
                        {
                            $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'
                            $filter_value = '%' . $filter_value_array[1];
                            foreach ($this->grid_field_db_match_reports as $key => $value)
                            {
                                if (strpos($filter_query_field, '(' . $key . ')') != 0)
                                {  //getting the filter field name
                                    $query->where($this->grid_field_db_match_reports[$key], 'like', $filter_value);
                                }
                            }
                        }
                        if ($filter_query_substr == 'tolower')
                        {
                            $filter_value_array = explode("'", $filter_query_value);  //extracting the input filter value between single quotes ex 'value'
                            $filter_value = $filter_value_array[1];
                            if ($filter_query_operator == 'eq')
                            {
                                $like = '=';
                            }
                            else
                            {
                                $like = '!=';
                            }
                            foreach ($this->grid_field_db_match_reports as $key => $value)
                            {
                                if (strpos($filter_query_field, '(' . $key . ')') != 0)
                                {  //getting the filter field name
                                    $query->where($this->grid_field_db_match_reports[$key], $like, $filter_value);
                                }
                            }
                        }
                        if ($filter_query_substr == 'indexof')
                        {
                            $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'
                            $filter_value = '%' . $filter_value_array[1] . '%';
                            if ($filter_query_operator == 'ge')
                            {
                                $like = 'like';
                            }
                            else
                            {
                                $like = 'not like';
                            }
                            foreach ($this->grid_field_db_match_reports as $key => $value)
                            {
                                if (strpos($filter_query_field, '(' . $key . ')') != 0)
                                {  //getting the filter field name
                                    $query->where($this->grid_field_db_match_reports[$key], $like, $filter_value);
                                }
                            }
                        }
                    }
                    else
                    {
                        switch ($filter_query_operator)
                        {
                            case 'eq' :
                                $filter_operator = '=';
                                break;
                            case 'ne':
                                $filter_operator = '!=';
                                break;
                            case 'gt' :
                                $filter_operator = '>';
                                break;
                            case 'lt' :
                                $filter_operator = '<';
                                break;
                            case 'ge' :
                                $filter_operator = '>=';
                                break;
                            case 'le' :
                                $filter_operator = '<=';
                                break;
                        }
                        if (isset($this->grid_field_db_match_reports[$filter_query_field]))
                        { //getting appropriate table field based on grid field
                            $filter_field = $this->grid_field_db_match_reports[$filter_query_field];
                        }
                        $query->where($filter_field, $filter_operator, $filter_query_value);
                    }
                }
            }
            if ($request->input('$orderby'))
            {    //checking for sorting
                $order = explode(' ', $request->input('$orderby'));
                $order_query_field = $order[0]; //on which field sorting need to be done
                $order_query_type = $order[1]; //sort type asc or desc
                $order_by_type = 'desc';

                if ($order_query_type == 'asc') 
                {
                        $order_by_type = 'asc';
                }

                if (isset($this->grid_field_db_match_reports[$order_query_field])) 
                { //getting appropriate table field based on grid field
                    $order_by = $this->grid_field_db_match_reports[$order_query_field];
                    $query->orderBy($order_by, $order_by_type);
                }
            }

            $count = count($query->get()->all());
            $result = array();
            $result['count'] = $count;
            $reports = $query->skip($page * $pageSize)->take($pageSize)->get()->all();
            $i = 0;
            foreach ($reports as $report)
            {
                 $pack_name="";
               $packSql=DB::select('select getMastLookupValue("'.$report->pro_pack_id.'") as pack_name');
               $packSql = json_decode(json_encode($packSql),true);
               
               if(!empty($packSql[0]['pack_name']))
               {
                 $pack_name=$packSql[0]['pack_name'];
               }else{
                 $report->bin_min_qty='';
                 $report->bin_max_qty='';
               } 
               $report->pack_type_name=$pack_name;
               $bin_inv= $report->bin_inv;
               if($bin_inv == '')
               {
                    $bin_inv =0;
               }
               $report->bin_inv= $bin_inv;
            }
           
                /*DB::raw('getMastLookupValue(wh_location_types) as wh_location_types')*/
        //$binSql = json_decode(json_encode($binSql),true);
        $cnt= $count;
        $data=json_encode(array("Records"=>$reports,"TotalRecordsCount"=>$cnt));
        return $data;
    }
    public function downloadBinExcel(Request $request)
    {
		//Log::info($request->get('wh_list_id2'));
        $withData = $request->get('with_data');
        $wh_id = $request->get('wh_list_id2'); 
		$bin_type_id = $request->get('bin_type_id');
        if($withData== 'on'){
        $dat = $this->WarehouseConfig->getProductBinMapping($wh_id,$bin_type_id);
        foreach($dat as $map)
        {
            $str = $map['bin_type'];
            $binType = explode('(', $str);
            $binTypeName = (isset($binType[0]))?$binType[0]:'';
            preg_match_all('!\d+!', $str, $matches);
            $dims = (isset($matches[0]))?$matches[0]:array();
            $length = (isset($dims[0]))?$dims[0]:0;
            $breadth = (isset($dims[1]))?$dims[1]:0;
            $height = (isset($dims[2]))?$dims[2]:0;
            $productGrpId = (isset($map['res_prod_grp_id']))?$map['res_prod_grp_id']:0;
            
            $binConfigData = DB::table('product_bin_config')->where('wh_id',$wh_id)
                    ->where('prod_group_id',$productGrpId)->where('bin_type_dim_id',$map['bin_type_dim_id'])->select(DB::raw('getMastLookupValue(pack_conf_id) as pack_conf_id'),'min_qty','max_qty')->first(); 
             
            $packConfId = (isset($binConfigData->pack_conf_id))?$binConfigData->pack_conf_id:'';
            $minQty = (isset($binConfigData->min_qty))?$binConfigData->min_qty:'';
            $maxQty = (isset($binConfigData->max_qty))?$binConfigData->max_qty:'';
            
            
            $data[] = [
                'Level'=>$map['level'],
                'Zone'=>$map['zone'],
                'Aisle'=>$map['aisle'],
                'Bin Location' => $map['wh_location'],
                //'Product ID'=>$map['pref_prod_id'],
                'Product Title'=>$map['title'],
                'SKU'=>$map['sku'],
                //'Product Group ID'=>$productGrpId,
                'Bin Qty'=>$map['qty'],
                'Bin Type'=>$binTypeName,
                'Length'=>$length,
                'Breadth'=>$breadth,
                'Height'=>$height,
                'Min Capacity'=>$minQty,
                'Max Capacity'=>$maxQty,
                'Pack Type'=>$packConfId,
  
            ];

        }
    }
    else{
        $data= array();
    }    
    if(empty($data)){
        $data = array('Level','Zone','Aisle','Bin Location','Product Title','SKU','Bin Qty','Bin Type','Length','Breadth','Height',
            'Min Capacity','Max Capacity','Pack Type');
    }  
$bin_types_data2 = DB::table('bin_type_dimensions')->select(DB::Raw('getBinDimById(bin_type_dim_id) as BinDimentions'))->get()->all() ;
	
    $bin_types_data = json_decode(json_encode($bin_types_data2),1); 
    Excel::create('Product Bin Mapping', function($excel) use($data,$wh_id,$bin_types_data) {
        $excel->sheet('ProductBins ', function($sheet) use($data,$wh_id) {          
        $sheet->fromArray($data);
        $sheet->prependRow(1, array('Warehouse ID', $wh_id));
        });      
		$excel->sheet('Bin Dimentions', function($sheet) use($bin_types_data) {          
        $sheet->fromArray($bin_types_data);
        });   
    })->export('xls');
    }
    

    public function importBinExcel(Request $request)
    {
        $file = Input::file('import_file')->getRealPath();
        $data = $this->readExcelData($file);
        $data = json_decode(json_encode($data), 1);

        $finalBin = array();
        $whId = (isset($data['wh_data'][1]))?$data['wh_data'][1]:4497;
        $prodData = (isset($data['prod_data']))?$data['prod_data']:array();

		$duplicateBinTypes = array_column($prodData, 'bin_type');
        $duplicateBinTypesCount = array_count_values($duplicateBinTypes);
		$all = count($duplicateBinTypesCount);
		if($all > 1)
		{
		$requiredData[] = "Sheet can't be imported as it includes multiple Bin types";
        $messg = json_encode(array('status_messages' => $requiredData));
        return $messg;
		}
		
        $message = array();
        $dimMessage = array();
        $grpMessage = array();
        $binInvMessage = array();
        $error = array();
        $success = array();
        $duplicateBins = array();
        $bupliBins = array();
        $exceedsMax = array();
        $requiredData = array();
        $packErrors = array();
        $c = 0;
        foreach ($prodData as $k => $v)
        {
            if (isset($prodData[$k + 1]['bin_location']) && $prodData[$k]['bin_location'] == $prodData[$k + 1]['bin_location'])
            {
                $c++;
                $duplicateBins[$prodData[$k]['bin_location']] = $c;
            }
            else
            {
                $c = 0;
                continue;
            }
        }
        $duplicateBins = array_column($prodData, 'bin_location');
        $duplicateBinsCount = array_count_values($duplicateBins);
        $productsGrpArr = DB::table('products')->pluck('product_group_id','product_id')->all();
        $productIdBySku = DB::table('products')->pluck('product_id','sku')->all();    
        //print_r($productIdBySku); die;
        foreach ($prodData as $prod)
        {
            $bin_id = '';
            $qty = '';
            $product_id =0;
            $pack_type = (isset($prod['pack_type'])) ? $prod['pack_type'] : '';
            $product_title = (isset($prod['product_title'])) ? $prod['product_title'] : '';
            $bin_type = DB::table('master_lookup')->where('master_lookup_name', trim($prod['bin_type']))->pluck('value')->all();
            $bin_type_id = (isset($bin_type[0])) ? $bin_type[0] : 0;
            $length = (isset($prod['length'])) ? $prod['length'] : 0;
            $breadth = (isset($prod['breadth'])) ? $prod['breadth'] : 0;
            $height = (isset($prod['height'])) ? $prod['height'] : 0;
            $min_qty = (isset($prod['min_capacity'])) ? $prod['min_capacity'] : 0;
            $max_qty = (isset($prod['max_capacity'])) ? $prod['max_capacity'] : 0;
            $bin_loc = (isset($prod['bin_location'])) ? trim($prod['bin_location']) : 0;            
            $sku = (isset($prod['sku'])) ? $prod['sku'] : '';
            if($sku != '')
            {
              $product_id = (isset($productIdBySku[$sku])) ? $productIdBySku[$sku] : 0;
            }
            $qty = (isset($prod['bin_qty'])) ? $prod['bin_qty'] : 0;
            $pack_id_val = 0;
            //check pack type exists for product
            //die($product_id);
            if ($product_id == 0)
            {
                $requiredData[] = "Row skipped as Product is not availsable for Bin " . $bin_loc;
                continue;
            }
            else
            {
                $product_group_id = (isset($productsGrpArr[$product_id])) ? $productsGrpArr[$product_id] : 0;
                //warehouse location availability
                $bin_dat = DB::table('warehouse_config')->where('wh_location', $bin_loc)->where('wh_location_types', 120006)->pluck('wh_loc_id')->all();
                $checkLocExists = $bin_id = (isset($bin_dat[0])) ? $bin_dat[0] : 0;
                if (!$checkLocExists)
                {
                    $error[] = 'Bin Location (' . $bin_loc . ') does not exists.';
                    continue;
                }
                else
                {
                    if (isset($duplicateBinsCount[$bin_loc]) && $duplicateBinsCount[$bin_loc] > 1)
                    {
                        $bupliBins[] = "Row skipped as Bin Location( " . $bin_loc . " ) exists more than once";
                        continue;
                    }
                    else
                    {
                                $pack_id = DB::table('master_lookup')->where('mas_cat_id', 16)->where('master_lookup_name', trim($pack_type))->pluck('value')->all();
                                $pack_id_val = (isset($pack_id[0])) ? $pack_id[0] : 0;
                                $pack_config_id = DB::table('product_pack_config')->where(['product_id' => $product_id, 'level' => $pack_id_val])->pluck('pack_id')->all();
                                if (empty($pack_config_id) && $bin_type_id != '109004' && $bin_type_id != '109005')
                                {
                                    $packErrors[] = "Row skipped as Pack Type( " . $pack_type . " ) does not exists for product " . $product_title;
                                    continue;
                                }
                            
                            else
                            {
                                //bin dimentions
                                $bin_dim_dat = DB::table('bin_type_dimensions')->where(['bin_type' => $bin_type_id, 'length' => $length, 'breadth' => $breadth,
                                            'heigth' => $height])->pluck('bin_type_dim_id')->all();
                                $checkBinDimExists = (isset($bin_dim_dat[0])) ? $bin_dim_dat[0] : 0;
                                if (!$checkBinDimExists)
                                {
                                    $checkBinDimExists = DB::table('bin_type_dimensions')->insertGetId(['bin_type' => $bin_type_id, 'length' => $length, 'breadth' => $breadth,
                                        'heigth' => $height]);
                                    DB::table('warehouse_config')->where(['le_wh_id' => $whId, 'wh_location_types' => 120006, 'pref_prod_id' => $product_id, 'wh_location' => $bin_loc])->update(['bin_type_dim_id' => $checkBinDimExists]);
                                    $dimMessage[] = 'Bin Dimention : ' . $bin_type_id . '( ' . $length . ',' . $breadth . ',' . $height . ') created successfully';
                                }

                                //min and max qty pack wise     
                                //DB::connection()->enableQueryLog();
                                $bin_maxmin_dat = DB::table('product_bin_config')->where(['wh_id' => $whId, 'prod_group_id' => $product_group_id,
                                            'bin_type_dim_id' => $checkBinDimExists, 'pack_conf_id' => $pack_id_val])->pluck('prod_bin_conf_id')->all();
                                $checkMaxMinExists = (isset($bin_maxmin_dat[0])) ? $bin_maxmin_dat[0] : 0;
                                //$queries = DB::getQueryLog();
								$group_name_arr = DB::table('product_groups')->where('product_grp_ref_id',$product_group_id)->pluck('product_grp_name')->all();
								$group_name = (isset($group_name_arr[0]))?$group_name_arr[0]:'';
                                if (!$checkMaxMinExists)
                                {
									if ($pack_type == '' && $bin_type_id != '109004' && $bin_type_id != '109005')
									{
										$requiredData[] = "Row skipped as Pack is not availsable for Bin " . $bin_loc;
										continue;
									}
									if(!$min_qty && !$max_qty && $checkBinDimExists && ($bin_type_id == '109004'||$bin_type_id == '109005' ))
									{
									$this->WarehouseConfig->addProductBinConfig($product_id,$checkBinDimExists,$whId);	
									}
									else
									{
                                    $checkMaxMinExists = DB::table('product_bin_config')->insertGetId(['wh_id' => $whId, 'prod_group_id' => $product_group_id,
                                        'bin_type_dim_id' => $checkBinDimExists, 'pack_conf_id' => $pack_id_val, 'min_qty' => $min_qty, 'max_qty' => $max_qty]);
                                    $grpMessage[] = 'Product Group(' .$group_name . ') with Dimentions Min and Max Qty created successfully';
									}
                                }
								else
                                {
                                    $checkMaxMinExists = DB::table('product_bin_config')->where(['wh_id' => $whId, 'prod_group_id' => $product_group_id,
                                        'bin_type_dim_id' => $checkBinDimExists, 'pack_conf_id' => $pack_id_val])->update(['min_qty' => $min_qty, 'max_qty' => $max_qty]);
                                    $grpMessage[] = 'Product Group(' . $group_name . ') with Dimentions Min and Max Qty updated successfully';                                    
                                }

                            $qtyExistsArray = DB::table('bin_inventory')->where(['bin_id' => $checkLocExists, 'wh_id' => $whId])->pluck('qty')->all();
                            
                            $qtyExists = (!empty($qtyExistsArray[0]))? $qtyExistsArray[0]:null; 

                           
                            
                                if(isset($qtyExists) && $qtyExists > 0)
                                {
									 $binInvMessage[] = "Product '" . $prod['sku'] . "' reservation to Bin" . $bin_loc . ' Failed as Bin holds Inventory.';                                 
                                }
                                else
                                {
								DB::table('warehouse_config')->where('wh_location', $bin_loc)->where('wh_location_types', 120006)->update(['res_prod_grp_id' => $product_group_id, 'pref_prod_id' => $product_id]);
                                $binInvMessage[] = "Product '" . $prod['sku'] . "' reservation updated to Bin" . $bin_loc;
									if($qtyExists == 0)
									{
									DB::table('bin_inventory')->where(['bin_id'=>$checkLocExists,'wh_id'=>$whId])->update(['product_id'=>$product_id]);									
									}
									else{
                                    DB::table('bin_inventory')->insert(['bin_id'=>$checkLocExists,'wh_id'=>$whId,'product_id'=>$product_id]);  
									}
                                }
                            
							
                                //adding bin inventory
//                                $exists = DB::table('bin_inventory')->where(['wh_id' => $whId, 'bin_id' => $bin_id, 'product_id' => $product_id])->pluck('bin_inv_id');
//                                if ($checkLocExists && $checkBinDimExists && $checkMaxMinExists)
//                                {
//                                    if (empty($exists))
//                                    {
//                                        DB::table('bin_inventory')->where(['wh_id' => $whId, 'bin_id' => $bin_id])->update(['product_id' => $product_id, 'qty' => $qty]);
//                                        $success[] = "Product SKU: " . $prod['sku'] . " updated with Bin Inventory " . $qty;
//                                    }
//                                    else
//                                    {
//                                        DB::table('bin_inventory')->where(['wh_id' => $whId, 'bin_id' => $bin_id, 'product_id' => $product_id])->update(['qty' => $qty]);
//                                        $success[] = "Product SKU: " . $prod['sku'] . " updated with Bin Inventory " . $qty;
//                                    }
//                                }
                            }
                        
                    }
                }
            }
        }
        $timestamp = date('Y-m-d H:i:s');
        $current_timeStamp = strtotime($timestamp);
        $success[] = "Product Bin mapping completed";
		$success[] = "*Bin inventory won't be imported.";
        $success[] = "<a target='_blank'  href='/binuploadmsg/".$current_timeStamp."'>Click Here to check upload status</a>";   
		
        $message = array_merge($requiredData, $bupliBins, $packErrors, $dimMessage, $grpMessage, $exceedsMax, $error, $binInvMessage);
		
		UserActivity::excelUploadFileLogs("BinInventoryGrid", $current_timeStamp, $file, $message);
		
        $messg = json_encode(array('status_messages' => $success));
        return $messg;
    }
	
	
	
	public function readExcelData($path) {
        try {
            $headerRowNumber = 1;
            Config::set('excel.import.startRow', $headerRowNumber);
            Config::set('excel.import.heading', 'numeric');
            $cat_data = Excel::selectSheetsByIndex(0)->load($path, function($reader) {
                        
                    })->first();
            $headerRowNumber = 2;
            Config::set('excel.import.startRow', $headerRowNumber);
            Config::set('excel.import.heading', 'slugged');
            $prod_data = Excel::selectSheetsByIndex(0)->load($path, function($reader) {
                        
                    })->get();
            $data['wh_data'] = $cat_data;
            $data['prod_data'] = $prod_data;
            return $data;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
        }
    }
	
	public function getBinTypeList()
    { 
        $binNameQuery = DB::table('master_lookup')->where('mas_cat_id', 109)->select('master_lookup_name', 'value')->get()->all();
        $list = json_decode(json_encode($binNameQuery), true);
        $opt = '<option value="">Please Select Bin Type...</option><option value="1">All</option>';
        foreach($list as $Arr)
        {
           $opt .=  '<option value="'.$Arr['value'].'">'.$Arr['master_lookup_name'].'</option>';            
        }
        return $opt;
    }
	public function readMessage($refId)
    {
        $readlogs = new productMongoMessage();
        $result = $readlogs->readMappingLogs($refId);
    }
     public function getBinCategory()
    {
        $wh_rs=$this->WarehouseConfig->masterLookUpData("Bin Categories");
        $grp_bin_dim_id='';
     
        foreach ($wh_rs as $valueBinDim)
        {           
           $this->selectOption.='<option value="'.$valueBinDim["location_value"].'">'.$valueBinDim['location_name'].'</option>';  
        }
        return $this->selectOption;
    }
    public function getProductGroupId($wh_id){
        $groupList=$this->WarehouseConfig->getGroupedProductsList($wh_id);
        return $groupList;
    }
}
