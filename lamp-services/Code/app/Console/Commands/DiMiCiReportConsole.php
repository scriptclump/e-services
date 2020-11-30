<?php
/*
  Filename  : DiMiCiReportConsole.php
  Author    : Ebutor
  Date      : 31-Jan-2017
  Desc      : Console to generate and email DiMiCi Report to requested user
 */

namespace App\Console\Commands;
use Illuminate\Console\Command;
use Session;

use App\Modules\DiMiCiReport\Controllers\DimiciController;
use App\Lib\Queue;


class DiMiCiReportConsole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'DiMiCiReport {data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Console to generate and email DiMiCi Report to requested user';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){

        $arguments = $this->argument();
        $data = base64_decode($arguments['data']);
        $this->info("Parameters ".$data);
        $data = json_decode($data, true);

        print_r($data);
        \Session::put('userId', $data['userId']);
        $Dimici = new DimiciController();
        echo "Came To Command DiMiCiReportConsole";
        $result = $Dimici->createExcelBkground($data['userName'], $data['userId'], $data['start'], $data['end'], $data['dc'],$data['cfc_check']);
        //print_r($result);
        
        $this->info("Report Email Sent to ".$result);
        return true;      
        
    }

}
