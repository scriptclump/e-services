<?php

namespace App\Modules\Cpmanager\Controllers;
use Illuminate\Support\Facades\Input;
use Session;
//use Response;
use Log;
use DB;
use Illuminate\Http\Request;
use App\Modules\Cpmanager\Models\HrmsModel;
use App\Http\Controllers\BaseController;

class HrmsController extends BaseController{

	public function __construct()
	{
		$this->hrms = new HrmsModel();

	}
	public function hrmsDashboard()
	{
		$data = json_decode($_POST['data'],1);
		if(isset($data['user_id'])){
			$data = $this->hrms->getHrmsData($data['user_id']);
			if(count($data)>0){
				$data = json_decode($data);
				//$data = '"'.$data.'"';
				return array('status' => "success", 'message' => 'hrms dashboard', 'data' => $data);
			}else{
				return array('status' => "failed", 'message' => 'No data found', 'data' => []);

			}			
		}else{
			return array('status' => "failed", 'message' => 'Please send user id', 'data' => []);
		}
	}
}
?>