<?php
namespace App\Modules\HrmsEmployees\Controllers;
use View;
use Session;
use Validator;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\BaseController;
use URL;
use Log;
use Response;
use Illuminate\Http\Request;
use Redirect;
use \App\Modules\HrmsEmployees\Models\ReportModel;
use Carbon\Carbon;
use Excel;
use PHPExcel_Cell;

Class ReportsController extends BaseController {
    
    
    public function __construct() {
      
        $this->reportModel = new ReportModel();
    }
    public function reportIndex()
    {
        $value = "there are values";
        $monthData = ["1"=>"January","2"=>"February","3"=>"March","4"=>"April","5"=>"May","6"=>"June","7"=>"July","8"=>"August","9"=>"September","10"=>"October","11"=>"November","12"=>"December"];
        $employee_type = $this->reportModel->getEmployeeTypes();
        return View::make('HrmsEmployees::varienceReports')->with(["checkVariable" => $value,"months" => $monthData,"employee_type"=>$employee_type]);
    }


    public function checkTheReportNameBySelection(Request $request){
        $report = $request->input();
        $monthData = ["1"=>"January","2"=>"February","3"=>"March","4"=>"April","5"=>"May","6"=>"June","7"=>"July","8"=>"August","9"=>"September","10"=>"October","11"=>"November","12"=>"December"];

        //get the  employee type
        $employee_type = $this->reportModel->getEmployeeTypes();
        
        if($report['select_report'] == 'variencereport'){
            $res_value = $this->downloadVarienceReport($report);
            return View::make('HrmsEmployees::varienceReports')->with(["checkVariable" => $res_value,"months"=>$monthData,"employee_type"=>$employee_type]);
        }else{
            $res_value = $this->downloadattendancereport($report);
            return View::make('HrmsEmployees::varienceReports')->with(["checkVariable" => $res_value,"months"=>$monthData,"employee_type"=>$employee_type]);
        }

    }


     // download varience report
   public function downloadVarienceReport($report){

        // get empdata with productive hours
        $data = $this->reportModel->getEmployeeVarienceReport($report);

        $arraycount = count($data);

        if ($arraycount == 0  || $data == 0) {
            $value = "no values";
            return $value;
        }

        $ids = json_decode(json_encode($data), true);
        $last_names = array_column($ids, 'emp_code');
        $mulempcode =  implode( ',', array_values($last_names));

        $data = json_decode(json_encode($data), true);

        $emp_array =array();
        foreach ($data as $value) {
            $emp_array[$value['emp_id']]['emp_code'] = $value['emp_code'];
            $emp_array[$value['emp_id']]['name'] = $value['Name'];
            $emp_array[$value['emp_id']]['designation'] = $value['designation'];
            $emp_array[$value['emp_id']]['department'] = $value['Department'];
            $emp_array[$value['emp_id']]['bu_name'] = $value['bu_name'];
            $emp_array[$value['emp_id']]['doj'] = $value['doj'];
        }

        $date_details = $this->reportModel->getDateWiseHistory($report,$mulempcode);
        $prodHrs = json_decode(json_encode($date_details),true);
        foreach ($prodHrs as $prodHrsValue)
        {   
            $emp_array[$prodHrsValue['emp_id']][$prodHrsValue['db_date']]= $prodHrsValue['Data'];
        }

        foreach ($data as $value)
        {   
            $emp_array[$value['emp_id']]['WorkingDays'] = $value['WorkingDays'];
            $emp_array[$value['emp_id']]['THExpected'] = $value['THExpected'];
            $emp_array[$value['emp_id']]['ActualHrs'] = $value['ActualHrs'];
            $emp_array[$value['emp_id']]['THDeviation'] = $value['THDeviation'];
            $emp_array[$value['emp_id']]['THExpected'] = $value['THExpected'];
            $emp_array[$value['emp_id']]['PHExpected'] = $value['PHExpected'];
            $emp_array[$value['emp_id']]['PHActual'] = $value['PHActual'];
            $emp_array[$value['emp_id']]['PHDeviation'] = $value['PHDeviation'];
            $emp_array[$value['emp_id']]['BiometricMissingDays'] = $value['BiometricMissingDays'];
            $emp_array[$value['emp_id']]['exit_date'] = $value['exit_date'];
            
        }
        

        $date = array_unique(array_column($prodHrs,'db_date'));
        $coun =  count($date);
        $count= $coun+6;


        $headers_line = array('Productive Hours Devation Report');
        $header_line1 = array();
        $header_line2 = array('Working Days');
        $header_line3 = array('Total Hours','Productive Hours','','','','','','','');
        $headers_line_one = array_merge($headers_line,$header_line1,$header_line2,$header_line3);
        
        $headers1 = array('Emp Code','Name', 'Designation', 'Department', 'Location','Date Of Joining');
        $headers2 = $date;
        $headers3 = array('Working Days');
        $headers4 = array('Expected','Actual','Deviation','Expected','Actual','Deviation','BiometricMissing','Date Of Leaving','Justification','Manager Approval','Remarks','HR Action');
        $headers = array_merge($headers1,$headers2,$headers3,$headers4);

        $mytime = Carbon::now();

        Excel::create('Variance Report Sheet-'.$mytime->toDateTimeString(), function($excel) use($headers,$headers_line_one, $emp_array,$count) 
            {
                $excel->sheet("Variance Report", function($sheet) use($headers,$headers_line_one, $emp_array,$count)
                {
                    $sheet->loadView('HrmsEmployees::downloadVarienceTemplate', array('headers' => $headers,'headers_line_one' => $headers_line_one, 'data' => $emp_array,'count'=>$count)); 
                });

            })->export('xlsx');
    }

    // download attendance report
     public function downloadattendancereport($report){
        
        // get empdata with productive hours
        $data = $this->reportModel->getEmployeeAttendanceReport($report);

        $arraycount = count($data);

        if ($arraycount == 0 || $data == 0) {
            $value = "no values";
            return $value;
        }


        $ids = json_decode(json_encode($data), true);
        $last_names = array_column($ids, 'emp_code');
        $multiemp =  implode( ',', array_values($last_names));
        $data = json_decode(json_encode($data), true);    
        $emp_array =array();
        foreach ($data as $value) {
            //echo "<pre/>";print_r($value);exit;
            $emp_array[$value['emp_id']]['Emp Code'] = $value['emp_code'];
            $emp_array[$value['emp_id']]['Name'] = $value['Name'];
            $emp_array[$value['emp_id']]['Designation'] = $value['designation'];
            $emp_array[$value['emp_id']]['Department'] = $value['Department'];
            $emp_array[$value['emp_id']]['Location'] = $value['bu_name'];
            $emp_array[$value['emp_id']]['Date Of Joining'] = $value['doj'];
        }

        $date_details = $this->reportModel->getDateWiseHistory($report,$multiemp);
        $prodHrs = json_decode(json_encode($date_details),true);
        foreach ($prodHrs as $prodHrsValue)
        {   
            $emp_array[$prodHrsValue['emp_id']][$prodHrsValue['db_date']]= $prodHrsValue['Data'];
        }


        $lopdetails = $this->reportModel->getLopDetailsReport($multiemp,$report);
        $details = json_decode(json_encode($lopdetails), true);

        foreach ($details as $data)
        {   
            //echo "<pre/>";print_r($data);exit;
            $emp_array[$data['emp_id']]['Biometric Present Days'] = '';
            $emp_array[$data['emp_id']]['LOP'] = '';
            $emp_array[$data['emp_id']]['CL'] = '';
            $emp_array[$data['emp_id']]['WFH'] = '';
            $emp_array[$data['emp_id']]['OOD'] = '';
            $emp_array[$data['emp_id']]['On Travel'] = '';
            $emp_array[$data['emp_id']]['Maternity Leave'] = '';
            $emp_array[$data['emp_id']]['Sick Leave'] = '';
            $emp_array[$data['emp_id']]['Present'] = '';
            $emp_array[$data['emp_id']]['Total Days Check'] = '';
            $emp_array[$data['emp_id']]['Remarks'] = '';
            $emp_array[$data['emp_id']]['Maximum Days Off To Work'] = '';
            $emp_array[$data['emp_id']]['Biometric Missing Days'] = $data['BiometricMissingDays'];
            $emp_array[$data['emp_id']]['Date Of Leaving'] = $data['exit_date'];
        }
        

        $date = array_unique(array_column($prodHrs,'db_date'));
        $coun =  count($date);
        $count= $coun+6;


        /*$headers_line = array('Attendance Report');
        $header_line1 = array();
        $header_line2 = array('BioMetric Present Days','LOP','CL','WFH','OOD','Comp Off','On Travel','Maternity Leave','Sick Leave','Present','Total Days Check','Remarks','Maximum Days Off To Work');
        $headers_line_one = array_merge($headers_line,$header_line1,$header_line2);*/
        
        $headers1 = array( 'Emp Code','Name', 'Designation', 'Department','Location', 'Date Of Joining');
        $headers2 = $date;
        $headers3 = array('BioMetric Present Days','LOP','CL','WFH','OOD','On Travel','Maternity Leave','Sick Leave','BiometricMissingDays','Date Of Leaving','Present','Total Days Check','Remarks','Maximum Days Off To Work');
        $headers = array_merge($headers1,$headers2,$headers3);

        $mytime = Carbon::now();

        $position = count($headers1)+count($headers2);
        $totRows = count($emp_array);

        // echo "<pre>";
        // //print_r($emp_array);
        // $pos1 = PHPExcel_Cell::stringFromColumnIndex($position);
        // echo count($headers1)."+".count($headers2)."<br>";
        // echo $pos1.'1:'.$pos1.'5<br>'; 
        // echo "Date Start:".PHPExcel_Cell::stringFromColumnIndex(count($headers1)).'<br>';
        // echo "Date End:".PHPExcel_Cell::stringFromColumnIndex($position-1).'<br>';
        // echo  exit;

        /*Excel::create('Attendance Report Sheet-'.$mytime->toDateTimeString(), function($excel) use($headers,$headers_line_one, $emp_array,$count,$position,$totRows) 
            {
                $excel->sheet("Attendance Report", function($sheet) use($headers,$headers_line_one, $emp_array,$count,$position,$totRows)
                {
                    $sheet->loadView('HrmsEmployees::downloadAttendanceTemplate', array('headers' => $headers,'headers_line_one' => $headers_line_one, 'data' => $emp_array,'count'=>$count)); 
                    $sheet->freezePane('G3');
                    $pos1 = PHPExcel_Cell::stringFromColumnIndex($position+1);
                    $pos1_Range =  $pos1.'1:'.$pos1.'5';
                    $sheet->setCellValue('L3','=COUNTIF(G3:K3,"LOP")');
                    $sheet->cell('L3', function($cells) {
                        $cells->setValue('=COUNTIF(G3:K3, "LOP")');
                    });
                });

            })->export('xlsx');*/

        Excel::create('Attendance Report Sheet-'.$mytime->toDateTimeString(), function($excel) use($emp_array,$headers1,$position,$totRows) 
        {
            //$excel->setPreCalculateFormulas(true);
            $excel->sheet("Attendance Report", function($sheet) use($emp_array,$headers1,$position,$totRows)
            {
                $sheet->fromArray($emp_array);
                $sheet->prependRow(1, array("Attendance Report"));
                //$sheet->freezePane('G3');
                $dtStart = PHPExcel_Cell::stringFromColumnIndex(count($headers1));
                $dtEnd = PHPExcel_Cell::stringFromColumnIndex($position-1);

                //$pos1 = PHPExcel_Cell::stringFromColumnIndex($position+1);
                //$pos1_Range =  $pos1.'1:'.$pos1.'5';
                for($i=0; $i<$totRows;$i++){
                    $j = $i+3;
                    $dtRange = $dtStart.$j.':'.$dtEnd.$j;

                    $bioP = PHPExcel_Cell::stringFromColumnIndex($position).$j;
                    $lop = PHPExcel_Cell::stringFromColumnIndex($position+1).$j;
                    $cl = PHPExcel_Cell::stringFromColumnIndex($position+2).$j;
                    $wfh = PHPExcel_Cell::stringFromColumnIndex($position+3).$j;
                    $ood = PHPExcel_Cell::stringFromColumnIndex($position+4).$j;
                    $ot = PHPExcel_Cell::stringFromColumnIndex($position+5).$j;
                    $ml = PHPExcel_Cell::stringFromColumnIndex($position+6).$j;
                    $sl = PHPExcel_Cell::stringFromColumnIndex($position+7).$j;
                    
                    $sheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($position).$j,'=COUNTIF('.$dtRange.',"??:??:??")');
                    $sheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($position+1).$j,'=COUNTIF('.$dtRange.',"LOP")');//
                    $sheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($position+2).$j,'=COUNTIF('.$dtRange.',"Casual Leave")');
                    $sheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($position+3).$j,'=COUNTIF('.$dtRange.',"Work from Home")');
                    $sheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($position+4).$j,'=COUNTIF('.$dtRange.',"On Official Duty")');
                    $sheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($position+5).$j,'=COUNTIF('.$dtRange.',"On Travel")');
                    $sheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($position+6).$j,'=COUNTIF('.$dtRange.',"Maternity Leave")');
                    $sheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($position+7).$j,'=COUNTIF('.$dtRange.',"Sick Leave")');
                    $sheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($position+8).$j,'=COUNTIF('.$dtRange.',"PRESENT")');

                    $sheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($position+9).$j,'=SUM('.$bioP.','.$lop.','.$cl.','.$wfh.','.$ood.','.$ot.','.$ml.','.$sl.')');
                }
                
                /*$sheet->cell('L3', function($cells) {
                    $cells->setValue('=COUNTIF(G3:K3, "LOP")');
                });*/
            });

        })->export('xls');
    }
     
}
