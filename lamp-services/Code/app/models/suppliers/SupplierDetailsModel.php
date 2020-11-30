<?php

namespace App\models\suppliers;

use Illuminate\Database\Eloquent\Model;

class SupplierDetailsModel extends Model
{
    //
    protected $table = 'supplier_details';
     protected $fillable = array('legal_entity_id','supplier_id');
     public $timestamps = false;
}
