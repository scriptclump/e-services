<?php

namespace App\models\Mongo;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Carbon\Carbon;
use MongoDB\BSON\UTCDateTime;

class MongoDmapiModel extends Eloquent
{
    protected $connection = 'mongo';
    protected $primaryKey = '_id';
    protected $table ="dmapirequests";
    public $timestamps = false;

    /**
     * [insertMailTemplate will insert a request into the dmapi and return a token for use of insertion]
     * @param  [string] $requestType  [description]
     * @param  [string] $resquestData [description]
     * @return [string] lastinsertId  [description]
     */
    public function insertDmapiRequest($requestType,$resquestData){
        $this->requestType = $requestType;
        $this->resquestData = $resquestData;
        $this->status = 'Not Set';
        $this->orderId = 'Not Set';
        $this->orderCode= 'Not Set';
        $this->customerLeId = 'Not Set';
        $this->responseData = 'Not Yet Processeed';
        $this->requestDateTime = new \MongoDate(Carbon::now()->timestamp);
        $this->save();
        $lastinsertId = $this->id;
        return $lastinsertId;
    }

    public function updateResponse($token,$response){
        $update_query = $this::where('_id', '=',$token)->first();
        $update_query->status =  $response['Status'];
        $update_query->responseData = $response['Message'];
        
        /**
         * Storing actual order id e.g. 410
         */
        if(isset($response['order_id_actual'])){
            $update_query->orderId = $response['order_id_actual'];
        }
        /**
         * storing order Code e.g TSSO0000410
         */
        if (isset($response['order_id'])) {
            $update_query->orderCode = $response['order_id'];
        }

        if (isset($response['customerLeId'])) {
            $update_query->customerLeId = $response['customerLeId'];
        }        
        
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

    public function getDmapiListDate($startDate,$endDate,$limit,$pageNo){

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


    public function updateTokenOnRetry($token){

        $update_query = $this::where('_id', '=',$token)->first();
        $update_query->status = 'Retrying';
        if($update_query->save()){
            return true;
        } else {
            return false;
        }
    }

    public function checkTokenStatus($token){

        $query = $this->where('_id',$token)->get()->all();
        if(count($query) > 0){

            $query = json_decode(json_encode($query, true));
            if($query[0]['status'] == 'Retrying'){
                return array('status' => 405,'message' => 'Already under retry');
            }else{
                return array('status' => 200,'message' => 'Go ahead with retry');
            }
        }else{
            return array('status' => 400,'message' => 'Token not found');
        }
   }

}
