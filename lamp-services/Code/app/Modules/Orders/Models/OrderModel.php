<?php

namespace App\Modules\Orders\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Indent\Models\LegalEntity;
use App\Modules\Roles\Models\Role;
use DB;
use Session;
use Log;
use Response;
use App\Modules\Cpmanager\Models\PickerModel;
use App\Central\Repositories\RoleRepo;
use UserActivity;
use Utility;
use App\Modules\Cpmanager\Controllers\MasterLookupController;


class OrderModel extends Model
{
    protected $table = "gds_orders";
    public $timestamps = false;
    protected $_roleRepo;

    public function __construct() {
    	$this->_roleRepo = new RoleRepo();
    }
	/*
	 * getOrderInfoById() method is used to get order information by order id
	 * @param $orderId Integer
	 * @fields Array
	 * @return Object
	 */
      public function getPartialCancelCount($filters){    	
      	$status = array('17009','17015');    	
      	$query = DB::table('gds_orders as go')->select(DB::raw('COUNT(DISTINCT go.gds_order_id) as tot'));			
      	$query->join('gds_cancel_grid as gcd', 'go.gds_order_id', '=', 'gcd.gds_order_id');			
      	$query->whereNOTIn('go.order_status_id',$status);

		if(!empty(Session::get('business_unitid')) && Session::get('business_unitid')!=0)
      	{
            $bu_id=Session::get('business_unitid');
            $userID = Session('userId');
            $globalAccess = $this->_roleRepo->checkPermissionByFeatureCode("GLBWH0001",$userID);
            if($globalAccess){
            	$data = DB::select(DB::raw("call getAllBuHierarchyByID($bu_id)"));
            }
            else{
            	$data = DB::select(DB::raw("call getBuHierarchyByID($bu_id)"));
            }
            $le_wh_ids=isset($data[0]->le_wh_ids) ? $data[0]->le_wh_ids :0;
            $array = explode(',', $le_wh_ids);
            $hubdata = DB::table('dc_hub_mapping')->select(DB::raw('GROUP_CONCAT(hub_id) as hubids'))->whereIn('dc_id',$array)->get()->all();
            $hubdata = isset($hubdata[0]->hubids) ? $hubdata[0]->hubids : 0;
    	}

      	if(Session::get('business_unitid')!=0 && !empty(Session::get('business_unitid')) && count($array)>0 && $le_wh_ids != "")
      	{
            
        	$query->whereRaw("go.le_wh_id IN (".$le_wh_ids.")");    
        }else{
        	$query->whereRaw("go.le_wh_id IN (0)");
    	}
      	
      	if(Session::get('business_unitid')!=0 && !empty(Session::get('business_unitid')) && !empty($hubdata))
      	{
        	$query->whereRaw("go.hub_id IN (".$hubdata.")");
    	}

      	$data = $query->first();
      	return isset($data->tot) ? $data->tot : 0;    
       }
       
	public function getOrderInfoById($orderId, $fields='') {
        try {
        	$fields = (empty($fields) ? 'orders.*' : $fields);
        	$query = DB::table('gds_orders as orders')->select($fields);
			$query->where('orders.gds_order_id', (int)$orderId);
			return $query->first();
		}
		catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}

	public function getToEmail($created_by){
		try {
			if(!empty($created_by)){
				$to_email = DB::table('users')->where('user_id',$created_by)->where('is_active',1)->select('email_id')->first();
				$to_email = isset($to_email->email_id) ? $to_email->email_id : '';
				return $to_email;
			}
		} catch (Exception $e) {
			 Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());	
		}
	}
	public function getFieldFrcPhoneNo($created_by){
		try {
			if(!empty($created_by)){
				$to_phoneno = DB::table('users')->where('user_id',$created_by)->select('mobile_no')->first();
				$to_phoneno = isset($to_phoneno->mobile_no) ? $to_phoneno->mobile_no : '';
				return $to_phoneno;
			}
		} catch (Exception $e) {
			 Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());	
		}
	}

	public function getUserEmailByRoleName($roleName) {
        try {
            $query = DB::table('users')->select('users.email_id');
            $query->join('user_roles', 'users.user_id', '=', 'user_roles.user_id');
            $query->join('roles', 'roles.role_id', '=', 'user_roles.role_id');
            $query->where('users.is_active','=', 1);
            return $query->whereIn('roles.name', $roleName)->get()->all();
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
	/*
	 * getOrderDetailById() method is used to get order information by order id
	 * @param $orderId Integer
	 * @return Array
	 */

	public function getOrderDetailById($orderId) {
       // try {
                $orderId = (int)$orderId;
                $fieldArr = array(
						'orders.legal_entity_id',
						'orders.le_wh_id',
						'orders.hub_id',
						'orders.gds_order_id',
						'orders.instant_wallet_cashback',
						'orders.cashback_amount',
						'orders.gds_cust_id',
						'orders.order_code',
                        'orders.mp_id',
						'orders.mp_order_id',
						'orders.firstname',
						'orders.lastname',
						'orders.discount',
						'orders.discount_type',
						'orders.discount_amt',
						'orders.lastname',
						'orders.created_by',
						'orders.email',
						'orders.shop_name',
						'orders.phone_no',
						'orders.order_date',
						'orders.total as order_value',
						'orders.order_status_id',
						'orders.ship_total',
						'orders.tax_total',
						'orders.sub_total',
						'orders.discount_amt as discount_total',
						'orders.total as grand_total',
						'orders.order_expiry_date',
						'orders.scheduled_delivery_date',
						'orders.pref_slab1',
						'orders.pref_slab2',
						'orders.cust_le_id',
						'orders.order_transit_status',
						'orders.is_self',
						'orders.discount_before_tax',
						'mp.mp_name',
						'mp.mp_logo',
						'mp.mp_url',
						'payment.payment_method_id',
						DB::raw('getMastLookupValue(payment.payment_method_id) as payment_method'),
						'payment.payment_status_id',
						'currency.code',
						'currency.symbol_left as symbol',
						'le.le_code',
						'le.legal_entity_type_id',
						'le.business_legal_name',
                        'pjp.pjp_name as beat',
                        'pjp.pdp',
                        'pjp.pdp_slot',
            			'spokes.spoke_name as spokeName',
                        'city.officename as areaname',
                        'lw.lp_wh_name as hub_name',
                    DB::raw('(select user_id from users where users.legal_entity_id=orders.cust_le_id and users.is_parent=1 limit 1) as cust_user_id'),
                    DB::raw('(select aadhar_id from users where users.legal_entity_id=orders.cust_le_id and users.is_parent=1 limit 1) as aadhar_id'),
                    	'lw.state'
						);

		$query = DB::table('gds_orders as orders')->select($fieldArr);
		$query->where('orders.gds_order_id',$orderId);
		$query->leftjoin('mp', 'orders.mp_id', '=', 'mp.mp_id');
        $query->leftJoin('customers as cust', 'cust.le_id', '=', 'orders.cust_le_id');
       	$query->leftJoin('cities_pincodes as city', 'city.city_id', '=', 'cust.area_id');
		$query->leftJoin('pjp_pincode_area as pjp', 'pjp.pjp_pincode_area_id', '=', 'orders.beat');
		$query->leftJoin('spokes','pjp.spoke_id','=','spokes.spoke_id');
		$query->leftjoin('gds_orders_payment as payment', 'payment.gds_order_id', '=', 'orders.gds_order_id');
                $query->leftjoin('currency', 'payment.currency_id', '=', 'currency.currency_id');
        $query->leftjoin('legal_entities as le', 'le.legal_entity_id', '=', 'orders.cust_le_id');        
		$query->leftJoin('legalentity_warehouses as lw', 'lw.le_wh_id', '=', 'orders.hub_id');
		//echo $query->toSql();//die;
		$orders = $query->first();
                return $orders;
		/*}
		catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}*/

	}

	/**
	 * [getOrderDetailByChannelOrderId description]
	 * @param  [mixed] $channelOrderId [description]
	 * @param  [int] 	$mp_id [channel/marketplaceId]
	 * @return [array]          [description]
	 */
	public function getOrderDetailByChannelOrderId($channelOrderId,$mp_id) {
		try{
			$fieldArr = array(
							'orders.legal_entity_id',
							'orders.gds_order_id',
							'orders.mp_order_id',
                                                        'orders.mp_id',
							'orders.firstname',
							'orders.lastname',
							'orders.email',
							'orders.phone_no',
							'orders.order_date',
							'orders.total as order_value',
							'orders.order_status_id',
							'orders.ship_total',
							'orders.tax_total',
							'orders.sub_total',
							'orders.discount_amt as discount_total',
							'orders.total as grand_total',
							'orders.discount_type',
							'mp.mp_name',
							'mp.mp_logo',
							'mp.mp_url',
							'payment.payment_method_id',
							'payment.payment_status_id',
							'currency.code',
							'currency.symbol_left as symbol',
							);

			$query = DB::table('gds_orders as orders')->select($fieldArr);
			$query->where('orders.mp_order_id', $channelOrderId);
			$query->where('orders.mp_id',$mp_id);
			$query->join('mp', 'orders.mp_id', '=', 'mp.mp_id');
			$query->join('gds_orders_payment as payment', 'payment.gds_order_id', '=', 'orders.gds_order_id');
	                $query->join('currency', 'payment.currency_id', '=', 'currency.currency_id');
			//echo $query->toSql();die;
			$orders = $query->first();
			return $orders;
		}
		catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}

	/*
	 * getBillingAndShippingAddressByOrderId() method is used to get
	 * billing and shipping address by order id
	 * @param Null
	 * @return Array
	 */

	public function getBillingAndShippingAddressByOrderId($orderId) {

		try{
			$fieldArr = array(
						'address.fname',
						'address.mname',
						'address.lname',
						'address.company',
						'address.address_type',
						'address.addr1',
						'address.addr2',
						'address.city',
						'address.postcode',
						'address.suffix',
						'address.telephone',
						'address.mobile',
						'countries.name as country_name',
						'zone.name as state_name'
						);

			$query = DB::table('gds_orders_addresses as address')->select($fieldArr);
			$query->where('address.gds_order_id', $orderId);
			$query->leftJoin('countries', 'countries.country_id', '=', 'address.country_id');
			$query->leftJoin('zone', 'zone.zone_id', '=', 'address.state_id');
			//echo $query->toSql();die;
			$address = $query->get()->all();
			return $address;
		}
		catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}


	/*
	 * getBillingAndShippingAddressByOrderId() method is used to get
	 * billing and shipping address by order id
	 * @param Null
	 * @return Array
	 */

	public function getBillAndShipAddrFrmLE($orderId) {

		try{
			$fieldArr = array(
						'orders.firstname as fname',
						DB::raw("'' as mname"),
						'orders.lastname as lname',
						'orders.shop_name as company',
						DB::raw("'shipping' as address_type"),
						'orders.phone_no as telephone',
						DB::raw("'' as suffix"),
						'legal_entities.address1 as addr1',
						'legal_entities.address2 as addr2',
						'legal_entities.city',
						DB::raw("'' as mobile"),
						'legal_entities.pincode as postcode',
						'legal_entities.locality',
						'legal_entities.landmark',
						'legal_entities.gstin',
						'countries.name as country_name',
						'zone.name as state_name',
						'zone.code as state_code',
						'zone_area.name as area_name',
						'retailer_flat.locality',
						'retailer_flat.landmark',
						'retailer_flat.fssai'
						);


			$query = DB::table('gds_orders as orders')->select($fieldArr);
			$query->where('orders.gds_order_id', $orderId);
			$query->join('legal_entities', 'legal_entities.legal_entity_id', '=', 'orders.cust_le_id');
			$query->leftjoin('customers', 'customers.le_id', '=', 'legal_entities.legal_entity_id');
			$query->leftjoin('zone as zone_area', 'zone_area.zone_id', '=', 'customers.area_id');
			$query->leftJoin('countries', 'countries.country_id', '=', 'legal_entities.country');
			$query->leftJoin('zone', 'zone.zone_id', '=', 'legal_entities.state_id');
			$query->leftjoin('retailer_flat', 'retailer_flat.legal_entity_id', '=', 'legal_entities.legal_entity_id');
			// echo $query->toSql();die;
			$address = $query->get()->all();

			return $address;
		}
		catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
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
							'legal.address1',
							'legal.address2',
							'legal.city',
							'legal.pincode',
							'legal.pan_number',
							'legal.tin_number',
							'legal.fssai',
							'countries.name as country_name',
							'zone.name as state_name'
						);

			$query = DB::table('legal_entities as legal')->select($fieldArr);
			$query->leftJoin('countries', 'countries.country_id', '=', 'legal.country');
			$query->leftJoin('zone', 'zone.zone_id', '=', 'legal.state_id');
			$query->where('legal.legal_entity_id', $legalEntityId);
			return $query->first();
		}
		catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}
	public function getLegalEntityWarehouseById($legalEntityId,$whId) {
		try{
			$fieldArr = array(
							'legal.business_legal_name',
							'warehouse.address1',
							'warehouse.address2',
							'warehouse.city',
							'warehouse.pincode',
							'legal.pan_number',
							'warehouse.tin_number',
							'legal.fssai',
							'countries.name as country_name',
							'zone.name as state_name'
						);
			$query = DB::table('legal_entities as legal')->select($fieldArr);
			$query->leftJoin('countries', 'countries.country_id', '=', 'legal.country');
			$query->leftJoin('zone', 'zone.zone_id', '=', 'legal.state_id');
			$query->leftJoin('legalentity_warehouses as warehouse', 'legal.legal_entity_id', '=', 'warehouse.legal_entity_id');
			$query->where('legal.legal_entity_id', $legalEntityId);
			$query->where('warehouse.le_wh_id', $whId);
			return $query->first();
		}
		catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}

	public function getProductByOrderId($orderId, $prdIds=array()) {
            try {
            	
		$fieldArr = array(
                                    'product.*',
                                    DB::raw('GROUP_CONCAT(DISTINCT(`master_lookup`.`master_lookup_name`) ) as starname'),	

//                                    'master_lookup.master_lookup_name as starname',
                                    DB::raw('GROUP_CONCAT(DISTINCT(master_lookup.description) ) as starcolor'),	
//                                    'master_lookup.description as starcolor',
                                    'currency.code',
                                    DB::raw('(product.price / product.qty) as unitPrice'),
                                    'orders.le_wh_id',
                                    'orders.order_code',
                                    'orders.order_status_id',
                                    'orders.shop_name',
                                    'currency.symbol_left as symbol',
                                    DB::raw('(
								    CASE
								      WHEN ISNULL(
								        `product`.`parent_id`
								      ) 
								      THEN `product`.`product_id` 
								      ELSE `product`.`parent_id` 
								    END
								  	) AS `parent_id`'),
								  	DB::raw("getInvoicePrdQty (product.gds_order_id,product.product_id)  AS invoiced_qty")
                                    );
		$query = DB::table('gds_order_products as product')->select($fieldArr);
        $query->join('gds_orders as orders', 'orders.gds_order_id', '=', 'product.gds_order_id');


            $query->leftJoin('gds_order_product_pack as gop', function($join)
        {
            $join->on('product.product_id','=','gop.product_id');
            $join->on('orders.gds_order_id','=','gop.gds_order_id');
        });

        $query->leftjoin('master_lookup', 'master_lookup.value', '=', 'gop.star');
        $query->join('currency', 'orders.currency_id', '=', 'currency.currency_id');
        if(count($prdIds)) {
			$query->whereIn('product.product_id', $prdIds);
		}
        if(is_array($orderId) && count($orderId) > 0) {
        	$query->whereIn('product.gds_order_id', $orderId);
        	$query->groupBy('orders.gds_order_id');
        }        
		else {
			$query->where('product.gds_order_id', $orderId);
		}
		$query->groupBy('product.product_id');
		$query->orderBy('product.pname','asc');
		$query->orderBy('parent_id', 'asc');
		//$query->orderBy('product.pname','asc');

		//echo $query->toSql();die;
		$products = $query->get()->all();
		return $products;
            } catch (Exception $e) {
                Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            }
        }

    public function getProductByOrderIdArray($orderIdArray) {
		try {
				
				$orderIdArray = implode(',',$orderIdArray);

				$products = DB::select(DB::raw("call getPickListByOrderId('$orderIdArray')"));

/*				$query = DB::table('vw_order_details')->whereIn('gds_order_id', $orderIdArray);
				$query->orderBy('parent_id', 'asc');
				$query->orderBy('gds_order_prod_id', 'asc');
				#echo $query->toSql();die;
				$products = $query->get();*/
				return $products;
			} catch (Exception $e) {
				Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
	}
	public function getCompleteProductByOrderId($orderId){

		try {
			$fieldArr = array(
					'gd_product.*',
					'products.*'
					);

	$query = DB::table('gds_order_products as gd_product')->select($fieldArr);
	$query->where('gd_product.gds_order_id', $orderId);
			$query->leftJoin('products', 'products.product_id', '=', 'gd_product.product_id');


	//echo $query->toSql(); die;
	$products = $query->get()->all();
	return $products;
		} catch (Exception $e) {
			Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}

	}
        /*
	 * getOrderCountGroupByStatus() method is used to fetch order count based on status
	 * @param Null
	 * @return Array
	 */

	public function getOrderCountGroupByStatus($status) {
		try {

			//code for getting assigned hubs and warehouses

			$filter = array();

			$roleModel = new Role();
			$Json = json_decode($roleModel->getFilterData(6),1);
			$Json = json_decode($Json['sbu'],1);


			$fieldArr = array('orders.order_status_id', DB::raw('COUNT("orders.gds_order_id") as total'));

			$query = DB::table('gds_orders as orders')->select($fieldArr);
			$query->whereIn("orders.order_status_id", $status);
			//$query->whereNull('orders.order_transit_status');

			$query->groupBy('orders.order_status_id');

            if(!empty(Session::get('business_unitid')) && Session::get('business_unitid')!=0){
                $data=DB::statement("SET SESSION group_concat_max_len = 100000");    
                $bu_id=Session::get('business_unitid');
                $userID = Session('userId');
	            $globalAccess = $this->_roleRepo->checkPermissionByFeatureCode("GLBWH0001",$userID);
	            if($globalAccess){
	            	$data = DB::select(DB::raw("call getAllBuHierarchyByID($bu_id)"));
	            }
	            else{
	            	$data = DB::select(DB::raw("call getBuHierarchyByID($bu_id)"));
	            }
                $le_wh_ids=isset($data[0]->le_wh_ids) ? $data[0]->le_wh_ids : 0;
                $array = explode(',', $le_wh_ids);
                $hubdata = DB::table('dc_hub_mapping')->select(DB::raw('GROUP_CONCAT(hub_id) as hubids'))->whereIn('dc_id',$array)->get()->all();
                $hubdata = isset($hubdata[0]->hubids) ? $hubdata[0]->hubids : 0;
            }
            if(!empty(Session::get('business_unitid')) && Session::get('business_unitid')!=0 && count($array)>0 && $le_wh_ids != ""){
            
                $query->whereRaw("orders.le_wh_id IN ($le_wh_ids)");                
            }else{
        		$query->whereRaw("orders.le_wh_id IN (0)");
            }
            if(!empty(Session::get('business_unitid')) && Session::get('business_unitid')!=0 && !empty($hubdata)){
                $query->whereRaw("orders.hub_id IN ($hubdata)");                
            }

			return $query->get()->all();
		}
		catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}

	/*
	 * getOrderStatus() method is used to get order name with value
	 * @param Null
	 * @return Array
	 */

	public function getOrderStatus($catName = 'Order Status') {
        try {
        		$fieldArr = array('orderStatus.master_lookup_name as name', 'orderStatus.value');
				$query = DB::table('master_lookup as orderStatus')->select($fieldArr);
				$query->join('master_lookup_categories','master_lookup_categories.mas_cat_id','=','orderStatus.mas_cat_id');
				$query->where('master_lookup_categories.mas_cat_name', $catName);
				#echo $query->toSql();die;
				$allOrderStatusArr = $query->get()->all();

				$orderStatusArr = array();
				if(is_array($allOrderStatusArr)) {
					foreach($allOrderStatusArr as $data){
						$orderStatusArr[$data->value] = $data->name;
					}
				}
			
			return $orderStatusArr;
        } 
        catch (Exception $e) {
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

	public function getAllInvoices($orderId, $rowCount=0, $offset=0, $perpage=10, $filter=array()) {
        try {
		$fieldArr = array('invoice.*',DB::raw('SUM(items.qty) as totQty'), 'currency.symbol_left as symbol', 'orders.order_code');

		$query = DB::table('gds_invoice_grid as invoice')->select($fieldArr);
		$query->join('gds_order_invoice as gdsinvoice', 'gdsinvoice.gds_invoice_grid_id', '=', 'invoice.gds_invoice_grid_id');
		$query->join('gds_invoice_items as items', 'gdsinvoice.gds_order_invoice_id', '=', 'items.gds_order_invoice_id');
		$query->join('gds_orders as orders', 'invoice.gds_order_id', '=', 'orders.gds_order_id');
		$query->join('currency', 'orders.currency_id', '=', 'currency.currency_id');
		$query->where('invoice.gds_order_id', $orderId);
		#echo $query->toSql();die;
		if($rowCount) {
                    $query->groupBy('invoice.invoice_code');
			return $query->count(DB::raw('DISTINCT(invoice.`gds_invoice_grid_id`)'));
		}
		else {
                    $query->groupBy('items.gds_order_invoice_id');
			return $query->get()->all();
		}
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
	}
    }
	public function getAllShipments($orderId, $rowCount=0) {
        try {
		$fieldArr = array('gds_ship_grid.*', 'orders.order_date','orders.shop_name', DB::raw('SUM(gsp.qty) as totShippedQty'), 'orders.order_code', 'gds_ship_grid.ship_code');

		$query = DB::table('gds_ship_grid')->select($fieldArr);
		$query->join('gds_orders as orders', 'gds_ship_grid.gds_order_id', '=', 'orders.gds_order_id');
		$query->join('gds_ship_products as gsp' ,'gsp.gds_ship_grid_id', '=', 'gds_ship_grid.gds_ship_grid_id');
		$query->where('gds_ship_grid.gds_order_id', $orderId);
		if(!$rowCount) {
			$query->groupBy('gds_ship_grid.gds_ship_grid_id');
		}
		//echo $query->toSql();die;
		if($rowCount) {
			return $query->count();
		}
		else {
			return $query->get()->all();
		}
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
	}
    }

    public function getShipmentCount($orderId) {
        try {
			$fieldArr = array('grid.gds_order_id');
			$query = DB::table('gds_ship_grid as grid')->select($fieldArr);
			$query->where('grid.gds_order_id', $orderId);
			//echo $query->toSql();die;
			return $query->count();
        }
        catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
    }

	/*
     * getPositionOfOrderStatusByValue() method is used to get position of order status by value
     * @param $statusValue Numeric
     * @return Object
     */

    public function getPositionOfOrderStatusByValue($statusValue) {
        try {
		$lookupFieldArr = array('master_lookup.value', 'master_lookup.sort_order');
		$orderStatus = DB::table('master_lookup')->select($lookupFieldArr)
						->join('master_lookup_categories as mlc', 'master_lookup.mas_cat_id', '=', 'mlc.mas_cat_id')
						->where('mlc.mas_cat_name', 'Order Status')
						->where('master_lookup.value', $statusValue)->first();
		return $orderStatus;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
	}
    }
	public function getOrderStatusValue($lookupCatName, $lookupName) {
        try {
		$lookupFieldArr = array('master_lookup.value', 'master_lookup.sort_order', 'master_lookup.master_lookup_id');
		$orderStatus = DB::table('master_lookup')->select($lookupFieldArr)
						->join('master_lookup_categories as mlc', 'master_lookup.mas_cat_id', '=', 'mlc.mas_cat_id')
						->where('mlc.mas_cat_name', $lookupCatName)
						->where('master_lookup.master_lookup_name', $lookupName)->first();
		return $orderStatus;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
	}
    }
	public function getOrderStatusById($orderId) {
        try {
		$fieldArr = array('gds_order_id', 'order_status_id', 'le_wh_id');
		return DB::table('gds_orders')->select($fieldArr)->where('gds_order_id', $orderId)->first();
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
	}
    }
	public function updateOrderStatusById($orderId, $statusId) {
		try{
			DB::table('gds_orders')->where('gds_order_id', $orderId)->update(array('order_status_id' => $statusId, 'updated_at'=>date('Y-m-d H:i:s'), 'updated_by'=>Session('userId')));
		}
		catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}
	public function updateDeliveryStatusById($orderId, $delivered_by, $delivered_date) {
		try{
			
			$delivered_date = date('Y-m-d H:i:s');

			$Order_Exist = DB::table('gds_order_track')->where('gds_order_id', $orderId)->get()->all();

			$OrderCode = $this->getOrdercodeByOrderid($orderId);

			$OrderCode = $OrderCode[0];


			if($Order_Exist) {
				DB::table('gds_order_track')->where('gds_order_id', $orderId)->update(array('delivered_by' => $delivered_by, 'delivery_date'=>$delivered_date));
			} else {
				DB::table('gds_order_track')->insert(array('delivered_by' => $delivered_by, 'delivery_date'=>$delivered_date,'gds_order_id'=> $orderId, 'gds_order_code'=>$OrderCode,'created_by'=>Session('userId')));
			}

		}
		catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}


	public function updateTrackBulkshipment($orderId, $delivered_by) {

		try{
			

			$Order_Exist = DB::table('gds_order_track')->where('gds_order_id', $orderId)->get()->all();

			$OrderCode = $this->getOrdercodeByOrderid($orderId);

			$OrderCode = $OrderCode[0];

			$InvoiceData = $this->getInvoiceGridOrderId($orderId);

			$invoice_id = isset($InvoiceData->gds_invoice_grid_id) ? $InvoiceData->gds_invoice_grid_id : '';
			$invoice_code = isset($InvoiceData->invoice_code) ? $InvoiceData->invoice_code : '';
			$curDate = date('Y-m-d H:i:s');

			if($Order_Exist) {
				DB::table('gds_order_track')->where('gds_order_id', $orderId)->update(array('delivered_by' => $delivered_by,'invoice_order_no'=>$invoice_id,'invoice_order_code'=>$invoice_code, 'delivery_date'=>$curDate));
			} else {
				DB::table('gds_order_track')->insert(array('delivered_by' => $delivered_by,'gds_order_id'=> $orderId, 'gds_order_code'=>$OrderCode,'created_by'=>Session('userId'),'invoice_order_no'=>$invoice_id,'invoice_order_code'=>$invoice_code,'delivery_date'=>$curDate));
			}

		}
		catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}

	}


    public function updatePicklistOrderTrack($orderId, $pickData, $pick_code = '') {
        try {
        	$query = DB::table('gds_orders')->select('le_wh_id as whareHouseID')->where('gds_order_id', $orderId)->first();
        	$whId = isset($query->whareHouseID) ? $query->whareHouseID: '';
        	$whdetails =$this->_roleRepo->getLEWHDetailsById($whId);
        	$statecode  = isset($whdetails->state_code)?$whdetails->state_code:"TS";
            $Order_Exist = DB::table('gds_order_track')->where('gds_order_id', $orderId)->get()->all();
            $OrderCode = $this->getOrdercodeByOrderid($orderId);
            $OrderCode = $OrderCode[0];
            if ($Order_Exist) {
                DB::table('gds_order_track')->where('gds_order_id', $orderId)->update($pickData);
            } else {
                $pickData['pick_type'] = 1;
                if ($pick_code == '') {
                    $pick_code = $this->getRefCode('PL',$statecode);
                    $pickData['pick_type'] = 0;
                }
                $pickData['gds_order_id'] = $orderId;
                $pickData['created_by'] = Session('userId');
                $pickData['pick_code'] = $pick_code;
                $pickData['gds_order_code'] = $OrderCode;
                DB::table('gds_order_track')->insert($pickData);
            }
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    public function updateProductStatus($orderId, $productId, $statusCode) {
		try{
			$userId = Session('userId');
			$userId = isset($userId) ? $userId : 0;
			$updated_at = (string)date('Y-m-d H:i:s');

			$response = DB::table('gds_order_products')
			->where ('gds_order_id',$orderId)
			->whereIn('product_id',explode(",", $productId))
			->update(array('order_status' => $statusCode, 'updated_by'=>$userId, 
				'updated_at'=>$updated_at));		
				
		}
		catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}
	
	public function updateOrderProductStatus($orderId, $statusCode) {
		try{
			DB::table('gds_order_products')
			->where(array('gds_order_id'=>$orderId))
			->update(array('order_status' => $statusCode, 'updated_by'=>Session('userId'), 'updated_at'=>date('Y-m-d H:i:s')));
		}
		catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}

	public function changeProductStatus($orderId, $statusCode) {
		try{
			DB::table('gds_order_products')
			->where(array('gds_order_id'=>$orderId, 'order_status'=>'17006'))
			->update(array('order_status' => $statusCode, 'updated_by'=>Session('userId'), 'updated_at'=>date('Y-m-d H:i:s')));
		}
		catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}


	public function getCommentTypeByName($name) {
        try {
		$lookupCat = DB::table('master_lookup_categories')->select('mas_cat_id')->where('mas_cat_name', $name)->first();
		return isset($lookupCat->mas_cat_id) ? (int)$lookupCat->mas_cat_id : 0;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
	}
    }
	public function saveComment($data) {

		try{
			$commentArr = array('entity_id'=>$data['entity_id'], 'comment'=>(string)$data['comment'],
			'order_status_id'=>$data['order_status_id'],'comment_type'=>$data['comment_type'], 'commentby'=>$data['commentby'],
			'comment_date'=>(string)$data['comment_date'], 'created_at'=>(string)$data['created_at']);
			return DB::Table('gds_orders_comments')->insertGetId($commentArr);
		}
		catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}

	public function getOrderCommentCountById($orderId) {
		try{
			$commentType = $this->getCommentTypeByName('Order Comment');
			$query = DB::table('gds_orders_comments as comment')->select('comment.*');
			$query->where('entity_id', $orderId);
			//$query->where('comment_type', $commentType);
			return $query->count();
		}
		catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}

	public function getOrderCommentById($entityId=0, $commentType='') {
		try{
			$fieldArr = array('comment.*', DB::raw('CONCAT(users.firstname, " ",users.lastname) AS user_name'),'master_lookup.master_lookup_name');
			$query = DB::table('gds_orders_comments as comment')->select($fieldArr);
			$query->leftJoin('users', 'users.user_id', '=', 'comment.commentby');
			$query->leftjoin('master_lookup', 'comment.order_status_id', '=', 'master_lookup.value');
			if($entityId) {
				$query->where('entity_id', $entityId);
			}
			if($commentType !='') {
				$commentType = $this->getCommentTypeByName($commentType);
				$query->where('comment_type', $commentType);
			}

			$query->groupBy('comment.comment_id');
			$query->orderBy('comment.comment_id', 'DESC');
			#echo $query->toSql();die;
			return $commentArr = $query->get()->all();
		}
		catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}

	public function getItemInvoicedQtyByOrderId($orderId) {

		try{

			$fieldArr = array(DB::raw('SUM(item.qty) AS invoicedQty'), 'item.product_id' );

			$query = DB::table('gds_invoice_grid AS grid')->select($fieldArr);
			$query->join('gds_order_invoice AS invoice', 'grid.gds_invoice_grid_id', '=', 'invoice.gds_invoice_grid_id');
			$query->join('gds_invoice_items AS item', 'item.gds_order_invoice_id', '=', 'invoice.gds_order_invoice_id');
			$query->where('grid.gds_order_id', $orderId);
			$query->groupBy('item.product_id');
			$invoicesArr = $query->get()->all();

			$dataArr = array();
			if(is_array($invoicesArr)) {
				foreach($invoicesArr as $data){
					$dataArr[$data->product_id] = $data->invoicedQty;
				}
			}
			return $dataArr;
		}
		catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}

	public function getItemInvoicesByOrderId($orderId) {

		try{

			$fieldArr = array(DB::raw('COUNT(item.gds_invoice_items_id) AS itemInvoices'), 'item.product_id' );

			$query = DB::table('gds_invoice_grid AS grid')->select($fieldArr);
			$query->join('gds_order_invoice AS invoice', 'grid.gds_invoice_grid_id', '=', 'invoice.gds_invoice_grid_id');
			$query->join('gds_invoice_items AS item', 'item.gds_order_invoice_id', '=', 'invoice.gds_order_invoice_id');
			$query->where('grid.gds_order_id', $orderId);
			$query->groupBy('item.product_id');
			$invoicesArr = $query->get()->all();

			$dataArr = array();
			if(is_array($invoicesArr)) {
				foreach($invoicesArr as $data){
					$dataArr[$data->product_id] = $data->itemInvoices;
				}
			}
			return $dataArr;
		}
		catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}

	public function getItemShippedByOrderId($orderId) {
		try{

			$fieldArr = array(DB::raw('SUM(item.qty) as totItem'), 'item.product_id' );

			$query = DB::table('gds_ship_grid AS grid')->select($fieldArr);
			$query->join('gds_ship_products AS item', 'grid.gds_ship_grid_id', '=', 'item.gds_ship_grid_id');
			$query->where('grid.gds_order_id', $orderId);
			$query->groupBy('item.product_id');
			$shippedArr = $query->get()->all();

			$dataArr = array();
			if(is_array($shippedArr)) {
				foreach($shippedArr as $data){
					$dataArr[$data->product_id] = $data->totItem;
				}
			}
			return $dataArr;
		}
		catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}

	public function getCancelledQtyByOrderId($orderId) {
        try {
		$fieldArr = array('goc.product_id', DB::raw('SUM(goc.qty) as cancelQty'));
		$query = DB::table('gds_cancel_grid as grid')->select($fieldArr);
		$query->leftjoin('gds_order_cancel as goc','grid.cancel_grid_id','=','goc.cancel_grid_id');
		if(is_array($orderId)){
                    $query->whereIn('grid.gds_order_id', $orderId);
                }else{
                    $query->where('grid.gds_order_id', $orderId);
                }
		$query->groupBy('goc.product_id');
		$cancelledArr = $query->get()->all();
		#print_r($query->toSql());die;
		$dataArr = array();

		if(is_array($cancelledArr)) {
			foreach($cancelledArr as $data){
				$dataArr[$data->product_id] = $data->cancelQty;
			}
		}
		return $dataArr;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
	}
    }

    public function getCancelledTotalQtyByOrderId($orderId) {
        try {
			$fieldArr = array(DB::raw('SUM(goc.qty) as cancelQty'));
			$query = DB::table('gds_cancel_grid as grid')->select($fieldArr);
			$query->leftjoin('gds_order_cancel as goc','grid.cancel_grid_id','=','goc.cancel_grid_id');
			$query->where('grid.gds_order_id', $orderId);
			//$query->groupBy('goc.product_id');
			$cancelledArr = $query->first();
			#print_r($query->toSql());die;
			return isset($cancelledArr->cancelQty) ? $cancelledArr->cancelQty : 0;
        }
        catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
    }

    public function getCancelledValueByOrderId($orderId, $cancelGridId=0) {
        try {
			$fieldArr = array(DB::raw('SUM(goc.total_price) as cancelAmt'));
			$query = DB::table('gds_cancel_grid as grid')->select($fieldArr);
			$query->leftjoin('gds_order_cancel as goc','grid.cancel_grid_id','=','goc.cancel_grid_id');
			$query->where('grid.gds_order_id', $orderId);
			if($cancelGridId) {
				$query->where('grid.cancel_grid_id', $cancelGridId);
			}
			
			$cancelledArr = $query->first();
			return isset($cancelledArr->cancelAmt) ? $cancelledArr->cancelAmt : 0;
        }
        catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
    }


	public function getAvailableQty($itemsArr, $cancelledArr) {
        try {
		$availableQtyArr = array();

		if(is_array($itemsArr)) {
			foreach($itemsArr as $data){
				$cancelQty = isset($cancelledArr[$data->product_id]) ? (int)$cancelledArr[$data->product_id] : 0;
				if($data->qty >= $cancelQty) {
					$availableQtyArr[$data->product_id] = (int)($data->qty - $cancelQty);
				}
				else if($data->qty < $cancelQty) {
					$availableQtyArr[$data->product_id] = 0;
				}
				else {
					$availableQtyArr[$data->product_id] = (int)$data->qty;
				}
			}
		}
		return $availableQtyArr;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
	}
    }
	public function cancelGrid($data,$user) {
        try {
		$cancelGridId = DB::table('gds_cancel_grid')
							->insertGetId([
								'gds_order_id' => $data['gds_order_id'],
								'cancel_code' => (isset($data['cancel_code']) ? $data['cancel_code'] : ''),
								'cancel_status_id'=>$data['cancel_status_id'],
								'created_by' => ($user=='system') ? 0 : Session('userId'),
								'created_at' => date('Y-m-d H:i:s'),
								'updated_at' => date('Y-m-d H:i:s')
								]);
		return $cancelGridId;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
	}
    }
	public function cancelGridItem($data,$user) {
        try {
		$id = DB::table('gds_order_cancel')
							->insertGetId([
								'product_id' => $data['product_id'],
								'qty' => $data['qty'] ,
								'created_by'=>($user=='system') ? 0 : Session('userId'),
								'cancel_grid_id' => $data['cancel_grid_id'],
								'cancel_reason_id' => (isset($data['cancel_reason_id']) ? $data['cancel_reason_id']: 0),
								'cancel_status_id'=>$data['cancel_status_id'],
								'unit_price'=>(isset($data['unit_price']) ? $data['unit_price'] : 0),
                                'total_price'=>(isset($data['total_price']) ? $data['total_price'] : 0)
								]);
		return $id;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
	}
    }
	public function getCouriers() {
        try {
		$fieldArr = array('carriers.carrier_id','carriers.name as carrier');
		$query = DB::table('carriers')->select($fieldArr);
		$query->where('carriers.is_active', 1);
		return $query->get()->all();
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
	}
	}

	public function getCourierById($carrierId) {
		$fieldArr = array('carriers.url','carriers.name as carrierName');
		$query = DB::table('carriers')->select($fieldArr);
		$query->where('carrier_id', $carrierId);
		return $query->first();
	}

	public function getShippingServiceName($carrierId) {
        try {
		$services = DB::table('shipping_services')->select('service_name')
							->where('carrier_id', $carrierId)->get()->all();
		return $services;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
	}
    }
	public function getShipmentQtyByOrderId($orderId) {
        try {
		$fieldArr = array('item.product_id', DB::raw('SUM(item.qty) as shipQty'));

		$query = DB::table('gds_ship_products as item')->select($fieldArr);
		$query->join('gds_ship_grid as grid', 'item.gds_ship_grid_id', '=', 'grid.gds_ship_grid_id');
		$query->where('grid.gds_order_id', $orderId);
		$query->groupBy('item.product_id');
		#echo $query->toSql();die;
		$shippedArr = $query->get()->all();
		$dataArr = array();

		if(is_array($shippedArr) && count($shippedArr)) {
			foreach($shippedArr as $data){
				$dataArr[$data->product_id] = (int)$data->shipQty;
			}
		}

		return $dataArr;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
	}
    }
	public function getAvailableShippedQty($itemsArr, $shippedArr) {
        try {
		$availableQtyArr = array();
		if(is_array($itemsArr)) {
			foreach($itemsArr as $data){
				$shippedQty = isset($shippedArr[$data->product_id]) ? (int)$shippedArr[$data->product_id] : 0;
				if($data->qty >= $shippedQty) {
					$availableQtyArr[$data->product_id] = (int)($data->qty - $shippedQty);
				}
				else if($data->qty < $shippedQty) {
					$availableQtyArr[$data->product_id] = 0;
				}
				else {
					$availableQtyArr[$data->product_id] = (int)$data->qty;
				}
			}
		}
		return $availableQtyArr;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
	}
    }


	public function saveShipmentGrid($data) {
        try {
			$query = DB::table('gds_orders')->select('le_wh_id as whareHouseID')->where('gds_order_id', $data['gds_order_id'])->first();
        	$whId = isset($query->whareHouseID) ? $query->whareHouseID: '';
        	$whdetails =$this->_roleRepo->getLEWHDetailsById($whId);
        	$statecode  = isset($whdetails->state_code)?$whdetails->state_code:"TS";        	
        	$ship_code = isset($data['ship_code']) ? $data['ship_code'] : '';
        	if(empty($ship_code)) {
        		$ship_code = $this->getRefCode('SS',$statecode);
        	}
        	$dataExist = DB::table('gds_ship_grid')->select('gds_ship_grid_id')->where('gds_order_id',$data['gds_order_id'])->get()->all();
        	if(count($dataExist)>0){
        		return $dataExist[0]->gds_ship_grid_id;
        	}else{
        		$shipGridId = DB::table('gds_ship_grid')
								->insertGetId(array(
									'gds_order_id' => $data['gds_order_id'],
									'status_id'=>$data['status_id'],
									'ship_code'=>$ship_code,
									'created_at' => date('Y-m-d H:i:s'),
									'updated_at' => date('Y-m-d H:i:s'),
									'created_by'=>Session('userId')
									));
				return $shipGridId;
        	}
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
	}
    }
	public function saveShipmentGridItem($data) {
        try {
		$id = DB::table('gds_ship_products')
							->insertGetId(array(
								'gds_ship_grid_id' => $data['gds_ship_grid_id'],
								'status_id'=>$data['status_id'],
								'product_id' => $data['product_id'],
								'qty' => $data['qty'],
								'comment' => (isset($data['comment']) ? $data['comment'] : ''),
								'created_by'=>Session('userId'),
								'created_at' => date('Y-m-d H:i:s'),
								'updated_at' => date('Y-m-d H:i:s')
						));
		return $id;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
	}
    }
	public function saveShipmentTrack($data) {
        try {
		$id = DB::table('gds_ship_track')
							->insertGetId(array(
								'gds_ship_grid_id' => $data['gds_ship_grid_id'],
								'qty' => $data['qty'],
								'product_id' => $data['product_id'],
								'created_by'=>Session('userId'),
								'created_at' => date('Y-m-d H:i:s'),
								'updated_at' => date('Y-m-d H:i:s')
					));
		return $id;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
	}
    }
	public function getProductByOrderIdProductId($orderId,$product_id) {
        try {
		$fieldArr = array(
                        'product.*', 'gds_orders.firstname', 'gds_orders.lastname','gds_orders.currency_id','gds_orders.shop_name'
                    );

        $query = DB::table('gds_order_products as product')->select($fieldArr);
		$query->join('gds_orders','gds_orders.gds_order_id','=','product.gds_order_id');
		$query->where('product.product_id', $product_id);
		$query->where('product.gds_order_id', $orderId);

		//echo $query->toSql();die;
		$products = $query->first();
		return $products;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
	}
    }
        public function getCurrencyCode($currency_id) {
        try {
            $fieldArr = array(
                    'currency.code'
                );
            $query = DB::table('currency')->select($fieldArr);
            $query->where('currency.currency_id', $currency_id);
            $currency = $query->first();
            $code = isset($currency->code)?$currency->code:'';
            return $code;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
	}
    }
	public function invoiceGrid($insertArr) {
        try {
            $id = DB::table('gds_invoice_grid')
                        ->insertGetId($insertArr);
            return $id;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
	}
    }
	public function gdsOrderInvoice($insertArr) {
        try {
            $id = DB::table('gds_order_invoice')
                        ->insertGetId($insertArr);
            return $id;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
	}
    }
	public function invoiceGridItems($insertArr) {
        try {
            $id = DB::table('gds_invoice_items')
                        ->insertGetId($insertArr);
            return $id;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
	}
    }

    public function insertBulkInvoiceGridItems($insertArr) {
        try {
            $id = DB::table('gds_invoice_items')
                        ->insert($insertArr);
            return $id;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
	}
    }

	public function getAllCancellations($orderId, $rowCount=0, $offset=0, $perpage=10, $filter=array()) {
        try {
		$fieldArr = array('cancel.*', 'orders.order_date', 'orders.order_code', DB::raw('SUM(item.qty) as qty, SUM(item.total_price) as total'));

		$query = DB::table('gds_cancel_grid as cancel')->select($fieldArr);
		$query->join('gds_orders as orders', 'cancel.gds_order_id', '=', 'orders.gds_order_id');
		$query->join('gds_order_cancel as item', 'cancel.cancel_grid_id', '=', 'item.cancel_grid_id');
		$query->where('cancel.gds_order_id', $orderId);
		$query->groupby('cancel.cancel_grid_id');
		#echo $query->toSql();die;
		if($rowCount) {
			return $query->count();
		}
		else {
			return $query->get()->all();
		}
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}

    }

    public function getReturnedCountByOrderId($orderId){

    	$fieldArr = array(DB::raw('COUNT(distinct grid.return_grid_id) as totReturned'));
		$query = DB::table('gds_return_grid as grid')->select($fieldArr);
		$query->where('grid.gds_order_id', $orderId);
		$row = $query->first();
		return isset($row->totReturned) ? (int)$row->totReturned : 0;


    }


    public function getTotalVerificationByOrderId($orderId){


    	$fieldArr = array(DB::raw('COUNT(verification.order_id) as totVerification'));
		$query = DB::table('order_verification_files as verification')->select($fieldArr)->where('order_id', $orderId);
		$row = $query->first();
		return isset($row->totVerification) ? (int)$row->totVerification : 0;


    }

    /**
     * [getApprovedReturnsCnt description]
     * @param  [type]  			[description]
     * @return [type]           [description]
     * Added by pavan
     */
    public function getApprovedReturnsCnt($filters){
    	$query = DB::table('gds_orders as orders')
                    ->select(array(DB::raw('COUNT(DISTINCT orders.gds_order_id) as cnt')))
 					->join('gds_returns as gdsr','orders.gds_order_id','=','gdsr.gds_order_id')
 					->where('orders.order_status_id','17022')
                    ->where('gdsr.approval_status', 1);
            
  			if(isset($filters['118001'])) {
                            $Dcs_Assigned = $filters['118001'];
    			$query->whereRaw("orders.le_wh_id IN ($Dcs_Assigned)");
			}
			if(isset($filters['118002'])) {
                            $Hubs_Assigned = $filters['118002'];
    			$query->whereRaw("orders.hub_id IN ($Hubs_Assigned)");
			}
        $result= $query->first();
		return $result->cnt;
    }

	public function getShipmentProductByOrderId($orderId) {
        try {
		 $subQuery = DB::table('gds_ship_products as gsp')->select('gsp.product_id');
		 $subQuery->join('gds_ship_grid as grid', 'gsp.gds_ship_grid_id', '=', 'grid.gds_ship_grid_id');
		 $productArr = $subQuery->where('grid.gds_order_id', $orderId)->get()->all();

		 $shippedProdArr = array();
		 if(is_array($productArr)) {
			 foreach($productArr as $rowData) {
				 $shippedProdArr[] = $rowData->product_id;
			 }
		 }

		 #print_r($shippedProdArr);

		 $fieldArr = array('gop.*', 'currency.symbol_left as symbol');
		 $query = DB::table('gds_order_products as gop')->select($fieldArr);
		 $query->join('gds_orders as orders', 'orders.gds_order_id', '=', 'gop.gds_order_id');
		 $query->join('currency', 'orders.currency_id', '=', 'currency.currency_id');
		 $query->where('gop.gds_order_id', $orderId);
		 $query->whereNotIn('gop.product_id', $shippedProdArr);
		 $shipmentProductArr = $query->get()->all();
		 return $shipmentProductArr;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
	}
    }
	public function getShipmentProduct($orderId, $shippedProdArr) {
        try {
		 $fieldArr = array('gop.*', 'currency.symbol_left as symbol');
		 $query = DB::table('gds_order_products as gop')->select($fieldArr);
		 $query->join('gds_orders as orders', 'orders.gds_order_id', '=', 'gop.gds_order_id');
		 $query->join('currency', 'orders.currency_id', '=', 'currency.currency_id');
		 $query->where('gop.gds_order_id', $orderId);
		 $query->whereIn('gop.product_id', $shippedProdArr);
		 $shipmentProductArr = $query->get()->all();
		 return $shipmentProductArr;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
	}
    }

	public function getShipmentDetailByShipmentId($orderId) {
        try {
		$query = DB::table('gds_orders_ship_details as ship')->select('ship.*');
		$query->where('ship.gds_order_id', $orderId);
		return $query->first();
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
    }
	public function getTrackingDetailByShipmentId($shipmentId) {
        try {
		$query = DB::table('gds_ship_track_details as track')->select(array('track.*',DB::raw('GetUserName(track.rep_name,2) as Reps_Name')));
		$query->where('track.gds_ship_grid_id', $shipmentId);
		return $query->get()->all();
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
    }
	public function saveTrackingDetail($dataArr, $gdsShipId=0) {
		try{
			if($gdsShipId) {
				$dataArr['updated_by'] = Session('userId');
				$dataArr['updated_at'] = Date('Y-m-d H:i:s');
				DB::table('gds_ship_track_details')->where('gds_ship_id', $gdsShipId)->update($dataArr);
			}
			else {
				DB::table('gds_ship_track_details')->insertGetId($dataArr);
			}
		}
		catch(Exception $e){
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}

        public function getShipmentProductsById($shipmentid) {
        try {
            $fieldArr = array('gds_ship_grid.*','products.product_id','gdsproducts.*','orders.gds_order_id','orders.order_date');
            $query = DB::table('gds_ship_grid')->select($fieldArr);
            $query->join('gds_orders as orders', 'gds_ship_grid.gds_order_id', '=', 'orders.gds_order_id');
            $query->join('gds_ship_products as products', 'gds_ship_grid.gds_ship_grid_id', '=', 'products.gds_ship_grid_id');
            $query->join('gds_order_products as gdsproducts', 'products.product_id', '=', 'gdsproducts.product_id');
            $query->where('gds_ship_grid.gds_ship_grid_id', $shipmentid);
            #echo $query->toSql();die;
            return $query->get()->all();
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
	}
    }
	public function getInvoiceProductsById($invoiceid,$orderId) {
        try {
        	
            $fieldArr = array('gds_invoice_grid.*','invoice.gds_order_invoice_id','invoice.created_at as invoice_date',
                'products.qty as invoicedQty','products.product_id','products.row_total_incl_tax','products.discount_amount','gdsproducts.*',
                'gdsproducts.price as single_price','orders.gds_order_id','orders.order_code','orders.order_date','orders.cust_le_id',
                'orders.created_by as order_created_by','orders.is_self','orders.order_status_id','gdsproducts.qty as orderedQty',
                'gdsproducts.total as sub_total', 'gdsproducts.discount','invoice.discount_type as bill_disc_type',
                'invoice.discount as bill_disc','invoice.discount_amt as bill_disc_amt','remarks',
            	DB::raw('(
								    CASE
								      WHEN ISNULL(
								        `gdsproducts`.`parent_id`
								      ) 
								      THEN `gdsproducts`.`product_id` 
								      ELSE `gdsproducts`.`parent_id` 
								    END
								  	) AS `sort_parent_id`'),
            	DB::raw('(products.qty/products.eaches_in_cfc) as invCfc'),
                DB::raw('getCFCType(products.product_id,products.eaches_in_cfc) as cfcName'),
            	'products.CGST',
            	'products.SGST',
            	'products.IGST',
            	'products.UTGST','products.price as item_price','products.row_total as item_row_total','products.tax_amount as item_tax_amount','products.comments',
            	'gdsproducts.hsn_code',
              DB::raw('(select user_id from users where users.legal_entity_id=orders.cust_le_id and users.is_parent=1 limit 1) as cust_user_id')
            	);
            $query = DB::table('gds_invoice_grid')->select($fieldArr);
            $query->join('gds_orders as orders', 'gds_invoice_grid.gds_order_id', '=', 'orders.gds_order_id');
            $query->join('gds_order_invoice as invoice', 'gds_invoice_grid.gds_invoice_grid_id', '=', 'invoice.gds_invoice_grid_id');
            $query->join('gds_invoice_items as products', 'invoice.gds_order_invoice_id', '=', 'products.gds_order_invoice_id');
            $query->join('gds_order_products as gdsproducts', 'products.product_id', '=', 'gdsproducts.product_id');
            //$query->join('users', 'users.legal_entity_id', '=', 'orders.cust_le_id');
            $query->where('gds_invoice_grid.gds_invoice_grid_id', $invoiceid);
            $query->where('gdsproducts.gds_order_id', $orderId);
            $query->orderBy('gdsproducts.pname','asc');
            //$query->orderBy('invCfc', 'asc');
            #echo $query->toSql();die;
            return $query->get()->all();
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
	}
    }
        public function getInvoiceProductByOrderId($orderId) {
        try {
		
		$fieldArr = array(
                            'product.*','invoice.gds_order_invoice_id','invoice.created_at as invoice_date','product.price as single_price','invproducts.qty as invoicedQty','product.total as sub_total',
                            'grid.gds_invoice_grid_id', 'grid.invoice_code','remarks','invoice.discount_type as bill_disc_type','invoice.discount as bill_disc','invoice.discount_amt as bill_disc_amt',
                            DB::raw('(invproducts.qty/invproducts.eaches_in_cfc) as invCfc'),
                            DB::raw('(
								    CASE
								      WHEN ISNULL(
								        `product`.`parent_id`
								      ) 
								      THEN `product`.`product_id` 
								      ELSE `product`.`parent_id` 
								    END
								  	) AS `sort_parent_id`'),
                            'invproducts.CGST',
                            'invproducts.SGST',
                            'invproducts.IGST',
                            'invproducts.UTGST',
                          );
		$query = DB::table('gds_invoice_items as invproducts')->select($fieldArr);
        $query->join('gds_order_products as product', 'product.product_id', '=', 'invproducts.product_id');
        $query->join('gds_order_invoice as invoice', 'invproducts.gds_order_invoice_id', '=', 'invoice.gds_order_invoice_id');
    	$query->join('gds_invoice_grid as grid', 'grid.gds_invoice_grid_id', '=', 'invoice.gds_invoice_grid_id');        
		$query->where('invproducts.gds_order_id', $orderId);
		$query->where('product.gds_order_id', $orderId);
		$query->orderBy('sort_parent_id', 'asc');
		//$query->orderBy('invCfc', 'asc');
        return $query->get()->all();
		//echo $query->toSql();die;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
	}
    }
    public function getInvoiceProducts($selectedvals) {
        try {
		$fieldArr = array(
                            'orders.gds_order_id'
                            ,'orders.order_code'
                            ,'orders.order_date'
                            ,'orders.shop_name'
                            ,DB::raw('CONCAT(orders.firstname," ",orders.lastname) as name')
                            ,'orders.phone_no'
                            ,'orders.mp_order_id'
                            ,'orders.sub_total as sub_total'
                            ,'orders.discount_amt as discount_total'
                            ,'tax.tax as tax_per'
                            ,'tax.tax_value as tax_total'
                            ,'gdsproducts.tax as tax'
                            ,'orders.total as grand_total'
                            ,'gdsproducts.pname'
                            ,'gdsproducts.mrp'
                            ,'gdsproducts.price as base_price'
                            ,'gdsproducts.unit_price'
                            ,'gdsproducts.sku as article_no'
                            ,'invoice.gds_order_invoice_id'
                            ,'invoice.created_at as invoice_date'
                            ,'invproducts.qty as invoicedQty'
                            ,'gdsproducts.qty as OrderQty'
                            ,'gdsproducts.total'
                            ,'gdsproducts.order_status as order_product_status'
                            ,'lookup.master_lookup_name as order_status'
                            ,'brands.brand_name'
                            ,'legal_entities.business_legal_name as manf_name'
                            ,'categories.cat_name as category_name'
                            ,DB::raw('CONCAT(users.firstname," ",users.lastname) as invoice_created_by')
                            ,DB::raw('(`gdsproducts`.`qty` / (SELECT `product_pack_config`.`no_of_eaches` FROM `product_pack_config` WHERE ((`product_pack_config`.`is_sellable` = 1) AND (`product_pack_config`.`product_id` = `gdsproducts`.`product_id`) AND (`product_pack_config`.`no_of_eaches` IS NOT NULL)) LIMIT 1)) AS `esu_qty`')
                    
                          );
		$query = DB::table('gds_invoice_items as invproducts')->select($fieldArr);
                $query->join('gds_order_invoice as invoice', 'invproducts.gds_order_invoice_id', '=', 'invoice.gds_order_invoice_id');
                $query->join('users', 'users.user_id', '=', 'invproducts.created_by');
                $query->leftJoin('gds_order_products as gdsproducts', function($join)
                    {
                        $join->on('gdsproducts.product_id','=','invproducts.product_id');
                        $join->on('gdsproducts.gds_order_id','=','invproducts.gds_order_id');
                    });
                $query->leftJoin('gds_orders as orders', function($join)
                    {
                        $join->on('invproducts.gds_order_id','=','orders.gds_order_id');
                        $join->on('gdsproducts.gds_order_id','=','orders.gds_order_id');
                    });
                $query->leftJoin('gds_orders_tax as tax', function($join)
                    {
                        $join->on('tax.gds_order_prod_id','=','gdsproducts.gds_order_prod_id');
                    });
                $query->leftJoin('master_lookup as lookup', function($join)
                    {
                        $join->on('lookup.value','=','orders.order_status_id');
                    });                
                $query->leftJoin('products', function($join)
                    {
                        $join->on('products.product_id','=','invproducts.product_id');
                    });
                $query->leftJoin('brands', function($join)
                {
                    $join->on('brands.brand_id','=','products.brand_id');
                });
                $query->leftJoin('legal_entities', function($join)
                {
                    $join->on('products.manufacturer_id','=','legal_entities.legal_entity_id');
                });
                $query->leftJoin('categories', function($join)
                {
                    $join->on('products.category_id','=','categories.category_id');
                });
                $query->groupBy('orders.gds_order_id');
                $query->groupBy('invproducts.product_id');
                if(!empty($selectedvals))
        	{
                    $explodevals = explode(",", $selectedvals);
                    array_unique($explodevals);
                    $query->whereIn("orders.gds_order_id", $explodevals);
        	}
                return $query->get()->all();
		//echo $query->toSql();die;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
	}
    }
	public function getInvoicedProductqty($orderId,$shipmentId,$product_id) {
        try {
            $query = DB::table('gds_invoice_items as items');
            $query->join('gds_order_invoice as invoice','invoice.gds_order_invoice_id','=','items.gds_order_invoice_id');
            $query->join('gds_invoice_grid as grid','grid.gds_invoice_grid_id','=','invoice.gds_invoice_grid_id');
            $query->where('grid.gds_order_id', $orderId);
            $query->where('grid.gds_ship_grid_id', $shipmentId);
            $query->where('items.product_id', $product_id);
            $invqty = $query->sum('qty');
            return $invqty;
	    } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    

    public function getShipmentDetailById($shipmentId) {
		try{

			$fieldArr = array('gds_ship_grid.*', 'gsp.comment', 'gsp.*', DB::raw('SUM(gsp.qty) as shippedQty'));

			$query = DB::table('gds_ship_grid')->select($fieldArr);
			$query->join('gds_ship_products as gsp' ,'gsp.gds_ship_grid_id', '=', 'gds_ship_grid.gds_ship_grid_id');
			$query->join('gds_order_products as product', function($join)
                {
                    $join->on('product.product_id','=','gsp.product_id');
                    $join->on('product.gds_order_id','=','gds_ship_grid.gds_order_id');
                });
			$query->where('gds_ship_grid.gds_ship_grid_id', $shipmentId);
			$query->groupBy('gds_ship_grid.gds_ship_grid_id');
			$query->groupBy('gsp.product_id');
			$query->orderBy('product.pname');

			return $query->get()->all();
		}
		catch(Exception $e){
		}
	}

	public function getCancelledProductById($cancelId) {
		try{

			$fieldArr = array('grid.*', 'item.qty', 'item.unit_price', 'item.total_price', 'item.product_id','item.cancel_reason_id',
				 DB::raw('(
								    CASE
								      WHEN ISNULL(
								        `gop`.`parent_id`
								      ) 
								      THEN `gop`.`product_id` 
								      ELSE `gop`.`parent_id` 
								    END
								  	) AS `parent_id`')
				);

			$query = DB::table('gds_cancel_grid as grid')->select($fieldArr);
			$query->join('gds_order_cancel as item' ,'item.cancel_grid_id', '=', 'grid.cancel_grid_id');
			$query->join('gds_order_products as gop', function($join)
                {
                    $join->on('item.product_id','=','gop.product_id');
                    $join->on('gop.gds_order_id','=','grid.gds_order_id');
                });
            
			$query->where('grid.cancel_grid_id', $cancelId);
			// $query->orderBy('parent_id', 'asc');
		    //$query->orderBy('gop.gds_order_prod_id', 'asc');
			$query->orderBy('gop.pname', 'asc');
			return $query->get()->all();
		}
		catch(Exception $e){
		}
	}

	public function getCancelDetailById($cancelId) {
		try{

			$fieldArr = array('grid.*','gop.sku', 'gop.seller_sku', 'gop.pname', DB::raw('SUM(item.qty) as canceledQty')
			, DB::raw('SUM(gop.qty) as orderedQty'));

			$query = DB::table('gds_cancel_grid as grid')->select($fieldArr);
			$query->join('gds_order_cancel as item' ,'item.cancel_grid_id', '=', 'grid.cancel_grid_id');
			$query->join('gds_order_products as gop', 'gop.product_id', '=', 'item.product_id');
			$query->where('grid.cancel_grid_id', $cancelId);
			$query->groupBy('grid.cancel_grid_id');
			return $query->get()->all();
		}
		catch(Exception $e){
		}
	}

    public function getAllChannels() {
		try{
			$fieldArr = array(
							'mp.mp_name',
							'mp.mp_id'
						);

			$query = DB::table('mp')->select($fieldArr);
			return $query->get()->all();
		}
		catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}

        public function getTaxtypes() {
        try {
            $query = DB::table('master_lookup')
                    ->where('mas_cat_id', 9);//masterlookupid of TAXTYPES is '9'
            $data = $query->get()->all();
            if($data){
                $tax = array();
                foreach ($data as $value) {
                    $tax[$value->master_lookup_name] = $value->value;
                }
                return $tax;
            }
            else{
                return false;
            }
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function getTaxclasses() {
        try {
            $query = DB::table('master_lookup')
                    ->where('mas_cat_id', 10);//masterlookupid of TAXCLASSES is '9'
            $data = $query->get()->all();
            if($data){
                $tax = array();
                foreach ($data as $value) {
                    $tax[$value->master_lookup_name] = $value->value;
                }
                return $tax;
            }
            else{
                return false;
            }
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

	public function getOrderedQtyByOrderId($orderId) {
		$fieldArr = array(DB::raw('SUM(products.qty) as orderedQty'));
		$query = DB::table('gds_orders as orders')->select($fieldArr);
		$query->join('gds_order_products as products', 'orders.gds_order_id', '=', 'products.gds_order_id');
		$query->where('orders.gds_order_id', $orderId);
		$row = $query->first();
		return isset($row->orderedQty) ? (int)$row->orderedQty : 0;
	}

	public function getOrderedQtyByOrderIdAndProductId($orderId, $productId) {
		$fieldArr = array(DB::raw('SUM(products.qty) as orderedQty'));
		$query = DB::table('gds_orders as orders')->select($fieldArr);
		$query->join('gds_order_products as products', 'orders.gds_order_id', '=', 'products.gds_order_id');
		$query->where('orders.gds_order_id', $orderId);
		$query->where('products.product_id', $productId);
		$row = $query->first();
		return isset($row->orderedQty) ? (int)$row->orderedQty : 0;
	}

	public function getShippedQtyByOrderId($orderId) {
		$fieldArr = array('grid.status_id', DB::raw('SUM(products.qty) as totShippedQty'));
		$query = DB::table('gds_ship_grid as grid')->select($fieldArr);
		$query->join('gds_ship_products as products', 'grid.gds_ship_grid_id', '=', 'products.gds_ship_grid_id');
		$query->where('grid.gds_order_id', $orderId);
	//	$query->where('grid.status_id', $status);
		$query->groupBy('grid.status_id');
		#echo $query->toSql();die;
		return $query->get()->all();
		#return isset($row->totShippedQty) ? (int)$row->totShippedQty : 0;
	}

	public function getShippedQtyByOrderIdAndProductId($orderId, $productId) {
		$fieldArr = array(DB::raw('SUM(products.qty) as totShippedQty'));
		$query = DB::table('gds_ship_grid as grid')->select($fieldArr);
		$query->join('gds_ship_products as products', 'grid.gds_ship_grid_id', '=', 'products.gds_ship_grid_id');
		$query->where('grid.gds_order_id', $orderId);
		$query->where('products.product_id', $productId);
		//echo $query->toSql();die;
		$row = $query->first();
		return isset($row->totShippedQty) ? (int)$row->totShippedQty : 0;
	}

	public function getInvoicedQtyByOrderId($orderId) {
		$fieldArr = array('products.invoice_status', DB::raw('SUM(products.qty) as totInvoicedQty'));
		$query = DB::table('gds_invoice_items as products')->select($fieldArr);
		$query->where('products.gds_order_id', $orderId);
		$query->groupBy('products.invoice_status');
		return $query->get()->all();
	}

	public function getTotalInvoicedByOrderId($orderId) {
		$fieldArr = array(DB::raw('COUNT(grid.gds_invoice_grid_id) as totInvoiced'));
		$query = DB::table('gds_invoice_grid as grid')->select($fieldArr);
		$query->where('grid.gds_order_id', $orderId);
		$row = $query->first();
		return isset($row->totInvoiced) ? (int)$row->totInvoiced : 0;
	}

	/**
	 * @prasenjit
	 * [getAllInvoiceGridByOrderId description]
	 * @param  [type] $orderId [description]
	 * @return [type]          [description]
	 */
	public function getAllInvoiceGridByOrderId($orderId) {

		// $fieldArr = array('grid.gds_invoice_grid_id','item.qty','invoice.gds_order_invoice_id','item.product_id');
		// $query = DB::table('gds_invoice_grid as grid');
		// $query->join('gds_order_invoice as invoice', 'invoice.gds_invoice_grid_id', '=', 'invoice.gds_invoice_grid_id');
		// $query->join('gds_invoice_items as item','invoice.gds_order_invoice_id','=','item.gds_order_invoice_id');	
		// //$query->leftJoin('products as product', 'product.product_id', '=', 'item.product_id'); 
  //       $query->select($fieldArr);
		// $query->where('grid.gds_order_id', $orderId);
		//echo $query->tosql(); die;
		$query = "SELECT grid.`gds_invoice_grid_id`, item.`product_id`,
						product.sku,product.product_title,sum(item.qty) as qty
						from  gds_invoice_grid AS grid
						INNER JOIN gds_order_invoice AS invoice ON (grid.gds_invoice_grid_id = invoice.gds_invoice_grid_id)
						INNER JOIN gds_invoice_items AS item ON (invoice.`gds_order_invoice_id` = item.`gds_order_invoice_id`)
						LEFT JOIN products AS product ON ( item.product_id = product.product_id)
						WHERE grid.`gds_order_id`=$orderId
						group by (item.product_id)
						";
		$data = DB::select($query);

		//$data = json_decode(json_encode($data),true);
		return $data;
	}

	public function getInvoiceGridOrderId($orderId) {
		$fieldArr = array('grid.gds_invoice_grid_id','grid.invoice_code');
		$query = DB::table('gds_invoice_grid as grid')->select($fieldArr);
		$query->where('grid.gds_order_id', $orderId);
		return $query->first();
	}

	public function getCanceledQtyByOrderId($orderId) {
		$fieldArr = array(DB::raw('SUM(products.qty) as totCancelledQty'));
		$query = DB::table('gds_cancel_grid as grid')->select($fieldArr);
		$query->join('gds_order_cancel as products', 'grid.cancel_grid_id', '=', 'products.cancel_grid_id');
		$query->where('grid.gds_order_id', $orderId);
		$row = $query->first();
		return isset($row->totCancelledQty) ? (int)$row->totCancelledQty : 0;
	}

	public function getCanceledCountByOrderId($orderId) {
		$fieldArr = array(DB::raw('COUNT(distinct grid.cancel_grid_id) as totCancelled'));
		$query = DB::table('gds_cancel_grid as grid')->select($fieldArr);
		$query->where('grid.gds_order_id', $orderId);
		$row = $query->first();
		return isset($row->totCancelled) ? (int)$row->totCancelled : 0;
	}

    /*
    *  getShipmentProducts() method is used to get shipment products of order*
     * @param $orderId,$shipmentid Numeric
     * @return Object
     */
    public function getShipmentProducts($orderId,$shipmentid) {
        try {
            $fieldArr = array('gds_ship_grid.*','products.qty as shippedQty',
                'products.product_id','gdsproducts.*',
                'orders.gds_order_id','orders.order_date',
                'currency.code',
                'currency.symbol_left as symbol',
                );
            $query = DB::table('gds_ship_grid')->select($fieldArr);
            $query->join('gds_orders as orders', 'gds_ship_grid.gds_order_id', '=', 'orders.gds_order_id');
            $query->join('gds_ship_products as products', 'gds_ship_grid.gds_ship_grid_id', '=', 'products.gds_ship_grid_id');
            $query->join('gds_order_products as gdsproducts', 'products.product_id', '=', 'gdsproducts.product_id');
            $query->join('currency', 'orders.currency_id', '=', 'currency.currency_id');
            $query->where('gds_ship_grid.gds_ship_grid_id', $shipmentid);
            $query->where('gdsproducts.gds_order_id', $orderId);
            #echo $query->toSql();die;
            return $query->get()->all();
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
	}
    }
    /*
    *  getShippedProductqty() method is used to get shipment products Qty of order*
     * @param $orderId,$shipmentId,$product_id Numeric
     * @return num
     */
    public function getShippedProductqty($orderId,$shipmentId,$product_id) {
        try {
            $query = DB::table('gds_ship_grid as grid');
            $query->join('gds_ship_products as products','products.gds_ship_grid_id','=','grid.gds_ship_grid_id');
            $query->where('grid.gds_order_id', $orderId);
            $query->where('grid.gds_ship_grid_id', $shipmentId);
            $query->where('products.product_id', $product_id);
            $invqty = $query->sum('qty');
            return $invqty;
	    } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    public function getShippedQtyWithProductByOrderId($orderId) {
        try {
            $query = DB::table('gds_ship_grid as grid')->select('products.product_id', DB::raw('SUM(products.qty) as shippedQty'));
            $query->join('gds_ship_products as products','products.gds_ship_grid_id','=','grid.gds_ship_grid_id');
            if(is_array($orderId)){
                $query->whereIn('grid.gds_order_id', $orderId);
            }else{
                $query->where('grid.gds_order_id', $orderId);
            }            
            $query->groupBy('products.product_id');
            $rows = $query->get()->all();
            $dataArr = array();
            foreach ($rows as $value) {
            	$dataArr[$value->product_id] = $value->shippedQty;
            }
            return $dataArr;
	    } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    /*
    *  getCancelledProductqty() method is used to get cancelled products Qty of order*
     * @param $orderId,$product_id Numeric
     * @return Object
     */
    public function getCancelledProductqty($orderId,$product_id) {
        try {
            $query = DB::table('gds_cancel_grid as grid');
            $query->join('gds_order_cancel as products','products.cancel_grid_id','=','grid.cancel_grid_id');
            $query->where('grid.gds_order_id', $orderId);
            $query->where('products.product_id', $product_id);
            $cancelQty = $query->sum('qty');
            return $cancelQty;
	    } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    public function getCancelledProductUnitPrice($orderId,$product_id) {
        try {
            $query = DB::table('gds_cancel_grid as grid');
            $query->join('gds_order_cancel as products','products.cancel_grid_id','=','grid.cancel_grid_id');
            $query->where('grid.gds_order_id', $orderId);
            $query->where('products.product_id', $product_id);
            $cancelQty = $query->pluck('unit_price', 'products.product_id')->all();
            return $cancelQty;
	    } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    public function updateShipmetStatus($dataArr, $gdsShipId) {
		try{
			$dataArr['updated_by'] = Session('userId');
			$dataArr['updated_at'] = Date('Y-m-d H:i:s');
			
			DB::table('gds_ship_grid')->where('gds_ship_grid_id', $gdsShipId)->update($dataArr);
			DB::table('gds_ship_products')->where('gds_ship_grid_id', $gdsShipId)->update($dataArr);
		}
		catch(Exception $e){
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}

	public function getProductTaxByOrderId($orderId) {

		try {
			$fields = array('tax.tax_class', 'tax.tax', 'tax.tax_value', 'products.product_id', 'tax_classes.tax_class_type as name','gds_orders.le_wh_id','gds_orders.gds_order_id','gds_orders.order_code', 'products.qty',
				'tax.SGST','tax.CGST','tax.IGST','tax.UTGST'
				);
            $query = DB::table('gds_orders_tax as tax')->select($fields);
            $query->join('gds_order_products as products','products.gds_order_prod_id','=','tax.gds_order_prod_id');
            $query->join('tax_classes','tax_classes.tax_class_id','=','tax.tax_class');
            $query->join('gds_orders','gds_orders.gds_order_id','=','products.gds_order_id');
            $query->where('products.gds_order_id', $orderId);
            $query->where('tax.gds_order_id', $orderId);
            $query->groupBy('tax.gds_orders_tax_id');
            #echo $query->toSql();die;
            $taxArr = $query->get()->all();
            return $taxArr;
	    } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    
	public function getTaxWithProduct($taxArr) {
		$productTaxArr = array();
		if(is_array($taxArr)) {
			foreach ($taxArr as $tax) {
				$productTaxArr[$tax->product_id] = array('name'=>$tax->name, 'tax'=>$tax->tax, 'tax_value'=>$tax->tax_value);
			}
			return $productTaxArr;
		}
	}

	//added by prasenjit on 22 September 2016 on basis on invoice 
	public function getTaxPercentageOnGdsProductId($gds_product_id){
	    $query = DB::table('gds_orders_tax as tax')->select(DB::raw('sum(tax) as tax_percentage , tax_class'), 'tax.CGST', 'tax.IGST', 'tax.SGST', 'tax.UTGST');
	    $query->where('tax.gds_order_prod_id', $gds_product_id);	    
		return $query->first();

	}

	public function getTaxSummary($taxArr) {
		$taxSummary = array();
		$productTaxArr = array();
		$taxBreakupArr = array();
		//echo '<pre>';print_r($taxArr);

		if(is_array($taxArr)) {
			$totAmt = 0;
			foreach ($taxArr as $tax) {
				
				if(isset($productTaxArr[$tax->product_id])) {
					$productTaxArr[$tax->product_id] = $productTaxArr[$tax->product_id] + $tax->tax;
				}
				else {
					$productTaxArr[$tax->product_id] = $tax->tax;
				}
				$taxIndex = strtolower($tax->name).'-'.(str_replace('.', '-', $tax->tax));

				$taxBreakupArr[$taxIndex][] = array('name'=>$tax->name, 'tax'=>$tax->tax, 'tax_value'=>$tax->tax_value, 'qty'=>$tax->qty, 'taxAmtPerUnit'=>($tax->tax_value/$tax->qty));
				
				if(isset($taxSummary[$tax->name]['tax_value'])) {
					$taxSummary[$tax->name]['tax_value'] = $taxSummary[$tax->name]['tax_value']+$tax->tax_value;
				}
				else {
					$taxSummary[$tax->name] = array('name'=>$tax->name, 'tax'=>$tax->tax, 'tax_value'=>$tax->tax_value, 'taxAmtPerUnit'=>($tax->tax_value/$tax->qty));
				}
			}
			$finalTaxArr = array();
			foreach ($taxBreakupArr as $key => $taxArr) {
				$finalTaxArr[$key] = array();
				$totAmt = 0;
				$totQty = 0;
				foreach ($taxArr as $tax) {
					$totAmt = $totAmt + $tax['tax_value'];
					$totQty = $totQty + $tax['qty'];
					$finalTaxArr[$key]['name'] = $tax['name'];
					$finalTaxArr[$key]['tax'] = $tax['tax'];
					$finalTaxArr[$key]['qty'] = $totQty;
				}
				
				$finalTaxArr[$key]['tax_value'] = $totAmt;
			}
			
			
			return array('item'=>$productTaxArr, 'summary'=>$taxSummary, 'breakup'=>$finalTaxArr);
		}
	}

        public function getWarehouseId($productId,$pincode) {
            try{
//            $pincode=DB::table('gds_orders_addresses')
//                     ->where('gds_order_id',$orderid)
//                    ->where('address_type','shipping')
//                    ->pluck('postcode');

            // if($addressinfo[1]->address_type=='shipping'){
            //  $pincode= $addressinfo[1]->pincode;
            // }elseif($addressinfo[0]->address_type=='shipping'){
            //   $pincode= $addressinfo[0]->pincode;
            // }
//            $warehouseid = DB::table('product_tot')
//                    ->SELECT('product_tot.product_id','supplier_le_wh_mapping.le_wh_id')
//                    ->JOIN('supplier_le_wh_mapping', 'product_tot.supplier_id', '=', 'supplier_le_wh_mapping.supplier_id')
//                    ->JOIN('legalentity_warehouses', 'supplier_le_wh_mapping.le_wh_id', '=', 'legalentity_warehouses.le_wh_id')
//                    ->JOIN('wh_serviceables', 'supplier_le_wh_mapping.le_wh_id', '=', 'wh_serviceables.le_wh_id')
//                    ->WHERE('wh_serviceables.pincode',$pincode)
//                    ->where('product_tot.product_id',$productId)
//                    ->groupby('supplier_le_wh_mapping.le_wh_id')
//                    ->first();
            $warehouseid=DB::select(DB::raw("call product_serviceables($pincode,$productId)"));
            if(count($warehouseid)>0){
                $warehouseid= $warehouseid[0];
                return $warehouseid->le_wh_id;
            }else{
               return false;
            }

        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

	/**
     * convertBillingAndShippingAddress() method is used to convert billing and shipping
     * address in array format
     * @param $billingAndShippingArr Array
     * @return Array
     */
    public function convertBillingAndShippingAddress($billingAndShippingArr) {
        $billingAndShipping = array();
        foreach ($billingAndShippingArr as $billingAndShippingData) {
            if ($billingAndShippingData->address_type == 'shipping') {
                $billingAndShipping['shipping'] = $billingAndShippingData;
            }

            if ($billingAndShippingData->address_type == 'billing') {
                $billingAndShipping['billing'] = $billingAndShippingData;
            }
        }
        return $billingAndShipping;
    }
    public function getInvoiceStatus($statusId){
        try{
        $query = DB::table('master_lookup');
        $query->where('value',$statusId);
        $status = $query->pluck('master_lookup_name')->all();
        $status = $status[0];
        return $status;
    }catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }

    }
     public function getCancelGridId($orderId){
        try{
        $query = DB::table('gds_cancel_grid');
        $query->where('gds_order_id',$orderId);
        $grid = $query->get('cancel_grid_id')->all();
        return $grid;
    }catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }

    }

    public function updateOrder($orderId, $fields) {
		try{
			$fields['updated_by'] = Session('userId');
			$fields['updated_at'] = Date('Y-m-d H:i:s');
			DB::table('gds_orders')->whereIn('gds_order_id', $orderId)->update($fields);
		}
		catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}


    public function getOrders($fromDate, $toDate) {
        try {

            $fields = array( 'products.*');
            $query = DB::table('gds_orders as orders')->select($fields);
            $query->join('gds_order_products as products', 'orders.gds_order_id', '=', 'products.gds_order_id');
            $query->skip(0)->take(10);
            $query->where('orders.is_indent' , '0');
            $query->whereBetween('orders.order_date' , array($fromDate, $toDate));
            return $query->get()->all();

    	}
    	catch(Exception $e) {
    		Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
    	}
    }
    public function getProductInvoicedTaxByGridId($gridId) {
            try {                    
                $fields = array('products.product_id','orders.gds_order_id','orders.order_code','products.row_total','products.tax_amount','tax.tax as tax_per','tax_class.tax_class_type','orders.le_wh_id','grid.gds_invoice_grid_id');
                $query = DB::table('gds_invoice_items as products')->select($fields);
                $query->join('gds_order_invoice as invoice','invoice.gds_order_invoice_id','=','products.gds_order_invoice_id');
                $query->join('gds_invoice_grid as grid','grid.gds_invoice_grid_id','=','invoice.gds_invoice_grid_id');
                $query->join('gds_orders as orders','orders.gds_order_id','=','grid.gds_order_id');
                $query->join('gds_order_products as gdsprod', function($join)
                {
                    $join->on('gdsprod.product_id','=','products.product_id');
                    $join->on('gdsprod.gds_order_id','=','orders.gds_order_id');
                });
                $query->leftJoin('gds_orders_tax as tax','tax.gds_order_prod_id','=','gdsprod.gds_order_prod_id');
                $query->leftJoin('tax_classes as tax_class','tax_class.tax_class_id','=','tax.tax_class');
                $query->where('grid.gds_invoice_grid_id', $gridId);
                #echo $query->toSql();die;
                $taxArr = $query->get()->all();           
                return $taxArr;
	    } catch (Exception $e) {
                Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function saveOutputTax($productTax){
        try {
            if(count($productTax)>0){
                $outputTax = array();
                foreach ($productTax as $tax){
                    $tax_amount = ($tax->row_total*$tax->tax_per)/100;
                    $outputTax[] =array('outward_id'=>$tax->gds_order_id,
                                        'product_id'=>$tax->product_id,
                                        'transaction_no'=>$tax->order_code,
                                        'transaction_date'=>date('Y-m-d H:i:s'),
                                        'transaction_type'=>101002,
                                        'tax_type'=>$tax->tax_class_type,
                                        'tax_percent'=>$tax->tax_per,
                                        'tax_amount'=>$tax_amount,
                                        'le_wh_id'=>$tax->le_wh_id,
                                        'created_by'=>Session('userId')
                                );
                }
                DB::table('output_tax')->insert($outputTax);
                return 'success';
            }else{
                return 'No Tax data found';
            }
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    public function getProductStockByOrderId($gridId) {
		
        try {
            $fields = array('products.product_id', 'products.qty','orders.le_wh_id','orders.hub_id','grid.gds_invoice_grid_id', 'grid.invoice_code','go_product.qty as ord_qty','go_product.actual_esp','grid.gds_order_id');
            $query = DB::table('gds_invoice_items as products')->select($fields);
            $query->leftJoin('gds_order_products as go_product', function($join)
            {
               $join->on('go_product.product_id', '=', 'products.product_id');
               $join->on('go_product.gds_order_id', '=', 'products.gds_order_id');
            });
            $query->join('gds_order_invoice as invoice','invoice.gds_order_invoice_id','=','products.gds_order_invoice_id');
            $query->join('gds_invoice_grid as grid','grid.gds_invoice_grid_id','=','invoice.gds_invoice_grid_id');
            $query->join('gds_orders as orders','orders.gds_order_id','=','grid.gds_order_id');            
            $query->where('grid.gds_invoice_grid_id', $gridId);
            #echo $query->toSql();die;
            $taxArr = $query->get()->all();
            return $taxArr;
	    } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
	}
	
    public function saveStockOutward($productStock){
        try {
            if(count($productStock)>0){
                $stockOutward = array();
                foreach ($productStock as $stock){
                    $stockOutward[] =array(
                                        'reference_no'=>$stock->gds_invoice_grid_id,
                                        'reference_type'=>$stock->invoice_code,
                                        'product_id'=>$stock->product_id,
                                        'ordered_qty'=>$stock->qty,
                                        'outward_date'=>date('Y-m-d'),
                                        'le_wh_id'=>$stock->le_wh_id,
                                        'created_by'=>Session('userId')
                                );
                }
                DB::table('stock_outward')->insert($stockOutward);
                return 'success';
            }else{
                return 'No data found';
            }
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    public function getInventory($productId, $leWhId) {
        try {

            $fields = array( 'inventory.*');
            $query = DB::connection('mysql-write')->table('inventory')->select($fields);
            $query->where('inventory.product_id' , $productId);
            $query->where('inventory.le_wh_id' , $leWhId);
            return $query->first();
    	}
    	catch(Exception $e) {
    		Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
    	}

    }

    public function getCustomerById($customerId) {

    	try {

            $fields = array( 'gds_customer.*');
            $query = DB::table('gds_customer')->select($fields);
            $query->where('gds_customer.gds_cust_id' , $customerId);
            return $query->first();
    	}
    	catch(Exception $e) {
    		Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
    	}
    }

    public function getStats($filter=array(), $isSumOfVal=0) {

		try	{
			//print_r($filter);die;
			$fieldArr = array(
	                            'orders.order_status_id',
	                            DB::raw('COUNT(DISTINCT orders.gds_order_id) as tot'),
	                           );

			if($isSumOfVal) {
				$fieldArr = array(
	                            DB::raw('SUM(orders.total) as totAmt'),
	                           );
			}
			// prepare sql

			$query = DB::table('gds_orders as orders')->select($fieldArr);

			//$leModel = new LegalEntity();
			//$leId = Session::get('legal_entity_id');
			//var_dump($leId);
			//$leParentId = $leModel->getLeParentIdByLeId($leId);
			//if($leParentId) {
			//	$query->where('orders.legal_entity_id', $leParentId);
			//}
			//print_r($leParentId);die;

			if(!empty($filter['order_fdate']) && !empty($filter['order_tdate'])) {
				$fdate = date('Y-m-d', strtotime($filter['order_fdate']));
				$tdate = date('Y-m-d', strtotime($filter['order_tdate']));
				$query->whereBetween('orders.order_date', [$fdate.' 00:00:00', $tdate.' 23:59:59']);
			}
			else if(!empty($filter['order_fdate'])) {
				$fdate = date('Y-m-d', strtotime($filter['order_fdate']));
				$query->where('orders.order_date', '>=', $fdate.' 23:59:59');
			}
			else if(!empty($filter['order_tdate'])) {
				$tdate = date('Y-m-d', strtotime($filter['order_tdate']));
				$query->where('orders.order_date', '<=', $tdate.' 00:00:00');
			}

			if(!empty($filter['exp_fdate']) && !empty($filter['exp_tdate'])) {
				$expfdate = date('Y-m-d', strtotime($filter['exp_fdate']));
				$exptdate = date('Y-m-d', strtotime($filter['exp_tdate']));
				$query->whereBetween('orders.order_expiry_date', [$expfdate.' 00:00:00', $exptdate.' 23:59:59']);
			}
			else if(!empty($filter['exp_fdate'])) {
				$expfdate = date('Y-m-d', strtotime($filter['exp_fdate']));
				$query->where('orders.order_expiry_date', '>=', $expfdate.' 23:59:59');
			}
			else if(!empty($filter['exp_tdate'])) {
				$exptdate = date('Y-m-d', strtotime($filter['exp_tdate']));
				$query->where('orders.order_expiry_date', '<=', $exptdate.' 00:00:00');
			}

			if(isset($filter['order_status_id']) && !is_array($filter['order_status_id'])) {
				$query->where('orders.order_status_id', $filter['order_status_id']);
			}

			if(isset($filter['order_status_id']) && is_array($filter['order_status_id'])) {
				$query->whereIn('orders.order_status_id', $filter['order_status_id']);
			}

			if(!empty($filter['order_id'])) {
				$query->where('orders.order_code', $filter['order_id']);
			}

			if(!empty($filter['customer'])) {
				$query->where('orders.shop_name', 'LIKE', '%'.trim($filter['customer']).'%');
			}
			if(!empty($filter['cust_mobile'])) {
				$query->where('orders.phone_no',trim($filter['cust_mobile']));
			}

			if(!empty($filter['payment_method'])) {
				$query->leftJoin('gds_orders_payment as payment', 'payment.gds_order_id', '=', 'orders.gds_order_id');
				$query->where('payment.payment_method_id',$filter['payment_method']);
			}
			if(isset($filter['channel']) && $filter['channel'] > 0) {
				$query->where('orders.mp_id', $filter['channel']);
			}

			//echo $query->toSql();die;

			if($isSumOfVal) {
				$order = $query->first();
				return isset($order->totAmt) ? $order->totAmt : 0;
			}
			else {
				$query->groupBy('orders.order_status_id');
				$ordersArr = $query->get()->all();
				$orderStatusArr = array();
				if(is_array($ordersArr) && count($ordersArr) > 0) {
					foreach ($ordersArr as $order) {
						$orderStatusArr[$order->order_status_id] = $order->tot;
					}
				}
				return $orderStatusArr;
			}

		}
		catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}


  public function savePicklist($data)
  {
  	DB::beginTransaction();
  try
      {
       	$data['pickDate'] = date('Y-m-d H:i:s');

        $orderIds = $data['ids'];
        $generatetrip = isset($data['generatetrip'])?$data['generatetrip']:0;
        $pick_code = '';
        $query = DB::table('gds_orders')->select('le_wh_id as whareHouseID')->where('gds_order_id',$orderIds[0])->first();
        $whId = isset($query->whareHouseID) ? $query->whareHouseID: '';
        $whdetails =$this->_roleRepo->getLEWHDetailsById($whId);
        $statecode  = isset($whdetails->state_code)?$whdetails->state_code:"TS";
        if($generatetrip==1){
            $pick_code = $this->getRefCode('PL',$statecode);
        }
        $typeId = $this->getCommentTypeByName('Order Status');
        $final = array();
        foreach($orderIds as $orderId) {
			$commentArr = array('entity_id'=>$orderId, 'comment_type'=>$typeId,
						'comment'=>'Picklist Generated (Assigned to Picker#'.$data['pickedBy'].')',
						'commentby'=>Session('userId'),
						'order_status_id'=>'17020',
						'created_at'=>date('Y-m-d H:i:s'),
						'comment_date'=>date('Y-m-d H:i:s')
						);

			$orderDetail = $this->getOrderInfo(array($orderId),array('gds_order_id','order_code','order_status_id'));

			if($orderDetail[0]->order_status_id==17001) {
				$orderwh=DB::select(DB::raw("select is_binusing from gds_orders g join legalentity_warehouses l  on l.le_Wh_id = g.le_wh_id where g.gds_order_id = ".$orderId));
				if($orderwh[0]->is_binusing == 1){
					$result = $this->addReserveBins($orderId);
				}else{
					$result = $this->addReserveBinsWithoutBin($orderId);
				}

				if(count($result['error'])>0){
					$final[] = $result;
				}else{
					//print_r($result);
					$this->saveComment($commentArr);
					$checkproductstatus=DB::select(DB::raw("select product_id,order_status from gds_order_products where gds_order_id =$orderId "));
					foreach ($checkproductstatus as $checkstatus) {
						$checkstatus = get_object_vars($checkstatus);
						$order_status = $checkstatus['order_status'];
						$productId = $checkstatus['product_id'];
						
						if($order_status== 17009 || $order_status== 17015){
							$this->updateProductStatus($orderId,$productId,$order_status);
						}else{
							$order_status=17020;
							$this->updateProductStatus($orderId,$productId,$order_status);
						}
					}
					//$this->updateOrderProductStatus($orderId, '17020');
		          	$this->updateOrderStatusById($orderId, '17020');

					$pickData = array('picker_id'=>$data['pickedBy'],'scheduled_piceker_date'=>$data['pickDate']);

					$this->updatePicklistOrderTrack($orderId, $pickData,$pick_code);
					$final[] = $result;	
				}				

			

			} else if(in_array($orderDetail[0]->order_status_id, array('17020'))){


				$this->saveComment($commentArr);
				$checkproductstatus=DB::select(DB::raw("select product_id,order_status from gds_order_products where gds_order_id =$orderId "));
					foreach ($checkproductstatus as $checkstatus) {
						$checkstatus = get_object_vars($checkstatus);
						$order_status = $checkstatus['order_status'];
						$productId = $checkstatus['product_id'];
						if($order_status== 17009 || $order_status== 17015){
							$this->updateProductStatus($orderId,$productId,$order_status);
						}else{
							$order_status=17020;
							$this->updateProductStatus($orderId,$productId,$order_status);
						}
					}
				
				//$this->updateOrderProductStatus($orderId, '17020');
	          	$this->updateOrderStatusById($orderId, '17020');

				$pickData = array('picker_id'=>$data['pickedBy'],'scheduled_piceker_date'=>$data['pickDate']);

				$this->updatePicklistOrderTrack($orderId, $pickData,$pick_code);

				$final[] = array("order_id"=>$orderId,
									"order_code"=>$orderDetail[0]->order_code,
									"message"=>"Picklist generated successfully",
									"error"=>"");	

			} else {
				$final[] = array("order_id"=>$orderId,
									"order_code"=>$orderDetail[0]->order_code,
									"message"=>"To Generate Picklist Order should be in open or picklist",
									"error"=>"");	

			}
			DB::commit();
        }
        return $final;
      }
      catch (\ErrorException $ex) {
      	DB::rollback();
          Log::info($ex->getMessage().' '.$ex->getTraceAsString());
          return Response::json(array('status'=>400, 'message'=>'Failed', 'po_id'=>0));
      }
  }

  /**
   * [getTaxClassesOnProductIdByOrderId description]
   * @param  [type] $product_id [description]
   * @param  [type] $orderId    [description]
   * @return [type]             [description]
   */
public function getTaxClassesOnProductIdByOrderId($product_id,$orderId){

  	$return_array = array();
  	
  	$query = "	select * from gds_orders_tax 
  				left join tax_classes on tax_classes.tax_class_id = gds_orders_tax.tax_class
  				where gds_orders_tax.gds_order_prod_id 
  				in (
						select gds_order_prod_id from gds_order_products 
						where gds_order_id = $orderId and product_id = $product_id
					)
			 ";
	$data = DB::select($query);
	if(count($data) > 0){

		$data = json_decode(json_encode($data),true);
		//var_dump($data);die;
		foreach ($data as $value) {
			$return_array_temp = array();
			$return_array_temp['tax_class'] = $value['tax_class'];
			$return_array_temp['tax_class_type'] = $value['tax_class_type'];
			$return_array_temp['tax'] = $value['tax'];
			$return_array_temp['tax_value'] = $value['tax_value'];
			$return_array[] = $return_array_temp;

		}
	}else{
		$data =  NULL;
	}
	return $return_array;
  }

  /**
   * [getUnitPricesTaxAndWithoutTax description]
   * @param  [type] $orderId   [description]
   * @param  [type] $productId [description]
   * @return [type]            [description]
   */
  public function getUnitPricesTaxAndWithoutTax($orderId,$productId){

  		$tax_details = $this->getTaxPercentageOnGdsOrderIdProductId($orderId,$productId);
  		$product = $this->getProductByOrderIdProductId($orderId, $productId);
  		
  		if(is_null($tax_details) || is_null($tax_details->tax_percentage)){
	        $tax_per_object = $this->getTaxPercentageOnGdsProductId($product->gds_order_prod_id);
	        $tax_per = $tax_per_object->tax_percentage;
	        $tax_class = $tax_per_object->tax_class;

	        $SGST = $tax_per_object->SGST;
  			$CGST = $tax_per_object->CGST;
  			$IGST = $tax_per_object->IGST;
  			$UTGST = $tax_per_object->UTGST;

	    }else{
  			$tax_per = $tax_details->tax_percentage;
  			$tax_class = $tax_details->tax_class;

  			$SGST = $tax_details->SGST;
  			$CGST = $tax_details->CGST;
  			$IGST = $tax_details->IGST;
  			$UTGST = $tax_details->UTGST;
  		}
        //get tax percentage
        $singleUnitPrice = (($product->total / (100+$tax_per)*100) / $product->qty);
        $singleUnitPriceWithtax = (($tax_per/100) * $singleUnitPrice) + $singleUnitPrice;

        return array(	'singleUnitPrice' => $singleUnitPrice,
						'singleUnitPriceWithtax' => $singleUnitPriceWithtax,
						'tax_percentage' => $tax_per,
						'tax_class' => $tax_class,
						'SGST' => $SGST,
						'CGST' => $CGST,
						'IGST' => $IGST,
						'UTGST' => $UTGST,       								
       				);

  }
  /**
   * [getTaxPercentageOnGdsOrderIdProductId 
   * 			Right now we are eliminating the data for need for gds_prod_id]
   * @param  [type] $orderId   [description]
   * @param  [type] $productId [description]
   * @return [type]            [description]
   */
  public function getTaxPercentageOnGdsOrderIdProductId($orderId,$productId){

  	$query = DB::table('gds_orders_tax as tax')->select(DB::raw('sum(tax) as tax_percentage,tax_class'), 'tax.SGST', 'tax.CGST', 'tax.IGST', 'tax.UTGST');
	$query->where('tax.gds_order_id', $orderId)->where('tax.product_id',$productId);	    
	return $query->first();	
  }

  public function getRefCode($prefix,$state_code='TS') {

  		//changed by prasenjit @31st July
        $refNoArr = Utility::getReferenceCode($prefix,$state_code);

        return $refNoArr;
    }


    public function getActiveUsers() {
        $result = DB::table('users')
                    ->select('user_id','firstname','lastname','email_id','mobile_no')
                    ->where(array('is_active'=>1,'legal_entity_id'=>2))
                    ->get()->all();
        return $result;
    }


    public function getUsersByRoleName($roleName) {
        $result = DB::table('users')
                    ->select('users.user_id','users.firstname','users.lastname','users.email_id','users.mobile_no')
 					->join('user_roles', 'users.user_id', '=', 'user_roles.user_id')
            		->join('roles', 'roles.role_id', '=', 'user_roles.role_id')
            		->where(array('users.is_active'=>1))
					->whereIn('roles.name', $roleName);
			// Checking the Global Access to View & Assign all the Users
			$globalAccess = (new RoleRepo())->checkPermissionByFeatureCode("GLB0001");
			if(!$globalAccess){
				// If the logged in User doesnot have access then we
				// restrict him with specific legal entity users
				$legalEntityId = Session::get('legal_entity_id');
				$result = $result->where('users.legal_entity_id',$legalEntityId);
			}
			$result = $result
					->orderBy('users.firstname')
					->get()->all();
        return $result;
    }


    // public function getAllDocAreas() {
    //     $result = DB::table('cat_bin_mapping')
    //                 ->select('bin_mapping_id','bin_location')
    //                 ->where('bin_type_id',109002)
    //                 ->get()->all();
    //     return $result;
    // }


public function getFillRateByOrderID($orderId){
  	try{
  		$getTotQty = DB::select("Select go.gds_order_id, 
		(select sum(gop.qty) from gds_order_products gop where gop.gds_order_id = ?) `order qty`,
		(select sum(gii.qty) from gds_invoice_items gii where gii.gds_order_id = ?) `invoice qty`
		from gds_orders go
		WHERE go.gds_order_id = ?", [$orderId, $orderId, $orderId]);

  		//print_r($getTotQty); die;
		return $getTotQty;
  	}
  	catch(\ErrorException $ex) {
		Log::info($ex->getMessage().' '.$ex->getTraceAsString());
		return Response::json(array('status'=>400, 'message'=>'Failed', 'po_id'=>0));
	}
  	
  }

    public function getAllOrderDetails($filterData) {
        try { 
            $query = DB::table('vw_order_details')->select('*');
            if(count($filterData)>0 && isset($filterData['fdate']) && $filterData['fdate']!='' && $filterData['tdate']!=''){
                $fdate = (isset($filterData['fdate']))?$filterData['fdate']:'';
                $tdate = (isset($filterData['tdate']))?$filterData['tdate']:'';
            }else{
                $fdate = date('Y/m/01'); // hard-coded '01' for first day
                $tdate  = date('Y/m/t');
            }
            if($fdate!='') {
                $query->whereBetween('order_date',array(date('Y-m-d 00:00:00',strtotime($fdate)),date('Y-m-d 23:59:59',strtotime($tdate))));
            }
            $query->orderBy('order_date','desc');
            $query->orderBy('parent_id','asc');
            $query->orderBy('gds_order_prod_id','asc');
            $orders = $query->get()->all();            
            return $orders;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
  
    public function getOrdercodeByOrderid($orderId) {
		try{
		
        	$query = DB::table('gds_orders as orders');
			return $query->where('orders.gds_order_id', (int)$orderId)->pluck('order_code')->all();
		}
		catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}

	}

	public function saveOrderTrackData($orderId, $data) {
		try{
			
			$trackInfo = DB::table('gds_order_track')->where('gds_order_id', $orderId)->first();
			$order_track_id = isset($trackInfo->order_track_id) ? $trackInfo->order_track_id : 0;

			$OrderCode = $this->getOrdercodeByOrderid($orderId);
			$OrderCode = $OrderCode[0];

			$trackData = array();

			$trackData['cfc_cnt'] = isset($data['cfc_cnt']) ? $data['cfc_cnt'] : 0;
			$trackData['bags_cnt'] = isset($data['bags_cnt']) ? $data['bags_cnt'] : 0;
			$trackData['crates_cnt'] = isset($data['crates_cnt']) ? $data['crates_cnt'] : 0;

			$picked_by = isset($data['picked_by']) ? (int)$data['picked_by'] : 0;
			$picked_date = isset($data['picked_date']) ? $data['picked_date'] : null;
			
			if($picked_by) {
				$trackData['picked_by'] = $picked_by;
			}

			if(!empty($picked_date) && $picked_date !='' && $picked_date !='0000-00-00 00:00:00') {
				$trackData['picked_date'] = $picked_date;
			}

			if($order_track_id) {
				DB::table('gds_order_track')->where('order_track_id', $order_track_id)->update($trackData);
			}
			else {
				$trackData['gds_order_id'] = $orderId;
				$trackData['created_by'] = Session('userId');
				$trackData['gds_order_code'] = $OrderCode;
				
				DB::table('gds_order_track')->insert($trackData);
			}
		}
		catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}

     public function getInvoicedDetail($invoiceId) {
        $fieldArr = array('grid.*','orders.*');
        $query = DB::table('gds_invoice_grid as grid')->select($fieldArr);
        $query->where('grid.gds_invoice_grid_id', $invoiceId);
        $query->join('gds_orders as orders', 'grid.gds_order_id', '=', 'orders.gds_order_id');
        //$query->groupBy('grid.gds_invoice_grid_id');
        //echo $query->toSql();
        //die;
        $invqty = $query->get()->all();
        return $invqty;
    }

    /**
     * [getInvoiceIdFromOrderId description]
     * @param  [type] $orderId [description]
     * @return [type]          [description]
     */
    public function getInvoiceIdFromOrderId($orderId){

    	$result = DB::table('gds_order_invoice')
                    ->select(array('gds_order_invoice.gds_order_invoice_id','gds_invoice_grid.invoice_code'))
 					->join('gds_invoice_grid', 'gds_invoice_grid.gds_invoice_grid_id', '=', 'gds_order_invoice.gds_invoice_grid_id')
            		->where(array('gds_invoice_grid.gds_order_id'=>$orderId))
                    ->get()->all();
		return $result;

    }

    public function getInvoiceDataFromInvoiceCode($invoice_code){

    	$result = DB::table('gds_invoice_grid')
                    ->select(array('gds_invoice_grid.gds_order_id','gds_invoice_grid.created_at','gds_invoice_grid.grand_total'))
            		->where(array('gds_invoice_grid.invoice_code'=>$invoice_code))
                    ->first();
		return $result;

    }
	public function getGdsTrackDetail($orderId) {
    	return DB::table('gds_order_track')->where('gds_order_id', $orderId)->first();
    }

    /**
     * [getInvoiceCodefromInvoiceID description]
     * @param  [type] $invoiceId [description]
     * @return [type]            [description]
     * Added by prasenjit for vouchers
     */
    public function getInvoiceCodefromInvoiceID($invoiceId){

    	$result = DB::table('gds_invoice_grid')
                    ->select(array('gds_invoice_grid.invoice_code'))
 					->where(array('gds_invoice_grid.gds_invoice_grid_id'=>$invoiceId))
                    ->get()->all();
		return $result;
    }

    /**
     * [getPendingReturnApprovalCnt description]
     * @param  [type] $invoiceId [description]
     * @return [type]            [description]
     * Added by pavan
     */
    public function getPendingReturnApprovalCnt($orderStatus=array(), $returnStatus=array(),$filters=array()){
    	$query = DB::table('gds_orders as orders')
                    ->select(array(DB::raw('COUNT(DISTINCT orders.gds_order_id) as cnt')))
 					->join('gds_returns as gdsr','orders.gds_order_id','=','gdsr.gds_order_id')
 					->whereIn('orders.order_status_id',$orderStatus)
                    ->whereIn('gdsr.return_status_id',$returnStatus);
            
            if(in_array('57067', $returnStatus)) {
            	$query->whereNull('orders.order_transit_status');
            }

			if(isset($filters['118001'])) {
                            $Dcs_Assigned = $filters['118001'];
    			$query->whereRaw("orders.le_wh_id IN ($Dcs_Assigned)");
			}
			if(isset($filters['118002'])) {
                            $Hubs_Assigned = $filters['118002'];
    			$query->whereRaw("orders.hub_id IN ($Hubs_Assigned)");
			}
        $result= $query->first();
		return $result->cnt;
    }
    public function getOrdersTransitCnt($orderStatus = array(), $transitStatus = array(),$filters=array()) {
        $query = DB::table('gds_orders as orders')
                ->select(array(DB::raw('COUNT(DISTINCT orders.gds_order_id) as cnt')))
                ->join('gds_returns as gdsr', 'orders.gds_order_id', '=', 'gdsr.gds_order_id')
                ->whereIn('orders.order_status_id', $orderStatus)
                ->whereIn('orders.order_transit_status', $transitStatus);
        if (isset($filters['118001'])) {
            $Dcs_Assigned = implode(',', explode(',', $filters['118001']));
            $query->whereRaw("orders.le_wh_id IN ($Dcs_Assigned)");
        }
        if (isset($filters['118002'])) {
            $Hubs_Assigned = implode(',', explode(',', $filters['118002']));
            $query->whereRaw("orders.hub_id IN ($Hubs_Assigned)");
        }
        $result = $query->first();
        return $result->cnt;
    }
	public function updateStockTransfer($orderId,$data,$docket_code,$transfer_status) {
		try{
			
			$date = date('Y-m-d H:i:s');
			$Order_Exist = DB::table('gds_order_track')->where('gds_order_id', $orderId)->get()->all();

			$orderDetail = $this->getOrderDetailById($orderId);

			if($transfer_status=='17027') {

				$stockArray = array('rt_del_ex_id' => $data['stock_delivered_by'],
								 'rt_del_mobile'=>$data['stock_delivered_mobile'],
								 'rt_del_date'=>$date,
								 'rt_vehicle_no'=>$data['stock_vehicle_number'],
								 'rt_vehicle_id'=>$data['stock_vehicle_id'],
								 'rt_driver_name'=>$data['stock_driver_name'],
								 'rt_driver_mobile'=>$data['stock_driver_mobile'],
								// 'order_transit_status'=>$transfer_status,
								 'rt_docket_no'=>$docket_code);
			} else {


				$stockArray = array('st_del_ex_id' => $data['stock_delivered_by'],
								 'st_del_mobile'=>$data['stock_delivered_mobile'],
								 'st_del_date'=>$date,
								 'st_vehicle_no'=>$data['stock_vehicle_number'],
								 'vehicle_id'=>$data['stock_vehicle_id'],
								 'st_driver_name'=>$data['stock_driver_name'],
								 'st_driver_mobile'=>$data['stock_driver_mobile'],
								// 'order_transit_status'=>$transfer_status,
								 'st_docket_no'=>$docket_code);

			}			

			if($Order_Exist) {
				DB::table('gds_order_track')->where('gds_order_id', $orderId)->update($stockArray);
			} else {
				
				$stockArray['gds_order_id'] = $orderId;
				DB::table('gds_order_track')->insert($stockArray);
			}

			$stockArray['gds_order_id'] = $orderId;
			$stockArray['status'] = $transfer_status;

			$stockArray['from_wh_id'] = $orderDetail->hub_id;
			$stockArray['to_wh_id'] = $orderDetail->le_wh_id;
			if($transfer_status=='17024') {
				$stockArray['from_wh_id'] = $orderDetail->le_wh_id;
				$stockArray['to_wh_id'] = $orderDetail->hub_id;
			}

			$stockArray['created_by'] = Session('userId');
			$stockArray['created_at'] = $date;
			DB::table('gds_stock_transfer_history')->insert($stockArray);

			if($transfer_status == '17027') {
				DB::table('gds_orders')->where('gds_order_id',$orderId)->update(array('order_transit_status'=>$transfer_status));
			}			
		}
		catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}

	/**
	 * 17025 - STOCK IN HUB
	 * 17028 - STOCK IN DC
	 */
	public function confirmStockDocket($data) {
		
		try{

			$date = date('Y-m-d H:i:s');
           
 			$dataArr = $data['completeorders'];
            
            if($data['confirm_stock_type']=='dc') {

	 			$updateArray = array('rt_received_by'=>$data['stock_received_by'],
 								'rt_received_at'=>$date);
 			} else {

	 			$updateArray = array('st_received_by'=>$data['stock_received_by'],
 								'st_received_at'=>$date);
 			}

            DB::table('gds_order_track')->whereIn('gds_order_id', $dataArr)->update($updateArray);

            $orderTransitStatus = 17028; // transit status for confirm at DC

            if($data['confirm_stock_type']=='hub') {
            	$orderTransitStatus = 17025;
            }
 			foreach($dataArr as $orderId) {
 				 
 				if($data['confirm_stock_type']=='dc') {

	 				$stockTransitHistory = array('gds_order_id'=>$orderId,
 											'status'=>$orderTransitStatus,
 											'rt_received_by'=>$data['stock_received_by'],
 											'rt_received_at'=>$date,
 											'created_by'=>Session('userId'),
 											'rt_docket_no'=>$data['docket_number'],
 											'created_at'=>$date
 											);

 					DB::table('gds_stock_transfer_history')->insert($stockTransitHistory);

 					DB::table('gds_orders')->where('gds_order_id', $orderId)->update(array('order_transit_status'=>17028));

 				}

 				if($data['confirm_stock_type']=='hub') {


	 				$stockTransitHistory = array('gds_order_id'=>$orderId,
 											'status'=>$orderTransitStatus,
 											'st_received_by'=>$data['stock_received_by'],
 											'st_received_at'=>$date,
 											'created_by'=>Session('userId'),
 											'st_docket_no'=>$data['docket_number'],
 											'created_at'=>$date
 											);

 					DB::table('gds_stock_transfer_history')->insert($stockTransitHistory);

 					DB::table('gds_orders')->where('gds_order_id', $orderId)->update(array('order_status_id'=>17025));
 				}

 			}

			return $dataArr;


		}
		catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}


	public function confirmStockAtHub($docketNo,$stock_received_by) {
		
		try{

			$fields = array('track.gds_order_id');
			$date = date('Y-m-d H:i:s');

            $query = DB::table('gds_order_track as track')->select($fields);
            $query->where('track.st_docket_no', $docketNo);
            $dataArr = $query->get()->all();
            if(is_array($dataArr) && count($dataArr) > 0) {
                foreach($dataArr as $data) {
                    DB::table('gds_orders')->where('gds_order_id', $data->gds_order_id)->update(array('order_status_id'=>17025));
                    DB::table('gds_order_track')->where('gds_order_id', $data->gds_order_id)->update(array('st_received_by'=>$stock_received_by,'st_received_at'=>$date));

	  				DB::table('gds_stock_transfer_history')->insert(array('gds_order_id'=> $data->gds_order_id,
	  					'st_received_by'=>$stock_received_by,
	  					'st_received_at'=>$date,
	  					'created_by'=>Session('userId'),
 						'st_docket_no'=>$docketNo,
 						'created_at'=>$date
	  					));
               }
            }
		}
		catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}


	public function confirmStockAtDc($docketNo,$stock_received_by) {
		
		try{

			$fields = array('track.gds_order_id');
			$date = date('Y-m-d H:i:s');

            $query = DB::table('gds_order_track as track')->select($fields);
            $query->where('track.st_docket_no', $docketNo);
            $dataArr = $query->get()->all();
            if(is_array($dataArr) && count($dataArr) > 0) {
                foreach($dataArr as $data) {
                    DB::table('gds_orders')->where('gds_order_id', $data->gds_order_id)->update(array('order_transit_status'=>17028));
                    DB::table('gds_order_track')->where('gds_order_id', $data->gds_order_id)->update(array('st_received_by'=>$stock_received_by,'st_received_at'=>$date));

	  				DB::table('gds_stock_transfer_history')->insert(array('gds_order_id'=> $data->gds_order_id,
	  					'st_received_by'=>$stock_received_by,
	  					'st_received_at'=>$date,
	  					'created_by'=>Session('userId'),
 						'st_docket_no'=>$docketNo,
 						'created_at'=>$date
	  					));
               }
            }
		}
		catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}

	public function verifyStockInTransitByDocketNo($docketNo) {
		$query = "SELECT order_status_id FROM gds_orders 
					WHERE gds_order_id IN(SELECT gds_order_id FROM gds_order_track WHERE st_docket_no = '$docketNo') AND order_status_id = '17025'";
        
        $orderhData = DB::select( DB::raw($query) );
        if(count($orderhData)) {
        	return false;
        }
        else {
        	return true;
        }        
	} 

	public function getOrdersByStDocketId($docketNo) {
		
		try{

			$fields = array('track.gds_order_id');

            $query = DB::table('gds_order_track as track')->select($fields);
            $query->where('track.st_docket_no', $docketNo);
            $dataArr = $query->get()->all();

            return $dataArr;
		}
		catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}

	public function getOrderInfo($orderIds, $fields) {
        try {
        	$query = DB::table('gds_orders as orders')->select($fields);
			$query->whereIn('orders.gds_order_id', $orderIds);
			return $query->get()->all();
		}
		catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}

    public function getContainerInfoByOrderId($orderId) {
        
        try {
        		
        		$dataArr = DB::select("CALL getContainerMappingByOrderId(".$orderId.")");
        		#echo '<pre>';print_r($dataArr);die;
				$pcmArr = array();

				foreach ($dataArr as $data) {
					$weight = number_format($data->weight, 3);
					$pcmArr[$data->container_type][] = $data->barcode.'('.$weight.' Kg)';
				}

				$cfc = isset($pcmArr[16004]) ? implode($pcmArr[16004], ', ') : '';
				$bags = isset($pcmArr[16006]) ? implode($pcmArr[16006], ', ') : '';
				$crates = isset($pcmArr[16007]) ? implode($pcmArr[16007], ', ') : '';

				return array('16004'=>$cfc, '16006'=>$bags, '16007'=>$crates);        		
            }
            catch(Exception $e) {
                Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            }
    }     
    public function getAssignedHubs() {

    		try {

				$roleModel = new Role();

				$Json = json_decode($roleModel->getFilterData(6),1);
				$Json = json_decode($Json['sbu'],1);

				$Hubs_Assigned = array();

	    		if(isset($Json['118002'])) {
					$Hubs_Assigned = explode(',',$Json['118002']);

				}

				return DB::table('legalentity_warehouses')
					->select(array('le_wh_id as hub_id','lp_wh_name as hub_name'))
					->whereIn('le_wh_id',$Hubs_Assigned)
					->get()->all();

		    }
		      catch (\ErrorException $ex) {
		          Log::info($ex->getMessage().' '.$ex->getTraceAsString());
		      }
			
    }


    public function getAssignedDcs() {

    		try {

				$roleModel = new Role();

				$Json = json_decode($roleModel->getFilterData(6),1);
				$Json = json_decode($Json['sbu'],1);

				$Hubs_Assigned = array();

	    		if(isset($Json['118001'])) {
					$Dcs_Assigned = explode(',',$Json['118001']);

				}

				return DB::table('legalentity_warehouses')
					->select(array('le_wh_id as hub_id','lp_wh_name as hub_name'))
					->whereIn('le_wh_id',$Dcs_Assigned)
					->get()->all();

		    }
		      catch (\ErrorException $ex) {
		          Log::info($ex->getMessage().' '.$ex->getTraceAsString());
		      }
			
    }

    
    public function getUserVehicles($Hub_Dc_Assigned,$Type='Hub',$HubIds=array()) {

    	if($Type=='DC' && empty($Hub_Dc_Assigned)) {
    		
    		$Assoc_DC = DB::table('dc_hub_mapping')
		    			->whereIn('hub_id',$HubIds)
		    			->pluck('dc_id')->all();

    		if(!empty($Assoc_DC)) {
    			$Hub_Dc_Assigned = $Assoc_DC; 
    		}	
    	}

    	$selectArr = array('le.business_legal_name AS vehicleName',
    		'vehicle.reg_no AS vehicleno',
    		'vehicle.vehicle_id',
        'vehicle.vehicle_type'
    		);
    	$options = '';
    	$result = DB::table('legal_entities as le')
	    		->select($selectArr)
	    		->join('vehicle','le.legal_entity_id','=','vehicle.legal_entity_id')
	    		->join('vehicle_attendance as va','va.vehicle_id','=','vehicle.vehicle_id')
	    		->leftJoin('legalentity_warehouses','vehicle.hub_id','=','legalentity_warehouses.le_wh_id')
	    		->where(array('le.legal_entity_type_id'=>1008,
	    						'vehicle.is_active'=>1))
	    		->where('va.attn_date' ,date('Y-m-d'))
	    		->where('va.is_present' , 1)
	    		->whereIn('vehicle.hub_id',$Hub_Dc_Assigned)
	    		->get()->all();
	    if(is_array($result) && count($result)>0) {
	    	foreach($result as $vehicle) {
	    		$options.='<option value="'.$vehicle->vehicle_id.'" vehicle_no="'.$vehicle->vehicleno.'">'.$vehicle->vehicleName.' ('.$vehicle->vehicleno.')</option>';
	    	}

	    }		
	    return $options;
    }


    public function getDriverByVehicleId($Driver_Ids) {

    	$selectArr = array('users.firstname',
    		'users.lastname',
    		'users.mobile_no',
    		'vehicle.vehicle_id'
    		);

    	$options = '<option selected value="">Select Driver</option>';

    	$result = DB::table('legal_entities as le')
	    		->select($selectArr)
	    		->join('vehicle','le.legal_entity_id','=','vehicle.legal_entity_id')
	    		->join('users','le.legal_entity_id','=','users.legal_entity_id')
	    		->where(array('le.legal_entity_type_id'=>1008))
	    		->whereIn('vehicle.vehicle_id',$Driver_Ids)
	    		->get()->all();


	    if(is_array($result) && count($result)>0) {

	    	foreach($result as $driver) {
	    		$options.='<option value="'.$driver->firstname.' '.$driver->lastname.'" mobile_no="'.$driver->mobile_no.'">'.$driver->firstname.' '.$driver->lastname.'</option>';
	    	}

	    }		
	    return $options;
    }

    //Function to insert Bin wise Reserve qty for picking products
    public function addReserveBins($orderId) {
    	DB::beginTransaction();
    	//DB::enablequerylog();
		try{
			$error = array();
			$picklistCheck = DB::table('picking_reserve_bins')
						->select('order_id')
						->where('order_id', $orderId)
						->get()->all();
			if(!empty($picklistCheck)){
				$error[] = array(
						"product"=>'',
						"bins"=>'',
						"reason"=> "Picklist Already Generated"
						);
			}

			$query = DB::table('gds_order_products')
				->select('gds_order_products.product_id','gds_order_products.sku','gds_order_products.qty','gds_order_products.order_status','gds_orders.le_wh_id','gds_orders.order_code', 'i.soh', 'p.kvi', 'fc.free_prd_id', 'fc.mpq', 'fc.qty as free_prd_qty')
				->join ('gds_orders', 'gds_orders.gds_order_id', '=', 'gds_order_products.gds_order_id')
				->join('inventory as i', function($join){
					$join->on('gds_orders.le_wh_id','=','i.le_wh_id')
						->on('gds_order_products.product_id','=','i.product_id');
				})
				->leftJoin('freebee_conf as fc', function($join2){
					$join2->on('fc.main_prd_id','=','gds_order_products.product_id')
						->on(DB::raw('DATE_FORMAT(gds_order_products.created_at, "%Y-%m-%d")'),'>=','fc.start_date')
						->on(DB::raw('DATE_FORMAT(gds_order_products.created_at, "%Y-%m-%d")'),'<=','fc.end_date');
				})
				->join('products as p', 'p.product_id', '=', 'gds_order_products.product_id')
				->where(array('gds_order_products.gds_order_id'=>$orderId))
				->orderBy('fc.free_prd_id', 'desc')
				->get()->all();

			// $sql = DB::getQueryLog();
			// print_r(end($sql)); exit;			

			$query = json_decode(json_encode($query), true);

			$prodArr = array();
			foreach($query as $qry){
				$prodMainArr[$qry['product_id']] = $qry;
			}

			$prodList = array_keys($prodMainArr);

			$binListQty = $this->getBinsQty($prodList);
			

			$cancelPrdArr = $this->getCancelledQtyByOrderId($orderId);

			$prodArr = array(); $reserveBin = array(); $packTypeJson = array();

			$odr_code = ''; $pendingProd = array();
			foreach($prodMainArr as $prod){
				$odr_code = $prod['order_code'];
				if(isset($cancelPrdArr[$prod['product_id']]) && $cancelPrdArr[$prod['product_id']]>0)
					$pending = $prod['qty'] - $cancelPrdArr[$prod['product_id']];
				else
					$pending = $prod['qty'];

				//Check ordered product pack types
				$prodPacks = $this->getOdrProductPackConfig($orderId, $prod['product_id']);

				//Create pack level wise array
				$packLevelArr = array();
				foreach($prodPacks as $packData){
					$packLevelArr[$packData['pack_id']] = array(
							"pack_type"=>$packData['pack_type'],
							"noEaches"=>$packData['no_of_eaches'], 
							"esu"=>$packData['esu'],
							"noPacks"=>$packData['esu']*$packData['esu_qty'],
							"pending"=>$packData['no_of_eaches']*$packData['esu']*$packData['esu_qty']
						);
				}

				if(empty($packLevelArr)){
					$error[] = array(
						"product"=>$prod['sku'],
						"bins"=>'',
						"reason"=> "Product pack definition missing at Order"
						);
				}

				//Get available pick-faces for product

				$binArr = $this->getProductBins($prod['product_id'],$prod['le_wh_id']);

				if(empty($binArr)){
					$error[] = array(
						"product"=>$prod['sku'],
						"bins"=>'',
						"reason"=> "Bin not configured for item"
						);
				} else{
					$binList = array_column($binArr, 'bin_id');


					foreach($binArr as &$bin){
						$qtyCheck = array_search($bin['bin_id'], array_column($binListQty, 'bin_id'));
						
						if($qtyCheck === false){
							$error[] = array(
								"product"=>$prod['sku'],
								"bins"=>$bin['bin'],
								"reason"=> "Product Configuration issue #1"
								);
							$bin['qty'] = 0;
							$bin['reserved_qty'] = 0;
						} else{
							$bin['qty'] = $binListQty[$qtyCheck]['qty'];
							$bin['reserved_qty'] = $binListQty[$qtyCheck]['reserved_qty'];
						}
						
					}
				}
				$bins = array();
				foreach($packLevelArr as $levelId=>&$levelData){
					$binIndex = 0;
					foreach ($binArr as $bin_data) {
						$balance = $bin_data['qty']-$bin_data['reserved_qty'];

						if($levelData['pending']>0 && $levelData['pending'] <= $balance && $balance>0){
							if(isset($reserveBin[$bin_data['bin_id']])){
								$reserveBin[$bin_data['bin_id']]['reserved_qty'] += $levelData['pending'];
							}else{
								$reserveBin[$bin_data['bin_id']] = array(
									"le_wh_id"=>$prod['le_wh_id'],
									"order_id"=>$orderId,
									"product_id"=>$prod['product_id'],
									"reserved_qty"=>$levelData['pending'],
									"bin_code"=>$bin_data['bin'],
									"bin_id"=>$bin_data['bin_id'],
									"sort_order"=>$bin_data['sort_order']
								);
							}
							if(isset($packTypeJson[$bin_data['bin_id']][$levelId]))
								$packTypeJson[$bin_data['bin_id']][$levelId] += $levelData['pending']/$levelData['noEaches'];
							else
								$packTypeJson[$bin_data['bin_id']][$levelId] = $levelData['pending']/$levelData['noEaches'];

							$binArr[$binIndex]['reserved_qty'] += $levelData['pending'];
							$levelData['pending'] -= $levelData['pending'];//10-10=0
						} 
						elseif($levelData['pending']>0 && $levelData['pending'] > $balance && $balance>0){
							$div = (int)($balance/($levelData['noEaches']*$levelData['esu']));
							if($div < 1){
								$binIndex++;
								continue;
							}
							$qty = $div*$levelData['noEaches']*$levelData['esu'];

							if(isset($reserveBin[$bin_data['bin_id']])){
								$reserveBin[$bin_data['bin_id']]['reserved_qty'] += $qty;
							} 
							else{
								$reserveBin[$bin_data['bin_id']] = array(
										"le_wh_id"=>$prod['le_wh_id'],
										"order_id"=>$orderId,
										"product_id"=>$prod['product_id'],
										"reserved_qty"=>$qty,
										"bin_code"=>$bin_data['bin'],
										"bin_id"=>$bin_data['bin_id'],
										"sort_order"=>$bin_data['sort_order']
									);
							}
							if(isset($packTypeJson[$bin_data['bin_id']][$levelId])){
								$packTypeJson[$bin_data['bin_id']][$levelId] += $div*$levelData['esu'];
							}
							else{
								$packTypeJson[$bin_data['bin_id']][$levelId] = $div*$levelData['esu'];
							}
							$binArr[$binIndex]['reserved_qty'] += $qty;
							$levelData['pending'] -= $qty;
						}
						if(!in_array($bin_data['bin'], $bins))
							$bins[] = $bin_data['bin'];
						
						$binIndex++;
					}
					if($levelData['pending'] > 0){
						//Check if freebie is short stop picklist
						if($prod['kvi'] == 69010){
							$error[] = array(
								"product"=>$prod['sku'],
								"bins"=> implode(",",$bins),
								"reason"=> "Insufficient bin inventory for Freebie Item"
							);
						}					
						elseif(!isset($pendingProd[$prod['product_id']]))
							$pendingProd[$prod['product_id']] = $levelData['pending'];
						else
							$pendingProd[$prod['product_id']] += $levelData['pending'];
					}
				}

				foreach($reserveBin as $key=>&$resBinData){
					if(in_array($key, array_keys($packTypeJson))){
						$resBinData['pack_config'] = json_encode($packTypeJson[$key]);
					}
				}
			}

			//print_r($reserveBin);
			//Check if Freebie/Main item needs to cancel
			$cancelArr = array();
			if(!empty($pendingProd) && empty($error)){
				$productsArr = array();
				foreach($pendingProd as $key=>$value){
					//Check for cancelled main prod
					if(!empty($prodMainArr[$key]['free_prd_id'])){
						$freebeeRatio = $prodMainArr[$key]['free_prd_qty']/$prodMainArr[$key]['mpq'];

						$finalProdQty = $prodMainArr[$key]['qty']-$value;
						if($finalProdQty>0)
							$finalFreebieQty = floor($freebeeRatio*$finalProdQty);
						else
							$finalFreebieQty = 0;
						$cancelFree = $prodMainArr[$prodMainArr[$key]['free_prd_id']]['qty']- $finalFreebieQty;



						if($cancelFree>0){
							$cancelArr[$prodMainArr[$key]['free_prd_id']] = $cancelFree;
						}
					} 
					//check for cancelled freebee --Update Oct 16- Need not to cancel Main Item while freebie is short
					/*elseif($prodMainArr[$key]['kvi'] == 69010){
						$mainProd = 0;
						foreach($prodMainArr as $prodId=>$prodValue){
							if($key==$prodValue['free_prd_id'])
								$mainProd = $prodId;
						}
						if($mainProd>0){
							// 1 freebee = x mpq
							$mainRatio = $prodMainArr[$mainProd]['mpq']/$prodMainArr[$mainProd]['free_prd_qty'];
							$cancelMain = floor($mainRatio*$value);

							if($cancelMain>0){
								$cancelArr[$mainProd] = $cancelMain;
							}
						}	
					}*/
					
					$productsArr[$key] = array('product_id'=>$key, 'qty'=>$value,'cancel_reason_id'=>'60012');
				}
			}

			if(!empty($cancelArr) && empty($error)){
				foreach($cancelArr as $key=>&$value){
					//Checking reserved array to decrease qty							
					$bins = array();
					foreach($reserveBin as $resKey=>$resValue){
						if($key == $resValue['product_id'])
							$bins[] = $resKey;
					}

					$prodPacks = $this->getOdrProductPackConfig($orderId, $key);

					if(!empty($bins)){
						foreach($bins as $bin){
							$finalPackConfig = array();
							if($reserveBin[$bin]['reserved_qty']==$value){
								//delete reserved data
								unset($reserveBin[$bin]);
								//add cancel data
								if(isset($productsArr[$key]) && !empty($productsArr[$key]))
									$productsArr[$key]['qty'] += $value;
								else
									$productsArr[$key] = array('product_id'=>$key, 'qty'=>$value,'cancel_reason_id'=>'60012');

							}
							else{
								//reserved packs
								$resPacks = json_decode($reserveBin[$bin]['pack_config'], true);
								foreach($resPacks as $level=>&$lvlValues){
									$eaches = $prodPacks[array_search($level, array_column($prodPacks, 'pack_id'))]['no_of_eaches'];
									$packCount = floor($value/$eaches);
									if($packCount>0){
										if($lvlValues>$packCount){
											$value -= $packCount*$eaches;
											$reserveBin[$bin]['reserved_qty'] -= $packCount*$eaches;
											if(isset($productsArr[$key]) && !empty($productsArr[$key]))
												$productsArr[$key]['qty'] += $packCount*$eaches;
											else
												$productsArr[$key] = array('product_id'=>$key, 'qty'=>$packCount*$eaches,'cancel_reason_id'=>'60012');

											$lvlValues -= $packCount;
										} else{
											$value -= $lvlValues*$eaches;
											$reserveBin[$bin]['reserved_qty'] -= $lvlValues*$eaches;
											if(isset($productsArr[$key]) && !empty($productsArr[$key]))
												$productsArr[$key]['qty'] += $lvlValues*$eaches;
											else
												$productsArr[$key] = array('product_id'=>$key, 'qty'=>$lvlValues*$eaches,'cancel_reason_id'=>'60012');

											$lvlValues = 0;
										}
									}
									if($reserveBin[$bin]['reserved_qty']<=0)
										unset($reserveBin[$bin]);
								}
								foreach($resPacks as $packKey=>$packVal)
									if($packVal==0)
										unset($resPacks[$packKey]);
								if(isset($reserveBin[$bin]) && !empty($reserveBin[$bin])) 
									$reserveBin[$bin]['pack_config'] = json_encode($resPacks);
							}
						}
					}
				}
			}
			$cancelProductsArr = array();
			if(empty($error) && !empty($productsArr)){	
				foreach($productsArr as $pid=>$eachProd){
					$cancelCheck = DB::table('gds_cancel_grid as gcg')
						->join('gds_order_cancel as goc', 'goc.cancel_grid_id','=','gcg.cancel_grid_id')
						->select('goc.cancel_id')
						->where('gcg.gds_order_id', $orderId)
						->where('goc.product_id',$pid)
						->get()->all();

					if(empty($cancelCheck)){
						$cancelProductsArr[$pid] = $eachProd;
					}
				}
			}


			// echo "Product loop completed...\n";
			// print_r($productsArr); //print_r($cancelArr); 
			// print_r($reserveBin); print_r($error); exit; //print_r($packTypeJson); print_r($error);exit;
			if(empty($error)){
				foreach($reserveBin as $binInv){
					//Check for existing entry for same Product/Order/bin combination
					$reserveCheck = DB::table('picking_reserve_bins')
						->select('reserve_id')
						->where('product_id', $binInv['product_id'])
						->where('order_id', $binInv['order_id'])
						->where('bin_id', $binInv['bin_id'])
						->where('le_wh_id', $binInv['le_wh_id'])
						->get()->all();

					if(empty($reserveCheck)){
						DB::table('picking_reserve_bins')->insert($binInv);

						$binQty  = DB::table('bin_inventory')
							->where('product_id', $binInv['product_id'])
							->where('bin_id', $binInv['bin_id'])
							->where('wh_id', $binInv['le_wh_id'])
							->pluck('reserved_qty')->all();
                                                //Log::info('$binQty');
                                                //Log::info($binQty);
                                                $binQtyy = isset($binQty[0])?$binQty[0]:0;
                                                //Log::info('$binQtyyyy');
                                                //Log::info($binQtyy);
						$newVal = array('new_reserved_qty'=> (int)$binInv['reserved_qty']+$binQtyy);
						$oldVal = array('old_reserved_qty'=>(int)$binQtyy);
						$uniquevalues = array('product_id'=>(int)$binInv['product_id'],
											'bin_id'=>(int)$binInv['bin_id'],
											'order_id'=>(int)$orderId);

						UserActivity::userActivityLog("BinInventoryUpdate", $newVal, "Update Bin Reserved Qty" , $oldVal, $uniquevalues);

						DB::table('bin_inventory')
							->where('product_id', $binInv['product_id'])
							->where('bin_id', $binInv['bin_id'])
							->where('wh_id', $binInv['le_wh_id'])
							->increment('reserved_qty', $binInv['reserved_qty']);
					}
				}

				$cancelItemArr = array();
				if(!empty($pendingProd) && !empty($cancelProductsArr)){				
					$cancelItemArr = array('Order_id'=>$orderId, 'product_list'=>$cancelProductsArr);
				}

				DB::commit();
				return array(
					"order_id"=>$orderId,
					"order_code"=>$odr_code,
					"message"=>"Picklist generated successfully",
					"error"=>array(),
					"cancelledArr"=>$cancelItemArr
				);
			}
			else{
				$temp = array(
					"order_id"=>$orderId,
					"order_code"=>$odr_code,
					"message"=>"",
					"error"=>$error
					);
				DB::commit();
				return $temp;
			}
		}
		catch(Exception $e) {
			DB::rollback();
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            //echo $e->getMessage();
            return $e->getMessage();
		}
	}

	public function getOdrProductPackConfig($order_id, $prod_id){
		$packs = DB::table('gds_order_product_pack as gop')
		->select(array('gop.gds_order_id', 'gop.product_id', 'gop.pack_id', DB::raw("getMastLookupValue(gop.pack_id) as pack_type"), 'ppc.no_of_eaches', 'gop.esu', 'gop.esu_qty'))
		->join('product_pack_config as ppc', function($q){
			$q->on('gop.product_id', '=', 'ppc.product_id');
			$q->on('gop.pack_id', '=', 'ppc.level');
		})
		->where('gop.gds_order_id','=', $order_id)
		->where('gop.product_id', '=', $prod_id)
		->orderBy('ppc.no_of_eaches','desc')
		//echo $packs->toSql();die;
		->get()->all();

		return json_decode(json_encode($packs), true);
	}


	public function getVerificationListById($orderId) {
		try{
			$fieldArr = array('verification.*',DB::raw('GetUserName(mapping.verified_by,2) as verified_by'));
			$query = DB::table('order_verification_files as verification')->select($fieldArr)
					->join('picker_container_mapping as mapping','mapping.order_id','=','verification.order_id')
					->where('verification.order_id', $orderId)
					->groupBy('verification.container_name');
			#echo $query->toSql();die;
			return $filesArr = $query->get()->all();
		}
		catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}

	public function getProductBins($product_id,$le_wh_id=0){
	try{
	        // DB::enablequerylog();
	        $bin_config =  DB::Table('warehouse_config as wc')
	            ->join('bin_type_dimensions as bin_dimension','wc.bin_type_dim_id','=','bin_dimension.bin_type_dim_id')
	            ->select(db::raw("distinct wc.wh_location as bin,wc.sort_order,wc.wh_loc_id as bin_id"))
	            ->where('wc.pref_prod_id', $product_id)
	            ->where('wc.le_wh_id', $le_wh_id)
	            ->where('bin_dimension.bin_type', 109003)
	            ->get()->all();
	        /*$sql = DB::getQueryLog();
	        print_r(end($sql)); exit;*/

			if(!empty($bin_config))
			{
				$bin_config= json_decode(json_encode($bin_config),true);
				return $bin_config;
			} else{
				return array();
			}
	       
		} catch (Exception $ex) {
		   Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
		   return array();
		}
	}

	public function getBinsQty($products){
	try{
			$bin_config =  DB::table('bin_inventory')
			->select(array('bin_id','qty','reserved_qty'))
			//->whereIn('bin_id',$bin_id)
			->whereIn('product_id',$products)
			->lockForUpdate()
			->get()->all();

			if(!empty($bin_config))
			{
				$bin_config= json_decode(json_encode($bin_config),true);
				return $bin_config;
			} else{
				return array();
			}

		} catch (Exception $ex) {
			Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
			return array();
		}
	}

	public function getGstin($orderId){
		$query = DB::select(DB::raw("SELECT IFNULL(le.`gstin`, '') as gstin FROM gds_orders go,legal_entities le WHERE go.`cust_le_id` = le.`legal_entity_id` AND go.`gds_order_id` = '$orderId'"));

		if( !empty($query) ){
            return $query[0]->gstin;
        }else{
            return "";
        }
		
	}
	public function getDcHubDataByAcess($dc_hub_ids){
		$dc_hub_query ="select le_wh_id,legal_entity_id,le_wh_code,CONCAT(lp_wh_name,' ','(',le_wh_code,')') as 'name' from legalentity_warehouses where le_wh_id In(".$dc_hub_ids.")";
        $queryResult=DB::select(DB::raw($dc_hub_query));

        return $queryResult;

	}
	public function addReserveBinsWithoutBin($orderId) {
    	DB::beginTransaction();
    	//DB::enablequerylog();
		try{
			$error = array();
			$picklistCheck = DB::table('picking_reserve_bins')
						->select('order_id')
						->where('order_id', $orderId)
						->get()->all();
			if(!empty($picklistCheck)){
				$error[] = array(
						"product"=>'',
						"bins"=>'',
						"reason"=> "Picklist Already Generated"
						);
			}

			$query = DB::table('gds_order_products')
				->select('gds_order_products.product_id','gds_order_products.sku','gds_order_products.qty','gds_order_products.order_status','gds_orders.le_wh_id','gds_orders.order_code', 'i.soh', 'p.kvi', 'fc.free_prd_id', 'fc.mpq', 'fc.qty as free_prd_qty')
				->join ('gds_orders', 'gds_orders.gds_order_id', '=', 'gds_order_products.gds_order_id')
				->join('inventory as i', function($join){
					$join->on('gds_orders.le_wh_id','=','i.le_wh_id')
						->on('gds_order_products.product_id','=','i.product_id');
				})
				->leftJoin('freebee_conf as fc', function($join2){
					$join2->on('fc.main_prd_id','=','gds_order_products.product_id')
						->on(DB::raw('DATE_FORMAT(gds_order_products.created_at, "%Y-%m-%d")'),'>=','fc.start_date')
						->on(DB::raw('DATE_FORMAT(gds_order_products.created_at, "%Y-%m-%d")'),'<=','fc.end_date');
				})
				->join('products as p', 'p.product_id', '=', 'gds_order_products.product_id')
				->where(array('gds_order_products.gds_order_id'=>$orderId))
				->orderBy('fc.free_prd_id', 'desc')
				->get()->all();

					

			$query = json_decode(json_encode($query), true);

			$prodArr = array();
			foreach($query as $qry){
				$prodMainArr[$qry['product_id']] = $qry;
			}

			$prodList = array_keys($prodMainArr);

			$binListQty = $this->getBinsQty($prodList);
			

			$cancelPrdArr = $this->getCancelledQtyByOrderId($orderId);

			$prodArr = array(); $reserveBin = array(); $packTypeJson = array();

			$odr_code = ''; $pendingProd = array();
			foreach($prodMainArr as $prod){
				$odr_code = $prod['order_code'];
				if(isset($cancelPrdArr[$prod['product_id']]) && $cancelPrdArr[$prod['product_id']]>0)
					$pending = $prod['qty'] - $cancelPrdArr[$prod['product_id']];
				else
					$pending = $prod['qty'];

				//Check ordered product pack types
				$prodPacks = $this->getOdrProductPackConfig($orderId, $prod['product_id']);

				//Create pack level wise array
				$packLevelArr = array();
				foreach($prodPacks as $packData){
					$packLevelArr[$packData['pack_id']] = array(
							"pack_type"=>$packData['pack_type'],
							"noEaches"=>$packData['no_of_eaches'], 
							"esu"=>$packData['esu'],
							"noPacks"=>$packData['esu']*$packData['esu_qty'],
							"pending"=>$packData['no_of_eaches']*$packData['esu']*$packData['esu_qty']
						);
				}

				if(empty($packLevelArr)){
					$error[] = array(
						"product"=>$prod['sku'],
						"bins"=>'',
						"reason"=> "Product pack definition missing at Order"
						);
				}

				//Get available pick-faces for product

				//$binArr = $this->getProductBins($prod['product_id'],$prod['le_wh_id']);
				
				$binArr[0]= Array ( 'bin' => 0, 'sort_order' => 0, 'bin_id' => 0 ) ;

				$bins = array();
				foreach($packLevelArr as $levelId=>$levelData){
					$pack_data=array();
					foreach ($binArr as $bin_data) {
						//print_r($levelData);
						$qty=0;						
						$pack_data[$levelId]=$levelData['noPacks'];
						$reserveBin[] = array(
								"le_wh_id"=>$prod['le_wh_id'],
								"order_id"=>$orderId,
								"product_id"=>$prod['product_id'],
								"reserved_qty"=>$qty,
								"bin_code"=>$bin_data['bin'],
								"bin_id"=>$bin_data['bin_id'],
								"sort_order"=>$bin_data['sort_order'],
								"pack_config"=>json_encode($pack_data)
							);
						
					}
					
				}
			
			}

			//print_r($reserveBin);
			//Check if Freebie/Main item needs to cancel
			$cancelArr = array();
			if(!empty($pendingProd) && empty($error)){
				$productsArr = array();
				foreach($pendingProd as $key=>$value){
					//Check for cancelled main prod
					if(!empty($prodMainArr[$key]['free_prd_id'])){
						$freebeeRatio = $prodMainArr[$key]['free_prd_qty']/$prodMainArr[$key]['mpq'];

						$finalProdQty = $prodMainArr[$key]['qty']-$value;
						if($finalProdQty>0)
							$finalFreebieQty = floor($freebeeRatio*$finalProdQty);
						else
							$finalFreebieQty = 0;
						$cancelFree = $prodMainArr[$prodMainArr[$key]['free_prd_id']]['qty']- $finalFreebieQty;



						if($cancelFree>0){
							$cancelArr[$prodMainArr[$key]['free_prd_id']] = $cancelFree;
						}
					} 
					//check for cancelled freebee --Update Oct 16- Need not to cancel Main Item while freebie is short
					$productsArr[$key] = array('product_id'=>$key, 'qty'=>$value,'cancel_reason_id'=>'60012');
				}
			}

			
			$cancelProductsArr = array();
			if(empty($error) && !empty($productsArr)){	
				foreach($productsArr as $pid=>$eachProd){
					$cancelCheck = DB::table('gds_cancel_grid as gcg')
						->join('gds_order_cancel as goc', 'goc.cancel_grid_id','=','gcg.cancel_grid_id')
						->select('goc.cancel_id')
						->where('gcg.gds_order_id', $orderId)
						->where('goc.product_id',$pid)
						->get()->all();

					if(empty($cancelCheck)){
						$cancelProductsArr[$pid] = $eachProd;
					}
				}
			}

			if(empty($error)){
				foreach($reserveBin as $binInv){
					//Check for existing entry for same Product/Order/bin combination
					
						DB::table('picking_reserve_bins')->insert($binInv);

						
//                                                Log::info('$binQty');
                                                //$binQtyy = isset($binQty[0])?$binQty[0]:0;
  //                                              Log::info('$binQtyyyy');
						
				}

				$cancelItemArr = array();
				if(!empty($pendingProd) && !empty($cancelProductsArr)){				
					$cancelItemArr = array('Order_id'=>$orderId, 'product_list'=>$cancelProductsArr);
				}

				DB::commit();
				//echo 'in if';exit;
				return array(
					"order_id"=>$orderId,
					"order_code"=>$odr_code,
					"message"=>"Picklist generated successfully",
					"error"=>array(),
					"cancelledArr"=>$cancelItemArr
				);
			}
			else{
				//echo 'in else';exit;
				$temp = array(
					"order_id"=>$orderId,
					"order_code"=>$odr_code,
					"message"=>"",
					"error"=>$error
					);
				DB::commit();
				return $temp;
			}
		
	}catch(Exception $e) {
			DB::rollback();
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            //echo $e->getMessage();
            return $e->getMessage();
		}
	}


	public function getTokenByUserid($userId){
		$token = DB::table('users')->select('password_token')->where('user_id',$userId)->first();
		$token = isset($token->password_token)?$token->password_token:"";
		return $token;
	}

	public function insertOrderTempData($data){
		$gds_order_id = $data['gds_order_id'];
		DB::table('gds_order_delivery_temp')->where('gds_order_id',$gds_order_id)->delete();
		$dataArry = DB::table('gds_order_delivery_temp')->insert($data);
		return $dataArry;
	}

	public function getOrderTempData($gds_order_id){
		$dataArry = DB::table('gds_order_delivery_temp')->where('gds_order_id',$gds_order_id)->first();
		return $dataArry;
	}


	public function isFreebie($product_id = null) {
        $result = false;
        if ($product_id != null or $product_id != '') {
            $result = DB::table('freebee_conf')
                    ->where('free_prd_id', $product_id)
                    ->count();
            if ($result > 0)
                $result = true;
        }
        return $result;
    }
    
    public function getOrderUserId($orderId){
    	$legal_entity_id = $this->getOrderInfo(array($orderId),array('cust_le_id'));
	    $legal_entity_id = $legal_entity_id[0]->cust_le_id;
	    $user_id = DB::table('users')->select("user_id")->where("legal_entity_id",$legal_entity_id)->where("is_parent",1)->first();
	    $user_id = isset($user_id->user_id)?$user_id->user_id:0;
    	return $user_id;
    }

  /**
   * [getUnitPricesTaxAndWithoutTax description]
   * @param  [type] $orderId   [description]
   * @param  [type] $productId [description]
   * @return [type]            [description]
   */
  public function getUnitPricesTaxAndWithoutTaxForLp($orderId,$productId){

  		$tax_details = $this->getTaxPercentageOnGdsOrderIdProductId($orderId,$productId);
  		$product = $this->getProductByOrderIdProductIdForLp($orderId, $productId);

  		if(is_null($tax_details) || is_null($tax_details->tax_percentage)){
	        $tax_per_object = $this->getTaxPercentageOnGdsProductId($product->gds_order_prod_id);
	        $tax_per = $tax_per_object->tax_percentage;
	        $tax_class = $tax_per_object->tax_class;

	        $SGST = $tax_per_object->SGST;
  			$CGST = $tax_per_object->CGST;
  			$IGST = $tax_per_object->IGST;
  			$UTGST = $tax_per_object->UTGST;

	    }else{
  			$tax_per = $tax_details->tax_percentage;
  			$tax_class = $tax_details->tax_class;

  			$SGST = $tax_details->SGST;
  			$CGST = $tax_details->CGST;
  			$IGST = $tax_details->IGST;
  			$UTGST = $tax_details->UTGST;
  		}
        //get tax percentage
        $singleUnitPrice = (($product->total / (100+$tax_per)*100) / $product->qty);
        $singleUnitPriceWithtax = (($tax_per/100) * $singleUnitPrice) + $singleUnitPrice;
        $singleUnitPriceBeforeTax = $singleUnitPrice;
        if($product->discount_before_tax == 1){
			$singleUnitPriceBeforeTax = $product->actual_cost;
		}


        return array(	'singleUnitPrice' => $singleUnitPrice,
						'singleUnitPriceWithtax' => $singleUnitPriceWithtax,
						'singleUnitPriceBeforeTax' => $singleUnitPriceBeforeTax,
						'tax_percentage' => $tax_per,
						'tax_class' => $tax_class,
						'SGST' => $SGST,
						'CGST' => $CGST,
						'IGST' => $IGST,
						'UTGST' => $UTGST,       								
       				);

  }
  public function getProductByOrderIdProductIdForLp($orderId,$product_id) {
	try {
			$resultset = DB::table('gds_invoice_items')->where('gds_order_id',$orderId)->where('product_id',$product_id)->where('qty','>',0)->count();
			if($resultset > 0){
				$fieldArr = array(
	                    'product.product_id as gds_order_prod_id', 'product.row_total_incl_tax as total','product.row_total as cost',DB::raw('go_product.cost/go_product.qty as actual_cost'),'product.qty', 'gds_orders.firstname', 'gds_orders.lastname','gds_orders.currency_id','gds_orders.shop_name','gds_orders.discount_before_tax'
	                );

	        $query = DB::table('gds_invoice_items as product')->select($fieldArr);
	        $query->leftJoin('gds_order_products as go_product', function($join)
            {
               $join->on('go_product.product_id', '=', 'product.product_id');
               $join->on('go_product.gds_order_id', '=', 'product.gds_order_id');
            });
			$query->join('gds_orders','gds_orders.gds_order_id','=','product.gds_order_id');
			$query->where('product.product_id', $product_id);
			$query->where('product.gds_order_id', $orderId);

			//echo $query->toSql();die;
			$products = $query->first();
			}else{
			$products = $this->getProductByOrderIdProductIdFromActualEsp($orderId,$product_id);
			}
		return $products;
		
	    } catch (Exception $e) {
	        Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
	}
  }

    public function getProductByOrderIdProductIdFromActualEsp($orderId,$product_id) {
        try {
		$fieldArr = array(
                        'product.gds_order_prod_id',DB::raw('product.actual_esp*product.qty as total'),DB::raw('product.actual_esp*product.qty as cost'),DB::raw('product.cost/product.qty as actual_cost'),'product.qty', 'gds_orders.firstname', 'gds_orders.lastname','gds_orders.currency_id','gds_orders.shop_name','gds_orders.discount_before_tax'
                    );

        $query = DB::table('gds_order_products as product')->select($fieldArr);
		$query->join('gds_orders','gds_orders.gds_order_id','=','product.gds_order_id');
		$query->where('product.product_id', $product_id);
		$query->where('product.gds_order_id', $orderId);

		//echo $query->toSql();die;
		$products = $query->first();
		return $products;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}

	public function getEcashByOrderId($order_id,$transaction_type=143002){
    	$ecash = DB::table("ecash_transaction_history")
    				->select("cash_back_amount")
    				->where("order_id",$order_id)
    				->where("transaction_type",$transaction_type)
    				->first();
    	return isset($ecash->cash_back_amount) ? $ecash->cash_back_amount : 0;
    }
    public function getProductList($term,$wh_id,$customer_type)
    {
    	//,'pf.pack_level','pf.pack_size','pf.level_name'

    	if($customer_type != 3015){
    		$customer_type = 3014;
    	}
    	$fieldArr = array(DB::raw("CONCAT(p.product_title,' (',p.sku,')') AS label"),'p.product_id','p.mrp','p.sku');
    	$data = DB::table('products as p')
    			->select($fieldArr)
    			->join('product_slab_flat as pf','pf.product_id','=','p.product_id')
    			->join('inventory as i','i.product_id','=','p.product_id')
    			->where('pf.wh_id',$wh_id)
    			->where('i.le_wh_id',$wh_id)
    			->where(DB::raw('i.soh-(i.order_qty+i.reserved_qty)'),'>',0)
    			->where(function($query) use($term){
    				$query->where('p.product_title','like','%'.$term.'%')
    					  ->orWhere('p.sku','like','%'.$term.'%');
    			})
    			->where('pf.customer_type',$customer_type)
    			->whereNotIn('p.kvi',[69010])
    			->groupby('p.product_id')
    			->get()->all();
    	//log::info($data);

    	return $data;
    }
    public function getRetailerInfo($phone)
    {
    	$data = DB::table('retailer_flat')->select('legal_entity_type_id')->where('mobile_no',$phone)->get()->all();
    	$data = json_decode(json_encode($data),1);
    	return $data[0];
    }
    public function getPacksData($id,$wh_id,$customer_type)
    {
    	if($customer_type != 3015){
    		$customer_type = 3014;
    	}
    	$data = DB::table('product_slab_flat')
    			->select('unit_price','pack_size','level_name','pack_level','product_id','star','esu')
    			->where('wh_id',$wh_id)
    			->where('customer_type',$customer_type)
    			->where('product_id',$id)
    			->get()->all();
    	return json_decode(json_encode($data),1);

    }
    public function addProductToOrder($product_info,$orderId,$le_wh_id,$product_id,$title,$sku,$mrp,$prod_qty,$customer_type){
    	$productArr = array();
    	$available = DB::table('inventory')
    		->select(DB::raw('soh-(order_qty+reserved_qty) as available'))
    		->where('product_id',$product_id)
    		->where('le_wh_id',$le_wh_id)
    		->get()->all();
    	$available = json_decode(json_encode($available),1);
    	$cu_state = DB::table('gds_orders_addresses')
    				->select('state_id')
    				->where('gds_order_id',$orderId)
    				->get()->all();
    	$cu_state = json_decode(json_encode($cu_state),1);
    	$wh_state = DB::table('legalentity_warehouses')
    				->select('state')
    				->where('le_wh_id',$le_wh_id)
    				->get()->all();
        $wh_state = json_decode(json_encode($wh_state),1);

    	$buyer_state_id= $cu_state[0]['state_id'];
    	$seller_state_id = $wh_state[0]['state'];
    	$taxinfo = $this->getTaxForProduct($product_id,$seller_state_id,$buyer_state_id);
    	$elp_date = date('Y-m-d');
		$elpQuery = "select getGrossElpByDtLeWhId($product_id,'$elp_date',$le_wh_id) as elp";
        $elpTemp = DB::select($elpQuery);
        $elp=0;$esp=0;
        if(count($elpTemp) > 0){
            $elp = $elpTemp[0]->elp;
            $productArr[0]['elp'] = $elp;
        }else{
    		return Response::json(array('status'=>false, 'message'=>'Elp not found'));
        }

        $espQuery = "select getProductEsp_wh($product_id,$le_wh_id) as esp";
        $espQuery = DB::select($espQuery);
        if(count($espQuery) > 0){
            $esp = $espQuery[0]->esp;
            $productArr[0]['esp'] = $esp;
        }else{
    		return Response::json(array('status'=>false, 'message'=>'Esp not found'));
        }

    	if(is_array($taxinfo) && count($taxinfo)>0){
    		$productArr[0]['taxinfo'] = $taxinfo;
            $productArr[0]['product_info'] = $product_info;

	    	if(count($available)>0 && $available[0]['available'] >= $prod_qty )
	    	{
	    		$productArr[0]['product_id'] = $product_id;
	    		$productArr[0]['title'] = $title;
	    		$productArr[0]['sku'] = $sku;
	    		$productArr[0]['mrp'] = $mrp;
	    		$productArr[0]['is_freebee'] =0;
	    		$productArr[0]['mpqty'] = $prod_qty;

	    		$freebee = $this->getFreebeeForProduct($product_id,$prod_qty,$le_wh_id,$buyer_state_id,$seller_state_id);

	    		log::info('freebee');
	    		log::info($freebee);

	    		if(count($freebee)>0){
	    			if($freebee['issue_with_product'] == true){
			        	return Response::json(array('status'=>false, 'message'=>$freebee['message']));
	    			}else{
	    				$productArr[count($productArr)]= $freebee;
	    				$productArr[0]['fbqty'] = $freebee['offer_qty'];
	    				$productArr[0]['mpqty'] = $freebee['main_prd_qty'];
	    			}
	    		}
		    	for($p_index =0; $p_index < count($productArr); $p_index++){
		    		log::info('productarray');
		    		log::info($productArr[$p_index]);
		    		$productData = DB::table('gds_order_products')
				        ->where('gds_order_id',$orderId)
				        ->where('product_id',$productArr[$p_index]['product_id'])
				        ->get()->all();
				        log::info('productData');
				    $productData = json_decode(json_encode($productData),1);

				    log::info($productData);
					if(count($productData) >0){
						$productData =$productData[0];
						$this->delteProductFromOpenOrder($productArr[$p_index]['product_id'],$orderId,$productData,$le_wh_id,$customer_type);
						$freebeedel = DB::table('gds_order_products')
				        ->where('gds_order_id',$orderId)
				        ->where('parent_id',$productArr[$p_index]['product_id'])
				        ->get()->all();
				        $freebeedel = json_decode(json_encode($freebeedel),1);
				        if(count($freebeedel) >0){
				        	$freebeedel =$freebeedel[0];
							$this->delteProductFromOpenOrder($freebeedel['product_id'],$orderId,$freebeedel,$le_wh_id,$customer_type);
				        }				
					}
					$packs_data = array();
					$row_tax =0;
					$row_qty =0;
					$row_cost =0;
					$taxData = $productArr[$p_index]['taxinfo'];
					$product_info = $productArr[$p_index]['product_info'];
					log::info('productinfo');
					log::info($product_info);
					$tax_percent = $taxData[0]['Tax Percentage'];
					$taxclass = $taxData[0]['Tax Class ID'];
					$hsn_code =$taxData[0]['HSN_Code'];
					$total_units = 0;
					$singleunitprice=0;
					$cust_type = 3014;
					if($customer_type ==  3015){
						$cust_type = 3015;
					}
					if(!$productArr[$p_index]['is_freebee']){
						$prod_unitprice = DB::table('product_slab_flat')
								->select('unit_price')
								->where('product_id',$productArr[$p_index]['product_id'])
								->where('wh_id',$le_wh_id)
								->where(DB::raw('pack_size*IFNULL(esu,1)'),'<=',$productArr[$p_index]['mpqty'])
								->where('customer_type',$cust_type)
								->orderBy(DB::raw('pack_size*IFNULL(esu,1)'),'desc')
								->skip(0)
								->take(1)
								->get()->all();
						$prod_unitprice = json_decode(json_encode($prod_unitprice),1);
						log::info('unit_price');
						log::info($prod_unitprice);
						if(count($prod_unitprice)==0){
							$prod_unitprice = DB::table('product_slab_flat')
											->select('unit_price')
											->where('product_id',$productArr[$p_index]['product_id'])
											->where('wh_id',$le_wh_id)
											->where('customer_type',$cust_type)
											->orderBy(DB::raw('pack_size*esu'), 'desc')
											->skip(0)
											->take(1)
											->get()->all();
							$prod_unitprice = json_decode(json_encode($prod_unitprice),1);
						}

						$singleunitprice = $prod_unitprice[0]['unit_price'];
					}
					
					
					log::info('$singleUnitPrice'.$singleunitprice);

		    		for($index =0; $index<count($product_info);$index++){
						log::info($product_info[$index]);
		            	$unitprice_without_Tax = $singleunitprice/(100+ $tax_percent) * 100 ;
		            	$taxval= $singleunitprice - $unitprice_without_Tax;
		            	$row_tax = $row_tax + ($taxval*$product_info[$index]['qty']);
		            	$row_qty = $row_qty+ $product_info[$index]['qty'];
		            	$row_cost = $row_cost + ($singleunitprice *$product_info[$index]['qty']); 
		            	$total_units = $total_units +$product_info[$index]['no_of_units'];
						log::info($taxval);
						
						$product_packs = array(
							'gds_order_id' => $orderId,
							'product_id'=>$productArr[$p_index]['product_id'],
							'pack_id' => $product_info[$index]['pack_level'],
							'esu' => $product_info[$index]['esu'],
							'esu_qty'=> $product_info[$index]['no_of_units'],
							'pack_qty'=>$product_info[$index]['qty'],
							'discount'=>0,
							'discount_amt'=>0,
							'discount_type'=>null,
							'star'=>$product_info[$index]['star'],
							'order_status'=>17001,
							'created_at' => date('Y-m-d H:i:s'),
							'updated_at' => date('Y-m-d H:i:s'),
							'created_by'=>Session('userId'),
							'updated_by'=>Session('userId')
						);  
						$packs_data[]=$product_packs;
					}
					
		            log::info($packs_data);
		            $price = $row_cost - $row_tax;
		            $parent_id = isset($productArr[$p_index]['parent_id'])?$productArr[$p_index]['parent_id']:null;
		            $freeqty = isset($productArr[$p_index]['fbqty'])?$productArr[$p_index]['fbqty']:0;
		            $mainqty = isset($productArr[$p_index]['mpqty'])?$productArr[$p_index]['mpqty']:0;

					$order_products = array('gds_order_id'=>$orderId,
						'product_id'=>$productArr[$p_index]['product_id'],
						'star'=> $product_info[0]['star'],
						'parent_id'=>$parent_id,
						'mp_product_id'=>$productArr[$p_index]['product_id'],
						'pname'=>$productArr[$p_index]['title'],
						'qty'=> $row_qty,
						'mrp'=> $productArr[$p_index]['mrp'],
						'price'=> $price,
						'discount'=> 0,
						'discount_amt'=> 0,
						'discount_type'=> '',
						'cost'=> $row_cost,
						'elp'=>$productArr[$p_index]['elp'],
						'actual_esp'=>$productArr[$p_index]['esp'],
						'tax'=> $row_tax,
						'tax_class'=> $taxclass,
						'total'=> $row_cost,
						'upc'=> 0,
						'sku'=>$productArr[$p_index]['sku'],
						'seller_sku'=> null,
						'unit_price'=>$singleunitprice,
						'no_of_units'=> $total_units,
						'order_status'=>17001,
						'product_slab_id'=>0,
						'freebie_qty'=>$freeqty,
						'freebie_mpq'=>$mainqty,
						'created_at' => date('Y-m-d H:i:s'),
						'updated_at' => date('Y-m-d H:i:s'),
						'created_by'=>Session('userId'),
						'updated_by'=>Session('userId'),
						'hsn_code' => $hsn_code
					);

					$gds_prod_id =DB::table('gds_order_products')->insertGetId($order_products);
					$SGST=$taxData[0]['SGST'];
					$CGST =$taxData[0]['CGST'] ;
					$IGST = $taxData[0]['IGST'];
					$UTGST =$taxData[0]['UTGST'] ;
					$tax_info = array(
						'gds_order_id' => $orderId,
						'product_id'=>$productArr[$p_index]['product_id'],
						'gds_order_prod_id' => $gds_prod_id,
						'tax_class' => $taxclass,
						'tax' => $tax_percent,
						'tax_value' => $row_tax,
						'SGST' => $SGST ,
						'CGST' => $CGST,
						'IGST'=> $IGST,
						'UTGST'=> $UTGST,
						'created_at' => date('Y-m-d H:i:s'),
						'updated_at' => date('Y-m-d H:i:s'),
						'created_by'=>Session('userId'),
						'updated_by'=>Session('userId')
					);
					DB::table('gds_orders_tax')->insert($tax_info);
					DB::table('gds_order_product_pack')->insert($packs_data);
		    		DB::table('inventory')
		    		->where('product_id',$productArr[$p_index]['product_id'])
		    		->where('le_wh_id',$le_wh_id)
		    		->update(['order_qty'=>DB::raw('(order_qty +'. $row_qty.')')]);
		    		DB::table('gds_orders_payment')
					->where('gds_order_id',$orderId)
					->update(['amount'=> DB::raw('(amount+'.$row_cost.')')]);
		    		DB::table('gds_orders')->where('gds_order_id',$orderId)->update([
		    			'total'=> DB::raw('(total +'. $row_cost.')'),
		    			'sub_total'=> DB::raw('(sub_total +'. $row_cost.')'),
		    			'tax_total'=> DB::raw('(tax_total +'. $row_tax.')'),
		    			'total_items'=> DB::raw('(total_items +1)'),
		    			'total_item_qty'=> DB::raw('(total_item_qty +'. $row_qty.')')
		    		]);
		    		$cbdata = DB::table('gds_orders')
		    		->select('cashback_amount','cust_le_id','created_at','total','is_self')
		    		->where('gds_order_id', $orderId)->get()->all();
    				$cbdata = json_decode(json_encode($cbdata),1);
    						    		$cust_le_id = $cbdata[0]['cust_le_id'];

    				$userId = DB::table('users')
    						  ->select('user_id')
    						  ->where('legal_entity_id',$cust_le_id)
    						  ->get()->all();
    				$userId = json_decode(json_encode($userId),1);
    				$userId = $userId[0]['user_id'];
    				$order_total = $cbdata[0]['total'];
    				$is_self = $cbdata[0]['is_self'];
    				$transType = 143002;
    				$comment = 'Adding cashback from edit order option';
    				$order_date = $cbdata[0]['created_at'];
    				$cashback=0;
    				$orderProducts = DB::table('gds_order_products')
    								->select('product_id','total')
    								->where('gds_order_id',$orderId)
    								->get()->all();
    				$orderProducts = json_decode(json_encode($orderProducts),1);
    				$cashback_prod = array();
			        foreach ($orderProducts as $product) {
			            $product_id = $product['product_id'];
			            $cashback_prod[][$product_id] = $product['total'];
			        }
			        log::info('cashbackprod');
			        log::info($cashback_prod);
			        $master_lookup = new MasterLookupController();
        			$ecashCalculated = json_decode($master_lookup->getOrderEcashValue($cashback_prod,$order_date,$le_wh_id,$customer_type,$is_self,$cust_le_id));
        			if(isset($ecashCalculated->data) && count($ecashCalculated->data)){
        				$cashback = $ecashCalculated->data[0]->cashback_applied;
        			}

					$this->paymentmodel = new PaymentModel();
    				if($cbdata[0]['cashback_amount']){
    					$comment = 'Updating cashback from edit order option';
    					$cashBackAmt = $cbdata[0]['cashback_amount'];
    					DB::table('ecash_transaction_history')
    					->where('order_id',$orderId)
    					->where('transaction_type',143002)
    					->where('order_status_id',17001)
    					->delete();
    					$eCash = ['cashback'=>DB::raw('(cashback-' . $cashBackAmt . ')')];
                    	$this->paymentmodel->updateEcash($userId, $eCash);
                    	DB::table('gds_orders')->where('gds_order_id', $orderId)->update(['cashback_amount' => 0,'instant_wallet_cashback'=>0]);

    				}
    				if($cashback){
    					$this->paymentmodel->updateUserEcash($userId,$cashback,$order_total,$orderId,$transType,$comment,17001);			

    					DB::table('gds_orders')->where('gds_order_id', $orderId)->update(['cashback_amount' => $cashback,'instant_wallet_cashback'=>1]);
    				}    				

		    	}
	        	return Response::json(array('status'=>true, 'message'=>'Product added successfully'));
		    }else{
		        return Response::json(array('status'=>false, 'message'=>'Soh not available'));
		    }
		}else{
			return Response::json(array('status'=>false, 'message'=>'Tax not found'));
		}
    }
    public function delteProductFromOpenOrder($product_id,$orderId,$productData,$le_wh_id,$customer_type)
    {
		DB::table('gds_orders_tax')
			->where('gds_order_id',$orderId)
			->where('product_id',$product_id)
			->delete();
		DB::table('gds_order_product_pack')
			->where('gds_order_id',$orderId)
			->where('product_id',$product_id)
			->delete();
		DB::table('gds_order_products')
			->where('gds_order_id',$orderId)
			->where('product_id',$product_id)
			->delete();
		DB::table('inventory')
			->where('product_id',$product_id)
			->where('le_wh_id',$le_wh_id)
			->decrement('order_qty', $productData['qty']);
		DB::table('gds_orders_payment')
			->where('gds_order_id',$orderId)
			->update(['amount'=> DB::raw('(amount-'.$productData['total'].')')]);
		DB::table('gds_orders')
		   ->where('gds_order_id', $orderId)
		   ->update([
		       'total' => DB::raw('(total -'. $productData['total'].')'),
		       'sub_total' => DB::raw('(sub_total -'. $productData['total'].')'),
		       'tax_total' => DB::raw('(tax_total -'. $productData['tax'].')'),
		       'discount' => DB::raw('(discount - '.$productData['discount'].')'),
		       'discount_amt' => DB::raw('(discount_amt  - '.$productData['discount_amt'].')'),
		       'total_items' => DB::raw('(total_items -1)'),
		       'total_item_qty' => DB::raw('(total_item_qty -'. $productData['qty'].')')
		   ]);
		$cbdata = DB::table('gds_orders')
		    		->select('cashback_amount','cust_le_id','created_at','total','is_self')
		    		->where('gds_order_id', $orderId)->get()->all();
		$cbdata = json_decode(json_encode($cbdata),1);
		$cust_le_id = $cbdata[0]['cust_le_id'];

		$userId = DB::table('users')
				  ->select('user_id')
				  ->where('legal_entity_id',$cust_le_id)
				  ->get()->all();
		$userId = json_decode(json_encode($userId),1);
		$userId = $userId[0]['user_id'];
		$order_total = $cbdata[0]['total'];
		$is_self = $cbdata[0]['is_self'];
		$transType = 143002;
		$comment = 'Adding cashback from edit order option';
		$order_date = $cbdata[0]['created_at'];
		$cashback=0;
		$orderProducts = DB::table('gds_order_products')
						->select('product_id','total')
						->where('gds_order_id',$orderId)
						->get()->all();
		$orderProducts = json_decode(json_encode($orderProducts),1);
		$cashback_prod = array();
        foreach ($orderProducts as $product) {
            $product_id = $product['product_id'];
            $cashback_prod[][$product_id] = $product['total'];
        }
        log::info('cashbackprod');
        log::info($cashback_prod);
        $master_lookup = new MasterLookupController();
		$ecashCalculated = json_decode($master_lookup->getOrderEcashValue($cashback_prod,$order_date,$le_wh_id,$customer_type,$is_self,$cust_le_id));
		if(isset($ecashCalculated->data) && count($ecashCalculated->data)){
			$cashback = $ecashCalculated->data[0]->cashback_applied;
		}

		$this->paymentmodel = new PaymentModel();
		if($cbdata[0]['cashback_amount']){
			$comment = 'Updating cashback from edit order option';
			$cashBackAmt = $cbdata[0]['cashback_amount'];
			DB::table('ecash_transaction_history')
			->where('order_id',$orderId)
			->where('transaction_type',143002)
			->where('order_status_id',17001)
			->delete();
			$eCash = ['cashback'=>DB::raw('(cashback-' . $cashBackAmt . ')')];
        	$this->paymentmodel->updateEcash($userId, $eCash);
        	DB::table('gds_orders')->where('gds_order_id', $orderId)->update(['cashback_amount' => 0,'instant_wallet_cashback'=>0]);

		}
		if($cashback){
			$this->paymentmodel->updateUserEcash($userId,$cashback,$order_total,$orderId,$transType,$comment,17001);			

			DB::table('gds_orders')->where('gds_order_id', $orderId)->update(['cashback_amount' => $cashback,'instant_wallet_cashback'=>1]);
		}  
		return true;	
    }
    public function getDeliveryExecutiveName($orderId){
    	$dlexename = DB::select(DB::raw("call  get_delivery_ff_name($orderId)"));
    	return $dlexename;

    }


    public function getStatusCounts($salestype){
    	try{
 			$bu_id=Session::get('business_unitid');
 			$bu_id =(isset($bu_id) && $bu_id!='') ? $bu_id : -1;
            $userID = Session('userId');
            $stscount=DB::select("CALL getSalesOrderCountByType($salestype,$bu_id,$userID)");   			
            return $stscount;
    	}catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return $stscount=array();
		}
    }

    public function getFreebeeForProduct($product_id,$qty,$le_wh_id,$buyer_state_id,$seller_state_id){
    	$freebee = DB::select(DB::raw("select main_prd_id,mpq,free_prd_id,qty from freebee_conf where main_prd_id=".$product_id." and '". date('Y-m-d') ."' between start_date and end_date"));
    	$freebee = json_decode(json_encode($freebee),1);
    	$message='';
    	$taxinfo = array();
    	if(count($freebee)>0){
    		if($freebee[0]['mpq']<=$qty){
    			$freebeeqty = floor($qty/$freebee[0]['mpq']);
    			$actual_freebee = $freebeeqty * $freebee[0]['qty'];
    			$available = DB::table('inventory')
				    		->select(DB::raw('soh-(order_qty+reserved_qty) as available'))
				    		->where('product_id',$freebee[0]['free_prd_id'])
				    		->where('le_wh_id',$le_wh_id)
                			->useWritePdo()
				    		->get()->all();
    			$available = json_decode(json_encode($available),1);
				$issue_with_product = false;
    			if(count($available)>0){
    				if($available[0]['available'] < $actual_freebee){
						$issue_with_product = true;
						$message = 'Freebee soh not available';
    				} 
    			}else{
					$issue_with_product = true;
    				$message = 'Freebee record missing in inventory';
    			}
    			if(!$issue_with_product){
    				$taxinfo = $this->getTaxForProduct($freebee[0]['free_prd_id'],$seller_state_id,$buyer_state_id);
    				if(is_array($taxinfo) && count($taxinfo)>0){
						
	    			}else{
	    				$issue_with_product = true;
	    				$message = 'Tax not found for freebee';
	    			}
    			}
    			$packs = array();
    			$sku='';
    			$product_title='';
    			if($issue_with_product == false){
    				$available = DB::table('product_pack_config')
				    		->select(DB::raw('level as pack_level,esu,star,no_of_eaches'))
				    		->where('product_id',$freebee[0]['free_prd_id'])
				    		->get()->all();		    

    				$available = json_decode(json_encode($available),1);
    				$product_price = DB::table('products')
				    				->select('mrp')
				    				->where('product_id',$freebee[0]['free_prd_id'])
				    				->get()->all();
				    $product_price = json_decode(json_encode($product_price),1);

    				$packs['esu'] = $available[0]['esu'];
    				$packs['star'] = $available[0]['star'];
    				$packs['pack_level'] = $available[0]['pack_level'];
    				$packs['qty'] = $actual_freebee;
    				$packs['unit_price'] = 0;
    				$packs['pack_total'] = 0;
    				$packs['no_of_units'] = $actual_freebee / ($available[0]['esu']* $available[0]['no_of_eaches']);
    				$prod_info = DB::table('products')->select('product_title','sku')->where('product_id',$freebee[0]['free_prd_id'])->get()->all();
    				$prod_info = json_decode(json_encode($prod_info),1);
    				$sku = $prod_info[0]['sku'];
    				$product_title = $prod_info[0]['product_title'];
    				$product_packs = array();
    				$product_packs[0] = $packs;
    				$free_prd = array(
	    				'product_id' => $freebee[0]['free_prd_id'],
	    				'freebie_qty' => $actual_freebee,
	    				'issue_with_product' => $issue_with_product,
	    				'is_freebee' => 1,
	    				'product_info' => $product_packs,
	    				'mrp' => $product_price[0]['mrp'],
	    				'total_qty' => $actual_freebee,
	    				'sku' => $sku,
	    				'title' => $product_title,
	    				'message' => $message,
	    				'total' => 0,
	    				'taxinfo' => $taxinfo,
	    				'elp'=>0,
	    				'esp'=>0,
	    				'parent_id'=>$product_id,
	    				'offer_qty'=>$freebee[0]['qty'],
	    				'main_prd_qty'=>$freebee[0]['mpq'],
	    				'mpqty'=>$freebee[0]['qty']
	    			);
	    			return $free_prd;
    			}else{
    				$free_prd = array(
    					'message' => $message,
    					'issue_with_product' => $issue_with_product
    				);
    				return $free_prd;
    			}

    			
    		}else{
    			log::info('first2');
    			return array();
    		}
    	}else{
    		log::info('first1');
    		return array();
    	}
    }
    public function getTaxForProduct($product_id,$seller,$buyer)
    {
    	$url = env('APP_TAXAPI');
        $callType = "POST";
        $seller_state_id = preg_replace('/[^0-9]/', '', $seller);
        $buyer_state_id = preg_replace('/[^0-9]/', '', $buyer);

    	$postData = array(
		                    'product_id' => $product_id, 
		                    'seller_state_id' => $seller_state_id,
		                    'buyer_state_id' => $buyer_state_id
	                	);
    	$taxinfo = Utility::sendRequest($url,$postData);
    	return $taxinfo;
    }
    public function getCancellationData($orderId)
	{
		$cancelcount = DB::table('gds_cancel_grid')
						->where('gds_order_id',$orderId)
						->count();
		return $cancelcount;
	}
	public function getOrderStatusFromOrderId($orderId)
	{
		$ordercount = DB::table('gds_orders')
						->where('gds_order_id',$orderId)
						->where('order_status_id',17001)
						->count();
		return $ordercount;
	}
    public function getAllOrderTotal($orderId){
		$query ="SELECT SUM(cost) as total,group_concat(product_id) as productIds  FROM gds_order_products gop WHERE gop.`gds_order_id`=$orderId";
        $queryResult=DB::select(DB::raw($query));

        return $queryResult;

	}

	public function getPendingOrderDays(){
		$pending_order_days=DB::table('master_lookup')
		                ->select('description') 
			            ->where('value','=',"78025")
			            ->where('mas_cat_id','=',"78")
			            ->get()->all();
		$result=json_decode(json_encode($pending_order_days),true);
        return $result;
	}
    
    public function getParentUserAccess($user_id){
    	$result=DB::table('users')
    	                ->select('user_id')
			            ->where('user_id',$user_id)
			            ->where('is_parent','=','1')
			            ->count();
	    return $result;
    }
    
}