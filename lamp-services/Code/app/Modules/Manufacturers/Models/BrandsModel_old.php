<?php

namespace App\Modules\Manufacturers\Models;
use Illuminate\Database\Eloquent\Model;

class BrandsModel extends Model
{
        public $timestamps = true;
        protected $fillable = [''];
	protected $table = "brands";
	protected $primaryKey = 'brand_id';
}
