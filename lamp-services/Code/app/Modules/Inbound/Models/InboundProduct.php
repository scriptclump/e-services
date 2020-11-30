<?php

namespace App\Modules\Inbound\Models;

/*
  Filename : InboundProduct.php
  Author : Vijaya Bhaskar Chenna
  CreateData : 20-May-2016
  Desc : Model for inbound product table
 */

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class InboundProduct extends Model {

    protected $table = "inbound_product";
    protected $primaryKey = "inbound_product_id";

    public function createInboundProducts($inwardRequestId, $createProductDetails) {
        $this->inbound_request_id = $inwardRequestId;
        $this->product_id = $createProductDetails['product_id'];
        $this->seller_sku = $createProductDetails['sku'];
        $this->product_quantity = $createProductDetails['product_qty'];
        $this->created_by = Session::get('legal_entity_id');
        $this->save();
    }

}
