<?php
/*

 */
namespace App\Modules\TaxReport\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
/*

 */
class OutputTax extends Model {

    protected $table = 'output_tax';
    protected $primaryKey = 'output_tax_id';

    public function outwardGridData($page, $pageSize, $filterBy = '') {
        $sql = $this;
        $sql = $sql->join('products', 'products.product_id', '=', 'output_tax.product_id');
        $sql = $sql->join('legalentity_warehouses', 'legalentity_warehouses.le_wh_id', '=', 'output_tax.le_wh_id');
        $sql = $sql->join('master_lookup', 'master_lookup.value', '=', 'output_tax.transaction_type');

        if (!empty($filterBy['dc_name'])) {
            $sql = $sql->whereIn('output_tax.le_wh_id', $filterBy['dc_name']);
        }

        if (!empty($filterBy['state'])) {
            $sql = $sql->whereIn('legalentity_warehouses.state', $filterBy['state']);
        }

        if (!empty($filterBy['trans_number'])) {
            $sql = $sql->whereIn('transaction_no', $filterBy['trans_number']);
        }

        if (!empty($filterBy['trans_type'])) {
            $sql = $sql->whereIn('output_tax.transaction_type', $filterBy['trans_type']);
        }

        if (!empty($filterBy['tax_type'])) {
            $sql = $sql->whereIn('tax_type', $filterBy['tax_type']);
        }

        
        if (!empty($filterBy['transac_from'])) {
            $sql = $sql->where('transaction_date', '>=', date("Y-m-d", strtotime($filterBy['transac_from'])));
        }

        if (!empty($filterBy['transac_to'])) {
            $sql = $sql->where('transaction_date', '<=', date("Y-m-d", strtotime($filterBy['transac_to'])));
        }

        $count = $sql->count();
        $final_result = array();
        $final_result['count'] = $count;
        $sql = $sql->skip((int) $page * (int) $pageSize)->take((int) $pageSize);
        $final_result['result'] = $sql->get(array('outward_id', 'products.product_title', 'transaction_no', 'master_lookup.master_lookup_name', 'tax_type', 'tax_percent', 'tax_amount', 'legalentity_warehouses.lp_wh_name', 'legalentity_warehouses.state'))->all();

        return $final_result;
    }

    public function getDataForReport($filterBy = '') {
        $sql = $this;
        $sql = $sql->join('products', 'products.product_id', '=', 'output_tax.product_id');
        $sql = $sql->join('legalentity_warehouses', 'legalentity_warehouses.le_wh_id', '=', 'output_tax.le_wh_id');
        $sql = $sql->join('master_lookup', 'master_lookup.value', '=', 'output_tax.transaction_type');

        if (!empty($filterBy['dc_name'])) {
            $sql = $sql->whereIn('output_tax.le_wh_id', $filterBy['dc_name']);
        }

        if (!empty($filterBy['state'])) {
            $sql = $sql->whereIn('legalentity_warehouses.state', $filterBy['state']);
        }

        if (!empty($filterBy['trans_number'])) {
            $sql = $sql->whereIn('transaction_no', $filterBy['trans_number']);
        }

        if (!empty($filterBy['trans_type'])) {
            $sql = $sql->whereIn('output_tax.transaction_type', $filterBy['trans_type']);
        }

        if (!empty($filterBy['tax_type'])) {
            $sql = $sql->whereIn('tax_type', $filterBy['tax_type']);
        }

        
        if (!empty($filterBy['transac_from'])) {
            $sql = $sql->where('transaction_date', '>=', date("Y-m-d", strtotime($filterBy['transac_from'])));
        }

        if (!empty($filterBy['transac_to'])) {
            $sql = $sql->where('transaction_date', '<=', date("Y-m-d", strtotime($filterBy['transac_to'])));
        }
        
        $final_result['result'] = $sql->get(array('outward_id', 'products.product_title', 'transaction_no', 'master_lookup.master_lookup_name', 'tax_type', 'tax_percent', 'tax_amount', 'legalentity_warehouses.lp_wh_name', 'legalentity_warehouses.state'))->all();

        return $final_result;
    }

/*

 */
    public function getDataForReportOlddd($startDate, $endDate)
    {

        $sql = $this;
        $sql = $sql->join('products', 'products.product_id', '=', 'output_tax.product_id');
        $sql = $sql->join('legalentity_warehouses', 'legalentity_warehouses.le_wh_id', '=', 'output_tax.le_wh_id');
        $sql = $sql->join('master_lookup', 'master_lookup.value', '=', 'output_tax.transaction_type');

        
        if (!empty($startDate)) {
            $sql = $sql->where('transaction_date', '>=', $startDate." 00:00:00");
        }

        if (!empty($endDate)) {
            $sql = $sql->where('transaction_date', '<=', $endDate." 23:59:00");
        }

        $final_result = array();
        $final_result['result'] = $sql->get(array('outward_id', 'products.product_title', 'transaction_no', 'master_lookup.master_lookup_name', 'tax_type', 'tax_percent', 'tax_amount', 'legalentity_warehouses.lp_wh_name', 'legalentity_warehouses.state'))->all();

        return $final_result;    
    }
}
