<?php
/*
FileName : uploadCashbackFiles
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
use App\Modules\Users\Models\Users;
use App\Modules\Notifications\Models\NotificationsModel;
class UploadCashbackFile extends BaseController{

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
        $this->cashbackUpload = new pricingDashboardModel();
    }


	// Upload promotion Slab Details
	public function uploadCashbackdata(Request $request){

        $name = Session::all();

        $environment    = env('APP_ENV');
        $mailHTML       = "";
        $file_data                      = Input::file('upload_cashbackfile');
        //$file_name                      = $file_data->getClientOriginalName();
        $file_extension                 = $file_data->getClientOriginalExtension();

        if( $file_extension != 'xlsx'){
            return 'Invalid file type';
        }else{

    		if (Input::hasFile('upload_cashbackfile')) {
                
                $path                           = Input::file('upload_cashbackfile')->getRealPath();
                $data                           = $this->readExcel($path);

                $file_data                      = Input::file('upload_cashbackfile');
                $result                         = json_decode($data['prod_data'], true);

                $headers                        = json_decode($data['cat_data'], true);
                $headers1                       = array('PRODUCT_ID','SKU','PRODUCT_TITLE', 'STATE','CUSTOMER_GROUP','WAREHOUSE','START_DATE(m/d/y)','END_DATE(m/d/y)','PRODUCT_STAR','BENIFICIARY','Quantity','CASHBACK','IS_PERCENT','IS_DELETE');
                $recordDiff                         = array_diff($headers,$headers1);     

                if(empty($recordDiff) && count($recordDiff)==0){

                    $timestamp = md5(microtime(true));
                    $txtFileName = 'cashback-import-' . $timestamp . '.txt';

                    $file_path = 'download' . DIRECTORY_SEPARATOR . 'pricing_log' . DIRECTORY_SEPARATOR . $txtFileName;
                    $msg = '';
                    $deleteCnt = $notDelCnt = $updateCnt = $insertCnt = $errorCnt = $notFoundCnt = 0;
                    $excelRowcounter = 1;

                    foreach($result as $key => $data){
                          if($data['sku'] && $data['customer_group']!=''){

                            $msg .= "#".$excelRowcounter." SKU (".$data['sku'].") ";

                            // get all the ID as per the data in excel
                            $product_id = $this->product_slab_details->getProductID($data['sku']);
                            // get product_price_id for cashback table reference
                            $cashback_ref_id = $this->product_slab_details->getcashback_ref_id($product_id);

                            $state = $this->product_slab_details->getState($data['state']);
                            $customer_type = $this->product_slab_details->getCustomerType($data['customer_group']);
                            //get benificiary
                            $benificiary = $this->product_slab_details->getBenificiaryIdForExcel($data['benificiary']);
                            // get products stars
                            $product_star = $this->product_slab_details->getProductstarForExcel($data['product_star']);
                            // get warehouse id
                            $warehouse_id = $this->product_slab_details->getWarehousesforexcel($data['warehouse']);


                            // assign dates
                            $start_date = is_array($data['start_datemdy']) ? $data['start_datemdy']['date'] : $data['start_datemdy'];
                            $end_date = is_array($data['end_datemdy']) ? $data['end_datemdy']['date'] : $data['end_datemdy'];
                            $start_date = date("Y-m-d", strtotime($start_date));
                            $end_date = date("Y-m-d", strtotime($end_date));


                            $mailHTML   .= "
                                <tr>
                                    <td>".$product_id."</td>
                                    <td>".$data['sku']."</td>
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
                            
                            // check product_price_id 
                            
                            if($cashback_ref_id=="" || $cashback_ref_id==0){
                                $msg .= " : Product price is not available and Cashback is not applied for that product ";
                                $validFlag = 1;  
                                $mailHTML   .= $blankTRs . "
                                    <td>Product price is not available and Cashback is not applied for that product</td>
                                ";
                            }

                            

                            if($start_date=='1970-01-01'){
                                $msg .= " : Start Date is required, please check date format (m/d/yyyy)!";
                                $validFlag = 1;  
                                $mailHTML   .= $blankTRs . "
                                    <td>Start Date is required, please check date format (m/d/yyyy)!</td>
                                ";
                            }
                            
                            if($end_date=="" || $end_date=='1970-01-01'){
                                $msg .= " : End Date is required, please check date format (m/d/yyyy)!";
                                $validFlag = 1;  
                                $mailHTML   .= $blankTRs . "
                                    <td>End Date is required, please check date format (m/d/yyyy)!</td>
                                ";
                            }

                            $cashback = $data['cashback'];
                            if(!is_numeric($cashback) || $cashback =='' ){
                                $msg .= " : Cashback amount  is required and it should be numeric!..";
                                $validFlag = 1; 
                                $mailHTML   .= $blankTRs . "
                                    <td>Cashback amount  is required and it should be numeric!.</td>
                                ";  
                            }

                            $benificiary_check = $data['benificiary'];

                            if($benificiary_check =='' || $benificiary ==''){
                                $msg .= " : Benificiary is required!.";
                                $validFlag = 1; 
                                $mailHTML   .= $blankTRs . "
                                    <td>Benificiary is required!.</td>
                                ";  
                            }

                            $quantity = $data['quantity'];

                            if(!is_numeric($quantity) || $quantity ==''  ){
                                $msg .= " : Quantity is required and it should be numeric!.";
                                $validFlag = 1; 
                                $mailHTML   .= $blankTRs . "
                                    <td>Quantity is required and it should be numeric!.</td>
                                ";  
                            }

                            $is_percent = $data['is_percent'];
                            if(!is_numeric($is_percent) || $is_percent =='' || $is_percent >=2 ){
                                $msg .= " : IS_PERCENT is required and it should be either 0 or 1!.";
                                $validFlag = 1; 
                                $mailHTML   .= $blankTRs . "
                                    <td>IS_PERCENT is required and it should be either 0 or 1.</td>
                                ";  
                            }
                            if($validFlag==0){

                                $cashback_data = array(
                                        'cbk_ref_id'                => $cashback_ref_id,
                                        'cbk_source_type'           => 2,
                                        'state_id'                  => $state,
                                        'product_id'                => $product_id,
                                        'customer_type'             => $customer_type,
                                        'wh_id'                     => $warehouse_id,
                                        'benificiary_type'          => $benificiary,
                                        'product_star'              => $product_star,
                                        'start_date'                => $start_date,
                                        'end_date'                  => $end_date,
                                        'range_to'                  => $quantity,
                                        'cbk_type'                  => $data['is_percent'],
                                        'created_by'                => Session::get('userId'),
                                        'cbk_value'                 => $cashback,
                                        'cbk_status'                => 2
                                    );
                               
                                $uploadResponse = $this->product_slab_details->saveCashBackDataIntoTableExcel($cashback_data);

                                if(is_numeric($uploadResponse)){
                                    $reponse = "";
                                    $reponse['message']="Cashback uploaded successfully!";
                                }
                                //write for the Text File
                                $msg .= $reponse['message'] . PHP_EOL;
                                //Write for the mail
                                


                                if($uploadResponse == 1){
                                    $updateCnt++;
                                }elseif($uploadResponse == 2){
                                    $insertCnt++;
                                }

                            }else{
                                $msg .= PHP_EOL;
                                $errorCnt++;
                            }

                        }
                        // excel row incrementer
                        $excelRowcounter++;
                        $mailHTML .= "</tr>";
                    }
                    //create the log file as per the excel sheet
                    $file = fopen($file_path, "w");
                    fwrite($file, $msg);
                    fclose($file);
                    print_r("Data Imported successfully.<br>Added : ".$insertCnt." || Updated :".$updateCnt." || Error : ".$errorCnt.' <a href="'.$file_path.'" target="_blank"> View Details </a>');exit;

                    Notifications::addNotification(['note_code' =>'PRS001']);
                    //create the log file as per the excel sheet
                    $file = fopen($file_path, "w");
                    fwrite($file, $msg);
                    fclose($file);
                    $toEmails = array();


                    $notificationObj= new NotificationsModel();
                    $usersObj = new Users();
                    $userIdData= $notificationObj->getUsersByCode('PRIC0001');
                    $userIdData=json_decode(json_encode($userIdData));
                    $data= $usersObj->wherein('user_id',$userIdData)->select('email_id')->get();
                    $emails=json_decode(json_encode($data,1),true);
                    $getEmails=array();
                    foreach ($emails as $keyValue ){
                        $getEmails[]=$keyValue['email_id'];
                    }




                    $topMsg     = "This is to notify you that cashback for the products has been uploaded successfully.";
                    Mail::send('emails.pricingMail', ['topMsg'=>$topMsg, 'changedby' => $name['userName'], 'mailHTML' => $mailHTML, 'editFlag' => 2 ], function ($message) use ($toEmails,$file_path,$txtFileName,$environment,$getEmails ) {
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

                    // return response

                    return "Data Imported successfully.<br>Added : ".$insertCnt." || Updated :".$updateCnt." || Deleted : ".$deleteCnt." || Error : ".$errorCnt.' <a href="'.$file_path.'" target="_blank"> View Details </a>';
                }else{
                    return "Invalid file type";
                }
            }
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
                        
                    })->get();
            $data['cat_data'] = $cat_data;
            $data['prod_data'] = $prod_data;
            return $data;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
        }
    }


}