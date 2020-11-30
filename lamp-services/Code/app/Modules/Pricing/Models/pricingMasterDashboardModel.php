<?php
/*
FileName :pricingMasterDashboardModel.php
Author   :eButor
Description : Pricing dashboard model.
CreatedDate :9/aug/2016
*/
//defining namespace
namespace App\Modules\Pricing\Models;

use Illuminate\Database\Eloquent\Model;
use Mail;
use DB;
use Session;
use UserActivity;

class pricingMasterDashboardModel extends Model{

    
    public function getAllProducttype()
    {
        $getproductdata=DB::table('master_lookup')
                          ->select('master_lookup_name','value')
                          ->where ('mas_cat_id', '=', '3')
                          ->get()->all();

            return $getproductdata;
               
    }
    
     public function getDefaultProducttype()
    {
        $getDefaultProducttype=DB::table('master_lookup')
                          ->select('value')
                          ->where ('mas_cat_id', '=', '3')
                          ->where('master_lookup_name', '=', 'ALL')
                          ->get()->all();

            return $getDefaultProducttype;
               
    }

   
    public function PricingMasterDetailsData($makeFinalSql, $orderBy, $page, $pageSize, $bu_id,$cust_tp,$flag){

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
        $sqlQuery = "CALL getPricingProductsByDC(".$bu_id.",".$cust_tp.",".$flag.")";
        $allData= DB::selectFromWriteConnection(DB::raw( $sqlQuery));
        $TotalRecordsCount = count($allData);        
       
        return json_encode(array('Records'=>$allData, 'TotalRecordsCount'=>(int)($TotalRecordsCount))); 
    }

  
     public function getstateIdByDC($dcid){
    
        $stateid = DB::table("legalentity_warehouses")
                ->select('state')
                ->where('le_wh_id', '=', $dcid)
                ->get()->all();

         return $stateid[0]->state;
    }

    public function  getExportPricingMasterData($wh_id,$cust_tp,$flag)
    {
        try{
           
            return DB::selectFromWriteConnection(DB::raw("CALL getPricingProductsByDC(".$wh_id.",".$cust_tp.",".$flag.")"));


        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return false;
        }
    }
   
    public function getWarehouseName($le_wh_id)
    {
       try{
           $le_wh_name = DB::table("legalentity_warehouses")
                ->select('display_name')
                ->where('le_wh_id', '=', $le_wh_id)
                ->get()->all();
            
            return $le_wh_name[0]->display_name;


        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return false;
        }


    }
    

}   