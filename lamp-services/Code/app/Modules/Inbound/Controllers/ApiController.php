<?php

namespace App\Modules\Inbound\Controllers;

/*
  Filename : ApiController.php
  Author : Vijaya Bhaskar Chenna
  CreateData : 25-May-2016
  Desc : Controller for API call to Node JS
 */
use \Session;
use App\Http\Controllers\Controller;
use App\Modules\Inbound\Models\ApiNodeJs;
use App\Modules\Inbound\Models\InboundWmsResponse;

class ApiController extends Controller {

    public function __construct() {
        $this->_api_node_js = new ApiNodeJs();
        $this->_inbound_wms_response = new InboundWmsResponse();
    }

    public function getAgnRequestStatusNodeJs() {
        $result_set = $this->_inbound_wms_response->responseByStatus('In Queue');
        $api_result_arr = array();
        foreach ($result_set as $each_result_set) {
            $query_string = $each_result_set['request_id'];
            
            $url = "http://localhost:3000/inbound/agnreqstatus/".$query_string;
            $api_result = $this->_api_node_js->nodeJsApi($url, 'GET');
            $api_result_arr= json_decode($api_result, true);
            
            $update_result = $this->_inbound_wms_response->updateStatus($api_result_arr['request_id'], $api_result_arr['status']);
        }
        return $update_result;
    }

}
