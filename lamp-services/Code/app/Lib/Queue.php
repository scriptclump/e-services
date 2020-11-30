<?php
	
	/*
	* 	@prasenjit Chowdhury
	*/
	namespace App\Lib;
	//namespace Resque;
	//use App\Lib;
	use Resque;
	use Resque_Job;
	use Resque_Job_Status;
	use Resque_Event;
	use App\models\Mongo\MongoResqueModel;

	class Queue{

		private $mongoConnector = null;
		public function __construct(){

			$backend_hosts = env('REDIS_HOST','localhost');
			$backend_port = env('REDIS_PORT', '6379');
			$backend_db = env('REDIS_DB', '1');
			$backend_password = env('REDIS_PASSWORD',null);
			$backend = $backend_hosts.':'.$backend_port;
			$this->mongoConnector = new MongoResqueModel();
			if(empty($backend_password)){
				//var_dump('setting no password1 Queue');
                Resque::setBackend($backend_hosts.':'.$backend_port,$backend_db,'resque');
            }else{
            	//var_dump('setting no password2 Queue');
                if($backend_password == '' || is_null($backend_password)){
                	Resque::setBackend($backend_hosts.':'.$backend_port,$backend_db,'resque');
                }else{
                	//var_dump('setting password Queue');
                	Resque::setBackend($backend_hosts.':'.$backend_port,$backend_db,'resque',$backend_password);
                }
            }
   				//Resque::setBackend($backend_hosts.':'.$backend_port,$backend_db,'resque');
			
		}

		public function enqueue($queue,$method,$args){
			
			//Resque::enqueue('default', 'My_Job', $args);{
			//Queue the call
        	$token = Resque::enqueue($queue,$method,$args,true);
        	//$this->mongoConnector->insertQueueRequest($token,$queue,$args);
	        return $token;     

		}

		public static function getJobStatus($token) {        
	        $status = new Resque_Job_Status($token);
	        $statuses = array('Queued', 'Running', 'Failed', 'Completed');
	        $s = $status->get();
	        if (!$s) {
	            return array(-1, 'Not Found');
	        }

	        return array($s, $statuses[$s - 1]);
    	}

    	public static function isJobTracked($token) {
        	
        	$status = new Resque_Job_Status($token);
        	return $status->isTracking();
    	
    	}

    	 /*General function for set data in cache
	     * @params string $key
	     * @params array $dtaa
	     * */
	    public static function saveKeyDataInCache($key,$data,$expiry=null) {
	       
	    }
	    /*Fetch key data from cache*/
	    public static function getKeyDataFromCache($key) {
	        
	    }
	    
	    /*Fetch key data from cache*/
	    public static function deleteKeyDataFromCache($key) {
	        
	    }

	}

?>
