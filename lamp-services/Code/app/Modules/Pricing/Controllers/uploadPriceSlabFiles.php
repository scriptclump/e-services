<?php
/*
FileName : uploadPriceSlabFiles
Author   : eButor
Description :
CreatedDate : 10/Aut/2016
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
use Excel;
use Carbon\Carbon;
use Session;
use Notifications;
use UserActivity;
use Mail;
use App\Modules\Users\Models\Users;
use App\Modules\Notifications\Models\NotificationsModel;
use Log;
use Cache;
use App\Lib\Queue;
use DB;
use Utility;
class uploadPriceSlabFiles extends BaseController{

    private $product_slab_details='';
    private $objPriginController='';

    //calling model 
    public function __construct() {
        $this->objPriginController = new pricingDashboadController();
        $this->product_slab_details = new uploadSlabProductsModel();
    }


    // Upload promotion Slab Details
    public function uploadPriceSlab(Request $request){

    try{
        DB::beginTransaction();
        $name = Session::all();

        $environment    = env('APP_ENV');
        $mailHTML       = "";

        $file_data                      = Input::file('price_data');
        //$file_name                      = $file_data->getClientOriginalName();
        $file_extension                 = $file_data->getClientOriginalExtension();

        if( $file_extension != 'xlsx'){
            return 'Invalid file type';
        }else{
            
            if (Input::hasFile('price_data')) {
                
                $path                           = Input::file('price_data')->getRealPath();
                $data                           = $this->readExcel($path);

                $file_data                      = Input::file('price_data');

                $result                         = json_decode(json_encode($data['prod_data']), true);

                $headers                        = json_decode(json_encode($data['cat_data']), true);
                $headers1                       = array('PRODUCT_ID','SKU','PRODUCT_TITLE','MRP','CUSTOMER_GROUP','STATE','DC_NAME','IS_APOB','ALL DCs','ALL FCs','EFFECTIVE_DATE(m/d/y)','ESP','PTR','IS_DELETE','FC SKU MARGIN','FC MARGIN(Percentage/Value)','DC SKU MARGIN','DC MARGIN(Percentage/Value)');
                $recordDiff                         = array_diff($headers,$headers1);
                if(empty($recordDiff) && count($recordDiff)==0){

                    $timestamp = md5(microtime(true));
                    $txtFileName = 'pricing-import-' . $timestamp . '.txt';

                    $file_path = 'download' . DIRECTORY_SEPARATOR . 'pricing_log' . DIRECTORY_SEPARATOR . $txtFileName;
                    $msg = '';
                    $deleteCnt = $notDelCnt = $updateCnt = $insertCnt = $errorCnt = $notFoundCnt = 0;
                    $excelRowcounter = 2;
                    ini_set('max_execution_time', 0);
                    $cache_flush_array = array();
                    $price_change_data = array();
                    foreach($result as $key => $data){

                        if($data['sku'] && isset($data['esp']) ){
                            $msg .= "#".$excelRowcounter." SKU (".$data['sku'].") ";
                            // get all the ID as per the data in excel
                            $product_id = $this->product_slab_details->getProductID($data['sku']);
                            $state = $this->product_slab_details->getState($data['state']);
                            $customer_type = $this->product_slab_details->getCustomerType($data['customer_group']);
                            $dc_id = $this->product_slab_details->getdcID($data['dc_name']);
                            $fc_sku_margin = isset($data['fc_sku_margin'])?$data['fc_sku_margin']:NULL;
                            $dc_sku_margin = isset($data['dc_sku_margin'])?$data['dc_sku_margin']:NULL;
                            $fc_margin_type = isset($data['fc_marginpercentagevalue'])?$data['fc_marginpercentagevalue']:NULL;
                            $dc_margin_type = isset($data['dc_marginpercentagevalue'])?$data['dc_marginpercentagevalue']:NULL;
                            // get Product TAX info (if TAX is not inserted then price should not accepted)
                            // call fro same state
                            $getTax = $this->objPriginController->getTaxByState($product_id, $state, $state);
                            // $getTax = $this->objPriginController->getTaxByState($product_id, $dc_id->state, $dc_id->state);
                            // get VAT
                            $taxDataVAT = 1;
                            if( !isset($getTax['ResponseBody']) || $getTax['ResponseBody'] =='Tax Mapping for Product Not Found' ){
                                $taxDataVAT = 0;
                            }

                            // assign dates
                            $effective_date = is_array($data['effective_datemdy']) ? $data['effective_datemdy']['date'] : $data['effective_datemdy'];
                            $effective_date = date("Y-m-d", strtotime($effective_date));

                            // Is delete value
                            $is_delete_flag = $data['is_delete'];

                            $mailHTML   .= "
                                <tr>
                                    <td>".$product_id."</td>
                                    <td>".$data['sku']."</td>
                                    <td>".$data['dc_name']."</td>
                                    <td>".$data['product_title']."</td>
                                ";

                            $blankTRs = "<td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>";

                            // Check for valid data
                            $validFlag = 0;
                            if($product_id==0 || $product_id==''){
                                $msg .= " is not valid!";
                                $validFlag = 1;
                                $mailHTML   .= $blankTRs . "
                                    <td>Invalid Product!</td>
                                ";
                            }
                            if($state==0 || $state==''){
                                $msg .= " : State is not valid!";
                                $validFlag = 1;
                                $mailHTML   .= $blankTRs . "
                                    <td>State is not valid!</td>
                                ";  
                            }
                            if($fc_sku_margin < 0 || (is_numeric($fc_sku_margin) != 1 && $fc_sku_margin!= NULL)){
                                $msg .= " : FC SKU Margin is not valid!";
                                $validFlag = 1;
                                $mailHTML   .= $blankTRs . "
                                    <td>FC SKU Margin is not valid!</td>
                                ";
                            }else{
                                if(!empty($fc_sku_margin) && $fc_margin_type == ''){
                                    $msg .= " : Please enter the FC margin type(Percentage/Value)!";
                                    $validFlag = 1;
                                    $mailHTML   .= $blankTRs . "
                                        <td>Please enter the FC margin type(Percentage/Value)!</td>
                                    ";
                                }elseif(!empty($fc_sku_margin) && $fc_margin_type != 'Percentage' && $fc_margin_type != 'Value'){
                                        $msg .= " : Please enter valid FC margin type(Percentage/Value)";
                                        $validFlag = 1;
                                        $mailHTML   .= $blankTRs . "
                                        <td>Please enter valid FC margin type(Percentage/Value)</td>
                                        ";
                                    }
                            }
                            if($fc_sku_margin == NULL && !empty($fc_margin_type)){
                                $msg .= ": Please enter the FC SKU Margin";
                                $validFlag = 1;
                                $mailHTML   .= $blankTRs . "
                                    <td>Please enter the FC SKU Margin</td>
                                    ";
                            }
                            if($dc_sku_margin < 0 || (is_numeric($dc_sku_margin) != 1 && $dc_sku_margin!= NULL)){
                                $msg .= " : DC SKU Margin is not valid!";
                                $validFlag = 1;
                                $mailHTML   .= $blankTRs . "
                                    <td>DC SKU Margin is not valid!</td>
                                ";
                            }else{
                                if(!empty($dc_sku_margin) && $dc_margin_type == ''){
                                    $msg .= " : Please enter the DC margin type(Percentage/Value)!";
                                    $validFlag = 1;
                                    $mailHTML   .= $blankTRs . "
                                        <td>Please enter the DC margin type(Percentage/Value)!</td>
                                    ";
                                }elseif(!empty($dc_sku_margin) && $dc_margin_type != 'Percentage' && $dc_margin_type != 'Value'){
                                        $msg .= " : Please enter valid DC margin type(Percentage/Value)";
                                        $validFlag = 1;
                                        $mailHTML   .= $blankTRs . "
                                        <td>Please enter valid DC margin type(Percentage/Value)</td>
                                        ";
                                    }
                            }
                            if($dc_sku_margin == NULL && !empty($dc_margin_type)){
                                $msg .= ": Please enter the DC SKU Margin";
                                $validFlag = 1;
                                $mailHTML   .= $blankTRs . "
                                    <td>Please enter the DC SKU Margin</td>
                                    ";
                            }
                            // if($dc_id->state==0 || $dc_id->state==''){
                            //     $msg .= " : State is not valid!";
                            //     $validFlag = 1;
                            //     $mailHTML   .= $blankTRs . "
                            //         <td>State is not valid!</td>
                            //     ";  
                            // }
                            /*if($data['dc_name']==0 || $data['dc_name']==''){
                                $msg .= " : DC Name is not valid!";
                                $validFlag = 1;
                                $mailHTML   .= $blankTRs . "
                                    <td>DC Name is not valid!</td>
                                ";  
                            }*/

                            if($customer_type==0 || $customer_type==''){
                                $msg .= " : Customer group is not valid!";
                                $validFlag = 1;
                                $mailHTML   .= $blankTRs . "
                                    <td>Customer group is not valid!</td>
                                ";
                            }
                            if($effective_date=="" || $effective_date=='1970-01-01'|| (strpos($effective_date,'1900') !== false)){
                                $msg .= " : Effective Date is not valid, please check date format (m/d/yyyy)!";
                                $validFlag = 1;  
                                $mailHTML   .= $blankTRs . "
                                    <td>Effective Date is not valid, please check date format (m/d/yyyy)!</td>
                                ";
                            }
                            $mrp = $data['mrp'];
                            $esp = $data['esp'];
                            if(!is_numeric($esp) || $esp>$mrp || $esp<=0 ){
                                $msg .= " : ESP is morethan MRP or not valid!";
                                $validFlag = 1;
                                $mailHTML   .= $blankTRs . "
                                    <td>ESP is morethan MRP or not valid!</td>
                                ";  
                            }

                            $ptr = $data['ptr'];
                            if(!is_numeric($ptr) || $ptr>$mrp || $ptr<=0 ){
                                $msg .= " : PTR is morethan MRP or not valid!";
                                $validFlag = 1; 
                                $mailHTML   .= $blankTRs . "
                                    <td>PTR is morethan MRP or not valid!</td>
                                ";  
                            }
                            if($taxDataVAT==0){
                                $msg .= " : " . trans('pricing.UI_PRICE_TAX_NOT_FOUND');
                                $validFlag = 1; 
                                $mailHTML   .= $blankTRs . "
                                    <td>". trans('pricing.UI_PRICE_TAX_NOT_FOUND')."</td>
                                ";    
                            }
                            if($validFlag==0){
                                $all_dcs = isset($data['all_dcs']) ? strtolower($data['all_dcs']) : 'no';
                                $all_fcs = isset($data['all_fcs']) ? strtolower($data['all_fcs']) : 'no';
                                $is_apob = isset($data['is_apob']) ? strtolower($data['is_apob']) : 'no';

                                if($all_dcs != 'no' && $all_dcs != 'yes')
                                    $all_dcs = 'no';
                                if($all_fcs != 'no' && $all_fcs != 'yes')
                                    $all_fcs = 'no';
                                if($is_apob != 'no' && $is_apob != 'yes')
                                    $is_apob = 'no';
                                                      
                                $alldcfcdata = array();
                                if(isset($dc_id->le_wh_id) && $dc_id->le_wh_id != 0){
                                    $alldcfcdata = $this->product_slab_details->getAllDCFCs($dc_id->le_wh_id);

                                    if($dc_id->is_apob == 1){
                                        // IF DC IS VITUAL /APOB
                                        if($all_fcs == 'yes'){

                                            $allapobfcArray = array();
                                            foreach ($alldcfcdata as $key => $fcvalue) {
                                                $allapobfcdata = $this->product_slab_details->getAllDCFCs($fcvalue->le_wh_id);
                                                foreach ($allapobfcdata as $inkey => $invalue) {
                                                    array_push($allapobfcArray, (object)array("le_wh_id"=>$invalue->le_wh_id,"legal_entity_id"=>$invalue->legal_entity_id));
                                                }                                                    
                                            }
                                        }

                                        if($all_dcs == 'yes' && $all_fcs == 'yes'){
                                            $alldcfcdata = array_merge($alldcfcdata,$allapobfcArray);
                                        }
                                        
                                        if($all_dcs == 'no' && $all_fcs == 'yes'){
                                            $alldcfcdata =  $allapobfcArray;
                                        }

                                        if($all_dcs == 'no' && $all_fcs == 'no'){
                                            $alldcfcdata = [];
                                            $alldcfcdata[] =  (object)array("le_wh_id"=>$dc_id->le_wh_id,"legal_entity_id"=>$dc_id->legal_entity_id);
                                        }

                                        if($is_apob == "yes" && ($all_dcs == 'yes' || $all_fcs == 'yes')){
                                            array_push($alldcfcdata, (object)array("le_wh_id"=>$dc_id->le_wh_id,"legal_entity_id"=>$dc_id->legal_entity_id));
                                        }

                                        if($is_apob == "no" && $all_dcs == 'no' && $all_fcs == 'no'){
                                            $alldcfcdata = [];
                                        }
                                    }else{

                                        if(isset($alldcfcdata[0]) && is_object($alldcfcdata[0])){
                                            // dc name is given and it has mapping fc
                                            if($all_fcs == "no"){
                                                $alldcfcdata = [];
                                            }
                                            if($all_dcs == "yes"){
                                                $dc_data = $this->product_slab_details->getdcData($dc_id->le_wh_id);
                                                array_push($alldcfcdata, (object)array("le_wh_id"=>$dc_id->le_wh_id,"legal_entity_id"=>$dc_data->legal_entity_id));
                                            }

                                        }else{
                                            // dc name is given and it has no mapping fc
                                            $dc_data = $this->product_slab_details->getdcData($dc_id->le_wh_id);
                                            $alldcfcdata = [];
                                            if($all_fcs == "yes" || $all_dcs == "yes")
                                                $alldcfcdata[] =  (object)array("le_wh_id"=>$dc_id->le_wh_id,"legal_entity_id"=>$dc_data->legal_entity_id);
                                        }
                                    }
                                }else{
                                    // dc name is empty so adding to all fc and dc
                                    if(!isset($dc_id->le_wh_id) && $data['dc_name'] =="")
                                        $alldcfcdata = $this->product_slab_details->getAllDCByState($state,$all_dcs,$all_fcs,$is_apob);
                                }
                                $mailHTML = "";

                                foreach ($alldcfcdata as $key => $dcfc) {
                                    # code...

                                    // $checkProduct = $this->product_slab_details->checkProductMapByDc($product_id,$dcfc->le_wh_id);
                                    // if(count($checkProduct)){
                                        $slab_data = array(
                                            'product_id'        => $product_id,
                                            //'state_id'          => $state,
                                            'state_id'          => $state,
                                            'customer_type'     => $customer_type,
                                            'price'             => $data['esp'],
                                            'ptr'               => $data['ptr'],
                                            'legal_entity_id'   => $dcfc->legal_entity_id,
                                            'effective_date'    => $effective_date,
                                            'created_by'        => Session::get('userId'),
                                            'dc_id'             => $dcfc->le_wh_id,
                                            'fc_sku_margin'     => $fc_sku_margin,
                                            'dc_sku_margin'     => $dc_sku_margin,
                                            'fc_margin_type'     => $fc_margin_type,
                                            'dc_margin_type'     => $dc_margin_type
                                        );
                                        $uploadResponse = $this->product_slab_details->insertUploadProducts($slab_data, $is_delete_flag);
                                        $dc_data = $this->product_slab_details->getdcData($dcfc->le_wh_id);
                                        $mailHTML   .= "
                                                <tr>
                                                    <td>".$product_id."</td>
                                                    <td>".$data['sku']."</td>
                                                    <td>".$dc_data->display_name."</td>
                                                    <td>".$data['product_title']."</td>
                                                ";
                                        $le_wh_name = '(' .$dc_data->display_name. ')';
                                        //write for the Text File
                                        $msg .= $uploadResponse['message'] . $le_wh_name . PHP_EOL;
                                        //Write for the mail
                                        $oldPrice   = isset($uploadResponse['old_new_data']['OLDVALUES']) ? $uploadResponse['old_new_data']['OLDVALUES']->price : 0;
                                        $oldPTR     = isset($uploadResponse['old_new_data']['OLDVALUES']) ? $uploadResponse['old_new_data']['OLDVALUES']->ptr : 0;

                                        $newPrice   = isset($uploadResponse['old_new_data']['NEWVALUES']['price']) ? $uploadResponse['old_new_data']['NEWVALUES']['price'] : 0;
                                        $newPTR     = isset($uploadResponse['old_new_data']['NEWVALUES']['ptr']) ? $uploadResponse['old_new_data']['NEWVALUES']['ptr'] : 0;
                                        $oldCustType   = isset($uploadResponse['old_new_data']['OLDVALUES']) ? $uploadResponse['old_new_data']['OLDVALUES']->customer_type : 0;

                                        $mailHTML   .= "
                                            <td>".$oldPrice."</td>
                                            <td bgcolor='#FFFF00'>".$newPrice."</td>
                                            <td>".$oldPTR."</td>
                                            <td bgcolor='#FFFF00'>".$newPTR."</td>
                                            <td>".$uploadResponse['message']."</td>
                                            </tr>
                                        ";

                                        if($uploadResponse['counter_flag']==1){
                                            $deleteCnt++;
                                        }elseif($uploadResponse['counter_flag']==2){
                                            $notDelCnt++;
                                        }elseif($uploadResponse['counter_flag']==3){
                                            $updateCnt++;
                                        }elseif($uploadResponse['counter_flag']==4){
                                            $insertCnt++;
                                        }elseif($uploadResponse['counter_flag']==5){
                                            $errorCnt++;
                                        }

                                        //clearing cache after updating/inserting pricing
                                        if($uploadResponse['counter_flag']==3 || $uploadResponse['counter_flag']==4){
                                            $appKeyData = env('DB_DATABASE');
                                            $keyString = $appKeyData . '_product_slab_' . $product_id . '_customer_type_' . $customer_type.'_le_wh_id_'.$dcfc->le_wh_id;
                                            $cache_flush_array[] = array(
                                                                    "cache_array"=>array("product_id"=>$product_id,"le_wh_id"=>$dcfc->le_wh_id,"customer_type"=>$customer_type),
                                                                    "cache_key"=>$keyString,
                                                                    "cache_type"=>1);
                                            $newPrice = round($newPrice,5);
                                            if($newPrice!=0 && $oldPrice!=0 && (double)$oldPrice != (double)$newPrice && $customer_type == $oldCustType && $effective_date >= date('Y-m-d') && $dc_data->is_apob == 0){
                                                $inv = $this->checkInventory($product_id,$dcfc->le_wh_id);
                                                if($inv>0){
                                                    $price_change_data[] = array(
                                                                    'stock'             =>$inv,
                                                                    'product_id'        => $product_id,
                                                                    'customer_type'     => $customer_type,
                                                                    'old_price'         => $oldPrice,
                                                                    'new_price'         => $newPrice,
                                                                    'price_difference'  => $oldPrice - $newPrice,
                                                                    'effective_date'    => $effective_date,
                                                                    'created_by'        => Session::get('userId'),
                                                                    'le_wh_id'          => $dcfc->le_wh_id
                                                                );
                                                }
                                            }
                                        }
                                    // }
                                }
                                if(count($alldcfcdata) == 0){
                                    $msg .= "#".$excelRowcounter." SKU (".$data['sku'].") ";
                                    $blankTRs = "<td>-</td>
                                            <td>-</td>
                                            <td>-</td>
                                            <td>-</td>";
                                    $msg .= "Invalid Data!";
                                    $validFlag = 1;
                                    $mailHTML   .= $blankTRs . "
                                            <td>Invalid Data!</td>
                                            </tr>
                                    ";
                                    $msg .= PHP_EOL;
                                }
                                
                            }else{
                                $msg .= PHP_EOL;
                                $errorCnt++;
                            }

                        }else{
                            $msg .= "#".$excelRowcounter." SKU (".$data['sku'].") ";
                            $blankTRs = "<td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>";
                            $msg .= "SKU is empty or ESP is Invalid!";
                            $validFlag = 1;
                            $errorCnt++;
                            $mailHTML   .= $blankTRs . "
                                    <td>Invalid Product!</td>
                            ";
                            $msg .= PHP_EOL;
                        }
                        // excel row incrementer
                        $excelRowcounter++;
                        $mailHTML .= "</tr>";
                    }




                    Notifications::addNotification(['note_code' =>'PRS001']);
                    //create the log file as per the excel sheet
                    $file = fopen($file_path, "w");
                    fwrite($file, $msg);
                    fclose($file);
                    $toEmails = array();

                    
                    $notificationObj= new NotificationsModel();
                    $usersObj = new Users();
                    $userIdData= $notificationObj->getUsersByCode('PRIC0001');
                    $userIdData=json_decode(json_encode($userIdData),1);
                    $data= $usersObj->wherein('user_id',$userIdData)->select('email_id')->get();
                    $emails=json_decode(json_encode($data),true);
                    $getEmails=array();
                    foreach ($emails as $keyValue ){
                        $getEmails[]=$keyValue['email_id'];
                    }

                    $topMsg     = "This is to notify you that pricing for the products has been uploaded successfully.";
                    Mail::send('emails.pricingMail', ['topMsg'=>$topMsg, 'changedby' => $name['userName'], 'mailHTML' => $mailHTML, 'editFlag' => 2 ], function ($message) use ($toEmails,$file_path,$txtFileName,$environment,$getEmails) {

                       /*if( $environment=='local' || $environment=='dev' || $environment=='qc' || $environment=='supplier' ){
                            $message->from("tracker@ebutor.com", $name = "Tech Support - " . $environment);
                            $message->to("venkatesh.burla@ebutor.com");
                        }else{
                            $message->from("tracker@ebutor.com", $name = "Tech Support");
                            $message->to("satish.racha@ebutor.com");
                        }*/
                        //$message->bcc("somnath.chowdhury@ebutor.com");
                        //$message->subject('Price uploaded for the Product on :' . date('d-m-Y H:i:s') );
                        
                        $message->to($getEmails)->subject('Price uploaded for the Product on :' . date('d-m-Y H:i:s') );
                        $message->attach($file_path, ['as' => $txtFileName]);
                    });

                    // clearing cache 
                    if(count($cache_flush_array)){
                        $this->queue = new Queue();
                        $cache_flush_array = base64_encode(json_encode($cache_flush_array));
                        $args = array("ConsoleClass" => 'clearcache', 'arguments' => array('cache_array'=>$cache_flush_array));
                        Log::info("clearcache Sent to Queue");
                        $job = $this->queue->enqueue('default', 'ResqueJobRiver', $args);
                        Log::info($job);
                    }
                    
                    if(count($price_change_data)){
                        $this->insertPriceChanges($price_change_data);
                    }
                    DB::commit();
                    // return response
                    return "Data Imported successfully.<br>Added : ".$insertCnt." || Updated :".$updateCnt." || Deleted : ".$deleteCnt." || Error : ".$errorCnt.' <a href="'.$file_path.'" target="_blank"> View Details </a>';
                    }else{
                    DB::rollback();
                    return "Invalid Data";
                    }
                }
            }
        }catch (\ErrorException $ex) {
            DB::rollback();
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return "Sorry Failed to Upload Sheet,Reverting all Records. Please check log for More Details";
        } 
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

    // Create the excel file as per the data selection
    public function downloadExcelWithData(Request $request)
    {
        $mdl_manufac = $request->input("mdl_manufac");
        $mdl_brand = $request->input("mdl_brand");
        $mdl_category = $request->input("mdl_category");
        $mdl_products = $request->input("mdl_products");
        $mdl_state = $request->input("mdl_state");
        $mdl_dc_id = $request->input("upload_dc");
        $mdl_custgroup = $request->input("mdl_custgroup");


        $selectionQueryOuter = array();
        $selectionQueryInner = array();

        if($mdl_manufac!='' && $mdl_manufac!='all'){
            $selectionQueryOuter[] = "prd.manufacturer_id = '".$mdl_manufac."'";
        }
        if($mdl_brand!='' && $mdl_brand!='all'){
            $selectionQueryOuter[] = "prd.brand_id = '".$mdl_brand."'";
        }
        if($mdl_category!='' && $mdl_category!='all'){
            $selectionQueryOuter[] = "prd.category_id = '".$mdl_category."'";
        }
        if($mdl_products!='' && $mdl_products!='all'){
            $selectionQueryOuter[] = "prd.product_id = '".$mdl_products."'";
        }
        if($mdl_dc_id!='' && $mdl_dc_id!='all'){
            $selectionQueryOuter[] = "pp.dc_id = '".$mdl_dc_id."'";
        }
        if($mdl_state!='' && $mdl_state!='all'){
            $selectionQueryOuter    [] = "pp.state_id = '".$mdl_state."'";
        }
        if($mdl_custgroup!='' && $mdl_custgroup!='all'){
            $selectionQueryInner[] = "pp.customer_type = '".$mdl_custgroup."'";
        }

        $mytime = Carbon::now();
        $headers = array('PRODUCT_ID','SKU','PRODUCT_TITLE','MRP','CUSTOMER_GROUP','STATE','DC_NAME','IS_APOB','ALL DCs','ALL FCs','EFFECTIVE_DATE(m/d/y)','ESP','PTR','IS_DELETE','FC SKU MARGIN','FC MARGIN(Percentage/Value)','DC SKU MARGIN','DC MARGIN(Percentage/Value)');
        $headers_second_page = array('STATE','CUSTOMER_GROUP','DC NAME');

        $exceldata = json_decode($this->product_slab_details->getDataAsPerQuery($selectionQueryOuter, $selectionQueryInner), true);

        $stateDet = json_decode($this->product_slab_details->getAllState(), true);
        $customerDet = json_decode($this->product_slab_details->getAllCustomerType(), true);
        $dcDet = json_decode($this->product_slab_details->getAllDCType(), true);


        $loopCounter = 0;
        $exceldata_second = array();
        foreach($stateDet as $val){
            $exceldata_second[$loopCounter]['state'] = $val['ItemName'];
            $loopCounter++;
        }

        $loopCounter = 0;
        foreach($customerDet as $val){
            $exceldata_second[$loopCounter]['customer'] = $val['ItemName'];
            $loopCounter++;
        }

        $loopCounter = 0;
        foreach($dcDet as $val){
            $exceldata_second[$loopCounter]['dc'] = $val['lp_wh_name'];
            $loopCounter++;
        }
        
        $dummyData = array('priceExcelName'=>'Pricing Template Sheet-'.$mytime->toDateTimeString());
        UserActivity::userActivityLog('Pricing',$dummyData, 'Price Excel downloaded by user');

        Excel::create('Pricing Template Sheet-'.$mytime->toDateTimeString(), function($excel) use($headers, $exceldata, $headers_second_page, $exceldata_second) 
        {

            $excel->sheet("Pricing", function($sheet) use($headers, $exceldata)
            {
                $sheet->loadView('Pricing::slabPriceTemplate', array('headers' => $headers, 'data' => $exceldata)); 
            });

            $excel->sheet("State_and_Customer_Data", function($sheet) use($headers_second_page, $exceldata_second)
            {
                $sheet->loadView('Pricing::stateAndCusomerSampleTemplate', array('headers' => $headers_second_page, 'data' => $exceldata_second)); 
            });
        })->export('xlsx');


    }

    //for clearing cache after updating pricing
    public function clearCache($cache_key,$cache_type=0,$cache_array=array()){
        Log::info($cache_key);
        if($cache_key !="" && $cache_key !=null){
            $response = Cache::get($cache_key);
            Log::info($response);
            Cache::forget($cache_key);
            if(count($cache_array)){
                $product_id = $cache_array['product_id'];
                $le_wh_id = $cache_array['le_wh_id'];
                $this->product_slab_details->prodSlabFlatRefreshByProductId($product_id,$le_wh_id);
            }
        }
    }

    public function insertPriceChanges($products){
        DB::table("price_change_details")
            ->insert($products);
        return 1;
    }

    public function checkInventory($product_id,$le_wh_id){

        $checkInventory = DB::table('inventory')
                                ->select(DB::raw('inv_display_mode'))
                                ->where('product_id', '=', $product_id)
                                ->where('le_wh_id', '=', $le_wh_id)
                                ->get()->all();
        $displaymode = isset($checkInventory[0]->inv_display_mode)?$checkInventory[0]->inv_display_mode:0;
        $query = DB::selectFromWriteConnection(DB::raw("select ($displaymode-(order_qty+reserved_qty)) as availQty from `inventory` where `product_id` = $product_id and `le_wh_id` = $le_wh_id"));
        if(count($query)>0){
            $availQty = isset($query[0]->availQty) ? $query[0]->availQty:0;

            return $availQty;
        }else{
            return 0;
        }
        

    }

    /**
     * Import the updated ESP
     * @param  Request $request Get the file as input
     * @return void
     */
    public function upload_esp_price(Request $request){
        try{
            DB::beginTransaction();
            $name = Session::all();
            $environment    = env('APP_ENV');
            $mailHTML       = "";
            $file_data                      = Input::file('price_data');
            $file_extension                 = $file_data->getClientOriginalExtension();
            if( $file_extension != 'xlsx' && $file_extension != 'xls'){
                return 'Invalid file type';
            }else{
                if (Input::hasFile('price_data')) {
                    $path                           = Input::file('price_data')->getRealPath();
                    $data                           = $this->readExcel($path);
                    $file_data                      = Input::file('price_data');
                    $result                         = json_decode(json_encode($data['prod_data']), true);
                    $headers                        = json_decode(json_encode($data['cat_data']), true);
                    $headers1 = array('SKU','Product_ID','Manufacturer','Product_Title','Group_ID','LAST','KVI','SOH','Active','CFC','ESU','MRP','PTR','PTR Percentage','GST%','Base Rate','Scheme%','Base Rate-Sch Amt','Net Rate','Ebutor Margin%','Net % after PTR','Extra%','ELP','ESP','ELP Percentage','State','is_APOB','ALL Dcs','ALL Fcs','ESP (INV)','ELP (ENV)','customer_group','dc_name');
                    $recordDiff = array_diff($headers,$headers1);
                    if(empty($recordDiff) && count($recordDiff)==0){
                        $timestamp = md5(microtime(true));
                        $txtFileName = 'pricing-ESP-import-' . $timestamp . '.txt';

                        $file_path = 'download' . DIRECTORY_SEPARATOR . 'pricing_esp_log' . DIRECTORY_SEPARATOR . $txtFileName;
                        $msg = '';
                        $deleteCnt = $notDelCnt = $updateCnt = $insertCnt = $errorCnt = $notFoundCnt = 0;
                        $excelRowcounter = 2;
                        ini_set('max_execution_time', 0);
                        $cache_flush_array = array();
                        $price_change_data = array();
                        foreach($result as $key => $data){
                            if($data['sku'] && isset($data['esp']) ){
                                $msg .= "#".$excelRowcounter." SKU (".$data['sku'].") ";
                                // get all the ID as per the data in excel
                                $product_id = $this->product_slab_details->getProductID($data['sku']);
                                $state = $this->product_slab_details->getState($data['state']);
                                $customer_type = $this->product_slab_details->getCustomerType($data['customer_group']);
                                $dc_id = $this->product_slab_details->getdcID($data['dc_name']);
                                // get Product TAX info (if TAX is not inserted then price should not accepted)
                                // call fro same state
                                $getTax = $this->objPriginController->getTaxByState($product_id, $state, $state);
                                // $getTax = $this->objPriginController->getTaxByState($product_id, $dc_id->state, $dc_id->state);
                                // get VAT
                                $taxDataVAT = 1;
                                if( !isset($getTax['ResponseBody']) || $getTax['ResponseBody'] =='Tax Mapping for Product Not Found' ){
                                    $taxDataVAT = 0;
                                }

                                // assign dates
                                $effective_date = date('Y-m-d');
                                $effective_date = date("Y-m-d", strtotime($effective_date));

                                // Is delete value
                                $is_delete_flag = 0;

                                $mailHTML   .= "
                                    <tr>
                                        <td>".$product_id."</td>
                                        <td>".$data['sku']."</td>
                                        <td>".$data['dc_name']."</td>
                                        <td>".$data['product_title']."</td>
                                    ";

                                $blankTRs = "<td>-</td>
                                        <td>-</td>
                                        <td>-</td>
                                        <td>-</td>";

                                // Check for valid data
                                $validFlag = 0;
                                if($product_id==0 || $product_id==''){
                                    $msg .= " is not valid!";
                                    $validFlag = 1;
                                    $mailHTML   .= $blankTRs . "
                                        <td>Invalid Product!</td>
                                    ";
                                }
                                if($state==0 || $state==''){
                                    $msg .= " : State is not valid!";
                                    $validFlag = 1;
                                    $mailHTML   .= $blankTRs . "
                                        <td>State is not valid!</td>
                                    ";  
                                }                                
                                if($customer_type==0 || $customer_type==''){
                                    $msg .= " : Customer group is not valid!";
                                    $validFlag = 1;
                                    $mailHTML   .= $blankTRs . "
                                        <td>Customer group is not valid!</td>
                                    ";
                                }
                                if($effective_date=="" || $effective_date=='1970-01-01'|| (strpos($effective_date,'1900') !== false)){
                                    $msg .= " : Effective Date is not valid, please check date format (m/d/yyyy)!";
                                    $validFlag = 1;  
                                    $mailHTML   .= $blankTRs . "
                                        <td>Effective Date is not valid, please check date format (m/d/yyyy)!</td>
                                    ";
                                }
                                $mrp = $data['mrp'];
                                $esp = $data['esp'];
                                if(!is_numeric($esp) || $esp>$mrp || $esp<=0 ){
                                    $msg .= " : ESP is morethan MRP or not valid!";
                                    $validFlag = 1;
                                    $mailHTML   .= $blankTRs . "
                                        <td>ESP is morethan MRP or not valid!</td>
                                    ";  
                                }

                                $ptr = $data['ptr'];
                                if(!is_numeric($ptr) || $ptr>$mrp || $ptr<=0 ){
                                    $msg .= " : PTR is morethan MRP or not valid!";
                                    $validFlag = 1; 
                                    $mailHTML   .= $blankTRs . "
                                        <td>PTR is morethan MRP or not valid!</td>
                                    ";  
                                }
                                if($taxDataVAT==0){
                                    $msg .= " : " . trans('pricing.UI_PRICE_TAX_NOT_FOUND');
                                    $validFlag = 1; 
                                    $mailHTML   .= $blankTRs . "
                                        <td>". trans('pricing.UI_PRICE_TAX_NOT_FOUND')."</td>
                                    ";    
                                }
                                if($validFlag==0){
                                    $all_dcs = isset($data['all_dcs']) ? strtolower($data['all_dcs']) : 'no';
                                    $all_fcs = isset($data['all_fcs']) ? strtolower($data['all_fcs']) : 'no';
                                    $is_apob = isset($data['is_apob']) ? strtolower($data['is_apob']) : 'no';

                                    if($all_dcs != 'no' && $all_dcs != 'yes')
                                        $all_dcs = 'no';
                                    if($all_fcs != 'no' && $all_fcs != 'yes')
                                        $all_fcs = 'no';
                                    if($is_apob != 'no' && $is_apob != 'yes')
                                        $is_apob = 'no';
                                                          
                                    $alldcfcdata = array();
                                    if(isset($dc_id->le_wh_id) && $dc_id->le_wh_id != 0){
                                        $alldcfcdata = $this->product_slab_details->getAllDCFCs($dc_id->le_wh_id);
                                        if($dc_id->is_apob == 1){
                                            // IF DC IS VITUAL /APOB
                                            if($all_fcs == 'yes'){
                                                $allapobfcArray = array();
                                                foreach ($alldcfcdata as $key => $fcvalue) {
                                                    $allapobfcdata = $this->product_slab_details->getAllDCFCs($fcvalue->le_wh_id);
                                                    foreach ($allapobfcdata as $inkey => $invalue) {
                                                        array_push($allapobfcArray, (object)array("le_wh_id"=>$invalue->le_wh_id,"legal_entity_id"=>$invalue->legal_entity_id));
                                                    }                                                    
                                                }
                                            }
                                            if($all_dcs == 'yes' && $all_fcs == 'yes'){
                                                $alldcfcdata = array_merge($alldcfcdata,$allapobfcArray);
                                            }
                                            
                                            if($all_dcs == 'no' && $all_fcs == 'yes'){
                                                $alldcfcdata =  $allapobfcArray;
                                            }

                                            if($all_dcs == 'no' && $all_fcs == 'no'){
                                                $alldcfcdata = [];
                                                $alldcfcdata[] =  (object)array("le_wh_id"=>$dc_id->le_wh_id,"legal_entity_id"=>$dc_id->legal_entity_id);
                                            }

                                            if($is_apob == "yes" && ($all_dcs == 'yes' || $all_fcs == 'yes')){
                                                array_push($alldcfcdata, (object)array("le_wh_id"=>$dc_id->le_wh_id,"legal_entity_id"=>$dc_id->legal_entity_id));
                                            }

                                            if($is_apob == "no" && $all_dcs == 'no' && $all_fcs == 'no'){
                                                $alldcfcdata = [];
                                            }
                                        }else{

                                            if(isset($alldcfcdata[0]) && is_object($alldcfcdata[0])){
                                                // dc name is given and it has mapping fc
                                                if($all_fcs == "no"){
                                                    $alldcfcdata = [];
                                                }
                                                if($all_dcs == "yes"){
                                                    $dc_data = $this->product_slab_details->getdcData($dc_id->le_wh_id);
                                                    array_push($alldcfcdata, (object)array("le_wh_id"=>$dc_id->le_wh_id,"legal_entity_id"=>$dc_data->legal_entity_id));
                                                }

                                            }else{
                                                // dc name is given and it has no mapping fc
                                                $dc_data = $this->product_slab_details->getdcData($dc_id->le_wh_id);
                                                $alldcfcdata = [];
                                                if($all_fcs == "yes" || $all_dcs == "yes")
                                                    $alldcfcdata[] =  (object)array("le_wh_id"=>$dc_id->le_wh_id,"legal_entity_id"=>$dc_data->legal_entity_id);
                                            }
                                        }
                                    }else{
                                        // dc name is empty so adding to all fc and dc
                                        if(!isset($dc_id->le_wh_id) && $data['dc_name'] =="")
                                            $alldcfcdata = $this->product_slab_details->getAllDCByState($state,$all_dcs,$all_fcs,$is_apob);
                                    }
                                    $mailHTML = "";
                                    foreach ($alldcfcdata as $key => $dcfc) {
                                        # code...

                                        // $checkProduct = $this->product_slab_details->checkProductMapByDc($product_id,$dcfc->le_wh_id);
                                        // if(count($checkProduct)){
                                            $slab_data = array(
                                                'product_id'        => $product_id,
                                                //'state_id'          => $state,
                                                'state_id'          => $state,
                                                'customer_type'     => $customer_type,
                                                'price'             => $data['esp'],
                                                'ptr'               => $data['ptr'],
                                                'legal_entity_id'   => $dcfc->legal_entity_id,
                                                'effective_date'    => $effective_date,
                                                'created_by'        => Session::get('userId'),
                                                'dc_id'             => $dcfc->le_wh_id
                                            );
                                            $uploadResponse = $this->product_slab_details->insertUploadProductsESP     ($slab_data, $is_delete_flag);
                                            $dc_data = $this->product_slab_details->getdcData($dcfc->le_wh_id);
                                            $mailHTML   .= "
                                                    <tr>
                                                        <td>".$product_id."</td>
                                                        <td>".$data['sku']."</td>
                                                        <td>".$dc_data->display_name."</td>
                                                        <td>".$data['product_title']."</td>
                                                    ";
                                            $le_wh_name = '(' .$dc_data->display_name. ')';
                                            //write for the Text File
                                            $msg .= $uploadResponse['message'] . $le_wh_name . PHP_EOL;
                                            //Write for the mail
                                            $oldPrice   = isset($uploadResponse['old_new_data']['OLDVALUES']) ? $uploadResponse['old_new_data']['OLDVALUES']->price : 0;
                                            $oldPTR     = isset($uploadResponse['old_new_data']['OLDVALUES']) ? $uploadResponse['old_new_data']['OLDVALUES']->ptr : 0;

                                            $newPrice   = isset($uploadResponse['old_new_data']['NEWVALUES']['price']) ? $uploadResponse['old_new_data']['NEWVALUES']['price'] : 0;
                                            $newPTR     = isset($uploadResponse['old_new_data']['NEWVALUES']['ptr']) ? $uploadResponse['old_new_data']['NEWVALUES']['ptr'] : 0;
                                            $oldCustType   = isset($uploadResponse['old_new_data']['OLDVALUES']) ? $uploadResponse['old_new_data']['OLDVALUES']->customer_type : 0;

                                            $mailHTML   .= "
                                                <td>".$oldPrice."</td>
                                                <td bgcolor='#FFFF00'>".$newPrice."</td>
                                                <td>".$oldPTR."</td>
                                                <td bgcolor='#FFFF00'>".$newPTR."</td>
                                                <td>".$uploadResponse['message']."</td>
                                                </tr>
                                            ";

                                            if($uploadResponse['counter_flag']==1){
                                                $deleteCnt++;
                                            }elseif($uploadResponse['counter_flag']==2){
                                                $notDelCnt++;
                                            }elseif($uploadResponse['counter_flag']==3){
                                                $updateCnt++;
                                            }elseif($uploadResponse['counter_flag']==4){
                                                $insertCnt++;
                                            }elseif($uploadResponse['counter_flag']==5){
                                                $errorCnt++;
                                            }

                                            //clearing cache after updating/inserting pricing
                                            if($uploadResponse['counter_flag']==3 || $uploadResponse['counter_flag']==4){
                                                $appKeyData = env('DB_DATABASE');
                                                $keyString = $appKeyData . '_product_slab_' . $product_id . '_customer_type_' . $customer_type.'_le_wh_id_'.$dcfc->le_wh_id;
                                                $cache_flush_array[] = array(
                                                                        "cache_array"=>array("product_id"=>$product_id,"le_wh_id"=>$dcfc->le_wh_id,"customer_type"=>$customer_type),
                                                                        "cache_key"=>$keyString,
                                                                        "cache_type"=>1);
                                                $newPrice = round($newPrice,5);
                                                if($newPrice!=0 && $oldPrice!=0 && (double)$oldPrice != (double)$newPrice && $customer_type == $oldCustType && $effective_date >= date('Y-m-d') && $dc_data->is_apob == 0){
                                                    $inv = $this->checkInventory($product_id,$dcfc->le_wh_id);
                                                    if($inv>0){
                                                        $price_change_data[] = array(
                                                                        'stock'             =>$inv,
                                                                        'product_id'        => $product_id,
                                                                        'customer_type'     => $customer_type,
                                                                        'old_price'         => $oldPrice,
                                                                        'new_price'         => $newPrice,
                                                                        'price_difference'  => $oldPrice - $newPrice,
                                                                        'effective_date'    => $effective_date,
                                                                        'created_by'        => Session::get('userId'),
                                                                        'le_wh_id'          => $dcfc->le_wh_id
                                                                    );
                                                    }
                                                }
                                            }
                                        // }
                                    }
                                    if(count($alldcfcdata) == 0){
                                        $msg .= "#".$excelRowcounter." SKU (".$data['sku'].") ";
                                        $blankTRs = "<td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>";
                                        $msg .= "Invalid Data!";
                                        $validFlag = 1;
                                        $mailHTML   .= $blankTRs . "
                                                <td>Invalid Data!</td>
                                                </tr>
                                        ";
                                        $msg .= PHP_EOL;
                                    }
                                    
                                }else{
                                    $msg .= PHP_EOL;
                                    $errorCnt++;
                                }

                            }else{
                                $msg .= "#".$excelRowcounter." SKU (".$data['sku'].") ";
                                $blankTRs = "<td>-</td>
                                        <td>-</td>
                                        <td>-</td>
                                        <td>-</td>";
                                $msg .= "SKU is empty or ESP is Invalid!";
                                $validFlag = 1;
                                $errorCnt++;
                                $mailHTML   .= $blankTRs . "
                                        <td>Invalid Product!</td>
                                ";
                                $msg .= PHP_EOL;
                            }
                            // excel row incrementer
                            $excelRowcounter++;
                            $mailHTML .= "</tr>";
                        }




                        Notifications::addNotification(['note_code' =>'PRS001']);
                        //create the log file as per the excel sheet
                        $file = fopen($file_path, "w");
                        fwrite($file, $msg);
                        fclose($file);
                        $toEmails = array();

                        
                        $notificationObj= new NotificationsModel();
                        $usersObj = new Users();
                        $userIdData= $notificationObj->getUsersByCode('PRIC0001');
                        $userIdData=json_decode(json_encode($userIdData),1);
                        $data= $usersObj->wherein('user_id',$userIdData)->select('email_id')->get();
                        $emails=json_decode(json_encode($data),true);
                        $getEmails=array();
                        foreach ($emails as $keyValue ){
                            $getEmails[]=$keyValue['email_id'];
                        }

                        $topMsg     = "This is to notify you that pricing for the products has been uploaded successfully.";
                        $subject='Price uploaded for the Product on :' . date('d-m-Y H:i:s');
                        $body = array('template'=>'emails.pricingMail', 'attachment'=>public_path().DIRECTORY_SEPARATOR.$file_path,'topMsg'=>$topMsg, 'changedby' => $name['userName'], 'mailHTML' => $mailHTML, 'editFlag' => 2);
                        Utility::sendEmail($getEmails, $subject, $body);

                        // clearing cache 
                        if(count($cache_flush_array)){
                            $this->queue = new Queue();
                            $cache_flush_array = base64_encode(json_encode($cache_flush_array));
                            $args = array("ConsoleClass" => 'clearcache', 'arguments' => array('cache_array'=>$cache_flush_array));
                            Log::info("clearcache Sent to Queue");
                            $job = $this->queue->enqueue('default', 'ResqueJobRiver', $args);
                            Log::info($job);
                        }
                        
                        if(count($price_change_data)){
                            $this->insertPriceChanges($price_change_data);
                        }
                        DB::commit();
                        // return response
                        return "Data Imported successfully.<br>Added : ".$insertCnt." || Updated :".$updateCnt." || Deleted : ".$deleteCnt." || Error : ".$errorCnt.' <a href="../'.$file_path.'" target="_blank"> View Details </a>';
                    }else{
                        DB::rollback();
                        return "Invalid Data";
                    }
                }
            }
        }catch (\ErrorException $ex) {
            DB::rollback();
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return "Sorry Failed to Upload Sheet,Reverting all Records. Please check log for More Details".$ex->getTraceAsString();
        } 
    }


}