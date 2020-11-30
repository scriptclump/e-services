<?php

namespace App\Modules\EmployeeAttendance\Controllers;
use App\Http\Controllers\BaseController;
use Session;
use View;
use Illuminate\Http\Request;
use File;
use Redirect;
use Excel;
use DB;
use Carbon\Carbon;
class VehicleController extends BaseController {

   
    public function vehicleAttReport()
    {
        $getVehicleList = DB::table('vehicle')
                            ->select('vehicle_id','reg_no')
                            ->wherenotnull('reg_no')
                            ->get()->all();
        $getVehicleList = json_decode(json_encode($getVehicleList), true);
        return View::make('EmployeeAttendance::vehicleAttRerport',["vehicle_list"=>$getVehicleList]);
    }
    public function vehicleattdownload(Request $request){
       
        $to_date = $request->get('to_date');
        $from_date = $request->get('from_date'); 
        $vehicle_list = (empty($request->get('vehicle_list')))?'NULL':$request->get('vehicle_list');
        
        $getData = DB::select("call getVehicleReport(NULL,NULL,".$vehicle_list.",'".$from_date."','".$to_date."')");
        $getData = json_decode(json_encode($getData), true);

        
        $prodHrs = $getData;
        $vehicle_array = array();
       foreach ($getData as $value) {
            $vehicle_array[$value['Vehicle ID']]['Registration_Number'] = $value['Registration_Number'];
            $vehicle_array[$value['Vehicle ID']]['Hub_DC'] = $value['Hub_DC'];
            $vehicle_array[$value['Vehicle ID']]['Vehicle_Type'] = $value['Vehicle_Type'];
            $vehicle_array[$value['Vehicle ID']]['Mobile_No'] = $value['Mobile_No'];
            $vehicle_array[$value['Vehicle ID']]['Vehicle_Provider'] = $value['Vehicle_Provider'];
            $vehicle_array[$value['Vehicle ID']]['Vehicle_Charges'] = $value['Vehicle_Charges'];
            if(isset($vehicle_array[$value['Vehicle ID']]['Presented Days']))
                $vehicle_array[$value['Vehicle ID']]['Presented Days'] += ($value['P_A'] == 'P')?1:0;
            else
                $vehicle_array[$value['Vehicle ID']]['Presented Days'] = ($value['P_A'] == 'P')?1:0;
            $vehicle_array[$value['Vehicle ID']][$value['Date']]= $value['Reported_Time'];
            $vehicle_array[$value['Vehicle ID']][$value['Date']]= $value['P_A'];
        }      

        $date = array_unique(array_column($getData,'Date'));
        $headers1 = array('Vehicle Registration No','Hub/DC','Type','Mobile No','Vehicle Provider','Vehicle Charges','Presented Days');
        $headers2 = $date;
        $headers = array_merge($headers1,$headers2);
        $mytime = Carbon::now();
        Excel::create('Vehicle Attendance Report'.$mytime->toDateTimeString(), function($excel) use($headers,$vehicle_array) 
        {
           $excel->sheet("Attendance Report", function($sheet) use($headers, $vehicle_array)
            {
                $sheet->loadView('EmployeeAttendance::Vehiclereport', array('headers' => $headers,'data' => $vehicle_array)); 

         });
        })->export('xlsx');
    }    
}
