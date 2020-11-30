<?php

namespace App\Modules\LogisticReports\Controllers;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Modules\LogisticReports\Models\LogisticReportModel;

class LogisticReportApiController extends BaseController {

    public function __construct() {
        $this->_logisticReportModel = new LogisticReportModel();
    }

    public function LogisticSummaryApi(Request $request) {
        $data = $request->json()->all();
        $LogisticReportModelObj = new LogisticReportModel();
        $data = $data['data'];
        if (isset($data['dc_id']) && isset($data['hub_id']) && (isset($data['user_id'])) && (isset($data['from_date']) && !empty($data['from_date'])) && (isset($data['to_date']) && !empty($data['to_date'])) && (isset($data['report_type']) && !empty($data['report_type'])) && (isset($data['period_type']) && !empty($data['period_type']))) {

            $resultQuery = $LogisticReportModelObj->getLogisticSummaryData($data);
            $resultArray = array();
            $key_name = '';
            $getHubInfoArray = array();
            $rs_hub_id = '';
            foreach ($resultQuery as $value) {
                if (isset($getHubInfoArray[$value['inp_date']])) {
                    $find = array_search($value['hub_name'], array_column($getHubInfoArray[$value['inp_date']], 'hub'));

                    if ($find === false)
                        $getHubInfoArray[$value['inp_date']][] = array("hub" => $value['hub_name'], "order_count" => $value['count_no'] ,"order_value" => $value['value_tot']);
                    else{
                        $getHubInfoArray[$value['inp_date']][$find]["order_count"] += $value['count_no'];
                        $getHubInfoArray[$value['inp_date']][$find]["order_value"] += $value['value_tot'];
                    }
                } else
                    $getHubInfoArray[$value['inp_date']][] = array("hub" => $value['hub_name'], "order_count" => $value['count_no'] ,"order_value" => $value['value_tot']);
            }

            $dateArr = array();
            foreach ($resultQuery as $value) {
                if (isset($getHubInfoArray[$value['inp_date']])) {
                    $find = array_search($value['hub_name'], array_column($getHubInfoArray[$value['inp_date']], 'hub'));
                    if ($find !== false) {
                        $getHubInfoArray[$value['inp_date']][$find]["values"][] = array($value['key_name'] => array("count" => $value['count_no'], "count_value" => $value['value_tot'], 'tool_tip' => $value['tooltip']));
                    }
                }
                if (!isset($dateArr[$value['inp_date']][$value['key_name']]['count'])) {
                    $dateArr[$value['inp_date']][$value['key_name']]['count'] = $value['count_no'];
                    $dateArr[$value['inp_date']][$value['key_name']]['count_value'] = $value['value_tot'];
                    $dateArr[$value['inp_date']][$value['key_name']]['tool_tip'] = $value['tooltip'];
                } else {
                    $dateArr[$value['inp_date']][$value['key_name']]['count'] += $value['count_no'];
                    $dateArr[$value['inp_date']][$value['key_name']]['count_value'] += $value['value_tot'];
                    $dateArr[$value['inp_date']][$value['key_name']]['tool_tip'] = $value['tooltip'];
                }
            }

            $dates = array_keys($getHubInfoArray);
            $allTot = array();
            foreach ($dates as $data) {
                $grandTot = array_sum(array_column($getHubInfoArray[$data], 'order_count'));
                $grandVal = array_sum(array_column($getHubInfoArray[$data], 'order_value'));
                $temp = array();
                foreach ($dateArr[$data] as $key => $grandValue) {
                    $temp[] = array($key => $grandValue);
                    if (isset($allTot[$key])) {
                        $allTot[$key]['count'] += $grandValue['count'];
                        $allTot[$key]['count_value'] += $grandValue['count_value'];
                    } else {
                        $allTot[$key]['count'] = $grandValue['count'];
                        $allTot[$key]['count_value'] = $grandValue['count_value'];
                    }
                }
                $getHubInfoArray[$data][] = array("hub" => "Grand Total", "order_count" => $grandTot, "order_value" => $grandVal, "values" => $temp);
            }
            $getHubInfoArray['Total'][] = $allTot;
            return json_encode(array("status" => "Success", "message" => "Report Data.", "data" => $getHubInfoArray));
        } else {
            return json_encode(array("status" => "Failed", "message" => "Please provide valid inputs like dc_id, hub_id, from_date, to_date, report_type and period_type.", "data" => []));
        }
    }

    public function crateSummaryReport(Request $request) {
        $request_data = $request->json()->all();

        if (isset($request_data["data"]["dc_id"]) && isset($request_data["data"]["hub_id"])) {
            $result = $this->_logisticReportModel->crateSummary($request_data["data"]["dc_id"], $request_data["data"]["hub_id"]);
            return json_encode(array("status" => "Success", "message" => "Crate summary report data", "data" => $result));
        } else {
            return json_encode(array("status" => "Failed", "message" => "Please provide valid inputs like dc_id, hub_id.", "data" => []));
        }
    }

}
