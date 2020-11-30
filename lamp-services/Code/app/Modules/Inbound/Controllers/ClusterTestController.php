<?php
namespace App\Modules\Inbound\Controllers;

use View;
use Illuminate\Support\Facades\Session;
use Validator;
use Illuminate\Support\Facades\Input;
use URL;
use Log;
use Redirect;
use Title;
use App\Http\Controllers\BaseController;
use App\Modules\Inbound\Models\ApiNodeJs;
use Illuminate\Http\Request;

class ClusterTestController extends BaseController {
	public function __construct() {
		$this->_api_node_js = new ApiNodeJs();
	}

	public function callNodeAPI(){
		for($i=0; $i<10; $i++){
			//$url = "http://localhost:3001/getagnstatus?agnid=164";
			$url = "http://10.175.8.12:3003/getagnstatus?agnid=164";
	        $api_result = $this->_api_node_js->nodeJsApi($url, 'GET', $params='');
	        $api_result_array = json_decode($api_result, true);
	        echo "<pre>"; print_r($api_result_array);
	    }
	}
}