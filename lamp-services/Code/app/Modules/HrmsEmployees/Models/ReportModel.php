<?php

namespace App\Modules\HrmsEmployees\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Mail;
use Session;

class ReportModel extends Model {

    protected $table = 'employee';
    protected $primaryKey = "emp_id";

    
        //get varience data
   public function getEmployeeVarienceReport($report){

            $activeEmployee = DB::table("employee")
                ->where("employment_type","=",$report['emp_type_report'])
                ->where("is_active", "=", $report['emp_type_status'])
                ->where('emp_code',"!=",null)
                ->where('emp_code',"!=",'')
                ->pluck('emp_code')->all();
            $implode_empcode = implode(',', $activeEmployee);


            if($implode_empcode == ""){
                return "0";
            }

        
        $varienceDetails = "select IFNULL(att.`productive_hrs`,0) AS productive_hrs,att.date,empl.`emp_id`,empl.doj,empl.emp_code,bu.bu_name,empl.exit_date,
                    getMastLookupValue(empl.department) AS 'Department', 
                    getMastLookupValue(empl.designation) AS 'designation',
                    CONCAT(IFNULL(empl.firstname,''),' ',IFNULL(empl.lastname,'')) AS 'Name',
                     getEmpBioPresentDays(empl.emp_code,'".$report['from_date_report']."','".$report['to_date_report']."') AS 'WorkingDays',
                     MAKETIME(getEmpBioPresentDays(empl.emp_code,'".$report['from_date_report']."','".$report['to_date_report']."')*9,00,00) AS THExpected ,
                     TIME(SEC_TO_TIME(SUM(TIME_TO_SEC(att.total_hrs))) + MAKETIME(SUM(IFNULL(lv.`hours`,0)),00,00))  AS 'ActualHrs', 
                     TIMEDIFF(MAKETIME(getEmpBioPresentDays(empl.emp_code,'".$report['from_date_report']."','".$report['to_date_report']."') *9,00,00), TIME(SEC_TO_TIME(SUM(TIME_TO_SEC(att.total_hrs))) + MAKETIME(SUM(IFNULL(lv.`hours`,0)),00,00)))  AS 'THDeviation',
                     MAKETIME(getEmpBioPresentDays(empl.emp_code,'".$report['from_date_report']."','".$report['to_date_report']."')*8,00,00) AS 'PHExpected',
                     IFNULL(getBioMissingDays(empl.emp_code,'".$report['from_date_report']."','".$report['to_date_report']."'),0) AS 'BiometricMissingDays',
                     TIME(SEC_TO_TIME(SUM(TIME_TO_SEC(att.productive_hrs))) + MAKETIME(SUM(IFNULL(lv.`hours`,0)),00,00)) AS 'PHActual',
                     TIMEDIFF(MAKETIME(getEmpBioPresentDays(empl.emp_code,'".$report['from_date_report']."','".$report['to_date_report']."')*8,00,00),TIME(SEC_TO_TIME(SUM(TIME_TO_SEC(att.productive_hrs))) + MAKETIME(SUM(IFNULL(lv.`hours`,0)),00,00))) AS 'PHDeviation'
                     FROM employee AS empl
                     LEFT JOIN business_units AS bu ON bu.bu_id = empl.business_unit_id
                     LEFT JOIN emp_attendance AS att ON att.`emp_id`=empl.`emp_code`
                     AND att.date BETWEEN '".$report['from_date_report']."' AND '".$report['to_date_report']."' 
             LEFT JOIN leave_history AS lv ON lv.emp_id = empl.emp_id AND lv.from_date = DATE(att.date) AND  lv.status = 57164
                     WHERE empl.emp_code IN (".$implode_empcode.")
                     GROUP BY empl.emp_id";

        $allData = DB::select(DB::raw($varienceDetails));
        return $allData;

    }

    // get date wise details for employee
    public function getDateWiseHistory($data,$ids){

        $datedetails = "select ca.emp_id,ca.db_date,ca.ename,                   
                        CASE 
                        WHEN (lv.status = 57164  AND ca.db_date  BETWEEN lv.`from_date` AND lv.to_date AND lv.leave_type=148007) THEN IFNULL(DATE_FORMAT(DATE_ADD(CONCAT(DATE(ca.db_date),' ' ,ea.productive_hrs), INTERVAL lv.hours HOUR),'%h:%i:%s'),MAKETIME(lv.hours,0,0))
                        WHEN ca.db_date = ea.date THEN IF(ea.productive_hrs = '','LOP',ea.productive_hrs)                         
                        WHEN ca.db_date = hl.holiday_date THEN hl.holiday_name                        
                        WHEN  (lv.status = 57164 and ca.`db_date` BETWEEN lv.`from_date` AND lv.to_date) THEN getMastLookupValue(lv.leave_type)
                        WHEN ca.emp_group_id = 1 AND ca.day_name IN ('Saturday','Sunday') THEN 'Week Off'
                        WHEN ca.emp_group_id = 2 AND ca.day_name IN ('Sunday') THEN 'Week Off'
                        ELSE 'LOP' END AS 'Data'                  
                        FROM  (SELECT ec.`db_date`,e.`emp_id`,e.`emp_code`,e.emp_group_id, CONCAT(IFNULL(e.firstname,''),'',IFNULL(e.lastname,'')) AS ename,ec.day_name
                          FROM emp_calendar ec CROSS JOIN employee e ) ca                  
                        LEFT JOIN emp_attendance ea ON ea.`date` = ca.db_date AND ea.`emp_id` = ca.emp_code                  
                        LEFT JOIN holiday_list hl ON hl.holiday_date = ca.db_date AND ca.emp_group_id = hl.emp_group_id and hl.holiday_type=1                  
                        LEFT JOIN leave_history lv ON lv.`emp_id` = ca.`emp_id` AND ca.db_date BETWEEN lv.from_date AND lv.to_date AND lv.status = 57164                 
                        WHERE ca.db_date >='".$data['from_date_report']."' AND ca.db_date <= '".$data['to_date_report']."'                  
                        AND ca.emp_code IN (".$ids.")                  
                        GROUP BY ca.db_date,ca.emp_id";

        $allData = DB::select(DB::raw($datedetails));
        return $allData;

    }

    //this is for employee attendance report
    public function getEmployeeAttendanceReport($report){

            $activeEmployee = DB::table("employee")
                ->where("employment_type","=",$report['emp_type_report'])
                ->where("is_active", "=", $report['emp_type_status'])
                ->where('emp_code',"!=",null)
                ->where('emp_code',"!=",'')
                ->pluck('emp_code')->all();
            $implode_empcode = implode(',', $activeEmployee);

             if($implode_empcode == ""){
                return "0";
            }

        $varienceDetails = "select CASE WHEN (lv.leave_type = 148007 AND lv.status = 57164) THEN DATE_FORMAT(DATE_ADD(CONCAT(DATE(lv.from_date),' ',att.productive_hrs), INTERVAL lv.hours HOUR),'%h:%i:%s')
                            ELSE att.`productive_hrs` END AS productive_hrs,att.date,empl.`emp_id`,empl.doj,empl.emp_code,bu.bu_name,
                            getMastLookupValue(empl.department) AS 'Department', getMastLookupValue(empl.designation) AS 'designation',
                            CONCAT(IFNULL(empl.firstname,''),' ',IFNULL(empl.lastname,'')) AS 'Name',
                            getEmpBioPresentDays(empl.emp_code,'".$report['from_date_report']."','".$report['to_date_report']."') AS 'WorkingDays',
                            getEmpBioPresentDays(empl.emp_code,'".$report['from_date_report']."','".$report['to_date_report']."')*9 AS THExpected ,
                            SUM(att.total_hrs) + SUM(IFNULL(lv.`hours`,0)) AS 'ActualHrs', 
                            getEmpBioPresentDays(empl.emp_code,'".$report['from_date_report']."','".$report['to_date_report']."')*9 - SUM(att.total_hrs)  AS 'THDeviation',
                            getEmpBioPresentDays(empl.emp_code,'".$report['from_date_report']."','".$report['to_date_report']."')*8 AS 'PHExpected',
                            SUM(att.productive_hrs) + SUM(IFNULL(lv.`hours`,0)) AS 'PHActual',
                            getEmpBioPresentDays(empl.emp_code,'".$report['from_date_report']."','".$report['to_date_report']."')*8 - SUM(att.productive_hrs) AS 'PHDeviation'
                            FROM employee AS empl
                            JOIN business_units as bu on bu.bu_id = empl.business_unit_id                       
                            LEFT JOIN emp_attendance AS att ON att.`emp_id`=empl.`emp_code` AND att.date BETWEEN '".$report['from_date_report']."' AND '".$report['to_date_report']."' 
                            LEFT JOIN leave_history AS lv ON lv.emp_id = empl.emp_id AND lv.from_date = DATE(att.date)
                            WHERE empl.emp_code IN (".$implode_empcode.")
                            GROUP BY empl.emp_id";


                            
        $allData = DB::select(DB::raw($varienceDetails));

        return $allData;
    }



    // this function for lop detils and more
    public function getLopDetailsReport($ids,$report){

        $data = "select e.`emp_code`,e.emp_id,e.exit_date,
                IFNULL(getEmpBioPresentDays(e.emp_code,'".$report['from_date_report']."','".$report['to_date_report']."'),0) AS 'BiometricPresentDays',
                IFNULL(getBioMissingDays(e.emp_code,'".$report['from_date_report']."','".$report['to_date_report']."'),0) AS 'BiometricMissingDays',
                IFNULL(getEmpLopDays(e.emp_code,'".$report['from_date_report']."','".$report['to_date_report']."'),0) AS 'LOP',
                IFNULL(SUM(CASE WHEN lh.`leave_type` = 148002 THEN no_of_days END),0) AS 'CL',
                IFNULL(SUM(CASE WHEN lh.`leave_type` = 148004 THEN no_of_days END),0) AS 'WFH',
                IFNULL(SUM(CASE WHEN lh.`leave_type` = 148006 THEN no_of_days END),0) AS 'OOD',
                IFNULL(SUM(CASE WHEN lh.`leave_type` = 148008 THEN no_of_days END),0) AS 'OnTravel',
                IFNULL(SUM(CASE WHEN lh.`leave_type` = 148009 THEN no_of_days END),0) AS 'MaternityLeave',
                IFNULL(SUM(CASE WHEN lh.`leave_type` = 148001 THEN no_of_days END),0) AS 'SickLeave'
                FROM employee e left join leave_history lh on lh.`emp_id` = e.`emp_id`
                AND lh.from_date >='".$report['from_date_report']."' AND lh.to_date <= '".$report['to_date_report']."' 
                where e.`emp_code` IN(".$ids.")
                group by e.emp_code";
            $allData = DB::select(DB::raw($data));
        return $allData;
    }

    public function getEmployeeTypes(){

        $activeEmployee = DB::table("master_lookup")
                ->where("mas_cat_id", "=", 152)
                ->where('is_active',"=",1)
                ->get()->all();
        return $activeEmployee;

    }

   
}
