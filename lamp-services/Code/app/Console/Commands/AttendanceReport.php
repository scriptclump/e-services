<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Central\Repositories\ProductRepo;
use Log;
use DateTime;
class AttendanceReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:attendance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'writes data to Mongo';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
		$this->reports = new ProductRepo();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //Log::info('in command');
        $message = $this->reports->updateAttendance();
        $this->info($message);
    }
}
