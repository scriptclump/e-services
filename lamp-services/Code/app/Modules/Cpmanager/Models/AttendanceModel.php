<?php
namespace App\Modules\Cpmanager\Models;

use \DB;
use Log;
   
class AttendanceModel extends \Eloquent {          

    public function __construct()
    {
        // Master Lookup ID for Entering Attendance Manually
        define('MANUAL_ATTENDANCE',145001);
        // Master Lookup ID for Entering Attendance by BioMetric
        define('BIOMETRIC_ATTENDANCE',145002);
        // Master Lookup ID for Temp Vehicle Legal Entity Id
        define('Temp_Vehicle_LegalId',78012);
        // Master Lookup ID for Temp Vehicle Provider 
        define('Temp_Vehicle_Provider',78013);
        // Master Lookup ID for Contract Vehicle 
        define('Contract_Vehicle',156001);
        // Is Active set to True
        define('IS_ACTIVE',1);
    }

    public function getUserData($user_ids,$date){
        try{
            $date="'".$date."'";
            if(is_array ($user_ids)){
            $user_ids=implode(',', $user_ids);
            }

            $result=DB::SELECT('
                SELECT 
                    u.user_id,
                    GetUserName (u.user_id, 2) AS username,
                    getRolesNamesbyUserId (u.user_id) AS roles,
                    IFNULL(is_present,1) AS is_present 
                FROM `users` AS `u`
                LEFT JOIN `attendance` AS `a` ON `u`.`user_id` = `a`.`user_id`
                AND (a.attn_date BETWEEN '.$date.' AND '.$date.')
                WHERE FIND_IN_SET(u.user_id,"'.$user_ids.'") and u.is_active=1');
       
            return  $result;
        }
        catch(Exception $e) {
            return Array('status' => "failed", 'message' => $e->getMessage(), 'data' => []);
        }
    }

    public function saveAttendances($array){
        try{
            if(!empty($array->attendance_data)){
                foreach($array->attendance_data as $key => $value) 
                {
                    $chk=$this->checkAttendance($value->user_id,$array->date);
                    if($chk==0){
                        DB::table('attendance')
                        ->insert(['user_id' => $value->user_id,
                           'source' => '145001',
                           'is_present'=>$value->is_present,
                           'attn_date'=>$array->date,
                           'created_by'=>$array->user_id,
                           'created_at' => date("Y-m-d H:i:s")
                           ]); 
                    }else{
                        DB::table('attendance')
                       ->where("user_id","=", $value->user_id)
                       ->whereDate("attn_date","=", $array->date)
                       ->update(array(
                        'is_present' => $value->is_present,
                        'updated_at' => date("Y-m-d H:i:s")));
                    }
                }
            }
        return true;
        }
        catch(Exception $e) {
            return Array('status' => "failed", 'message' => $e->getMessage(), 'data' => []);
        }
    }

    public function checkAttendance($user_id,$date) {
        try{
            $result=DB::table('attendance')
                ->select(DB::raw("count(user_id) as user_count"))
                ->where('user_id',$user_id)
                ->whereDate('attn_date','=',$date)
                ->first();

            return  $result->user_count;
        }
        catch(Exception $e) {
            return Array('status' => "failed", 'message' => $e->getMessage(), 'data' => []);
        }
    }

    /**
    * [getVehicleIdsByUserIdModel    description]
    * @param [array] [$data]
    * @return [bool] [TRUE/FALSE]
    */
    public function getVehicleIdsByUserIdModel($user_id)
    {
        $query = '
            SELECT 
                le.business_legal_name AS vehicleName,
                vehicle.reg_no AS vehicleno,
                vehicle.vehicle_id AS vehicle_id,
                IFNULL(ve.is_present,0) AS is_present,
                IFNULL(ve.reporting_time,"") AS reporting_time
            FROM
                legal_entities AS le
            LEFT JOIN vehicle ON le.legal_entity_id = vehicle.legal_entity_id     
            LEFT JOIN legalentity_warehouses AS lw ON lw.le_wh_id = vehicle.hub_id 
            LEFT JOIN users ON users.legal_entity_id = lw.legal_entity_id
            LEFT JOIN (
                SELECT va.is_present,va.reporting_time,va.vehicle_id
                FROM vehicle_attendance AS va
                WHERE va.attn_date = ?
                ) AS ve ON ve.vehicle_id = vehicle.vehicle_id
            WHERE
                users.user_id = ?
                AND vehicle.is_active = ?
                AND vehicle.vehicle_type = ? 
                AND vehicle.vehicle_id NOT IN (SELECT repl.`replace_with` FROM vehicle AS repl 
                JOIN vehicle_attendance AS rva ON repl.`vehicle_id`=rva.`vehicle_id`
                WHERE repl.replace_with = vehicle.vehicle_id AND rva.attn_date = ? )';

        $today = date("Y-m-d");
        $result = DB::SELECT($query,[$today,$user_id,IS_ACTIVE,Contract_Vehicle,$today]);

        return $result;
    }

    /**
    * [saveVehicleAttendances    description]
    * @param [array] [$data]
    * @return [bool] [TRUE/FALSE]
    */
    public function saveVehicleAttendances($data)
    {
        if(!isset($data) or empty($data))
            return FALSE;

        $userId = $data['user_id'];
        $date = $data['date'];

        if(isset($data['attendance_data']))
        foreach ($data['attendance_data'] as $record) {

            // Data Validations
            if(!isset($record['vehicle_id'])) continue;
            if(!isset($record['vehicle_reg_no'])) continue;
            if(!isset($record['is_present'])) continue;
            if(!isset($record['reporting_time'])) continue;

            $vehicle_id = $record['vehicle_id'];
            $vehicle_reg_no = $record['vehicle_reg_no'];
            $is_present = $record['is_present'];
            $reporting_time = $record['reporting_time'];

            if($this->checkVehicleAttendance($vehicle_id,$date)){
                $query = '
                    INSERT INTO 
                        vehicle_attendance (attn_date,vehicle_id,vehicle_reg_no,is_present,reporting_time,source,created_by)
                    VALUES (?,?,?,?,?,?,?)';

                DB::INSERT($query,[
                    $date,
                    $vehicle_id,
                    $vehicle_reg_no,
                    $is_present,
                    $reporting_time,
                    MANUAL_ATTENDANCE,
                    $userId
                ]);

            }else{

                // Update Laravel Query                
                DB::table('vehicle_attendance')
                   ->where("vehicle_id","=", $vehicle_id)
                   ->whereDate("attn_date","=", $date)
                   ->update(array(
                    'is_present' => $is_present,
                    'reporting_time' => $reporting_time,
                    'updated_at' => date("Y-m-d H:i:s")));
                
            }
        }
        return TRUE;
    }

    /**
    * [checkVehicleAttendance    description]
    * @param [integer] [$vehicle_id]
    * @param [date] [$attn_date]
    * @return [bool] [TRUE or FALSE]
    */
    public function checkVehicleAttendance($vehicle_id,$attn_date)
    {
        $query = '
            SELECT COUNT(vehicle_id) AS count
            FROM vehicle_attendance
            WHERE vehicle_id = ? AND attn_date = ?
            ';

        $result = DB::SELECT($query,[$vehicle_id,$attn_date]);
        $count = isset($result[0]->count)?$result[0]->count:-1;
        
        if(intval($count) > 0)  return FALSE;

        return TRUE;
    }

    /**
    * [saveTemporaryVehicleData    description]
    * @param [array] [$data]
    * @return [array] [status, message]
    */
    public function saveTemporaryVehicleData($data)
    {
        // Intially we need to check the Data, whether the recored is inserted or not
        $output = $this->checkTemporaryVehicleData($data);
        if(!$output)
            return ['status' => "failed", 'message' => "Data is already Inserted"];
        $checkContrack = $this->checkContractVehicleData($data);
        if(!$checkContrack)
            return ['status' => "failed", 'message' => "Vehicle already registered as contract base"];

        // Method to Get Master Lookup Value
        $veh_le_id = $this->getMasterLookupValue(Temp_Vehicle_LegalId);
        $veh_provider = $this->getMasterLookupValue(Temp_Vehicle_Provider);
        $now = date('Y-m-d H:i:s');

        // Wrote Laravel Query Builder
        // to Get the Vehicle Id inserted 
        $vehicle_id = DB::table('vehicle')
            ->insertGetId([
                'legal_entity_id' => $veh_le_id,
                'hub_id' => $data['hub_id'],
                'reg_no' => $data['vehicle_reg_no'],
                'veh_provider' => $veh_provider,
                'replace_with' => $data['replace_with'],
                'vehicle_type' => $data['vehicle_type'],
                'is_active' => IS_ACTIVE,
                'created_by' => $data['user_id'],
                'approved_at' => $now,
                'created_at' => $now
            ]);

        // Creating Array to Insert a Record in Vehicle Attendance Table
        $attendanceArray['vehicle_id'] = $vehicle_id;
        $attendanceArray['vehicle_reg_no'] = $data['vehicle_reg_no'];
        $attendanceArray['is_present'] = 1;
        $attendanceArray['reporting_time'] = date('h:i:s');

        $insertArray['user_id']=$data['user_id'];
        $insertArray['date']=date('Y-m-d');
        $insertArray['attendance_data']=[$attendanceArray];

        $this->saveVehicleAttendances($insertArray);

        return ['status' => "success", 'message' => "Temporary Vehicle has been Inserted"];
    }
/*
 *  Modified By :Raju.A
 *  Modified Date: 21th December 2017
 */
    public function checkTemporaryVehicleData($data)
    {
        $query = '
            SELECT COUNT(vehicle_id) AS count
            FROM vehicle
            WHERE
                hub_id = ?
                AND UPPER(REPLACE(`reg_no` , " ",""))  = ?
                AND DATE(created_at) = ?
            ';
        $dataArr=[$data['hub_id'], strtoupper(str_replace(' ', '', $data['vehicle_reg_no'])),date('Y-m-d')];
        $result = DB::SELECT($query,$dataArr);
        $count = isset($result[0]->count)?$result[0]->count:-1;
        if(intval($count) > 0)
            return FALSE;
        return TRUE;
    }
    /**
    * [checkContractVehicleData    description]
    * @param [array]
    * @return [bool] [TRUE or FALSE]
     * Author: Code Optimization [Raju.A]
     * Copyright: ebutor 2017
     * Created Date: 21th December 2017 
    */
    public function checkContractVehicleData($data)
    {
        $query = '
            SELECT COUNT(vehicle_id) AS count
            FROM vehicle
            WHERE
                UPPER(REPLACE(`reg_no` , " ","")) = ?
                AND vehicle_type = ?
            ';
        $result = DB::SELECT($query,[strtoupper(str_replace(' ', '', $data['vehicle_reg_no'])), 156001]);
        $count = isset($result[0]->count)?$result[0]->count:-1;        
        if(intval($count) > 0)
            return FALSE;
        return TRUE;
    }

    public function getMasterLookupValue($value)
    {
        // To get Temp Vehicle Legal Id and Provider Id
        $query = '
            SELECT value,description
            FROM master_lookup
            WHERE value IN (?)';
        
        $result = DB::SELECT($query,[$value]);

        if($result[0]->value == $value)
            return $result[0]->description;
        return '';
    }
}