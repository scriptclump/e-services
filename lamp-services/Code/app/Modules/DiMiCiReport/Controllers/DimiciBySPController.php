<?php

/*
 * Filename: DimiciBySPController.php
 * Description: This file is used for downloading the DiMiCi Report
 * Author: Ebutor <info@ebutor.com>
 * Copyright: ebutor@2016
 * Version: v1.0
 * Created date: 16 February 2018
 * Modified date: 16 February 2018
 */

namespace App\Modules\DiMiCiReport\Controllers;

date_default_timezone_set('Asia/Kolkata');

use App\Http\Controllers\BaseController;
use Log;
use Session;
use Redirect;
use Excel;
use Illuminate\Http\Request;
use App\Modules\DiMiCiReport\Models\DimiciStoreProcedure;
use App\Modules\DiMiCiReport\Models\DimiciGrid;
use App\Central\Repositories\RoleRepo;

class DimiciBySPController extends BaseController {

    public function __construct() {
        try {
            parent::__construct();
            parent::Title('Ebutor - Di Mi Ci Report');
            $this->middleware(function ($request, $next) {
                if (!Session::has('userId')) {
                    Redirect::to('/login')->send();
                }
                $this->_modelStoreProcedure = new DimiciStoreProcedure();
                $this->_modelGrid = new DimiciGrid();
                $this->_roleRepo = new RoleRepo();

                $access = $this->_roleRepo->checkPermissionByFeatureCode('DIMICI001');
                if (!$access) {
                    Redirect::to('/')->send();
                    die();
                }
                return $next($request);
            });

        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }


    public function downloadAction(Request $inputParams) {
        try {
            $params = $inputParams->all();

            if (isset($params["cfc_check"])) {
                $cfc_to_by = $params["cfc_check"];
            } else {
                $cfc_to_by = "";
            }
//            Log::info("Request landed at DimiciBySPController controller : downloadAction method with input requests, From Date: " . $params["transac_date_from"] . " To Date: " . $params["transac_date_to"] . " Warehouse Id: " . $params["dc_name"] . " and CFC To Buy: " . $cfc_to_by);

            $params["userId"] = SESSION::get("userId");
            $params["userName"] = SESSION::get("userName");
          //  Log::info("Getting the session values, User Id: " . $params["userId"] . " and User Name: " . $params["userName"]);

            $flat_data = $this->_modelStoreProcedure->generateNewReport($params["dc_name"], $params["transac_date_from"], $params["transac_date_to"], $cfc_to_by);
            $flat_data = json_decode(json_encode($flat_data), true);

            if (!empty($flat_data)) {
                $fileName = "DimiCiReport_" . str_replace("-", "_", $params["transac_date_from"]) . "_to_ " . str_replace("-", "_", $params["transac_date_to"]);
              //  Log::info("Creating Excel File with filename: " . $fileName);

                $i = 0;
                $dc = $params["dc_name"];
               // Log::info("Total Rows: " . count($flat_data));
                Excel::create($fileName, function($excel) use($flat_data, $i, $dc) {
                    $excel->sheet("DiMiCiReport", function($sheet) use($flat_data, $i, $dc) {
                        $sheet->fromArray($flat_data);
                        $sheet->setWidth(array(
                            'B' => 20,
                            'C' => 40,
                            'D' => 70
                        ));
                        $sheet->prependRow(1, array("Warehouse Id:", $dc));
                        $sheet->freezePane('E3');
                    });
                })->download('xlsx');
            } else {
                $breadCrumbs = array('Home' => url('/'), 'DiMiCi Report' => url('/dimici/index'), 'Dashboard' => url('#'));
                parent::Breadcrumbs($breadCrumbs);
                $warehouses = $this->_modelGrid->getAllWareHouses();
                $notification = "Something went wrong! Please send the request again.";
                return view('DiMiCiReport::index')->with(['warehouses' => $warehouses, 'notification' => $notification]);
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }


}
