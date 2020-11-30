<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Lib\Queue;
use Cache;
use Exception;

class queuetestConsole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queuetest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Queue related works';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        // echo app_path().PHP_EOL; // Path to the application root (app/)
        // echo base_path().PHP_EOL; // Path to the project root (parent of app/)
        // echo public_path().PHP_EOL; // Path to the public/ folder
        // echo storage_path().PHP_EOL; // Path to the storage folder (usually app/storage/)
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){

        // $args = array("ConsoleClass" => 'Channel','arguments' => array('checkAuthentication',2,'{test:json}'));
        // $args = array("ConsoleClass" => 'mail','arguments' => array('DmapiOrderTemplate','222'));
        // $queue = new Queue();
        // $token = $queue->enqueue('default','ResqueJobRiver',$args);
        // var_dump($token);
        // var_dump($queue->getJobStatus($token));
        // $array = array('methodname' => 'asdasd');
        // $this->call('Channel',$array);
        
        // $value = Cache::get('user');
        // var_dump($value);
        // Cache::put('user', 'fuck off', 10);
        
        $args = array("ConsoleClass" => 'TestEmail','arguments' => array());
        $queue = new Queue();
        $token = $queue->enqueue('default','ResqueJobRiver',$args);
        var_dump($token);

        // $queue = new Queue();
        // $token = $queue->enqueue('default','ResqueJobRiver',$args);
        // var_dump($token);

        // $queue = new Queue();
        // $token = $queue->enqueue('default','ResqueJobRiver',$args);
        // var_dump($token);
    }

}
