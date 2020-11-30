<?php

namespace App\models\Mongo;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Carbon\Carbon;
use MongoDB\BSON\UTCDateTime;

class MongoApiLogsModel extends Eloquent
{
    protected $connection = 'mongo';
    protected $primaryKey = '_id';
    protected $table ="ApiLogs";
    public $timestamps = false;

    /**
     * [insertMailTemplate will insert a request into the dmapi and return a token for use of insertion]
     * @param  [string] $requestType  [description]
     * @param  [string] $resquestData [description]
     * @return [string] lastinsertId  [description]
     */
    public function insertApiLogsRequest($requestType,$resquestData, $logType='SalesOrders'){
        
        $this->logType = $logType;
        $this->requestType = $requestType;
        $this->requestId = '';
        $this->resquestData = $resquestData;
        $this->status = 'Not Set Yet';
        $this->responseData = 'Not Yet Processeed';
        $this->responseDateTime = '';
        $this->requestDateTime = new \MongoDate(Carbon::now()->timestamp);
        $this->save();
        $lastinsertId = $this->id;
        return $lastinsertId;
    }

    public function updateResponse($token,$response, $requestId){
        $update_query = $this::where('_id', '=',$token)->first();
        $update_query->status =  $response['Status'];
        $update_query->responseData = $response['Message'];
        $update_query->requestId = $requestId;
        $update_query->responseDateTime = new \MongoDate(Carbon::now()->timestamp);
                
        if($update_query->save()){
            return true;
        } else {
            return false;
        }
    }

    public function getDmapiResponse($token){

        if(!is_null($token)){
            $processedData = $this::where('_id', '=',$token)->get()->all();
            $processedData = json_decode(json_encode($processedData, true));
            return $processedData;
        }else{
            return false;
        }
    }

    public function getApiLogsListDate($startDate,$endDate,$limit,$pageNo){

        if($pageNo == 0){
            $pageNo = 1;
        }
        $pageNo = $pageNo - 1;
        $startDate = new \MongoDate(strtotime($startDate));
        $endDate = new \MongoDate(strtotime($endDate));
        $data = $this::whereBetween('requestDateTime', [$startDate, $endDate])
                        ->skip($limit*$pageNo)->take($limit)
                        ->get()->all();
        //$data = $data->toArray();

        return $data;

    }


    public function getDataById($id){

        $data = $this::find($id);
        return $data;

    }

}
