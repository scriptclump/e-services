<?php

namespace App\Modules\Orders\Models;

use Illuminate\Database\Eloquent\Model;
use Log;
use DB;
use Response;
use Session;
use Notifications;
use Mail;
use Utility;

class AutoAssignpickerCheckerModel extends Model {

    public function getUsersByFeatureCode($featureCode,$le_id,$roletoIgnore) {
        $usersList = [];
        if ($featureCode != '') {
            $msdata = $this->getMasterLokup(78014);
            $mslimitdata = $this->getMasterLokup(78015);
            $roletoIgnore = isset($msdata->description) ? $msdata->description : 0;
            $maxassigned = isset($mslimitdata->description) ? $mslimitdata->description : 200;
            DB::enableQueryLog();
            $query = 'select `users`.`user_id`, `users`.`firstname`, `users`.`lastname`, `users`.`email_id`, `users`.`mobile_no`, 
            concat(users.firstname, " ", users.lastname) as name, 
            (select count(gop.product_id) 
            from gds_order_track trck join gds_orders go on trck.gds_order_id=go.gds_order_id 
            join gds_order_products gop on gop.gds_order_id=go.gds_order_id 
            where picker_id=users.user_id 
            AND DATE(scheduled_piceker_date)=CURDATE()) as ordersassigned 
            from `role_access` 
            inner join `features` on `role_access`.`feature_id` = `features`.`feature_id` 
            inner join `user_roles` on `role_access`.`role_id` = `user_roles`.`role_id` 
            inner join `users` on `users`.`user_id` = `user_roles`.`user_id` 
            left join `attendance` as attn on `attn`.`user_id` = `users`.`user_id`
            where (`features`.`feature_code` = "' . $featureCode . '" and `users`.`is_active` = 1 and users.legal_entity_id='.$le_id.') 
            and `role_access`.`role_id` not in (' . $roletoIgnore . ')
            and attn.`attn_date`=CURDATE() AND attn.is_present=1 
            group by `users`.`user_id` having `ordersassigned` < ' . $maxassigned . ' order by `ordersassigned` asc limit 1';
            $usersList = DB::selectFromWriteConnection(DB::raw($query));
            //Log::info('auto assign pickers query');
            //Log::info(DB::getQueryLog());
        }
        return $usersList;
    }

    public function getOpenOrders($le_id) {
        $openorderQuery = "select go.gds_order_id,go.le_wh_id,go.hub_id,go.legal_entity_id,order_status_id,order_code FROM gds_orders AS go 
                left join gds_order_track trck on trck.gds_order_id=go.gds_order_id 
                WHERE go.order_status_id=17001 AND go.auto_assign=0 AND go.legal_entity_id=$le_id AND (trck.picker_id=0 OR trck.picker_id IS NULL) order by go.gds_order_id desc";
        $allData = DB::select(DB::raw($openorderQuery));
        return $allData;
    }
    public function checkDCHubMapping($le_wh_id,$hub_id) {
        $data = DB::table('dc_hub_mapping')
                        ->select('dc_hub_map_id', 'is_active')
                        ->where('dc_id', $le_wh_id)
                        ->where('hub_id', $hub_id)
                        ->first();
        return $data;
    }

    public function updateOrder($gds_order_id) {
        DB::table('gds_orders AS go')->where('go.gds_order_id' ,$gds_order_id)->update(array('auto_assign' => 1));
    }
    public function getMasterLokup($value) {
        $data = DB::table('master_lookup')
                        ->select('master_lookup_id', 'description', 'value')
                        ->where('value', $value)->first();
        return $data;
    }
    public function getEnableLegalEntity() {
        $data = DB::table('legal_auto_assign_picker')
                        ->select('legal_entity_id', 'is_enable','roles_to_ignore')
                        ->where('is_enable', 1)->get()->all();
        return $data;
    }

}
