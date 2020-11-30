#!/usr/bin/env php
<?php

/**
 *
 * Created By : Prasenjit CHowdhury
 * date : !st August
 * updated On : 29th August 2016
 * Description : Created To drop jobs on resque workers for forking for redis monitories and data management 
 */


$path = realpath(dirname(__FILE__));
$ds = DIRECTORY_SEPARATOR;
$pathArray = explode('/',$path);
array_pop($pathArray);
array_pop($pathArray);          
$pathArray = implode($ds,$pathArray);

$laravelPath2 = $pathArray.$ds.'bootstrap'.$ds.'autoload.php';
$laravelPath1 = $pathArray.$ds.'bootstrap'.$ds.'app.php';
require_once($laravelPath2);
use Illuminate\Console\Command;

use Illuminate\Console\Application as Artisan;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\models\Mongo\MongoResqueModel;
use Illuminate\Foundation\Console\Kernel;
use Illuminate\Foundation\Application;
//use Illuminate\Support\Facades\Artisan;


/**
 * @author : prasenjit chowdhury
 * @version : 0.1 Alpha + 2  
 * 
 * 
 * A PHP-Resque Job which starts a Laravel Task.
 * This enables us to use PHP-Resque as robust processing queue
 * but still use Laravel Commands, bundles etc to do the work
 * 
            
            To those brave hearts who wanders here.This one is long tesed and tried method 
            almost burnt me out so pay heed to this advice design the console class such that is avoids optional arguments symphony seems to break on direct cases if we acces it the normal way from outside
 *
 */

Resque_Event::listen('beforeFirstFork', array('ResquePlugin', 'beforeFirstFork'));
Resque_Event::listen('beforeFork', array('ResquePlugin', 'beforeFork'));
Resque_Event::listen('afterFork', array('ResquePlugin', 'afterFork'));
Resque_Event::listen('beforePerform', array('ResquePlugin', 'beforePerform'));
Resque_Event::listen('afterPerform', array('ResquePlugin', 'afterPerform'));
Resque_Event::listen('onFailure', array('ResquePlugin', 'onFailure'));
Resque_Event::listen('beforeEnqueue', array('ResquePlugin', 'beforeEnqueue'));
Resque_Event::listen('afterEnqueue', array('ResquePlugin', 'afterEnqueue'));




class ResqueJobRiver extends Kernel
{
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ResqueJobRiver';

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
        //parent::__construct();
        //$resquePlugin = new ResquePlugin();
    }

    public function setUp(){

        
    }

    public function perform()
    {

        $args = $this->args;
        $task      = $args['ConsoleClass'];
        $arguments = $args['arguments'];

        $argv = array();
        array_push($argv,null);
        array_push($argv,$task);
        foreach ($arguments as $value) {
            array_push($argv,$value);            
        }

        //Can be moved to constructor but will make no significant change
        $ds = DIRECTORY_SEPARATOR;
        $path = realpath(dirname(__FILE__));
        $pathArray = explode($ds,$path);
        array_pop($pathArray);
        array_pop($pathArray);          
        $pathArray = implode($ds,$pathArray);
        /*#################################################################
        ***** The magic to access the whole laravel factory begins here ****
        #################################################################*/

        $app = new Illuminate\Foundation\Application(realpath($pathArray));
        $app->singleton(
            Illuminate\Contracts\Http\Kernel::class,
            App\Http\Kernel::class
        );

        $app->singleton(
            Illuminate\Contracts\Console\Kernel::class,
            App\Console\Kernel::class
        );

        $app->singleton(
            Illuminate\Contracts\Debug\ExceptionHandler::class,
            App\Exceptions\Handler::class
        );

        $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
        $input = new Symfony\Component\Console\Input\ArgvInput($argv);
        $status = $kernel->handle(
                                     $input,
                                     new Symfony\Component\Console\Output\ConsoleOutput
        );
        exit($status);

    }

    public function tearDown()
    {
        var_dump("put new data after");
    }

    public function setCatchExceptions(){
        echo "exception handled";
    }
}


class ResquePlugin{

    private $path;

    public static function get_path(){

        
        return $path;
        
    }

    public static function afterEnqueue($class, $arguments)
    {
        echo "Job was queued for " . $class . ". Arguments:";
        //print_r($arguments);
    }

    public static function beforeFirstFork($worker)
    {
        echo "Worker started. Listening on queues: " . implode(', ', $worker->queues(false)) . "\n";
    }

    public static function beforeFork($job){

        $ds = DIRECTORY_SEPARATOR;
        $path = realpath(dirname(__FILE__));
        $pathArray = explode($ds,$path);
        array_pop($pathArray);
        array_pop($pathArray);
        $path = implode($ds,$pathArray);
        $job = $job->payload;
        $id = $job['id'];
        $status = 'QueuePicked';
        $task = 'updateResque';
        echo "cd $path & php artisan $task $id $status" .PHP_EOL;
        //exec("cd $path & php artisan $task $id $status");
    }

    public static function afterFork($job)
    {
        $ds = DIRECTORY_SEPARATOR;
        $path = realpath(dirname(__FILE__));
        $pathArray = explode($ds,$path);
        array_pop($pathArray);
        array_pop($pathArray);
        $path = implode($ds,$pathArray);
        $job = $job->payload;
        $id = $job['id'];
        $status = 'DeQueue';
        $task = 'updateResque';
        echo "cd $path & php artisan $task $id $status" .PHP_EOL;
        //exec("cd $path & php artisan $task $id $status");
    }

    public static function beforePerform($job)
    {
        
        $ds = DIRECTORY_SEPARATOR;
        $path = realpath(dirname(__FILE__));
        $pathArray = explode($ds,$path);
        array_pop($pathArray);
        array_pop($pathArray);
        $path = implode($ds,$pathArray);
        $job = $job->payload;
        $id = $job['id'];
        $status = 'Running';
        $task = 'updateResque';
        echo "cd $path & php artisan $task $id $status" .PHP_EOL;
        //exec("cd $path & php artisan $task $id $status");
    }

    public static function afterPerform($job)
    {
        $ds = DIRECTORY_SEPARATOR;
        $path = realpath(dirname(__FILE__));
        $pathArray = explode($ds,$path);
        array_pop($pathArray);
        array_pop($pathArray);
        $path = implode($ds,$pathArray);
        $job = $job->payload;
        $id = $job['id'];
        $status = 'Completed';
        $task = 'updateResque';
        echo "cd $path & php artisan $task $id $status" .PHP_EOL;
        //exec("cd $path & php artisan $task $id $status");
    }

    public static function onFailure($exception, $job)
    {
        
        $ds = DIRECTORY_SEPARATOR;
        $path = realpath(dirname(__FILE__));
        $pathArray = explode($ds,$path);
        array_pop($pathArray);
        array_pop($pathArray);     
        $path = implode($ds,$pathArray);

         if (! isset($job->payload['args'][0]['attempts'])) {

            $job->payload['args'][0]['attempts'] = 0;

        }

        // Increase the number of attempts
        $job->payload['args'][0]['attempts']++;

        if ( 3 >= $job->payload['args'][0]['attempts']) {

            $job->payload['args'] =  $job->payload['args'][0];
            //$k = $job->recreate();
            $status = new Resque_Job_Status($job->payload['id']);
            $monitor = false;
            if($status->isTracking()) {
                    $monitor = true;
            }
            $id = $job->create('failed','ResqueJobRiver', $job->payload['args'],true);
            echo $id.PHP_EOL;

        }

    }

    public static function onSucess($exception, $job)
    {
        
        $ds = DIRECTORY_SEPARATOR;
        $path = realpath(dirname(__FILE__));
        $pathArray = explode($ds,$path);
        array_pop($pathArray);
        array_pop($pathArray);     
        $path = implode($ds,$pathArray);
        $job = $job->payload;
        $id = $job['id'];
        $status = 'Completed';
        $task = 'updateResque';
        echo "cd $path & php artisan $task $id $status" .PHP_EOL;
        //exec("cd $path & php artisan $task $id $status");
    }
}

?>