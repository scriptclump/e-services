<?php
/*
FileName : uploadPromotionFiles
Author   : eButor
Description :
CreatedDate : 31/Aug/2016
*/
//defining namespace
namespace App\Modules\Promotions\Controllers;
//loading namespaces
use App\Http\Controllers\BaseController;
use App\Modules\Promotions\Models\uploadPromotionSlab;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Config;
use Excel;
use Carbon\Carbon;
use Session;
use Notifications;
use Log;
use Redirect;
use App\Modules\Pricing\Models\uploadSlabProductsModel;
use App\Central\Repositories\ProductRepo;
use App\Central\Repositories\RoleRepo;
use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;
use App\Modules\PurchaseOrder\Models\PurchaseOrder;
use App\Modules\Promotions\Models\slabDetailsModel;
use DB;

class uploadPromotionFiles extends BaseController{
    //calling model 
    public function __construct() {
        $this->obj_uploadPromotionSlab = new uploadPromotionSlab();
        $this->uploadslab = new uploadSlabProductsModel();
        $this->repo = new ProductRepo();
        $this->role = new RoleRepo();
    }

    /**
     * [downloadExcelWithData Create the excel file as per the data selection]
     * @param  Request $request [date range,manufacturer,state,brand, cust type]
     * @return [excel]           [Downloads excel with slab information]
     */
    public function downloadExcelWithData(Request $request)
    {
           
        try{
            $mdl_state = $request->input("mdl_state");
            $mdl_custgroup = $request->input("mdl_custgroup");
            $mdl_manufac = $request->input("mdl_manufac");
            $mdl_brand = $request->input("mdl_brand");

            $selectionQueryOuter = array();
            $selectionQueryInner = array();

            if($mdl_manufac!='' && $mdl_manufac!='all'){
                $selectionQueryOuter[] = "prd.manufacturer_id = '".$mdl_manufac."'";
            }
            if($mdl_brand!='' && $mdl_brand!='all'){
                $selectionQueryOuter[] = "prd.brand_id = '".$mdl_brand."'";
            }

            if($mdl_state!='' && $mdl_state!='all'){
                $selectionQueryOuter[] = "prd.state_id = '".$mdl_state."'";
            }
            if($mdl_custgroup!='' && $mdl_custgroup!='all'){
                $selectionQueryOuter[] = "prd.customer_type = '".$mdl_custgroup."'";
            }

            $mytime = Carbon::now();
            $headers_line_one = array('', '', '', '', '', '','', '(m/d/y)', '(m/d/y)','','', 'Slab 1', 'Slab 2', 'Slab 3');
            $headers_line_two = array('SKU','PRODUCT_TITLE','MRP', 'LOCK_QUANTITY', 'STATE','CUSTOMER_GROUP','DC_NAME','START_DATE', 'END_DATE','onlydc','onlyfc', 'PACK1','ESU1','SLAB_QTY1', 'PRICE1','PACK2','ESU2', 'SLAB_QTY2', 'PRICE2','PACK3','ESU3', 'SLAB_QTY3', 'PRICE3');
                //'PACK4','ESU4', 'SLAB_QTY4', 'PRICE4', 'PACK5','ESU5','SLAB_QTY5', 'PRICE5');
            $headers_second_page = array('STATE','CUSTOMER_GROUP','PACK_NAME','DC NAME');

            $state = $this->obj_uploadPromotionSlab->getStateByID($mdl_state);
            
            $exceldata = json_decode($this->obj_uploadPromotionSlab->getDataAsPerQuery($selectionQueryInner, $selectionQueryOuter, $state), true);
            $excelcounter=0;

            foreach($exceldata as $dataExcel){

                if($dataExcel['pack_type']!=''){
                    $exceldata[$excelcounter]['pack_type']=$this->obj_uploadPromotionSlab->getPackNameById($dataExcel['pack_type']);
                }
                $excelcounter++;
               
            }

            

            $stateDet = json_decode($this->obj_uploadPromotionSlab->getAllState(), true);
            $customerDet = json_decode($this->obj_uploadPromotionSlab->getAllCustomerType(), true);
            $packdata=json_decode($this->obj_uploadPromotionSlab->getPacktypeData(),true);

            $dcDet = json_decode($this->obj_uploadPromotionSlab->getAllDCType(), true);

            $loopCounter = 0;
            $exceldata_second = array();
            foreach($dcDet as $val){
                $exceldata_second[$loopCounter]['state'] = isset($stateDet[$loopCounter]) ? $stateDet[$loopCounter]['ItemName'] : '';
                $exceldata_second[$loopCounter]['customer'] = isset($customerDet[$loopCounter]) ? $customerDet[$loopCounter]['ItemName'] : '';
                $exceldata_second[$loopCounter]['packname'] = isset($packdata[$loopCounter])?$packdata[$loopCounter]['packname']:'';
                 $exceldata_second[$loopCounter]['dc'] = $val['lp_wh_name'];
                $loopCounter++;
            }
            
            Excel::create('Slab_Promotion_Template_Sheet-'.$mytime->toDateTimeString(), function($excel) use($headers_line_one, $headers_line_two, $exceldata, $headers_second_page, $exceldata_second) 
            {
                $excel->sheet("SlabDetails", function($sheet) use($headers_line_one, $headers_line_two, $exceldata)
                {
                    $sheet->loadView('Promotions::promotionSlabTemplate', array('headers_one' => $headers_line_one, 'headers_two' => $headers_line_two, 'data' => $exceldata)); 
                });

                $excel->sheet("State_and_Customer_Data", function($sheet) use($headers_second_page, $exceldata_second)
                {
                    $sheet->loadView('Promotions::stateAndCusomerSampleTemplate', array('headers' => $headers_second_page, 'data' => $exceldata_second)); 
                });
            })->export('xlsx');
                            
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            Redirect::to('/')->send();
        } 
    }

    /**
     * [uploadPromotionSlab Upload promotion Slab Details]
     * @param  Request $request [Promotion information]
     * @return [string]           [message with upload information]
     */
    public function uploadPromotionSlab(Request $request){
        try{
            DB::beginTransaction();
        
            $file_data                      = Input::file('slab_data');
            $file_name                      = $file_data->getClientOriginalName();
            $file_extension                 = $file_data->getClientOriginalExtension();

            if( $file_extension != 'xlsx'){
                return 'Invalid file type';
            }elseif (Input::hasFile('slab_data')) {  
                $path                           = Input::file('slab_data')->getRealPath();
                $data                           = $this->readExcel($path);
                $file_data                      = Input::file('slab_data');
                $result                         = json_decode($data['prod_data'], true);
                $headers                        = json_decode($data['cat_data'], true); 
                
                // Generate the name of the file

               // echo "<pre/>";print_r($data);exit;
                $timestamp = md5(microtime(true));
                $file_path = 'download' . DIRECTORY_SEPARATOR . 'promotion_log' . DIRECTORY_SEPARATOR . 'promotion-slab-import-' . $timestamp . '.txt';
                $msg = '';
                $existCnt = $notDelCnt = $updateCnt = $insertCnt = $errorCnt = $notFoundCnt = 0;
                $excelRowcounter = 2;
                foreach($result as $key => $data){
                    $rowCounter=1;
                    if($data['sku'] && $data['state'] && $data['customer_group']){                        

                        $msg .= "#".$excelRowcounter." SKU (".$data['sku'].") ";

                        // get all the ID as per the data in excel
                        $product_id = $this->obj_uploadPromotionSlab->getProductID($data['sku']);
                        $state = $this->obj_uploadPromotionSlab->getState($data['state']);
                        $customer_type = $this->obj_uploadPromotionSlab->getCustomerType($data['customer_group']);
                        $lock_qty = $data['lock_quantity'];

                        // assign dates
                        $start_date = is_array($data['start_date'])? $data['start_date']['date'] : "1970-01-01";
                        $start_date = date("Y-m-d", strtotime($start_date));

                        log::info($start_date);

                        $end_date = is_array($data['end_date']) ? $data['end_date']['date'] : "1970-01-01";
                        $end_date = date("Y-m-d", strtotime($end_date));
                        log::info($end_date);
                        // Check for valid data
                        $validFlag = 0;
                        $flagForInsert=1;
                        if($product_id==0 || $product_id==''){
                            $msg .= " is not valid!";
                            $validFlag = 1;
                        }
                        
                        // check the Date Range validation
                        $dateDiff = date_diff(date_create($start_date), date_create($end_date));
                        if( $dateDiff->invert==1 ){
                            $msg .= " : Start date is greater than End date!";
                            $validFlag = 1;
                        }

                        if($state==0 || $state==''){
                            $msg .= " : State is not valid!";
                            $validFlag = 1;  
                        }
                        if($customer_type==0 || $customer_type==''){
                            $msg .= " : Customer group is not valid!";
                            $validFlag = 1;
                        }
                        $getalldcfc=array();
                        if(isset($data['dc_name'])){
                            $getdcid = $this->obj_uploadPromotionSlab->getDcId($data['dc_name']);
                            $purchaseOrder = new PurchaseOrder();
                            if(!$getdcid){
                                $msg .= " dc Name is not valid!";
                                $validFlag = 1;
                            }else{
                                $legal_entity_type_id = $purchaseOrder->getLegalEntityTypeId($getdcid->legal_entity_id);
                                if(strtolower(trim($data['onlyfc']))=='yes'){
                                    if($legal_entity_type_id==1014){
                                        $getalldcfc[count($getalldcfc)] = json_decode(json_encode(array("le_wh_id"=>$getdcid->le_wh_id)));
                                    }else{
                                        $getalldcfc = $this->uploadslab->getAllDCFCs($getdcid->le_wh_id);
                                    }
                                }
                                if(strtolower(trim($data['onlydc']))=='yes'){
                                    if($legal_entity_type_id!=1014){
                                        $getalldcfc[count($getalldcfc)] = json_decode(json_encode(array("le_wh_id"=>$getdcid->le_wh_id)));
                                    }
                                }
                            }
                        }else{
                            $getalldcfc=$this->uploadslab->getAllDCByState($state);
                        }
                        foreach ($getalldcfc as $value) {
                            $dc=$this->obj_uploadPromotionSlab->getDcNameById($value->le_wh_id);
                            if($dc){
                                $msg .= $dc->display_name ."-  ";
                            }
                            $isProductExist = $this->obj_uploadPromotionSlab->isProductExistForLe($value->le_wh_id,$product_id);
                            //if($isProductExist>0){
                                $checkSlabExist=array(
                                'prmt_customer_group'           => $customer_type,
                                'applied_ids'                   => $product_id,
                                'start_date'                    => $start_date,
                                'end_date'                      => $end_date,
                                'warehouse'                     => $value->le_wh_id);
                                $validateSlab=0;
                                $validateSlab=$this->obj_uploadPromotionSlab->checkIsSlabExist($checkSlabExist);
                                if($validateSlab == 1){
                                    $msg .=" : slab already exist on given date";
                                }
                        
                                if($validFlag==0 && $validateSlab==0){
                                    $main_promotion_data = array(
                                        'prmt_tmpl_Id'                  => "1",
                                        'prmt_det_name'                 => "Slab Applied On : " . $data['sku'],
                                        'prmt_offer_value'              => '0',
                                        'prmt_condition_value1'         => '0',
                                        'prmt_condition_value2'         => '0',
                                        'prmt_description'              => '',
                                        'prmt_label'                    => '',
                                        'prmt_free_product'             => '',
                                        'prmt_free_qty'                 => '0',
                                        'offon_free_product'            => '0',
                                        'is_percent_on_free'            => '0',
                                        'prmt_condition'                => "Range",
                                        'prmt_states'                   => $state,
                                        'prmt_customer_group'           => $customer_type,
                                        'prmt_offer_on'                 => "Product",
                                        'applied_ids'                   => $product_id,
                                        'prmt_det_status'               => "1",
                                        'start_date'                    => $start_date,
                                        'end_date'                      => $end_date,
                                        'prmt_offer_type'               => "Slab",
                                        'prmt_lock_qty'                 => $lock_qty,
                                        'legal_entity_id'               => Session::get('legal_entity_id'),
                                        'created_by'                    => Session::get('userId'),
                                        'created_at'                    => date('Y-m-d H:m:s'),
                                        'warehouse'                     => $value->le_wh_id
                                    );

                                    // storing data into main table
                                    $uploadResponse = $this->obj_uploadPromotionSlab->insertPromotionSlabsMain($main_promotion_data);

                                    // save data into slab Table
                                    if( $uploadResponse['main_table_id']!="0" ){

                                        // Delete table before update
                                        if( $uploadResponse['counter_flag']=="3" ){
                                            $this->obj_uploadPromotionSlab->deleteFromDetailsTable($uploadResponse['main_table_id']);
                                        }

                                        $slabCounter = 1;
                                        $checkConfigFlag = 0;
                                        $packs = array();
                                        $packFlag = 0;
                                        
                                        for($slabCounter=1; $slabCounter<=3; $slabCounter++){

                                            $configFlagEachLine = 0;
                                            $duplicateDataExist=0;
                                            $configFlagPriceCheck=0;

                                            if($data['esu'.$slabCounter]!='' && $data['esu'.$slabCounter]!='0' && $data['pack'.$slabCounter]!='' && $data['pack'.$slabCounter]!='0' && isset($data['esu'.$slabCounter]) && isset($data['pack'.$slabCounter]) && $data['slab_qty'.$slabCounter]!='' && $data['slab_qty'.$slabCounter]!='0' && isset($data['slab_qty'.$slabCounter])&& $data['price'.$slabCounter]!='' && $data['price'.$slabCounter]!='0' && isset($data['price'.$slabCounter])){

                                                // Get the Product Pack information which is entered
                                                $productPackInfo = $this->obj_uploadPromotionSlab->getPacktype($data['pack'.$slabCounter], $product_id, $data['esu'.$slabCounter]);
                                                $getData = json_decode($productPackInfo,true);
                                                $slabQty = $getData[0]['no_of_eaches']*$data['esu'.$slabCounter];
                                                $priceCheck=is_numeric($data['price'.$slabCounter]);
                                                if($priceCheck==1){
                                                    $configFlagPriceCheck=1;
                                                }
                                                else{
                                                    $configFlagPriceCheck=0;
                                                    $msg .= " Promotion slab price is not matching" . PHP_EOL;

                                                }
                                                if($getData[0]['count'] > 0){
                                                    $checkConfigFlag++;
                                                    $configFlagEachLine=1;
                                                }
                                                if($data['slab_qty'.$slabCounter]==$slabQty){
                                                    $checkSlabData=1;
                                                }
                                                else{
                                                    if($getData[0]['count'] > 0){
                                                    $checkConfigFlag--;
                                                    }
                                                    $checkSlabData=0;
                                                   
                                                }
                                                for($duplicateCounter=1;$duplicateCounter<$slabCounter;$duplicateCounter++){
                                                    if($data['pack'.$slabCounter]==$data['pack'.$duplicateCounter]&&$data['slab_qty'.$slabCounter]==$data['slab_qty'.$duplicateCounter]){
                                                        $duplicateDataExist=1;
                                                    }
                                                }
                                                    
                                            }

                                            if(isset( $data['slab_qty'.$slabCounter]) && $configFlagEachLine==1 && $checkSlabData==1 && $duplicateDataExist==0 && $configFlagPriceCheck==1){
                                               $slab_data = array(

                                                    "prmt_det_id"       =>  $uploadResponse['main_table_id'],
                                                    "end_range"         =>  $data['slab_qty'.$slabCounter],
                                                    "price"             =>  $data['price'.$slabCounter],
                                                    "pack_type"         =>  $getData[0]['level'],
                                                    "esu"               =>  $data['esu'.$slabCounter],
                                                    "product_star_slab" =>  $getData[0]['star'],
                                                    "product_id"        =>  $product_id,
                                                    "state_id"          =>  $state,
                                                    "customer_type"     =>  $customer_type,
                                                    "prmt_lock_qty"     =>  $lock_qty,
                                                    "start_date"        =>  $start_date,
                                                    "end_date"          =>  $end_date,
                                                    "prmt_det_status"   =>  "1",
                                                    'created_by'        => Session::get('userId'),
                                                    'wh_id'             => $value->le_wh_id,
                                                );
                                               if( $uploadResponse['counter_flag']=="3" ){
                                                $slab_data['updated_by'] = Session::get('userId');
                                               }
                                                // storing data into main table
                                                $uploadResponseSlab = $this->obj_uploadPromotionSlab->insertSlabData($slab_data);
                                            }

                                            $slab_data="";



                                            if($duplicateDataExist==0 && $checkSlabData==1 && $configFlagEachLine!=0&&$configFlagPriceCheck==1){

                                                $msg.="pack$slabCounter is inserted".PHP_EOL;
                                                $packFlag++;
                                                $eaches = $data['slab_qty'.$slabCounter] / $data['esu'.$slabCounter];
                                                //$singleunitprice = $data['price'.$slabCounter]/$eaches;
                                                $packs[count($packs)]=array(
                                                    "esu" =>$data['esu'.$slabCounter],
                                                    "slab_qty" =>$data['slab_qty'.$slabCounter],
                                                    "price" =>$data['price'.$slabCounter],
                                                    "eaches" => $eaches
                                                );
                                            }
                                            else{
                                                if($duplicateDataExist!=0){
                                                     $msg.="The given pack configuration for pack $slabCounter is already exist".PHP_EOL;
                                                }
                                                if($configFlagEachLine==0||$checkSlabData==0 || $configFlagPriceCheck!=1){
                                                     $msg .="The given pack configuration of slab $slabCounter is not matching".PHP_EOL;
                                                }                                       
                                            }

                                          
                                        }
                                        if($packFlag>0){
                                            log::info($packs);

                                            //$sorted_array[0]=$packs[0];
                                            for($index=0;$index<count($packs);$index++){
                                                for($j=0;$j<count($packs)-$index;$j++){
                                                    if($packs[$j]['price'] > $packs[$index]['price']){
                                                        $temp=$packs[$j];
                                                        $packs[$j] = $packs[$index];
                                                        $packs[$index] = $temp;
                                                    }
                                                }
                                            }
                                            $pack_data = $this->obj_uploadPromotionSlab->getPacksToApplySlab($packs[0]['slab_qty'],$product_id);
                                            $slab_pack = array();
                                            foreach ($pack_data as $key => $packing) {
                                                $slab_pack[] = array(

                                                    "prmt_det_id"       =>  $uploadResponse['main_table_id'],
                                                    "end_range"         =>  $packing['no_of_eaches'],
                                                    "price"             =>  $packs[0]['price'],
                                                    "pack_type"         =>  $packing['level'],
                                                    "esu"               =>  $packing['esu'],
                                                    "product_star_slab" =>  $packing['star'],
                                                    "product_id"        =>  $product_id,
                                                    "state_id"          =>  $state,
                                                    "customer_type"     =>  $customer_type,
                                                    "prmt_lock_qty"     =>  $lock_qty,
                                                    "start_date"        =>  $start_date,
                                                    "end_date"          =>  $end_date,
                                                    "prmt_det_status"   =>  "1",
                                                    'created_by'        => Session::get('userId'),
                                                    'wh_id'             => $value->le_wh_id,
                                                );
                                            }
                                            $objSlab = new slabDetailsModel();
                                            $objSlab->insertSlabDetails($slab_pack);
                                        }

                                        // Delete the main Table data as there is no pack matching
                                        if($checkConfigFlag==0){
                                            // Delete here
                                            $this->obj_uploadPromotionSlab->deleteMainDetails($uploadResponse['main_table_id']);
                                        }

                                        if($checkConfigFlag==0){
                                            $msg .= " Promotion slab is not matching" . PHP_EOL;
                                            //$insertCnt++;
                                            $notFoundCnt++;
                                        }else{

                                            if( $uploadResponse['counter_flag']=="1" ){
                                                $msg .= " Promotion slab inserted" . PHP_EOL;
                                                $insertCnt++;
                                            }else if( $uploadResponse['counter_flag']=="3" ){
                                                $msg .= " Promotion slab Updated" . PHP_EOL;
                                                $updateCnt++;
                                            }
                                        }

                                    }else{

                                        if( $uploadResponse['counter_flag']=="3" ){
                                            $msg .= $uploadResponse['message'] . PHP_EOL;
                                            $existCnt++;
                                        }else{
                                            $msg .= " Error! Slab not inserted" . PHP_EOL;
                                            $errorCnt++;
                                        }
                                    }

                                }else{
                                    if($validateSlab == 0)
                                    $msg.=" data entered is not relevent";
                                    $msg .= PHP_EOL;
                                    $notFoundCnt++;
                                }
                            /*}else{
                                $msg.=" product not mapped". PHP_EOL;
                            }*/
                        }                        
                        $rowCounter++;
                    }else{
                        $msg .= "#".$excelRowcounter." Data entered is not relevent!" . PHP_EOL;
                    }
                    // excel row incrementer
                    $excelRowcounter++;
                }
                //create the log file as per the excel sheet
                $file = fopen($file_path, "w");
                fwrite($file, $msg);
                fclose($file);
                if($insertCnt>0){
//                    Log::info('in slab add');
                    $notificationMessage="Slabs added successfully";
                    //Notifications::addNotification(['note_code' =>'SLAB002']);
                    $users=$this->role->getUsersByFeatureCode('SLAB001');
                    if(count($users)>0){
                        $users=json_decode(json_encode($users),1);
                        $userIds=array_column($users, 'user_id');
                        $approvalFlowObj = new CommonApprovalFlowFunctionModel();
                        $userIds=implode(',',$userIds);
                        $deviceToken = $approvalFlowObj->getRegIds($userIds);
                        //$this->repo->pushNotifications($notificationMessage, $deviceToken, "",'Ebutor','','','');
                    }
                }
                DB::commit();
                return "Data Imported successfully.<br>Added : ".$insertCnt." || Updated :".$updateCnt." || Duplicate Found : ".$existCnt." || Error : ".$notFoundCnt.' <a href="/'.$file_path.'" target="_blank"> View Details </a>';
            }else{
                DB::rollback();
                return 'File with no data!';  
            }
                            
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            DB::rollback();
            Redirect::to('/')->send();
        } 
        

    }
    /**
     * [readExcel read excel data]
     * @param  [string] $path [file path]
     * @return [array]       [Excel data]
     */
    public function readExcel($path) {
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
            $data['cat_data'] = $cat_data;
            $data['prod_data'] = $prod_data;
            return $data;

        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            Redirect::to('/')->send();
        } 

    }

    // Get all Brands as per Manufacture
    /**
     * [getBrandsAsManufac Get brands on the basis manufacturer]
     * @param  [int] $manufac [manufacturer id]
     * @return [array]          [brands information under a manufacturer]
     */
    public function getBrandsAsManufac($manufac){
        return  $this->obj_uploadPromotionSlab->getBrandsAsManufacId($manufac);
    }
    public function sort($a,$b)
    {
        if ($a['selling_price'] == $b['selling_price']) return 0;
        return ($a['selling_price'] > $b['selling_price']) ? 1 : -1;
    }

    
                        
}