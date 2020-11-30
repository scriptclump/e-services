<?php

namespace App\Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;


class CategoryEloquentModel extends Model
{
    public $timestamps = false;
    protected $primaryKey = "category_id";
    protected $table = "categories";

}

