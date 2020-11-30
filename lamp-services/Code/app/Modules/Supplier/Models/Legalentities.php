<?php

namespace App\Modules\Supplier\Models;

use Illuminate\Database\Eloquent\Model;

class Legalentities extends Model
{
        public $timestamps = false;
        protected $fillable = ['business_legal_name','parent_le_id','legal_entity_type_id','business_type_id','address1','address2','city','state_id','country','pincode','pan_number','tin_number','profile_completed','website_url','logo','parent_id'];
	protected $table = "legal_entities";
	protected $primaryKey = 'legal_entity_id';
}
