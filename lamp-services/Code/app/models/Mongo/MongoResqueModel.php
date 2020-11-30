<?php

namespace App\models\Mongo;

//use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Carbon\Carbon;
use MongoDB\BSON\UTCDateTime;

class MongoResqueModel extends Eloquent
{
    protected $connection = 'mongo';
    protected $primaryKey = '_id';
    protected $table ="resque_stats";

    /**
     * [insertMailTemplate will insert a request into the dmapi and return a token for use of insertion]
     * @param  [string] $requestType  [description]
     * @param  [string] $resquestData [description]
     * @return [string] lastinsertId  [description]
     */
    public function insertQueueRequest($token,$queue,$arguments){

        $now = new \MongoDate(Carbon::now()->timestamp);
        $this->queue = $queue;
        $this->token = $token;
        $this->consoleClass = $arguments['ConsoleClass'] ;
        $this->arguments = $arguments['arguments'];
        $this->tokenGenerated = $now;
        $this->jobStatus = 'Queued';
        $this->tokenUpdated  = $now;
        $this->save();
        $lastinsertId = $this->id;
        return $lastinsertId;
    }

    public function updateQueueStatus($token,$status){

        $this->connection = 'mongo';
        $this->primaryKey = '_id';
        $this->table ="resque_stats";
        $now = new \MongoDate(Carbon::now()->timestamp);
    	$update_query = $this->where('token', '=',$token)->first();
        $update_query->tokenUpdated = $now;
        $update_query->jobStatus = $status;
        if($update_query->save()){
            return true;
        } else {
            return false;
        }
    }

}
