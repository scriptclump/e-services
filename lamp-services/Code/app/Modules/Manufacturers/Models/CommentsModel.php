<?php

namespace App\Modules\Manufacturers\Models;

use Illuminate\Database\Eloquent\Model;

use DB;

class CommentsModel extends Model
{
    public $timestamps = false;
    protected $fillable = ['comments','legal_entity_id','supplier_id','user_id'];
	protected $table = "product_comments";
	protected $primaryKey = 'cmt_id';
	
}
