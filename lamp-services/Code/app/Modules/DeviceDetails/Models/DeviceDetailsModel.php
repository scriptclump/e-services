<?php

namespace App\Modules\DeviceDetails\Models;
use App\Central\Repositories\RoleRepo;
use Illuminate\Database\Eloquent\Model;

use DB;
use Log;

class DeviceDetailsModel extends Model
{
  public function getDeviceList($makeFinalSql, $orderBy, $page, $pageSize)
    {
    	
       if($orderBy!=''){
			$orderBy = ' ORDER BY ' . $orderBy;
		}

		$sqlWhrCls = '';
		$countLoop = 0;
		
		foreach ($makeFinalSql as $value) {
			if( $countLoop==0 ){
				$sqlWhrCls .= ' WHERE ' . $value;
			}elseif( count($makeFinalSql)==$countLoop ){
				$sqlWhrCls .= $value;
			}else{
				$sqlWhrCls .= ' AND ' .$value;
			}
			$countLoop++;
		}
    
        $sqlQuery="SELECT * from vw_retailer_device_details as inrtbl";
         if($sqlWhrCls!=''){
            
         	  $sqlQuery.=$sqlWhrCls;
         }
         $pageLimit = '';
		if($page!='' && $pageSize!=''){
		    $pageLimit = " LIMIT " . (int)($page*$pageSize) . ", " . $pageSize;
		}

    	DB::enableQueryLog();
    	$result = DB::select(DB::raw($sqlQuery . $pageLimit));
       return json_decode(json_encode($result),true);

		
    }

   public function GetWarehouses()
      {

      $warehouse_res=DB::table("legalentity_warehouses")
                         ->select('*')
                         ->where('dc_type','=','118001')
                         ->get()->all();

             
      return json_decode(json_encode($warehouse_res),true);
      }  

    public function GetBeats()
    {

     $beat_res=DB::table("pjp_pincode_area")
                         ->select('*')->get()->all();
           
      return json_decode(json_encode($beat_res),true);

    } 

    public function GetHubs()
     {

     	     $hub_qry="SELECT * FROM legalentity_warehouses WHERE dc_type='118002'";


                 $hubs_res=DB::select(DB::raw($hub_qry));

         
        return json_decode(json_encode($hubs_res),true); 
     }  

     public function getAjaxHubsList($warehouse)
     {

     DB::enableQueryLog();
    	 

            $result = DB::table("legalentity_warehouses")
    	                ->Join('dc_hub_mapping','dc_hub_mapping.hub_id','=','legalentity_warehouses.le_wh_id')
    					->select('*')
    					->where('dc_hub_mapping.dc_id','=',$warehouse)
                        ->get()->all();            
       
       return json_decode(json_encode($result),true);



     }

     public function getAjaxBeatsList($hubid)
     {

            $result = DB::table("pjp_pincode_area")
                        ->Join('legalentity_warehouses','pjp_pincode_area.le_wh_id','=','legalentity_warehouses.le_wh_id')
                        ->select('*')
                        ->where('pjp_pincode_area.le_wh_id','=',$hubid)
                        ->get()->all();            
       
       return json_decode(json_encode($result),true);



     }


     public function getAjaxDeviceHubList($hub)
     {
    
     DB::enableQueryLog();
    	 $result = DB::table("device_details")
    	                ->leftJoin('users','users.user_id','=','device_details.user_id')
    	                ->Join('legalentity_warehouses','legalentity_warehouses.legal_entity_id','=','users.legal_entity_id')
    	                ->Join('pjp_pincode_area','pjp_pincode_area.le_wh_id','=','legalentity_warehouses.le_wh_id')
    					->select('*')
    					->where('legalentity_warehouses.le_wh_id','=',$hub)
    					->groupBy(['device_details.user_id'])
                        ->get()->all();

        
       
       return json_decode(json_encode($result),true);



     }

     public function getAjaxDeviceBeatsList($beats)
     {
    
         DB::enableQueryLog();
    	 $result = DB::table("device_details")
    	                ->leftJoin('users','users.user_id','=','device_details.user_id')
    	                ->leftJoin('legalentity_warehouses','legalentity_warehouses.legal_entity_id','=','users.legal_entity_id')
    	                ->Join('pjp_pincode_area','pjp_pincode_area.le_wh_id','=','legalentity_warehouses.le_wh_id')
    					->select('*')
    					->where('legalentity_warehouses.le_wh_id','=',$beats)
    					->groupBy(['device_details.user_id'])
                        ->get()->all();

          return json_decode(json_encode($result),true);



     }

}
