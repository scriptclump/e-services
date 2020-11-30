<?php
namespace App\Modules\Attendance\Models;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use \Log;
use \Session;
use Carbon\Carbon;
use DateTime;

class AttendanceMongoModel extends Eloquent {

    protected $connection = 'mongo';
    protected $table = 'attendance';
    protected $primaryKey = '_id';

        
    public function updateAttendance($data)
    {
        try
        {     
            //print_r($data); die;
            $status = false;
            $response = '';
            $message = '';
            //log::info($data);
            $exists = $this->where(['user_name'=>$data['user_name'], 'role_id'=>$data['role_id'], 'first_checkin_time'=>$data['first_checkin_time']])->pluck('_id')->all();
            $exists = json_decode(json_encode($exists),true);
            if(!empty($data))
            {
                if(!empty($exists))
                {
				   //log::info($exists);	
                   $update = $this->find($exists[0]);
				   //log::info($update);
                   $update->last_checkout_time = $data['last_checkout_time'];
				   $update->save();
				   exit;
                }
                
                if($this->insert($data))
                {
                    $status = true;
                    $message = "Data saved";
                }else{
                    $message = "Unable to save data";
                }
            }else{
                $message = 'Please provide data.';
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            $message = $ex->getMessage();
        }
        return json_encode(['status' => $status, 'message' => $message]);
    }
        
    public function getAttendance()
    {
        try
        {
            $notifications = $this->select('user_name', 'role_id', 'first_checkin_time', 'last_checkout_time');   
           
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
        }
        return $notifications;
    }
    
} 