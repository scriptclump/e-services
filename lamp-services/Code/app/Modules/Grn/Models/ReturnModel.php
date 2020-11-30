<?php

namespace App\Modules\Grn\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\SerialNumber\Models\SerialNumber;
use Log;
use DB;
use Response;
use Session;
use Notifications;
use App\Modules\Grn\Models\Grn;
use App\Modules\Grn\Models\Inward;
use App\Modules\Indent\Models\LegalEntity;
use App\Modules\Orders\Models\Inventory;
use Utility;

class ReturnModel extends Model {
     
    public function getAllPurchaseReturns($inward_id, $rowCount = 0, $offset = 0, $perpage = 10) {
        try {
            $fieldArr = array(
                'purchase_returns.inward_id',
                'purchase_returns.pr_id',
                'purchase_returns.pr_code',
                'purchase_returns.pr_status',
                'purchase_returns.created_at as returnCreatedAt',
                'purchase_returns.pr_total_qty',
                'purchase_returns.pr_grand_total',
                DB::raw('GetUserName(purchase_returns.created_by,2) as user_name'),
                'inward.inward_code',
                'inward.created_at as inwardCreatedAt',
            );
            // prepare sql
            $query = DB::table('purchase_returns')->select($fieldArr);            
            $query->where('purchase_returns.inward_id', $inward_id);
            if ($rowCount) {
                $pr = $query->count();
            } else {
                $query->join('purchase_return_products', 'purchase_returns.pr_id', '=', 'purchase_return_products.pr_id');
                $query->join('inward', 'inward.inward_id', '=', 'purchase_returns.inward_id');
                $page = $perpage * $offset;
                $query->orderBy('purchase_returns.pr_id', 'desc');
                $query->groupBy('purchase_returns.pr_id');
                $query->skip($page)->take($perpage);
                $pr = $query->get()->all();
            }
            //echo $query->toSql();die;
            return $pr;
        } catch (Exception $e) {
            
        }
    }
    public function getReturnDetailById($returnId) {
        try {
            $fieldArr = array('returns.*', 'reurnpr.*',
                //'inward.le_wh_id',
                'inward.inward_code',
                'inward.created_at as inward_date',
                'legal.business_legal_name',
                'legal.address1',
                'legal.address2',
                'legal.city',
                'legal.pincode',
                'legal.le_code',
                'wh.lp_wh_name',
                'wh.address1 as dc_address1',
                'wh.address2 as dc_address2',
                'countries.name as country_name',
                'zone.name as state_name',
                DB::raw('GetUserName(returns.created_by,2) as createdBy'),
                DB::raw('(select mobile_no from users where users.legal_entity_id=returns.legal_entity_id ORDER BY created_at DESC LIMIT 1) as legalMobile'),
                DB::raw('(select email_id from users where users.legal_entity_id=returns.legal_entity_id ORDER BY created_at DESC LIMIT 1) as legalEmail'),
                'gdsp.sku',
                'gdsp.product_title',
                'gdsp.mrp',
            );
            $query = DB::table('purchase_returns as returns')->select($fieldArr);
            $query->leftJoin('inward', 'inward.inward_id', '=', 'returns.inward_id');
            $query->join('purchase_return_products as reurnpr', 'returns.pr_id', '=', 'reurnpr.pr_id');
            $query->join('products as gdsp', 'gdsp.product_id', '=', 'reurnpr.product_id');
            $query->join('legal_entities as legal', 'legal.legal_entity_id', '=', 'returns.legal_entity_id');
            $query->join('legalentity_warehouses as wh', 'wh.le_wh_id', '=', 'returns.le_wh_id');
            //$query->leftJoin('users as legalUser', 'legalUser.legal_entity_id', '=', 'returns.legal_entity_id');
            $query->leftJoin('countries', 'countries.country_id', '=', 'legal.country');
            $query->leftJoin('zone', 'zone.zone_id', '=', 'legal.state_id');
            $query->where('returns.pr_id', $returnId);
            //echo $query->toSql();die();
            $result = $query->get()->all();
            //print_r($result);die;
            return $result;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function getProductReturnQty($inwardId,$productId) {
        try {
            $fieldArr = array(
                DB::raw('SUM(reurnpr.qty) as ret_soh_qty'),
                DB::raw('SUM(reurnpr.dit_qty) as ret_dit_qty'),
                DB::raw('SUM(reurnpr.dnd_qty) as ret_dnd_qty')
            );
            $query = DB::table('purchase_returns as returns')->select($fieldArr);
            $query->join('purchase_return_products as reurnpr', 'returns.pr_id', '=', 'reurnpr.pr_id');
            $query->where('returns.inward_id', $inwardId);
            $query->where('reurnpr.product_id', $productId);
            #echo $query->toSql();
            return $query->first();
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function getReturnQtyByInwardId($inwardId) {
        try {
            $fieldArr = array(
                DB::raw('SUM(returns.pr_total_qty) as totReturnQty')
            );
            $query = DB::table('purchase_returns as returns')->select($fieldArr);
            $query->where('returns.inward_id', $inwardId);
            #echo $query->toSql();
            $totRet = $query->first();
            return $totRetQty = (isset($totRet->totReturnQty))?$totRet->totReturnQty:0;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    public function saveReturns($returnArr) {
        try {
            $pr_id = DB::table('purchase_returns')->insertGetId($returnArr);
            return $pr_id;
        } catch (Exception $ex) {
            
        }
    }
    public function saveReturnProducts($returnArr) {
        try {
            DB::table('purchase_return_products')->insert($returnArr);
        } catch (Exception $ex) {
            
        }
    }
    public function updateReturn($pr_id,$arr) {
        try {
            DB::table('purchase_returns')->where('pr_id',$pr_id)->update($arr);
        } catch (Exception $ex) {
            
        }
    }
}
