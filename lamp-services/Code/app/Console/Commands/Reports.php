<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Central\Repositories\ReportsRepo;

class Reports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:reports';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'All Reports';
    protected $reports;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->reports = new ReportsRepo();
    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $message = $this->reports->sendReport();
        $this->info($message);
    }
}
