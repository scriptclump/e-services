<?php

namespace App\Modules\Supplier\Models;

use Illuminate\Database\Eloquent\Model;

class Documentsmaster extends Model
{
        public $timestamps = false;
        protected $fillable = ['business_type_id','country','doc_no','reference_no','is_required','created_by','created_at','updated_by','updated_at'];
	protected $table = "documents_master";
	protected $primaryKey = 'doc_master_id';
}
