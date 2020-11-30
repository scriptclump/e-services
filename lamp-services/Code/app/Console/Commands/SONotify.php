<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Central\Repositories\ReportsRepo;

class SONotify extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:sonotify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify SO for OOS SKU';
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
        $message = $this->reports->sendSONotifyMail();
        $this->info($message);
    }
}
