<?php

namespace App\Modules\EmployeeAttendance\Models;

use Illuminate\Database\Eloquent\Model;
use \Log;
use \Session;
use \DB;

class Attendance extends Model {
    //protected $connection = 'sqlSrv';
    
    public function getAttendance($empId)
    {
        try{
        	$yDate = date('Y-m-d 00:00:00',strtotime("-1 days"));

            $sql = DB::connection('sqlSrv')->table('dbo.Attendance')
            	->whereIn('EmpId', $empId)
            	->where('Date', $yDate)
            	->orderBy('Date', 'desc')
            	//->skip(0)->take(10)
            	->get()->all();
            return json_decode(json_encode($sql), true);
            
        }catch(\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
            return $ex->getMessage();
        }
    }

    /*public function getAllEmployees($orgId)
    {
        try{
            $sql = DB::connection('sqlSrv2')->table('dbo.Employees')
            	//->select('Emp_Id')
            	->where('Organization_Id', $orgId)
            	->pluck('Emp_Id')->all();
            return json_decode(json_encode($sql), true);
            
        }catch(\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
            return $ex->getMessage();
        }
    }*/
    
    public function getAllEmployees()
    {
        try{
            $sql = DB::table('employee')
                ->where('is_active', 1)
                ->pluck('emp_code')->all();
            return json_decode(json_encode($sql), true);
            
        }catch(\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
            return $ex->getMessage();
        }
    }

    public function putEmpAttendance($empData){
    	try{
            foreach($empData as $data){
            	DB::table('emp_attendance')->insert(
            		[
            			'emp_id'=>$data['EmpId'],
            			'date'=> date('Y-m-d H:i:s', strtotime($data['Date'])),
            			'in_time'=>$data['In_Time'],
            			'out_time'=>$data['Out_Time'],
            			'total_hrs'=>$data['TotalHours'],
            			'productive_hrs'=>$data['ProductHours']
            		]
            	);
            }
            return true;
            
        }catch(\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
            return $ex->getMessage();
        }
    }

    public function getEmployeeDetails($orgId)
    {
        try{
            $sql = DB::connection('sqlSrv2')->table('dbo.Employees')
            	->where('Organization_Id', $orgId)
            	//->where('Is_Active', 0)
            	//->where('Emp_Id', 400073)
            	->get()->all();
            return json_decode(json_encode($sql), true);
            
        }catch(\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
            return $ex->getMessage();
        }
    }

    public function insertEmployees($empArr)
    {
        try{
            $sql = DB::table('employee')
            	->insert($empArr);
            return json_decode(json_encode($sql), true);
            
        }catch(\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
            return $ex->getMessage();
        }
    }
    public function userChecknUpdate()
    {
        try{
            $sql = DB::table('employee')
            	->whereNotNull('emp_code')
            	->select('emp_id','emp_code')
            	->get()->all();
            $sql = json_decode(json_encode($sql), true);

            foreach ($sql as $value) {
            	if(!empty($value['emp_code'])){
            		$updateSql = DB::table('users')
            			->where('emp_code',$value['emp_code'])
            			->update(['emp_id'=>$value['emp_id']]);
            	}
            }
			            
        }catch(\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
            return $ex->getMessage();
        }
    }

    public function empRoleUpdate()
    {
        try{
            $sql = DB::table('users')
            	->join('user_roles as ur', 'ur.user_id', '=', 'users.user_id')
            	->whereNotNull('users.emp_code')
            	->where('users.emp_id','>',0)
            	->select("users.emp_id","users.emp_code","users.business_unit_id",'ur.role_id','ur.user_id',"users.department","users.designation","users.reporting_manager_id")
            	->get()->all();
            $sql = json_decode(json_encode($sql), true);

            //print_r($sql);
            echo "User Count: ".count($sql);
            foreach ($sql as $value) {
            	if(!empty($value['emp_id'])){
            		$updateSql = DB::table('employee')
            			->where('emp_id',$value['emp_id'])
            			->where('emp_code',$value['emp_code'])
            			->update([
            				'business_unit_id'=>$value['business_unit_id'],
            				'role_id'=>$value['role_id'],
            				'reporting_manager_id'=>$value['reporting_manager_id'],
            				'designation'=>$value['designation'],
            				'department'=>$value['department'],
            				'is_active'=>1,
            				'status'=>57155
            			]);
            	}
            }
			            
        }catch(\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
            return $ex->getMessage();
        }
    }

    public function updateLeaveQuota($empArr){
    	try{
    		$totalInserts = 0;
    		
            $currentEmp = DB::table('leave_master')
            	//->whereNotNull('emp_code')
            	->pluck('emp_id')->all();
            
            $currentEmp = json_decode(json_encode($currentEmp), true);
            echo "Current count:".count($currentEmp)."\n";
            

            $empLocal = DB::table('employee')
            	//->whereNotNull('emp_code')
            	->where('emp_code','>','')
            	->where('is_active',1)
            	->where('status',57155)
            	->whereNotIn('emp_id',$currentEmp)
            	->pluck('emp_id','emp_code')->all();

            echo "Emp count:".count($empLocal)."\n";
            //return $empLocal ;


            $leaveMaster= array();
    		foreach($empArr as $emp){
    			$leaves = DB::connection('sqlSrv2')->table('dbo.Employee_Leave_Quota')
            	->where('Emp_Id', $emp['emp_id'])
            	->get()->all();

            	$leaves = json_decode(json_encode($leaves), true);

            	//return $leaves;
            	
            	foreach($leaves as $data){
            		if(isset($empLocal[$emp['emp_code']])){
            			$temp = array();
	            		$temp['emp_id'] = $empLocal[$emp['emp_code']];
	            		$temp['leave_type'] = ($data['Leave_Type_Id']==2)?148002:148001;
	            		$temp['no_of_leaves'] = $data['Number_Of_Leaves'];

	            		$leaveMaster[] = $temp;
	            		$totalInserts++;
            		}
            		
            	}
    		}
    		$insertLeaves = DB::table('leave_master')
            			->insert($leaveMaster);
            
            return $totalInserts;
            
        }catch(\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
            return $ex->getMessage();
        }

    }

    public function updateLeaveHistory($empArr){
    	try{
    		$totalInserts = 0;

    		$currentEmp = DB::table('leave_master')
            	//->whereNotNull('emp_code')
            	->pluck('emp_id')->all();
            
            // $currentEmp = json_decode(json_encode($currentEmp), true);
            // echo "Current count:".count($currentEmp)."\n";


    		$empLocal = DB::table('employee')
            	//->whereNotNull('emp_code')
            	->where('emp_code','>','')
            	->where('is_active',1)
            	->where('status',57155)
            	//->whereNotIn('emp_id',$currentEmp)
            	->pluck('emp_id','emp_code')->all();

            echo 'Emp count:'.count($empLocal);
            $leaveHistory= array();
    		foreach($empArr as $emp){
    			$history = DB::connection('sqlSrv2')->table('dbo.Leave_Histories')
            	->where('Emp_Id', $emp['emp_id'])
            	->get()->all();

            	$history = json_decode(json_encode($history), true);


            	//return $history;
            	
            	foreach($history as $data){
            		if(isset($empLocal[$emp['emp_code']])){
            			if($data['Leave_Type_Id']==1)
            				$type = 148001;
            			elseif($data['Leave_Type_Id']==2)
            				$type = 148002;
            			elseif($data['Leave_Type_Id']==3)
            				$type = 148003;
            			elseif($data['Leave_Type_Id']==4)
            				$type = 148004;
            			elseif($data['Leave_Type_Id']==5)
            				$type = 148005;
            			elseif($data['Leave_Type_Id']==6)
            				$type = 148006;
            			elseif($data['Leave_Type_Id']==7)
            				$type = 148007;
            			elseif($data['Leave_Type_Id']==8)
            				$type = 148008;
            			elseif($data['Leave_Type_Id']==9)
            				$type = 148009;
            			else
            				$type = 00000;

            			if($data['Leave_Status_Id'] == 1)
            				$status = 57163;
            			elseif($data['Leave_Status_Id'] == 2)
            				$status = 57164;
            			elseif($data['Leave_Status_Id'] == 3)
            				$status = 57165;
            			elseif($data['Leave_Status_Id'] == 4)
            				$status = 57166;
            			else
            				$status = 57163;

	            		$temp = array();
	            		$temp['emp_id'] = $empLocal[$emp['emp_code']];
	            		$temp['leave_type'] = $type;

	            		$data['From_Date'] = date('Y-m-d', strtotime($data['From_Date']));
	            		$temp['from_date'] = $data['From_Date'];
	            		$data['To_Date'] = date('Y-m-d', strtotime($data['To_Date']));
	            		$temp['to_date'] = $data['To_Date'];
	            		$temp['no_of_days'] = $data['Number_Of_Days'];
	            		//$temp['reason'] = $data['Reason'];
	            		$temp['contact_number'] = (int)$data['Contact_Number'];
	            		$temp['status'] = $status;

	            		$leaveHistory[] = $temp;
	            		$totalInserts++;
            		}
            		
            	}
    		}
    		echo "Total Inserts:".$totalInserts."\n";

            $insertLeaves = DB::table('leave_history')
            			->insert($leaveHistory);
            
            return $totalInserts;
            
        }catch(\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
            return $ex->getMessage();
        }
    }

    public function dumpAttendance($allEmp){
    	$fDate = date('Y-m-d 00:00:00',strtotime("-45 days"));
    	$tDate = date('Y-m-d 00:00:00');

    	echo "From: ".$fDate." - "." To: ".$tDate."\n";
    	$count = 0;
    	foreach($allEmp as $emp){
    		//echo $emp['emp_id']."\n";
    		$sql = DB::connection('sqlSrv')->table('dbo.Attendance')
            	->where('EmpId', $emp['emp_code'])
            	->whereBetween('Date', [$fDate,$tDate])
            	->orderBy('Date', 'desc')
            	->get()->all();

            // $sql = DB::getQueryLog();
            // print_r(end($sql)); exit;

            $sql = json_decode(json_encode($sql), true);

            $empAttendance =  array();
            foreach($sql as $data){
            	$temp = array();
            	$temp['emp_id'] = $data['EmpId'];
    			$temp['date'] = date('Y-m-d H:i:s', strtotime($data['Date']));
    			$temp['in_time'] = $data['In_Time'];
    			$temp['out_time'] = $data['Out_Time'];
    			$temp['total_hrs'] = $data['TotalHours'];
    			$temp['productive_hrs'] = $data['ProductHours'];

    			$empAttendance[] = $temp;
            }

            DB::table('emp_attendance')->insert($empAttendance);
            $count++;
    	}
    	return $count;

    }

    public function authenticatToken($lpToken) {
        try {
            $ids = DB::table("employee as emp")->join("users as u", function ($join) {
                        $join->on("emp.emp_id", "=", "u.emp_id");
                    })->where("u.is_active", 1)->where("emp.is_active", 1)
                    ->where("u.lp_token", $lpToken)->orWhere("u.password_token", $lpToken)
                    ->get(["emp.emp_id", "emp.emp_group_id", "u.user_id", "emp.emp_code"]);
            return json_decode(json_encode($ids), true)[0];
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return $ex->getMessage();
        }
    }

    public function attendanceByDateEmpCode($dateArray, $empCode) {
        try {
             $attendance_history = DB::select(DB::raw("SELECT ca.emp_id,ca.ename,ca.db_date, IFNULL(ea.in_time, '00:00:00') AS in_time, IFNULL(ea.out_time, '00:00:00') AS out_time, 
                CASE WHEN (lv.leave_type = 148007) THEN DATE_FORMAT(DATE_ADD(CONCAT(DATE(ca.db_date),' ' ,ea.total_hrs), INTERVAL lv.hours HOUR),'%h:%i:%s') 
                when ca.db_date = ea.date THEN ea.total_hrs 
                WHEN ca.db_date = hl.holiday_date AND hl.holiday_type = 1 THEN hl.holiday_name 
                WHEN (lv.status = 57164 and ca.`db_date` BETWEEN lv.from_date AND lv.to_date) THEN getMastLookupValue(lv.leave_type) 
                WHEN ca.emp_group_id = 1 AND ca.day_name IN ('Saturday','Sunday') THEN 'Weekoff' 
                WHEN ca.emp_group_id = 2 AND ca.day_name IN ('Sunday') THEN 'Weekoff' 
                ELSE 'LOP' END AS 'total_hours', 
                CASE WHEN (lv.leave_type = 148007 AND lv.status = 57164) THEN DATE_FORMAT(DATE_ADD(CONCAT(DATE(ca.db_date),' ' ,ea.productive_hrs ), INTERVAL lv.hours HOUR),'%h:%i:%s') 
                WHEN ca.db_date = ea.date THEN ea.productive_hrs 
                WHEN ca.db_date = hl.holiday_date AND hl.holiday_type = 1  THEN hl.holiday_name
                WHEN (lv.status = 57164 AND ca.`db_date` BETWEEN lv.from_date AND lv.to_date AND ca.db_date = hl.holiday_date AND hl.holiday_type = 0 AND lv.leave_type = 148005) THEN hl.holiday_name                 
                WHEN (lv.status = 57164 and ca.`db_date` BETWEEN lv.from_date AND lv.to_date) THEN getMastLookupValue(lv.leave_type) 
                WHEN ca.emp_group_id = 1 AND ca.day_name IN ('Saturday','Sunday') THEN 'Weekoff' 
                WHEN ca.emp_group_id = 2 AND ca.day_name IN ('Sunday') THEN 'Weekoff' 
                ELSE 'LOP' END AS 'productive_hours'
                FROM  (SELECT ec.`db_date`,e.`emp_id`,e.`emp_code`,e.emp_group_id, CONCAT(IFNULL(e.firstname,''),'',IFNULL(e.lastname,'')) AS ename,ec.day_name,e.doj 
                FROM emp_calendar ec CROSS JOIN employee e ) ca 
                LEFT JOIN emp_attendance ea ON ea.`date` = ca.db_date AND ea.`emp_id` = ca.emp_code 
                LEFT JOIN holiday_list hl ON hl.holiday_date = ca.db_date AND ca.emp_group_id = hl.emp_group_id and hl.holiday_type=1
                LEFT JOIN leave_history lv ON lv.`emp_id` = ca.`emp_id` AND ca.db_date BETWEEN lv.from_date AND lv.to_date AND lv.status = 57164
                WHERE ca.db_date >= '" . $dateArray['from_date'] . "' AND ca.db_date <= '" . $dateArray['to_date'] . "' 
                AND ca.db_date >= ca.doj
                AND ca.emp_code IN (" . $empCode . ") 
                GROUP BY ca.emp_code,ca.db_date
                ORDER BY ca.db_date DESC;"));
            $attendance_history = json_decode(json_encode($attendance_history), true);
            return $attendance_history;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return $ex->getMessage();
        }
    }

    public function getSubordinatesByUserId($userId) {
        try {     
            $query = DB::selectFromWriteConnection(DB::raw("CALL getEmpSubordinates($userId)"));
            $query = json_decode(json_encode($query),true);
            $usersData = array();
            foreach ($query as $users){
                if(!empty($users['emp_id'])){
                    $usersData[] = $users['emp_id'];
                }    
            }
            if(!empty($usersData)){
                $impData = implode(",",$usersData);
                $query = "select `emp_id`, `emp_code`, CONCAT(firstname, IF(middlename IS NULL, '', CONCAT(' ', middlename)), ' ', lastname) AS full_name from `employee` where `emp_id` in (" . $impData .") and `is_active` = 1";
                $subOrdinates = DB::select(DB::raw($query)); 
                return $subOrdinates;
            } else {
                return $usersData;
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return $ex->getMessage();
        }
    }

    public function getDevices()
    {
        try{
            $sql = DB::connection('sqlSrv')->table('dbo.Devices')
                ->get()->all();
            return json_decode(json_encode($sql), true);
            
        }catch(\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
            return $ex->getMessage();
        }
    }

    public function putDevices($DeviceData){
        try{
            foreach($DeviceData as $data){
                $temp = array();
                $temp['device_id'] = $data['DeviceId'];
                $temp['device_fname'] = $data['DeviceFName'];
                $temp['device_sname'] = $data['DeviceSName'];
                $temp['device_direction'] = $data['DeviceDirection'];
                $temp['serial_no'] = $data['SerialNumber'];
                $temp['device_ip'] = $data['IpAddress'];

                $check = DB::table('emp_device_details')->where('device_id',$data['DeviceId'])->first();
                if(empty($check))
                    DB::table('emp_device_details')->insert($temp);
            }
            return true;
            
        }catch(\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
            return $ex->getMessage();
        }
    }

    public function getDeviceLogs($empCode)
    {
        try{
            //$month = date('m'); $year = date('Y');
            //echo $month."/".$year."\n";

            $start = date('Y-m-d 00:00:00',strtotime("-1 days"));
            $end = date('Y-m-d 23:59:59',strtotime("-1 days"));
            
            $month = date("n",strtotime($start));
            $year = date("Y",strtotime($start));
            echo $month."/".$year."\n";

            echo $start." - ".$end."\n";

            $sql = DB::connection('sqlSrv')->table('dbo.DeviceLogs_'.$month.'_'.$year)
                ->whereIn('UserId', $empCode)
                ->whereBetween('LogDate', [$start,$end])
                ->get()->all();
            return json_decode(json_encode($sql), true);
            
        }catch(\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
            return $ex->getMessage();
        }
    }

    public function putDeviceAttendance($empData){
        try{
            $attenLogs = array();
            foreach($empData as $data){
                $temp = array();
                $temp['log_date'] = date('Y-m-d H:i:s', strtotime($data['LogDate']));
                $temp['device_id'] = $data['DeviceId'];
                $temp['emp_code'] = $data['UserId'];
                $temp['device_direction'] = $data['Direction'];
                $attenLogs[] = $temp;
            }
            DB::table('emp_device_logs')->insert($attenLogs);
            return true;
            
        }catch(\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
            return $ex->getMessage();
        }
    }


}