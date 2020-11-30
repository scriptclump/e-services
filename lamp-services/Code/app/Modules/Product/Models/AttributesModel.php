<?php

namespace App\Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;


class AttributesModel extends Model
{
    public $timestamps = false;
    protected $primaryKey = "attribute_id";
    protected $table = "attributes";

}

