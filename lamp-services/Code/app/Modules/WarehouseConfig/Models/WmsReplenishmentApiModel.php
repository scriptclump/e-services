<?php

namespace App\Modules\WarehouseConfig\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Inventory\Models\Inventory;
use DB;
use Session;
use Log;
Class WmsReplenishmentApiModel extends Model
{
    public function reservedReplanishment($binType, $wh_id)
    {
    	try{
    		DB::enablequerylog();
    		$status = "Regular";
	    	$replenishmentArray= array();
	    	$repUpdateArray = array();
	    	$invReturns = new Inventory();
	    	$rack_code = array();
	    	$binInv = DB::table('bin_inventory as bin_inv')
	    				->join('inventory as inv',function($join)
	                         {
	                             $join->on('inv.product_id','=','bin_inv.product_id');
	                             $join->on('inv.le_wh_id','=','bin_inv.wh_id');
	                         })
	    				->join('products as p', 'p.product_id','=','bin_inv.product_id')
	    				->join('warehouse_config as wh_config','wh_config.wh_loc_id','=','bin_inv.bin_id')
	    				->join('bin_type_dimensions as bin_config','bin_config.bin_type_dim_id','=','wh_config.bin_type_dim_id')
	    				->select('wh_config.wh_location as bin_code','bin_inv.bin_id','bin_inv.qty','bin_inv.wh_id','bin_inv.product_id','p.product_title', 'p.product_group_id','bin_config.bin_type_dim_id','inv.min-pickface-replenishment','inv.replenishment_UOM')
	    				->where('bin_config.bin_type','=',$binType)
	    				->where('inv.le_wh_id','=',$wh_id)
	    				->get()->all();
	    	
	    	$binInv = json_decode(json_encode($binInv),true);
	    	//echo "<pre>"; print_r($binInv);
	    	
	    	//Checking if any bin having peding Putaway.
	    	
	    	$putawayBins = $this->getPendingPutawayBins($wh_id);

	    	if(!empty($binInv))
	    	{
	    		foreach ($binInv as $invValue)
	    		{
	    			$returnQty = $invReturns->pendingReturns($invValue['product_id'],$invValue['wh_id']);

	    			//Search Source Reserved bins - 109004
					if($binType == 109004)
						$source = $this->getSourceBin($invValue['product_id'],$invValue['wh_id'], 109005);
					else
						$source = $this->getSourceBin($invValue['product_id'],$invValue['wh_id'], 109004);
					if(empty($source))
						continue;

					//Initialisation 
					$totQty = 0; $binTotCap = 0; $totCap = 0; $totMin = 0;
    				$bins = array(); $status = ''; 	

    				//Checking Min-Max Qty for Product Bin
					$minMaxQty = $this->getProdBinMinMax($invValue['product_group_id'], $invValue['bin_type_dim_id'],$invValue['wh_id']);

					//Check No of Eaches in pack type
					if(!empty($minMaxQty) && $minMaxQty['packType'] !='')
						$packEachesCount = $this->getPackEachesCount($invValue['product_id'], $minMaxQty['packType']);
					else
						continue;

					if(!empty($packEachesCount) && $packEachesCount['no_of_eaches']!=''){
						$min = $minMaxQty['min_qty']*$packEachesCount['no_of_eaches'];
						$max = $minMaxQty['max_qty']*$packEachesCount['no_of_eaches'];
					} else
						continue;
					
					if($binType == 109003)
						$totQty +=$invValue['qty']+$returnQty;
					else
						$totQty +=$invValue['qty'];
					$totCap += $max; 
					$totMin += $min;

					$totReplQty = $max-$min;

					$sourceQty = 0; $sourceWhLocation = ''; $sourceWhLocId = '';
					foreach($source as $sourceData){
						//Check pending replenishment qty for source bin
						$pendingRepl = $this->getPendingReplenishQty($invValue['wh_id'], $sourceData['wh_loc_id']);
						$remainingQty = $sourceData['qty'] - $pendingRepl;
						if($remainingQty >0){
							$sourceQty = $remainingQty;
							$sourceWhLocation = $sourceData['wh_location'];
							$sourceWhLocId = $sourceData['wh_loc_id'];
							break;
						}
					}

    				if($totReplQty > $sourceQty)
    					$totReplQty = $sourceQty;
    				

    				//if replenishment qty is 0 no need to execute further
    				if($totReplQty <= 0)
    					continue;

    				//Check pending qty for source
    				
					$bins[$invValue['bin_code']]['bin_code'] = $invValue['bin_code'];
					$bins[$invValue['bin_code']]['product_id'] = $invValue['product_id'];
					$bins[$invValue['bin_code']]['product_title'] = $invValue['product_title'];
					$bins[$invValue['bin_code']]['repl_qty'] = $totReplQty;
					$bins[$invValue['bin_code']]['source'] = ($sourceWhLocation!='')?$sourceWhLocation:'';
					$bins[$invValue['bin_code']]['source_id'] = ($sourceWhLocId!='')?$sourceWhLocId:'';
					$rackStringArray = explode('-', $invValue['bin_code']);
					$bins[$invValue['bin_code']]['rack'] = $rackStringArray[0];
					$bins[$invValue['bin_code']]['wh_id'] = $invValue['wh_id'];
					$bins[$invValue['bin_code']]['bin_id'] = $invValue['bin_id'];

					//Calculate Repl. qty and check if rplenishment is needed or not
	    			if(!empty($invValue['replenishment_UOM']) && !empty($invValue['min-pickface-replenishment'])){
	    				if($binType == 109003 && $invValue['replenishment_UOM'] == '129001'){
	    					$repQty = floor(($totCap*$invValue['min-pickface-replenishment'])/100);
	    					// echo "Rep Qty: ".$repQty." Total Qty:".$totQty."Returns: ".$returnQty."<br>";
	    					if($totQty<=$repQty)
	    						$status = 'Priority';
	    					else
	    						$status = '';
	    				} elseif($binType == 109003 && $invValue['replenishment_UOM'] == '129002'){
	    					$repQty = $invValue['min-pickface-replenishment'];
	    					// echo "Rep Qty: ".$repQty." Total Qty:".$totQty."Returns: ".$returnQty."<br>";
	    					if($totQty<=$repQty)
	    						$status = 'Priority';
	    					else
	    						$status = '';
	    				}
	    			} 
	    			//If product inventory replenishment level is not present take MIN from warehouse config
	    			elseif($binType == 109003 || $binType == 109004){
	    				if($totQty<=$totMin)
    						$status = 'Priority';
    					else
    						$status = '';
	    			} else{
    					$status = '';
    				}


    				foreach($bins as $bin){
    					if($bin['source'] =='' || $bin['source_id'] =='' || $bin['repl_qty'] <=0)
    						continue;
    					if(in_array($bin['bin_id'], $putawayBins) )
    						continue;
    					$bin['status'] = $status;
    					echo "<pre>"; print_r($bin);
    					$this->binReplenishment($bin, $binType);
    				}
	    		}    		
	    	}
	    	return 'Success';
    	} catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            echo $ex;
            return $ex;
        }
    	
    }

    public function binReplenishment($binDetails, $binType){
    	try{
    		$checkRepProducts = '';
    		$whr = array('Open','Assigned');
    		$checkRepProducts = DB::table('replenishment_products')
								->where('bin_code',$binDetails['bin_code'])
								->where('bin_id',$binDetails['bin_id'])
								->where('product_id',$binDetails['product_id'])
								->where('rack',$binDetails['rack'])
								->where('wh_id',$binDetails['wh_id'])
								->whereIn('status',$whr)
								->select('replenishment_product_id')
								->first();

			//$sql = DB::getQueryLog();
			//print_r(end($sql));
			$checkRepProducts = json_decode(json_encode($checkRepProducts),true);

			if(empty($checkRepProducts) && $binDetails['status'] !='')
			{		
				if($binDetails['source']!=''){
					$getRepProductId = DB::table('replenishment_products')
						->insertGetId(array("product_id"=>$binDetails['product_id'],
									"product_title"=>$binDetails['product_title'],
									"wh_id"=>$binDetails['wh_id'],
									"bin_code"=>$binDetails['bin_code'],
									"rack"=>$binDetails['rack'],
									"replenishment_type"=>$binDetails['status'],
									"replenishment_flow"=>($binType==109004)?2:1,
									"replenish_qty"=>$binDetails['repl_qty'],
									"status"=>'Open',
									"source"=>$binDetails['source'],
									"source_id"=>$binDetails['source_id'],
									"bin_id"=>$binDetails['bin_id']));
					echo $getRepProductId."<br>";
				}				
			} elseif(!empty($checkRepProducts) && $binDetails['status'] ==''){
				DB::table('replenishment_products')
				->where('replenishment_product_id',$checkRepProducts['replenishment_product_id'])
				->update(array('status'=>'Complete', 'assigned_user'=>1, 'updated_at'=>date('Y-m-d H:i:s')));
			}
    	} catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
		
    }

	public function binConfig($bin_id)
	{
		$rs = DB::table('warehouse_config as wh_config')
				->join('bin_type_dimensions as bin_config','bin_config.bin_type_dim_id','=','wh_config.bin_type_dim_id')
				->select('bin_config.length','bin_config.heigth','bin_config.breadth')
				->where('wh_loc_id',$bin_id)
				->first();
		$rs = json_decode(json_encode($rs),true);
		$sum= $rs['length']*$rs['breadth']*$rs['heigth'];
		return floor($sum);
	}

	public function productPackConfig($p_id)
	{
		$rs = DB::table('product_pack_config')
				->where('product_id',$p_id)
				->where('level','=','16001')
				->first();
		$rs = json_decode(json_encode($rs),true);
		$sum= $rs['length']*$rs['breadth']*$rs['height'];
		return floor($sum);
	}

	public function getSourceBin($prodId, $wh_id, $binType){
		$sql = DB::table('warehouse_config as wh_config')
			->join('bin_type_dimensions as bin_config','bin_config.bin_type_dim_id','=','wh_config.bin_type_dim_id')
			->join('bin_inventory as binv', 'binv.bin_id','=','wh_config.wh_loc_id')
			->select('wh_config.wh_location', 'wh_config.wh_loc_id', 'binv.qty')
			->where('bin_config.bin_type','=',$binType)
			->where('wh_config.le_wh_id',$wh_id)
			->where('wh_config.pref_prod_id',$prodId)
			->get()->all();
			//->first();
		/*echo "<pre>";
		$sql = DB::getQueryLog();
		print_r(end($sql));*/
		$res = json_decode(json_encode($sql), true);

		if(!empty($res))
			return $res;
		else
			return '';
	}
    
    public function authenticatToken($lpToken) {
        return json_decode(json_encode(DB::table("users")->where("lp_token", $lpToken)->orWhere("password_token", $lpToken)->pluck("user_id")->all()), true);
    }
    
    public function replenishList($whId, $flow) {
        return DB::select("SELECT `replenishment_product_id`,`rack`, `product_title`, `bin_code` AS `destination`, `source`, `replenish_qty`, `replenishment_type` AS `type` FROM `replenishment_products` WHERE `status` = 'Open' AND `wh_id` = ".$whId." AND `replenishment_flow` = ".$flow."
            AND `bin_id` NOT IN (SELECT bin_id FROM replenishment_products WHERE STATUS = 'Assigned')");
    }
    
    public function saveAssign($saveparams, $flow) {
        $update_array = array("assigned_user" => $saveparams["picker_id"],
                        "assigned_time" => date('Y-m-d H:i:s'),
                        "status" => "Assigned");
        foreach($saveparams["rack_info"] as $each_info){
            $res[] = DB::table("replenishment_products")
            	->where("rack", $each_info["rack"])
            	->Where(function ($q) {
					$q->where("status", "Open")
						->orwhere("status", "Assigned");
					})
            	//->where("status", "Open")
            	->where("wh_id", $saveparams["wh_id"])
                ->where("replenishment_type", $each_info["replenishment_type"])
                ->where("replenishment_flow", $flow)
                ->update($update_array);
        }
        return $res;
    }
    
    public function getAssignList($getAssignParams, $flow) {
    	//DB::enablequerylog();
        $query =  DB::table("replenishment_products as RP")
					->join("product_pack_config as PPC", "RP.product_id", "=", "PPC.product_id")
					->join("products as p", "p.product_id", "=", "RP.product_id")
					->join("bin_inventory as bi", "bi.bin_id", "=", "RP.bin_id")
					->where("RP.assigned_user", $getAssignParams["picker_id"])
					->where("RP.status", "Assigned")
					->where("replenishment_flow",$flow)
					->where("PPC.pack_code_type", "=", 79002)
					->where("PPC.level", "=", 16001)
					->where("RP.wh_id", $getAssignParams["wh_id"])->orderBy("replenishment_type", "asc")
					->get(["RP.replenishment_product_id", "RP.product_id", "RP.product_title", "p.mrp", "RP.rack", "RP.replenishment_type", "RP.replenish_qty", "RP.assigned_user",
					"RP.bin_id", "RP.bin_code", "bi.qty as bin_inventory", "RP.source_id","RP.source", "PPC.pack_sku_code as ean"])->all();

	 	//$sql = DB::getQueryLog();
		// print_r(end($sql)); exit;
		$query = json_decode(json_encode($query), true);

		$final = array();
		if(!empty($query)){
			foreach($query as $data){
				$sql = DB::table("product_pack_config")
						->select(DB::raw("getMastLookupValue(level) as level,no_of_eaches"))
						->where("product_id",$data["product_id"])
						->get()->all();
				$sql = json_decode(json_encode($sql), true);
				$data["pack_type"] = $sql;

				$final[] = $data;
			}
		}
		return $final;
    }

    public function getAllAssignList($whId, $flow) {
    	// DB::enablequerylog();
        $sql = DB::select("SELECT `replenishment_product_id`,`rack`, `product_title`, `bin_code` AS `destination`, `source`, `replenish_qty`, `replenishment_type` AS `type`, GetUserName(assigned_user,2) as user 
        	FROM `replenishment_products` WHERE `status` = 'Assigned' AND `wh_id` = ".$whId." AND `replenishment_flow` = ".$flow."
            AND `bin_id` IN (SELECT bin_id FROM replenishment_products WHERE STATUS = 'Assigned')");
	 // 	$sql = DB::getQueryLog();
		// print_r(end($sql)); exit;
		
		return $sql;
    }
    
    public function saveReplenishmentQty($updateParams) {
    	$checkRepProducts = DB::table('replenishment_products')
								->where('replenishment_product_id',$updateParams["replenishment_product_id"])
								->select('status')
								->first();
		$checkRepProducts = json_decode(json_encode($checkRepProducts), true);

		if(!empty($checkRepProducts) && $checkRepProducts['status'] != 'Complete'){
			$update_array = array("placed_qty" => $updateParams["placed_qty"],
	                            "status" => "Complete",
	                            "updated_at" => date('Y-m-d H:i:s'));        
	        $update_res["replenish_update"] = DB::table("replenishment_products")
							->where("replenishment_product_id", $updateParams["replenishment_product_id"])
       						->where("bin_code", $updateParams["bin_code"])
       						->where("rack", $updateParams["rack"])->where("replenishment_type", $updateParams["replenishment_type"])
       						->where("assigned_user", $updateParams["picker_id"])
       						->where("wh_id", $updateParams["wh_id"])
       						->where("status", "Assigned")
       						->where("bin_id", $updateParams["bin_id"])
       						->where("source", $updateParams["source"])
       						->update($update_array);
	        if($update_res){
	            $update_res["bin_inv_destination_res"] = DB::table('bin_inventory')
							->where("wh_id", $updateParams["wh_id"])
							->where("bin_id", $updateParams["bin_id"])
							->where("product_id", $updateParams["product_id"])
							->increment('qty', $updateParams["placed_qty"]);
	            
	            $update_res["bin_inv_source_res"] = DB::table('bin_inventory')
							->where("wh_id", $updateParams["wh_id"])
							->where("bin_id", $updateParams["source_id"])
							->where("product_id", $updateParams["product_id"])
							->decrement('qty', $updateParams["placed_qty"]);
	        }
	        return $update_res;
		} else
			return 0;
    }

    public function getProdBinMinMax($grp_id, $bin_type_dim_id, $wh_id){
    	$sql = DB::table("product_bin_config")
    		->select('pack_conf_id as packType', 'min_qty', 'max_qty')
    		->where("prod_group_id", $grp_id)
    		->where("bin_type_dim_id", $bin_type_dim_id)
    		->where("wh_id", $wh_id)
    		->first();
    	$res = json_decode(json_encode($sql), true);
    	if(!empty($res))
    		return $res;
    	else
    		return '';
    }

    public function getPackEachesCount($prod_id, $packType){
    	$sql = DB::table("product_pack_config")
    	->select("no_of_eaches")
    	->where("product_id", $prod_id)
    	->where("level", $packType)
    	->first();
    	$res = json_decode(json_encode($sql), true);
    	if(!empty($res))
    		return $res;
    	else
    		return '';
    }

    public function getPendingPutawayBins($wh_id){
    	$sql = DB::table("putaway_allocation as pa")
    	->join("putaway_list as pl","pa.putaway_list_id","=", "pl.putaway_id")
    	->where("pa.wh_id",$wh_id)
    	->where("pl.putaway_status", 12801)
    	->groupBy("pa.bin_id","pa.putaway_list_id")
    	->pluck('pa.bin_id')->all();

  //   	$sql = DB::getQueryLog();
		// print_r(end($sql));
    	$res = json_decode(json_encode($sql), true);
    	if(!empty($res))
    		return $res;
    	else
    		return '';
    }

    public function getCompletedList($getCompletedParams, $flow) {
    	//DB::enablequerylog();
        $query =  DB::table("replenishment_products as RP")
					->join("product_pack_config as PPC", "RP.product_id", "=", "PPC.product_id")
					->join("products as p", "p.product_id", "=", "RP.product_id")
					->join("bin_inventory as bi", "bi.bin_id", "=", "RP.bin_id")
					->where("RP.assigned_user", $getCompletedParams["picker_id"])
					->where("RP.status", "Complete")
					->where("replenishment_flow",$flow)
					->where("PPC.pack_code_type", "=", 79002)
					->where("PPC.level", "=", 16001)
					->where("RP.wh_id", $getCompletedParams["wh_id"])->orderBy("replenishment_type", "asc")
					->get(["RP.replenishment_product_id", "RP.product_id", "RP.product_title", "p.mrp", "RP.rack", "RP.replenishment_type", "RP.replenish_qty", "RP.placed_qty", "RP.assigned_user",
					"RP.bin_id", "RP.bin_code", "bi.qty as bin_inventory", "RP.source_id","RP.source", "PPC.pack_sku_code as ean"])->all();

	 	//$sql = DB::getQueryLog();
		// print_r(end($sql)); exit;
		$query = json_decode(json_encode($query), true);

		$final = array();
		if(!empty($query)){
			foreach($query as $data){
				$sql = DB::table("product_pack_config")
						->select(DB::raw("getMastLookupValue(level) as level,no_of_eaches"))
						->where("product_id",$data["product_id"])
						->get()->all();
				$sql = json_decode(json_encode($sql), true);
				$data["pack_type"] = $sql;

				$final[] = $data;
			}
		}
		return $final;
    }

    public function getPendingReplenishQty($wh_id,$bin_id){
    	$whr = array('Open','Assigned');
    	$sql = DB::table("replenishment_products as rp")
    	->where("rp.wh_id",$wh_id)
    	->where("rp.source_id", $bin_id)
    	->whereIn('rp.status',$whr)
    	->sum('rp.replenish_qty');

  //   	$sql = DB::getQueryLog();
		// print_r(end($sql));
    	$res = json_decode(json_encode($sql), true);
    	if(!empty($res))
    		return $res;
    	else
    		return 0;
    }
}
?>