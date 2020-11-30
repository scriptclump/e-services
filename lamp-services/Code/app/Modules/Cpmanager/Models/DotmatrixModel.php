<?php
namespace App\Modules\Cpmanager\Models;
use Illuminate\Database\Eloquent\Model;
use App\Modules\Cpmanager\Views\order;
use App\Modules\Roles\Models\Role;
use App\Modules\Cpmanager\Controllers\accountController;
use DB;
use views;
use view;
use Config;



class DotmatrixModel extends Model
{
  public function getOrderInfo($hubId, $filters) {
      try {
            $fields = array('orders.gds_order_id as order_id','orders.order_code','orders.shop_name',DB::raw("getBeatName(orders.beat) as beat"),DB::raw('DATE(invgrid.created_at) as invoice_date'),'orders.is_inv_print as inv_print');
            $query = DB::table('gds_orders as orders')->select($fields);
            $query->join('gds_invoice_grid as invgrid', 'invgrid.gds_order_id','=','orders.gds_order_id');
            $query->where('orders.hub_id', $hubId);
            
            if($filters['status']=='sit') {
                $query->where('orders.order_status_id', 17024);
              } else {
              $query->where('orders.order_status_id', 17021);
            }           
            if(isset($filters['beat_ids']) && !empty($filters['beat_ids'])) {
              $query->whereIn('orders.beat', $filters['beat_ids']);
            }  

            if($filters['start_date']!='' && $filters['end_date']!='') {
                $query->whereBetween('invgrid.created_at', [$filters['start_date'].' 00:00:00', $filters['end_date'].' 23:59:59']);
            } else {
                $query->whereBetween('invgrid.created_at', [date('Y-m-d').' 00:00:00', date('Y-m-d').' 23:59:59']);
            }
            return $query->get()->all();
      }
      catch(Exception $e) {
              Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
      }
  }

  public function getHubsList($user_id) {
      try {
        
           $roleModel = new Role();
            $Json = json_decode($roleModel->getFilterData(6,$user_id),1);
            $Json = json_decode($Json['sbu'],1);
            //print_r($Json);
           if(count($Json)>0){
              if(array_key_exists("118002",$Json)){
                $data=$Json['118002'];
                $query="select le_wh_id as hub_id,lp_wh_name as hub_name,bu.tally_company_name, bu.sales_ledger_name from legalentity_warehouses
                  JOIN business_units AS bu ON bu.bu_id = legalentity_warehouses.bu_id
                  where le_wh_id IN($data) AND status";
                $query=DB::select($query);
                return $query;
              }else{
                return 0;
              }
            }else{
              return 0;
            }

            

      }
      catch(Exception $e) {
              Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
      }
  }

  public function getBeatsByHub($hub_id,$flag) {
      try {
        if($flag == 1){
          $hub_id=explode(',',$hub_id);
          $beatData = DB::table('pjp_pincode_area')
              ->select(array('pjp_pincode_area_id','pjp_name'))
              ->whereIn('le_wh_id',$hub_id)
              ->get()->all();
            return $beatData;
        }else{
          if($hub_id>0) {
            $hub_id=explode(',', $hub_id);
            $beatData = DB::table('pjp_pincode_area')
              ->select(array('pjp_pincode_area_id as beat_id','pjp_name as beat_name'))
              ->whereIn('le_wh_id',$hub_id)
              ->get()->all();
            return $beatData;

          } else {
            return false;
          }
        }

      }
      catch(Exception $e) {
              Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
      }
  }


  public function updateInvoicePrintStatus($orderIds) {

      DB::beginTransaction(); 

      try {
            $query = DB::table('gds_orders')->whereIn('gds_order_id',$orderIds)->update(array('is_inv_print'=>1));
            DB::commit();

            return true;
      }
      catch(Exception $e) {
            DB::rollback(); 
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
      }
  }
  public function getCheckingToken($token){
    $account = new accountController();
    $query=$account->getDataFromToken(1,$token,['password_token','lp_token']);
    return $query;
  }

}