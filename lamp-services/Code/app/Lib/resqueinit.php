	<?php
	/**
	 *
	 * Created By : Prasenjit CHowdhury
	 * date : 10 th August
	 * Description : MY own resque terminal for more control than the chrisboulton Libary
	 * credits = chris boulton ####Php-Resque creator ####
	 */
	$path = getcwd();///var/www/fbe/Code
	$ds = DIRECTORY_SEPARATOR;
	if($path != ''){
	    $pathArray = explode($ds,$path);
	    $pathArray = implode($ds,$pathArray);
	}

	$QUEUE = getenv('QUEUE');
	if(empty($QUEUE)) {
		die("Set QUEUE env var containing the list of queues to work.\n");
	}
	
	$resqueVendor = $pathArray.$ds.'vendor'.$ds.'kamisama'.$ds.'php-resque-ex'.$ds;
	//$resqueVendor = $pathArray.$ds.'vendor'.$ds.'chrisboulton'.$ds.'php-resque-ex'.$ds;
	require_once $resqueVendor.'lib/Resque.php';
	require_once $resqueVendor.'lib/Resque/Worker.php';

	$REDIS_BACKEND = getenv('REDIS_BACKEND');
	$REDIS_DB = getenv('REDIS_DB');
	$REDIS_PASSWORD = getenv('REDIS_PASSWORD');
	if(!empty($REDIS_BACKEND)){

		if(empty($REDIS_PASSWORD)){
			//var_dump('setting no password1 resqueinit');
			Resque::setBackend($REDIS_BACKEND,$REDIS_DB,'resque');
		}else{
			if($REDIS_PASSWORD === '' || is_null($REDIS_PASSWORD)){
				//var_dump('setting no password2 resqueinit');
				Resque::setBackend($REDIS_BACKEND,$REDIS_DB,'resque');
			}else{
				//var_dump('setting password resqueinit');
				Resque::setBackend($REDIS_BACKEND,$REDIS_DB,'resque',$REDIS_PASSWORD);
			}
		}
		//Resque::setBackend($REDIS_BACKEND,$REDIS_DB,'resque');
		
		
	}	

	$logLevel = 0;
	$LOGGING = getenv('LOGGING');
	$VERBOSE = getenv('VERBOSE');
	$VVERBOSE = getenv('VVERBOSE');
	if(!empty($LOGGING) || !empty($VERBOSE)) {
	$logLevel = Resque_Worker::LOG_NORMAL;
	}
	else if(!empty($VVERBOSE)) {
	$logLevel = Resque_Worker::LOG_VERBOSE;
	}

	$APP_INCLUDE = getenv('APP_INCLUDE');
	if($APP_INCLUDE) {
	if(!file_exists($APP_INCLUDE)) {
		die('APP_INCLUDE ('.$APP_INCLUDE.") does not exist.\n");
	}

	require_once $APP_INCLUDE;
	}

	$interval = 5;
	$INTERVAL = getenv('INTERVAL');
	if(!empty($INTERVAL)) {
	$interval = $INTERVAL;
	}

	$count = 1;
	$COUNT = getenv('COUNT');
	if(!empty($COUNT) && $COUNT > 1) {
	$count = $COUNT;
	}

	if($count > 1) {
	for($i = 0; $i < $count; ++$i) {
		$pid = pcntl_fork();
		if($pid == -1) {
			die("Could not fork worker ".$i."\n");
		}
		// Child, start the worker
		else if(!$pid) {
			$queues = explode(',', $QUEUE);
			$worker = new Resque_Worker($queues);
			$worker->logLevel = $logLevel;
			fwrite(STDOUT, '*** Starting worker '.$worker."\n");
			$worker->work($interval);
			break;
		}
	}
	}
	// Start a single worker
	else {
	$queues = explode(',', $QUEUE);
	$worker = new Resque_Worker($queues);
	$worker->logLevel = $logLevel;

	$PIDFILE = getenv('PIDFILE');
	if ($PIDFILE) {
		file_put_contents($PIDFILE, getmypid()) or
			die('Could not write PID information to ' . $PIDFILE);
	}

	fwrite(STDOUT, '*** Starting worker '.$worker."\n");
	$worker->work($interval);
	}
	?>
