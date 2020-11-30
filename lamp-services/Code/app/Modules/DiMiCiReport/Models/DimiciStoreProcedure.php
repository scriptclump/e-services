<?php

namespace App\Modules\DiMiCiReport\Models;

use DB;
use Illuminate\Database\Eloquent\Model;
use Log;

class DimiciStoreProcedure extends Model {

    public function generateNewReport($dc, $start, $end, $cfc_to_buy) {
//        Log::info("Request came to DimiciStoreProcedure model : generateNewReport method");

        if (empty($end)) {
            $end_dt = date('Y-m-d');
        }
        if (empty($start)) {
            $start_dt = date('Y-m-d', strtotime($end . " -15 days"));
        }

        $start_dt = date('Y-m-d', strtotime($start));
        $end_dt = date('Y-m-d', strtotime($end));

      //  Log::info("Calling UpdateDiMiCiFlat_dt procedure with parametes, From Date: " . $start_dt . " To Date: " . $end_dt . " and Warehouse Id: " . $dc);

        $procedure_call = json_decode(json_encode(DB::select(DB::raw("CALL UpdateDiMiCiFlat_dt('" . $start_dt . "', '" . $end_dt . "', " . $dc . ")"))), true);

        if (isset($procedure_call[0]) && $procedure_call[0]["success"] = "success") {
           // Log::info("Procedure call is success, Fetching data from product_di_mi_ci_flat_dt table with where clauses - From Date: " . $start_dt . " To Date: " . $end_dt . " and Warehouse Id: " . $dc);

            $flat_table_res = DB::table("product_di_mi_ci_flat_dt")
                    ->where("from_date", "$start_dt")
                    ->where("to_date", "$end_dt");

            if ($cfc_to_buy != "") {
                $flat_table_res = $flat_table_res->where("cfc_to_buy", $cfc_to_buy);
              //  Log::info("Applying where clause for cfc_to_buy: " . $cfc_to_buy);
            }

            $flat_table_res = $flat_table_res->get([DB::raw("p_group_id as 'Product Group Id', sku as SKU, manf as 'Manufacturer Name',"
                        . " prod_name as 'Product Title', product_id as 'Product Id', kvi as KVI, mrp as MRP, cp_enabled as 'CP Enabled', esu as ESU,"
                        . " cfc_qty as 'CFC Qty', sp as ESP, lp as ELP, ptr as PTR, margin as 'Ebutor Margin %', tlc as TLC, net_sold_qty as 'Net Sold Qty',"
                        . " avgdaysales_ea as 'Avg Day Sales (Eaches)', avgdaysales_cfc as 'Avg Day Sales (CFC)', avgesp as 'Avg ESP', avgelp as 'Avg ELP',"
                        . " total_earnings as 'Total Earnings', di as 'DI%', mi as 'MI%', ci as 'CI%', avg_dimici as 'DI MI CI Avg %', kvi_ranking as 'KVI Ranking',"
                        . " available_cfc as 'Available CFC', di_stock_days as 'DI Stock Days', mi_stock_days as 'MI Stock Days', ci_stock_days as 'CI Stock Days',"
                        . " avg_dimici_stock_days as 'Avg DI MI CI Stock Days', stock_norm as 'Stock Norm', suggested_bq as 'Suggested BQ',"
                        . " cfc_to_buy as 'CFC To Buy', cfc_elp as 'CFC ELP', target_cfc_elp as 'Target CFC ELP', total_amount as 'Total Amount', rm as PM,"
                        . " supplier_code as 'Supplier Code', supplier_name as 'Supplier Name', max_bq as 'Max BQ', max_cfc_allowed as 'Max CFC Allowed',"
                        . " max_cfc_elp_allowed as 'Max CFC ELP Allowed', freebie_title as 'Freebie Product Title', star as Star")])->all();

           // Log::info("Return final result from product_di_mi_ci_flat_dt table");
            return $flat_table_res;
        } else {
            Log::info("Something went wrong while calling the procedure, so returning empty array.");
            return array();
        }
    }

}
