<?php

/*
 * Filename: DimiciController.php
 * Description: This file is used for manage product inventory
 * Author: Ebutor <info@ebutor.com>
 * Copyright: ebutor@2016
 * Version: v1.0
 * Created date: 24 January 2017
 * Modified date: 24 January 2017
 */

namespace App\Modules\DiMiCiReport\Controllers;
date_default_timezone_set('Asia/Kolkata');

use App\Http\Controllers\BaseController;
use View;
use Log;
use Session;
use Redirect;
use Excel;
use Carbon\Carbon;
use Mail;
use File;
use DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Config;
use App\Lib\Queue;
use Illuminate\Http\Request;
use App\Modules\DiMiCiReport\Models\DimiciGrid;
use App\Modules\DiMiCiReport\Models\DimiciReport;
use App\Modules\DiMiCiReport\Models\DimiciMongo;
use App\Central\Repositories\RoleRepo;
use App\Central\Repositories\ProductRepo;
use App\Modules\Indent\Models\IndentModel;
use App\Modules\Users\Models\Users;
use App\Modules\Notifications\Models\NotificationsModel;
use App\Modules\Orders\Models\OrderModel;
use App\Modules\Roles\Models\Role;
use Utility;
class DimiciController extends BaseController {

    public $queue;
    protected $_orderModel;
    protected $_roleModel;
    public function __construct() {
        try {
            parent::__construct();
            parent::Title('Ebutor - Di Mi Ci Report');
            $this->middleware(function ($request, $next) {
                if (!Session::has('userId')) {
                    Redirect::to('/login')->send();
                }
               
                $this->_roleRepo = new RoleRepo();
                //$this->_indent = new IndentModel();
                $access = $this->_roleRepo->checkPermissionByFeatureCode('DIMICI001');
                
                if(!$access) {
                    Redirect::to('/')->send();
                    die();
                }
                return $next($request);
            });
            $this->_modelGrid = new DimiciGrid();
            $this->_modelReport = new DimiciReport();
            $this->_modelMongo = new DimiciMongo();
            $this->queue = new Queue();
            $this->_orderModel = new OrderModel();
            $this->_roleModel = new Role();
        } catch (\ErrorException $ex) {
        Log::error($ex->getMessage());
        Log::error($ex->getTraceAsString());
        }
    }


    
    public function indexAction() {
       try {
           $breadCrumbs = array('Home' => url('/'), 'DiMiCi Report' => url('/dimici/index'), 'Dashboard' => url('#'));
           parent::Breadcrumbs($breadCrumbs);
           // $warehouses = $this->_modelGrid->getAllWareHouses();
           $Json = json_decode($this->_roleModel->getFilterData(6), 1);
           $filters = json_decode($Json['sbu'], 1);
           $dc_acess_list = isset($filters['118001']) ? $filters['118001'] : 'NULL';
           $warehouses = $this->_orderModel->getDcHubDataByAcess($dc_acess_list);
           return view('DiMiCiReport::index')->with(['warehouses' => $warehouses]);
       } catch (\ErrorException $ex) {
           Log::error($ex->getMessage());
           Log::error($ex->getTraceAsString());
       }        
    }
    
    public function gridAction(Request $request) {
        try {
            $param = array();
            $param['userName'] = $request->session()->get('userName');
            $param['userId'] = $request->session()->get('userId');

            $inputs = json_decode($request->input('filterDetails'), true);
            $param['start'] = $inputs[0]['startDate'];
            $param['end'] = $inputs[0]['endDate'];
            $param['dc'] = $inputs[0]['dcName'];
            $param['cfc_check'] = $inputs[0]['cfc_check'];

            $encoded = base64_encode(json_encode($param));

            $args = array("ConsoleClass" => 'DiMiCiReport', 'arguments' => array('data'=>$encoded));
            $job = $this->queue->enqueue('default', 'ResqueJobRiver', $args);
            
            return "You will get an email with Report attached !!";

        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function createExcelBkground($userName, $userId, $start, $end, $dc,$cfc_check){
        try {
            $page = 0;
            $page_size = 0;
            $filepath = '';
            $res = $this->_modelReport->generateReport($start, $end, $dc);
            if($cfc_check == 1){
                $newArray = array();
                foreach ($res as $key => $value) {
                    # code...
                    if($res[$key]['CFC To Buy'] >=1 and $res[$key]['CFC To Buy']!=""){
                        array_push($newArray, $res[$key]);
                    }
                }
                $res = $newArray; 
            }
            
            $fullData = json_decode(json_encode($res), true);
            
//            array_unshift($fullData, array("Warehouse Id" => $dc));
            
//            for($i = 0; $i <= count($fullData); $i++){
//                print_r($fullData[$i]);
//                if($i == 3){
//                    break;
//                }
//            }
//            exit;
            $fileName = "DimiCiReport_".str_replace("/", "_", $start)."_to_ ".str_replace("/", "_", $end);
//            Log::info("Creating Excel File ".$fileName);

            $filepath = $this->makeExcelFile($fileName, $fullData, $dc);
  //          Log::info("sending Email User Id".$userId);

            return $this->mailExcelReport($filepath, $userId, $userName);

        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function makeExcelFile($fileName, $results, $dc) {
        try {
            $i = 0;
            echo "Total Rows: ".count($results)."\n"; //exit;
          //  Log::info("Total Rows: ".count($results));
            Excel::create($fileName, function($excel) use($results, $i, $dc) {
                $excel->sheet("DiMiCiReport", function($sheet) use($results, $i, $dc) {
                    $sheet->fromArray($results);
                    $sheet->setWidth(array(
                        'B' => 20,
                        'C' => 40,
                        'D' => 70
                        ));
                    $sheet->prependRow(1, array("Warehouse Id:", $dc));
                    $sheet->freezePane('E3');
                });
            })->store('xlsx', public_path('download'));
            return public_path('download') . DIRECTORY_SEPARATOR . $fileName . ".xlsx";
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function mailExcelReport($filePath, $userId, $userName){
        try{
            $email = $this->_modelGrid->getUserEmail($userId);
            $time = Carbon::now();
            $emailBody = "Hello " . ucwords(str_replace(".", " ", explode("@", $email)[0])) . ", <br/><br/>";
            $emailBody .= "Please find attached DiMiCi Report.<br/><br/>";
            $emailBody .= "*Note: This is an auto generated email !!";
            $emails = array($email);
            $body= array('template'=>'emails.dmsMail', 'attachment'=>$filePath, 'name'=>'Hello!', 'emailBody'=>$emailBody);
            $subject='DiMiCi Report '.date('d-m-Y',strtotime($time->toDateTimeString()));
            Utility::sendEmail($emails,$subject,$body);
            // $sendEmail =Mail::send('emails.dmsMail', ['emailBody' => $emailBody], function ($message) use ($email, $filePath, $time) {
            //             $message->to($email);
            //             $message->subject('DiMiCi Report '.date('d-m-Y',strtotime($time->toDateTimeString())));
            //             $message->attach($filePath);
            //         })
            
            //File::delete($filePath);
                // echo "Mail sent to - ".$email." !! Temp file deleted !!\n";
          //      Log::info("Mail sent to - ".$email." !! Temp file deleted !!");
            
            return $email;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            Log::error("Error in sending mail to ".$email." !!");
        }
    }
    
    public function uploadAction(Request $request) {
        try{
            $time = Carbon::now();
            $timeString = strtotime($time->toDateTimeString());
            
            if (Input::hasFile('upload_doc')) {
                $path = Input::file('upload_doc')->getRealPath();
                $data = $this->readExcel($path);
                $excelData = json_decode(json_encode($data), true);

                $indentdata = $this->readExcel4indent($path);
                $indentformatted = json_decode(json_encode($indentdata), true);
                //print_r($indentformatted); exit;
                foreach ($indentformatted as $key => $value) {
                    $dc_id = $key;
                    break;
                }

                $createIndent = $this->createIndent($indentformatted);
                //Auto Email with Indent List...
               /* if(!empty($createIndent['emailData'])){
                    $table = "<table width='100%' style='border: 1px solid #aabcfe;'>";
                    $th = "<tr>";
                    $th .= "<th style='border-color:#aabcfe; color:#075285; background-color:#b9c9fe;'>Manufacturer</th>";
                    $th .= "<th style='border-color:#aabcfe; color:#075285; background-color:#b9c9fe;'>Indent Code</th>";
                    $th .= "</tr>";
                    $table .= $th;

                    $emailBody = "Hello, <br/><br/>";
                    $emailBody .= "Please find the Generated Indents List below:<br/><br/>";
                    $url = env('APP_ENV');
                    if(strtolower($url) == "production")
                    {
                        $emailId = "pmo@ebutor.com";    
                    } else {
                        $emailId = "rohit@yopmail.com";    
                    } 

                    foreach($createIndent['emailData'] as $each){
                        $tr = "<tr>";
                        $tr .= "<td style='border-color:#aabcfe; color:#417497; background-color:#e8edff;'>".$each['ManuF']."</td>";
                        $tr .= "<td style='border-color:#aabcfe; color:#417497; background-color:#e8edff;'>".$each['IndentCode']."</td>";
                        $tr .= "</tr>";
                        $table .= $tr;
                    }

                    $table .= "</table>";
                    $emailBody .= $table."<br /><br />";
                    $emailBody .= "*Note: This is a system generated email !!";

                    $emailSubject = "Generated Indents !! - ".date('d-m-Y', $timeString);

                    $email_result = $this->mailToRelManager($emailBody, $emailId, $emailSubject);                    
                } */
                if(!is_array($excelData)){
                    return $excelData;
                } else {
                    if(!empty($excelData)){
                        $productObj = new ProductRepo();
                        $file_data = Input::file('upload_doc');
                        $url = $productObj->uploadToS3($file_data, 'di_mi_ci', 1);
                        $lastId = $this->_modelMongo->storeDimiciDetails($url, $timeString);
                        $newExcelHeaders = $excelData["headers"];
                        $th = "<tr>";
                        $th .= "<th style='border-color:#aabcfe; color:#075285; background-color:#b9c9fe;'>Indent Code</th>";
                        foreach($excelData["headers"] as $eachHeader){
                            $th .= "<th style='border-color:#aabcfe; color:#075285; background-color:#b9c9fe;'>".$eachHeader."</th>";
                        }
                        $th .= "</tr>";
                        unset($excelData["headers"]);
                        // new code to send excel file
                        foreach($excelData as $key => $value){
                            $table = "<table width='100%' style='border: 1px solid #aabcfe;'>";
                            $table .= $th;

                            $emailBody = "Hello ".ucwords(str_replace(".", " ", explode("@", $key)[0])).", <br/><br/>";
                            $emailBody .= "Please find the DiMiCi Indent Report.<br/><br/>";

                            $emailId = $key;
                            $excelArray = array();
                            $cfc_elp_total = 0;
                            $target_cfc_elp_total = 0;
                            $total_amount = 0;
                            $max_cfc_allowed = 0;
                            foreach($value as $key => $eachValue){
                                $cfc_elp_total += $eachValue[6];
                                $target_cfc_elp_total += $eachValue[7];
                                $total_amount += $eachValue[8]; 
                                $max_cfc_allowed += $eachValue[9];
                                $temparray = array();
                                $tr = "<tr>";
                                $total_td = '';
                                if(isset($createIndent['IndentCodes'][$key])){
                                    $temparray['Indent Code'] =  $createIndent['IndentCodes'][$key];
                                    $tr .= "<td style='border-color:#aabcfe; color:#417497; background-color:#e8edff;'>".$createIndent['IndentCodes'][$key]."</td>";
                                    $total_td .= "<td style='border-color:#aabcfe; color:#417497; background-color:#e8edff;'></td>";
                                    foreach ($eachValue as $inkey => $subEach) {
                                        # code...
                                        $tr .= "<td style='border-color:#aabcfe; color:#417497; background-color:#e8edff;'>".$subEach."</td>";
                                        if($inkey <=5)
                                        $total_td .= "<td style='border-color:#aabcfe; color:#417497; background-color:#e8edff;'></td>"; 

                                        $tempkey = $newExcelHeaders[$inkey];
                                        $temparray[$tempkey] =  $subEach;
                                    }
                                }
                                $tr .= "</tr>";
                                $trAll[] = $tr;
                                array_push($excelArray, $temparray);
                            }
                            foreach($trAll as $eachTr){
                                $table .= $eachTr;
                            }

                            $cfc_elp_total = round($cfc_elp_total,2);
                            $target_cfc_elp_total = round($target_cfc_elp_total,2);
                            $total_amount = round($total_amount,2);
                            $max_cfc_allowed = round($max_cfc_allowed,2);
                            $totla_array = array(
                                "Indent Code"=>"",
                                "SKU"=>"",
                                "Manufacturer Name"=>"",
                                "Product Title"=>"",
                                "MRP"=>"",
                                "CFC Qty"=>"",
                                "CFC To Buy"=>"",
                                "CFC ELP"=>$cfc_elp_total,
                                "Target CFC ELP"=>$target_cfc_elp_total,
                                "Total Amount"=>$total_amount,
                                "Max CFC Allowed"=>$max_cfc_allowed,
                                "PM"=>"");
                            array_push($excelArray, $totla_array);

                            $cfc_elp_total = "<td style='border-color:#aabcfe; color:#417497; background-color:#e8edff;'>" . $cfc_elp_total . "</td>";
                            $target_cfc_elp_total = "<td style='border-color:#aabcfe; color:#417497; background-color:#e8edff;'>" . $target_cfc_elp_total . "</td>";
                            $total_amount = "<td style='border-color:#aabcfe; color:#417497; background-color:#e8edff;'>" . $total_amount . "</td>";
                            $max_cfc_allowed = "<td style='border-color:#aabcfe; color:#417497; background-color:#e8edff;'>" . $max_cfc_allowed . "</td>";
                            $final_tr = "";
                            $final_tr .= "<tr>";
                            $final_tr .= $final_tr . $total_td . $cfc_elp_total . $target_cfc_elp_total . $total_amount . $max_cfc_allowed;
                            $final_tr .= "</tr>";
                            $table .= $final_tr;

                            $table .= "</table>";
                            $emailBody .= $table."<br /><br />";
                            $emailBody .= "*Note: This is an auto generated email !!";

                            $emailSubject = "DiMiCi Indent Report - ".date('d-m-Y', $timeString);

                            $filepath = $this->makeExcelFileNew($emailSubject, $excelArray, $dc_id);
                            $email_result = $this->mailExcelReportByEmail($filepath, $emailId, $emailBody);
                            $updateMongo[] = array($emailId, $email_result);
                            $emailBody = $emailId = $emailSubject = $table = $tr = $trAll = "";
                        }

                        $update_result = $this->_modelMongo->updateDimiciDetails($lastId, $updateMongo);

                        $th = $email_result = $updateMongo = "";

                        if($update_result){
                            return "success-Di Mi Ci report was uploaded successfully !!";
                        } else {
                            return "danger-Something wrong in excel, upload was unsuccessfully !!";
                        }
                    } else {
                        return "danger-SKU, CFC To Buy or PM columns were empty";
                    }
                }
            } else {
                return "danger-Something went wrong, upload was unsuccessfully !!";
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function readExcel($filePath) {
        try{
            $headerRowNumber = 1;
            Config::set('excel.import.startRow', $headerRowNumber);
            Config::set('excel.import.heading', 'numeric');
            $excelData["warehouse_data"] = json_decode(json_encode(Excel::selectSheetsByIndex(0)->load($filePath, function($reader) {})->first()), true);
            
            if($excelData["warehouse_data"][0] == "Warehouse Id:" && $excelData["warehouse_data"][1] !== NULL){
                $headerRowNumber = 2;
                Config::set('excel.import.startRow', $headerRowNumber);
                Config::set('excel.import.heading', 'numeric');
                $excelDataAll = json_decode(json_encode(Excel::selectSheetsByIndex(0)->load($filePath, function($reader) {})->get()->all()),true);
                $excelData["headers_data"] = array_shift($excelDataAll);
                $excelData["product_data"] = $excelDataAll;

                $dataDetails = array();

                if(count($excelData["headers_data"]) == 45 && $excelData["headers_data"][1] == "SKU" && $excelData["headers_data"][33] == "CFC To Buy" && $excelData["headers_data"][37] == "PM" && $excelData["headers_data"][38] == "Supplier Code"){
                    foreach($excelData["product_data"] as $eachData){
                        //print_r($excelData["headers_data"]);
                        if(count($eachData) == 45 && $eachData[1] !== NULL && $eachData[33] !== NULL && $eachData[37] !== NULL && $eachData[38] !== NULL){
                            $dataDetails["headers"] = array($excelData["headers_data"][1], $excelData["headers_data"][2],
                                                      $excelData["headers_data"][3], $excelData["headers_data"][6], $excelData["headers_data"][9],
                                                      $excelData["headers_data"][33], $excelData["headers_data"][34], $excelData["headers_data"][35], $excelData["headers_data"][36],
                                                      $excelData["headers_data"][41], $excelData["headers_data"][37]);
                            $productId = $this->_modelReport->productIdBySku($eachData[1]);
                            $emailId = $this->_modelReport->srmName($productId, "email");

                            // added new code for cheking email id /user is active or not
                            if(!empty($emailId)){
                                $dataDetails[$emailId][$eachData[1]] = array($eachData[1], $eachData[2],
                                                      $eachData[3], $eachData[6], $eachData[9],
                                                      $eachData[33], $eachData[34], $eachData[35], $eachData[36],
                                                      $eachData[41], $eachData[37]);
                            }



                        }
                    }
//                    print_r($dataDetails); exit;
                    return $dataDetails;
                } else {
                    return "danger-SKU, CFC To Buy and PM columns were misplaced in the uploaded excel sheet";
                }
            } else {
                return "danger-Warehouse Id is misplaced / modified in the uploaded excel sheet";
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        
    }
    
    public function mailToRelManager($emailBody, $emailId, $emailSubject) {
        try{
            if (Mail::send('emails.dmsMail', ['emailBody' => $emailBody], function ($message) use ($emailId, $emailSubject) {
                $message->to($emailId);
                $message->subject($emailSubject);
            })) {
                return "success";
            } else {
                return "error";
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    //reading excel for Indents
    public function readExcel4indent($filePath) {
        try{
            $headerRowNumber = 1;
            Config::set('excel.import.startRow', $headerRowNumber);
            Config::set('excel.import.heading', 'numeric');
            $excelData["warehouse_data"] = json_decode(json_encode(Excel::selectSheetsByIndex(0)->load($filePath, function($reader) {})->first()), true);
            
            if($excelData["warehouse_data"][0] == "Warehouse Id:" && $excelData["warehouse_data"][1] !== NULL){
                $headerRowNumber = 2;
                Config::set('excel.import.startRow', $headerRowNumber);
                Config::set('excel.import.heading', 'numeric');
                $excelDataAll = json_decode(json_encode(Excel::selectSheetsByIndex(0)->load($filePath, function($reader) {})->get()->all()),true);
                $excelData["headers_data"] = array_shift($excelDataAll);
                $excelData["product_data"] = $excelDataAll;

                $dataDetails = array();

                if(count($excelData["headers_data"]) == 45 && $excelData["headers_data"][1] == "SKU" && $excelData["headers_data"][33] == "CFC To Buy" && $excelData["headers_data"][37] == "PM" && $excelData["headers_data"][38] == "Supplier Code"){
                    //print_r($excelData["headers_data"]);
                    //print_r($excelData["product_data"]); exit;
                    foreach($excelData["product_data"] as $eachData){
                        if(count($eachData) == 45 && $eachData[1] !== NULL && $eachData[33] !== NULL && $eachData[37] !== NULL && $eachData[38] !== NULL){
                            $productId = $this->_modelReport->productIdBySku($eachData[1]);
                            $manuId = $this->_modelReport->manufaturerByProduct($productId);
                            $sup = $this->_modelReport->getSupplierByCode($productId,$excelData["warehouse_data"][1],$eachData[38]);
                            $suppId = (isset($sup['legal_entity_id']))?$sup['legal_entity_id']:'';
                            //$emailId = $this->_modelReport->srmName($productId, "email");
                            /*if(!isset($dataDetails[$excelData["warehouse_data"][1]][$manuId]["suppliers"]))
                                $dataDetails[$excelData["warehouse_data"][1]][$manuId]["suppliers"] = array();*/
                            if($suppId!=''){
                                $temp = array();
                                $temp['product_Id'] = $productId;
                                $temp['product_name'] = $eachData[3];
                                $temp['sku'] = $eachData[1];
                                $temp['cfc_to_buy_qty'] = ceil($eachData[33]);
                                $temp['no_of_eaches'] = $eachData[9];
                                $temp['mrp'] = $eachData[6];
                                $temp['target_elp'] = $eachData[35];
                                $temp['max_elp'] = $eachData[40];
                                $temp['menufacturer'] = $manuId;
                                $temp['menufacturerName'] = $eachData[2];
                                $dataDetails[$excelData["warehouse_data"][1]][$suppId][] = $temp;
                            }
                            /*$supplier = array();
                            $supplier = $this->_modelReport->getSuppliers($productId, $excelData["warehouse_data"][1]);
                            if(!empty($supplier)){
                                foreach($supplier as $sup_id){
                                    if(!in_array($sup_id, $dataDetails[$excelData["warehouse_data"][1]][$manuId]["suppliers"])){
                                        $dataDetails[$excelData["warehouse_data"][1]][$manuId]["suppliers"][] = $sup_id;
                                    }
                                }
                            }*/
                            //$temp['supplier'] = $supplier;
                            
                            
                        }
                    }
                    return $dataDetails;
                } else {
                    return "danger-SKU, CFC To Buy and PM columns were misplaced in the uploaded excel sheet";
                }
            } else {
                return "danger-Warehouse Id is misplaced / modified in the uploaded excel sheet";
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return $ex->getMessage();
        }
        
    } 

    //Creating Indent Function...
    public function createIndent($indentData){
        try{
            $indentArr = array(); $emailData = array();
            $indentCodes = array();
            foreach($indentData as $wh=>$whData){
                foreach($whData as $supId=>$prodData){
                    $indent_code = $this->getIndentCode();
                    $indentArr=array('indent_date'=>date("Y-m-d"),
                                    'indent_type'=>0,
                                    'indent_code'=>$indent_code,
                                    'le_wh_id'=>$wh,
                                    'legal_entity_id'=>$supId,
                                    );
                    $this->_Indent = new IndentModel();
                    $indent_id = $this->_Indent->saveIndent($indentArr);
                    $indentArr[] = $indent_id;

                    $indentProducts = array();
                    foreach($prodData as $data){
                        if($data['cfc_to_buy_qty']>0){
                            //echo $prodData['Open to buy CFC']." Qty\n";
                            $indentProducts[] = array(
                                'indent_id'=>$indent_id,
                                'product_id'=>$data['product_Id'],
                                'sku'=>$data['sku'],
                                'pname'=>$data['product_name'],
                                'qty'=>ceil($data['cfc_to_buy_qty']),
                                'no_of_eaches' => $data['no_of_eaches'],
                                'mrp'=>$data['mrp'],
                                'max_elp'=>$data['max_elp'],
                                'target_elp'=>$data['target_elp'],
                                'price'=>NULL,
                                'cost'=>NULL,
                                'upc'=>NULL
                                );
                            $indentCodes[$data['sku']]=$indent_code;
                        }
                    }
                    if(!empty($indentProducts)){
                        $indentProdIds = $this->_Indent->saveIndentProducts($indentProducts);
                        $name = '';//$this->_Indent->manufaturerName($manuId);
                        $emailData[] = array("ManuF"=>$name, "IndentCode"=>$indent_code);
                        //echo $indent_id." Created !!\n";
                    } else{
                        $delIndent = DB::table('indent')->where('indent_id', '=', $indent_id)->delete();
                    }
                }                    
            }
            $indentArr["IndentCodes"] = $indentCodes;
            return  $indentArr;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return $ex->getMessage();
        }
    }

    private function getIndentCode() {
        // $refNoArr = DB::select(DB::raw("CALL prc_reference_no('TS', 'ID')"));  //To support master slave concept
        $refNoArr = Utility::getReferenceCode('ID','TS');

        return  $refNoArr;
    }

    public function mailExcelReportByEmail($filePath, $emailId, $emailBody){
        try{
            $email = $emailId;
            $time = Carbon::now();
            $emailBody = $emailBody;
            // template for only purchaing email
            $notificationObj= new NotificationsModel();
            $usersObj = new Users();
            $userIdData= $notificationObj->getUsersByCode('DIMICIR001');
            $userIdData=json_decode(json_encode($userIdData));
            //$email = $emailId;
            $data= $usersObj->wherein('user_id',$userIdData)->select('email_id')->get()->all();
            $emails=json_decode(json_encode($data,1),true);
            $PurchaseEmails=array();
            foreach ($emails as $keyValue ){
                $PurchaseEmails[]=$keyValue['email_id'];
            }
            // Log::info("Purchase Manager Email: ". $PurchaseEmails);

            if (Mail::send('emails.dmsMail', ['emailBody' => $emailBody], function ($message) use ($email, $filePath, $time,$PurchaseEmails) {
                        $message->to($email);
                        $message->cc($PurchaseEmails);                        
                        $message->subject('DiMiCi Report '.date('d-m-Y',strtotime($time->toDateTimeString())));
                        $message->attach($filePath);
                    })) {
                File::delete($filePath);
                Log::info("Mail sent to - ".$email." !! Temp file deleted !!");
            } else {
                Log::info("Error in sending mail to ".$email." !!");
            }
            return $email;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function makeExcelFileNew($fileName, $results, $dc) {
        try {
            $i = 0;
            Log::info("Total Rows: ".count($results));
            Excel::create($fileName, function($excel) use($results, $i, $dc) {
                $excel->sheet("DiMiCiReport", function($sheet) use($results, $i, $dc) {
                    $sheet->fromArray($results);
                    $sheet->setWidth(array(
                        'B' => 20,
                        'C' => 40,
                        'D' => 70
                        ));
                    $sheet->prependRow(1, array("Warehouse Id:", $dc));
                    $sheet->freezePane('E3');
                });
            })->store('xls', public_path('download'));
            return public_path('download') . DIRECTORY_SEPARATOR . $fileName . ".xls";
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
}