<?php

/*
 * Filename: InventoryController.php
 * Description: This file is used for manage product inventory
 * Author: Ebutor <info@ebutor.com>
 * Copyright: ebutor@2016
 * Version: v1.0
 * Created date: 4 July 2016
 * Modified date: 4 July 2016
 */

/*
 * InventoryController is used to manage product inventory
 * @author		Ebutor <info@ebutor.com>
 * @copyright	ebutor@2016
 * @package		Orders
 * @version: 	v1.0
 */

namespace App\Modules\Inventory\Controllers;

ini_set('max_execution_time', 0);
ini_set('memory_limit', -1);

use App\Http\Controllers\BaseController;
use Session;
use View;
use Illuminate\Http\Request;
use URL;
use Log;
use Auth;
// use Illuminate\Support\Facades\Redirect;
use App\Modules\Inventory\Models\InventorySOH;
use App\Central\Repositories\RoleRepo;
use Excel;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;
use PDF;
use Notifications;
use UserActivity;
use Redirect;
use Illuminate\Support\Facades\Input;
use App\Modules\Roles\Models\Role;
use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;
use App\Central\Repositories\ProductRepo;
use App\Modules\Inventory\Models\ReadLogs;
use Mail;
use App\Modules\Inventory\Controllers\commonIgridController;
use App\Lib\Queue;
use File;

class InventorySOHController extends BaseController {

    public function __construct(){
         $this->middleware(function ($request, $next) {
                if (!Session::has('userId')) {
                         Redirect::to('/login')->send();
                }
                $this->_inventorySOH = new InventorySOH();
            return $next($request);
        });

    }


    // this is for download soh template
    public function downloadSohTemplate(Request $request){
        try{
            $headers = array('OLD PRODUCT NAME','OLD SKU','NEW PRODUCT NAME','NEW SKU','FROM DC ID','TO DC ID');
            //$headers = array('PRODUCT NAME','SKU','FROM DC ID','TO DC ID');
            $mytime = Carbon::now();
            Excel::create('SOH Transfer Template Sheet-'.$mytime->toDateTimeString(), function($excel) use($headers) 
            {
                $excel->sheet("SOH Transfer ", function($sheet) use($headers)
                {
                    $sheet->loadView('Inventory::downloadSOHTemplate', array('headers' => $headers)); 
                });
            })->export('xlsx');

        }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            Redirect::to('/')->send();
        }

    }

    // read excel for soh transfer
    public function readExcelForStocktransfer($path) {
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
            Redirect::to('/')->send();
        }
    }

    // import old sku to new sku transfer code
    public function stackTransferUploadExcel(Request $request){
        try{

            $file_data      =  Input::file('stocktransfer_data');
            $file_extension  = $file_data->getClientOriginalExtension();
            $msg = "";
            
            if( $file_extension != 'xlsx'){
                $msg .= "Please upload valid file";
                return $msg;die;
            }elseif(Input::hasFile('stocktransfer_data')){
                    $path                           = Input::file('stocktransfer_data')->getRealPath();
                    $data                           = $this->readExcelForStocktransfer($path);
                    $file_data                      = Input::file('stocktransfer_data');
                    $result                         = json_decode($data['prod_data'], true);
                    $headers                        = json_decode($data['cat_data'], true);
                    $headers1                       = array('OLD PRODUCT NAME','OLD SKU','NEW PRODUCT NAME','NEW SKU','FROM DC ID','TO DC ID');
                    //$headers = array('PRODUCT NAME','SKU','FROM DC ID','TO DC ID');
                    $recordDiff                     = array_diff($headers,$headers1);

                    if ($headers != $headers1) {
                        $msg .=  "Please upload valid file";
                        return $msg;
                        die;
                    }

                    if(empty($result)){
                        $msg .=  "Please upload valid data";
                        return $msg;
                        die;
                    }

                    $errorCnt = 0;
                    //save into stock_transfer_grid table
                    $approval_flow = new CommonApprovalFlowFunctionModel();
                    $productObj = new ProductRepo();
                    $url =$productObj->uploadToS3($file_data,'inventory',1);
                    $approvalStatusDetails = $approval_flow->getApprovalFlowDetails('Stock Transfer',57176, Session::get('userId'));

                    if($approvalStatusDetails['status'] == 0){
                        $msg .=  "You Don't Have Permission To Upload The Sheet";
                        return $msg;
                    }

                    $approvalData = isset($approvalStatusDetails['data'])?$approvalStatusDetails['data']:"";
                    foreach($approvalData as $data){
                       
                        $NextStatusId  = isset($data['nextStatusId'])?$data['nextStatusId']:"";
                    }
                    $currentStatusId  = isset($approvalStatusDetails['currentStatusId'])?$approvalStatusDetails['currentStatusId']:"";
                    $savegriddata = array(
                                    'filepath'              =>$url,
                                    'approved_by'           =>Session::get('userId'),                            
                                    'created_by'            => Session::get('userId')
                                );
                    $st_ID = $this->_inventorySOH->saveIntoGridTable($savegriddata);
                    $saveintotransferdetails = array();
                    foreach($result as $data){
                        // Checking for the old sku with cp enabled and is sellable
                            $skudata = $this->_inventorySOH->checkTheSkuWithIssellableAndCpenabled($data['old_sku']);
                            $skudata=json_decode(json_encode($skudata),true);
                            $inventoryoldsku = $this->_inventorySOH->checkTheOldSkuExistOrNot($data['old_sku'],$data['to_dc_id']);

                            $inventoryoldsku=json_decode(json_encode($inventoryoldsku),true);
                            $inventorynewsku = $this->_inventorySOH->checkTheOldSkuExistOrNot($data['new_sku'],$data['to_dc_id']);
                            $inventorynewsku=json_decode(json_encode($inventorynewsku),true);

                            $skuExistornot = $this->_inventorySOH->checkTheOldSkuInTable($data['old_sku']);
                            $skuExistornot=json_decode(json_encode($skuExistornot),true);

                            $msg .= " Sku : " . $data['old_sku'] . " : ";
                            $stockNotInsertedFlag = 0;

                            if( $skudata['is_sellable'] == 1 ){
                                $msg .= "Is Sellable is Enable for ".$data['old_sku']."||";
                                $stockNotInsertedFlag=1;
                            }
                            if( $skudata['cp_enabled'] == 1 ){
                                $msg .= "CP Enabled is Enable for ".$data['old_sku']."||";
                                $stockNotInsertedFlag=1;
                            }
                            if( $inventoryoldsku['sku'] != $data['old_sku'] ){
                                $msg .= " No Record is found in inventory table For Old Sku ".$data['old_sku']."||";
                                $stockNotInsertedFlag=1;
                            }
                            if( $inventorynewsku['sku'] != $data['new_sku'] ){
                                $msg .= " No Record is found in inventory table for new sku ".$data['new_sku']."||";
                                $stockNotInsertedFlag=1;
                            }
                            if( count($skuExistornot)>0 ){
                                $msg .= " Approval Already Exist With ".$data['sku']."||";
                                $stockNotInsertedFlag=1;
                            }
                            $check_espandelp_indcandapob=$this->_inventorySOH->checkEspElpIn_APOB_DC($data['new_sku'],$data['from_dc_id'],$data['to_dc_id']);
                            if(!$check_espandelp_indcandapob){
                                $msg .= $data['new_sku']." Product Doesn't exists in either DC or APOB ||";
                                $stockNotInsertedFlag=1;
                            }
                            if($stockNotInsertedFlag==1 ){
                                $msg .= " Stocktransfer not successful"  . PHP_EOL ;
                                $errorCnt++;
                            }if($stockNotInsertedFlag == 0){
                               
                                $saveintotransferdetails[] = array(
                                            'old_sku'          =>   $data['old_sku'],                           
                                            'new_sku'          =>   $data['new_sku'],
                                            'old_sku_id'        =>  $inventoryoldsku['product_id'],
                                            'new_sku_id'        =>  $inventorynewsku['product_id'],
                                            'st_id'            =>   $st_ID,
                                            'approval_status'   =>  $NextStatusId,
                                            'audited_by'       =>   Session::get('userId'),
                                            'old_product_name'  => $data['old_product_name'],
                                            'new_product_name'  => $data['new_product_name'],
                                            'approved_by'       =>Session::get('userId'),
                                            'created_by'        =>Session::get('userId')
                                    );
                                //$stockdetails = $this->_inventoryDc->saveIntodetailsTable($saveintotransferdetails);  
                                $msg .= " StockTransfer Added Succesfully." . PHP_EOL;
                            }else{
                                $msg .= PHP_EOL;
                                $errorCnt++;
                            }
                    }

                    if(count($saveintotransferdetails)>0){
                        $stockdetails = $this->_inventorySOH->saveIntodetailsTable($saveintotransferdetails); 
                         
                         //$approval_flow->notifyUserForFirstApproval("Stock Transfer",$st_ID,\Session::get('userId'));
                        //workflow here
                        $approvalDataResp =  $approval_flow->storeWorkFlowHistory("Stock Transfer", $st_ID, $currentStatusId, $NextStatusId, "soh uploaded", Session::get('userId'));     
                    }
                     
                        $timestamp = md5(microtime(true));
                        $txtFileName = 'inventorySoh-Impost-Status-' . $timestamp . '.txt';
                        $file_path = 'download' . DIRECTORY_SEPARATOR . 'inventorysoh_log' . DIRECTORY_SEPARATOR . $txtFileName;
                        $file = fopen($file_path, "w");
                        fwrite($file, $msg);
                        fclose($file);
                       // $ImportUrl      = $productObj->uploadToS3($file_path,'inventory',2);              
                        return "Inventory Sheet Uploaded Succesfully.Click on view details for further information ".'<a href='.$file_path.' target="_blank"> View Details </a>'; 

                }else{
                    return 'File with no data!'.$msg; 
                }
        }catch(\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            Redirect::to('/')->send(); 
        }
    }

    // get approval page for stock
    public function getApprovalsForStock($stockid){

        try {
            $approval_flow_func= new CommonApprovalFlowFunctionModel();
            $stockDetails = $this->_inventorySOH->getAllTheStockDetailsForView($stockid);
            $approvalStatus = $stockDetails[0]->approval_status;
            
            $approvalStatusDetails = $approval_flow_func->getApprovalFlowDetails('Stock Transfer', $approvalStatus, \Session::get('userId'));

            $approvalData = isset($approvalStatusDetails['data'])?$approvalStatusDetails['data']:"";
            $currentStatusId  = isset($approvalStatusDetails['currentStatusId'])?$approvalStatusDetails['currentStatusId']:"";
            return view('Inventory::viewapprovalticketforsoh')
                            ->with([ 
                                    'appDropdown'=>$approvalData, 
                                    'stockDetails' => $stockDetails,
                                    'stockID'       =>$stockid,
                                    'currentStatusId' =>$currentStatusId
                                   ]);

        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
        }
    }



    public function ApprovalForNextStatusUsers(Request $request){

         try{
            $sohdata = $request->input();
            $next = $sohdata['nextStatusId'];
            $explodevalue = explode(',', $next);
            $NextStatusID = $explodevalue[0];
            $isFinalStep = $explodevalue[1];

            if ($isFinalStep != 1) {
                $isFinalStep = $NextStatusID;
            }


            if($isFinalStep == 1){
                //get the old sku all values from inventory table

                $data= $this->_inventorySOH->getTheoldSkuValue($sohdata['stock_id']);
                $data=json_decode(json_encode($data),true);
                
                foreach($data as $skuvalue){
                    // get the data from inventory table
                    $oldskuvalues= $this->_inventorySOH->getThevaluesFromInventory($skuvalue['old_sku_id']);
                    $oldskuvalues=json_decode(json_encode($oldskuvalues),true);

                    // create a array for update into new sku
                    $update = $this->_inventorySOH->updateToNewSkuInventory($skuvalue['new_sku_id'],$skuvalue['old_sku_id'],$oldskuvalues);
                }

            }

            $update = $this->_inventorySOH->updateStatusInTableInventory($sohdata['stock_id'], $isFinalStep);
            $flowType = "Stock Transfer";
            $approval_flow_func= new CommonApprovalFlowFunctionModel();
            //flowtype,tableid,currentstatusfrom flow,nextstatusid dropdown,comment,userid
            $approvalDataResp = $approval_flow_func->storeWorkFlowHistory($flowType, $sohdata['stock_id'], $sohdata['currentStatus'], $NextStatusID, $sohdata['comments'], Session::get('userId'));
            
            return  "Submitted Successfully";
        }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }

    }



}
