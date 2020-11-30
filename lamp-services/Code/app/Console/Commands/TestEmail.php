<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Central\Repositories\MailMongo;
use Central\Repositories\OrderRepo;
use Exception;
use DB;

class TestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'TestEmail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

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
        throw new Exception('Gds order products insert array could\'nt be constructed check the error log');
    }
}
