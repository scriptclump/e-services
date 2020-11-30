<?php

namespace App\models\Dmapi;

use Illuminate\Database\Eloquent\Model;

class gdsProducts extends Model
{
    /**
     * [$primaryKey - primary key for the table]
     * [$timestamps - unknown]
     * [$table - 'table name']
     * 
     * @var string
     */
    protected $primaryKey = 'gds_order_prod_id';
    public $timestamps = false;
    protected $table = 'gds_order_products';

    /** [updateTaxValueProduct description] */
    public function updateTaxValueProduct($gds_order_prod_id,$tax_value){

    	// $query = " update gds_order_products set tax = $tax_value where gds_order_prod_id = $gds_order_prod_id";
    	

    }
}
