<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ResqueJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ResqueJob';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'porting the job to the handler';

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
        
    }

    public function setUp()
    {
    }

    public function perform()
    {
        parent::__construct(); //just to mimic the work flow
        $args = $this->args;
        $task      = $args['ConsoleClass'];
        $arguments = $args['arguments'];
        $this->call($task,$arguments);
    }

    public function tearDown()
    {
    }
}
