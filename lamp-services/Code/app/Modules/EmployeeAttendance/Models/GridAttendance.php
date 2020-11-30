<?php

namespace App\Modules\EmployeeAttendance\Models;

use Illuminate\Database\Eloquent\Model;
use \Log;
use \Session;
use \DB;

class GridAttendance extends Model {
    //protected $connection = 'sqlSrv';
    
    public function GridAttendanceByDateEmpCode($dateArray, $empCode,$page,$pageSize,$orderBy = "") {
        try {
            $query = "SELECT ca.emp_id,ca.ename,ca.db_date, IFNULL(ea.in_time, '00:00:00') AS in_time, IFNULL(ea.out_time, '00:00:00') AS out_time, 
                CASE WHEN (lv.leave_type = 148007) THEN DATE_FORMAT(DATE_ADD(CONCAT(DATE(ca.db_date),' ' ,ea.total_hrs), INTERVAL lv.hours HOUR),'%h:%i:%s')
                WHEN ca.db_date = ea.date THEN ea.total_hrs 
                WHEN ca.db_date = hl.holiday_date AND hl.holiday_type = 1 THEN hl.holiday_name 
                WHEN (lv.status = 57164 and ca.`db_date` BETWEEN lv.from_date AND lv.to_date) THEN getMastLookupValue(lv.leave_type) 
                WHEN ca.emp_group_id = 1 AND ca.day_name IN ('Saturday','Sunday') THEN 'Weekoff' 
                WHEN ca.emp_group_id = 2 AND ca.day_name IN ('Sunday') THEN 'Weekoff' 
                ELSE 'LOP' END AS 'total_hours', 
                CASE WHEN (lv.leave_type = 148007 AND lv.status = 57164) THEN DATE_FORMAT(DATE_ADD(CONCAT(DATE(ca.db_date),' ' ,ea.productive_hrs ), INTERVAL lv.hours HOUR),'%h:%i:%s')
                WHEN ca.db_date = ea.date THEN ea.productive_hrs 
                WHEN ca.db_date = hl.holiday_date AND hl.holiday_type = 1 THEN hl.holiday_name 
                WHEN (lv.status = 57164 AND ca.`db_date` BETWEEN lv.from_date AND lv.to_date AND ca.db_date = hl.holiday_date AND hl.holiday_type = 0 AND lv.leave_type = 148005) THEN hl.holiday_name
                WHEN (lv.status = 57164 and ca.`db_date` BETWEEN lv.from_date AND lv.to_date) THEN getMastLookupValue(lv.leave_type) 
                WHEN ca.emp_group_id = 1 AND ca.day_name IN ('Saturday','Sunday') THEN 'Weekoff' 
                WHEN ca.emp_group_id = 2 AND ca.day_name IN ('Sunday') THEN 'Weekoff'
                ELSE 'LOP' END AS 'productive_hours'
                FROM (SELECT ec.`db_date`,e.`emp_id`,e.`emp_code`,e.emp_group_id, CONCAT(IFNULL(e.firstname,''),'',IFNULL(e.lastname,'')) AS ename,ec.day_name, e.doj
                FROM emp_calendar ec CROSS JOIN employee e ) ca 
                LEFT JOIN emp_attendance ea ON ea.`date` = ca.db_date AND ea.`emp_id` = ca.emp_code 
                LEFT JOIN holiday_list hl ON hl.holiday_date = ca.db_date AND ca.emp_group_id = hl.emp_group_id  and hl.holiday_type=1
                LEFT JOIN leave_history lv ON lv.`emp_id` = ca.`emp_id` AND ca.db_date BETWEEN lv.from_date AND lv.to_date AND lv.status = 57164
                WHERE ca.db_date >= '" . $dateArray['from_date'] . "' AND ca.db_date <= '" . $dateArray['to_date'] . "' 
                AND ca.db_date >= ca.doj AND ca.emp_code IN (" . $empCode . ") 
                GROUP BY ca.emp_code,ca.db_date ";
            if (!empty($orderBy)) {
                $orderClause = explode(" ", $orderBy);
                $query .= "ORDER BY " . $orderClause[0] . " " .$orderClause[1];
            } else {
                $query .= "ORDER BY ca.db_date DESC";
            }
            $query .= " limit ".$page.",".$pageSize.";";
            $attendance_history = DB::select(DB::raw($query));
            $attendance_history = json_decode(json_encode($attendance_history), true);
            return $attendance_history;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return $ex->getMessage();
        }
    }
}