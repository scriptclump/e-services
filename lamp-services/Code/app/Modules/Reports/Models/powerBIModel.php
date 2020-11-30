<?php

namespace App\Modules\Reports\Models;

use Illuminate\Database\Eloquent\Model;

class powerBIModel extends Model
{
    public $timestamps = true;
    protected $fillable = ['feature_code','pbi_url'];
	protected $table = "powerbi_urls";
	protected $primaryKey = 'pbu_id';
}