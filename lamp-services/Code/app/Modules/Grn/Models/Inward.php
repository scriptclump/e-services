<?php

namespace App\Modules\Grn\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Log;
use Session;
use App\Modules\Indent\Models\LegalEntity;
use App\Modules\Roles\Models\Role;
use App\Modules\PurchaseOrder\Models\PurchaseOrder;

class Inward extends Model {

    public function getTotalInward($filterBy = array(), $filter = array()) {
        $query = $this->getInwardGridList($filterBy, $filter, 1);
        if (!empty($filterBy)) {
            foreach ($filterBy as $key => $filterByEach) {
                if(!is_array($filterByEach)){
                    $filterByEachExplode = explode(' ', $filterByEach);    

                
                $length = count($filterByEachExplode);
                $filter_query_value = '';
                if ($length > 3) {
                    $filter_query_field = $filterByEachExplode[0];
                    $filter_query_operator = $filterByEachExplode[1];
                    for ($i = 2; $i < $length; $i++)
                        $filter_query_value .= $filterByEachExplode[$i] . " ";
                } else {
                    $filter_query_field = trim($filterByEachExplode[0]);
                    $filter_query_operator = $filterByEachExplode[1];
                    $filter_query_value = $filterByEachExplode[2];
                }
            }else {
                $filter_query_field = '';
                $filter_query_operator = '';
                $filter_query_value = '';
            }

                $operator_array = array('=', '!=', '>', '<', '>=', '<=');


            if (isset($filterBy['grnDate']) && $key=='grnDate') {
                $fdate = '';
                if (isset($filterBy['grnDate'][2]) && isset($filterBy['grnDate'][1]) && isset($filterBy['grnDate'][0])) {
                    $fdate = $filterBy['grnDate'][2] . '-' . $filterBy['grnDate'][1] . '-' . $filterBy['grnDate'][0];
                }
                if ($filterBy['grnDate']['operator'] == '=' && !empty($fdate)) {
                    $query = $query->whereBetween('inward.created_at', [$fdate . ' 00:00:00', $fdate . ' 23:59:59']);
                } else if (!empty($fdate) && $filterBy['grnDate']['operator'] == '<' || $filterBy['grnDate']['operator'] == '<=') {
                    $query = $query->where('inward.created_at', $filterBy['grnDate']['operator'], $fdate . ' 23:59:59');
                } else if (!empty($fdate)) {
                    $query = $query->where('inward.created_at', $filterBy['grnDate']['operator'], $fdate . ' 00:00:00');
                }
            }            
            else if ($filter_query_field == "dcname") {
                    $query = $query->where(DB::raw("getLeWhName(inward.le_wh_id)"), $filter_query_operator, $filter_query_value);
                     //dd($query->toSql());
                }
                else if ($filter_query_field == "createdBy") {
                    $query = $query->where(DB::raw("REPLACE(GetUserName(inward.created_by,2),'  ',' ')"), $filter_query_operator, $filter_query_value);
                    // dd($query->toSql());
                }else if ($filter_query_field == "grnvalue") {
                    $query = $query->where(DB::raw('ROUND(inward.grand_total,2)'), $filter_query_operator, trim($filter_query_value));
                }else if ($filter_query_field == "povalue") {
                    $query = $query->where(DB::raw('ROUND((select sum(po_products.sub_total) from po_products where po_products.po_id=po.po_id),2)'), $filter_query_operator, trim($filter_query_value));
                }else if ($filter_query_field == "item_discount_value") {
                    $query = $query->where(DB::raw('ROUND((select sum(inward_products.discount_total) from inward_products where inward_products.inward_id=inward.inward_id),2)'), $filter_query_operator, trim($filter_query_value));
                } else if($filter_query_field!=''){
                    $query = $query->where($filter_query_field, $filter_query_operator, trim($filter_query_value));
                }
            }
        }
        $query->groupBy('inward.inward_id');                
        return count($query->get()->all());
    }
    public function getInwardCountByStatus($filterBy = array(), $filter = array()) {
        $query = $this->getInwardGridList($filterBy, $filter, 0);
        $data = $query->get()->all();
        return count($data);
        //echo '<pre/>';print_r(DB::getQueryLog());die;
    }

    public function getInwardSuppliersList()
    {
        //query to retrieve all suppliers list for Export GRN
        $query = DB::table('legal_entities')
                    ->select('legal_entities.business_legal_name as supplier_name','legal_entities.legal_entity_id')
                    ->leftjoin('suppliers','legal_entities.legal_entity_id','=','suppliers.legal_entity_id')
                    ->where('legal_entity_type_id','=','1002')
                    ->where('suppliers.is_active','=','1')
                    ->get()->all();
        return $query;
    }

    public function getAllInward($filterBy = array(), $filter = array(), $count = 0, $page = 1, $pageSize = 10, $orderbyarray = array()) {
        $query = $this->getInwardGridList($filterBy, $filter, 0, $filterBy);
        #echo $query->toSql();die;
        if ($count) {
            return $query->count();
        } else {
            // $query->orderBy('inward.inward_id', 'DESC');
            if (!empty($orderbyarray)) {
                $orderClause = explode(" ", $orderbyarray);
                $query = $query->orderby($orderClause[0], $orderClause[1]);  //order by query 
            }else
            {
                $query = $query->orderby('inward_code', 'desc');  //order by query 
            }
            if (!empty($filterBy)) {
                foreach ($filterBy as $key=>$filterByEach) {
                    if(!is_array($filterByEach)){
                    $filterByEachExplode = explode(' ', $filterByEach);
                    $length = count($filterByEachExplode);
                    $filter_query_value = '';
                    if ($length > 3) {
                        $filter_query_field = $filterByEachExplode[0];
                        $filter_query_operator = $filterByEachExplode[1];
                        for ($i = 2; $i < $length; $i++)
                            $filter_query_value .= $filterByEachExplode[$i] . " ";
                    } else {
                        $filter_query_field = $filterByEachExplode[0];
                        $filter_query_operator = $filterByEachExplode[1];
                        $filter_query_value = $filterByEachExplode[2];
                    }
                }else {
                    $filter_query_field = '';
                    $filter_query_operator = '';
                    $filter_query_value = '';
                }
                
                    $operator_array = array('=', '!=', '>', '<', '>=', '<=');
                    
                    if (isset($filterBy['grnDate']) && $key=='grnDate') {
                        $fdate = '';
                        if (isset($filterBy['grnDate'][2]) && isset($filterBy['grnDate'][1]) && isset($filterBy['grnDate'][0])) {
                            $fdate = $filterBy['grnDate'][2] . '-' . $filterBy['grnDate'][1] . '-' . $filterBy['grnDate'][0];
                        }
                        if ($filterBy['grnDate']['operator'] == '=' && !empty($fdate)) {
                            $query = $query->whereBetween('inward.created_at', [$fdate . ' 00:00:00', $fdate . ' 23:59:59']);
                        } else if (!empty($fdate) && $filterBy['grnDate']['operator'] == '<' || $filterBy['grnDate']['operator'] == '<=') {
                            $query = $query->where('inward.created_at', $filterBy['grnDate']['operator'], $fdate . ' 23:59:59');
                        } else if (!empty($fdate)) {
                            $query = $query->where('inward.created_at', $filterBy['grnDate']['operator'], $fdate . ' 00:00:00');
                        }
                        
                    }else if ($filter_query_field == "dcname") {
                        $query = $query->where(DB::raw("getLeWhName(inward.le_wh_id)"), $filter_query_operator, $filter_query_value);
                     //dd($query->toSql());
                
                    }else if ($filter_query_field == "createdBy") {
                        $query = $query->where(DB::raw("REPLACE(GetUserName(inward.created_by,2),'  ',' ')"), $filter_query_operator, trim($filter_query_value));
                        // dd($query->toSql());
                    }  else if ($filter_query_field == "grnvalue") {
                        $query = $query->where(DB::raw('ROUND(inward.grand_total,2)'), $filter_query_operator, trim($filter_query_value));
                    }else if ($filter_query_field == "povalue") {
                        $query = $query->where(DB::raw('ROUND((select sum(po_products.sub_total) from po_products where po_products.po_id=po.po_id),2)'), $filter_query_operator, trim($filter_query_value));
                    }else if ($filter_query_field == "item_discount_value") {
                        $query = $query->where(DB::raw('ROUND((select sum(inward_products.discount_total) from inward_products where inward_products.inward_id=inward.inward_id),2)'), $filter_query_operator, trim($filter_query_value));
                    } else  if($filter_query_field!=''){
                        $query = $query->where($filter_query_field, $filter_query_operator, trim($filter_query_value));
                    }
                }
            }

            // $query->skip($offset)->take($perpage);
            $query->skip(($page * $pageSize))->take($pageSize);
            return $query->get()->all();
        }
    }

    public function getInwardGridList($filterBy, $filter = array(), $forCount) {
//        $_leModel = new LegalEntity();
//        $supplierIds = $_leModel->getSupplierId();
        $this->_roleModel = new Role();
        $Json = json_decode($this->_roleModel->getFilterData(6), 1);
        $filters = json_decode($Json['sbu'], 1);
        $dc_acess_list = isset($filters['118001']) ? $filters['118001'] : 'NULL';
        $legalentityId = Session::get('legal_entity_id');
        $legal_entity_type_id = [1002,1014,1016];
        if($forCount)
        {
            $fieldArr = array('inward.inward_id');
        }else{
            $fieldArr = array('inward.*', 'legal.business_legal_name', 
                //'users.firstname','users.lastname', 
                'currency.symbol_left as symbol', 'po.po_code as poCode',
                DB::raw('GetUserName(inward.created_by,2) as createdBy'),
                DB::raw('getLeWhName(inward.le_wh_id) as dcname'),
            // DB::raw('SUM((((products.received_qty - products.free_qty) * products.price) + products.tax_amount) - products.discount_total) as povalue'),
            DB::raw('(select sum(po_products.sub_total) from po_products where po_products.po_id=po.po_id) as povalue'),
            DB::raw('SUM(products.discount_total) as item_discount_value'),
                'inward.grand_total as grnvalue',
            'po.po_code', 'po_invoice_grid.invoice_code'
            );
        }
        $query = DB::table('inward')->select($fieldArr);
        $query->leftjoin('legal_entities as legal', 'legal.legal_entity_id', '=', 'inward.legal_entity_id');
        $query->leftjoin('inward_products as products', 'products.inward_id', '=', 'inward.inward_id');
        //$query->leftjoin('users', 'users.user_id', '=', 'inward.created_by');
        $query->leftjoin('currency', 'currency.currency_id', '=', 'inward.currency_id');
        $query->leftjoin('po', 'po.po_id', '=', 'inward.po_no');        
        $query->leftjoin('po_invoice_grid', 'inward.inward_id', '=', 'po_invoice_grid.inward_id');
        $query->whereIn('legal.legal_entity_type_id' , $legal_entity_type_id);
        
        $user_id = Session::get("userId");
        // print_r($brands);die;
        $poObj = new PurchaseOrder();
        $userData = $poObj->checkUserIsSupplier($user_id);
        if (count($userData) == 0) {
            //$query->whereIn('po.legal_entity_id', $suppliers);
            $query->whereIn('po.le_wh_id', explode(',',$dc_acess_list));
        }
        if(count($userData) > 0){
            $brands = $poObj->getAllAccessBrands($user_id);
            $query->leftJoin('products as pro', 'pro.product_id', '=', 'products.product_id');
            $brands = implode(',',$brands);
            $query->whereIn('pro.brand_id', explode(',',$brands));
            $globalSupperLier = DB::table('master_lookup')->select('description')->where('value',78023)->get()->all();
            $globalSupperLierId = isset($globalSupperLier[0]->description)?$globalSupperLier[0]->description:'NULL';
            $query->whereNotIn('inward.legal_entity_id', [$globalSupperLierId]);
        }

        if (isset($filter['status_id']) && $filter['status_id']=='invoiced') {            
            $query->where('po_invoice_grid.inward_id','!=', '');
        }
        if (isset($filter['status_id']) && $filter['status_id']=='notinvoiced') {            
//            $query->leftjoin('po_invoice_grid', 'inward.inward_id', '=', 'po_invoice_grid.inward_id');
            $query->whereNull('po_invoice_grid.inward_id');
        }
        if (isset($filter['status_id']) && $filter['status_id']=='approved') {
            $query->where('inward.approval_status', 1);
        }
        if (isset($filter['status_id']) && $filter['status_id']=='notapproved') {
            $query->where('inward.approval_status','!=', 1);
        }
//        if (count($supplierIds)) {
//            $query->whereIn('inward.legal_entity_id', $supplierIds);
//        }
        if (!$forCount) {
            $query->groupBy('inward.inward_id');
        }
        return $query;
    }

	public function getProductPackInfo($inwardPrdId) {
		try{
			$fieldArr = array('detail.exp_date','detail.mfg_date',
				'detail.freshness_per','master_lookup.master_lookup_name as pack_level',
				'detail.pack_qty', 'detail.received_qty',
				'detail.tot_rec_qty');

			$query = DB::table('inward_product_details as detail')->select($fieldArr);
			$query->leftJoin('master_lookup', 'master_lookup.value', '=', 'detail.pack_level');
			$query->where('detail.inward_prd_id', $inwardPrdId);
			#echo $query->toSql();
			return $query->get()->all();
		}
		catch(Exception $e) {
			Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}

	public function getInwardDetailById($inwardId) {
		try{
			$fieldArr = array('inward.*', 'product.*',
                                                        'pop.uom',
                                                        'pop.no_of_eaches',
							'legal.business_legal_name',
                            'legal.gstin',
							'legal.address1',
							'legal.address2',
                            'legal.state_id',
							'legal.city',
							'legal.pincode',
							'legal.le_code',
							'wh.lp_wh_name',
							'wh.address1 as dc_address1',
							'wh.address2 as dc_address2',
							'countries.name as country_name',
							'zone.name as state_name',
                            'zone.code as state_code',
							'users.firstname',
							'users.lastname',
                                                        DB::raw('(select users.mobile_no from users where users.legal_entity_id=inward.legal_entity_id limit 1) as legalMobile'),
                                                        DB::raw('(select users.email_id from users where users.legal_entity_id=inward.legal_entity_id limit 1) as legalEmail'),
							'currency.symbol_left as symbol',
							'gdsp.sku',
							'gdsp.upc',
							'gdsp.seller_sku',
							'gdsp.product_title',
							'gdsp.mrp',
							'tot.dlp',
							'tot.base_price',
                                                        'po.po_code', 'po_invoice_grid.invoice_code', 
                                                        'po_invoice_grid.created_at as po_invoice_created_at',
                                                        DB::raw('po.po_date as po_created_date')
							);

			$query = DB::table('inward')->select($fieldArr);
			$query->join('inward_products as product', 'inward.inward_id', '=', 'product.inward_id');
			$query->join('products as gdsp', 'gdsp.product_id', '=', 'product.product_id');
                        $query->leftJoin('product_tot as tot', function($join)
                        {
                            $join->on('gdsp.product_id','=','tot.product_id');
                            $join->on('tot.supplier_id','=','inward.legal_entity_id');
                            $join->on('tot.le_wh_id','=','inward.le_wh_id');
                        });
                        $query->leftJoin('po', 'po.po_id', '=', 'inward.po_no');
                        $query->leftJoin('po_invoice_grid', 'po_invoice_grid.inward_id', '=', 'inward.inward_id');
                        $query->leftJoin('po_products as pop', function($join)
                        {
                            $join->on('product.product_id','=','pop.product_id');
                            $join->on('inward.po_no','=','pop.po_id');
                        });
			$query->join('legal_entities as legal', 'legal.legal_entity_id', '=', 'inward.legal_entity_id');
			$query->join('legalentity_warehouses as wh', 'wh.le_wh_id', '=', 'inward.le_wh_id');
			$query->leftJoin('users', 'users.user_id', '=', 'inward.created_by');
			$query->leftJoin('currency', 'currency.currency_id', '=', 'inward.currency_id');
			$query->leftJoin('countries', 'countries.country_id', '=', 'legal.country');
			$query->leftJoin('zone', 'zone.zone_id', '=', 'legal.state_id');
			$query->where('inward.inward_id', $inwardId);
			#echo $query->toSql();
			return $query->get()->all();
		}
		catch(Exception $e) {
			Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}
        
    public function getInwardDetailsById($inwardId)
    {
        try
        {
            $fieldArr = ['inward.grand_total', 
                'inward.discount_on_total', 
                'inward.discount_before_tax', 
				'products.product_type_id',
                'ip.tax_per',
                'ip.tax_amount',
                'ip.discount_total',
                'ip.sub_total',
				'ip.tax_data',
                'inward.inward_id',
                'inward.inward_code',
                'inward.le_wh_id',
                'le.le_code',
                'le.business_legal_name',
                'po.po_code',
                'po.is_stock_transfer',
                'po.stock_transfer_dc',
                'le.state_id as sup_state_id',
                DB::raw('po.created_at as po_created_date'),
                'po.po_id',
                'inward.created_at',
                'inward.invoice_date',
                'inward.inward_code',
                'poig.invoice_code',
                'inward.shipping_fee',
                DB::raw('poig.created_at as po_invoice_created_at'),
                DB::raw('(SELECT GROUP_CONCAT(CASE WHEN (doc_ref_no="0" OR doc_ref_no="") THEN NULL ELSE doc_ref_no END) FROM inward_docs WHERE inward_id = inward.`inward_id`) AS reference_docs')
                ];
            $query = DB::table('inward')->select($fieldArr);
            $query->join('inward_products as ip', 'inward.inward_id', '=', 'ip.inward_id')
					->leftJoin('products', 'products.product_id', '=', 'ip.product_id');			
            $query->join('po', 'po.po_id', '=', 'inward.po_no');            
            $query->join('po_invoice_grid as poig', 'poig.inward_id', '=', 'inward.inward_id');            
            $query->join('legal_entities as le', 'le.legal_entity_id', '=', 'inward.legal_entity_id');            
            $query->where('inward.inward_id', $inwardId);
            return $query->get()->all();
        } catch (Exception $e)
        {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    public function getLedgerName($accountType, $taxPercentage,$accountNumber = null)
        {
            try
            {
                //DB::enableQueryLog();
                if($accountNumber)
                {
                    $temp = DB::table('tally_ledger_master')
                        ->where('tlm_name', 'like', $accountNumber.'%')
                        ->where('tlm_name', 'like', '%'.$accountType.'%')
                        ->where('tlm_name', 'like', '%'.$taxPercentage.'%')
                        ->orderBy('sync_date', 'DESC')
                        ->first(['tlm_name']);
                }else{
                    $temp = DB::table('tally_ledger_master')
//                        ->where('tlm_name', 'like', $accountNumber.'%')
                        ->where('tlm_name', 'like', '%'.$accountType.'%')
                        ->where('tlm_name', 'like', '%'.$taxPercentage.'%')
                        ->orderBy('sync_date', 'DESC')
                        ->first(['tlm_name']);
                }
                //\Log::info(DB::getQueryLog());
                return $temp;
            }catch(Exception $e) {
                \Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            }
        }
        
        public function getLedgerGroupName($accountNumber)
        {
            try
            {
                DB::enableQueryLog();
                if($accountNumber)
                {
                    $temp = DB::table('tally_ledger_master')
                        ->where('tlm_name', $accountNumber)
                        ->orderBy('sync_date', 'DESC')
                        ->first(['tlm_group']);
                }
//                \Log::info(DB::getQueryLog());
                return $temp;
            }catch(Exception $e) {
                \Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            }
        }

	public function getInwardCodeById($inwardId) {
		try{
			$fieldArr = array('inward.inward_code');

			$query = DB::table('inward')->select($fieldArr);
			$query->where('inward.inward_id', $inwardId);
			#echo $query->toSql();
			return $query->first();
		}
		catch(Exception $e) {
			Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}

	public function getTotalInwardQtyById($poId) {
		try{
			$fieldArr = array(DB::raw('SUM(inward_products.received_qty) as totQty'));

			$query = DB::table('inward_products')
                                ->leftJoin('inward', 'inward.inward_id','=','inward_products.inward_id')
                                ->where('inward.po_no', $poId)
                                ->select($fieldArr)
                                ->groupBy('inward.po_no');
			$row = $query->first();
			return isset($row->totQty) ? (int)$row->totQty : 0;
		}
		catch(Exception $e) {
			Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}

  public function getTotalRecQtyByInwardId($inward_id, $product_id) {
		try{
      $fieldArr = array(DB::raw('SUM(prp.qty) as totQty'));

			$query = DB::table('purchase_returns as pr')->select($fieldArr)
              ->leftJoin('purchase_return_products as prp', 'pr.pr_id','=','prp.pr_id');

      $query->where('pr.inward_id', $inward_id);
      $query->where('prp.product_id', $product_id);

			#echo $query->toSql();
			$row = $query->first();
			return isset($row->totQty) ? (int)$row->totQty : 0;
		}
		catch(Exception $e) {
			Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}

	public function getUserEmailByRoleName($roleName) {
        try {
            $query = DB::table('users')->select('users.email_id');
            $query->join('user_roles', 'users.user_id', '=', 'user_roles.user_id');
            $query->join('roles', 'roles.role_id', '=', 'user_roles.role_id');
            $query->where('users.is_active', 1);
            return $query->whereIn('roles.name', $roleName)->get()->all();
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

	public function getActiveInwards() {
    	try{
    		$fields = array('inward.inward_id', 'inward.inward_code');
    		$query = DB::table('inward')->select($fields);
//    	$query->whereIn('inward.inward_status', array('76002'));
    		$query->orderBy('inward.created_at','desc');
			return $query->get()->all();
    	} catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }

    public function getInwardSupplierProductList($inwardId){
      try{
        $legal_entity_id = \Session::get('legal_entity_id');
            if($inwardId > 0)
            {
                $supplierList = DB::table('legal_entities')
                        ->join('inward', 'inward.legal_entity_id', '=', 'legal_entities.legal_entity_id')
                        ->where(['inward.inward_id' => $inwardId, 'legal_entities.legal_entity_type_id' => 1002, 'legal_entities.is_approved' => 1, 'parent_id'=>$legal_entity_id])
                        ->get(['legal_entities.legal_entity_id', 'legal_entities.business_legal_name'])->all();

                $warehouseList = DB::table('legalentity_warehouses')
                        ->join('inward', 'inward.le_wh_id', '=', 'legalentity_warehouses.le_wh_id')
                        ->where(['inward.inward_id' => $inwardId,'legalentity_warehouses.legal_entity_id' => $legal_entity_id])
                        ->get(['legalentity_warehouses.lp_wh_name', 'legalentity_warehouses.le_wh_id'])->all();


                $products = DB::table('inward_products as inwardprod')
                        ->where(['inwardprod.inward_id' => $inwardId])
                        ->leftJoin('products','products.product_id','=','inwardprod.product_id')
                        ->leftJoin('inward','inward.inward_id','=','inwardprod.inward_id')
                        ->leftJoin('brands','products.brand_id','=','brands.brand_id')
                        ->leftJoin('product_tot as tot', function($join)
                        {
                            $join->on('products.product_id','=','tot.product_id');
                            $join->on('tot.supplier_id','=','inward.legal_entity_id');
                            $join->on('tot.le_wh_id','=','inward.le_wh_id');
                        })
                        ->leftJoin('currency','tot.currency_id','=','currency.currency_id')
                        ->select('products.product_title', 'inwardprod.*', 'quarantine_stock','products.sku', 'products.seller_sku', 'products.upc', 'products.mrp','brands.brand_id', 'products.pack_size','tot.dlp','tot.base_price','currency.symbol_right as symbol')
                        ->get()->all();


                foreach($products as $k=>$product) {

                      $tot_rec_qty = $this->getTotalRecQtyByInwardId($product->inward_id, $product->product_id);

                      $rem_qty = $product->received_qty - $tot_rec_qty;


                      if($rem_qty>0)
                      {
                        $products[$k]->rem_qty = $rem_qty;
                      } else {
                        unset($products[$k]);
                      }

                }

                $data=array('supplierList'=>$supplierList,'warehouseList'=>$warehouseList,'products'=>$products);
                return $data;
            }



          } catch (\ErrorException $ex) {
                Log::error($ex->getMessage().' '.$ex->getTraceAsString());
            }


    }
    public function saveVoucher($voucherArr) {
        try {
            $voucher_id = DB::table('vouchers')->insert($voucherArr);
        } catch (Exception $ex) {
            
        }
    }
    
    public function createVoucher($date)
    {
        try
        {
            $inwardCollection = \DB::table('po')
                    ->leftJoin('inward', 'inward.po_no', '=', 'po.po_id')
                    ->where(DB::raw('DATE(inward.created_at)'), '>=', $date)
                    ->whereNotNUll('inward.inward_code')
                    ->select('po.created_at', 'po_date', 'po.po_code', 'inward.inward_code', 'inward.inward_id')
                    ->orderBy('inward.inward_id', 'DESC')
                    ->get()->all();
            if(!empty($inwardCollection))
            {
                foreach($inwardCollection as $inwardDetails)
                {
                    $inwardId = $inwardDetails->inward_id;
                    $inwardCode = $inwardDetails->inward_code;
                    if($inwardId > 0)
                    {
                        $checkInvoice = DB::table('po_invoice_grid')->where('inward_id', $inwardId)->select('po_invoice_grid_id')->first();
                        if(empty($checkInvoice))
                        {
                            app('App\Modules\PurchaseOrder\Controllers\PurchaseInvoiceController')->createInvoiceByinwardId($inwardId);
                            //$purchaseInvoiceData = DB::table('po_invoice_grid')->where('inward_id', $inwardId)->select('po_invoice_grid_id')->first();
                            //$purchaseInvoiceId = $purchaseInvoiceData->po_invoice_grid_id;
                            //app('App\Modules\PurchaseOrder\Controllers\PurchaseInvoiceController')->creatPaymentVoucher($purchaseInvoiceId);
                        }
                        $checkVoucher = DB::table('vouchers')->where('reference_no', $inwardCode)->select('voucher_id')->first();
                        if(empty($checkVoucher))
                        {
                            app('App\Modules\Grn\Controllers\GrnController')->creatPurchaseVoucher($inwardId);
                        }
                    }                    
                }
            }
            return "done";
        } catch (\ErrorException $ex) {
            \Log::error($ex->getMessage());
            \Log::error($ex->getTraceAsString());
        }
    }
    public function getInwardProductById($inwardId, $productId) {
        try {
            if ($inwardId > 0 && $productId > 0) {
                $products = DB::table('inward_products as inwardprod')
                        ->leftJoin('products', 'products.product_id', '=', 'inwardprod.product_id')
                        ->leftJoin('input_tax as tax', function($join) {
                            $join->on('inwardprod.inward_id', '=', 'tax.inward_id');
                            $join->on('inwardprod.product_id', '=', 'tax.product_id');
                        })
                        ->select('inwardprod.*', 'products.mrp', 'tax.tax_type')
                        ->where(['inwardprod.inward_id' => $inwardId])
                        ->where(['inwardprod.product_id' => $productId])
                        ->first();
                return $products;
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage() . ' ' . $ex->getTraceAsString());
        }
    }
    public function getPOIdByInwardId($inwardId) {
        try {
            if ($inwardId>0) {                
                $fieldArr = [
                    'po.po_code',
                    'po.po_id',
                    'po.approval_status',
                ];
                $query = DB::table('inward')->select($fieldArr);
                $query->join('po', 'po.po_id', '=', 'inward.po_no');
                $query->where('inward.inward_id', $inwardId);
                $products = $query->first();
                return $products;
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage() . ' ' . $ex->getTraceAsString());
        }
    }
    
    public function sendErrorVoucherEntryMail($grnCode, $voucher)
    {
        try
        {
            // send email
                $body = array('template'=>'emails.grncomment', 'attachment'=>'', 'name'=>'Hello All', 'comment'=> $voucher);

//                $userEmailArr = $this->_inwardModel->getUserEmailByRoleName(['Logistics Manager', 'Finance Manager']);
                $toEmails = array();
                $toEmails[] = 'raju.aavudoddi@ebutor.com';
                
                /*if(is_array($userEmailArr) && count($userEmailArr) > 0) {
                    foreach($userEmailArr as $userData){
                        $toEmails[] = $userData->email_id;
                    }
                }*/
                $subject = env('MAIL_ENV').'Voucher with wrong round off - GRN#'.$grnCode.' '.date('d-m-Y');
                \Utility::sendEmail($toEmails, $subject, $body);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getInwardDetail($grnId) {
        try
        {
            $result = [];
            if($grnId > 0)
            {
                $result = DB::table('inward')
                        ->leftJoin('inward_products', 'inward_products.inward_id', '=' , 'inward.inward_id')
                        ->leftJoin('legal_entities', 'legal_entities.legal_entity_id', '=' , 'inward.legal_entity_id')
                        ->leftJoin('po', 'po.po_id', '=', 'inward.po_no')
                        ->leftJoin('legalentity_warehouses', 'legalentity_warehouses.le_wh_id', '=', 'inward.le_wh_id')
                        ->where(['inward.inward_id' => $grnId])
                        ->select('legal_entities.business_legal_name', 'po.po_code',
                                'inward.*', 'legalentity_warehouses.lp_wh_name', DB::raw('SUM(inward_products.tax_amount) as total_tax_amount')
                                , DB::raw('SUM(inward_products.received_qty) as total_received_qty'))
                        ->first();
            }
            return $result;
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

     public function getInwardTaxDetailsById($inwardId)
    {
        try
        {
            $fieldArr = ['inward.grand_total', 
                'inward.discount_on_total', 
                'inward.discount_before_tax', 
                'products.product_type_id',
                'ip.tax_per',
                'ip.tax_amount',
                'ip.discount_total',
                'ip.sub_total',
                'ip.tax_data',
                'inward.inward_id',
                'inward.inward_code',
                'inward.le_wh_id',
                'le.le_code',
                'le.business_legal_name',
                'po.po_code',
                DB::raw('po.created_at as po_created_date'),
                'po.po_id',
                'inward.created_at',
                'inward.inward_code',
                'poig.invoice_code',
                'inward.shipping_fee',
                DB::raw('poig.created_at as po_invoice_created_at'),
                DB::raw('(SELECT GROUP_CONCAT(CASE WHEN (doc_ref_no="0" OR doc_ref_no="") THEN NULL ELSE doc_ref_no END) FROM inward_docs WHERE inward_id = inward.`inward_id` and doc_ref_type = 95001) AS reference_docs'),
                DB::raw('SUM(JSON_EXTRACT(tax_data, "$.CGST_VALUE")) AS CGST'),
                DB::raw('SUM(JSON_EXTRACT(tax_data, "$.SGST_VALUE")) AS SGST'),
                DB::raw('SUM(JSON_EXTRACT(tax_data, "$.IGST_VALUE")) AS IGST'),
                DB::raw('SUM(JSON_EXTRACT(tax_data, "$.UTGST_VALUE")) AS UTGST'),
                DB::raw('SUM(sub_total) AS SUBTOTAL')
                ];
            $query = DB::table('inward')->select($fieldArr);
            $query->join('inward_products as ip', 'inward.inward_id', '=', 'ip.inward_id')
                    ->leftJoin('products', 'products.product_id', '=', 'ip.product_id');            
            $query->join('po', 'po.po_id', '=', 'inward.po_no');            
            $query->join('po_invoice_grid as poig', 'poig.inward_id', '=', 'inward.inward_id');            
            $query->join('legal_entities as le', 'le.legal_entity_id', '=', 'inward.legal_entity_id');
            $query->groupBy('ip.tax_per','inward.inward_id');         
            $query->where('inward.inward_id', $inwardId);
            return $query->get()->all();
        } catch (Exception $e)
        {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }


}
?>
