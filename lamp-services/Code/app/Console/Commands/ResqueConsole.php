<?php


/**
 *
 * Created By : Prasenjit CHowdhury
 * date : (st August
 * Description : Creating a single interface for acting with the redis no need of third party code single wntry for redis monitories and data management 
 */

namespace App\Console\Commands;
date_default_timezone_set('UTC');

use Illuminate\Console\Command;
use Resque;
use Resque_Job_Status;
use Resque_Worker;
use Resque_Stat;

class ResqueConsole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    
    private $resquePath;
    private $laravelPath;
    private $logPath;
    private $error = array();
    private $errorMessage;
    private $user;
    private $redisHost;
    private $redisPort;

    protected $signature = 'resque {action}
                                   {-queue=default}
                                   {-n=5}
                                   {-interval=5}
                                   ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'handle all resque related request from this single console';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $path = app_path();
        $ds = DIRECTORY_SEPARATOR;
        if($path != ''){

            $pathArray = explode($ds,$path);
            array_pop($pathArray);            
            $pathArray = implode($ds,$pathArray);
            $this->laravelPath = $pathArray.$ds.'app'.$ds.'Lib'.$ds.'ResqueJobRiver.php';
            //$pathArray.$ds.'app'.$ds.'Console'.$ds.'Commands'.$ds.'ResqueJob.php';//$pathArray.$ds.'bootstrap'.$ds.'app.php';
            //$this->resquePath = $pathArray.$ds.'vendor'.$ds.'chrisboulton'.$ds.'php-resque'.$ds.'resque.php';
            $this->resquePath = $pathArray.$ds.'app'.$ds.'Lib'.$ds.'resqueinit.php';
            //var_dump($this->resquePath);
            $this->logPath = $pathArray.$ds.'storage'.$ds.'logs'.$ds.'resque.log';

        } 

        $this->redisHost = env('REDIS_HOST','localhost');
        $this->redisPort = env('REDIS_PORT', 6379);
        $this->redisDb= env('REDIS_DB', 2);
        $this->redisPassword = env('REDIS_PASSWORD',null);

        if(is_null($this->redisPort) || is_null($this->redisHost)){

            $this->error[] = "Redis config missing";
        }

        if(!file_exists($this->resquePath)){

            $this->error[] = "Resque folder chrisboulton".$ds."php-resque".$ds."resque.php missing please update the composer to add resque part";
        }

            
        if(!file_exists($this->logPath)){

            $fh = fopen($this->logPath,'w');
            fclose($fh);
            chmod($this->logPath, 0777);
            
        }
        $this->user = exec('whoami');//get_current_user();

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
            
        /*
        *   Check for any errors of directory structure if present 
        *   move forward else throw error
        */
        if(count($this->error) > 0){

            $error = '';
            foreach ($this->error as $key => $value) {
                
                $error .= $key." $value";
                //var_dump($value);
            }

            $this->error($error);

        }else{


            $arguments = $this->argument();
            $action = $arguments['action'];
            $queue = $arguments['-queue'];
            $noOfworkers = $arguments['-n'];
            $interval = $arguments['-interval'];


            if(!in_array($action,array('start','tail','stop','stats','restart'))){//'stop','restart',

                $this->error("Action Allowed are start / stop / restart");
                return;

            }

            //var_dump($this->redisPassword);
            if(empty($this->redisPassword)){
                echo "setting no password1 resconsole";
                Resque::setBackend($this->redisHost.':'.$this->redisPort,$this->redisDb,'resque');
            }else{

                if($this->redisPassword == '' || is_null($this->redisPassword)){
                    echo "setting no password2 resconsole";
                    Resque::setBackend($this->redisHost.':'.$this->redisPort,$this->redisDb,'resque');
                }else{
                    echo "setting password resconsole";
                    Resque::setBackend($this->redisHost.':'.$this->redisPort,$this->redisDb,'resque',$this->redisPassword);
                }
            }
            
            //Resque::setBackend($this->redisHost.':'.$this->redisPort,$this->redisDb,'resque');
            //land on the required action fields
            //@params 
            //      queue = queuename
            //      number = numberofworkers
            //      interval = intervaltime   
            
            $this->$action(escapeshellarg($queue),escapeshellarg($noOfworkers),escapeshellarg($interval));

        }
    }


    public function start($queue,$workersNumber,$interval){
        
        $this->info(" Forking new PHP Resque worker service ( queue: {$queue} no of workers {$workersNumber} interval time {$interval})" );
        //$cmd = 'nohup sudo -u '.$this->user.' bash -c "cd ' .' php ./resque.php';
        $cmd = "nohup sudo -u $this->user QUEUE=$queue COUNT=$workersNumber REDIS_BACKEND=$this->redisHost:$this->redisPort REDIS_DB=$this->redisDb ";
        $cmd .= "REDIS_PASSWORD=$this->redisPassword ";
        $cmd .= "VVERBOSE=true APP_INCLUDE=$this->laravelPath INTERVAL=$interval php $this->resquePath";
        $cmd .= ' >> '.$this->logPath;
        $this->info($cmd);
        //$cmd.= ' > /dev/null 2>&1 &';
       passthru($cmd);
        
    }

    /**
     * Convenience functions.
     */
    public function tail($queue,$workersNumber,$interval){
        //$log_path = $this->logPath;
        if (file_exists($this->logPath))
        {
            passthru('sudo tail -f ' . escapeshellarg($this->logPath));
        }
        else
        {
            $this->out('Log file does not exist. Is the service running?');
        }
    }


    /**
     * Kill all php resque worker services.
     */
    public function stop($queue,$workersNumber=NULL,$interval=NULL){

        $queue = str_replace("'", "", $queue);
        $forced = false;
        $this->info('Shutting down Resque Worker complete');
        $workers = Resque_Worker::all();
        if (empty($workers))
        {
            $this->error('   There were no active workers to kill ...');
        }
        else{
            
            $count = 0;
            foreach($workers as $w)
            {
                if($forced){
                    $w->shutDownNow();  
                }else{
                    $w->shutDown();    // Send signal to stop processing jobs
                }

                // Remove jobs from resque environment
                //list($hostname, $pid, $queue) = explode(':', (string)$w);
                
                $dumps = explode(':', (string)$w);
                if($dumps[2] == $queue){
                    $w->unregisterWorker();
                    $this->error('Killing ' . $dumps[1]);
                    exec('kill -9 '.$dumps[1]);    
                    $count++;
                } // Kill all remaining system process
            }
            $this->info('Killed '.$count.' workers ...');
        }
    }


    public function stats($queue,$workersNumber,$interval)
    {
        $this->info("\n");
        $this->info('<info>PHPResque Statistics</info>');
        $this->info("\n");
        $this->info('<info>Jobs Stats</info>');
        $this->info("   Processed Jobs : " . Resque_Stat::get('processed'));
        $this->info("   <warning>Failed Jobs    : " . Resque_Stat::get('failed') . "</warning>");
        $this->info("\n");
        $this->info('<info>Workers Stats</info>');
        $workers = Resque_Worker::all();
        $this->info("   Active Workers : " . count($workers));
        
        if (!empty($workers))
        {
            foreach($workers as $worker)
            {
                $this->info("\tWorker : " . $worker);
                $this->info("\t - Started on     : " . Resque::Redis()->get('worker:' . $worker . ':started'));
                $this->info("\t - Processed Jobs : " . $worker->getStat('processed'));
                $worker->getStat('failed') == 0
                    ? $this->info("\t - Failed Jobs    : " . $worker->getStat('failed'))
                    : $this->info("\t - <warning>Failed Jobs    : " . $worker->getStat('failed') . "</warning>");
            }
        }
        
        $this->info("\n");
    }


    /**
     * Restart all workers
     */
    public function restart()
    {
        $this->stop(false);
        
        if (false !== $workers = $this->__getWorkers())
        {
            foreach($workers as $worker)
                $this->start($worker);
        }
        else
            $this->start();
    }
}
