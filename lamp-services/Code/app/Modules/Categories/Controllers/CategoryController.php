<?php

namespace App\Modules\Categories\Controllers;

use View;
use Validator;
use Illuminate\Support\Facades\Input;
use Log;
use DB;
use Response;
use Session;
use Redirect;
use URL;
use Image;
use Imagine;
use App\Modules\Categories\Models\CategoryModel;
use App\Modules\Pricing\Models\uploadSlabProductsModel;
use Illuminate\Support\Facades\Config;
use App\Http\Controllers\BaseController;
use App\Central\Repositories\RoleRepo;
use App\Central\Repositories\CustomerRepo;
use Illuminate\Http\Request;

use App\Modules\Roles\Models\Role;
use Illuminate\Support\Facades\Cache;
use App\Central\Repositories\ProductRepo;
use Carbon\Carbon;
use UserActivity;
use Excel;
use Notifications;
use App\Modules\Notifications\Models\NotificationsModel;

class CategoryController extends BaseController {

    var $FlipkartObj;
    protected $category;
    protected $_manufacturerId;
    private $roleRepo;
    private $custRepo;
    private $marginRepo;

    public function __construct() {
        $category = new CategoryModel();
        $roleRepo = new RoleRepo;
        $this->product_slab_details = new uploadSlabProductsModel();
        $this->roleRepo = $roleRepo;
        $this->_category = $category;
        $this->grid_field_db_match = array(
            'cat_name'    => 'cat_name',
            
            
        );
    }
    public function indexAction() {
        return View::make('Categories::category');
    }
    public function getMasterLookUpData($id,$name)
    {
      $returnData = DB::table('master_lookup_categories')
            ->join('master_lookup','master_lookup.mas_cat_id','=','master_lookup_categories.mas_cat_id')
           ->select('master_lookup.master_lookup_name as name','master_lookup.value as value','sort_order')
            ->where('master_lookup_categories.mas_cat_id','=',$id)
            ->where('master_lookup.is_active','1')
            ->where('master_lookup_categories.mas_cat_name','=',$name)
             ->get()->all();
      return $returnData;
    }
    public function getSegments($cat_id)
    {
        $data= DB::Table('mp_categories as mp')
            ->join('segment_mapping as se_map','se_map.mp_category_id','=','mp.mp_category_id')
            ->select('se_map.value')
            ->where('mp.mp_category_id','=',$cat_id)
            ->get()->all();
            $data= json_decode(json_encode($data),true);
        return $data;
    }
    public function treegrid() {
        $checkCatPermissions= $this->roleRepo->checkPermissionByFeatureCode('CAT001');
        $marginsPerm= $this->roleRepo->checkPermissionByFeatureCode('CMU001');
        if ($checkCatPermissions==0)
        {
            return Redirect::to('/');
        }
        parent::Breadcrumbs(array('Home' => '/', 'Categories' => '#'));
        $segments= $this->getMasterLookUpData('48','Business Segments');
        return View::make('Categories::tree',['segments'=>$segments,'marginsPerm'=>$marginsPerm]);
    }
    //get parent category in edit module
    public function getParentCategory($cat_id)
    {
        $rs=0;
        $data=  $this->_category->where('category_id',$cat_id)->select('parent_id')->first();
        if($data->parent_id!=0)
        {
           $rs= $this->_category->where('category_id',$data->parent_id)->select('category_id','cat_name')->first();
        }
        return $rs;
    }
    
    public function getCategoryImage($cat_id)
    {
        $rs=null;
        $rs=  $this->_category->where('category_id',$cat_id)->select('parent_id','category_id','cat_name','image_url')->first();
        return $rs;
    }
    public function checkuniqueparentname(Request $request){

        $data= $request->all();
        $edit_category_id= $data['edit_category_id'];
        $edit_category_name= $data['edit_category_name'];
        $parent_id= $data['parent_id'];
        $is_active= $data['is_active'];
        $category_image =$data['category_image'];      
    }

    public function downloadExcel(){
        $mytime = Carbon::now();
        $headers = array('CATEGORY ID','DC CODE','EFFECTIVE DATE(mm/dd/yyyy)','FC CATEGORY MARGIN','FC MARGIN(Percentage/Value)','DC CATEGORY MARGIN','DC MARGIN(Percentage/Value)');
        $headers_second_page = array('CATEGORY NAME','CATEGORY ID','DC NAME','DC CODE');
        $dcDet = json_decode($this->product_slab_details->getAllDCType(), true);
        $categories = json_decode(json_encode($this->_category->getAllCategories()),1);

        $loopCounter = 0;
        $exceldata_second = array();
        foreach($dcDet as $val){
            $exceldata_second[$loopCounter]['dc_name'] = $val['lp_wh_name'];
            $exceldata_second[$loopCounter]['dc_code'] = $val['le_wh_code'];
            $loopCounter++;
        }
        $loopCounter = 0;
        foreach($categories as $cat){
            $exceldata_second[$loopCounter]['category_name'] = $cat['cat_name'];
            $exceldata_second[$loopCounter]['category_id'] = $cat['category_id'];
            $loopCounter++;
        }
        $dummyData = array('categoryExcelName'=>'Category Margin Sheet-'.$mytime->toDateTimeString());
        UserActivity::userActivityLog('Category',$dummyData, 'Category Excel downloaded by user');

        Excel::create('Category Margin Sheet-'.$mytime->toDateTimeString(), function($excel) use($headers, $headers_second_page, $exceldata_second) 
        {
            $excel->sheet("Category", function($sheet) use($headers)
            {
                $sheet->loadView('Categories::categoryMarginTemplate', array('headers' => $headers)); 
            });

            $excel->sheet("Category&DC Data", function($sheet) use($headers_second_page, $exceldata_second)
            {
                $sheet->loadView('Categories::dcNamesSampleTemplate', array('headers' => $headers_second_page, 'data' => $exceldata_second)); 
            });
        })->export('xlsx');
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
                        
                    })->get()->all();
            $data['cat_data'] = $cat_data;
            $data['prod_data'] = $prod_data;
            return $data;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
        }
    }

    public function uploadCatmargin(Request $request){
        try{
            DB::beginTransaction();
            $name = Session::all();
            $environment    = env('APP_ENV');
            $file_data                      = Input::file('category_data');
            //$file_name                      = $file_data->getClientOriginalName();
            $file_extension                 = $file_data->getClientOriginalExtension();

            if( $file_extension != 'xlsx'){
                return 'Invalid file type';
            }else{
                if (Input::hasFile('category_data')) {
                    $path                           = Input::file('category_data')->getRealPath();
                    $data                           = $this->readExcel($path);
                    //$file_data                      = Input::file('category_data');
                    $result                         = json_decode(json_encode($data['prod_data']), true);
                    $headers                        = json_decode(json_encode($data['cat_data']), true);
                    $headers1                       = array('CATEGORY ID','DC CODE','EFFECTIVE DATE(mm/dd/yyyy)','FC CATEGORY MARGIN','FC MARGIN(Percentage/Value)','DC CATEGORY MARGIN','DC MARGIN(Percentage/Value)');
                    $recordDiff                         = array_diff($headers,$headers1);
                    if(empty($recordDiff) && count($recordDiff)==0){
                        $timestamp = md5(microtime(true));
                        $txtFileName = 'category-import-' . $timestamp . '.txt';

                        $file_path = 'download' . DIRECTORY_SEPARATOR . 'category_log' . DIRECTORY_SEPARATOR . $txtFileName;
                        $msg = '';
                        $updateCnt = $insertCnt = $errorCnt = $notFoundCnt = 0;
                        $excelRowcounter = 2;
                        ini_set('max_execution_time', 0);
                        foreach($result as $key => $data){
                            $msg .= "#".$excelRowcounter." CATEGORY(".$data['category_id'].") : ";
                            $dc_id = $this->_category->getdcID($data['dc_code']);
                            $fc_category_margin = isset($data['fc_category_margin'])?$data['fc_category_margin']:NULL;
                            $dc_category_margin = isset($data['dc_category_margin'])?$data['dc_category_margin']:NULL;
                            $fc_margin_type = isset($data['fc_marginpercentagevalue'])?$data['fc_marginpercentagevalue']:NULL;
                            $dc_margin_type = isset($data['dc_marginpercentagevalue'])?$data['dc_marginpercentagevalue']:NULL;
                            // assign dates
                            $effective_date = is_array($data['effective_datemmddyyyy']) ? $data['effective_datemmddyyyy']['date'] :'1970-01-01' ;
                            $effective_date = date("Y-m-d", strtotime($effective_date));
                            //Log::info($effective_date);
                            // Check for valid data
                            $validFlag = 0;
                            if($data['category_id']==0 || $data['category_id']==''){
                                $msg .= "Category Id is not valid!";
                                $validFlag = 1;
                            }else{
                                $category = DB::table('categories')->select('category_id')
                                            ->where('category_id',$data['category_id'])
                                            ->where('is_product_class',1)
                                            ->first();
                                if(empty($category)){
                                    $msg .= "Category ID is not valid!";
                                    $validFlag = 1;
                                }
                            }
                            if($fc_category_margin < 0 || (is_numeric($fc_category_margin) != 1 && $fc_category_margin!= NULL)){
                                $msg .= " : FC CATEGORY Margin is not valid!";
                                $validFlag = 1;
                            }else{
                                if(!empty($fc_category_margin) && $fc_margin_type == NULL){
                                    $msg .= ": Please enter the FC margin type(Percentage/Value)!";
                                    $validFlag = 1;
                                }elseif(!empty($fc_category_margin) && $fc_margin_type != 'Percentage' && $fc_margin_type != 'Value'){
                                        $msg .= ": Please enter valid FC margin type(Percentage/Value)";
                                        $validFlag = 1;
                                    }
                                }
                            
                            if($fc_category_margin == NULL && !empty($fc_margin_type)){
                                $msg .= ": Please enter the FC Category Margin";
                                $validFlag = 1;
                            }
                            if($dc_category_margin < 0 || (is_numeric($dc_category_margin) != 1 && $dc_category_margin!= NULL)){
                                $msg .= " : DC CATEGORY Margin is not valid!";
                                $validFlag = 1;
                            }else{
                                if(!empty($dc_category_margin) && $dc_margin_type == NULL){
                                    $msg .= ": Please enter the DC margin type(Percentage/Value)!";
                                    $validFlag = 1;
                                }elseif(!empty($dc_category_margin) && $dc_margin_type != 'Percentage' && $dc_margin_type != 'Value'){
                                        $msg .= ": Please enter valid DC margin type(Percentage/Value)";
                                        $validFlag = 1;
                                    }
                                }
                            
                            if($dc_category_margin == NULL && !empty($dc_margin_type)){
                                $msg .= ": Please enter the FC Category Margin";
                                $validFlag = 1;
                            }
                            if($data['dc_code']==''){
                                $msg .= " : DC Code is not valid!";
                                $validFlag = 1;
                            }
                            if($effective_date=="" || $effective_date=='1970-01-01'|| (strpos($effective_date,'1900') !== false)){
                                $msg .= " : Effective Date is not valid, please check date format (m/d/yyyy)!";
                                $validFlag = 1;
                            }
                            if($validFlag==0){
                                $alldcfcdata = array();
                                if(isset($dc_id->le_wh_id) && $dc_id->le_wh_id != 0){
                                    $alldcfcdata = $this->product_slab_details->getAllDCFCs($dc_id->le_wh_id);
                                    if($dc_id->is_apob == 1){
                                        // IF DC IS VITUAL /APOB
                                        $allapobfcArray = array();
                                        foreach ($alldcfcdata as $key => $fcvalue) {
                                            $allapobfcdata = $this->product_slab_details->getAllDCFCs($fcvalue->le_wh_id);
                                            foreach ($allapobfcdata as $inkey => $invalue) {
                                                array_push($allapobfcArray, (object)array("le_wh_id"=>$invalue->le_wh_id,"legal_entity_id"=>$invalue->legal_entity_id));
                                            }
                                        }
                                        $alldcfcdata = array_merge($alldcfcdata,$allapobfcArray);
                                        array_push($alldcfcdata, (object)array("le_wh_id"=>$dc_id->le_wh_id,"legal_entity_id"=>$dc_id->legal_entity_id));
                                    }else{
                                        if(isset($alldcfcdata[0]) && is_object($alldcfcdata[0])){
                                            $dc_data = $this->product_slab_details->getdcData($dc_id->le_wh_id);
                                            array_push($alldcfcdata, (object)array("le_wh_id"=>$dc_id->le_wh_id,"legal_entity_id"=>$dc_data->legal_entity_id));
                                        }else{
                                            // dc name is given and it has no mapping fc
                                            $dc_data = $this->product_slab_details->getdcData($dc_id->le_wh_id);
                                            $alldcfcdata = [];
                                            $alldcfcdata[] =  (object)array("le_wh_id"=>$dc_id->le_wh_id,"legal_entity_id"=>$dc_data->legal_entity_id);
                                        }
                                    }
                                }
                                elseif(empty($dc_id) || !isset($dc_id->le_wh_id)){
                                    return "Please enter a valid DC";
                                }
                                foreach ($alldcfcdata as $key => $dcfc) {
                                    $slab_data = array(
                                        'category_id'        => $data['category_id'],
                                        'effective_date'     => $effective_date,
                                        'created_by'         => Session::get('userId'),
                                        'dc_id'              => $dcfc->le_wh_id,
                                        'fc_category_margin' => $fc_category_margin,
                                        'dc_category_margin' => $dc_category_margin,
                                        'fc_margin_type'     => $fc_margin_type,
                                        'dc_margin_type'     => $dc_margin_type
                                    );
                                    $uploadResponse =$this->_category->insertUploadProducts($slab_data);
                                    $dc_data = $this->product_slab_details->getdcData($dcfc->le_wh_id);
                                    $le_wh_name = '(' .$dc_data->display_name. ')';
                                    //write for the Text File
                                    $msg .= $uploadResponse['message'] . $le_wh_name . PHP_EOL;
                                    
                                    if($uploadResponse['counter_flag']==1){
                                        $updateCnt++;
                                    }elseif($uploadResponse['counter_flag']==2){
                                        $insertCnt++;
                                    }elseif($uploadResponse['counter_flag']==3){
                                        $errorCnt++;
                                    }
                                }
                                if(count($alldcfcdata) == 0){
                                    $msg .= "#".$excelRowcounter." CATEGORY (".$data['category_id'].") ";
                                    $msg .= "Invalid Data!";
                                    $validFlag = 1;
                                    $msg .= PHP_EOL;
                                }
                            }else{
                                $msg .= PHP_EOL;
                                $errorCnt++;
                            }
                            $excelRowcounter++;
                        }
                        Notifications::addNotification(['note_code' =>'PRS001']);
                        //create the log file as per the excel sheet
                        $file = fopen($file_path, "w");
                        fwrite($file, $msg);
                        fclose($file);
                        DB::commit();
                        return "Data Imported successfully.<br>Added : ".$insertCnt." || Updated :".$updateCnt." || Error : ".$errorCnt.' <a href="/'.$file_path.'" target="_blank"> View Details </a>';
                    }else{
                        DB::rollback();
                        return "Invalid Data";
                    }
                }else{
                    return "Invalid Data!";
                }
            }
        }catch (\ErrorException $ex) {
                DB::rollback();
                Log::error($ex->getMessage());
                Log::error($ex->getTraceAsString());
                return "Sorry Failed to Upload Sheet,Reverting all Records. Please check log for More Details";
        } 
    }
    
    public function getParentCategories(Request $request) {
        $orderby_array = "";
        $filter_by = "";
        $page = $request->input('page');   //Page number
        $page_size = $request->input('pageSize'); //Page size for ajax call

        if ($request->input('$orderby')) {             //checking for sorting
            $order = explode(' ', $request->input('$orderby'));
            $order_query_field = $order[0]; //on which field sorting need to be done
            $order_query_type = $order[1]; //sort type asc or desc
            $order_by_type = 'desc';
            if ($order_query_type == 'asc') {
                $order_by_type = 'asc';
            }
            if (isset($this->grid_field_db_match[$order_query_field])) { //getting appropriate table field based on grid field
                $order_by = $this->grid_field_db_match[$order_query_field];
            }
            $orderby_array = $order_by . " " . $order_by_type;
        }

        if (isset($request['$filter'])) {
            $filter_explode = explode(' and ', $request['$filter']);
            foreach ($filter_explode as $filter_each) {
                $filter_each_explode = explode(' ', $filter_each);
                $length = count($filter_each_explode);
                $filter_query_field = '';
                if ($length > 3) {
                    for ($i = 0; $i < $length - 2; $i++)
                        $filter_query_field .= $filter_each_explode[$i] . " ";
                    $filter_query_field = trim($filter_query_field);
                    $filter_query_operator = $filter_each_explode[$length - 2];
                    $filter_query_value = $filter_each_explode[$length - 1];
                } else {
                    $filter_query_field = $filter_each_explode[0];
                    $filter_query_operator = $filter_each_explode[1];
                    $filter_query_value = $filter_each_explode[2];
                }
                $filter_query_field_substr = substr($filter_query_field, 0, 7);

                if ($filter_query_field_substr == 'startsw' || $filter_query_field_substr == 'endswit' || $filter_query_field_substr == 'indexof' || $filter_query_field_substr == 'tolower') {
                    //Here we are checking the filter is of which type startwith, endswith, contains, doesn't contain, equals, doesn't equal

                    if ($filter_query_field_substr == 'startsw') {
                        $filter_query_field_value_array = explode("'", $filter_query_field);
                        //extracting the input filter value between single quotes, example: 'value'

                        $filter_value = $filter_query_field_value_array[1] . '%';

                        foreach ($this->grid_field_db_match as $key => $value) {
                            if (strpos($filter_query_field, '(' . $key . ')') != 0) {
                                //getting the filter field name
                                $starts_with_value = $this->grid_field_db_match[$key] . ' like ' . $filter_value;
                                $filter_by[] = $starts_with_value;
                            } else {
                                $starts_with_value = "";
                            }
                        }
                    }

                    if ($filter_query_field_substr == 'endswit') {
                        $filter_query_field_value_array = explode("'", $filter_query_field);
                        //extracting the input filter value between single quotes, example: 'value'

                        $filter_value = '%' . $filter_query_field_value_array[1];

                        foreach ($this->grid_field_db_match as $key => $value) {
                            if (strpos($filter_query_field, '(' . $key . ')') != 0) {
                                //getting the filter field name
                                $ends_with_value = $this->grid_field_db_match[$key] . ' like ' . $filter_value;
                                $filter_by[] = $ends_with_value;
                            } else {
                                $ends_with_value = "";
                            }
                        }
                    }

                    if ($filter_query_field_substr == 'tolower') {
                        $filter_query_value_array = explode("'", $filter_query_value);
                        //extracting the input filter value between single quotes, example: 'value'

                        $filter_value = $filter_query_value_array[1];
                        if ($filter_query_operator == 'eq') {
                            $like = ' = ';
                        } else {
                            $like = ' != ';
                        }
                        foreach ($this->grid_field_db_match as $key => $value) {
                            if (strpos($filter_query_field, '(' . $key . ')') != 0) {
                                //getting the filter field name
                                $to_lower_value = $this->grid_field_db_match[$key] . $like . $filter_value;
                                $filter_by[] = $to_lower_value;
                            } else {
                                $to_lower_value = "";
                            }
                        }
                    }

                    if ($filter_query_field_substr == 'indexof') {
                        $filter_query_value_array = explode("'", $filter_query_field);
                        //extracting the input filter value between single quotes ex 'value'

                        $filter_value = '%' . $filter_query_value_array[1] . '%';

                        if ($filter_query_operator == 'ge') {
                            $like = ' like ';
                        } else {
                            $like = ' not like ';
                        }
                        foreach ($this->grid_field_db_match as $key => $value) {
                            if (strpos($filter_query_field, '(' . $key . ')') != 0) {
                                //getting the filter field name
                                $indexof_value = $this->grid_field_db_match[$key] . $like . $filter_value;
                                $filter_by[] = $indexof_value;
                            } else {
                                $indexof_value = "";
                            }
                        }
                    }
                } else {

                    switch ($filter_query_operator) {
                        case 'eq' :
                            $filter_operator = ' = ';
                            break;

                        case 'ne':
                            $filter_operator = ' != ';
                            break;

                        case 'gt' :
                            $filter_operator = ' > ';
                            break;

                        case 'lt' :
                            $filter_operator = ' < ';
                            break;

                        case 'ge' :
                            $filter_operator = ' >= ';
                            break;

                        case 'le' :
                            $filter_operator = ' <= ';
                            break;
                    }

                    if (isset($this->grid_field_db_match[$filter_query_field])) {
                        //getting appropriate table field based on grid field
                        $filter_field = $this->grid_field_db_match[$filter_query_field];
                    }

                    $filter_by[] = $filter_field . $filter_operator . $filter_query_value;
                }
            }
        }

        $getallcats = $this->_category->getParentCats($page, $page_size, $orderby_array, $filter_by);

        $decodedData = json_decode($getallcats['result'], true);
        foreach ($decodedData as $key => $value) {
            if ($value["is_product_class"] == 1) {
                $decodedData[$key]['prodclass'] = "Yes";
            } else {
                $decodedData[$key]['prodclass'] = "No";
            }

            if ($value['is_active'] == 1) {
                $decodedData[$key]['status'] = "Active";
            } else {
                $decodedData[$key]['prodclass'] = "in-Active";
            }

            $decodedData[$key]['actions'] = '<code style="cursor: pointer;"><a data-type="edit" data-id="' . $value['category_id'] . '" data-toggle="modal" data-target="#createrule-modal"><span  style="padding-left:15px;"><i class="fa fa-pencil"></i></span></a><a data-type="edit" data-id="' . $value['category_id'] . '" data-toggle="modal" data-target="#createrule-modal"><span  style="padding-left:15px;"><i  class="fa fa-trash-o" ></i></span></a></code>';
        }

        echo json_encode(array('results' => $decodedData, 'TotalRecordsCount' => $getallcats['count']));

        // echo "<pre>";print_r($getallcats);
    }
public function checkCategoryId(Request $request)
    {
        $data= $request->all();
        //echo $data['cat_id'].'______'.$data['name'];
        $rs= $this->_category->where('cat_name',$data['name'])->where('parent_id','=',$data['cat_id'])->select('category_id')->first();
        $rs= json_decode(json_encode($rs));
        return $rs->category_id;
    }

    public function getChildCategories(Request $request) {
        $path = $request->input('path');
        $page = $request->input('page');   //Page number
        $page_size = $request->input('pageSize'); //Page size for ajax call
        // echo "hi<br>";echo "pagesize = ".$page_size;die;
        // echo "page".$page."Page size = ".$page_size;die;
        // print_r($path);die;
        $explodedata = explode(':', $path);
        // print_r($explodedata);die;
        if (sizeof($explodedata) > 2) {
            $catid = $explodedata[sizeof($explodedata) - 1];
        } else {
            $catid = $explodedata[1];
        }
        // echo $catid;die;
        $getChildData = $this->_category->getChildData($page, $page_size, $catid);
        $decodedData = json_decode($getChildData['result'], true);
        foreach ($decodedData as $key => $value) {
            if ($value["is_product_class"] == 1) {
                $decodedData[$key]['prodclass'] = "Yes";
            } else {
                $decodedData[$key]['prodclass'] = "No";
            }

            if ($value['is_active'] == 1) {
                $decodedData[$key]['status'] = "Active";
            } else {
                $decodedData[$key]['prodclass'] = "in-Active";
            }
            $decodedData[$key]['actions'] = '<code style="cursor: pointer;"><a data-type="edit" data-id="' . $value['category_id'] . '" data-toggle="modal" data-target="#createrule-modal"><span  style="padding-left:15px;"><i class="fa fa-pencil"></i></span></a><a data-type="edit" data-id="' . $value['category_id'] . '" data-toggle="modal" data-target="#createrule-modal"><span  style="padding-left:15px;"><i  class="fa fa-trash-o" ></i></span></a></code>';
        }

        // echo $getChildData['count'];die;

        echo json_encode(array('resultschild' => $decodedData, 'TotalRecordsCount' => $getChildData['count']));
    }

    public function treeCats()
    {
        $userId = Session::get('userId');
        if(Cache::tags(['ebutor', 'categories'])->has('get_category_treegrid_'.$userId))
        {
            echo Cache::tags(['ebutor', 'categories'])->get('get_category_treegrid_'.$userId);
        }else{
            $allCat = $this->_category->allCategory();
            $finalArr = array();
            $parentWiseArr = array();
            foreach($allCat as $key=>$catData){
                if($catData['parent_id'] == 0){
                    $parentWiseArr[$catData['category_id']]['category_id']      = $catData['category_id'];
                    $parentWiseArr[$catData['category_id']]['cat_name']         = $catData['cat_name'];
                    $parentWiseArr[$catData['category_id']]['is_active']        = ($catData['is_active']==1)?'Active':'Inactive';
                    $parentWiseArr[$catData['category_id']]['is_product_class'] = ($catData['is_product_class']==1)?'Yes':'No';
                    $parentWiseArr[$catData['category_id']]['actions']          = '<span><code style="cursor: pointer;"><a data-type="edit" data-id="' . $catData['category_id'] . '" data-toggle="modal"  onclick="editCategory(\''.$catData['cat_name'].'\','.$catData['category_id'].','.$catData['is_active'].','.$catData['is_product_class'].');"><span  style="padding-left:15px;"><i class="fa fa-pencil"></i></span></a></span></code>&nbsp&nbsp<span><a data-type="edit" data-id="' . $catData['category_id'] . '"  onclick="deleteEntityType('.$catData['category_id'].');" ><span  style="padding-left:15px;"><i  class="fa fa-trash-o" ></i></span></a></span>';
                    unset($allCat[$key]);
                    $child = $this->catLoop($catData['category_id'], $allCat);
                    if(!empty($child));
                        $parentWiseArr[$catData['category_id']]['cats'] = $child;

                }
            }

            foreach($parentWiseArr as $value){
                $finalArr[] = $value;
            }
            //\Log::info('we are in '.__METHOD__);
            if(!Cache::tags(['ebutor', 'categories'])->has('get_category_treegrid_'.$userId))
            {
                //\Log::info('we are in else');
                $expiresAt = 10;            
                Cache::tags(['ebutor', 'categories'])->add('get_category_treegrid_'.$userId, json_encode($finalArr), $expiresAt);
            }
            //$results['result'] = $finalArr;
            echo json_encode($finalArr);
            //echo "<pre>";print_r($parentWiseArr);
        }        
    }

    public function catLoop($catId, $catArr){
        $collectChild = array();

        $temp = array();
        if(!empty($catArr)){
            foreach($catArr as $key=>$value){
                if($value['parent_id']==$catId){
                    unset($temp);
                    $temp['category_id']        = $value['category_id'];
                    $temp['cat_name']           = $value['cat_name'];
                    $temp['is_active']          = ($value['is_active']==1)?'Active':'Inactive';
                    $temp['is_product_class']   = ($value['is_product_class']==1)?'Yes':'No';
                    $temp['actions']            = '<code style="cursor: pointer;"><a data-type="edit" data-id="' . $value['category_id'] . '" ><span  style="padding-left:15px;" onclick="editCategory(\''.$value['cat_name'].'\','.$value['category_id'].','.$value['is_active'].','.$value['is_product_class'].');"><i class="fa fa-pencil"></i></span></a><a data-type="edit" data-id="' . $value['category_id'] . '" data-toggle="modal" ><span  style="padding-left:25px;"><i  class="fa fa-trash-o" onclick="deleteEntityType('.$value['category_id'].');"></i></span></a></code>';
                    unset($catArr[$key]);
                    $child = $this->catLoop($value['category_id'], $catArr);
                    if(!empty($child)); 
                        $temp['cats'] = $child;
                    $collectChild[] = $temp;
                    
                    
                }
            } 
        }
        else{
            return $collectChild;
        }
        return $collectChild;
    }


    public function viewCategory() {

        $allowAddCategory = true; // $this->roleRepo->checkPermissionByFeatureCode('CAT002');
        $allowAddCustomerCategory = true; //$this->roleRepo->checkPermissionByFeatureCode('CAT008');
        parent::Breadcrumbs(array('Home' => '/', 'Categories' => '#'));
        $allowedButtons['add_new_parent_category'] = $allowAddCategory;
        $allowedButtons['add_category'] = $allowAddCustomerCategory;
        //$manufacturerList = $this->_product->getManufacturers($this->_manufacturerId);
        $categoryList = $this->getCategoryList();

        return View::make('Categories::category', ['categoryList' => $categoryList, 'allowed_buttons' => $allowedButtons]);
    }

   
    public function getCategoryList() {
        return $this->_category->getCategoryList(0);
    }
     public function uniqueNameValidation()
    {

        $data= Input::all();
        $name=Input::get('name');
        $edit_name= Input::get('edit_name');
        $Response="";
        if($edit_name==$name)
        {
             return '{"valid":true}';
        }
        else
        {
            $rs= $this->_category->where('cat_name',$name)->where('parent_id','=',0)->select('category_id')->first();
            if($rs=="")
            {
               return '{"valid":true}';

            } else {

                return '{"valid":false}';

            }
        
        }
        
    }
    public function uniquevalidation()
    {
        DB::enablequerylog();
        $data= Input::all();
        $name=Input::get('name');
        //$cat_id=Input::get('cat_id');
        $edit_cat_id = Input::get('edit_cat_id');
         $cat_id = Input::get('edit_cat_id1');

        $Response="";

        $rs= $this->_category->where('category_id',$cat_id)->first();
       
        if($rs)
        {   
            if($rs->parent_id!=0)
            {
                $getParent_category= $this->_category->where('category_id',$cat_id)->first();
                
                $Response= $this->_category->where('parent_id',$getParent_category->parent_id)->where('cat_name',$name)->select('cat_name')->first();

            }else
            {
                $Response= $this->_category->where('cat_name',$name)->select('cat_name')->first();
            }
        }
        echo $Response;
    }
    /* this is for all adding and editing categories  is_product_class*/

    public function addNewcategory(Request $request) {
        $data = Input::all();
        $parent_id= (Input::get('parent_id') == "") ? 0 : Input::get('parent_id'); 

        $edit_category_id= (Input::get('edit_category_id') == "") ? "" : Input::get('edit_category_id'); 
       
        if(Input::hasFile('brow_image')!='' && Input::get('check')=="1" )
        {
            if(Input::hasFile('brow_image'))
            {
                $file = Input::file('brow_image');

                $productObj = new ProductRepo();
		        $url = $productObj->uploadToS3($file,'categories',1);
                
               // $destinationPath = public_path() .'/images/categories/';
                $filename = $file->getClientOriginalName();
                //$file->move($destinationPath, Input::get('name').'_'.$filename);
                //$categoryImage=$filename;
                return $url;//'/images/categories/'.Input::get('name').'_'.$filename;
            }
        }
				$file = Input::file('brow_image');
		        $thumbnail_path = 'uploads/thumbnail_images/';
                $ext                = $file->guessClientExtension();  
                // Client file name, including the extension of the client getPrimaryImage
                $fullname           = $file->getClientOriginalName(); 
                // Hash processed file name, including the real extension
                $hashname           = time().'.'.$ext; 
                $upload_success     = $file->move($thumbnail_path, $hashname);
                $thumbnail_name = 'category_thumbnail_img'.$hashname;
                $thumbnail = Image::open($thumbnail_path . $hashname)
                        ->thumbnail(new Imagine\Image\Box(270, 90));
                $thumbnail->save($thumbnail_path . $thumbnail_name);
				$thumb = new ProductRepo();
                $s3ImageUrl2 = $thumb->uploadToS3($thumbnail_path . $thumbnail_name, 'cat_thumbnails', 2);
				
				
        if(Input::get('category_image_local')!='' &&  Input::get('category_image') == "" )
        {
            $categoryImage = Input::get('category_image_local');
        }else
        {
            $categoryImage = (Input::get('category_image') == "") ? "" : Input::get('category_image');    
        }
        // $categoryImage = (Input::get('category_image') == "") ? "" : Input::get('category_image');       
        $edit_category_name= (Input::get('edit_category_name') == "") ? "" : Input::get('edit_category_name');
        $message="Successfully Saved.";
        $checkEditParentCatName="";
        $rs="";

        //this is for insert or update category
        if($edit_category_id!="")
        {  
          if($edit_category_name!=Input::get('name') )
          {
            $checkEditParentCatName= $this->checkCatName(Input::get('name'),$parent_id);
          }
          if($checkEditParentCatName)
          {
            $rs="This name is already exits.";               
          }else
          {                    
              $this->updateCategory($data,$edit_category_id,$parent_id,$categoryImage,$s3ImageUrl2); 
              $rs="Successfully Updated."; 
          }
            //$message = $this->updateCategory($data,$edit_category_id,$parent_id,$categoryImage);
          
        }else
        {
            if($parent_id == 0)
            {              
               $checkParentCatName= DB::table('categories')
                    ->where('cat_name', Input::get('name'))
                    ->pluck('category_id')->all();
                if($checkParentCatName)
                {
                    $rs="This name is already exits.";
                }
                else
                {        
                    
                    $this->saveCategory($data,$parent_id,$categoryImage,$s3ImageUrl2);
                    $rs="Successfully Created.";    
                }
            }else
            {
                $checkParentCatName=$this->checkCatName(Input::get('name'),$parent_id);
                if($checkParentCatName)
                {                    
                    $rs="This name is already exits.";
                }
                else
                {      
                    $rs="Successfully Created.";      
                     $this->saveCategory($data,$parent_id,$categoryImage,$s3ImageUrl2); 
                }
            }          
        }
       
        $rolesObj= new Role();
        $rolesObj->flushCache('categories');
        return Response::json([
                    'status' => true,
                    'message' => $rs
        ]);
    }
    public function checkCatName($name,$parent_id)
    {
        return  DB::table('categories')
                ->where('cat_name', $name)
                ->where('parent_id',$parent_id)
                ->pluck('category_id')->all();
    }
    public function saveCategory($data,$parent_id,$categoryImage,$s3ImageUrl2)
    {
        $categoryId = DB::Table('categories')->insertGetId([
                    'cat_name' => Input::get('name'),
                    'parent_id' => $parent_id,
                    'is_active' => Input::get('is_active'),
                    'is_product_class'=>Input::get('is_product_class'),
                    'image_url'=> $categoryImage,
					'thumbnail_url'=>$s3ImageUrl2	
                    ]); 
                    $mpcategoryId = DB::Table('mp_categories')->insertGetId([
                        'mp_category_id' => $categoryId,
                        'category_name' => Input::get('name'),
                         'mp_id'=>1,
                         'is_leaf_category'=>Input::get('is_product_class'),
                        'parent_category_id' => $parent_id,
                        'image_url'=> $categoryImage,
						'thumbnail_url'=>$s3ImageUrl2
                    ]);
                    $this->segmentsMapping($data,$categoryId,0,$parent_id);
                    $this->addSegemntToCategory($data,$categoryId,0);

        return $categoryId;
    }
    public function updateCategory($data,$edit_category_id,$parent_id,$categoryImage,$s3ImageUrl2)
    {
         $categoryId= DB::Table('categories')
                ->where('category_id', $edit_category_id)
                ->update(array('cat_name' => Input::get('name'),
                    'is_active' => Input::get('is_active'),
                    'parent_id' => $parent_id,
                    'is_product_class' => Input::get('is_product_class'),
                    'image_url'=> $categoryImage,
					'thumbnail_url'=>$s3ImageUrl2					
                ));
            $checkCat=DB::table('mp_categories')->where('mp_category_id',$edit_category_id)->first();
            if($checkCat)
            {
                $mpcategoryId = DB::Table('mp_categories')
                            ->where('mp_category_id',$edit_category_id)
                            ->update(array('category_name' => Input::get('name'),
                                'parent_category_id' => $parent_id,
                                'is_leaf_category' => Input::get('is_product_class'),
                                'image_url'=> $categoryImage,
								'thumbnail_url'=>$s3ImageUrl2								
                            ));
            }else
            {
                 $mpcategoryId = DB::Table('mp_categories')->insertGetId([
                        'mp_category_id' => $edit_category_id,
                        'category_name' => Input::get('name'),
                         'mp_id'=>1,
                         'is_leaf_category'=>Input::get('is_product_class'),
                        'parent_category_id' => $parent_id,
                        'image_url'=> $categoryImage,
						'thumbnail_url'=>$s3ImageUrl2
                    ]);
            }
            
            $this->segmentsMapping($data,$edit_category_id,1,$parent_id);
            $this->addSegemntToCategory($data,$edit_category_id,1);

            return $categoryId;
    }
    public function addSegemntToCategory($data,$edit_category_id,$status)
    {
            DB::table('segment_mapping')
                ->where('mp_category_id',$edit_category_id)
                ->delete();
            foreach (Input::get('segments') as $key)
            {
                 DB::table('segment_mapping')
                ->insert(['value'=>$key,'mp_category_id'=>$edit_category_id,'created_by'=>Session::get('userId')]);
            }

    }
    public function segmentsMapping($data,$categoryId,$status,$parent_id)
    {
        $cat =DB::table('mp_categories')
            ->where('parent_category_id', $categoryId)
            ->where('is_approved','1')
            ->select('mp_category_id', 'parent_category_id')
            ->get()->all();
        if (!empty($cat)) 
        {
            foreach($cat as  $cat1)
            {      
                /*DB::table('segment_mapping')
                ->where('mp_category_id',$cat1->mp_category_id)
                ->delete();  */
                foreach (Input::get('segments') as $key)
                {
                    DB::table('segment_mapping')
                    ->insert(['value'=>$key,'mp_category_id'=>$cat1->mp_category_id,'created_by'=>Session::get('userId')]);
                }
                $this->segmentsMapping($data,$cat1->mp_category_id,$status,$cat1->parent_category_id);
            }
        }    
        
    }
    public function getSegmentsByCategory($categoryId)
    {
         $data= DB::table('segment_mapping')
                ->where('mp_category_id',$categoryId)
                ->select('value')
                ->get()->all();
        $data= json_decode(json_encode($data),true);
        return $data;
    }
    public function deleteCategory($category_id) {
        DB::enablequerylog();
        $checkSubcat = DB::Table('categories')->where('parent_id', '=', $category_id)->first();
        if ($checkSubcat) {
            return Response::json([
                        'status' => false,
                        'message' => 'Please Delete Product  classes.'
            ]);
        } else {
            $check_attributesSetTbl = DB::Table('attribute_sets')->where('category_id', '=', $category_id)->first();
            if ($check_attributesSetTbl) {
                return Response::json([
                            'status' => false,
                            'message' => 'This is already associated to Attribute Set.'
                ]);
            } else {
                $check_product_att = DB::Table('products')->where('category_id', '=', $category_id)->get()->all();
                if ($check_product_att) {
                    return Response::json([
                                'status' => false,
                                'message' => 'This is already associated with Products.'
                    ]);
                } else {
                     DB::Table('categories')->where('category_id', '=', $category_id)->orWhere('parent_id', '=', $category_id)->delete();
                   
                     DB::Table('mp_categories')->where('mp_category_id', '=', $category_id)->orWhere('parent_category_id', '=', $category_id)->delete();
                     $rolesObj= new Role();
                    $rolesObj->flushCache('categories');
                    return Response::json([
                                'status' => true,
                                'message' => 'Successfully Deleted.'
                    ]);
                }
            }
        }
    }

    

}
