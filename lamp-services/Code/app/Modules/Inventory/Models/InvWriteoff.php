<?php
namespace App\Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Roles\Models\Role;
use Session;
use DB;
use App\Central\Repositories\RoleRepo;
use Notifications;
use UserActivity;
use Utility;
use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;
use Log;
use Mail;
use Carbon\Carbon;
use App\Lib\Queue;
use \App\Modules\Users\Models\Users;
use App\Modules\Notifications\Models\NotificationsModel;

class InvWriteoff extends Model {
	 public function updating_inv_writeoff_Values($productsData, $warewhouseid, $URL="")
    {    	
    	try{
//    		Log::info("startin method");
    		$approval_flow_func     = new CommonApprovalFlowFunctionModel();
        	$rolesObj               = new Role();
    		$writeoff_update_array = array();
	        $writeoff_neg_array = array();
	        $resulted_array = array();
	        $timestamp              = date('Y-m-d H:i:s');
        	$current_timeStamp      = strtotime($timestamp);
	        $i=3;
	        $failed_cnt =0;

	        $res_approval_flow_func = $approval_flow_func->getApprovalFlowDetails('Inventory Write-Off', 'drafted', \Session::get('userId'));

//	        Log::info(print_r($res_approval_flow_func,true));
  //      	Log::info("call approval flow ");
	        $curr_status_ID         = $res_approval_flow_func['currentStatusId'];
	        $nextlevelStatusId      = $res_approval_flow_func['data'][0]['nextStatusId'];
	        
	        $insert_array = array("filepath"                => $URL,
	                               "approval_status"         => $nextlevelStatusId,
	                               "created_by"              => \Session::get('userId')
	                            );

	        $writeoff_id = DB::table("inventory_writeoff_upload")->insertGetId($insert_array);
	       // Log::info("write off id = ".$writeoff_id);
	        if(empty($writeoff_id))
	        {
	        	return "inventory writeoff not inserted.";
	        }	
            $uploaded_size = sizeof($productsData);
	       foreach ($productsData as $value) {
	       		if(!empty($value['product_id']))
	       		{
	       			$result_dnd_qty = $value['dnd_qty'];
			        $result_dit_qty = $value['dit_qty'];
			        $prodId = $value['product_id'];
			        $getcurrent_dnd_dit_qty  = $this->getInventoryDetailsBasedOnProductId($prodId, $warewhouseid);
			        $writeoffApprovalStatus = $this->getOpenProductsInTracking_WorkFlow($prodId,$warewhouseid);
			//        log::info("product approval status");
			  //      log::info(print_r($writeoffApprovalStatus,true));
			        if($writeoffApprovalStatus != 0)
			        {
                       // log::info("im in writeoff approval pending condition si zero..".$writeoffApprovalStatus);
			        	$resulted_array["error"][] = 'Line #' . ($i)."!! Product Id ".$prodId.", Approval request for same product is pending. Please close pending requests first to continue. <br>";
			        	$failed_cnt++;
			        	$i++;
			        	continue;
			        }
		        	if(($result_dit_qty > 0 && $result_dnd_qty > 0) || ($result_dnd_qty > 0 && $result_dit_qty == 0) || ($result_dit_qty > 0 && $result_dnd_qty == 0) )  {	           
			           if(!empty($getcurrent_dnd_dit_qty))
			           {
				           $getcurrent_dnd_qty = $getcurrent_dnd_dit_qty[0]['dnd_qty'];
				           $getcurrent_dit_qty = $getcurrent_dnd_dit_qty[0]['dit_qty'];
				           if(($getcurrent_dnd_qty >= $result_dnd_qty) && ($getcurrent_dit_qty >= $result_dit_qty)){

				           	$writeoff_update_array[]= array("product_id"=>$prodId,"uploaded_dnd_qty"=>$result_dnd_qty,"uploaded_dit_qty"=>$result_dit_qty,"current_dit_qty"=>$getcurrent_dit_qty,"current_dnd_qty"=>$getcurrent_dnd_qty,"wh_id"=>$warewhouseid,"status"=>"true");

				           }else{

				           	$writeoff_neg_array[] = array("product_id"=>$prodId,"uploaded_dnd_qty"=>$result_dnd_qty,"uploaded_dit_qty"=>$result_dit_qty,"current_dit_qty"=>$getcurrent_dit_qty,"current_dnd_qty"=>$getcurrent_dnd_qty,"wh_id"=>$warewhouseid,"status"=>"negative");
				           	$resulted_array["error"][] = "Line #".($i)."!! Product Id ".$prodId.", Sum of dit qty or dnd qty should be less than current dit qty or dnd qty. <br>";
				           	$failed_cnt++;
				           }
			           }else{
			           	log::info("Inv table dont have this record. ".$prodId);
			           }	           
			        }else{
			        	$resulted_array["error"][] = 'Line #' . ($i)."!! Product Id ".$prodId.", Zero or negative values not allowed. <br>";
			        	$failed_cnt++;
			        }
			        $i++;
	       		}else{
	       			continue;
	       		}		        
        	}
        	$resulted_array['updated_count'] = 0;
        	//log::info("write off negative array");
        	//log::info(print_r($writeoff_neg_array,true));
            //log::info("updated array..............");
            //log::info(print_r($writeoff_update_array,true));
            //log::info("uploaded size.......");
            //log::info($uploaded_size);
        	if(empty($writeoff_neg_array) && !empty($writeoff_update_array))
        	{
        		foreach($writeoff_update_array as $writeoffValue) {

        			$this->writeoffInsert($writeoffValue['product_id'],$writeoffValue['wh_id'],$writeoffValue['current_dit_qty'],$writeoffValue['current_dnd_qty'],$writeoffValue['uploaded_dit_qty'],$writeoffValue['uploaded_dnd_qty'],$writeoff_id,$nextlevelStatusId,$curr_status_ID);
        		}
        		$resulted_array['updated_count'] = sizeof($writeoff_update_array);
        		$approval_flow_func->storeWorkFlowHistory('Inventory Write-Off', $writeoff_id, $curr_status_ID, $nextlevelStatusId, 'Event drafted by user', \Session::get('userId')); //creating tickets
        	}else
        	{

        	}
        	$resulted_array['reference'] = $current_timeStamp;
        	$resulted_array['error_count'] = $failed_cnt;
            $resulted_array['total_uploaded_count'] = $uploaded_size;
        	//Log::info(print_r($resulted_array,true));
       		UserActivity::excelUploadFileLogs("INVENTORY_WRITEOFF", $current_timeStamp, $URL, $resulted_array);
        	return $resulted_array;

    	}catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
        }
    }
    public function getInventoryDetailsBasedOnProductId($productId, $leWhId)
    {
        $getvalues      = array("soh", "dit_qty", "dnd_qty", "quarantine_qty", "order_qty");
        $sql            = DB::table("inventory")
                        ->where("product_id", "=", $productId)
                        ->where("le_wh_id", "=", $leWhId)
                        ->get($getvalues)->all();
        return json_decode(json_encode($sql), true);
    }
    public function writeoffInsert($product_id,$le_wh_id,$old_dit,$old_dnd,$new_dit,$new_dnd,$writeoff_id,$nextlevelStatusId,$currentStatusId)
    {
    	$rs = DB::table('inventory_writeoff_tracking')
    		->insert(array("product_id"=>$product_id,"le_wh_id"=>$le_wh_id,"old_dnd_qty"=>$old_dnd,"old_dit_qty"=>$old_dit,"upload_dit_qty"=>$new_dit,"upload_dnd_qty"=>$new_dnd,"writeoff_upload_id"=>$writeoff_id,"approved_by"=>$currentStatusId,"approval_status"=>$nextlevelStatusId,"created_by"=>\Session::get('userId')));
    }
    public function getOpenProductsInTracking_WorkFlow($product_id, $wh_id)
    {
        try {
            DB::enablequerylog();
                $statusArr = array("1", "57089", "57075");
                $sql1 = DB::select(DB::raw(" select count(*) as count1 from inventory_writeoff_tracking where product_id = ".$product_id." and le_wh_id = ".$wh_id." and approval_status not in (57187,57089, 57075, 1)  "));
               /* Log::info("check tracking table with product id. ".$product_id."____wh_id..".$wh_id);
                $queryLog = DB::getquerylog();
                log::info(print_r($queryLog,true));*/
                $data = json_decode(json_encode($sql1), true);
                return isset($data[0]['count1'])?$data[0]['count1']:0;
            
        } catch (\ErrorException $ex) {
                Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
        }
    }
     public function getWriteoffUploadDetails($product_id, $wh_id)
    {
        try {
                $statusArr = array("1", "57089", "57075");
                $sql1 = DB::select(DB::raw(" select count(*) as count1 from inventory_writeoff_tracking where product_id = ".$product_id." and le_wh_id = ".$wh_id." and approval_status not in (57089, 57075, 1)  "));
                $data = json_decode(json_encode($sql1), true);
                return isset($data[0]['count1'])?$data[0]['count1']:0;
            
        } catch (\ErrorException $ex) {
                Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
        }
    }
    public function getBulkWriteoffUploadDetails($bulkId)
    {
		$sql    = DB::table("inventory_writeoff_tracking")
			->select('inventory_writeoff_tracking.product_id','products.product_title','mrp','esu','old_dnd_qty', 'old_dit_qty', 'upload_dnd_qty', 'upload_dit_qty','le_wh_id','approval_status')
			->join('products','products.product_id','=','inventory_writeoff_tracking.product_id')
			->where("inventory_writeoff_tracking.writeoff_upload_id", "=", $bulkId)
			->get()->all();
                    
        $data = json_decode(json_encode($sql), true);
        return $data;
    }
    public function updateTrackingTableWithStatusforWriteoff($tableUpdateID, $tracingID)
    {
        date_default_timezone_set("Asia/Kolkata");
        $userId  = \Session::get('userId');
        $getoldStatus = $this->getBulkWriteoffUploadDetails($tracingID);
        foreach ($getoldStatus as $value) {
            $uniquevalues = array("product_id" => $value['product_id']);
            $oldvaluesArray = array("old_status"=> $value['approval_status']);



            $DBentries = array("new_status"=>$tableUpdateID);    
                
            UserActivity::userActivityLog("Inventory Write-Off", $DBentries, "Changing the workflow status from".$value['approval_status']." to".$tableUpdateID , $oldvaluesArray, $uniquevalues);
        }
        $sql = DB::table("inventory_writeoff_tracking")
                ->where("writeoff_upload_id", "=", $tracingID)
                ->update(['approved_by'=> $userId, "approval_status" => $tableUpdateID]);

        $sql_bulk  = DB::table("inventory_writeoff_upload")
                        ->where("writeoff_upload_id", "=", $tracingID)
                        ->update(["approved_by" => $userId, "approval_status" => $tableUpdateID, "approved_at" => date('Y-m-d H:i:s')]);

                

    }
    public function updateInventoryTableforWriteoff($bulkuploadId)
    {
    	try {
    	$mytime = Carbon::now();
		$writeoff_update_array = array();
        $writeoff_neg_array = array();
        date_default_timezone_set("Asia/Kolkata");
        $uploaded_details   = $this->getBulkWriteoffUploadDetails($bulkuploadId);
        $invnegativearray = array();
       	 	foreach ($uploaded_details as $value) {
	   			$result_dnd_qty = $value['upload_dnd_qty'];
		        $result_dit_qty = $value['upload_dit_qty'];
		        $prodId = $value['product_id'];
		        $warewhouseid = $value['le_wh_id'];
		        $getcurrent_dnd_dit_qty  = $this->getInventoryDetailsBasedOnProductId($prodId, $warewhouseid);
	        	if(($result_dit_qty > 0 && $result_dnd_qty > 0) || ($result_dnd_qty > 0 && $result_dit_qty == 0) || ($result_dit_qty > 0 && $result_dnd_qty == 0) )  {	           
		           if(!empty($getcurrent_dnd_dit_qty))
		           {
			           $getcurrent_dnd_qty = $getcurrent_dnd_dit_qty[0]['dnd_qty'];
			           $getcurrent_dit_qty = $getcurrent_dnd_dit_qty[0]['dit_qty'];
			           if(($getcurrent_dnd_qty >= $result_dnd_qty) && ($getcurrent_dit_qty >= $result_dit_qty)){

			           	$writeoff_update_array[]= array("product_id"=>$prodId,"uploaded_dnd_qty"=>$getcurrent_dnd_qty-$result_dnd_qty,"uploaded_dit_qty"=>$getcurrent_dit_qty-$result_dit_qty,"current_dit_qty"=>$getcurrent_dit_qty,"current_dnd_qty"=>$getcurrent_dnd_qty,"wh_id"=>$warewhouseid,"status"=>"true");

			           }else{

			           	$writeoff_neg_array[] = array("product_id"=>$prodId,"uploaded_dnd_qty"=>$result_dnd_qty,"uploaded_dit_qty"=>$result_dit_qty,"current_dit_qty"=>$getcurrent_dit_qty,"current_dnd_qty"=>$getcurrent_dnd_qty,"wh_id"=>$warewhouseid,"status"=>"negative");
			           	$resulted_array["error"][] = "Line #".($i)."!! Product Id ".$prodId.", Sum of dit qty or dnd qty should be less than current dit qty or dnd qty. <br>";
			           	$failed_cnt++;
			           }
		           }else{
		           	log::info("Inv table dont have this record. ".$prodId);
		           }	           
		        }
	    	}
	    	if(empty($writeoff_neg_array) && !empty($writeoff_update_array))
        	{
        		foreach($writeoff_update_array as $writeoffValue) {
        			$sql_inv_tracking = DB::table("inventory_writeoff_tracking")
        								->where("product_id",$writeoffValue["product_id"])
        								->where("writeoff_upload_id", $bulkuploadId)
        								->update(array("approved_at" => $mytime->toDateTimeString()));

					$update_inventory = DB::table("inventory")
					                    ->where("product_id", "=", $writeoffValue['product_id'])
					                    ->where("le_wh_id",$writeoffValue['wh_id'])
					                    ->update(array("dit_qty"=>$writeoffValue["uploaded_dit_qty"],"dnd_qty"=>$writeoffValue["uploaded_dnd_qty"]));
					//Log::info(print_r($update_inventory,true));
					//Log::info("Updating inventory table");
					//log::info($writeoffValue["uploaded_dit_qty"]);
					//log::info($writeoffValue["uploaded_dnd_qty"]);
					$uniquevalues = array("product_id" => $writeoffValue['product_id']);
					$oldvaluesArray = array("DIT_QTY" => $writeoffValue["current_dit_qty"], "DND_QTY" => $writeoffValue["current_dnd_qty"]);
					$DBentries = array( "DIT_QTY"=>$writeoffValue["uploaded_dit_qty"], "DND_QTY" => $writeoffValue["uploaded_dnd_qty"], "warehouse_id" => $writeoffValue["wh_id"], "Product_id" => $writeoffValue['product_id']);
					UserActivity::userActivityLog("Inventory Write-Off", $DBentries, "DIT_QTY and D&D_QTY values has been updated in Inventory Table", $oldvaluesArray, $uniquevalues);
        		}
        	}else
        	{
        		return $writeoff_neg_array;
        	}
		} catch (\ErrorException $ex) {
                Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
        }
    }
   
}
