<?php

namespace App\Modules\Manufacturers\Models;

use Illuminate\Database\Eloquent\Model;

class Legalentitydocs extends Model
{
        public $timestamps = false;
        protected $fillable = ['legal_entity_id','doc_name','doc_url','doc_type','ref_type','media_type','reference_no','created_by','created_at','updated_by','updated_at'];
	protected $table = "legal_entity_docs";
	protected $primaryKey = 'doc_id';
}
