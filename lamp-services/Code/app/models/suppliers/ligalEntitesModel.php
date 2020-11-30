<?php

namespace App\models\suppliers;
use Illuminate\Database\Eloquent\Model;

class ligalEntitesModel extends Model
{
    //
    protected $table = 'legal_entities';
     protected $fillable = array('legal_entity_id','legal_name', 'address1','address2','city','state_id','pincode','website_url','business_name','pan_number','legal_entity_type_id','parent_id');
     public $timestamps = false;
}
