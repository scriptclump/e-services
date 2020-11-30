<?php

namespace App\Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;


class LegalEntityModel extends Model
{
    public $timestamps = false;
    protected $primaryKey = "legal_entity_id";
    protected $table = "legal_entities";

}

