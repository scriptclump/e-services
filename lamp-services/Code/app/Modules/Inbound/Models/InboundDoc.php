<?php

namespace App\Modules\Inbound\Models;

use Illuminate\Database\Eloquent\Model;

class InboundDoc extends Model{
    
    protected $primaryKey = "inbound_doc_id";
    
    public function uploadFile($uploadData) {
        $this->inbound_request_id = $uploadData['inbound_request_id'];
        $this->doc_type = $uploadData['file_extension'];
        $this->doc_url = $uploadData['file_location'];
        $this->save();
    }
}