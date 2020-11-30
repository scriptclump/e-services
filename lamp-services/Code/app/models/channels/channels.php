<?php

namespace App\models\channels;
use Illuminate\Database\Eloquent\Model;
use DB;
use Session;

class channels extends Model {
    public function getChannels() {
        try {            
            $channels = DB::table("mp")->select('mp_id', 'mp_name', 'mp_item_url', 'mp_logo','mp_key')->get()->all();
            return $channels;
        } catch (\Whoops\Exception\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
    }

    public function getChannelImages($channelId) {
        try {
            $channelImage = DB::table("mp")->select('mp_id', 'mp_name', 'mp_item_url', 'mp_logo', 'mp_description')
                            ->where('mp_id', $channelId)->get()->all();
            return $channelImage;
        } catch (\Whoops\Exception\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
    }

	
	public function getChannelWarehouses($legalEntityId=0) {
        try {                          
            $warehouses = DB::table('legalentity_warehouses')
                    ->where('legal_entity_id','=',$legalEntityId)
                    ->select('lp_wh_id','lp_wh_name')
                    ->get()->all();
             return $warehouses;               
            //$wherehouses = 1;
            
        } catch (\Whoops\Exception\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
    }

    /*public function getChannelWarehouses() {
       
        try {
            $userId = Session::get('userId');
            if($userId!=1){
            $legal_id = Session::get('legal_entity_id');
               $warehouses = DB::table('legalentity_warehouses')
                            ->where('legal_entity_id','=',$legal_id)
                            ->select('le_wh_id','lp_wh_name')                            
                            ->get()->all();
               return $warehouses;
            }
            else
            {
                $warehouses = DB::table('legalentity_warehouses')                            
                            ->select('le_wh_id','lp_wh_name')
                            ->groupBy('lp_wh_name')
                            ->get()->all();
                return $warehouses;
            }
                    //echo '<pre>';print_R($warehouses);exit;
            /*$warehouses = DB::table('lp_warehouses as lw')
                    ->Join('logistics_partners as lp', 'lp.lp_id', '=', 'lw.lp_id')
                    ->join('zone','zone.zone_id','=','lw.state')
                    ->select('lp.lp_legal_name', 'lw.lp_wh_id', 'lw.lp_wh_name', 'zone.name')
                    ->get()->all();
            
            //$wherehouses = 1;
            
        } catch (\Whoops\Exception\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
    }*/

}
