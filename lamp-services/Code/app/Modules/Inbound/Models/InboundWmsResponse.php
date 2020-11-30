<?php

namespace App\Modules\Inbound\Models;

/*
  Filename : InboundWmsResponse.php
  Author : Vijaya Bhaskar Chenna
  CreateData : 02-June-2016
  Desc : Model for inbound wms response table
 */

use Illuminate\Database\Eloquent\Model;

class InboundWmsResponse extends Model
{
    protected $primaryKey = "inbound_wms_response_id";
    
    public function createWmsResponse($inwardRequestId, $apiResultArray) {
        $this->log_request_id = $apiResultArray['context']['run_log_id'];
        $this->request_type = "AGN Create";
        $this->status = "In Queue";
        $this->inbound_request_id = $inwardRequestId;
        return $this->save();
    }
    
    public function responseByStatus($requestedStatus) {
        $reslut_set = $this::all()->where('status', $requestedStatus);
        $reslut_set_json = json_decode(json_encode($reslut_set), true);
        return $reslut_set_json;
    }
    
    public function updateStatus($requestId, $status) {
        $update_query = $this::all()->where('log_request_id', $requestId)->first();
        $update_query->status = $status;
        if($update_query->save()){
            echo "status updated";
        } else {
            echo "update fail!";
        }
    }
}