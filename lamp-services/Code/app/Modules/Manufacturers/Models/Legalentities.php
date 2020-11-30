<?php

namespace App\Modules\Manufacturers\Models;

use Illuminate\Database\Eloquent\Model;

class Legalentities extends Model
{
        public $timestamps = true;
        protected $fillable = ['business_legal_name','legal_entity_type_id','business_type_id','logo','parent_id'];
	protected $table = "legal_entities";
	protected $primaryKey = 'legal_entity_id';
}
