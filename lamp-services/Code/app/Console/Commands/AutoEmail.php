<?php
/*
  Filename : copyInventoryConsole.php
  Author : Ebutor
  CreateData : 19-Sep-2016
  Desc : Command to copy all Inventory data from 'vw_inventory_report' daily @ 23:00
 */
namespace App\Console\Commands;

date_default_timezone_set("Asia/Kolkata");

use Illuminate\Console\Command;
use App\Modules\InvDataMismatchReports\Controllers\ReportController;
class AutoEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'AutoEmail {notify_code} {mongo_template_code} {is_attach?} {is_summary?}  {options?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'AutoEmail Command';

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
    public function handle()
    {
        $arguments = $this->argument();
        $notify_code = $arguments['notify_code'];
        $mongo_template = $arguments['mongo_template_code'];
        $options['conditions'] = isset($arguments['options'])?json_decode(base64_decode($arguments['options']),true):[];
        $options['is_attach'] = isset($arguments['is_attach'])?$arguments['is_attach']:0;
        $options['is_summary'] = isset($arguments['is_summary'])?$arguments['is_summary']:0;
        $reportsObj = new ReportController();
        $message= $reportsObj->index($notify_code,$mongo_template,$options);
        //$this->info($message);
    }
}
