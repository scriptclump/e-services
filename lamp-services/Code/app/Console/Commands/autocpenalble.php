<?php

/**
 *
 * Created By : Rasheed Ahamed Shaik
 * date : 30th Jan 2019
 * Description : Single base control is used for inserting cp_enable_table.
 * 
 */

namespace App\Console\Commands;

date_default_timezone_set("Asia/Kolkata");

use Illuminate\Console\Command;
use App\Modules\Grn\Controllers\GrnController;
class autocpenalble extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'autocpenalble {inward_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Single base control is used for inserting cp_enable_table';

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
        $inward_id = $arguments['inward_id'];
        $grnObj = new GrnController();
        $grnObj->enableCp($inward_id);
        
    }
}
