<?php
/*
  Filename : EmpAttendanceConsole.php
  Author : Ebutor
  CreateData : 19-Sep-2017
  Desc : Schedule Job to take capture Employee Attendance @ 04:00 hrs daily
 */
namespace App\Console\Commands;

date_default_timezone_set("Asia/Kolkata");

use Illuminate\Console\Command;
use App\Modules\EmployeeAttendance\Controllers\EmployeeAttendanceController;

class GetDevicesConsole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'GetDevices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Capture attendace device config -- daily';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->_EmpAtten = new EmployeeAttendanceController();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        echo "\nStart time: ".date('Y-m-d H:i:s')."\n";
        $result = $this->_EmpAtten->getDeviceData();
        //echo "<pre>"; print_r($allData);
        if($result == 'Success')
            echo "job Done !!\n";
        else
            echo $result;

        echo "End time: ".date('Y-m-d H:i:s')."\n";
    }
}
