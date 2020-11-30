<?php
namespace App\Modules\DailyInventoryReport\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use \Log;
use \Session;
use Carbon\Carbon;
use DateTime;




class DailyInventoryReportMongoModel extends Eloquent {

    protected $connection = 'mongo';
    protected $table = 'daily_inventory_report';
    protected $primaryKey = '_id';
    
    public function storeDailyInventoryReportMongo($dcname, $le_wh_id, $prod_id, $sku, $p_title, $quarantine_qty,$dit, $missing, $soh, $grn_qty, $order_qty, $SR_qty, $PR_qty, $date )
    {
    	try {
                // $tz = new DateTimeZone('Asia/Kolkata'); // or whatever zone you're after
            
                $getyesterdayData = $this->oldData($date, $prod_id, $le_wh_id);
                    
                $old_data = json_decode(json_encode($getyesterdayData), true);    

                $old_quaranite_qty  = (isset($getyesterdayData['quarantine_qty']) ? $getyesterdayData['quarantine_qty'] : $quarantine_qty);
                $old_dit_qty  = (isset($getyesterdayData['dit_qty']) ? $getyesterdayData['dit_qty'] : $dit);
                $old_missing_qty  = (isset($getyesterdayData['missing_qty']) ? $getyesterdayData['missing_qty'] : $missing);
                $old_soh = (isset($getyesterdayData['soh']) ? $getyesterdayData['soh'] : $soh);

                // echo "<prE>";print_r();die;
                $ipaddress = isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:"";
                $username = Session::get("fullname");
                $userId = Session::get('userId');
                $userDetails  = array("userId" => $userId, "username" => $username);
                $now = new DateTime('Asia/Kolkata');
                $timestamp = $now->format('Y-m-d H:i:s');

                $this->date = $date;
                $this->old_quaranite_qty = $old_quaranite_qty;
                $this->old_dit_qty = $old_dit_qty;
                $this->old_missing_qty = $old_missing_qty;
                $this->old_soh = $old_soh;
                $this->dc_name = $dcname;
                $this->le_wh_id = $le_wh_id;
                $this->product_id = $prod_id;
                $this->sku = $sku;
                $this->product_title = $p_title;
                $this->quarantine_qty = (($quarantine_qty - $old_quaranite_qty ) == 0?$quarantine_qty:$old_quaranite_qty);
                $this->dit_qty = (($dit - $old_dit_qty) == 0?$dit:$old_dit_qty);
                $this->missing_qty = (($missing - $old_missing_qty) == 0?$missing:$old_missing_qty);
                $this->soh = (($soh - $old_soh)?$soh :$old_soh);
                $this->grn_qty = $grn_qty;
                $this->ordered_qty = $order_qty;
                $this->sales_return_qty = $SR_qty;
                $this->purchase_return_qty = $PR_qty;
                
				$this->userDetails = $userDetails;
                $this->ipaddress = $ipaddress;
				$this->created_at = $timestamp;
                $this->updated_at = $timestamp;
				$this->save();
    		
    	} catch (\ErrorException $ex) {
	            Log::info($ex->getMessage());
	            Log::info($ex->getTraceAsString());
        }
    	
    }

    public function getHistoryOfUserActivity($modulename)
    {
        try {
            $result = $this::where("module", "=", $modulename)->get()->all();
            $result = json_decode($result, true);
            return $result;
            
        } catch (\ErrorException $ex) {
                Log::info($ex->getMessage());
                Log::info($ex->getTraceAsString());
        }
    }

    public function oldData($date, $product_id, $le_wh_id)
    {
        try {
                $yesterday = date('Y-m-d', strtotime('-1 day', strtotime($date)));
                $sql = $this::where("date", $yesterday)->where("product_id", $product_id)->where("le_wh_id", $le_wh_id)->orderBy("date", "desc")->limit(1)->get(array("quarantine_qty", "dit_qty", "missing_qty", "soh"))->all();
                return $sql;
        } catch (\ErrorException $ex) {
                Log::info($ex->getMessage());
                Log::info($ex->getTraceAsString());
        }
    }
}
     