<?php
/*

 */
namespace App\Modules\TaxReport\Models;

use Illuminate\Database\Eloquent\Model;
use Session;
use DB;
use App\Modules\TaxReport\Models\OutputTax;
use Log;

class InputTax extends Model {

    protected $table = 'input_tax';
    protected $primaryKey = 'input_tax_id';

    public function inwardGridData($page, $pageSize, $filterBy = '') {
        
        $sql = $this;
        $sql = $sql->join('products', 'products.product_id', '=', 'input_tax.product_id');
        $sql = $sql->join('legalentity_warehouses', 'legalentity_warehouses.le_wh_id', '=', 'input_tax.le_wh_id');
        $sql = $sql->join('master_lookup', 'master_lookup.value', '=', 'input_tax.transaction_type');

        if (!empty($filterBy['dc_name'])) {
            $sql = $sql->whereIn('input_tax.le_wh_id', $filterBy['dc_name']);
        }

        if (!empty($filterBy['state'])) {
            $sql = $sql->whereIn('legalentity_warehouses.state', $filterBy['state']);
        }

        if (!empty($filterBy['trans_number'])) {
            $sql = $sql->whereIn('transaction_no', $filterBy['trans_number']);
        }

        if (!empty($filterBy['trans_type'])) {
            $sql = $sql->whereIn('input_tax.transaction_type', $filterBy['trans_type']);
        }

        if (!empty($filterBy['tax_type'])) {
            $sql = $sql->whereIn('tax_type', $filterBy['tax_type']);
        }
        // echo date("Y-m-d", strtotime($filterBy['transac_from']))." <br> diff <br>  ". date("Y-m-d", strtotime($filterBy['transac_to']));die;
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
        $final_result['result'] = $sql->get(array('inward_id', 'products.product_title', 'transaction_no', 'input_tax.transaction_type', 'master_lookup.master_lookup_name', 'tax_type', 'tax_percent', 'tax_amount', 'input_tax.le_wh_id', 'legalentity_warehouses.lp_wh_name', 'legalentity_warehouses.state'))->all();
        return $final_result;
    }

    public function inwardGridDataExport($filterBy) {
        $sql = $this;
        $sql = $sql->join('products', 'products.product_id', '=', 'input_tax.product_id');
        $sql = $sql->join('legalentity_warehouses', 'legalentity_warehouses.le_wh_id', '=', 'input_tax.le_wh_id');
        $sql = $sql->join('master_lookup', 'master_lookup.value', '=', 'input_tax.transaction_type');

        if (!empty($filterBy['dc_name'])) {
            $sql = $sql->whereIn('input_tax.le_wh_id', $filterBy['dc_name']);
        }

        if (!empty($filterBy['state'])) {
            $sql = $sql->whereIn('legalentity_warehouses.state', $filterBy['state']);
        }

        if (!empty($filterBy['trans_number'])) {
            $sql = $sql->whereIn('transaction_no', $filterBy['trans_number']);
        }

        if (!empty($filterBy['trans_type'])) {
            $sql = $sql->whereIn('input_tax.transaction_type', $filterBy['trans_type']);
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
        

        $final_result['result'] = $sql->get(array('inward_id', 'products.product_title', 'transaction_no', 'input_tax.transaction_type', 'master_lookup.master_lookup_name', 'tax_type', 'tax_percent', 'tax_amount', 'input_tax.le_wh_id', 'legalentity_warehouses.lp_wh_name', 'legalentity_warehouses.state'))->all();
        
        return $final_result;
    }
/*

 */
    public function getDataForReport($startDate, $endDate)
    {

        $sql = $this;
        $sql = $sql->join('products', 'products.product_id', '=', 'input_tax.product_id');
        $sql = $sql->join('legalentity_warehouses', 'legalentity_warehouses.le_wh_id', '=', 'input_tax.le_wh_id');
        $sql = $sql->join('master_lookup', 'master_lookup.value', '=', 'input_tax.transaction_type');

        if (!empty($startDate)) {
            $sql = $sql->where('input_tax.created_at', '>=', $startDate." 00:00:00");
        }

        if (!empty($endDate)) {
            $sql = $sql->where('input_tax.created_at', '<=', $endDate." 23:59:00");
        }

        $final_result = array();
        $final_result['result'] = $sql->get(array('inward_id', 'products.product_title', 'transaction_no', 'input_tax.transaction_type', 'master_lookup.master_lookup_name', 'tax_type', 'tax_percent', 'tax_amount', 'input_tax.le_wh_id', 'legalentity_warehouses.lp_wh_name', 'legalentity_warehouses.state'))->all();
        return $final_result;
    
    }
/*

 */
    public function filterOptions() {
        $filter_array = Array();
        $warehouses_table = DB::table('legalentity_warehouses');
        $this->_outputTaxObj = new OutputTax();

        if (Session('roleId') != '1') {
            $warehouses_table = $warehouses_table->where('legal_entity_id', Session::get('legal_entity_id'));
        }

        $filter_array['dc_name'] = $warehouses_table->where('lp_wh_name', '!=', NULL)->where('lp_wh_name', '!=', '')->orderBy('lp_wh_name', 'asc')->pluck('lp_wh_name', 'le_wh_id');
        $filter_array['tax_type'] = DB::table('master_lookup')->where('mas_cat_id', '=', 9)->orderBy('master_lookup_name', 'asc')->pluck('master_lookup_name');
        $filter_array['inward_id'] = $this->distinct('inward_id')->pluck('inward_id');
        $filter_array['state'] = DB::table('zone')->where('country_id', 99)->where('status', 1)->orderBy("sort_order", "asc")->pluck('name', 'zone_id');
        $filter_array['outward_id'] = $this->_outputTaxObj->distinct('outward_id')->pluck('outward_id');
        $filter_array['transaction_no'] = $this->distinct('transaction_no')->where('transaction_no', '!=', NULL)->where('transaction_no', '!=', '')->pluck('transaction_no');
        $filter_array['transaction_no_out'] = $this->_outputTaxObj->distinct('transaction_no')->where('transaction_no', '!=', NULL)->where('transaction_no', '!=', '')->pluck('transaction_no');
        $filter_array['transaction_type'] = DB::table('master_lookup')->where('mas_cat_id', '=', 101)->orderBy('master_lookup_name', 'asc')->pluck('master_lookup_name', 'value');
        return $filter_array;
    }
/*

 */
    public function getStateName($stateId) {
        $state_name = json_decode(json_encode(DB::table('zone')->where('zone_id', $stateId)->pluck('name')), true);
        return $state_name[0];
    }

}
