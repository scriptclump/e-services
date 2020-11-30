<?php

namespace App\Modules\Grn\Models;

use Illuminate\Database\Eloquent\Model;

use DB;

class Tax extends Model
{
      public function getInputTaxByInwardId($id) {
            try {
                  $fields = array('tax.tax_type', 'tax.tax_percent', DB::raw('sum(tax.tax_amount) as tax_amount'));

            $query = DB::table('input_tax as tax')->select($fields);
            $query->where('tax.inward_id', $id)
                    ->where('tax.tax_amount', '>', 0)
                    ->groupBy('tax.tax_type');
            $taxArr = $query->get()->all();
            
            return $taxArr;
          } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }   
      }

	public function getProductTaxByGrnId($grnId) {
		
		try {
			$fields = array('tax.tax_class', 'tax.tax_percent', 'tax.tax_value', 'products.product_id', 'lookup.master_lookup_name as name');

            $query = DB::table('grn_taxes as tax')->select($fields);
            $query->join('grn_products as products','products.grn_prd_id','=','tax.grn_prd_id');
            $query->join('master_lookup as lookup','lookup.value','=','tax.tax_class');            
            $query->where('products.grn_id', $grnId);
            $query->groupBy('tax.grn_tax_id');
            $taxArr = $query->get()->all();

            $totTaxPer = 0;
            $taxDataArr = array();
            foreach($taxArr as $tax) {
            	$taxDataArr['item'][$tax->product_id][] = array('tax'=>$tax->tax_percent, 
																'tax_value'=>$tax->tax_value, 
																'name'=>$tax->name);

				$taxDataArr['summary'][] = array('tax'=>$tax->tax_percent, 'tax_value'=>$tax->tax_value, 'name'=>$tax->name);
			}            
            return $taxDataArr;
	    } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }	
	}
}
