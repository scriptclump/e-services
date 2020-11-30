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

    
}
