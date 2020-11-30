<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Central\Repositories\ProductRepo;

class FfReportComplete extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:ffreportcomplete';

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
        $message = $this->reports->setFfReportCompleteData();
        $this->info($message);
    }
}
