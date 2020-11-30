<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Central\Repositories\ProductRepo;

class FfReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:ffreport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'writes data to ff_report table';

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
        $message = $this->reports->setFfReportData();
        $this->info($message);
    }
}
