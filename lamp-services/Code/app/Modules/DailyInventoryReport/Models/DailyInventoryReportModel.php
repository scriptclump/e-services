<?php
namespace App\Modules\DailyInventoryReport\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Roles\Models\Role;
use Session;
use DB;
use App\Central\Repositories\RoleRepo;
use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;
use Log;
use App\Modules\DailyInventoryReport\Models\DailyInventoryReportMongoModel;


class DailyInventoryReportModel extends Model {
    public function getDialyInventoryData()
    {
        try {
                // $yesterdayDate = date('Y-m-d',strtotime("1 days"));
                $yesterdayDate = date('Y-m-d');
                
                // $yesterdayDate = "2017-06-08";
                $end_Date = $yesterdayDate." 23:59:59";
                $Grn_Data = array();
                $Sales_Orders_Data = array();
                $Purchase_Return_Data = array();
                $Sales_Return_Data = array();
                /* GRN DATA*/
                $GRN_data  = DB::table("stock_inward")
                                        ->whereNotNull('po_no')
                                        ->whereBetween('created_at', array($yesterdayDate, $end_Date))
                                        ->groupBy("product_id")
                                        ->get(array("product_id", DB::raw("sum(good_qty) as good_qty"), "le_wh_id"))->all();
                $grn_mian_data = json_decode(json_encode($GRN_data), true);
            
                foreach ($grn_mian_data as $key => $grnvalue) {
                    
                    $Grn_Data[$grnvalue['le_wh_id']][$grnvalue['product_id']] = isset($grnvalue['good_qty'])?$grnvalue['good_qty']:0;
                }  
                /* Sales Orders DATA*/
                
                $Sales_orders_Data = DB::table("gds_order_products as GOP")
                                ->join("gds_orders as GO", "GO.gds_order_id", "=", "GOP.gds_order_id")
                                ->whereBetween("GOP.created_at", array($yesterdayDate, $end_Date))->groupBy("product_id")->get(array("product_id", DB::raw("sum(qty) as qty"), "GO.le_wh_id"))->all();

                $sales_return_maindata = json_decode(json_encode($Sales_orders_Data), true);
                
                foreach ($sales_return_maindata as $key => $value) {
                    $Sales_Orders_Data[$value['le_wh_id']][$value['product_id']] = isset($value['qty'])?$value['qty']:0;
                }
                

                /* Purchase Returns*/
                $purchase_return_sql = DB::table("inward AS I")
                                        ->leftJoin("purchase_returns AS PR", "PR.inward_id", "=", "I.inward_id")
                                        ->leftJoin("purchase_return_products AS PRP", "PR.pr_id", "=", "PRP.pr_id")
                                        ->whereBetween("PRP.created_at", array($yesterdayDate, $end_Date))
                                        ->groupBy("PRP.product_id")
                                        ->get(array(DB::raw("sum(PRP.qty) as qty"), "PRP.product_id", "I.le_wh_id"))->all();
                $purchase_return_data = json_decode(json_encode($purchase_return_sql), true);

                foreach ($purchase_return_data as $pur_ret_key => $pur_ret_value) {
                    $Purchase_Return_Data[$pur_ret_value['le_wh_id']][$pur_ret_value['product_id']] = isset($pur_ret_value['qty'])?$pur_ret_value['qty']:0;
                }

                /*Sales Returns*/
                $salesReturnsSql = DB::table("gds_orders AS GO")
                                        ->join("gds_returns AS GR", "GO.gds_order_id", "=", "GR.gds_order_id")
                                        ->whereBetween("GR.created_at", array($yesterdayDate, $end_Date))
                                        ->groupBy("product_id")
                                        ->get(array("product_id", DB::raw("SUM(qty) AS qty"), "GO.le_wh_id"))->all();
                $sales_returns_data = json_decode(json_encode($salesReturnsSql), true);

                foreach ($sales_returns_data as $sales_returns_key => $sales_returns_value) {
                    $Sales_Return_Data[$sales_returns_value['le_wh_id']][$sales_returns_value['product_id']] = isset($sales_returns_value['qty'])?$sales_returns_value['qty']:0;
                }

                $sql = DB::table("vw_inventory_report as VIR")
                                    ->join("legalentity_warehouses as LW","LW.le_wh_id", "=", "VIR.le_wh_id" )
                                    ->where("LW.status", "=", 1) /* Here we'll get all active warehouses  */
                                    ->get(array("VIR.product_id", "VIR.sku", "VIR.product_title", "VIR.dcname", "VIR.le_wh_id", "quarantine_qty", "dit_qty", "dnd_qty as missing_qty", "VIR.soh"))->all();
                $maindata  = json_decode(json_encode($sql), true);

                foreach ($maindata as $key => $value) {
                    $dailyInventoryObj = new DailyInventoryReportMongoModel();
                    $oldMongoData = $dailyInventoryObj->oldData($yesterdayDate, $value['product_id'], $value['le_wh_id']);
                    $old_MongoData = json_decode(json_encode($oldMongoData), true);
                    

                    $grn_qty = isset($Grn_Data[$value['le_wh_id']][$value['product_id']])?$Grn_Data[$value['le_wh_id']][$value['product_id']]:0;
                    $order_qty = isset($Sales_Orders_Data[$value['le_wh_id']][$value['product_id']])?$Sales_Orders_Data[$value['le_wh_id']][$value['product_id']]:0;
                    $sales_return_qty = isset($Sales_Return_Data[$value['le_wh_id']][$value['product_id']])?$Sales_Return_Data[$value['le_wh_id']][$value['product_id']]:0;
                    $puurchase_return_qty = isset($Purchase_Return_Data[$value['le_wh_id']][$value['product_id']])?$Purchase_Return_Data[$value['le_wh_id']][$value['product_id']]:0;

                    $maindata[$key]['opening_balance'] = (isset($old_MongoData[0]['soh']) ? $old_MongoData[0]['soh'] : 0);
                    $maindata[$key]['grn_qty'] = $grn_qty;
                    $maindata[$key]['gds_order_qty'] = $order_qty;
                    $maindata[$key]['sales_return_qty'] = $sales_return_qty;
                    $maindata[$key]['purchase_return_qty'] = $puurchase_return_qty;



                     /* storing the Report in Mongo Collection */
                    $saveReport = $dailyInventoryObj->storeDailyInventoryReportMongo($value['dcname'], $value['le_wh_id'], $value['product_id'], $value['sku'], $value['product_title'], $value['quarantine_qty'],$value['dit_qty'], $value['missing_qty'], $value['soh'], $grn_qty, $order_qty, $sales_return_qty, $puurchase_return_qty, $yesterdayDate);

                    // $maindata[$key]['old_quarantine_qty'] = (isset($old_MongoData['quarantine_qty']) ? $old_MongoData['quarantine_qty'] : $value['quarantine_qty']);
                    // $maindata[$key]['old_dit_qty'] = (isset($old_MongoData['dit_qty']) ? $old_MongoData['dit_qty'] : $value['dit_qty']);
                    // $maindata[$key]['old_missing_qty'] = (isset($old_MongoData['missing_qty']) ? $old_MongoData['missing_qty'] : $value['missing_qty']);

                    unset($maindata[$key]['quarantine_qty']);
                    unset($maindata[$key]['dit_qty']);
                    unset($maindata[$key]['missing_qty']);
                    unset($maindata[$key]['le_wh_id']);
                    unset($maindata[$key]['soh']);

                    $maindata[$key]['quarantine_qty'] = ($value['quarantine_qty'] - (isset($old_MongoData[0]['quarantine_qty']) ? $old_MongoData[0]['quarantine_qty'] : 0));
                    $maindata[$key]['dit_qty'] = ($value['dit_qty'] - (isset($old_MongoData[0]['dit_qty']) ? $old_MongoData[0]['dit_qty'] : 0));
                    $maindata[$key]['missing_qty'] = ($value['missing_qty'] - (isset($old_MongoData[0]['missing_qty']) ? $old_MongoData[0]['missing_qty'] : 0));
                    
                    $maindata[$key]['closing_balance'] = $value['soh'];
                }

                return $maindata;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
        }
    }

    public function getUserEmail($userId){
        $srmQuery = json_decode(json_encode(DB::table("users")->where('user_id','=',$userId)->pluck('email_id')->all()), true);
        return ($srmQuery[0]);
    }

    public function getAllRolesByFeatureCode($feature_code)
    {
        try {
            $roles_query = DB::table("features AS FF")
                            ->join("role_access AS RA", "FF.feature_id", "=", "RA.feature_id")
                            ->join("roles as R", "R.role_id", "=", "RA.role_id")
                            ->where("feature_code", "=", $feature_code)
                            ->pluck("R.name")->all();
            return $roles_query;
            
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
        }
    }




}
