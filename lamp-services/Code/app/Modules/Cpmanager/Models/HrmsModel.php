<?php
namespace App\Modules\Cpmanager\Models;
use Illuminate\Database\Eloquent\Model;
use DB;
use Config;

class HrmsModel extends Model{

	
	public function getHrmsData($user_id)
	{
		$sql = "select e.emp_code FROM users u JOIN employee e ON u.emp_id = e.emp_id WHERE u.user_id=".$user_id;

		$sql = DB::select(DB::raw($sql));
		$sql = json_decode(json_encode($sql),1);
		if(count($sql)>0 && isset($sql[0]['emp_code'])){
			$dashboardData = DB::select(DB::raw("CALL get_employeeDynamicDashboard(".$sql[0]['emp_code'].")"));
			$dashboardData = $dashboardData[0]->Emp_Dashboard;
			return $dashboardData;
		}else{
			return  array();
		}
	}
} 
?>