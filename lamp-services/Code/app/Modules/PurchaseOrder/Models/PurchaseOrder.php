<?php

namespace App\Modules\PurchaseOrder\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Grn\Models\Grn;
use App\Modules\Indent\Models\IndentModel;
use App\Modules\SerialNumber\Models\SerialNumber;
use App\Modules\Roles\Models\Role;
use Log;
use DB;
use Response;
use Session;
use Notifications;
use App\Modules\Indent\Models\LegalEntity;
use Mail;

use Utility;
use Lang;
use  App\Central\Repositories\CustomerRepo;
use App\Central\Repositories\roleRepo;
use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;


date_default_timezone_set('Asia/Kolkata');
class PurchaseOrder extends Model
{
    protected $table = "po";
    protected $primaryKey = 'po_id';
    protected $fillable = array('legal_entity_id', 'po_type','po_code', 'po_status', 'le_wh_id', 'currency_id','delivery_date'
                    ,'po_validity','po_remarks','created_by', 'indent_id', 'exp_delivery_date','po_date','payment_due_date'
                    ,'platform','payment_mode','payment_type','payment_refno','tlm_name','tlm_group','logistics_cost','approval_status'
                    ,'apply_discount_on_bill','discount_type','discount','discount_before_tax','supply_le_wh_id','is_stock_transfer','stock_transfer_dc');

    protected $_SNumberModel;

    // protected $_ebutorDc;

    // public function __construct(){
    //     $this->_ebutorDc = [ 19980, 24766, 71976 ];
    // }

    /*
     * getAllPurchasedOrders() is used to get all purchased orders based on filters
     * @param $filter Array
     *
     * $filter = array('channel_id'=>5, 'po_status'=>33, 'fdate'=>'2016-01-01', 'tdate'=>'2016-05-09');
     *
     * @param $offset Integer, default 0
     * @param $perpage Integer, default 10
     *
     * @return Array
     */

    public function getAllPurchasedOrders($orderbyarray, $filter = array(), $rowCount = 0, $offset = 0, $perpage = 10) {
        try {
            //$_leModel = new LegalEntity();
            //$suppliers = $_leModel->getSupplierId();
            $this->_roleModel = new Role();
            $legalEntityId = Session::get('legal_entity_id');
            $Json = json_decode($this->_roleModel->getFilterData(6), 1);
            $filters = json_decode($Json['sbu'], 1);
            $dc_acess_list = isset($filters['118001']) ? $filters['118001'] : 'NULL';
            $user_id = Session::get("userId");
            $roleRepo = new roleRepo();
            $globalFeature = $roleRepo->checkPermissionByFeatureCode('GLB0001',$user_id);
            $inActiveDCAccess = $roleRepo->checkPermissionByFeatureCode('GLBWH0001',$user_id);

            $fieldArr = array(
                'po.le_wh_id',
                'po.legal_entity_id',
                'po.po_id',
                'po.po_code',
                'po.parent_id',
                'po.po_validity',
                'po.payment_mode',
                'po.payment_due_date',
                'po.tlm_name',
                'po.po_status',
                'po.approval_status as approval_status_val',
                'po.po_so_order_code',
                DB::raw('IF(po.approval_status=1,"Shelved", getMastLookupValue(po.approval_status)) as approval_status'),
                DB::raw('getMastLookupValue(po.payment_status) as payment_status'),
                'po.created_at',
                'po.po_date',
                DB::raw('(select sum(po_products.sub_total) from po_products where po_products.po_id=po.po_id) as poValue'),
                DB::raw('GetUserName(po.created_by,2) AS user_name'),
                DB::raw('(select SUM(inward.grand_total) from inward where inward.po_no=po.po_id) as grn_value'),
                DB::raw('((select sum(po_products.sub_total) from po_products where po_products.po_id=po.po_id)-(select SUM(inward.grand_total) from inward where inward.po_no=po.po_id)) as po_grn_diff'),
                DB::raw('(select inward.created_at from inward where inward.po_no=po.po_id ORDER BY created_at DESC LIMIT 1) as grn_created'),
                'p1.po_code as po_parent_code',
                'p1.po_id as po_parent_id',
                'currency.code as currency_code',
                'currency.symbol_left as symbol',
                'legal_entities.business_legal_name',
                'legal_entities.le_code',
                'lwh.lp_wh_name',
                'lwh.city',
                'lwh.pincode',
                'lwh.address1'
            );

            $query = DB::table('po')->select($fieldArr);
            $query->join('legal_entities', 'legal_entities.legal_entity_id', '=', 'po.legal_entity_id');
            $query->leftjoin('po as p1', 'p1.po_id', '=', 'po.parent_id');
            $query->join('legalentity_warehouses as lwh', 'lwh.le_wh_id', '=', 'po.le_wh_id');
            if(!$globalFeature){
                $query->join('user_permssion as up', function($join) use($user_id)
                 {
                    //$join->on('up.object_id','=',DB::raw(' case when up.object_id=0 then 1 else lwh.bu_id end'));
                    $join->whereRaw('case when up.object_id=0 then 1 else up.object_id=lwh.bu_id end');
                    $join->on('up.user_id','=',DB::raw($user_id));
                    $join->on('up.permission_level_id','=',DB::raw(6));
                 });
            }
            $query->leftJoin('currency', 'currency.currency_id', '=', 'po.currency_id');
            
            if (!$inActiveDCAccess) { // if user dont have access to inactive dc's
                $query->where(['lwh.status' => 1]); //query returns only active records
            }
            if (isset($filter['po_status_id']) && is_array($filter['po_status_id'])) {
                $query->whereIn('po.po_status', $filter['po_status_id']);
            }
            if (isset($filter['po_status_id']) && !is_array($filter['po_status_id'])) {
                if ($filter['po_status_id'] == 87005) {
                    $query->where('po.po_status', $filter['po_status_id']);
                    $query->where('po.is_closed', 0);
                } else if ($filter['po_status_id'] == 87002) {
                    $query->where(function ($query) use($filter) {
                        $query->where('po.po_status', $filter['po_status_id'])
                                ->orWhere('po.is_closed', 1);
                    });
                    $query->whereNotIn('po.approval_status', [1, null, 0]);
                } else if ($filter['po_status_id'] == 87001) {
                    $query->where('po.po_status', $filter['po_status_id']);
                    $query->where('po.approval_status', '!=', 57117);
                } else if ($filter['po_status_id'] == 87004) {
                    $query->where(function ($query) use($filter) {
                        $query->where('po.po_status', $filter['po_status_id'])
                                ->orWhere('po.approval_status', 57117);
                    });
                } else {
                    $query->where('po.po_status', $filter['po_status_id']);
                }
            }
            if (isset($filter['approval_status_id']) && !is_array($filter['approval_status_id'])) {
                if ($filter['approval_status_id'] == 57032) {
                    $query->where(function ($query) {
                        $query->where('po.payment_mode', 2);
                        $query->orWhere('po.payment_due_date', '<=', date('Y-m-d') . ' 23:59:59');
                    });
                    $query->where(function ($query) {
                        $query->where('po.payment_status', 57118);
                        $query->orWhereNull('po.payment_status');
                    });
                    $query->whereNotIn('po.approval_status', [57117, 57106, 57029, 57030]);
                } else if ($filter['approval_status_id'] == 57107) {
                    $query->whereIn('po.approval_status', [57119, 57120, $filter['approval_status_id']]);
                    $query->whereNotIn('po.approval_status', [57117]);
                } else if ($filter['approval_status_id'] == 57034) {
                    $query->whereIn('po.approval_status', [57122, $filter['approval_status_id']]);
                    $query->whereNotIn('po.approval_status', [57117]);
                } else {
                    $query->where('po.approval_status', $filter['approval_status_id']);
                }
                $query->whereNotIn('po.po_status', [87003, 87004]);
            }
            if (isset($filter['poId']) && !empty($filter['poId'])) {
                $query->where('po.po_code', $filter['poId']['operator'], $filter['poId']['value']);
            }
            if (isset($filter['le_code']) && !empty($filter['le_code'])) {
                $query->where('legal_entities.le_code', $filter['le_code']['operator'], $filter['le_code']['value']);
            }
            if (isset($filter['Supplier']) && !empty($filter['Supplier'])) {
                $query->where('legal_entities.business_legal_name', $filter['Supplier']['operator'], $filter['Supplier']['value']);
            }
            if (isset($filter['shipTo']) && !empty($filter['shipTo'])) {
                $query->where('lwh.lp_wh_name', $filter['shipTo']['operator'], $filter['shipTo']['value']);
            }
            if (isset($filter['validity']) && !empty($filter['validity'])) {
                $query->where('po.po_validity', $filter['validity']['operator'], $filter['validity']['value']);
            }
            if (isset($filter['payment_mode']) && !empty($filter['payment_mode'])) {
                $query->whereIn('po.payment_mode', $filter['payment_mode']['value']);
            }
            if (isset($filter['tlm_name']) && !empty($filter['tlm_name'])) {
                $query->where('po.tlm_name', $filter['tlm_name']['operator'], $filter['tlm_name']['value']);
            }
            if (isset($filter['po_so_order_link']) && !empty($filter['po_so_order_link'])) {
                $query->where('po.po_so_order_code', $filter['po_so_order_link']['operator'], $filter['po_so_order_link']['value']);
            }
            if (isset($filter['po_parent_link']) && !empty($filter['po_parent_link'])) {
                $query->where('p1.po_code', $filter['po_parent_link']['operator'], $filter['po_parent_link']['value']);
            }
            if (!empty($filter['createdOn'])) {
                $fdate = '';
                if (isset($filter['createdOn'][2]) && isset($filter['createdOn'][1]) && isset($filter['createdOn'][0])) {
                    $fdate = $filter['createdOn'][2] . '-' . $filter['createdOn'][1] . '-' . $filter['createdOn'][0];
                }
                if ($filter['createdOn']['operator'] == '=' && !empty($fdate)) {
                    $query->whereBetween('po.po_date', [$fdate . ' 00:00:00', $fdate . ' 23:59:59']);
                } else if (!empty($fdate) && $filter['createdOn']['operator'] == '<' || $filter['createdOn']['operator'] == '<=') {
                    $query->where('po.po_date', $filter['createdOn']['operator'], $fdate . ' 23:59:59');
                } else if (!empty($fdate)) {
                    $query->where('po.po_date', $filter['createdOn']['operator'], $fdate . ' 00:00:00');
                }
            }
            //print_r($filter);die;
            if (!empty($filter['grn_created'])) {
                $gfdate = '';
                if (isset($filter['grn_created'][2]) && isset($filter['grn_created'][1]) && isset($filter['grn_created'][0])) {
                    $gfdate = $filter['grn_created'][2] . '-' . $filter['grn_created'][1] . '-' . $filter['grn_created'][0];
                }
                $grncreate_opr = $filter['grn_created']['operator'];
                if ($grncreate_opr == '=' && !empty($gfdate)) {
                    $query->whereBetween(DB::raw('(select inward.created_at from inward where inward.po_no=po.po_id ORDER BY created_at DESC LIMIT 1)'), [$gfdate . ' 00:00:00', $gfdate . ' 23:59:59']);
                } else if (!empty($gfdate)){
                    $timeapnd = ($grncreate_opr == '<' || $grncreate_opr == '<=')?' 23:59:59':' 00:00:00';
                    $query->where(DB::raw('(select inward.created_at from inward where inward.po_no=po.po_id ORDER BY created_at DESC LIMIT 1)'), $grncreate_opr, $gfdate . $timeapnd);
                }
            }
            if (!empty($filter['payment_due_date'])) {
                $fdate = '';
                if (isset($filter['payment_due_date'][2]) && isset($filter['payment_due_date'][1]) && isset($filter['payment_due_date'][0])) {
                    $fdate = $filter['payment_due_date'][2] . '-' . $filter['payment_due_date'][1] . '-' . $filter['payment_due_date'][0];
                }
                if ($filter['payment_due_date']['operator'] == '=' && !empty($fdate)) {
                    $query->whereBetween('po.payment_due_date', [$fdate . ' 00:00:00', $fdate . ' 23:59:59']);
                } else if (!empty($fdate) && $filter['payment_due_date']['operator'] == '<' || $filter['payment_due_date']['operator'] == '<=') {
                    $query->where('po.payment_due_date', $filter['payment_due_date']['operator'], $fdate . ' 23:59:59');
                } else if (!empty($fdate)) {
                    $query->where('po.payment_due_date', $filter['payment_due_date']['operator'], $fdate . ' 00:00:00');
                }
            }
//			print_r($filter);exit;
            if (isset($filter['createdBy']) && !empty($filter['createdBy'])) {
                $query->where(DB::raw('GetUserName(po.created_by,2)'), $filter['createdBy']['operator'], $filter['createdBy']['value']);
            }
            if (isset($filter['Status']) && !empty($filter['Status'])) {
                $query->where('lookup.master_lookup_name', $filter['Status']['operator'], $filter['Status']['value']);
            }
            if (isset($filter['payment_status']) && !empty($filter['payment_status'])) {
                $query->where(DB::raw('getMastLookupValue(po.payment_status)'), $filter['payment_status']['operator'], $filter['payment_status']['value']);
            }
            if (isset($filter['approval_status']) && !empty($filter['approval_status'])) {
                $query->where(DB::raw('IF(po.approval_status=1,"Shelved", getMastLookupValue(po.approval_status))'), $filter['approval_status']['operator'], $filter['approval_status']['value']);
            }
            if (isset($filter['poValue']) && !empty($filter['poValue'])) {
                $query->having(DB::raw('ROUND(poValue,2)'), $filter['poValue']['operator'], $filter['poValue']['value']);
            }
            if (isset($filter['grn_value']) && !empty($filter['grn_value'])) {
                $query->having(DB::raw('ROUND(grn_value,2)'), $filter['grn_value']['operator'], $filter['grn_value']['value']);
            }
            if (isset($filter['po_grn_diff']) && !empty($filter['po_grn_diff'])) {
                $query->having(DB::raw('ROUND(po_grn_diff,2)'), $filter['po_grn_diff']['operator'], $filter['po_grn_diff']['value']);
            }

            
            $userData = $this->checkUserIsSupplier($user_id);
            if (count($userData) == 0) {
                //$query->whereIn('po.legal_entity_id', $suppliers);
                //$query->whereIn('po.le_wh_id', explode(',',$dc_acess_list));
            }
           // Log::info($dc_acess_list);
            if(count($userData) > 0){
                    $manufacturer=DB::table('user_permssion')
                       ->where(['permission_level_id' => 11, 'user_id' => $user_id])
                     ->pluck('object_id')->all();
                    $getMappedSuppliers=$this->getMappedSuppliersForManufacturer($manufacturer);
                    $brands = $this->getAllAccessBrands($user_id);
                    $globalSupperLier = DB::table('master_lookup')->select('description')->where('value',78023)->get()->all();
                    $globalSupperLierId = isset($globalSupperLier[0]->description)?$globalSupperLier[0]->description:'NULL';
                    $query->leftJoin('po_products as pop', 'pop.po_id', '=', 'po.po_id');
                    $query->leftJoin('products as pro', 'pop.product_id', '=', 'pro.product_id');
                    $brands = implode(',',$brands);
                    $query->whereIn('pro.brand_id', explode(',',$brands));
                if(count($getMappedSuppliers)>0){
                    $query->whereIn('po.legal_entity_id', $getMappedSuppliers);
                    $query->whereIn('pro.manufacturer_id', $manufacturer);   
                }else{
                    $query->whereNotIn('po.legal_entity_id', [$globalSupperLierId]);
                }
           // Log::info($dc_acess_list);
                //$query->whereIn('po.le_wh_id', explode(',',$dc_acess_list));
            }

            // if(Session::has("from_date") && Session::get("from_date") !=""){
            //    $filter['from_date'] = Session::get("from_date");
            // }

            // if(Session::has("to_date") && Session::get("to_date") !=""){
            //     $filter['to_date'] = Session::get("to_date");
            // }
            if(isset($filter['from_date']) && $filter['from_date'] != "" && isset($filter['to_date']) && $filter['to_date'] != "") {
                $query->whereBetween('po.po_date', [$filter['from_date'] . ' 00:00:00', $filter['to_date'] . ' 23:59:59']);
            }
            if ($rowCount) {
                $query->groupBy('po.po_id');
                $po = count($query->get()->all());

            } else {
                $offset = ($offset * $perpage);
                if (!empty($orderbyarray)) {
                    $orderClause = explode(" ", $orderbyarray);
                    $query->orderby($orderClause[0], $orderClause[1]);  //order by query
                } else {
                    $query->orderBy('po.po_id', 'desc');
                }
                $query->groupBy('po.po_id');
                $query->skip($offset)->take($perpage);
                $po = $query->get()->all();
            }
            //Log::info(DB::getQueryLog());
            //echo $query->toSql();die;
            return $po;
        } catch (Exception $e) {
            return Array('status' => 'failed', 'message' => $e->getMessage(), 'data' => []);
        }
    }
    // using in multiple places
    public function getLEWHById($le_wh_id) {

		try{
			$fieldArr = array(
                            'warehouses.le_wh_id',
                            'warehouses.legal_entity_id',
							'warehouses.lp_wh_name',
							'warehouses.address1',
							'warehouses.address2',
							'warehouses.city',
							'warehouses.pincode',
							'warehouses.phone_no',
                            'warehouses.email',
                            'warehouses.credit_limit_check',
                            'warehouses.state as state_id',
							'countries.name as country',
							'zone.name as state',
							'zone.code as state_code'
						);

			$query = DB::table('legalentity_warehouses as warehouses')->select($fieldArr);
			$query->join('legal_entities as legal', 'warehouses.legal_entity_id', '=', 'legal.legal_entity_id');
			$query->leftJoin('countries', 'countries.country_id', '=', 'warehouses.country');
			$query->leftJoin('zone', 'zone.zone_id', '=', 'warehouses.state');
			if($le_wh_id) {
				$query->where('warehouses.le_wh_id', $le_wh_id);
			}
			return $query->first();
		}
		catch(Exception $e) {

		}
	}

	public function getAllLogistics() {

		try{
			$fieldArr = array(
							'warehouses.le_wh_id',
							'warehouses.lp_wh_name',
							'warehouses.address1',
							'warehouses.address2',
							'warehouses.city',
							'warehouses.pincode',
							'countries.name as country',
							'zone.name as state'
						);

			$query = DB::table('legalentity_warehouses as warehouses')->select($fieldArr);
			$query->join('legal_entities as legal', 'warehouses.legal_entity_id', '=', 'legal.legal_entity_id');
			$query->leftJoin('countries', 'countries.country_id', '=', 'warehouses.country');
			$query->leftJoin('zone', 'zone.zone_id', '=', 'warehouses.state');
			$warehousesArr = $query->get()->all();
			$dataArr = array();
			if(is_array($warehousesArr)) {
				foreach($warehousesArr as $data) {
					$dataArr[$data->le_wh_id] = $data;
				}
			}
			return $dataArr;
		}
		catch(Exception $e) {

		}
	}

	public function getAllSuppliers() {
		try{
			$fieldArr = array(
							'legal.legal_entity_id',
							'legal.business_legal_name',
							'legal.address1',
							'legal.address2',
							'legal.city',
							'legal.pincode',
							'countries.name as country',
							'zone.name as state'
						);

			$query = DB::table('legal_entities as legal')->select($fieldArr);
			//$query->leftJoin('legal_entities as legal', 'suppliers.legal_entity_id', '=', 'legal.legal_entity_id');
			$query->leftJoin('countries', 'countries.country_id', '=', 'legal.country');
			$query->leftJoin('zone', 'zone.zone_id', '=', 'legal.state_id');
			$suppliersArr = $query->get()->all();

			$dataArr = array();
			if(is_array($suppliersArr)) {
				foreach($suppliersArr as $data) {
					$dataArr[$data->legal_entity_id] = $data;
				}
			}
			return $dataArr;

		}
		catch(Exception $e) {

		}
	}

	public function getTaxByProductId($productId) {
		try	{

			$fieldArr = array(
						'tax.tax_class_type as name',
						'tax.tax_percentage as tax',
						'taxmap.product_id'
						);

			$query = DB::table('tax_classes as tax')->select($fieldArr);
			$query->join('tax_class_product_map as taxmap', 'tax.tax_class_id', '=', 'taxmap.tax_class_id');
			$query->where('taxmap.product_id', $productId);
			$query->where('tax.state_id', 4033);
			$tax = $query->get()->all();

			//echo $query->toSql();die;
			return $tax;
		}
		catch(Exception $e) {

		}
	}

	public function getPoDetailById($poId,$orderby='parent_id') {
		try	{
            // getting global supplier id for stockists
            $globalSupperLier = DB::table('master_lookup')->select('description')->where('value',78023)->get()->all();
            $globalSupperLierId = isset($globalSupperLier[0]->description)?$globalSupperLier[0]->description:'NULL';
            $legal_entity_id = Session::get('legal_entity_id');
            // checking whether the current user is stockist or not
            $is_Stockist = $this->checkStockist($legal_entity_id);
            $dc_le_id_list = $this->getAllDCLeids();
            $globalSupperLierId = $globalSupperLierId.",".$dc_le_id_list;

            if($is_Stockist>0){
                // if stockist ,display prev_elp,thityd,std elp by Ebutor as global supplier
                $stockistQuery = "and pph.supplier_id IN ($globalSupperLierId)";
            }else{
                // if not stockist ,remove Ebutor as a supplier in the supplier list
                $stockistQuery = "and pph.supplier_id NOT IN ($globalSupperLierId)";
            }
			$curdate = date('Y-m-d 23:59:59');
			$lastdate = date('Y-m-d 00:00:00',strtotime('-30 days')); //date 30 days ago
			$fieldArr = array(
						'po.le_wh_id',
						'po.legal_entity_id',
						'po.po_id',
						'po.po_code',
						'po.parent_id as poparentid',
						'po.indent_id',
						'po.po_type',
						'po.po_status',
						'po.is_closed',
						'po.po_date',
						'po.payment_due_date',
						'po.created_at',
						'po.delivery_date',
						'po.exp_delivery_date',
						'po.po_remarks',
						'po.reason_to_close',
						'po.logistics_cost',
						'po.payment_mode',
						'po.payment_type',
						'po.payment_refno',
						'po.tlm_name',
						'po.tlm_group',
						'po.approval_status',
						'po.payment_status',
						'po.apply_discount_on_bill',
						'po.discount_type',
						'po.discount',
                        'po.po_so_status',
                        'po.po_so_order_code',
                        'po.discount_before_tax',
                        'po.is_stock_transfer as stock_transfer',
                        'po.stock_transfer_dc',
						DB::raw('getMastLookupValue(po.payment_status) as paymentStatus'),
						DB::raw('GetUserName(po.created_by,2) AS user_name'),
                        DB::raw('getLeWhName(po.supply_le_wh_id) AS dc_name'),
                        DB::raw('getLeWhName(po.stock_transfer_dc) AS st_dc_name'),
						'currency.code as currency_code',
                        'supply_le_wh_id',
						'currency.symbol_left as symbol',
						'po_products.product_id',
						'po_products.qty',
						'po_products.free_qty',
						'po_products.free_uom',
						'po_products.free_eaches',
						'po_products.price',
						'po_products.sub_total',
						'po_products.uom',
						'po_products.unit_price',
						'po_products.cur_elp',
						//'brands.brand_name',
						'gdsp.product_title',
            'gdsp.mrp',
            'gdsp.sku',
            'gdsp.seller_sku',
            'gdsp.manufacturer_id',
						'po_products.is_tax_included',
						'po_products.tax_name',
						'po_products.tax_per',
						'po_products.tax_amt',
						'po_products.hsn_code',
						'po_products.tax_data',
						'po_products.no_of_eaches',
						'po_products.apply_discount',
						'po_products.discount_type as item_discount_type',
						'po_products.discount as item_discount',
						'tot.dlp',
                                               // DB::raw('(select slp from slp_history as slph where slph.product_id=po_products.product_id and slph.le_wh_id=po.le_wh_id and slph.supplier_id=po.legal_entity_id order by effective_date limit 0,1) as slp'),
                                                DB::raw('(select min(elp) from purchase_price_history as pph where pph.product_id=po_products.product_id '.$stockistQuery.' ) as std'),
                                                DB::raw('(select min(elp) from purchase_price_history as pph where pph.product_id=po_products.product_id and effective_date between "'.$lastdate.'" and "'.$curdate.'" '.$stockistQuery.') as thirtyd'),
                                                DB::raw('(select elp from purchase_price_history as pph where pph.product_id=po_products.product_id '.$stockistQuery.' and pph.created_at < po.created_at and pph.po_id!=po.po_id and pph.le_wh_id = po.le_wh_id order by effective_date desc limit 0,1) as prev_elp'),
                                                DB::raw("(SELECT available_inventory FROM vw_inventory_report WHERE product_id = po_products.product_id and le_wh_id = po.le_wh_id) as 'available_inventory'"),
                                                DB::raw('(
                                                        CASE
                                                          WHEN
                                                            `po_products`.`parent_id`=0
                                                          THEN `po_products`.`product_id`
                                                          ELSE `po_products`.`parent_id`
                                                        END
                                                            ) AS `parent_id`')
						);

			// prepare sql

			$query = DB::table('po')->select($fieldArr);
			$query->join('po_products', 'po.po_id', '=', 'po_products.po_id');
			$query->join('products as gdsp', 'gdsp.product_id', '=', 'po_products.product_id');
                        $query->leftJoin('product_tot as tot', function($join)
                         {
                            $join->on('po_products.product_id','=','tot.product_id');
                            $join->on('gdsp.product_id','=','tot.product_id');
                            $join->on('po.le_wh_id','=','tot.le_wh_id');
                            $join->on('po.legal_entity_id','=','tot.supplier_id');
                         });
			//$query->leftJoin('brands', 'brands.brand_id', '=', 'gdsp.brand_id');
			//$query->join('users', 'users.user_id', '=', 'po.created_by');
			$query->leftJoin('currency', 'currency.currency_id', '=', 'po.currency_id');
            $query->useWritePdo();
			$query->where('po.po_id', $poId);
                        if($orderby=='parent_id'){
                            $query->orderBy('parent_id', 'asc');
                        }else{
                            $query->orderBy('po_product_id', 'asc');
                        }
            $po = $query->get()->all();

			//echo $query->toSql();die;
			return $po;
		}
		catch(Exception $e) {

		}
	}
	public function getPoProdutDetailById($poId) {
		try	{
			$curdate = date('Y-m-d');
			$lastdate = date('Y-m-d',strtotime('-30 days')); //date 30 days ago
			$fieldArr = array(
                                        'po.le_wh_id',
                                        'po.legal_entity_id',
                                        'po.po_id',
                                        'po.po_code',
                                        'po.parent_id as poparentid',
                                        'po.indent_id',
                                        'po.po_type',
                                        'po.po_status',
                                        'po.is_closed',
                                        'po_products.product_id',
                                        'gdsp.product_title',
                                        'gdsp.mrp',
                                        'gdsp.sku',
                                        'gdsp.seller_sku',
                                        'gdsp.manufacturer_id',
                                        DB::raw('getManfName(gdsp.manufacturer_id) as manf_name')
                                    );
			// prepare sql
			$query = DB::table('po')->select($fieldArr);
			$query->join('po_products', 'po.po_id', '=', 'po_products.po_id');
			$query->join('products as gdsp', 'gdsp.product_id', '=', 'po_products.product_id');
                        $query->leftJoin('product_tot as tot', function($join)
                        {
                            $join->on('po_products.product_id','=','tot.product_id');
                            $join->on('gdsp.product_id','=','tot.product_id');
                            $join->on('po.le_wh_id','=','tot.le_wh_id');
                            $join->on('po.legal_entity_id','=','tot.supplier_id');
                        });
			$query->where('po.po_id', $poId);
			$po = $query->get()->all();
			return $po;
		}
		catch(Exception $e) {

		}
	}
    public function getPoById($poId) {
        try {
            $fieldArr = array('po.*'
            );
            // prepare sql
            $query = DB::table('po')->select($fieldArr);
            $query->where('po.po_id', $poId);
            $po = $query->first();
            return $po;
        } catch (Exception $e) {

        }
    }
    public function getPoProdutsById($poId, $product_id) {
        try {
            $fieldArr = array('po_products.*'
            );
            // prepare sql
            $query = DB::table('po_products')->select($fieldArr);
            $query->where('po_products.po_id', $poId);
            if ($product_id != '') {
                $query->where('po_products.product_id', $product_id);
            }
            $po = $query->first();
            return $po;
        } catch (Exception $e) {

        }
    }
    public function getSupplierId($legal_entity_id,$product_id) {
        try {
            // prepare sql
            $query = DB::table('product_tot as tot')
                    ->leftJoin('purchase_price_history as pph','pph.product_id','=','tot.product_id')
                    ->select(['tot.product_id','tot.supplier_id','pph.supplier_id as history_sup_id']);
            $query->where('tot.product_id', $product_id);
            $query->where('tot.supplier_id','!=', $legal_entity_id);
            $query->orderBy('pph.effective_date', 'DESC');
            $sup = $query->first();
            return $sup;
        } catch (Exception $e) {

        }
    }
    public function checkProductSuscribe($legal_entity_id,$le_wh_id,$product_id) {
        try {
            // prepare sql
            $query = DB::table('product_tot as tot')
                    ->select(['tot.product_id','tot.supplier_id','tot.subscribe']);
            $query->where('tot.product_id', $product_id);
            $query->where('tot.supplier_id', $legal_entity_id);
            $query->where('tot.le_wh_id', $le_wh_id);
            $sup = $query->first();
            return $sup;
        } catch (Exception $e) {

        }
    }
    public function getProductdetails($product_id) {
        try {
            // prepare sql
            $query = DB::table('products')
                    ->select(['products.product_title','products.product_id','products.sku']);
            $query->where('products.product_id', $product_id);
            $sup = $query->first();
            return $sup;
        } catch (Exception $e) {

        }
    }
    public function saveProductTot($product_tot) {
        try {
            DB::table('product_tot')->insert($product_tot);
        } catch (Exception $e) {

        }
    }
    public function updateProductTot($product_tot,$supId,$le_wh_id,$product_id) {
        try {
            DB::table('product_tot')->update($product_tot)
                    ->where('product_id',$product_id)
                    ->where('le_wh_id',$le_wh_id)
                    ->where('supplier_id',$supId);
        } catch (Exception $e) {

        }
    }
    public function getManfMapSupplier($le_wh_id,$manfId) {
        try {
            // prepare sql
            $query = DB::table('supplier_wh_mapping as swm')
                    ->select(['swm.legal_entity_id','swm.le_wh_id','swm.manf_id']);
            $query->where('swm.le_wh_id', $le_wh_id);
            $query->where('swm.manf_id', $manfId);
            $query->where('swm.status', 1);
            $sup = $query->first();
            return $sup;
        } catch (Exception $e) {

        }
    }
    public function checkChildPoExist($po_id,$parent_po_code="") {
        try {
            // prepare sql
            $query = DB::table('po')
                    ->select([DB::raw('COUNT(po.po_id) as count')]);
            $query->where('po.parent_id', $po_id);
            if($parent_po_code!=""){
                $query->where("po_code","NOT LIKE","%".$parent_po_code."%");
            }
            $count = $query->first();
            //print_r($count);die;
            return isset($count->count)?$count->count:0;
        } catch (Exception $e) {

        }
    }
    /*
     * getPODetailsByCode() method is used to get Po details by code
     * @param $poCode string
     * @return Array
    */
    public function getPODetailsByCode($poCode) {
        try {
            $fieldArr = array(
                'po.le_wh_id',
                'po.legal_entity_id',
                'po.po_id',
                'po.po_code',
                'po.po_status',
                'po.is_closed',
                'po.po_date',
                'po.payment_due_date'
            );
            // prepare sql
            $query = DB::table('po')->select($fieldArr);
            $query->where('po.po_code', $poCode);
            $po = $query->first();
            return $po;
        } catch (Exception $e) {

        }
    }
    /*
     * getAllPODetailsByCode() method is used to get all Po details by code
     * @param $poCode string
     * @return Array
    */
    public function getAllPODetailsByCode($poCode) {
        try {
            $fieldArr = array(
                'po.le_wh_id',
                'po.legal_entity_id',
                'po.po_id',
                'po.po_code',
                'po.po_status',
                'po.is_closed',
                'po.po_date',
                'po.payment_due_date'
            );
            // prepare sql
            $query = DB::table('po')->select($fieldArr);
            $query->where('po.po_code','LIKE', '%'.$poCode.'%');
            $po = $query->get()->all();
            return $po;
        } catch (Exception $e) {

        }
    }

    public function netSoldQty($productIds, $fromDate, $toDate, $dcId) {
        $invoice_qty_query = DB::table("gds_invoice_grid")
                            ->join("gds_orders", "gds_orders.gds_order_id", "=", "gds_invoice_grid.gds_order_id")
                            ->join("gds_order_invoice", "gds_order_invoice.gds_invoice_grid_id", "=", "gds_invoice_grid.gds_invoice_grid_id")
                            ->join("gds_invoice_items", "gds_invoice_items.gds_order_invoice_id", "=", "gds_order_invoice.gds_order_invoice_id")
                            ->where("gds_orders.le_wh_id", $dcId)
                            ->whereIn("gds_invoice_items.product_id", $productIds)
                            ->whereBetween("gds_invoice_grid.created_at", [$fromDate, $toDate])
                            ->select(DB::raw("IFNULL(SUM(gds_invoice_items.qty), 0) AS invoice_qty"))
                            ->get()->all();
        $invoice_qty = json_decode(json_encode($invoice_qty_query), true)[0];
        $return_qty_query = DB::table("gds_returns")
                            ->join("gds_orders", "gds_orders.gds_order_id", "=", "gds_returns.gds_order_id")
                            ->where("gds_orders.le_wh_id", $dcId)
                            ->whereIn("gds_returns.product_id", $productIds)
                            ->whereBetween("gds_returns.created_at", [$fromDate, $toDate])
                            ->select(DB::raw("IFNULL(SUM(qty), 0) AS return_qty"))
                            ->get()->all();
        $return_qty = json_decode(json_encode($return_qty_query), true)[0];
        if(isset($invoice_qty["invoice_qty"]) && isset($return_qty["return_qty"])){
            $net_sold_qty =  $invoice_qty["invoice_qty"] - $return_qty["return_qty"];
        } else {
            $net_sold_qty =  0;
        }
        return $net_sold_qty;
    }
    public function dateFunct($fromDate, $toDate) {
       $date_from = strtotime($fromDate);
       $date_to = strtotime($toDate);
       $dateArray = array();
       for ($i = $date_from; $i <= $date_to; $i += 86400) {
           $weekDay = date("w", $i);
           if($weekDay == 0){
               $dateArray[] = date("Y-m-d", $i);
           }
       }
       return $dateArray;
    }
    public function openPOQty($productId, $wh_id){
        $sql = DB::table("po_products as pp")
            ->join("po", "po.po_id", "=", "pp.po_id")
            ->where("po.po_status", "=", "87001")
            ->where("pp.product_id", "=", $productId)
            ->where("po.le_wh_id", "=", $wh_id)
            ->pluck(DB::raw("pp.no_of_eaches*pp.qty as qty"))->all();
        $result = json_decode(json_encode($sql), true);
        if(!empty($result)){
            return $result[0];
        }
        else
            return 0;
    }
    public function pendingReturns($productId, $wh_id) {
        $re_query = DB::table("gds_returns")
                    ->select([DB::raw("SUM(qty) as re_qty")])
                    ->join("gds_orders", "gds_orders.gds_order_id", "=", "gds_returns.gds_order_id")
                    ->where("approval_status", "!=", 1)
                    ->where("approval_status", "!=", 0)
                    ->where("approval_status", "!=", NULL)
                    ->where("gds_orders.le_wh_id", "=", $wh_id)
                    ->where("product_id", $productId)
                    ->get()->all();
        if(isset($re_query[0])){
            $re_query_en = json_decode(json_encode($re_query[0]), true);
        }
        else{
            $re_query_en = json_decode(json_encode(reset($re_query)), true);
        }

        if($re_query_en["re_qty"]){
            $final_res = $re_query_en["re_qty"];
        } else {
            $final_res = 0;
        }
        return $final_res;
    }
    public function getProductPackUOMInfo($productId,$uom) {
        try {
            $fields = array('lookup.value','starConfig.description as starCode','lookup.master_lookup_name as uomName','pack.no_of_eaches','esu','star');
            $query = DB::table('product_pack_config as pack');
            $query->leftJoin('master_lookup as lookup','pack.level','=','lookup.value');
            $query->leftJoin('master_lookup as starConfig','pack.star','=','starConfig.value');
            $query->select($fields);
            $query->where('pack.product_id',$productId);
            $query->where('pack.level',$uom);
            $packStatus = $query->first();
            return $packStatus;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function getProductPackUOMInfoById($packId) {
        try {
            $fields = array('lookup.value','lookup.master_lookup_name as uomName','pack.no_of_eaches');
            $query = DB::table('product_pack_config as pack');
            $query->leftJoin('master_lookup as lookup','pack.level','=','lookup.value');
            $query->select($fields);
            $query->where('pack.pack_id',$packId);
            $packStatus = $query->first();
            return $packStatus;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    public function getPoCodeById($poId) {
        try {

            $fieldArr = array('po.po_code','po.le_wh_id', 'po.parent_id', 'po.po_date', 'legal_entity_id', 'po.approval_status', 'po.payment_status');
            $query = DB::table('po')->select($fieldArr);
            $query->where('po.po_id', $poId);
            $po = $query->first();
            return $po;
        } catch (Exception $e) {

        }
    }

    /**
	 * getPoCountByStatus method is used to get total record based on status by legal entity
	 * @param  Integer $legalEntityId
	 * @return Array
	 */
	
    public function getPoCountByStatus($legalEntityId, $approval = 0,$from_date="",$to_date="") {
        try {
            
            //log::info($from_date);
            //log::info(DB::enableQueryLog());
            $roleId = Session::get('roles');

            /**
             *
             * roleId - 2: company
             * roleId - 4: supplier
             * roleId - 1: superadmin
             */
            //DB::enableQueryLog();
            $user_id = Session::get("userId");
            $this->_roleModel = new Role();
            //$Json = json_decode($this->_roleModel->getFilterData(6,$user_id), 1);
            //$filters = json_decode($Json['sbu'], 1);
            //$dc_acess_list = isset($filters['118001']) ? $filters['118001'] : 'NULL';

            $roleRepo = new roleRepo();
            $globalFeature = $roleRepo->checkPermissionByFeatureCode('GLB0001',$user_id);
            $inActiveDCAccess = $roleRepo->checkPermissionByFeatureCode('GLBWH0001',$user_id);

            $fieldArr = array(
                'po.po_status',
                'po.approval_status',
                DB::raw('count(distinct po.po_id) as tot'),
            );
            //$_leModel = new LegalEntity();
            //$suppliers = $_leModel->getSupplierId($legalEntityId);
            $query = DB::table('po')->select($fieldArr);
            $query->join('legal_entities', 'legal_entities.legal_entity_id', '=', 'po.legal_entity_id');
            $query->join('legalentity_warehouses as lewh', 'lewh.le_wh_id', '=', 'po.le_wh_id');

            if(!$globalFeature){
                $query->join('user_permssion as up', function($join) use($user_id)
                 {
                    $join->on('up.object_id','=','lewh.bu_id');
                    $join->on('up.user_id','=',DB::raw($user_id));
                    $join->on('up.permission_level_id','=',DB::raw(6));
                 });
            }
            if (!$inActiveDCAccess) { // if user dont have access to inactive dc's
                $query->where(['lewh.status' => 1]); //query returns only active records
            }            

            if ($approval == 1) {
                $query->whereNotIn('po.po_status', [87003,87004]);
                $query->groupBy('po.approval_status');
            }else if ($approval == 2) {
                $query->whereIn('po.po_status',[87002,87005]);
                $query->where('po.approval_status',1);
                $query->groupBy('po.approval_status');
            }else if ($approval == 3) {
                $query->where('po.po_status', 87005);
                $query->where('po.is_closed', 0);
                $query->groupBy('po.po_status');
            }else if ($approval == 4) {
                $query->where(function ($query) {
                    $query->where('po.po_status', 87002)
                        ->orWhere('po.is_closed', 1);
                });
                $query->whereNotIn('po.approval_status', [1,null,0]);
                $query->groupBy('po.po_status');
            }else if ($approval == 5) {
                $query->where(function ($query) {
                    $query->where('po.payment_mode', 2);
                    $query->orWhere('po.payment_due_date', '<=',date('Y-m-d').' 23:59:59');
                });
                $query->where(function ($query) {
                    $query->where('po.payment_status', 57118);
                    $query->orWhereNull('po.payment_status');
                });
                $query->whereNotIn('po.approval_status', [57117,57106,57029,57030]);
                $query->groupBy('po.po_status');
            } else {
                $query->groupBy('po.po_status');
            }
            // print_r($brands);die;
            $userData = $this->checkUserIsSupplier($user_id);
            if ($roleId != 1 && count($userData) == 0) {
                //$query->whereIn('po.legal_entity_id', $suppliers);
                //$query->whereIn('po.le_wh_id', explode(',',$dc_acess_list));

            }
            // if(Session::has("from_date") && Session::get("from_date") !=""){
            //    $from_date = Session::get("from_date");
            // }

            // if(Session::has("to_date") && Session::get("to_date") !=""){
            //    $to_date = Session::get("to_date");
            // }
            if(isset($from_date) && $from_date != "" && isset($to_date) && $to_date != "") {

                $query->whereBetween('po.po_date', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
            }
            if(count($userData) > 0){
                $brands = $this->getAllAccessBrands($user_id);
                $globalSupperLier = DB::table('master_lookup')->select('description')->where('value',78023)->get()->all();
                $globalSupperLierId = isset($globalSupperLier[0]->description)?$globalSupperLier[0]->description:'NULL';
                $query->leftJoin('po_products as pop', 'pop.po_id', '=', 'po.po_id');
                $query->leftJoin('products as pro', 'pop.product_id', '=', 'pro.product_id');
                $brands = implode(',',$brands);
                $query->whereIn('pro.brand_id', explode(',',$brands));
                $query->whereNotIn('po.legal_entity_id', [$globalSupperLierId]);
                //$query->whereIn('po.le_wh_id', explode(',',$dc_acess_list));
            }

            $poArr = $query->get()->all();
            $dataArr = array();
            if (is_array($poArr)) {
                foreach ($poArr as $data) {
                    if ($approval == 1 || $approval == 2) {
                        $dataArr[$data->approval_status] = $data->tot;
                    } else {
                        $dataArr[$data->po_status] = $data->tot;
                    }
                    $dataArr[$data->po_status] = $data->tot;
                }
            }
            return $dataArr;
        } catch (Exception $e) {

        }
    }

    /*
	 * getLegalEntityById() method is used to fetch legal entity detail by id
	 * @param $legalEntityId Integer
	 * @return Array
	 */

	public function getLegalEntityById($legalEntityId) {
		try{
			$fieldArr = array(
                            'legal.business_legal_name',
                            'legal.legal_entity_id',
							'legal.address1',
							'legal.address2',
							'legal.city',
							'legal.state_id',
							'legal.pincode',
							'legal.pan_number',
							'legal.tin_number',
							'legal.gstin',
                            'legal.fssai',
							'suppliers.sup_bank_name',
							'suppliers.sup_account_no',
							'suppliers.sup_account_name',
							'suppliers.sup_ifsc_code',
							'countries.name as country_name',
							'zone.name as state_name',
							'zone.code as state_code'
						);

			$query = DB::table('legal_entities as legal')->select($fieldArr);
			$query->leftJoin('countries', 'countries.country_id', '=', 'legal.country');
			$query->leftJoin('zone', 'zone.zone_id', '=', 'legal.state_id');
			$query->leftJoin('suppliers', 'suppliers.legal_entity_id', '=', 'legal.legal_entity_id');
			$query->where('legal.legal_entity_id', $legalEntityId);
			return $query->first();
		}
		catch(Exception $e) {

		}
	}

	/*
	 * getUserByLegalEntityId() method is used to fetch user detail by id
	 * @param $legalEntityId Integer
	 * @return Array
	 */

	public function getUserByLegalEntityId($legalEntityId) {
		try{
			$fieldArr = array(
							'legal.business_legal_name',
							'legal.address1',
							'legal.address2',
							'legal.city',
							'legal.pincode',
							'legal.pan_number',
							'legal.tin_number',
							'legal.gstin',
							'countries.name as country_name',
							'zone.name as state_name',
							'zone.code as state_code'
						);

			$query = DB::table('legal_entities as legal')->select($fieldArr);
			$query->leftJoin('countries', 'countries.country_id', '=', 'legal.country');
			$query->leftJoin('zone', 'zone.zone_id', '=', 'legal.state_id');
			$query->where('legal.legal_entity_id', $legalEntityId);
			return $query->first();
		}
		catch(Exception $e) {

		}
	}


	/*
	 * getOrderStatus() method is used to get order name with value
	 * @param Null
	 * @return Array
	 */

	public function getOrderStatus($catName = 'Order Status') {

		$fieldArr = array('orderStatus.master_lookup_name as name', 'orderStatus.value');
		$query = DB::table('master_lookup as orderStatus')->select($fieldArr);
		$query->join('master_lookup_categories','master_lookup_categories.mas_cat_id','=','orderStatus.mas_cat_id');
		$query->where('master_lookup_categories.mas_cat_name', $catName);
		$allOrderStatusArr = $query->get()->all();

		$orderStatusArr = array();
		if(is_array($allOrderStatusArr)) {
			foreach($allOrderStatusArr as $data){
				$orderStatusArr[$data->value] = $data->name;
			}
		}

		return $orderStatusArr;
	}
        /*
	 * getMasterLookupByCatId() method is used to get lookup data with value
	 * @param Null
	 * @return Array
	 */

	public function getMasterLookupByCatId($catName = 'Order Status') {

		$fieldArr = array('lookup.master_lookup_name as name', 'lookup.value', 'lookup.description');
		$query = DB::table('master_lookup as lookup')->select($fieldArr);
		$query->join('master_lookup_categories','master_lookup_categories.mas_cat_id','=','lookup.mas_cat_id');
		$query->where('master_lookup_categories.mas_cat_name', $catName);
		$allLookupArr = $query->get()->all();

		$lookupArr = array();
		if(is_array($allLookupArr)) {
                    foreach($allLookupArr as $data){
                            $lookupArr[$data->value] = $data;
                    }
		}
		return $lookupArr;
	}

    public function getCreatePoData()
    {
        try
        {
            $response = [];
            $latestPoId = $this->max('po_id');
            $response['latestPoId'] = ++$latestPoId;

            //$_leModel = new LegalEntity();
            //$suppliers = $_leModel->getSupplierId();
            
            $this->_roleModel = new Role();
            $Json = json_decode($this->_roleModel->getFilterData(6), 1);
            $filters = json_decode($Json['sbu'], 1);
            $dc_acess_list = isset($filters['118001']) ? $filters['118001'] : 'NULL';
            DB::enableQueryLog();
            $indent = new IndentModel();
            $indentsList = $indent->where(['indent_status' => 70001])
                    //->whereIn('legal_entity_id',$suppliers)
                    ->whereIn('le_wh_id', explode(',',$dc_acess_list))
                    ->where('legal_entity_id','!=',NULL)
                    ->orderBy('indent_id','desc')
                    ->pluck('indent_id', 'indent_code')->all();
            $response['indentsList'] = $indentsList;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
        return $response;
    }

	private function getPoQtyWithProductByIndentId($indentId) {
		$fields = array('product.product_id', DB::raw('SUM(product.qty) as totQty'));
		$query = DB::table('po')->select($fields);
		$query->join('po_products as product', 'product.po_id', '=', 'po.po_id');
		$query->where('po.indent_id', $indentId);
		$query->groupBy('product.product_id');
		$rows = $query->get()->all();
		$dataArr = array();

		if(is_array($rows)) {
			foreach ($rows as $row) {
				$dataArr[$row->product_id] = (int)$row->totQty;
			}
		}
		return $dataArr;
	}

	public function getPoQtyByIndentId($indentId) {
		$fields = array(DB::raw('SUM(product.qty*product.no_of_eaches) as totQty'));
		$query = DB::table('po')->select($fields);
		$query->join('po_products as product', 'product.po_id', '=', 'po.po_id');
		$query->where('po.indent_id', $indentId);
		$row = $query->first();
		return isset($row->totQty) ? (int)$row->totQty : 0;
	}
	public function getPoProductQtyByIndentId($indentId,$product_id) {
		$fields = array(DB::raw('SUM(product.qty*product.no_of_eaches) as totQty'));
		$query = DB::table('po')->select($fields);
		$query->join('po_products as product', 'product.po_id', '=', 'po.po_id');
		$query->where('product.product_id', $product_id);
		$query->where('po.indent_id', $indentId);
		$row = $query->first();
		return isset($row->totQty) ? (int)$row->totQty : 0;
	}

	public function getPoQtyByPoId($poId) {
		$fields = array(DB::raw('SUM(product.qty * product.no_of_eaches) as totQty'));
		$query = DB::table('po')->select($fields);
		$query->join('po_products as product', 'product.po_id', '=', 'po.po_id');
		$query->where('po.po_id', $poId);
		$row = $query->first();
		return isset($row->totQty) ? (int)$row->totQty : 0;
	}

	public function getSupplierByLEId($leId) {
		$fields = array('le.legal_entity_id', 'le.business_legal_name','le.le_code');

		$query = DB::table('legal_entities as le')->select($fields);
		$query->where(['le.legal_entity_type_id' => 1002, 'le.is_approved' => 1,
						'le.parent_id'=>$leId]);
		return $query->get()->all();
	}

    public function getSuppliers($data)
    {
        try
        {
            $response['supplierList'] = [];
            $indentId = $data['indent_id'];
            $legal_entity_id = \Session::get('legal_entity_id');
            $legal_entity_type_id = $this->getLegalEntityTypeId($legal_entity_id);
            
            $supOptions='';
            $warehouseOptions='';
            $this->_roleModel = new Role();
            $Json = json_decode($this->_roleModel->getFilterData(6), 1);
            $filters = json_decode($Json['sbu'], 1);
            $dc_acess_list = isset($filters['118001']) ? $filters['118001'] : 'NULL';
            if($indentId > 0)
            {
            	$prodArr = $this->getPoQtyWithProductByIndentId($indentId);
            	//print_r($prodArr);
                $supplierList = DB::table('legal_entities')
                        ->join('suppliers', 'suppliers.legal_entity_id', '=', 'legal_entities.legal_entity_id')
                        ->join('indent', 'indent.legal_entity_id', '=', 'legal_entities.legal_entity_id')
                        ->where(['indent.indent_id' => $indentId, 'legal_entities.legal_entity_type_id' => 1002, 'suppliers.is_active' => 1, 'legal_entities.is_approved' => 1, 'parent_id'=>$legal_entity_id])
                        ->get(['legal_entities.legal_entity_id','legal_entities.legal_entity_type_id', 'legal_entities.business_legal_name',DB::raw("(SELECT getMastLookupValue(legal_entities.legal_entity_type_id)) as btype")])->all();

                $warehouseList = DB::table('legalentity_warehouses')
                        ->join('indent', 'indent.le_wh_id', '=', 'legalentity_warehouses.le_wh_id')
                        ->join('legal_entities', 'legal_entities.legal_entity_id', '=', 'legalentity_warehouses.legal_entity_id')
                        ->where(['indent.indent_id' => $indentId])
                        ->whereIn('legalentity_warehouses.le_wh_id', explode(',',$dc_acess_list))
                        ->get(['legalentity_warehouses.lp_wh_name', 'legalentity_warehouses.le_wh_id','legal_entities.legal_entity_type_id'])->all();

                $products = DB::table('indent_products as indentprod')
                        ->where(['indent.indent_id' => $indentId])
                        //->where('tot.subscribe',1)
                        ->leftJoin('indent','indent.indent_id','=','indentprod.indent_id')
                        ->leftJoin('products','products.product_id','=','indentprod.product_id')
                        ->leftJoin('product_tot as tot', function($join)
                         {
                            $join->on('products.product_id','=','tot.product_id');
                            $join->on('indent.le_wh_id','=','tot.le_wh_id');
                            $join->on('indent.legal_entity_id','=','tot.supplier_id');
                         })
                        ->leftJoin('brands','products.brand_id','=','brands.brand_id')
                        ->select('indent.indent_id','indent.le_wh_id','indent.legal_entity_id',
                                'indentprod.product_id','tot.subscribe'
                                )
                        ->get()->all();
            }else{
                $supOptions='<option value="">Select Supplier</option>';
                $warehouseOptions='<option value="">Select Delivery Location</option>';

                $supplierList = DB::table('legal_entities')
                        ->join('suppliers', 'suppliers.legal_entity_id', '=', 'legal_entities.legal_entity_id')
                        ->where(['legal_entities.legal_entity_type_id' => 1002, 'suppliers.is_active' => 1, 'legal_entities.is_approved' => 1, 'parent_id'=>$legal_entity_id])
                        ->get(['legal_entities.legal_entity_id','legal_entities.legal_entity_type_id', 'legal_entities.business_legal_name',DB::raw("(SELECT getMastLookupValue(legal_entities.legal_entity_type_id)) as btype")])->all();
                $warehouseList=array();
                $products = array();
            }
            $universalSupplier = DB::table('master_lookup')->select('description')->where('value','=',78023)->first();
            $legal_id = DB::table('legal_entities')->select(['legal_entity_id','legal_entity_type_id','business_legal_name',DB::raw("(SELECT getMastLookupValue(legal_entities.legal_entity_type_id)) as btype")])->where('legal_entity_id','=',$universalSupplier->description)->first();

            $supOptions .= '<option value='.$legal_id->legal_entity_id.' le_type_id='.$legal_id->legal_entity_type_id.'>'.$legal_id->business_legal_name.'- '.$legal_id->btype.'</option>';

            $dc_fc_mapping_data = $this->getDCFCData($legal_entity_id);
            foreach($dc_fc_mapping_data as $dc_fc){
                $dc_fc->legal_entity_type_id = isset($dc_fc->legal_entity_type_id) ? $dc_fc->legal_entity_type_id : 0;
                $supOptions .= '<option value='.$dc_fc->legal_entity_id.' le_type_id='.$dc_fc->legal_entity_type_id.'>'.$dc_fc->business_legal_name.'- '.$dc_fc->btype.'</option>';
            }
            if($legal_entity_type_id == 1001){
                $legal_entity_type_id = [1014,1016];
            
                $fc_dc_legal_entities = DB::table('dc_fc_mapping')->select(DB::raw("GROUP_CONCAT(DISTINCT CONCAT(dc_le_id,',',fc_le_id) ) AS dc_le_id"))
                            ->whereIn('dc_fc_mapping.dc_le_wh_id', explode(',',$dc_acess_list))
                            ->orWhereIn('dc_fc_mapping.fc_le_wh_id', explode(',',$dc_acess_list))
                            ->first();
                $fc_dc_legal_entities = isset($fc_dc_legal_entities->dc_le_id) ? $fc_dc_legal_entities->dc_le_id : "";
                $dcfcList = array();
                if($fc_dc_legal_entities != ""){
                    $dcfcList = DB::table('legal_entities')
                            ->whereIn( 'legal_entities.legal_entity_id',explode(',',$fc_dc_legal_entities))
                            ->whereIn( 'legal_entities.legal_entity_type_id',$legal_entity_type_id)
                            ->get(['legal_entities.legal_entity_id',"legal_entities.legal_entity_type_id", 'legal_entities.business_legal_name',DB::raw("(SELECT getMastLookupValue(legal_entities.legal_entity_type_id)) as btype")])->all();
                }
                
                foreach($dcfcList as $dc_fc){
                    $dc_fc->legal_entity_type_id = isset($dc_fc->legal_entity_type_id) ? $dc_fc->legal_entity_type_id : 0;
                    $supOptions .= '<option value='.$dc_fc->legal_entity_id.' le_type_id='.$dc_fc->legal_entity_type_id.'>'.$dc_fc->business_legal_name.' - '.$dc_fc->btype.'</option>';
                }
            }
            foreach($supplierList as $supplier){
                if($supplier->legal_entity_id != $legal_id->legal_entity_id)
                $supOptions .= '<option value='.$supplier->legal_entity_id.' le_type_id='.$supplier->legal_entity_type_id.'>'.$supplier->business_legal_name.' - '.$supplier->btype.'</option>';
            }
            foreach($warehouseList as $warehouse){
                $warehouseOptions .= '<option value='.$warehouse->le_wh_id.'>'.$warehouse->lp_wh_name.'</option>';
            }
            $response['supplierList'] = $supOptions;
            $response['warehouseList'] = $warehouseOptions;
            $response['products'] = $products;
            $response['productList'] = '';
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
        return $response;
    }
    public function getProductPackInfo($product_id) {
        try {
        	$fieldArr = array('products.product_id','products.product_title','products.seller_sku','products.upc','pack.pack_id','pack.level','pack.no_of_eaches','pack.pack_sku_code','lookup.master_lookup_name as packname', 'products.mrp');
            $query = DB::table('products')->select($fieldArr);
            $query->join('product_pack_config as pack', 'pack.product_id', '=', 'products.product_id');
            $query->join('master_lookup as lookup', 'pack.level', '=', 'lookup.value');
            $query->where('pack.no_of_eaches','>', 0);
            $query->where('products.product_id', $product_id);
            $query->orderBy('lookup.sort_order', 'desc');
            $query->orderBy('pack.effective_date', 'desc');
            //echo $query->toSql();die();
            $product = $query->get()->all();
            return $product;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function getFreebieParent($product_id) {
        try {
            $fieldArr = array('free_conf_id','main_prd_id','free_prd_id');
            $query = DB::table('freebee_conf')->select($fieldArr);
            $query->where('free_prd_id', $product_id);
            $query->orderBy('created_at', 'desc');
            $product = $query->first();
            return $product;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function getFreebieProducts($product_id) {
        try {
            $fieldArr = array('free_conf_id','main_prd_id','free_prd_id','mpq','qty');
            $query = DB::table('freebee_conf')->select($fieldArr);
            $query->where('main_prd_id', $product_id);
            $product = $query->get()->all();
            return $product;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    private function getDeliveryDate($days) {
		$curDate = date('Y-m-d');
		$deliveryDate = array();
		if(is_array($days) && count($days) > 0) {
			foreach($days as $day) {
				$date = date( 'Y-m-d', strtotime( $day.' this week' ) );

				if($date >=$curDate) {
					$date = date( 'Y-m-d', strtotime( $day.' next week' ) );
				}

				$deliveryDate[] = $date;
			}
		}
		return $deliveryDate;
	}
public function updatePOProducts($po_product,$product_id,$poId,$flagdata){
    try
    {
        $update = DB::table('po_products')->where('product_id',$product_id)
                                        ->where('po_id',$poId)->update($po_product);

    }
    catch (\ErrorException $ex) {
        Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        return Response::json(array('status'=>400, 'message'=>'Failed', 'po_id'=>0));
    }
}
    public function savePurchaseOrderData($data)
    {
        try
        {
            $poId = 0;
            $totPoQty = 0;

            #echo '<pre>';print_r($data);die;

            $legalEntityId = \Session::get('legal_entity_id');


            /*
            expected delivery date according to supplier service day
             */

            $expDeliveryDateArr = $this->getDeliveryDate(array());
            $expDeliveryDate  = implode(",", $expDeliveryDateArr);
            $warehouse_id = isset($data['warehouse_list']) ? $data['warehouse_list'] : 0;

            //$refNoArr = DB::selectFromWriteConnection(DB::raw("CALL prc_reference_no('TS', 'PO')"));
            //$serialNumber = isset($refNoArr[0]->ref_no) ? $refNoArr[0]->ref_no : '';
            
            $whdetails = $this->getLEWHById($warehouse_id);
            $state_code = isset($whdetails->state_code)?$whdetails->state_code:"TS";
            $serialNumber = Utility::getReferenceCode("PO",$state_code);
            if($serialNumber == ""){
                return array('status'=>400, 'message'=>Lang::get('po.serialnoerr'), 'po_id'=>0,'serialNumber'=>$serialNumber);
            }

            DB::beginTransaction();
            $indentId = isset($data['indent_id']) ? (int)$data['indent_id'] : 0;
            #var_dump($totPoQty);die;
            $supplier_id = isset($data['supplier_list']) ? $data['supplier_list'] : 0;
            $po_type =  isset($data['po_type']) ? $data['po_type'] : 1;
            $payment_mode =  isset($data['payment_mode']) ? $data['payment_mode'] : 1;
            $paid_through =  isset($data['paid_through']) ? $data['paid_through'] : '';
            $accountinfo = explode('===', $paid_through);
            $tlm_name = (isset($accountinfo[0]))?$accountinfo[0]:'';
            $tlm_group = (isset($accountinfo[1]))?$accountinfo[1]:'';
            $payment_type =  isset($data['payment_type']) ? $data['payment_type'] : '';
            $payment_ref =  isset($data['payment_ref']) ? $data['payment_ref'] : '';
            $poDetails['legal_entity_id'] = $supplier_id;
            $poDetails['le_wh_id'] = $warehouse_id;
            $poDetails['supply_le_wh_id'] = (isset($data['dc_warehouse_id']))?$data['dc_warehouse_id']:0;
            $poDetails['delivery_date'] = date('Y-m-d',strtotime($data['delivery_before']));
            $poDetails['po_type'] = $po_type;
            $poDetails['created_by'] = (isset($data['created_by']) && $data['created_by']!='')?$data['created_by']:\Session::get('userId');
            $poDetails['platform'] = (isset($data['platform_id']) && $data['platform_id']!='')?$data['platform_id']:5001;
            $poDetails['payment_mode'] = $payment_mode;
            $poDetails['payment_type'] = $payment_type;
            $poDetails['payment_refno'] = $payment_ref;
            $poDetails['tlm_name'] = $tlm_name;
            $poDetails['tlm_group'] = $tlm_group;
            $poDetails['approval_status'] = 57106;
            $poDetails['apply_discount_on_bill'] = (isset($data['apply_bill_discount']))?$data['apply_bill_discount']:0;
            $poDetails['discount_type'] = (isset($data['bill_discount_type']))?$data['bill_discount_type']:0;
            $poDetails['discount'] = (isset($data['bill_discount']))?$data['bill_discount']:0;

            if(!empty($data['po_date'])) {
            	$poDetails['po_date'] = date('Y-m-d', strtotime($data['po_date'])).' '.date('H:i:s');
            }
            if(!empty($data['payment_due_date']) && $payment_mode==1) {
            	$poDetails['payment_due_date'] = date('Y-m-d', strtotime($data['payment_due_date'])).' '.date('H:i:s');
            }
            $poDetails['logistics_cost'] = (isset($data['logistics_cost']))?$data['logistics_cost']:0;
            $poDetails['po_validity'] = isset($data['validity']) ? $data['validity'] : 7;
            $poDetails['po_remarks'] = isset($data['po_remarks']) ? $data['po_remarks'] : '';
            $poDetails['po_code'] = $serialNumber;
            $poDetails['discount_before_tax'] = (isset($data['discount_before_tax']))?$data['discount_before_tax']:0;
            $poDetails['is_stock_transfer'] = (isset($data['stock_transfer']))?$data['stock_transfer']:0;
            $poDetails['stock_transfer_dc'] = (isset($data['st_warehouse_id']))?$data['st_warehouse_id']:0;
            if($indentId) {
            	$poDetails['indent_id'] = $indentId;
            }

            if(!empty($expDeliveryDate)) {
            	$poDetails['exp_delivery_date'] = $expDeliveryDate;
            }
            //Log::info("po details");
            //Log::info($poDetails);
            //echo "<pre>";print_r($poDetails);die;
            //DB::enableQueryLog();
            $poId = $this->savePo($poDetails);
            //Log::info(DB::getQueryLog());
            //$poId = $poId->po_id;
            //Log::info($poId);
            if($poId)
            {
                $productInfo = isset($data['po_product_id']) ? $data['po_product_id'] : [];
                $packsize = $data['packsize'];

                if(!empty($productInfo))
                {
                    $is_asset = 0;
                    foreach($productInfo as $key=>$product_id)
                    {
                        $product = $this->getProductInfoByID($product_id,$supplier_id,$warehouse_id);

                        $product = json_decode(json_encode($product),true);
                        $po_product = array();
                        $po_product['product_id'] = $product_id;
                        $po_product['parent_id'] = (isset($data['parent_id']) && isset($data['parent_id'][$key]))?$data['parent_id'][$key]:'';
                        $po_product['mrp'] = (isset($product['mrp']) && $product['mrp']!='')?$product['mrp']:0;
                        $po_product['qty'] = (isset($data['qty'][$key]))?$data['qty'][$key]:1;
                        $pack_id = (isset($packsize[$key]) && $packsize[$key]!='')?$packsize[$key]:'';
                        $uomPackinfo = $this->getProductPackUOMInfoById($pack_id);
                        $po_product['uom'] = (isset($uomPackinfo->value))?$uomPackinfo->value:'';
                        $po_product['no_of_eaches'] = (isset($uomPackinfo->no_of_eaches))?$uomPackinfo->no_of_eaches:0;
                        $po_product['free_qty'] = (isset($data['freeqty'][$key]))?$data['freeqty'][$key]:0;
                        $free_pack_id=(isset($data['freepacksize'][$key]) && $data['freeqty'][$key]!=0)?$data['freepacksize'][$key]:'';
                        $freeUOMPackinfo = $this->getProductPackUOMInfoById($free_pack_id);
                        $po_product['free_uom'] = (isset($freeUOMPackinfo->value) && $data['freeqty'][$key]!=0)?$freeUOMPackinfo->value:'';
                        $po_product['free_eaches'] = (isset($freeUOMPackinfo->no_of_eaches))?$freeUOMPackinfo->no_of_eaches:0;
                        $po_product['is_tax_included'] = (isset($data['pretax'][$product_id]))?$data['pretax'][$product_id]:0;
                        $po_product['apply_discount'] = (isset($data['apply_discount'][$product_id]))?$data['apply_discount'][$product_id]:0;
                        $po_product['discount_type'] = (isset($data['item_discount_type'][$product_id]))?$data['item_discount_type'][$product_id]:0;
                        $po_product['discount'] = (isset($data['item_discount'][$product_id]))?$data['item_discount'][$product_id]:0;
                        $po_product['cur_elp'] = (isset($data['curelpval'][$product_id]))?$data['curelpval'][$product_id]:0;
                        //$po_product['excluding_tax_check'] = (isset($data['excluding_tax_type'][$product_id]))?$data['excluding_tax_type'][$product_id]:0;
                        if($po_type==1){
                            $po_product['unit_price'] = 0;
                            $po_product['price'] = 0;
                            $po_product['sub_total'] = 0;
                            $po_product['tax_name'] = '';
                            $po_product['tax_per'] = 0;
                            $po_product['tax_amt'] = 0;
                        }else{
                            $po_product['unit_price'] = (isset($data['unit_price'][$product_id]))?$data['unit_price'][$product_id]:0;
                            $po_product['price'] = (isset($data['po_baseprice'][$key]))?$data['po_baseprice'][$key]:0;
                            $po_product['sub_total'] = (isset($data['po_totprice'][$key]))?$data['po_totprice'][$key]:0;
                            $po_product['tax_name'] = (isset($data['po_taxname'][$product_id]) && $data['po_taxname'][$product_id] != '')?$data['po_taxname'][$product_id]:'';
                            $po_product['tax_per'] = (isset($data['po_taxper'][$product_id]))?$data['po_taxper'][$product_id]:0.00;
                            $po_product['tax_amt'] = (isset($data['po_taxvalue'][$product_id]))?$data['po_taxvalue'][$product_id]:0.00;
                            $po_product['tax_data'] = (isset($data['po_taxdata'][$product_id]) && $data['po_taxdata'][$product_id] != '')?base64_decode($data['po_taxdata'][$product_id],true):'{}';
                            $po_product['hsn_code'] = (isset($data['hsn_code'][$product_id]))?$data['hsn_code'][$product_id]:0;
                        }

                        $po_product['po_id'] = $poId;
                        #print_r($po_product);die;
                       /* $poamount=$po_product['cur_elp']*$po_product['qty']*$po_product['free_eaches'];
                        $checkLOC = $this->checkLOCByLeWhid($poDetails['le_wh_id']);
                        if($poamount>$checkLOC){
                            return ["status" => 'failure', "message" => "PO is greater than order limit,PO can't be placed.",'data'=>''];
                        }*/
                        DB::table('po_products')->insert($po_product);
                        if($po_type==2){
                            $po_product['created_by'] = (isset($data['created_by']) && $data['created_by']!='')?$data['created_by']:\Session::get('userId');
//                            $this->savePurchaseHistory($po_product,$product);
                        }
                        if($is_asset==0){
                            $is_asset = (isset($product['product_type_id']) && $product['product_type_id']==130001)?1:0;
                        }
                       
                    }
                    $this->updatePO($poId, ['is_asset'=>$is_asset]);
                    DB::commit();
                }

                // update indent status
               /* if($indentId > 0) {
                    $totPoQty = $this->getPoQtyByIndentId($indentId);
                    $objIndent = new IndentModel();
                    $totIndentQty = (int)$objIndent->getIndentQtyById($indentId);
                    $indent_status = ($totPoQty >= $totIndentQty) ? 70002 : 70001;
                    //$indent_status = 70002;

                    $objIndent->updateIndent($indentId, array('indent_status'=>$indent_status));
                }   */
                return array('status'=>200, 'message'=>Lang::get('po.successPO'), 'po_id'=>$poId,'serialNumber'=>$serialNumber);
            }
        }
        catch (\ErrorException $ex) {
            DB::rollback();
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
            return Response::json(array('status'=>400, 'message'=>$ex->getMessage(), 'po_id'=>0));
        }
    }
    public function savePurchaseHistory($po_product,$product){
        try
        {
            $product_id = $product['product_id'];
            $supplier_id = $product['supplier_id'];
            $warehouse_id = $product['le_wh_id'];
            $tot_dlp = $product['dlp'];
            $created_by = (isset($po_product['created_by']))?$po_product['created_by']:\Session::get('userId');
            $po_id = (isset($po_product['po_id']))?$po_product['po_id']:0;
            if($po_product['is_tax_included']==1){
                $dlp = $po_product['unit_price'];
            }else{
                $dlp = $po_product['unit_price']+(($po_product['unit_price']*$po_product['tax_per'])/100);
            }
            if((float)$tot_dlp!=(float)$dlp && $po_id>0){
                $price_history = array(
                            'po_id' =>$po_id,
                            'product_id' =>$product_id,
                            'supplier_id' =>$supplier_id,
                            'le_wh_id' =>$warehouse_id,
                            'elp' =>$dlp,
                            'effective_date' =>date('Y-m-d h:i:s'),
                            'created_by' =>$created_by,
                        );
                DB::table('purchase_price_history')->insert($price_history);
            }

        } catch (Exception $ex) {

        }
    }
    public function getSkus($data)
    {
        try
        {
           $term = $data['term'];
           $legal_entity_id = \Session::get('legal_entity_id');
           $supplier_id = $data['supplier_id'];
           $warehouse_id = $data['warehouse_id'];
                $products = DB::table('products')
                //->where('tot.supplier_id',$supplier_id)
                //->where('tot.le_wh_id',$warehouse_id)
                //->where('tot.subscribe',1)
                ->where('products.is_sellable',1)
                ->where(function ($query) use($term) {
                    $query->orWhere('products.sku','like', '%'.$term.'%')
                          ->orWhere('products.product_title','like', '%'.$term.'%')
                          ->orWhere('products.upc','like', '%'.$term.'%');
                          //->orWhere('content.product_name','like', '%'.$term.'%');
                })
                ->leftJoin('brands','products.brand_id','=','brands.brand_id')
                ->leftJoin('product_content as content','products.product_id','=','content.product_id')
                ->leftJoin('product_tot as tot','products.product_id','=','tot.product_id')
                ->select('products.product_id','products.product_title','products.upc','products.sku','products.pack_size','products.seller_sku','products.mrp','brands.brand_id','brands.brand_name')
                ->groupBy('tot.product_id')->get()->all();

            $prodAry = array();
            if(count($products)>0){
                foreach($products as $product){
                    $brand = $product->brand_name;
                    $product_name = $product->product_title.' ( '.$brand.' )';
                    $product_id = $product->product_id;
                    $product_title = $product->product_title;
                    $sku = $product->sku;
                    $mrp = ($product->mrp!='')?$product->mrp:0;
                    $prod_arr = array("label" => $product_name, "product_id" => $product_id, "product_title" => $product_title, "brand" => $brand, "sku" => $sku,'mrp'=>'Rs. '.$mrp);
                    array_push($prodAry, $prod_arr);
                }
            }else{
                $prod_arr = array("label" => 'No Result Found','value'=>'');
                array_push($prodAry, $prod_arr);
            }
            echo json_encode($prodAry);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }
    /**
     *
     */

    public function getRemarkReasons($parentId) {
    	try{
    		$fields = array('reason.reason_id','reason.name', 'reason.description');
    		$query = DB::table('reason_master as reason')->select($fields);
    		$query->where('reason.parent_id', $parentId);
    		return $query->get()->all();
    	} catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }
    public function getTallyLedgerAccounts() {
        try {
            $fields = array('tlm_name', 'tlm_group', 'show_default');
            $query = DB::table('tally_ledger_master')->select($fields);
            $query->where('tlm_name','like','101%');
            $query->orWhere('tlm_name','like','120%');
            return $query->get()->all();
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage() . ' ' . $ex->getTraceAsString());
        }
    }
    public function getRemarkReasonsById($reasonId) {
    	try{
    		$fields = array('reason.reason_id','reason.name', 'reason.description');
    		$query = DB::table('reason_master as reason')->select($fields);
    		$query->where('reason.reason_id', $reasonId);
    		return $query->get()->all();
    	} catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }

    public function getUserByLeId($leId) {
    	try{
    		$fields = array('users.*');
    		$query = DB::table('users')->select($fields);
    		$query->where('users.legal_entity_id', $leId);
    		return $query->first();
    	} catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }

    public function getExpiredPO() {
    	try{
            $fields = array('po.po_id');
    		$query = DB::table('po')->select($fields);
    		$query->where('po.po_status', '87001');
    		$query->where(DB::raw('DATE(po.delivery_date)'),'<',date('Y-m-d'));
    		return $query->get()->all();
    	} catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }


    public function getActivePOS() {
    	try{
    		$fields = array('po.po_id', 'po.po_code');
    		$query = DB::table('po')->select($fields);
    		$query->whereIn('po.po_status', array('87001','87002'));
    		return $query->get()->all();
    	} catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }

    public function changeStatusOfExpiredPO() {
    	$poArr = $this->getExpiredPO();
    	if(is_array($poArr) && count($poArr) > 0) {
    		foreach ($poArr as $po) {
    			$this->updatePO($po->po_id, array('po_status'=>'87003'));
    		}
    	}
    }

    public function updatePO($poId, $dataArr) {
        try {
            DB::table('po')->where('po_id', $poId)->update($dataArr);
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function getUserEmailByRoleName($roleName) {
        try {
            $query = DB::table('users')->select('users.email_id');
            $query->join('user_roles', 'users.user_id', '=', 'user_roles.user_id');
            $query->join('roles', 'roles.role_id', '=', 'user_roles.role_id');
            $query->where('users.is_active',1);
            return $query->whereIn('roles.name', $roleName)->get()->all();
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function getUserEmailByIds($Ids) {
        try {
            if (is_array($Ids) && count($Ids) > 0) {
                $data = DB::table('users')->select('email_id')
                        ->wherein('user_id', $Ids)
                        ->where('is_active', 1)
                        ->get()->all();
                $emails = json_decode(json_encode($data, 1), true);
            } else {
                $emails = [];
            }
            return $emails;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return ['status'=>200,'message'=>$e->getMessage()];
        }
    }

    private function getTaxInfo($poArr) {
		$taxArr = array();
                if(is_array($poArr)) {
			foreach ($poArr as $product) {
                            $prodTaxArr = $this->getProductTaxClass($product->product_id);
                                $taxArr[$product->product_id] = $prodTaxArr;
			}
		}
		return $taxArr;
	}
    public function getProductTaxClass($product_id,$wh_state_code=4033,$seller_state_code=4033) {
        try {
            $url=env('APP_TAXAPI');
            $data['product_id'] = (int)$product_id;
            $data['buyer_state_id'] = (int)$wh_state_code;
            $data['seller_state_id'] = (int)$seller_state_code;
            $taxData = Utility::sendRequest($url,$data);
            return $taxData;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function getAllPODetails($filterData) {
        try {
            $_leModel = new LegalEntity();
            $suppliers = $_leModel->getSupplierId();
            $query = DB::table('vw_purchase_details')->select('*');
            if(count($suppliers)) {
                $query->whereIn('legal_entity_id',$suppliers);
            }

            if(count($filterData)>0 && isset($filterData['fdate']) && $filterData['fdate']!='' && $filterData['tdate']!=''){
                $fdate = (isset($filterData['fdate']))?$filterData['fdate']:'';
                $tdate = (isset($filterData['tdate']))?$filterData['tdate']:'';
            }else{
                $fdate = date('Y/m/01'); // hard-coded '01' for first day
                $tdate  = date('Y/m/t');
            }
            if($fdate!='' && $tdate!='') {
                $query->where('po_date','>=',date('Y-m-d 00:00:00',strtotime($fdate)));
                $query->where('po_date','<=',date('Y-m-d 23:59:59',strtotime($tdate)));
            }
            $query->orderBy('po_date','desc');
            $poProducts = $query->get()->all();
            return $poProducts;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    /*
	 * getSupplierIDByName() method is used to get supplier Id by Name
	 * @param $supplierName Integer
	 * @return Array
	 */

	public function getSupplierIDByName($supplierName) {
            try{
                $legalEntityId = Session::get('legal_entity_id');
                $legal_entity_type_id = [1002,1016,1014];
                $fieldArr = array('legal.legal_entity_id');
                $query = DB::table('legal_entities as legal')->select($fieldArr);
                $query->where('legal.parent_id', $legalEntityId);
                $query->whereIn( 'legal.legal_entity_type_id',$legal_entity_type_id);
                $query->where('legal.business_legal_name', $supplierName);
                return $query->first();
            }
            catch(Exception $e) {

            }
	}

    public function getSupplierIDCode($supp_code) {
            try{
                DB::enableQueryLog();

                // $legalEntityId = Session::get('legal_entity_id');
                $legal_entity_type_id = [1002,1016,1014];
                $fieldArr = array('legal.legal_entity_id');
                $query = DB::table('legal_entities as legal')->select($fieldArr);
                // $query->where('legal.parent_id', $legalEntityId);
                $query->whereIn( 'legal.legal_entity_type_id',$legal_entity_type_id);
                $query->where('legal.le_code', $supp_code);
                return $query->first();
            }
            catch(Exception $e) {

            }
    }
    /*
	 * getWarehouseIDByName() method is used to warehouse id by name
	 * @param $warehouseName Integer
	 * @return Array
	 */

	public function getWarehouseIDByName($warehouseName) {
            try{
                $legalEntityId = Session::get('legal_entity_id');
                $fieldArr = array('legal.le_wh_id');
                $query = DB::table('legalentity_warehouses as legal')->select($fieldArr);
                $query->where('legal.legal_entity_id', $legalEntityId);
                $query->where('legal.lp_wh_name', $warehouseName);
                return $query->first();
            }
            catch(Exception $e) {

            }
	}


    public function getWarehouseIDByCode($wh_code) {
        // $legalEntityId = Session::get('legal_entity_id');
        $fieldArr = array('legal.le_wh_id','legal.state');
        $query = DB::table('legalentity_warehouses as legal')->select($fieldArr);
        // $query->where('legal.legal_entity_id', $legalEntityId);
        $query->where('legal.le_wh_code', $wh_code);
        return $query->first();
    }


        /*
         * $indentsList = $indent->where(['indent_status' => 70001])
                    ->whereIn('legal_entity_id',$suppliers)
                    ->orderBy('indent_id','desc')
                    ->pluck('indent_id', 'indent_code')->all();
         */
    public function getProductInfoBySku($sku,$le_wh_id,$sup_id) {
        try {
            $fieldArr = array('products.product_id','products.brand_id','products.product_title as product_name','products.sku', 'products.seller_sku','products.upc',
                'products.pack_size','products.mrp','tot.dlp','tot.base_price','inventory.mbq','inventory.soh','inventory.order_qty','currency.symbol_right as symbol');
            $query = DB::table('products')->select($fieldArr);
            $query->leftJoin('product_tot as tot', function($join)
                    {
                        $join->on('products.product_id','=','tot.product_id');
                        //$join->on('tot.supplier_id','=','po.legal_entity_id');
                        //$join->on('tot.le_wh_id','=','po.le_wh_id');
                    });
            $query->leftJoin('inventory', function($join)
             {
                $join->on('products.product_id','=','inventory.product_id');
                $join->on('tot.le_wh_id','=','inventory.le_wh_id');
             });

            $query->leftJoin('currency','tot.currency_id','=','currency.currency_id');
            $query->where('products.sku', $sku);
            $query->where('tot.le_wh_id', $le_wh_id);
            $query->where('tot.supplier_id', $sup_id);
            $product = $query->first();
            return $product;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function getProductInfoByID($product_id,$supplier_id,$warehouse_id) {
        try {
            // getting global supplier id for stockists
            $globalSupperLier = DB::table('master_lookup')->select('description')->where('value',78023)->get()->all();
            $globalSupperLierId = isset($globalSupperLier[0]->description)?$globalSupperLier[0]->description:'NULL';
            $legal_entity_id = Session::get('legal_entity_id');
            // checking whether the current user is stockist or not
            $is_Stockist = $this->checkStockist($legal_entity_id);
            $dc_le_id_list = $this->getAllDCLeids();
            $globalSupperLierId = $globalSupperLierId.",".$dc_le_id_list;

            if($is_Stockist>0){
                // if stockist ,display prev_elp,thityd,std elp by Ebutor as global supplier
                $stockistQuery = "and pph.supplier_id IN ($globalSupperLierId)";
                $customer_type_id = $this->getStockistPriceGroup($legal_entity_id,$warehouse_id);
                $globalDcId = DB::table('master_lookup')->select('description')->where('value',78021)->get()->all();
                $globalDcId = isset($globalDcId[0]->description)?$globalDcId[0]->description:'NULL';
                //getting esp as default lp for stockist
                $esp = "(SELECT p_p.`ptr` FROM product_prices p_p  WHERE p_p.`product_id` = $product_id  AND p_p.`dc_id` = $globalDcId AND customer_type = $customer_type_id AND p_p.`effective_date` <= CURRENT_DATE ORDER BY effective_date limit 1) as dlp";
            }else{
                // if not stockist ,remove Ebutor as a supplier in the supplier list
                $stockistQuery = "and pph.supplier_id NOT IN ($globalSupperLierId)";
                $esp = 'tot.dlp';
            }
            $product = DB::table('products')
                        ->where('products.product_id',$product_id)
                        ->where('tot.supplier_id',$supplier_id)
                        ->where('tot.le_wh_id',$warehouse_id)
                        ->leftJoin('brands','products.brand_id','=','brands.brand_id')
                        ->leftJoin('product_content as content','products.product_id','=','content.product_id')
                        ->leftJoin('product_tot as tot','products.product_id','=','tot.product_id')
                        ->leftJoin('inventory', function($join)
                         {
                            $join->on('products.product_id','=','inventory.product_id');
                            $join->on('tot.le_wh_id','=','inventory.le_wh_id');
                         })
                        ->leftJoin('currency','tot.currency_id','=','currency.currency_id')
                        ->select('products.product_id','products.upc','products.product_title as pname','products.sku'
                                ,'products.pack_size','products.seller_sku','products.mrp','tot.base_price as price'
                                ,DB::raw($esp),'tot.supplier_id','tot.le_wh_id','products.product_type_id'
                                ,'brands.brand_id','brands.brand_name','inventory.mbq','inventory.soh'
                                ,'inventory.atp','inventory.order_qty','currency.symbol_left as symbol','products.is_sellable',
                                DB::raw('getPackType(products.product_id) AS packType'),
                                DB::raw('(select elp from purchase_price_history as pph where pph.product_id=products.product_id '.$stockistQuery.' and pph.le_wh_id = '.$warehouse_id.' order by pur_price_id desc limit 0,1) as prev_elp'),
                                DB::raw('getMastLookupValue(products.kvi) AS KVI'))
                        ->first();
            return $product;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function savePoProducts($po_product){
        try{
            DB::table('po_products')->insert($po_product);
        } catch (Exception $ex) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function savePo($podata){
        try{
           $poid = DB::table('po')->insertGetId($podata);
           DB::commit();
           return $poid;
        } catch (Exception $ex) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function checkPOProductExist($poId,$product_id){
        try{
            $fieldArr = array('po_products.po_id','po_products.product_id');
            $query = DB::table('po_products')->select($fieldArr);
            $query->where('po_products.po_id', $poId);
            $query->where('po_products.product_id', $product_id);
            $productInfo = $query->first();
            if($productInfo && count($productInfo)>0){
                return 1;
            }else{
                return 0;
            }
        } catch (Exception $ex) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function getPreUpdatePOProducts($poId,$product_id){
        try{
            $fieldArr = array('po_products.qty','po_products.uom','po_products.no_of_eaches','po_products.free_qty','po_products.free_uom','po_products.free_eaches'
                ,'po_products.is_tax_included','po_products.apply_discount','po_products.discount_type','po_products.discount','po_products.unit_price','po_products.price','po_products.tax_name','po_products.tax_per','po_products.tax_amt','po_products.sub_total');
            $query = DB::table('po_products')->select($fieldArr);
            $query->where('po_products.po_id', $poId);
            $query->where('po_products.product_id', $product_id);
            $productInfo = $query->first();
            return json_decode(json_encode($productInfo),true);
        } catch (Exception $ex) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function verifyNewProductInWH($warehouse_id,$product_id){
        try{
            $fieldArr = array('stock_inward_id','product_id','le_wh_id');
            $query = DB::table('stock_inward')->select($fieldArr);
            $query->where('stock_inward.le_wh_id', $warehouse_id);
            $query->where('stock_inward.product_id', $product_id);
            $query->groupBy('product_id');
            $productInfo = $query->get()->all();
            if($productInfo && count($productInfo)>0){
                return 1;
            }else{
                return 0;
            }
        } catch (Exception $ex) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function deletePoProducts($poId,$productId){
        try{
            $delete = DB::table('po_products')->where('product_id', $productId)
                            ->where('po_id',$poId)->delete();
            return $delete;
        } catch (Exception $ex) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function getInwardProductsCountByPOId($poId){
        try{
            $fieldArr = array('inward_products.product_id',DB::raw('SUM(inward_products.received_qty) as received'));
            $query = DB::table('inward_products')->select($fieldArr);
            $query->leftJoin('inward','inward.inward_id','=','inward_products.inward_id');
            $query->where('inward.po_no', $poId);
            $query->groupBy('inward_products.product_id');
            $results = $query->pluck('received','inward_products.product_id')->all();
            return $results;
        } catch (Exception $ex) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    	/*
	 * getAllInvoices() method is used to get all invoices based on order id
	 * @param $orderId Numeric
	 * @param $rowCount Numeric, default zero
	 * @param $offset Numeric, default zero
	 * @param $perpage Numeric default 10
	 * @param $filter Array, default array
	 * @return Array
	 */

    public function getAllInvoices($poId, $rowCount=0, $offset=0, $perpage=10, $filter=array()) {
        try {
		$fieldArr = array('invoice.*',DB::raw('SUM(items.qty) as totQty'), 'inward.inward_code');

		$query = DB::table('po_invoice_grid as invoice')->select($fieldArr);
		$query->join('po_invoice_products as items', 'invoice.po_invoice_grid_id', '=', 'items.po_invoice_grid_id');
		$query->join('inward', 'invoice.inward_id', '=', 'inward.inward_id');
		$query->where('inward.po_no', $poId);
		#echo $query->toSql();die;
		if($rowCount) {
			return $query->count();
		}
		else {
                    $query->groupBy('items.po_invoice_grid_id');
			return $query->get()->all();
		}
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
	}
    }
    public function getInwardDetailsById($inwardId){
        try{
            $fieldArr = array('inward_products.*','inward.grand_total','inward.discount_on_total','inward.shipping_fee','po_products.price as poprice','po_products.tax_name','legal_entities.business_legal_name','inward.le_wh_id');
            $query = DB::table('inward_products')->select($fieldArr);
            $query->leftJoin('inward','inward.inward_id','=','inward_products.inward_id');
            $query->leftJoin('po_products',function($join){
                $join->on('po_products.product_id','=','inward_products.product_id');
                $join->on('po_products.po_id','=','inward.po_no');
            });
            $query->leftJoin('legal_entities','legal_entities.legal_entity_id','=','inward.legal_entity_id');
            $query->where('inward.inward_id', $inwardId);
            $productInfo = $query->get()->all();
            return $productInfo;
        } catch (Exception $ex) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    	public function getPoInvoiceDetailById($invoiceId) {
		try	{
			$fieldArr = array(
                                            'po.po_id',
                                            'po.po_status',
                                            'po.po_code',
                                            'inward.inward_id',
                                            'inward.inward_code',
                                            'inward.created_at as inward_date',
                                            'inward.le_wh_id',
                                            'inward.legal_entity_id',
                                            'inward.shipping_fee',
                                            'inward.discount_on_total',
                                            'grid.invoice_code',
                                            'grid.billing_name',
                                            'grid.invoice_status',
                                            'grid.created_at as invoice_date',
                                            'grid.grand_total',
                                            'grid.approval_status',
                                            DB::raw('GetUserName(grid.created_by,2) AS user_name'),
                                            'invprod.product_id',
                                            'invprod.qty',
                                            'invprod.free_qty',
                                            'invprod.unit_price',
                                            'invprod.price',
                                            'invprod.sub_total',
                                            'invprod.tax_name',
                                            'invprod.tax_per',
                                            'invprod.tax_amount as tax_amt',
                                            'invprod.hsn_code',
                                            'invprod.tax_data',
                                            'invprod.discount_per',
                                            'invprod.discount_amount',
                                            'invprod.comment',
                                            'brands.brand_name',
                                            'gdsp.product_title',
                                            'gdsp.sku',
                                            'gdsp.mrp',
                                            );
			$query = DB::table('po_invoice_grid as grid')->select($fieldArr);
			$query->join('po_invoice_products as invprod', 'grid.po_invoice_grid_id', '=', 'invprod.po_invoice_grid_id');
			$query->join('inward', 'inward.inward_id', '=', 'grid.inward_id');
			$query->join('po', 'inward.po_no', '=', 'po.po_id');
			$query->join('products as gdsp', 'gdsp.product_id', '=', 'invprod.product_id');
			$query->leftJoin('brands', 'brands.brand_id', '=', 'gdsp.brand_id');
			//$query->join('users', 'users.user_id', '=', 'grid.created_by');
			$query->where('grid.po_invoice_grid_id', $invoiceId);
                        //$query->orderBy('parent_id', 'asc');
			$po = $query->get()->all();
			//echo $query->toSql();die;
			return $po;
		}
		catch(Exception $e) {

		}
	}
    public function savePOInvoice($invoiceGrid){
        try{
           return DB::table('po_invoice_grid')->insertGetId($invoiceGrid);
        } catch (Exception $ex) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function savePOInvoiceProducts($invoiceProduct){
        try{
           DB::table('po_invoice_products')->insert($invoiceProduct);
        } catch (Exception $ex) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function checkInvoiceByInwardId($inwardId) {
        try {
            $fieldArr = array(
                'grid.invoice_code',
                'grid.billing_name',
                'grid.invoice_status',
                'grid.created_at as invoice_date',
                'grid.grand_total',
            );
            $query = DB::table('po_invoice_grid as grid')->select($fieldArr);
            $query->where('grid.inward_id', $inwardId);
            $Invoice = $query->get()->all();
            if(count($Invoice)>0){
                return 1;
            }else{
                return 0;
            }
        } catch (Exception $e) {

        }
    }
    public function poInvoiceCountByPOId($poId) {
        try {
            $fieldArr = array(
                'grid.invoice_code',
                'grid.billing_name',
                'grid.invoice_status',
                'grid.created_at as invoice_date',
                'grid.grand_total',
            );
            $query = DB::table('po_invoice_grid as grid')->select($fieldArr);
            $query->join('inward', 'inward.inward_id', '=', 'grid.inward_id');
            $query->join('po', 'inward.po_no', '=', 'po.po_id');
            $query->where('inward.po_no', $poId);
            $Invoice = $query->get()->all();
            return count($Invoice);
        } catch (Exception $e) {

        }
    }

    public function getPoInvoiceIdByInwardId($inwardId) {
        try {
            $InvoiceId = 0;
            $query = DB::table('po_invoice_grid as grid')
                    ->where('grid.inward_id', $inwardId);
            $Invoice = $query->first(['po_invoice_grid_id']);
            if(!empty($Invoice))
            {
                $InvoiceId = property_exists($Invoice, 'po_invoice_grid_id') ? $Invoice->po_invoice_grid_id : 0;
            }
            return $InvoiceId;
        } catch (\ErrorException $ex) {
            \Log::info($ex->getMessage());
            \Log::info($ex->getTraceByString());
        }
    }
    public function updateStatusAWF($table,$unique_column,$approval_unique_id, $next_status_id){
        try{
            $status = explode(',',$next_status_id);
            $new_status = ($status[1]==0)?$status[0]:$status[1];
            $invoice = array(
                'approved_by'=>\Session::get('userId'),
                'approved_at'=>date('Y-m-d H:i:s')
            );
            if($table == 'po' && in_array($new_status, [57118,57032,57222,57223,57224]))
            {
                $invoice['payment_status'] = $new_status;
            }else{
                $invoice['approval_status'] = $new_status;
            }
            
            if($new_status == 1 && $table == 'po_invoice_grid')
            {
                $invoice['invoice_status'] = 11302;
            }
            if($new_status == 1 && $table == 'user_ecash_creditlimit')
            {
                DB::beginTransaction();
                $invoice['creditlimit'] = DB::raw('creditlimit+pre_approve_limit');
                $invoice['pre_approve_limit'] = 0;
                $getuserecashdetailsid=DB::table('user_ecash_credit_details')
                                        ->select('*')
                                        ->where($unique_column, $approval_unique_id)
                                        ->orderBy('user_ecash_details_id','DESC')
                                        ->first();
                $getuserecashdetailsid=json_decode(json_encode($getuserecashdetailsid),1);
                DB::table($table)->where($unique_column, $approval_unique_id)->update($invoice);
                if(!empty($getuserecashdetailsid))
                { 
                    $description='Amount of Rs '.$getuserecashdetailsid['amount_requested_to_approve'].' has been modified or updated';

                //DB::table('user_ecash_credit_details')->where($unique_column, $approval_unique_id)->where('status',1)->where('updated_status',0)->update(['updated_status'=>1]);

                  DB::table('user_ecash_credit_details')->where($unique_column, $approval_unique_id)->where('updated_status',0)->where('status',0)->where('user_ecash_details_id',$getuserecashdetailsid['user_ecash_details_id'])->update(['status'=>1,'description'=>$description]);
               }
               DB::commit();
            }elseif($new_status == 57200 && $table == 'user_ecash_creditlimit')
            {
                DB::beginTransaction();
                $invoice['pre_approve_limit'] = 0;
                $getuserecashdetailsid=DB::table('user_ecash_credit_details')
                                        ->select('*')
                                        ->where($unique_column, $approval_unique_id)
                                        ->orderBy('user_ecash_details_id','DESC')
                                        ->first();
                $getuserecashdetailsid=json_decode(json_encode($getuserecashdetailsid),1);
                DB::table($table)->where($unique_column, $approval_unique_id)->update($invoice);
                if(!empty($getuserecashdetailsid))
                {
                   $description='Amount of Rs '.$getuserecashdetailsid['amount_requested_to_approve'].' has been rejected';
                   DB::table('user_ecash_credit_details')->where($unique_column, $approval_unique_id)->where('status',0)->where('user_ecash_details_id',$getuserecashdetailsid['user_ecash_details_id'])->update(['description'=>$description,'updated_status'=>1]);    
                }                        
             DB::commit();   
            }elseif($table == 'user_ecash_creditlimit' && $new_status != 1 && $new_status != 57200){
                DB::beginTransaction();
                DB::table($table)->where($unique_column, $approval_unique_id)->update($invoice);
                DB::commit();
            }
            //DB::enableQueryLog();
            if($table != 'user_ecash_creditlimit'){
                DB::table($table)->where($unique_column, $approval_unique_id)->update($invoice);    
            }
            
            // if($table == "po" and $new_status==57107){
            //     $queue = new Queue();
            //     $query= DB::select(DB::raw("select * from po where po_id=$approval_unique_id"));
            //     $le_wh_id = $query[0]->le_wh_id;
            //     $args = array("ConsoleClass" => 'autosmsnotify', 'arguments' => array('notification_code'=>"NEWPRODUCT01","params"=>"$approval_unique_id,$le_wh_id"));
            //     $token_job = $queue->enqueue('default', 'ResqueJobRiver', $args);
            // }
            //Log::info(DB::getQueryLog());
            if($new_status == 1 && $table == 'inward')
            {
//                $grnModel = new Grn();
//                $grnModel->saveStockInward($approval_unique_id);
                app('App\Modules\Grn\Controllers\GrnController')->creatPurchaseVoucher($approval_unique_id);
                //$po_invoice_id = $this->getPoInvoiceIdByInwardId($approval_unique_id);
                //app('App\Modules\PurchaseOrder\Controllers\PurchaseInvoiceController')->creatPaymentVoucher($po_invoice_id);
                //$grnModel = new Grn();
                //$grnModel->updateElpData($approval_unique_id);
            }
            if($new_status == 1 && $table == 'payment_details')
            {
                app('App\Modules\PurchaseOrder\Controllers\PaymentController')->createVendorPaymentVoucher($approval_unique_id);
            }
        } catch (Exception $ex) {
            log::info($table);
            if($table == 'user_ecash_creditlimit'){
                DB::rollback();    
            }
            Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
        }
    }
    public function getApprovalHistory($module,$id) {
        $data= array();
        $isnewPo=0;
        $history=array();
        $totalhistory=array();
        $approval_flow_func = new CommonApprovalFlowFunctionModel();

        if($module=='Purchase Order' || $module=='GRN' || $module=='Purchase Return' || $module=='Credit Limit'){
            $totalhistory=$approval_flow_func->getApprovalHistoryFromCommentsTable($id,$module);
            if(count($totalhistory)>0){
                $history=json_decode($totalhistory[0]->comments,1);
                if(!is_array($history) || empty($history)){
                    $history=array();
                }
                $history = array_reverse($history);
            }else{
                $history=$approval_flow_func->getApprovalHistory($module,$id);
            }
        }else{
            $history=$approval_flow_func->getApprovalHistory($module,$id);
        }        

       /* if(count($history)>0){
            $isnewPo=1;
        }
        if($isnewPo==1){
            foreach ($history as $key => $value) {
                //print_r($value);exit;
                $dbhistory=DB::table('users as us')
                            ->where('us.user_id','=',$value['user_id'])
                            ->join('user_roles as ur','ur.user_id','=','us.user_id')
                            ->join('roles as rl','rl.role_id','=','ur.role_id')
                            ->join('master_lookup as ml',function($join) use($value){
                                    $join->on('ml.value','=',DB::raw($value['status_to_id']));
                                })
                            ->select('us.profile_picture','us.firstname','us.lastname',DB::raw('group_concat(rl.name) as name'),'ml.master_lookup_name')->get()->all();
                            echo "-----";
                print_r($dbhistory);exit;
                if(count($dbhistory)>0){
                    
                    $dbhistory[0]->created_at=$value['created_at'];
                    $dbhistory[0]->status_to_id=$value['status_to_id'];
                    $dbhistory[0]->status_from_id=$value['status_from_id'];
                    $dbhistory[0]->awf_comment=$value['awf_comment'];
                    $data[count($data)]=$dbhistory[0];
                }
            }
        }else{
            $data=DB::table('appr_workflow_history as hs')
                        ->join('users as us','us.user_id','=','hs.user_id')
                        ->join('user_roles as ur','ur.user_id','=','hs.user_id')
                        ->join('roles as rl','rl.role_id','=','ur.role_id')
                        ->join('master_lookup as ml','ml.value','=','hs.status_to_id')
                        ->select('us.profile_picture','us.firstname','us.lastname',DB::raw('group_concat(rl.name) as name'),'hs.created_at','hs.status_to_id','hs.status_from_id','hs.awf_comment','ml.master_lookup_name')
                        ->where('hs.awf_for_id',$id)
                        ->where('hs.awf_for_type',$module)
                        ->groupBy('hs.created_at')
                        ->orderBy('hs.created_at','desc')
                        ->get()->all();
                        echo '****';print_r($data);exit;

        }*/
        
        
       //print_r(array_reverse($data));exit;
        return json_decode(json_encode($history),true);
    }
    public function closePO($poId,$po_status_val,$reason){
        try{
            $po = array(
                'reason_to_close'=>$reason,
                'updated_by'=>\Session::get('userId'),
                'updated_at'=>date('Y-m-d H:i:s')
            );
            if($po_status_val==1){
                $po['is_closed']=1;
            }else if($po_status_val==87004){
                $po['po_status']=$po_status_val;
            }
            DB::table('po')->where('po_id', $poId)->update($po);
        } catch (Exception $ex) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    public function getPurchaseInvoiceDetailsById($purchaseInvoiceId)
    {
        try
        {
            if($purchaseInvoiceId > 0)
            {
                $fieldArr = ['po.payment_mode', 'po.payment_type', 'po.tlm_name', 'po.tlm_group', 'inward.inward_code',DB::raw('inward.created_at as inward_date'),
                    'po.legal_entity_id', 'po_invoice_grid.billing_name', 'po_invoice_grid.grand_total','po_invoice_grid.created_at', 'po_invoice_grid.invoice_code', 'po_invoice_grid.created_at'
                    ];
                DB::enableQueryLog();
                $poInvoiceData = DB::table('po_invoice_grid')
                        ->join('inward', 'inward.inward_id', '=', 'po_invoice_grid.inward_id')
                        ->join('po', 'po.po_id', '=', 'inward.po_no')
                        ->select($fieldArr)
                        ->where('po_invoice_grid.po_invoice_grid_id', $purchaseInvoiceId)
                        ->first();
//                echo "<pre>";
//                print_r(DB::getQueryLog());
//                print_r($query);
//                die;
                return $poInvoiceData;
            }
        } catch (Exception $e)
        {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function updateElp($poId,$product_id,$supplier_id,$warehouse_id,$tax_per)
    {
        try
        {
            if($poId > 0)
            {
                $priceHistory = $this->getPriceHistory($poId,$product_id,$supplier_id,$warehouse_id);
                if(isset($priceHistory) && is_array($priceHistory) && count($priceHistory)>0){
                    $this->deletePriceHistory($poId,$product_id,$supplier_id,$warehouse_id);
                    $lastPriceHistory = $this->getPriceHistory(0,$product_id,$supplier_id,$warehouse_id);
                    $dlp = isset($lastPriceHistory->elp)?$lastPriceHistory->elp:0;
                    $effective_date = isset($lastPriceHistory->effective_date)?$lastPriceHistory->effective_date:date('Y-m-d');
                    if($dlp>0){
                        $totArr['dlp']=$dlp;
                        $totArr['base_price']=($dlp/(100+$tax_per)*100);
                        $totArr['effective_date']=$effective_date;
                        $this->updateTot($product_id,$supplier_id,$warehouse_id,$totArr);
                    }

                }
            }
        } catch (Exception $e)
        {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function updateTot($product_id,$supplier_id,$warehouse_id,$totArr)
    {
        try
        {
            DB::table('product_tot')
                    ->where('product_id',$product_id)
                    ->where('supplier_id',$supplier_id)
                    ->where('le_wh_id',$warehouse_id)
                    ->update($totArr);
        } catch (Exception $e)
        {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function getPriceHistory($poId=0,$product_id,$supplier_id,$warehouse_id)
    {
        try
        {
            $query = DB::table('purchase_price_history as pph')
                    ->select('pph.*');
            if($poId!=0){
                $query->where('pph.po_id', $poId);
            }
            $query->where('pph.supplier_id', $supplier_id)
                ->where('pph.le_wh_id', $warehouse_id)
                ->where('pph.product_id', $product_id);
            if($poId==0){
                $query->orderBy('created_at','DESC');
                $priceHistory=$query->first();
            }else{
                $priceHistory=$query->get()->all();
            }
            return $priceHistory;
        } catch (Exception $e)
        {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function deletePriceHistory($poId, $product_id, $supplier_id, $warehouse_id) {
        try {
            $delete = DB::table('purchase_price_history')
                    ->where('po_id', $poId)
                    ->where('supplier_id', $supplier_id)
                    ->where('le_wh_id', $warehouse_id)
                    ->where('product_id', $product_id)
                    ->delete();
            return $delete;
        } catch (Exception $ex) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function saveDocument($docsArr) {
        try {
            $id = DB::table('po_docs')->insertGetId($docsArr);
            return $id;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function poDocUpdate($po_id,$docid) {
        try {
            $query = DB::table('po_docs');
            $query->where('po_docs.doc_id', $docid);
            $query->update(array('po_id' => $po_id));
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function deleteDoc($doc_id) {
        try {
            $query = DB::table('po_docs');
            $query->where('po_docs.doc_id', $doc_id)->delete();
            Session::put('podocs', array_diff(Session::get('podocs'), [$doc_id]));
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function getpoDocs($po_id) {
        try {
            $query = DB::table('po_docs');
            $query->where('po_docs.po_id', $po_id);
            return $docs = $query->get()->all();
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function checkSupplier($supplier_id) {
        try {
            $query = DB::table('legal_entities')
                        ->leftJoin('suppliers', 'suppliers.legal_entity_id', '=', 'legal_entities.legal_entity_id')
                        ->where(['legal_entities.is_approved' => 1, 'legal_entities.legal_entity_id'=>$supplier_id])
                        ->get(['legal_entities.legal_entity_id', 'legal_entities.business_legal_name'])->all();

            $legal_entity_type_id = [1014,1016];
            $query1 = DB::table('legal_entities')
                        ->where(['legal_entities.is_approved' => 1, 'legal_entities.legal_entity_id'=>$supplier_id])
                        ->whereIn( 'legal_entities.legal_entity_type_id',$legal_entity_type_id)
                        ->get(['legal_entities.legal_entity_id', 'legal_entities.business_legal_name'])->all();
            return array_merge($query,$query1);
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function getAllPayments($leId, $rowCount=0, $offset=0, $perpage=10,$filter=array(), $orderbyarray='',$module='PO') {
        try {
                $fieldArr = array('payment.*',
                    DB::raw('getMastLookupValue(payment.pay_type) AS payment_type'),
                    DB::raw('getMastLookupValue(payment.pay_for) AS pay_for_name'),
                    DB::raw('IF(payment.approval_status=1,"Payment Completed", getMastLookupValue(payment.approval_status)) as approval_status_name'),
                    DB::raw('GetUserName(payment.created_by,2) AS createdBy'),
                    DB::raw("(SELECT po_code FROM po WHERE po.po_id = payment.reff_id and payment.pay_for_module='PO') as 'po_code'"),
                    DB::raw("(select sum(po_products.sub_total) from po_products where po_products.po_id=payment.reff_id and payment.pay_for_module='PO') as po_value"),
                    DB::raw("(select SUM(inward.grand_total) from inward where inward.po_no=payment.reff_id and payment.pay_for_module='PO') as grn_value"),
                        );
		$query = DB::table('payment_details as payment')->select($fieldArr);
                $query->where('payment.txn_tolegal_id', $leId);

                if(isset($filter['pay_code']) && !empty($filter['pay_code'])) {
                    $query->where('payment.pay_code', $filter['pay_code']['operator'],$filter['pay_code']['value']);
                }
                if(isset($filter['po_code']) && !empty($filter['po_code'])) {
                    $query->where(DB::raw("(SELECT po_code FROM po WHERE po.po_id = payment.reff_id and payment.pay_for_module='PO')"), $filter['po_code']['operator'],$filter['po_code']['value']);
                }
                if(isset($filter['pay_type']) && !empty($filter['pay_type'])) {
                    $query->where(DB::raw('getMastLookupValue(payment.pay_type)'), $filter['pay_type']['operator'],$filter['pay_type']['value']);
                }
                if(isset($filter['pay_for']) && !empty($filter['pay_for'])) {
                    $query->where(DB::raw('getMastLookupValue(payment.pay_for)'), $filter['pay_for']['operator'],$filter['pay_for']['value']);
                }
                if(isset($filter['approval_status']) && !empty($filter['approval_status'])) {
                    $query->where(DB::raw('IF(payment.approval_status=1,"Payment Completed", getMastLookupValue(payment.approval_status))'), $filter['approval_status']['operator'],$filter['approval_status']['value']);
                }
                if(isset($filter['ledger_account']) && !empty($filter['ledger_account'])) {
                    $query->where('payment.ledger_account', $filter['ledger_account']['operator'],$filter['ledger_account']['value']);
                }
                if(isset($filter['pay_amount']) && !empty($filter['pay_amount'])) {
                    $query->where('payment.pay_amount', $filter['pay_amount']['operator'],$filter['pay_amount']['value']);
                }
                if(isset($filter['po_value']) && !empty($filter['po_value'])) {
                    $query->where(DB::raw("(select sum(po_products.sub_total) from po_products where po_products.po_id=payment.reff_id and payment.pay_for_module='PO')"), $filter['po_value']['operator'],$filter['po_value']['value']);
                }
                if(isset($filter['grn_value']) && !empty($filter['grn_value'])) {
                    $query->where(DB::raw("(select SUM(inward.grand_total) from inward where inward.po_no=payment.reff_id and payment.pay_for_module='PO')"), $filter['grn_value']['operator'],$filter['grn_value']['value']);
                }
                if(isset($filter['ledger_account']) && !empty($filter['ledger_account'])) {
                    $query->where('payment.ledger_account', $filter['ledger_account']['operator'],$filter['ledger_account']['value']);
                }
                if(isset($filter['txn_reff_code']) && !empty($filter['txn_reff_code'])) {
                    $query->where('payment.txn_reff_code', $filter['txn_reff_code']['operator'],$filter['txn_reff_code']['value']);
                }
                if(isset($filter['pay_utr_code']) && !empty($filter['pay_utr_code'])) {
                    $query->where('payment.pay_utr_code', $filter['pay_utr_code']['operator'],$filter['pay_utr_code']['value']);
                }
                if(isset($filter['createdBy']) && !empty($filter['createdBy'])) {
                    $query->where(DB::raw('GetUserName(payment.created_by,2)'), $filter['createdBy']['operator'],$filter['createdBy']['value']);
                }
                if(!empty($filter['pay_date'])) {
                        $fdate = '';
                        if(isset($filter['pay_date'][2]) && isset($filter['pay_date'][1]) && isset($filter['pay_date'][0])) {
                            $fdate = $filter['pay_date'][2].'-'.$filter['pay_date'][1].'-'.$filter['pay_date'][0];
                        }
        		if($filter['pay_date']['operator'] == '=' && !empty($fdate)) {
                            $query->whereBetween('payment.pay_date', [$fdate.' 00:00:00', $fdate.' 23:59:59']);
        		}
        		else if(!empty($fdate) && $filter['pay_date']['operator'] == '<' || $filter['pay_date']['operator'] == '<=') {
                            $query->where('payment.pay_date', $filter['pay_date']['operator'], $fdate.' 23:59:59');
        		}
        		else if(!empty($fdate)){
                            $query->where('payment.pay_date', $filter['pay_date']['operator'], $fdate.' 00:00:00');
        		}
        	}
                if(!empty($filter['created_at'])) {
                        $fdate = '';
                        if(isset($filter['created_at'][2]) && isset($filter['created_at'][1]) && isset($filter['created_at'][0])) {
                            $fdate = $filter['created_at'][2].'-'.$filter['created_at'][1].'-'.$filter['created_at'][0];
                        }
        		if($filter['created_at']['operator'] == '=' && !empty($fdate)) {
                            $query->whereBetween('payment.created_at', [$fdate.' 00:00:00', $fdate.' 23:59:59']);
        		}
        		else if(!empty($fdate) && $filter['created_at']['operator'] == '<' || $filter['created_at']['operator'] == '<=') {
                            $query->where('payment.created_at', $filter['created_at']['operator'], $fdate.' 23:59:59');
        		}
        		else if(!empty($fdate)){
                            $query->where('payment.created_at', $filter['created_at']['operator'], $fdate.' 00:00:00');
        		}
        	}
		if($rowCount) {
                    return $query->count();
		}
		else {
                    $query->skip($offset * $perpage)->take($perpage);
                    if (!empty($orderbyarray)) {
                        $orderClause = explode(" ", $orderbyarray);
                        $query->orderby($orderClause[0], $orderClause[1]);  //order by query
                    }
                return $query->get()->all();
		}
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
	}
    }
    public function getPaymentDetailsByCode($payCode) {
        try {
		$fieldArr = array('payment.*',
                    DB::raw('getMastLookupValue(payment.pay_type) AS payment_type'),
                    DB::raw('GetUserName(payment.created_by,2) AS createdBy'),
                    'legal_entities.business_legal_name',
                    'legal_entities.le_code',
                    'po.po_code',
                    'po.le_wh_id',
                    'inward.inward_code',
                    'po.created_at as poCreatedAt'
                        );
		$query = DB::connection('mysql-write')->table('payment_details as payment')->select($fieldArr);
		$query->join('po', 'payment.reff_id', '=', 'po.po_id');
                $query->leftJoin('inward', 'inward.po_no', '=', 'po.po_id');
		$query->join('legal_entities', 'legal_entities.legal_entity_id', '=', 'payment.txn_tolegal_id');
		$query->where('payment.pay_code', $payCode);
                return $query->first();
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
	}
    }
    public function getPaymentDetailByCode($payCode) {
        try {
		$fieldArr = array('payment.*',
                    DB::raw('getMastLookupValue(payment.pay_type) AS payment_type'),
                    DB::raw('GetUserName(payment.created_by,2) AS createdBy'),
                    DB::raw('IF(payment.approval_status=1,"Payment Completed", getMastLookupValue(payment.approval_status)) as approval_status_name'),
                    //'legal_entities.business_legal_name',
                        );
		$query = DB::connection('mysql-write')->table('payment_details as payment')->select($fieldArr);
		//$query->join('legal_entities', 'legal_entities.legal_entity_id', '=', 'payment.txn_tolegal_id');
		$query->where('payment.pay_code', $payCode);
                return $query->first();
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
	}
    }
    public function getPaymentDetailsById($payId) {
        try {
		$fieldArr = array('payment.*','bph.*',
                    DB::raw('getMastLookupValue(payment.pay_type) AS payment_type'),
                    DB::raw('GetUserName(payment.created_by,2) AS createdBy'),
                    DB::raw('IF(payment.approval_status=1,"Payment Completed", getMastLookupValue(payment.approval_status)) as approval_status_name'),
                    DB::raw('getMastLookupValue(payment.pay_for) AS pay_for_name'),
                   'legal_entities.business_legal_name',
                   'legal_entities.le_code',
                        );
		$query = DB::connection('mysql-write')->table('payment_details as payment')->select($fieldArr);
		$query->join('legal_entities', 'legal_entities.legal_entity_id', '=', 'payment.txn_tolegal_id');
        $query->leftJoin('brand_payment_histroy as bph','bph.pay_id','=','payment.pay_id');
		$query->where('payment.pay_id', $payId);
                return $query->first();
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
	}
    }
    public function getPOInvoiceGrandTotal($poId) {
        try {
            $fieldArr = array(DB::raw('SUM(grand_total) as grand_total'));
            $query = DB::table('inward')->select($fieldArr);
            $query->where('po_no', $poId);
            return $query->first();
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    public function getPayedAmount($poId) {
        try {
            $fieldArr = array(DB::raw('SUM(pay_amount) as totAmount'));
            $query = DB::table('payment_details')->select($fieldArr);
            $query->where('reff_id', $poId);
            $query->where('pay_for_module', 'PO');
            return $query->first();
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function getPOGRNValueByPoId($poId) {
        try {
            $fieldArr = array(
                'po.le_wh_id',
                'po.legal_entity_id',
                'po.po_id',
                'po.po_code',
                'po.payment_mode',
                'po.po_status',
                DB::raw('IF(po.approval_status=1,"Shelved", getMastLookupValue(po.approval_status)) as approval_status'),
                DB::raw('getMastLookupValue(po.payment_status) as payment_status'),
                'po.po_date',
                DB::raw('(select sum(po_products.sub_total) from po_products where po_products.po_id=po.po_id) as po_value'),
                DB::raw('(select SUM(inward.grand_total) from inward where inward.po_no=po.po_id) as grn_value'),
            );
            $query = DB::table('po')->select($fieldArr);
            $query->where('po.po_id', $poId);
            $result = $query->first();
            return $result;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function getTotalPaymentsBySupplier($leId,$payType='') {
        try {
            $fieldArr = array(
                DB::raw('sum(pd.pay_amount) as amount'),
            );
            $query = DB::table('payment_details as pd')->select($fieldArr);
            $query->where('pd.txn_tolegal_id', $leId);
            $query->where('pd.pay_for_module', 'PO');
            if($payType==''){
                $query->where('pd.pay_type','!=', 22014);
            }else if($payType==22014){
                $query->where('pd.pay_type', $payType);
            }
            $result = $query->first();
            return $result;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function getTotalPaymentsByPO($poId) {
        try {
            $fieldArr = array(
                DB::raw('sum(pd.pay_amount) as amount'),
            );
            $query = DB::table('payment_details as pd')->select($fieldArr);
            $query->where('pd.reff_id', $poId);
            $query->where('pd.pay_for_module', 'PO');
            $result = $query->first();
            return $result;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function getTotalGRNValBySupplier($leId) {
        try {
            $fieldArr = array(
                DB::raw('sum(inward.grand_total) as tot_grn_val'),
            );
            $query = DB::table('inward')->select($fieldArr);
            $query->where('inward.legal_entity_id', $leId);
            $result = $query->first();
            return $result;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function getIndentProduct($indentId,$product_id) {
        try {
            $fieldArr = array(
                'indentprod.indent_id',
                'indentprod.product_id',
                'indentprod.qty',
                'indentprod.target_elp',
                'indentprod.no_of_units',
                'indentprod.pack_type'
            );
            $query = DB::table('indent_products as indentprod')->select($fieldArr);
                        $query->where('indentprod.indent_id',$indentId);
                        $query->where('indentprod.product_id',$product_id);
            $result = $query->first();
            return $result;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function getPurchasedata($fdate, $tdate, $is_grndate,$dcNames) {
        $filters = $this->getAccesDetails();
        $dc_acess_list = $filters['dc_acess_list'];
        try {
            $fieldArr = array(
                DB::raw('Date(po.created_at)'),
                'legal_entities.business_legal_name',
                'legal_entities.le_code',
                DB::raw('getLeWhName(po.le_wh_id) as wh_name'),
                'legal_entities.gstin',
                DB::raw('(select GROUP_CONCAT(doc_ref_no)  FROM inward_docs where inward_id = inward.inward_id) as SupplierInvoice'),
                'inward.invoice_no',
                'inward.2a_invoice_no as twoainvoice',
                'inward.invoice_date',
                'po.po_code',
                DB::raw('Date(po.po_date)'),
                DB::raw('(select sum(po_products.sub_total) from po_products where po_products.po_id=po.po_id) as poValue'),
                'inward.inward_code',
                'inward.created_at as inward_date',
                'inward.grand_total as grnValue',
                'inward.discount_on_total as discount_total',
                'po_invoice_grid.grand_total as invoiceValue',
                'po_invoice_grid.po_invoice_grid_id',
                'inward.inward_id',
                'po.po_id',

            );
            $query = DB::table('po')->select($fieldArr);
            $query->leftJoin('inward', 'inward.po_no', '=', 'po.po_id');
            $query->leftJoin('po_invoice_grid', 'inward.inward_id', '=', 'po_invoice_grid.inward_id');
            $query->leftJoin('legal_entities', 'legal_entities.legal_entity_id', '=', 'po.legal_entity_id');

            $user_id = Session::get("userId");
            $userData = $this->checkUserIsSupplier($user_id);
            if (count($userData) == 0) {
                //$query->whereIn('po.legal_entity_id', $suppliers);
                $query->whereNotNull(DB::raw("FIND_IN_SET(po.le_wh_id,'$dc_acess_list')"));
            }

            if(count($userData) > 0){
                $brands = $this->getAllAccessBrands($user_id);
                $globalSupperLier = DB::table('master_lookup')->select('description')->where('value',78023)->get()->all();
                $globalSupperLierId = isset($globalSupperLier[0]->description)?$globalSupperLier[0]->description:'NULL';
                $query->leftJoin('po_products as pop', 'pop.po_id', '=', 'po.po_id');
                $query->leftJoin('products as pro', 'pop.product_id', '=', 'pro.product_id');
                $brands = implode(',',$brands);
                $query->whereIn('pro.brand_id', explode(',',$brands));
                $query->whereNotIn('po.legal_entity_id', [$globalSupperLierId]);
            }
            $query->whereIn('po.le_wh_id', $dcNames);
            if($is_grndate==1){
                $query->whereBetween('inward.created_at', ["$fdate".' 00:00:00', "$tdate".' 23:59:59']);

            }else{
                $query->whereBetween('po.po_date', ["$fdate".' 00:00:00', "$tdate".' 23:59:59']);

            }
            $query->orderBy('po.po_date','desc');
            $po = $query->get()->all();
            //echo $query->toSql();die;
            return $po;
        } catch (Exception $e) {

        }
    }
    public function getPurchaseHSNdata($fdate, $tdate, $is_grndate,$dcNames) {
        $filters = $this->getAccesDetails();
        $dc_acess_list = $filters['dc_acess_list'];
        try {
            $fieldArr = array(
                DB::raw('Date(po.created_at)'),
                'legal_entities.business_legal_name',
                DB::raw('getLeWhName(po.le_wh_id) as wh_name'),
                'legal_entities.gstin',
                DB::raw('(select GROUP_CONCAT(doc_ref_no) FROM inward_docs where inward_id = inward.inward_id) as SupplierInvoice'),
                'inward.invoice_no',
                'inward.invoice_date',
                'po.po_code',
                DB::raw('Date(po.po_date)'),
                DB::raw('(select sum(po_products.sub_total) from po_products where po_products.po_id=po.po_id) as poValue'),
                'inward.inward_code',
                'inward.created_at as inward_date',
                'inward.grand_total as grnValue',
                'inward.discount_on_total as discount_total',
                'ip.hsn_code',
                DB::raw('SUM(ip.sub_total) AS gstbase'),
                DB::raw('(SELECT tax_name FROM po_products WHERE po_products.po_id=inward.po_no AND po_products.product_id=ip.product_id LIMIT 1) AS tax_name'),
                'ip.tax_per',
                DB::raw('SUM(ip.tax_amount) AS tax_amount'),
                //'po_invoice_grid.grand_total as invoiceValue',
                //'po_invoice_grid.po_invoice_grid_id',
                'inward.inward_id',
                'po.po_id',
            );
            $query = DB::table('inward_products as ip')->select($fieldArr);
            $query->leftJoin('inward', 'inward.inward_id', '=', 'ip.inward_id');
            $query->leftJoin('po', 'po.po_id', '=', 'inward.po_no');
            $query->leftJoin('legal_entities', 'legal_entities.legal_entity_id', '=', 'inward.legal_entity_id');
            
            $query->whereIn('inward.le_wh_id', $dcNames);
            //if($is_grndate==1){
                $query->whereBetween('inward.created_at', ["$fdate".' 00:00:00', "$tdate".' 23:59:59']);
                $query->groupBy('inward.inward_id','ip.hsn_code','ip.tax_per');
                $query->orderBy('inward.created_at','desc');

            //}else{
                //$query->whereBetween('po.po_date', ["$fdate".' 00:00:00', "$tdate".' 23:59:59']);

            //}
            //DB::enableQueryLog();
            $po = $query->get()->all();
            //Log::info(DB::getQueryLog());
            //echo $query->toSql();die;
            return $po;
        } catch (Exception $e) {

        }
    }
    public function getInwardDetailById($inwardId)
    {
        try
        {
            $fieldArr = ['inward.grand_total',
                'inward.discount_on_total',
                'ip.tax_per',
                'ip.tax_amount',
                'ip.tax_data',
                DB::raw('(select tax_name from po_products where po_products.po_id=inward.po_no and po_products.product_id=ip.product_id limit 1) as tax_name'),
                'ip.discount_total',
                'ip.sub_total',
                'inward.inward_id',
                'inward.inward_code',
                'inward.created_at',
                ];
            $query = DB::table('inward')->select($fieldArr);
            $query->join('inward_products as ip', 'inward.inward_id', '=', 'ip.inward_id');
            $query->where('inward.inward_id', $inwardId);
            return $query->get()->all();
        } catch (Exception $e)
        {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function getInvoiceDetailById($invoice_grid_id)
    {
        try
        {
            $fieldArr = ['po_invoice_grid.grand_total',
                'po_invoice_grid.discount_on_total',
                'ip.tax_per',
                'ip.tax_amount',
                'ip.tax_name',
                'ip.tax_data',
                'ip.discount_amount as discount_total',
                'ip.price as sub_total',
                'po_invoice_grid.po_invoice_grid_id',
                'po_invoice_grid.invoice_code',
                'po_invoice_grid.created_at',
                ];
            $query = DB::table('po_invoice_grid')->select($fieldArr);
            $query->join('po_invoice_products as ip', 'po_invoice_grid.po_invoice_grid_id', '=', 'ip.po_invoice_grid_id');
            $query->where('po_invoice_grid.po_invoice_grid_id', $invoice_grid_id);
            return $query->get()->all();
        } catch (Exception $e)
        {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function updatePaymentdata($paymentdata, $pay_id) {
        try {
            $update = DB::table('payment_details')->where('pay_id', $pay_id)
                            ->update($paymentdata);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage() . ' ' . $ex->getTraceAsString());
            return Response::json(array('status' => 400, 'message' => 'Failed', 'po_id' => 0));
        }
    }
    /*
     * @func getAllWarehouses
     * @param $warehouseType
     *  -> 118001 -> for Dcs
     *  -> 118002 -> for Hubs
     */
    public function getAllWarehouses($warehouseType = "")
    {
      if($warehouseType == "" or $warehouseType == null)
        $warehouseType = 118001;
      $wareousesList = DB::table('legalentity_warehouses')
				->select('le_wh_id as lp_wh_id','lp_wh_name')
				->where([['status',1],['dc_type',$warehouseType],])
				->get()->all();
      return $wareousesList;
    }

    public function getAllStates($countryId)
    {
      $states = DB::table('zone')
        ->select('zone_id as state_id','name as state_name')
        ->where([['status',1],['country_id',$countryId]])
        ->orderBy('sort_order')
        ->get()->all();
      return $states;
    }

    public function getPOProductDetailsById($productId)
    {
      // DB::table("po_products")
    }


    public function addToStockistCart($variantarray, $customerId,$customer_token) {
        try {
            $sizeof_product_array = sizeof($variantarray);
            $cartArray = array();
            $cartArray['cart'] = array();
            for ($i = 0; $i < $sizeof_product_array; $i++) {
                $esu_qty = (isset($variantarray[$i]['esu_quantity']) && $variantarray[$i]['esu_quantity'] != '') ? $variantarray[$i]['esu_quantity'] : '';
                $produc_ID = $variantarray[$i]['product_id'];
                $esu_qty = $variantarray[$i]['esu_quantity'];
                $total_qty = $variantarray[$i]['quantity'];
                $total_price = $variantarray[$i]['total_price'];
                $rate = $variantarray[$i]['unit_price'];
                $margin = $variantarray[$i]['applied_margin'];
                $le_wh_id = $variantarray[$i]['le_wh_id'];
                $hub_id = $variantarray[$i]['hub_id'];
                $token = $customer_token;
                $check_cart_table = DB::table('cart')
                        ->select(DB::raw('count(cart_id) as cc,cart_id'))
                        ->where('product_id', '=', $produc_ID)
                        ->where('user_id', '=', $customerId)
                        ->get()->all();
                $cart_table = json_decode(json_encode($check_cart_table[0]), true);
                $cart_table_count = $cart_table['cc'];
                if ($cart_table_count == 0) {
                    $insert_product_id = DB::table('cart')
                            ->insert(['product_id' => $produc_ID,
                        'user_id' => "$customerId",
                        'session_id' => "$token",
                        'esu_quantity'=>$esu_qty,
                        'quantity' => $total_qty,
                        'total_price' => $total_price,
                        'rate' => $rate,
                        'margin' => $margin,
                        'le_wh_id_list'=>"$le_wh_id",
                        'le_wh_id'=>$le_wh_id,
                        'hub_id'=>$hub_id,
                        'created_at' => date("Y-m-d H:i:s")
                    ]);
                    $CART_ID = DB::getPdo()->lastInsertId();
                } else {

                    $update_cart_table = DB::Table('cart')
                            ->where('user_id', $customerId)
                            ->where('product_id', $produc_ID)
                            ->update(array('quantity' => $total_qty, 'total_price' => $total_price, 'rate' => $rate, 'margin' => $margin, 'updated_at' => date("Y-m-d H:i:s")));

                    $CART_ID = $cart_table['cart_id'];
                 }

                $cart_count = $this->add_cartcount($customerId);
                $cartArray['status'] = "added to cart successfully done";
                $cartArray['cartcount'] = $cart_count;
                $cartArray['cart'][$i] = $CART_ID;
            }
            return $cartArray;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return "cart Error!";
        }
    }

    public function add_cartcount($customer_id) {
        try {
            $query = DB::select(DB::raw("select count(cart_id) as cc from cart where user_id='" . $customer_id . "'"));
            if (empty($query)) {
                $count = 0;
            } else {
                $total = json_decode(json_encode($query[0]), true);
                $count = $total['cc'];
            }
            return $count;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    public function getTokenByUserId($user_id){
        $token = DB::table('users')->select('password_token')->where(['user_id'=>$user_id,'is_active'=>1])->get()->all();
        return isset($token[0]->password_token)?$token[0]->password_token:'';
    }

    
    public function checkOrderStatusByCount($order_code){
        $countQry = "SELECT COUNT(gds_order_id) AS count FROM `gds_orders` WHERE (`order_code` = '$order_code')";
        //Log::info($countQry);
        $count = DB::select(DB::raw($countQry));
        //Log::info($count);
        return $count[0]->count;
    }

    public function updateStockistOrderStatus($po_id,$order_code,$status=0){
        $status = DB::table('po')->where(['po_id'=>$po_id])->update(['po_so_status'=>$status,'po_so_order_code'=>$order_code]);
        return $status;
    }

    public function getOrderIdByCode($order_code){
        $order_id = DB::table('gds_orders')->select('gds_order_id')->where(['order_code'=>$order_code])
                ->orderBy('created_at','desc')
                ->get()->all();
        return isset($order_id[0]->gds_order_id)?$order_id[0]->gds_order_id:'';
    }

    public function checkLOCByLeID($le_id){
       // $checkLOC = "SELECT  vp.`Order_Limit`  FROM `vw_stockist_payment_details` vp WHERE vp.`cust_le_id` = '$le_id'";
        $checkLOC ="CALL get_StockistPaymentDetails(".$le_id.")";
        $checkLOC = DB::select(DB::raw($checkLOC));
        return isset($checkLOC[0]->Order_Limit)?$checkLOC[0]->Order_Limit:0;
    }

    public function getLePayments($le_id){
        $lePayments = "SELECT * FROM `vw_stockist_payment_details` vp WHERE vp.`cust_le_id` = '$le_id'";
        $lePayments = DB::select(DB::raw($lePayments));
        return isset($lePayments[0]->Order_Limit)?$lePayments[0]:array();
    }

    public function checkLOCByLeWhid($wid){
        //$checkLOC = "SELECT  vp.`Order_Limit`  FROM `vw_stockist_payment_details` vp WHERE vp.`le_wh_id` = '$wid'";
        $leid=DB::table('legalentity_warehouses')->select('legal_entity_id')->where('le_wh_id',$wid)->first();
        $leid=isset($leid->legal_entity_id)?$leid->legal_entity_id:0;
        $checkLOC ="CALL get_StockistPaymentDetails(".$leid.")";
        $checkLOC = DB::select(DB::raw($checkLOC));
        return isset($checkLOC[0]->Order_Limit)?$checkLOC[0]->Order_Limit:0;
    }

    public function getCustomerDataByNo($mobile_number){
        $customerData = DB::table('users')->select('password_token','user_id')->where(['mobile_no'=>$mobile_number,'is_active'=>1])->first();
        return $customerData;
    }

    public function getStockistPriceGroup($legalEntityId,$le_wh_id){
        $price_id_data = DB::table('stockist_price_mapping')->select('stockist_price_group_id')->where(['legal_entity_id'=>$legalEntityId,'le_wh_id'=>$le_wh_id])->first();
        return isset($price_id_data->stockist_price_group_id)?$price_id_data->stockist_price_group_id:0;
    }

    public function updateCustomerToken($user_id,$password_token){
        $update = DB::table('users')->where('user_id',$user_id)->update(['password_token'=>$password_token]);
        $status = 0;

        if($update){
            $status = 1;
        }else{
            Log::info($password_token."  Token Update Error for $user_id");
        }
        return $status;
    }

    public function checkPricingMismatch($po_id,$customer_type,$le_wh_id){
        /*$mismatchData = DB::table(DB::raw('po_products as pp , product_slab_flat as pf'))
                    ->select('pp.po_id','pp.product_id',DB::raw('getProductName(pp.product_id) as prd_name'),'pp.price','pf.unit_price AS slab_price','pp.unit_price AS po_price')
                    ->where(['pp.product_id' => 'pf.product_id',
                        'pf.customer_type' =>$customer_type,
                        'pp.po_id'=>$po_id,
                        'pf.wh_id'=>$le_wh_id])
                    ->havingRaw('pf.unit_price != ?',['pp.unit_price'])
                    ->groupBy('pp.product_id')
                    ->get()->all();*/
                    $mismatchData = DB::select(DB::raw('select `pp`.`sku`,`pp`.`po_id`, `pp`.`product_id`, getProductName(pp.product_id) as prd_name,getSkuById(pp.product_id) as sku, `pp`.`price`, `pf`.`unit_price` as `slab_price`, `pp`.`unit_price` as `po_price`,
                        getCpEnableSatus(`pp`.`product_id`,'.$le_wh_id.') AS cp_enable,
                        getIsSellableSatus(`pp`.`product_id`,'.$le_wh_id.') AS is_sellable from po_products as pp , product_slab_flat as pf where (`pp`.`product_id` = pf.product_id and `pf`.`customer_type` = '.$customer_type.' and `pp`.`po_id` = '.$po_id.' and `pf`.`wh_id` = '.$le_wh_id.') group by `pp`.`product_id` having pf.unit_price != pp.unit_price'));
        return $mismatchData;
    }

    public function getTotData($product_id,$le_wh_id,$supplier_id){
        $totData = DB::table('product_tot')
                    ->select('dlp')
                    ->where([
                        'le_wh_id'=>$le_wh_id,
                        'product_id'=>$product_id,
                        'supplier_id'=>$supplier_id
                    ])->first();
        return isset($totData->dlp)?$totData->dlp:0;
    }

    public function checkStockist($legal_entity_id){
        $count = DB::table('legal_entities')->whereIn('legal_entity_type_id',[1014,1016])->where('business_type_id',47001)->where('legal_entity_id',$legal_entity_id)->count();
        return $count;
    }
    public function getAccesDetails(){
         $this->_roleModel = new Role();
        $Json = json_decode($this->_roleModel->getFilterData(6), 1);
        $filters = json_decode($Json['sbu'], 1);
        $dc_acess_list = isset($filters['118001']) ? $filters['118001'] : 'NULL';
        $hub_acess_list = isset($filters['118002']) ? $filters['118002'] : 'NULL';
        $data['dc_acess_list'] = $dc_acess_list;
        $data['hub_acess_list'] = $hub_acess_list;
        return $data;
    }

    public function checkUserIsSupplier($user_id){
        if($user_id!=""){
            $userData = DB::select(DB::raw('select * from users u join legal_entities l on u.`legal_entity_id`= l.legal_entity_id where l.`legal_entity_type_id` IN (1006,1002,89002) AND  u.is_active=1 and u.user_id='.$user_id ));
        }else{
            $userData = [];
        }
        return $userData;
    }
    public function getAllAccessBrands($user_id){
        $brands=DB::table('user_permssion')
                           ->where(['permission_level_id' => 7, 'user_id' => $user_id])
                         ->pluck('object_id')->all();
        $manufacturer=DB::table('user_permssion')
                       ->where(['permission_level_id' => 11, 'user_id' => $user_id])
                     ->pluck('object_id')->all();            
       
        if(!empty($manufacturer)){
            $brandsFromManufacturer=DB::table('brands')
                                ->whereIn('mfg_id',$manufacturer)
                                ->pluck('brand_id')->all();
            $finalArray=implode(',',array_unique(array_merge($brands,$brandsFromManufacturer)));
            $finalArray=explode(',',$finalArray);
            
        }else{
            if(!in_array(0, $brands)){
                $finalArray = $brands;
            }
        }

        return $brands;
    }
    public function getPurchaseGSTdata($fdate,$tdate,$is_grndate,$dcNames){
            $fieldArr = array(
                'legal_entities.gstin',
                'legal_entities.business_legal_name',
                DB::raw('(select GROUP_CONCAT(doc_ref_no)  FROM inward_docs where inward_id = inward.inward_id ) as SupplierInvoice'),
                'inward.2a_invoice_no as twoainvoice',
                DB::raw("(CASE  WHEN `po`.`payment_mode` = 2 THEN  'R' ELSE 'R' END) AS 'paymentType'"),
                'inward.invoice_date',
                'inward.grand_total as grnValue',
                // 'inward.inward_code',
                // DB::raw('Date(po.created_at)'),
                //'z.name as State',
                // 'inward.created_at as inward_date',
                // 'po_invoice_grid.discount_on_total as discount_total',
                // 'po_invoice_grid.grand_total as invoiceValue',
                'po_invoice_grid.po_invoice_grid_id',
                'inward.inward_id',
                'po.po_id',
                DB::raw('(select sum(po_products.sub_total) from po_products where po_products.po_id=po.po_id) as poValue'),
                DB::raw('getStateNameById(legalentity_warehouses.state) as State'),
                'legalentity_warehouses.legal_entity_id as wh_legal_id',
                'po.legal_entity_id as sup_legal_id',
            );
            $query = DB::table('po')->select($fieldArr);
            $query->leftJoin('inward', 'inward.po_no', '=', 'po.po_id');
            $query->leftJoin('po_invoice_grid', 'inward.inward_id', '=', 'po_invoice_grid.inward_id');
            $query->leftJoin('legal_entities', 'legal_entities.legal_entity_id', '=', 'po.legal_entity_id');
            $query->leftJoin('legalentity_warehouses', 'legalentity_warehouses.le_wh_id', '=', 'po.le_wh_id');
            //$query->leftJoin('suppliers as sp','sp.legal_entity_id','=','po.legal_entity_id');
            //$query->leftJoin('zone AS z','z.zone_id','=','sp.sup_state');

            $user_id = Session::get("userId");
            $userData = $this->checkUserIsSupplier($user_id);
            if (count($userData) == 0) {
                //$query->whereIn('po.legal_entity_id', $suppliers);
              $query->whereIn('po.le_wh_id', $dcNames);
            }

            if(count($userData) > 0){
                $brands = $this->getAllAccessBrands($user_id);
                $globalSupperLier = DB::table('master_lookup')->select('description')->where('value',78023)->get()->all();
                $globalSupperLierId = isset($globalSupperLier[0]->description)?$globalSupperLier[0]->description:'NULL';
                $query->leftJoin('po_products as pop', 'pop.po_id', '=', 'po.po_id');
                $query->leftJoin('products as pro', 'pop.product_id', '=', 'pro.product_id');
                $brands = implode(',',$brands);
                $query->whereIn('pro.brand_id', explode(',',$brands));
                $query->whereNotIn('po.legal_entity_id', [$globalSupperLierId]);
            }

            if($is_grndate==1){
                $query->whereBetween('inward.created_at', ["$fdate".' 00:00:00', "$tdate".' 23:59:59']);

            }else{
                $query->whereBetween('po.po_date', ["$fdate".' 00:00:00', "$tdate".' 23:59:59']);

            }
            $query->orderBy('po.po_date','desc');
            $po = $query->get()->all();
            //echo $query->toSql();die;
            return $po;
    }

    public function getLegalEntityTypeId($le_id){
        $legal_entity_type_id = DB::table("legal_entities")->select('legal_entity_type_id')->where('legal_entity_id',$le_id)->first();
        return isset($legal_entity_type_id->legal_entity_type_id)?$legal_entity_type_id->legal_entity_type_id:0;
    }

    public function getDCFCData($le_id,$fieldArr = array("*")){
        array_push($fieldArr, DB::raw("(SELECT getMastLookupValue(legal_entities.legal_entity_type_id)) as btype"));
        $dcfcData = DB::table("legal_entities")->select($fieldArr)->leftJoin('dc_fc_mapping', 'dc_le_id', '=', 'legal_entities.legal_entity_id')->where('dc_fc_mapping.fc_le_id',$le_id)->get()->all();

        return $dcfcData;
    }

    public function checkInventory($product_id,$le_wh_id){

        $checkInventory = DB::table('inventory')
                                ->select(DB::raw('inv_display_mode'))
                                ->where('product_id', '=', $product_id)
                                ->where('le_wh_id', '=', $le_wh_id)
                                ->get()->all();
        $displaymode = isset($checkInventory[0]->inv_display_mode)?$checkInventory[0]->inv_display_mode:0;
        $query = DB::selectFromWriteConnection(DB::raw("select ($displaymode-(order_qty+reserved_qty)) as availQty from `inventory` where `product_id` = $product_id and `le_wh_id` = $le_wh_id"));

        $availQty = isset($query[0]->availQty) ? $query[0]->availQty:0;

        return $availQty;

    }
    public function checkIsSelfTax($leid){
        try{

         $selftax=DB::table('legal_entities')
                      ->select('is_self_tax')
                      ->where('legal_entity_id','=',$leid)
                      ->get()->all();
           return $selftax;           
        }catch (\ErrorException $ex) {
            \Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }

    public function getAllDCLeids(){
        $legal_entity_id=DB::table('legal_entities')
                      ->select(DB::raw('GROUP_CONCAT(legal_entity_id) as dc_le_id_list'))
                      ->where('legal_entity_type_id','=',1016)
                      ->first();
        return isset($legal_entity_id->dc_le_id_list)?$legal_entity_id->dc_le_id_list:'NULL';    
    }

    //txn_reff_code list for add payment
    public function getTxnsList($ref_Code){
        try{
        $txn_list = DB::table('payment_details')
                        ->where('txn_reff_code',$ref_Code)
                        ->count();
                    return $txn_list;
                }catch (\ErrorException $ex) {
            \Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }



    //suppliers list for indents

    public function getSuppliersforIndents($data)
    {
        try
        {
            $legal_entity_id = \Session::get('legal_entity_id');
            $legal_entity_type_id = $this->getLegalEntityTypeId($legal_entity_id);
            
            $this->_roleModel = new Role();
            $Json = json_decode($this->_roleModel->getFilterData(6), 1);
            $filters = json_decode($Json['sbu'], 1);
            $dc_acess_list = isset($filters['118001']) ? $filters['118001'] : 'NULL';


            $supplierList = DB::table('legal_entities')
                    ->join('suppliers', 'suppliers.legal_entity_id', '=', 'legal_entities.legal_entity_id')
                    ->where(['legal_entities.legal_entity_type_id' => 1002, 'suppliers.is_active' => 1, 'legal_entities.is_approved' => 1, 'parent_id'=>$legal_entity_id])
                    ->get(['legal_entities.legal_entity_id', 'legal_entities.business_legal_name','city','legal_entities.le_code'])->all();

            $supplierList=json_decode(json_encode($supplierList,1),1);
            $universalSupplier = DB::table('master_lookup')->select('description')->where('value','=',78023)->first();
            $legal_id = DB::table('legal_entities')->select(['legal_entity_id','business_legal_name'])->where('legal_entity_id','=',$universalSupplier->description)->first();
            array_push($supplierList, $legal_id);
            
            $field_data = array( 
    //DB::raw('distinct(pt.supplier_id) as suppliers'), 
    'business_legal_name','legal_entity_id',
    'address1','address2', 'city','pincode','pan_number','tin_number');

            $dc_fc_mapping_data = $this->getDCFCData($legal_entity_id,$field_data);
             $supplierList=array_merge($supplierList,$dc_fc_mapping_data);
            if($legal_entity_type_id == 1001){
                $legal_entity_type_id = [1014,1016];
            
                $fc_dc_legal_entities = DB::table('dc_fc_mapping')->select(DB::raw("GROUP_CONCAT(DISTINCT CONCAT(dc_le_id,',',fc_le_id) ) AS dc_le_id"))
                            ->whereIn('dc_fc_mapping.dc_le_wh_id', explode(',',$dc_acess_list))
                            ->orWhereIn('dc_fc_mapping.fc_le_wh_id', explode(',',$dc_acess_list))
                            ->first();
                $fc_dc_legal_entities = isset($fc_dc_legal_entities->dc_le_id) ? $fc_dc_legal_entities->dc_le_id : "";
                $dcfcList = array();
                if($fc_dc_legal_entities != ""){
                    $dcfcList = DB::table('legal_entities')
                            ->whereIn( 'legal_entities.legal_entity_id',explode(',',$fc_dc_legal_entities))
                            ->whereIn( 'legal_entities.legal_entity_type_id',$legal_entity_type_id)
                            ->get(['legal_entities.legal_entity_id', 'legal_entities.business_legal_name','city','legal_entities.le_code'])->all();
                }
                $supplierList=array_merge($supplierList,$dcfcList);
            }
            //echo '<pre/>';print_r($supplierList);exit;
            $response = json_decode(json_encode($supplierList,1),1);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
        return $response;
    }
    public function getInvoiceByCode($invoiceCode) {
        try{
            $fieldArr = array('gds_invoice_grid.invoice_code','gds_invoice_grid_id','gds_invoice_grid.created_at');
            $query = DB::table('gds_invoice_grid')->select($fieldArr)->where('invoice_code',$invoiceCode);
            return $query->first();
        }
        catch(Exception $e) {
                Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }	
    }
    
    public function getAllDCFCData(){
        $sqlQuery = "select * from legalentity_warehouses where dc_type=118001 and status=1";
        $allData = DB::select(DB::raw($sqlQuery));
        return json_encode($allData);
    }

    public function getUOMdata(){
        $sqlQuery = "SELECT * FROM master_lookup m WHERE m.`mas_cat_id`=16 AND m.`is_active`=1";
        $allData = DB::select(DB::raw($sqlQuery));
        return json_encode($allData);
    }

    public function getProductIdbySku($sku){
        $product_id = DB::table('products')->select('product_id')->where('sku',$sku)->first();
        return isset($product_id->product_id)?$product_id->product_id:0;
    }

    public function getApobData($leId){
        $fieldArr = array(
                            'legal.business_legal_name',
                            'lw.le_wh_id',
                            'lw.legal_entity_id',
                            'legal.logo',
                            'lw.address1',
                            DB::raw('lw.state as state_id'),
                            'lw.address2',
                            'lw.city',
                            'legal.logo',
                            'lw.pincode',
                        DB::raw("'' pan_number"),
                            'lw.tin_number as gstin', 
                            'countries.name as country_name', 
                            'zone.name as state_name',
                            'zone.name as state',
                            'zone.code as state_code'
                        );
        $dcApobData = DB::table("legalentity_warehouses as lw")
                    ->select($fieldArr)
                    ->leftJoin('dc_fc_mapping', 'dc_le_wh_id', '=', 'lw.le_wh_id')
                    ->leftJoin('legal_entities as legal', 'legal.legal_entity_id', '=', 'dc_fc_mapping.dc_le_id')
                    ->leftJoin('countries', 'countries.country_id', '=', 'lw.country')
                    ->leftJoin('zone', 'zone.zone_id', '=', 'lw.state')
                    ->where('dc_fc_mapping.fc_le_id',$leId)
                    ->where('lw.dc_type',118001)
                    ->first();
        return $dcApobData;
    }
    public function getPoIdBySOCode($order_code){
        $po_id = DB::table('po')->select('po_id')->where(['po_so_order_code'=>$order_code])->first();
        return isset($po_id->po_id) ? $po_id->po_id :0;
    }

    public function getCPEnableData($product_id,$le_wh_id){
        $cp_data = DB::table('product_cpenabled_dcfcwise')->select('cp_enabled','is_sellable')->where(['product_id'=>$product_id,'le_wh_id'=>$le_wh_id])->orderby('updated_at','desc')->first();
        return $cp_data;
    }

    public function deletePOPayment($Id) {
    try {
            
      $query = DB::table('vouchers')->select('voucher_code')->join('payment_details','payment_details.pay_code','=','vouchers.voucher_code')->where('payment_details.pay_id','=',$Id)->where('vouchers.is_posted',1)->first();
        if(!count($query)){
          $query= DB::selectFromWriteConnection(DB::raw("CALL delete_po_payment(".$Id.")"));
          return true;
        }
        else{
           return false;
        }

      } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function brandPaymentDetailsInsert($clicks,$clicks_cost,$clicks_amt,$impressions,$impressions_cost,$impressions_amt,$config_mapping_id,$LeId,$buId,$payment_amount,$payId,$item){
        try{
            $created_by=Session::get('userId');
            $checkpayid="select count(brand_pay_id) as brand_pay_id from brand_payment_histroy where pay_id=".$payId;
            $checkpayid=DB::selectFromWriteConnection(DB::raw($checkpayid));

            if($checkpayid[0]->brand_pay_id>0){

                $updatepaymentdata=DB::table('brand_payment_histroy')->where('pay_id',$payId)->update(['config_mapping_id'=>$config_mapping_id,'clicks'=>$clicks, 'click_cost'=>$clicks_cost,'click_amt'=> $clicks_amt,'impression_cost'=>$impressions_cost,'impressions'=>$impressions,'impression_amt'=>$impressions_amt,'bu_id'=>$buId,'updated_by'=>$created_by,'supplier_id'=>$LeId,'total_amt_paid'=>$payment_amount,'item_id'=>$item]);
                //$updatepaymentdata="update brand_payment_histroy set clicks=".$clicks.",click_cost=".$clicks_cost.",click_amt=". $clicks_amt.",impression_cost=".$impressions_cost.",impressions=".$impressions.",impression_amt=".$impressions_amt.",bu_id=".$buId.",updated_by=".$created_by.",item_id=".$item." where pay_id=".$payId." and config_mapping_id=".$config_mapping_id;
                
            }else{

                $brandpaymentsave=DB::table('brand_payment_histroy')->insert(['config_mapping_id'=> $config_mapping_id,'clicks'=>$clicks, 'click_cost'=>$clicks_cost,'click_amt'=> $clicks_amt,'impression_cost'=>$impressions_cost,'impressions'=>$impressions,'impression_amt'=>$impressions_amt,'bu_id'=>$buId,'created_by'=>$created_by,'supplier_id'=>$LeId,'total_amt_paid'=>$payment_amount,'pay_id'=>$payId,'item_id'=>$item]);
                
            }
        }
        catch(Exception $e) {
                Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    public function deleteBannerPopupPayments($config_mapping_id,$payId){
     try{
        $checkpayid="select count(brand_pay_id) as brand_pay_id from brand_payment_histroy where pay_id=".$payId." and config_mapping_id=".$config_mapping_id;
        $checkpayid=DB::selectFromWriteConnection(DB::raw($checkpayid));
        if($checkpayid[0]->brand_pay_id>0){
            $bannerpaymentdelete = DB::table("brand_payment_histroy")
                         ->where('config_mapping_id', '=', $config_mapping_id)->where('pay_id',$payId)->delete();
        }
     
     }
        catch(Exception $e) {
                Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }   
    }

    public function getUpcomingPayments($status, $orderbyarray, $filter = array(), $rowCount = 0, $offset = 0, $perpage = 10,$from_date = null ,$to_date= null ,$sup_name = null) {
        try {
            /*echo '<pre>';
            print_r($filter['from_date']);exit;*/

            $this->_roleModel = new Role();
            $legalEntityId = Session::get('legal_entity_id');
            $Json = json_decode($this->_roleModel->getFilterData(6), 1);
            $filters = json_decode($Json['sbu'], 1);
            $dc_acess_list = isset($filters['118001']) ? $filters['118001'] : 'NULL';
            $user_id = Session::get("userId");
            $roleRepo = new roleRepo();
            $globalFeature = $roleRepo->checkPermissionByFeatureCode('GLB0001',$user_id);
            $inActiveDCAccess = $roleRepo->checkPermissionByFeatureCode('GLBWH0001',$user_id);
            // initiation = 57203

            if( $status == 57203 ){
                $requested_query = DB::raw('vpr.requested_amount AS requested_amount');
            } else{
                $requested_query = DB::raw('
                    (SELECT SUM(vendor_payment_request.requested_amount) FROM vendor_payment_request WHERE vendor_payment_request.po_id=po.po_id AND vendor_payment_request.approval_status = 57203 ) AS requested_amount');               
            }

            
            $fieldArr = array(
                DB::raw('DATEDIFF(`po`.`payment_due_date`, now()) as duedays'),
                'po.le_wh_id',
                'po.legal_entity_id',
                //'vendor_payment_request.id AS request_id',
                //'vendor_payment_request.approved_amount AS appr_amt',
                //'vendor_payment_request.requested_amount AS req_amt',
                $requested_query,
                DB::raw('
                    (SELECT SUM(vendor_payment_request.approved_amount) FROM vendor_payment_request WHERE vendor_payment_request.po_id=po.po_id AND vendor_payment_request.approval_status IN ("57204", "57218", "57219", "57220") ) AS approved_amount'),
                //'vendor_payment_request.ebutor_bank_account',
                //'vendor_payment_request.utr_number', 
                //DB::raw('CASE
                //        WHEN vendor_payment_request.bank_status = 0 THEN "Successful"
                //        WHEN vendor_payment_request.bank_status = 1 THEN "Failed"
                //        ELSE " "
                //    END AS bank_status'),                
                //'vendor_payment_request.bank_comment', 
                //'vendor_payment_request.bank_payment_date',
                'po.po_id',
                'po.po_code',
                'po.parent_id',
                'po.po_validity',
                'po.payment_mode',
                'po.payment_due_date',
                'po.tlm_name',
                'po.po_status',
                'po.approval_status as approval_status_val',
                'po.po_so_order_code',
                DB::raw('IF(po.approval_status=1,"Shelved", getMastLookupValue(po.approval_status)) as approval_status'),
                DB::raw('getMastLookupValue(po.payment_status) as payment_status'),
                'po.created_at',
                'po.po_date',
                DB::raw('(select sum(po_products.sub_total) from po_products where po_products.po_id=po.po_id) as poValue'),
                DB::raw('GetUserName(po.created_by,2) AS user_name'),
                DB::raw('(select SUM(inward.grand_total) from inward where inward.po_no=po.po_id) as grn_value'),
                DB::raw('((select sum(po_products.sub_total) from po_products where po_products.po_id=po.po_id)-(select SUM(inward.grand_total) from inward where inward.po_no=po.po_id)) as po_grn_diff'),
                DB::raw('(select inward.created_at from inward where inward.po_no=po.po_id ORDER BY created_at DESC LIMIT 1) as grn_created'),
                'currency.code as currency_code',
                'currency.symbol_left as symbol',
                'legal_entities.business_legal_name',
                'legal_entities.le_code',
                'lwh.lp_wh_name',
                'lwh.city',
                'lwh.pincode',
                'lwh.address1',
                'z.name AS state_name'
            );
            $statuslist= ["57222","57223","allpo",""];
            if(!in_array($status, $statuslist)){
               $vpArr = array( 
                'vpr.id AS request_id',
                    'vpr.approved_amount AS appr_amt',
                    'vpr.requested_amount AS req_amt',
                    'vpr.ebutor_bank_account',
                    'vpr.utr_number',
                    DB::raw('CASE
                            WHEN vpr.bank_status = 0 THEN "Successful"
                            WHEN vpr.bank_status = 1 THEN "Failed"
                            ELSE " "
                        END AS bank_status'),                
                    'vpr.bank_comment', 
                    'vpr.bank_payment_date');
               $fieldArr = array_merge($fieldArr,$vpArr);
            }

            $query = DB::table('po')->select($fieldArr);

            //$query->whereNotNull('po.payment_due_date');            
            
            //$query->where('po.po_status', 87001);            
           // $query->where('po.payment_due_date', '!=', '0000-00-00 00:00:00');
            $query->leftJoin('legal_entities', 'legal_entities.legal_entity_id', '=', 'po.legal_entity_id');
            $query->join('legalentity_warehouses as lwh', 'lwh.le_wh_id', '=', 'po.le_wh_id');
            if(!in_array($status, $statuslist)){
                $query->leftJoin('vendor_payment_request as vpr', 'vpr.po_id', '=', 'po.po_id');
            }
            $query->join('zone as z', 'lwh.state', '=', 'z.zone_id');
            if(!$globalFeature){
                $query->join('user_permssion as up', function($join) use($user_id)
                 {
                    $join->on('up.object_id','=','lwh.bu_id');
                    $join->on('up.user_id','=',DB::raw($user_id));
                    $join->on('up.permission_level_id','=',DB::raw(6));
                 });
            }
            $query->leftJoin('currency', 'currency.currency_id', '=', 'po.currency_id');
            if (!$inActiveDCAccess) { // if user dont have access to inactive dc's
                $query->where(['lwh.status' => 1]); //query returns only active records
            }
            
            if (isset($filter['duedays']) && !empty($filter['duedays'])) { 
                $query->where( DB::raw('DATEDIFF(`po`.`payment_due_date`, NOW())'), $filter['duedays']['operator'], $filter['duedays']['value']);
            }
            if(isset($from_date) && $from_date != "" && isset($to_date) && $to_date != "") {

                $query->whereBetween('vpr.bank_payment_date', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
            }
              if(isset($from_date) && $from_date != "" && isset($to_date) && $to_date != ""&& isset($sup_name) && $sup_name != "") {

                $query->whereBetween('vpr.bank_payment_date', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);

                $query->where('legal_entities.legal_entity_id',$sup_name);
            }

           if(isset($filter['from_date']) && $filter['from_date'] != "" && isset($filter['to_date']) && $filter['to_date'] != ""&& isset($filter['sup_name']) && $filter['sup_name'] != "")  {

                $query->whereBetween('vpr.bank_payment_date', [$filter['from_date'] . ' 00:00:00', $filter['to_date'] . ' 23:59:59']);
                
                $query->where('legal_entities.legal_entity_id',$filter['sup_name']);
                 //echo $query->toSql();die;
              
            }
            if(isset($filter['from_date']) && $filter['from_date'] != "" && isset($filter['to_date']) && $filter['to_date'] != "")  {

                $query->whereBetween('vpr.bank_payment_date', [$filter['from_date'] . ' 00:00:00', $filter['to_date'] . ' 23:59:59']);
                
                 //echo $query->toSql();die;
              
            }
           

            if (isset($filter['po_status_id']) && is_array($filter['po_status_id'])) {
                $query->whereIn('po.po_status', $filter['po_status_id']);
            }
            if (isset($filter['po_status_id']) && !is_array($filter['po_status_id'])) {
                if ($filter['po_status_id'] == 87005) {
                    $query->where('po.po_status', $filter['po_status_id']);
                    $query->where('po.is_closed', 0);
                } else if ($filter['po_status_id'] == 87002) {
                    $query->where(function ($query) use($filter) {
                        $query->where('po.po_status', $filter['po_status_id'])
                                ->orWhere('po.is_closed', 1);
                    });
                    $query->whereNotIn('po.approval_status', [1, null, 0]);
                } else if ($filter['po_status_id'] == 87001) {
                    $query->where('po.po_status', $filter['po_status_id']);
                    $query->where('po.approval_status', '!=', 57117);
                } else if ($filter['po_status_id'] == 87004) {
                    $query->where(function ($query) use($filter) {
                        $query->where('po.po_status', $filter['po_status_id'])
                                ->orWhere('po.approval_status', 57117);
                    });
                } else {
                    $query->where('po.po_status', $filter['po_status_id']);
                }
            }
            if (isset($filter['approval_status_id']) && !is_array($filter['approval_status_id'])) {
                if ($filter['approval_status_id'] == 57032) {
                    $query->where(function ($query) {
                        $query->where('po.payment_mode', 2);
                        $query->orWhere('po.payment_due_date', '<=', date('Y-m-d') . ' 23:59:59');
                    });
                    $query->where(function ($query) {
                        $query->where('po.payment_status', 57118);
                        $query->orWhereNull('po.payment_status');
                    });
                    $query->whereNotIn('po.approval_status', [57117, 57106, 57029, 57030]);
                } else if ($filter['approval_status_id'] == 57107) {
                    $query->whereIn('po.approval_status', [57119, 57120, $filter['approval_status_id']]);
                    $query->whereNotIn('po.approval_status', [57117]);
                } else if ($filter['approval_status_id'] == 57034) {
                    $query->whereIn('po.approval_status', [57122, $filter['approval_status_id']]);
                    $query->whereNotIn('po.approval_status', [57117]);
                } else {
                    $query->where('po.approval_status', $filter['approval_status_id']);
                }
                $query->whereNotIn('po.po_status', [87003, 87004]);
            }
            if (isset($filter['po_code']) && !empty($filter['po_code'])) {
                $query->where('po.po_code', $filter['po_code']['operator'], $filter['po_code']['value']);
            }
            if (isset($filter['le_code']) && !empty($filter['le_code'])) {
                $query->where('legal_entities.le_code', $filter['le_code']['operator'], $filter['le_code']['value']);
            }
            if (isset($filter['Supplier']) && !empty($filter['Supplier'])) {
                $query->where('legal_entities.business_legal_name', $filter['Supplier']['operator'], $filter['Supplier']['value']);
            }
            if (isset($filter['city']) && !empty($filter['city'])) {
                $query->where('lwh.city', $filter['city']['operator'], $filter['city']['value']);
            }
            if (isset($filter['state_name']) && !empty($filter['state_name'])) {
                $query->where('z.name', $filter['state_name']['operator'], $filter['state_name']['value']);
            }
            if (isset($filter['shipTo']) && !empty($filter['shipTo'])) {
                $query->where('lwh.lp_wh_name', $filter['shipTo']['operator'], $filter['shipTo']['value']);
            }
            if (isset($filter['validity']) && !empty($filter['validity'])) {
                $query->where('po.po_validity', $filter['validity']['operator'], $filter['validity']['value']);
            }
            if (isset($filter['payment_mode']) && !empty($filter['payment_mode'])) {
                $query->whereIn('po.payment_mode', $filter['payment_mode']['value']);
            }
            if (isset($filter['tlm_name']) && !empty($filter['tlm_name'])) {
                $query->where('po.tlm_name', $filter['tlm_name']['operator'], $filter['tlm_name']['value']);
            }
            if (isset($filter['po_so_order_link']) && !empty($filter['po_so_order_link'])) {
                $query->where('po.po_so_order_code', $filter['po_so_order_link']['operator'], $filter['po_so_order_link']['value']);
            }
            if (!empty($filter['createdOn'])) {
                $fdate = '';
                if (isset($filter['createdOn'][2]) && isset($filter['createdOn'][1]) && isset($filter['createdOn'][0])) {
                    $fdate = $filter['createdOn'][2] . '-' . $filter['createdOn'][1] . '-' . $filter['createdOn'][0];
                }
                if ($filter['createdOn']['operator'] == '=' && !empty($fdate)) {
                    $query->whereBetween('po.po_date', [$fdate . ' 00:00:00', $fdate . ' 23:59:59']);
                } else if (!empty($fdate) && $filter['createdOn']['operator'] == '<' || $filter['createdOn']['operator'] == '<=') {
                    $query->where('po.po_date', $filter['createdOn']['operator'], $fdate . ' 23:59:59');
                } else if (!empty($fdate)) {
                    $query->where('po.po_date', $filter['createdOn']['operator'], $fdate . ' 00:00:00');
                }
            }
            //print_r($filter);die;
            if (!empty($filter['grn_created'])) {
                $gfdate = '';
                if (isset($filter['grn_created'][2]) && isset($filter['grn_created'][1]) && isset($filter['grn_created'][0])) {
                    $gfdate = $filter['grn_created'][2] . '-' . $filter['grn_created'][1] . '-' . $filter['grn_created'][0];
                }
                $grncreate_opr = $filter['grn_created']['operator'];
                if ($grncreate_opr == '=' && !empty($gfdate)) {
                    $query->whereBetween(DB::raw('(select inward.created_at from inward where inward.po_no=po.po_id ORDER BY created_at DESC LIMIT 1)'), [$gfdate . ' 00:00:00', $gfdate . ' 23:59:59']);
                } else if (!empty($gfdate)){
                    $timeapnd = ($grncreate_opr == '<' || $grncreate_opr == '<=')?' 23:59:59':' 00:00:00';
                    $query->where(DB::raw('(select inward.created_at from inward where inward.po_no=po.po_id ORDER BY created_at DESC LIMIT 1)'), $grncreate_opr, $gfdate . $timeapnd);
                }
            }
            if (!empty($filter['payment_due_date'])) {
                $fdate = '';
                if (isset($filter['payment_due_date'][2]) && isset($filter['payment_due_date'][1]) && isset($filter['payment_due_date'][0])) {
                    $fdate = $filter['payment_due_date'][2] . '-' . $filter['payment_due_date'][1] . '-' . $filter['payment_due_date'][0];
                }
                if ($filter['payment_due_date']['operator'] == '=' && !empty($fdate)) {
                    $query->whereBetween('po.payment_due_date', [$fdate . ' 00:00:00', $fdate . ' 23:59:59']);
                } else if (!empty($fdate) && $filter['payment_due_date']['operator'] == '<' || $filter['payment_due_date']['operator'] == '<=') {
                    $query->where('po.payment_due_date', $filter['payment_due_date']['operator'], $fdate . ' 23:59:59');
                } else if (!empty($fdate)) {
                    $query->where('po.payment_due_date', $filter['payment_due_date']['operator'], $fdate . ' 00:00:00');
                }
            }
//          print_r($filter);exit;
            if (isset($filter['createdBy']) && !empty($filter['createdBy'])) {
                $query->where(DB::raw('GetUserName(po.created_by,2)'), $filter['createdBy']['operator'], $filter['createdBy']['value']);
            }
            if (isset($filter['Status']) && !empty($filter['Status'])) {
                $query->where('lookup.master_lookup_name', $filter['Status']['operator'], $filter['Status']['value']);
            }
            if (isset($filter['payment_status']) && !empty($filter['payment_status'])) {
                $query->where(DB::raw('getMastLookupValue(po.payment_status)'), $filter['payment_status']['operator'], $filter['payment_status']['value']);
            }
            if (isset($filter['approval_status']) && !empty($filter['approval_status'])) {
                $query->where(DB::raw('IF(po.approval_status=1,"Shelved", getMastLookupValue(po.approval_status))'), $filter['approval_status']['operator'], $filter['approval_status']['value']);
            }
            if (isset($filter['approved_amount']) && !empty($filter['approved_amount'])) {
                $query->having(DB::raw('ROUND(approved_amount,2)'), $filter['approved_amount']['operator'], $filter['approved_amount']['value']);
            }
            if (isset($filter['requested_amount']) && !empty($filter['requested_amount'])) {
                $query->having(DB::raw('ROUND(requested_amount,2)'), $filter['requested_amount']['operator'], $filter['requested_amount']['value']);
            }
            if (isset($filter['poValue']) && !empty($filter['poValue'])) {
                $query->having(DB::raw('ROUND(poValue,2)'), $filter['poValue']['operator'], $filter['poValue']['value']);
            }
            if (isset($filter['grn_value']) && !empty($filter['grn_value'])) {
                $query->having(DB::raw('ROUND(grn_value,2)'), $filter['grn_value']['operator'], $filter['grn_value']['value']);
            }
            if (isset($filter['po_grn_diff']) && !empty($filter['po_grn_diff'])) {
                $query->having(DB::raw('ROUND(po_grn_diff,2)'), $filter['po_grn_diff']['operator'], $filter['po_grn_diff']['value']);
            }

            
            $user_id = Session::get("userId");
            $userData = $this->checkUserIsSupplier($user_id);
            if (count($userData) == 0) {
                //$query->whereIn('po.legal_entity_id', $suppliers);
                //$query->whereIn('po.le_wh_id', explode(',',$dc_acess_list));
            }
           // Log::info($dc_acess_list);
            if(count($userData) > 0){
                $brands = $this->getAllAccessBrands($user_id);
                $globalSupperLier = DB::table('master_lookup')->select('description')->where('value',78023)->get();
                $globalSupperLierId = isset($globalSupperLier[0]->description)?$globalSupperLier[0]->description:'NULL';
                $query->leftJoin('po_products as pop', 'pop.po_id', '=', 'po.po_id');
                $query->leftJoin('products as pro', 'pop.product_id', '=', 'pro.product_id');
                $brands = implode(',',$brands);
                $query->whereIn('pro.brand_id', explode(',',$brands));
                $query->whereNotIn('po.legal_entity_id', [$globalSupperLierId]);
                // Log::info($dc_acess_list);
                //$query->whereIn('po.le_wh_id', explode(',',$dc_acess_list));
            }
            $query->where('legal_entities.legal_entity_type_id', 1002);
            $query->whereNotIn('po.legal_entity_id', array(19980, 24766, 71976));
            if($status == 'allpo'){
                $query->groupBy('po.po_id');                
            //} elseif($status == 'total'){
            //    $query->groupBy('po.po_id');
            } else if($status == ''){
                $query->where(function ($query) {
                    // $query->where('po.payment_mode', 2);
                    // $query->orWhere('po.payment_due_date', '<=', date('Y-m-d') . ' 23:59:59');
                    $query->whereNotNull('po.payment_due_date');    
                    $query->where('po.payment_due_date', '!=', '0000-00-00 00:00:00');
                });
                $query->where(function ($query) {
                    $query->whereIn('po.payment_status', [57118,57224]);
                    $query->orWhereNull('po.payment_status');
                });
                $query->whereNotIn('po.approval_status', [57117, 57106, 57029, 57030]);
                $query->groupBy('po.po_id');
            } else if(in_array($status, [57222,57223])){
                $query->where('po.payment_status', $status);
                $query->groupBy('po.po_id');
            } else{
                
                    
                $query->where('vpr.approval_status', $status);
                if($status!="57219"){  // should not consider po payment status for completed requests
                    $query->whereNotIn('po.approval_status', [57117]);
                    $query->where(function ($query) {
                        $query->whereIn('po.payment_status', [57118,57224]); //only payment pending or paid part should show
                        $query->orWhereNull('po.payment_status');
                    });    
                }
                $query->groupBy('vpr.id');
            }
            if ($rowCount) {
                $po = $query->get()->count();
            } else {
                $offset = ($offset * $perpage);
                if (!empty($orderbyarray)) {
                    $orderClause = explode(" ", $orderbyarray);
                    $query->orderby($orderClause[0], $orderClause[1]);  //order by query
                } else {
                    $query->orderBy('po.po_id', 'desc');
                }
               
                $query->skip($offset)->take($perpage);
                $po = $query->get()->all();
            }
              //echo $query->toSql();die;
            //Log::info(DB::getQueryLog());
            
            return $po;

        } catch (Exception $e) {
            return Array('status' => 'failed', 'message' => $e->getMessage(), 'data' => []);
        }
    }


    /**
     * Get the list of payment request raised
     * @param  string  $orderbyarray Sort order of the records
     * @param  array   $filter       Filter for grid
     * @param  integer $rowCount     Total number of records
     * @param  integer $offset       Pagination Offset
     * @param  integer $perpage      Pagination Perpage
     * @return [type]                Array of payment request raised against 
     *                               the purchase order
     */
    public function getPaymentRequestRaisedList($orderbyarray, $filter = array(), $rowCount = 0, $offset = 0, $perpage = 10) {
        try {
            $this->_roleModel = new Role();
            $legalEntityId = Session::get('legal_entity_id');
            $Json = json_decode($this->_roleModel->getFilterData(6), 1);
            $filters = json_decode($Json['sbu'], 1);
            $dc_acess_list = isset($filters['118001']) ? $filters['118001'] : 'NULL';
            $fieldArr = array(
                DB::raw(' DATEDIFF(`po`.`payment_due_date`, now()) as duedays'),
                'po.le_wh_id',
                'vendor_payment_request.id AS request_id',
                'po.legal_entity_id',
                'po.po_id',
                'po.po_code',
                'po.parent_id',
                'po.po_validity',
                'po.payment_mode',
                'po.payment_due_date',
                'po.tlm_name',
                'po.po_status',
                'po.approval_status as approval_status_val',
                'po.po_so_order_code',
                DB::raw('IF(po.approval_status=1,"Shelved", getMastLookupValue(po.approval_status)) as approval_status'),
                DB::raw('getMastLookupValue(po.payment_status) as payment_status'),
                'po.created_at',
                'po.po_date',
                DB::raw('(select sum(po_products.sub_total) from po_products where po_products.po_id=po.po_id) as poValue'),
                DB::raw('GetUserName(po.created_by,2) AS user_name'),
                DB::raw('(select SUM(inward.grand_total) from inward where inward.po_no=po.po_id) as grn_value'),
                DB::raw('((select sum(po_products.sub_total) from po_products where po_products.po_id=po.po_id)-(select SUM(inward.grand_total) from inward where inward.po_no=po.po_id)) as po_grn_diff'),
                DB::raw('(select inward.created_at from inward where inward.po_no=po.po_id ORDER BY created_at DESC LIMIT 1) as grn_created'),
                'currency.code as currency_code',
                'currency.symbol_left as symbol',
                'legal_entities.business_legal_name',
                'legal_entities.le_code',
                'lwh.lp_wh_name',
                'lwh.city',
                'lwh.pincode',
                'lwh.address1'
            );

            $query = DB::table('po')->select($fieldArr);
            $query->whereNotNull('po.payment_due_date');
            $query->whereNotNull('vendor_payment_request.po_id');          
            $query->where('po.payment_due_date', '!=', '0000-00-00 00:00:00');
            $query->join('legal_entities', 'legal_entities.legal_entity_id', '=', 'po.legal_entity_id');
            $query->join('legalentity_warehouses as lwh', 'lwh.le_wh_id', '=', 'po.le_wh_id');
            $query->leftJoin('currency', 'currency.currency_id', '=', 'po.currency_id');
            $query->leftJoin('vendor_payment_request', 'vendor_payment_request.po_id', '=', 'po.po_id');

            if (isset($filter['duedays']) && !empty($filter['duedays'])) { 
                $query->where( DB::raw('DATEDIFF(`po`.`payment_due_date`, NOW())'), $filter['duedays']['operator'], $filter['duedays']['value']);
            }

            if (isset($filter['po_status_id']) && is_array($filter['po_status_id'])) {
                $query->whereIn('po.po_status', $filter['po_status_id']);
            }
            if (isset($filter['po_status_id']) && !is_array($filter['po_status_id'])) {
                if ($filter['po_status_id'] == 87005) {
                    $query->where('po.po_status', $filter['po_status_id']);
                    $query->where('po.is_closed', 0);
                } else if ($filter['po_status_id'] == 87002) {
                    $query->where(function ($query) use($filter) {
                        $query->where('po.po_status', $filter['po_status_id'])
                                ->orWhere('po.is_closed', 1);
                    });
                    $query->whereNotIn('po.approval_status', [1, null, 0]);
                } else if ($filter['po_status_id'] == 87001) {
                    $query->where('po.po_status', $filter['po_status_id']);
                    $query->where('po.approval_status', '!=', 57117);
                } else if ($filter['po_status_id'] == 87004) {
                    $query->where(function ($query) use($filter) {
                        $query->where('po.po_status', $filter['po_status_id'])
                                ->orWhere('po.approval_status', 57117);
                    });
                } else {
                    $query->where('po.po_status', $filter['po_status_id']);
                }
            }
            if (isset($filter['approval_status_id']) && !is_array($filter['approval_status_id'])) {
                if ($filter['approval_status_id'] == 57032) {
                    $query->where(function ($query) {
                        $query->where('po.payment_mode', 2);
                        $query->orWhere('po.payment_due_date', '<=', date('Y-m-d') . ' 23:59:59');
                    });
                    $query->where(function ($query) {
                        $query->where('po.payment_status', 57118);
                        $query->orWhereNull('po.payment_status');
                    });
                    $query->whereNotIn('po.approval_status', [57117, 57106, 57029, 57030]);
                } else if ($filter['approval_status_id'] == 57107) {
                    $query->whereIn('po.approval_status', [57119, 57120, $filter['approval_status_id']]);
                    $query->whereNotIn('po.approval_status', [57117]);
                } else if ($filter['approval_status_id'] == 57034) {
                    $query->whereIn('po.approval_status', [57122, $filter['approval_status_id']]);
                    $query->whereNotIn('po.approval_status', [57117]);
                } else {
                    $query->where('po.approval_status', $filter['approval_status_id']);
                }
                $query->whereNotIn('po.po_status', [87003, 87004]);
            }
            if (isset($filter['poId']) && !empty($filter['poId'])) {
                $query->where('po.po_code', $filter['poId']['operator'], $filter['poId']['value']);
            }
            if (isset($filter['le_code']) && !empty($filter['le_code'])) {
                $query->where('legal_entities.le_code', $filter['le_code']['operator'], $filter['le_code']['value']);
            }
            if (isset($filter['Supplier']) && !empty($filter['Supplier'])) {
                $query->where('legal_entities.business_legal_name', $filter['Supplier']['operator'], $filter['Supplier']['value']);
            }
            if (isset($filter['shipTo']) && !empty($filter['shipTo'])) {
                $query->where('lwh.lp_wh_name', $filter['shipTo']['operator'], $filter['shipTo']['value']);
            }
            if (isset($filter['validity']) && !empty($filter['validity'])) {
                $query->where('po.po_validity', $filter['validity']['operator'], $filter['validity']['value']);
            }
            if (isset($filter['payment_mode']) && !empty($filter['payment_mode'])) {
                $query->whereIn('po.payment_mode', $filter['payment_mode']['value']);
            }
            if (isset($filter['tlm_name']) && !empty($filter['tlm_name'])) {
                $query->where('po.tlm_name', $filter['tlm_name']['operator'], $filter['tlm_name']['value']);
            }
            if (isset($filter['po_so_order_link']) && !empty($filter['po_so_order_link'])) {
                $query->where('po.po_so_order_code', $filter['po_so_order_link']['operator'], $filter['po_so_order_link']['value']);
            }
            if (!empty($filter['createdOn'])) {
                $fdate = '';
                if (isset($filter['createdOn'][2]) && isset($filter['createdOn'][1]) && isset($filter['createdOn'][0])) {
                    $fdate = $filter['createdOn'][2] . '-' . $filter['createdOn'][1] . '-' . $filter['createdOn'][0];
                }
                if ($filter['createdOn']['operator'] == '=' && !empty($fdate)) {
                    $query->whereBetween('po.po_date', [$fdate . ' 00:00:00', $fdate . ' 23:59:59']);
                } else if (!empty($fdate) && $filter['createdOn']['operator'] == '<' || $filter['createdOn']['operator'] == '<=') {
                    $query->where('po.po_date', $filter['createdOn']['operator'], $fdate . ' 23:59:59');
                } else if (!empty($fdate)) {
                    $query->where('po.po_date', $filter['createdOn']['operator'], $fdate . ' 00:00:00');
                }
            }
            //print_r($filter);die;
            if (!empty($filter['grn_created'])) {
                $gfdate = '';
                if (isset($filter['grn_created'][2]) && isset($filter['grn_created'][1]) && isset($filter['grn_created'][0])) {
                    $gfdate = $filter['grn_created'][2] . '-' . $filter['grn_created'][1] . '-' . $filter['grn_created'][0];
                }
                $grncreate_opr = $filter['grn_created']['operator'];
                if ($grncreate_opr == '=' && !empty($gfdate)) {
                    $query->whereBetween(DB::raw('(select inward.created_at from inward where inward.po_no=po.po_id ORDER BY created_at DESC LIMIT 1)'), [$gfdate . ' 00:00:00', $gfdate . ' 23:59:59']);
                } else if (!empty($gfdate)){
                    $timeapnd = ($grncreate_opr == '<' || $grncreate_opr == '<=')?' 23:59:59':' 00:00:00';
                    $query->where(DB::raw('(select inward.created_at from inward where inward.po_no=po.po_id ORDER BY created_at DESC LIMIT 1)'), $grncreate_opr, $gfdate . $timeapnd);
                }
            }
            if (!empty($filter['payment_due_date'])) {
                $fdate = '';
                if (isset($filter['payment_due_date'][2]) && isset($filter['payment_due_date'][1]) && isset($filter['payment_due_date'][0])) {
                    $fdate = $filter['payment_due_date'][2] . '-' . $filter['payment_due_date'][1] . '-' . $filter['payment_due_date'][0];
                }
                if ($filter['payment_due_date']['operator'] == '=' && !empty($fdate)) {
                    $query->whereBetween('po.payment_due_date', [$fdate . ' 00:00:00', $fdate . ' 23:59:59']);
                } else if (!empty($fdate) && $filter['payment_due_date']['operator'] == '<' || $filter['payment_due_date']['operator'] == '<=') {
                    $query->where('po.payment_due_date', $filter['payment_due_date']['operator'], $fdate . ' 23:59:59');
                } else if (!empty($fdate)) {
                    $query->where('po.payment_due_date', $filter['payment_due_date']['operator'], $fdate . ' 00:00:00');
                }
            }
//          print_r($filter);exit;
            if (isset($filter['createdBy']) && !empty($filter['createdBy'])) {
                $query->where(DB::raw('GetUserName(po.created_by,2)'), $filter['createdBy']['operator'], $filter['createdBy']['value']);
            }
            if (isset($filter['Status']) && !empty($filter['Status'])) {
                $query->where('lookup.master_lookup_name', $filter['Status']['operator'], $filter['Status']['value']);
            }
            if (isset($filter['payment_status']) && !empty($filter['payment_status'])) {
                $query->where(DB::raw('getMastLookupValue(po.payment_status)'), $filter['payment_status']['operator'], $filter['payment_status']['value']);
            }
            if (isset($filter['approval_status']) && !empty($filter['approval_status'])) {
                $query->where(DB::raw('IF(po.approval_status=1,"Shelved", getMastLookupValue(po.approval_status))'), $filter['approval_status']['operator'], $filter['approval_status']['value']);
            }
            if (isset($filter['poValue']) && !empty($filter['poValue'])) {
                $query->having(DB::raw('ROUND(poValue,2)'), $filter['poValue']['operator'], $filter['poValue']['value']);
            }
            if (isset($filter['grn_value']) && !empty($filter['grn_value'])) {
                $query->having(DB::raw('ROUND(grn_value,2)'), $filter['grn_value']['operator'], $filter['grn_value']['value']);
            }
            if (isset($filter['po_grn_diff']) && !empty($filter['po_grn_diff'])) {
                $query->having(DB::raw('ROUND(po_grn_diff,2)'), $filter['po_grn_diff']['operator'], $filter['po_grn_diff']['value']);
            }

            
            $user_id = Session::get("userId");
            $userData = $this->checkUserIsSupplier($user_id);
            if (count($userData) == 0) {
                //$query->whereIn('po.legal_entity_id', $suppliers);
                $query->whereIn('po.le_wh_id', explode(',',$dc_acess_list));
            }
           // Log::info($dc_acess_list);
            if(count($userData) > 0){
                $brands = $this->getAllAccessBrands($user_id);
                $globalSupperLier = DB::table('master_lookup')->select('description')->where('value',78023)->get();
                $globalSupperLierId = isset($globalSupperLier[0]->description)?$globalSupperLier[0]->description:'NULL';
                $query->leftJoin('po_products as pop', 'pop.po_id', '=', 'po.po_id');
                $query->leftJoin('products as pro', 'pop.product_id', '=', 'pro.product_id');
                $brands = implode(',',$brands);
                $query->whereIn('pro.brand_id', explode(',',$brands));
                $query->whereNotIn('po.legal_entity_id', [$globalSupperLierId]);
           // Log::info($dc_acess_list);
                $query->whereIn('po.le_wh_id', explode(',',$dc_acess_list));
            }


            if ($rowCount) {
                $query->groupBy('po.po_id');
                $po = count($query->get());
            } else {
                $offset = ($offset * $perpage);
                if (!empty($orderbyarray)) {
                    $orderClause = explode(" ", $orderbyarray);
                    $query->orderby($orderClause[0], $orderClause[1]);  //order by query
                } else {
                    $query->orderBy('po.po_id', 'desc');
                }
                $query->groupBy('po.po_id');
                $query->skip($offset)->take($perpage);
                $po = $query->get();
            }
            //Log::info(DB::getQueryLog());
            //echo $query->toSql();die;
            return $po;
        } catch (Exception $e) {
            return Array('status' => 'failed', 'message' => $e->getMessage(), 'data' => []);
        }
    }

    /**
     * Get the purchase order detail by field name
     * @param  Integer $poId   Purchase order ID
     * @param  Array $fields Array of field name
     * @return [type]         [description]
     */
    public function getPoByFields($poId, $fields) {
        try {          
            $query = DB::table('po')->select($fields);
            $query->where('po_id', $poId);
            $po = $query->first();
            return $po;
        } catch (Exception $e) {

        }
    }

    /**
     * Get the purchase order total
     * @param  Integer $poId   Purchase order ID
     * @return integer         Total of order using PO_ID
     */
    public function getPoValue($poId) {
        try {        
            $query = DB::table('po_products')->select(DB::raw('sum(po_products.sub_total) as poValue'));
            $query->where('po_id', $poId);
            $po = $query->first();
            return $po;
        } catch (Exception $e) {

        }
    }

    /**
     * Get the PO count whose payment is pending
     * @return [type] [description]
     */
    function getPendingPaymentPOCount(){
        $fieldArr = array(
            DB::raw('count(po.po_id) AS total_pending')
        );

        $query = DB::table('po')->select($fieldArr);
        // $query->where(function ($query) {
        //     $query->where('po.payment_mode', 2);
        //     $query->orWhere('po.payment_due_date', '<=',date('Y-m-d').' 23:59:59');
        // });
        $query->where(function ($query) {
            $query->where('po.payment_status', 57118);
            $query->orWhereNull('po.payment_status');
        });
        $query->whereNotIn('po.approval_status', [57117,57106,57029,57030]);
       // $query->groupBy('po.po_id');
        return $po = $query->get();
    }
    /**
     * Get the PO count whose payment is initiated
     * @return [type] [description]
     */
    public function getPOPaymentRequests($po_id,$status){
        $fieldArr = array(
            DB::raw('id,po_id')
        );

        $query = DB::table('vendor_payment_request')->select($fieldArr);
        $query->where('po_id', $po_id);
        $query->whereIn('approval_status', $status);
       // $query->groupBy('po.po_id');
        return $po = $query->get()->all();
    }

    function getMappedSuppliersForManufacturer($manufacturer){

        $getmappedSuppliers=DB::table('supplier_brand_mapping')
                            ->select(DB::raw('GROUP_CONCAT(supplier_id) as supplier'));
        //if(!in_array(0, $manufacturer)){
        array_push($manufacturer, 0);
        $manufacturer=implode('|', $manufacturer);
          //$getmappedSuppliers=$getmappedSuppliers->whereIn('manufacturer_id',$manufacturer);
        $getmappedSuppliers = $getmappedSuppliers->whereRaw('CONCAT(",", manufacturer_id, ",") REGEXP ",('.$manufacturer.'),"');
        //}
        $getmappedSuppliers=$getmappedSuppliers->first();
        $getmappedSuppliers=isset($getmappedSuppliers->supplier)?$getmappedSuppliers->supplier:'';
        $getmappedSuppliers=explode(',', $getmappedSuppliers);
        return $getmappedSuppliers;
    }

    public function getSKUByProductId($pid){
        return $skucode=DB::table('products')->select('sku')->where('product_id',$pid)->first();
    }

    public function getProductAttributes($pid){
        return $offerpack=DB::table('product_attributes')->select('value')->where('attribute_id',6)->where('product_id',$pid)->first();
    }

    public function getKVIByProductId($pid){
        return $kvi=DB::table('products')->select('kvi')->where('product_id',$pid)->first();
    }
}
