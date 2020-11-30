<?php

namespace App\Modules\LogisticReports\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class LogisticReportModel extends Model {

    public function getLogisticSummaryData($data) {
        $dc_id = ($data['dc_id'] != '') ? $data['dc_id'] : 'NULL';
        $hub_id = ($data['hub_id'] != '') ? $data['hub_id'] : 'NULL';
        $from_date = $data['from_date'];
        $to_date = $data['to_date'];
        $report_type = $data['report_type'];
        $period_type = $data['period_type'];
        $user_id = ($data['user_id'] != '') ? $data['user_id'] : 'NULL';
        $rs = DB::select('CALL getKPIReportsLogisticsData(' . $dc_id . ',' . $hub_id . ',' . $user_id . ',"' . $report_type . '","' . $from_date . '","' . $to_date . '",' . $period_type . ')');
        $rs = json_decode(json_encode($rs), true);
        return $rs;
    }

    public function crateSummary($dcId = "", $hubId = "") {
        $query = "SELECT * FROM rpt_logistics_dashboard_cols WHERE report_type = 'getKPICratesCount'";
        if ($dcId != "") {
            $query .= " AND dc_id IN (" . $dcId . ")";
        }
        if ($hubId != "") {
            $query .= " AND hub_id IN (" . $hubId . ")";
        }
        $query_res = json_decode(json_encode(DB::select(DB::raw($query))), true);

        $unique_status = json_decode(json_encode(DB::table("rpt_logistics_dashboard_cols")->distinct()->where("report_type", "getKPICratesCount")->pluck("key_name")->all()), true);

        $hub_wise = $temp = $final = $grand_total = array();
        foreach ($query_res as $each_array) {
            $hub_wise[$each_array["hub_name"]][$each_array["key_name"]] = $each_array["count_no"];
        }

        foreach ($unique_status as $each_uq_status) {
            foreach ($hub_wise as $hub_name => $hub_values) {
                if (!in_array($each_uq_status, array_keys($hub_values))) {
                    foreach ($hub_values as $status_name => $status_count) {
                        $temp[$hub_name][$status_name]["count"] = $status_count;
                    }
                    $temp[$hub_name][$each_uq_status]["count"] = 0;
                }
            }
        }

        foreach ($temp as $temp_key => $temp_value) {
            $final[] = array("hub" => $temp_key, "order_count" => "", "temp_values" => $temp_value);
        }

        foreach ($final as $key => $val) {
            foreach ($val["temp_values"] as $sub_key => $sub_value) {
                $final[$key]["order_count"] += $sub_value["count"];
                $grand_total["temp_values"][$sub_key]["count"][] = $sub_value["count"];
                $final[$key]["values"][] = array($sub_key => $sub_value);
            }
            unset($final[$key]["temp_values"]);
        }

        $grand_total["hub"] = "Grant Total";
        $grand_total["order_count"] = array_sum(array_column($final, 'order_count'));
        foreach ($grand_total["temp_values"] as $get_key => $gt_value) {
            $grand_total["values"][][$get_key]["count"] = array_sum($gt_value["count"]);
        }
        unset($grand_total["temp_values"]);

        array_push($final, $grand_total);
        return $final;
    }

}
