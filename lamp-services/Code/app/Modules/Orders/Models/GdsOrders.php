<?php

namespace App\Modules\Orders\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Indent\Models\LegalEntity;
use DB;
use Session;
use Log;
use Response;
use Input;
use Cache;
use App\Central\Repositories\RoleRepo;

class GdsOrders extends Model
{
    protected $table = "gds_orders";
    public $timestamps = false;


	public function getOrderData($filterType, $count=0, $offset=0, $perpage=10, $filter=array(), $sort=array(),$salestype) {
		$fields = array(
                            'orders.gds_order_id',
                            'orders.platform_id',
                            'orders.order_code',
                            'orders.shop_name', 
                            'orders.order_date', 
                            'orders.total as order_value',
                            'orders.hub_id',
                            'city.officename as areaname',
                            'pjp.pjp_name as beat',
            				'orders.total_items as totSku',
            				'orders.total_item_qty as orderedQty',
            				'orders.order_status_id',
            				'le.le_code',
            				'lw.lp_wh_name',
            				'spokes.spoke_name as spokeName',
            				DB::raw('getRetailerRatingName(le.legal_entity_id) as custRating')
                        );

		$query = DB::table('gds_orders as orders');
        //$query->leftJoin('gds_order_products as gdsprd', 'orders.gds_order_id', '=', 'gdsprd.gds_order_id');
       
        $query->leftJoin('customers as cust', 'cust.le_id', '=', 'orders.cust_le_id');
       	$query->leftJoin('cities_pincodes as city', 'city.city_id', '=', 'cust.area_id');
		$query->leftJoin('pjp_pincode_area as pjp', 'pjp.pjp_pincode_area_id', '=', 'orders.beat');
		$query->leftJoin('spokes','pjp.spoke_id','=','spokes.spoke_id');
	    $query->leftJoin('master_lookup as ordStatus', function ($join) {
		            $join->on('ordStatus.value', '=', 'orders.order_status_id')->where("ordStatus.mas_cat_id", '=', '17');
		        	});   	
		$query->leftJoin('legal_entities as le', 'le.legal_entity_id', '=', 'orders.cust_le_id');
		$query->leftJoin('legalentity_warehouses as lw', 'lw.le_wh_id', '=', 'orders.hub_id');
		// code start for tabs
		#var_dump($filterType);die;
		if($filterType == 'allorders') {
			
			$fields[] = 'ordSlot1.master_lookup_name as slot1';
            $fields[] = DB::raw("CONCAT(pickuser.firstname, ' ', pickuser.lastname) as pickedby");
            $fields[] = 'ordSlot2.master_lookup_name as slot2';
            $fields[] = 'orders.scheduled_delivery_date';
            $fields[] = 'ordStatus.master_lookup_name as orderStatus';
            $fields[] = DB::raw("CONCAT(users.firstname, ' ', users.lastname) as created_by");
            $fields[] = DB::raw("((invgrid.invoice_qty*100)/orders.total_item_qty) as fillrate");
            $fields[] = 'invgrid.gds_invoice_grid_id';
            $fields[] = 'inv.invoice_code';
            $fields[] = 'track.delivery_date';
            $fields[] = DB::raw("GetUserName (track.delivered_by, 2) AS deliveredby");
            $fields[] = 'track.pick_code';  
            $query->leftJoin('users', 'users.user_id', '=', 'orders.created_by');
            $query->leftJoin('master_lookup as ordSlot1', function ($join) {
		            $join->on('ordSlot1.value', '=', 'orders.pref_slab1')->where("ordSlot1.mas_cat_id", '=', '110');
		        	});

        	$query->leftJoin('master_lookup as ordSlot2', function ($join) {
		            $join->on('ordSlot2.value', '=', 'orders.pref_slab2')->where("ordSlot2.mas_cat_id", '=', '110');
	       	 		});       	
          	$query->leftJoin('gds_order_track as track', 'track.gds_order_id', '=', 'orders.gds_order_id');
          	$query->leftJoin('users as pickuser', 'pickuser.user_id', '=', 'track.picker_id');
       		$query->leftJoin('gds_invoice_grid as invgrid', 'invgrid.gds_order_id', '=', 'orders.gds_order_id');
       		$query->leftJoin('gds_invoice_grid as inv', 'inv.gds_order_id', '=', 'orders.gds_order_id');
       		$query->whereNotIn('orders.order_status_id', array(17016));

		}

		
		if($filterType == 'open') {
			$fields[] = DB::raw("CONCAT(users.firstname, ' ', users.lastname) as created_by");
			$fields[] = 'ordSlot1.master_lookup_name as slot1';
            $fields[] = 'ordSlot2.master_lookup_name as slot2';
            $fields[] = 'orders.scheduled_delivery_date';

			$query->leftJoin('users', 'users.user_id', '=', 'orders.created_by');
	        $query->leftJoin('master_lookup as ordSlot1', function ($join) {
		            $join->on('ordSlot1.value', '=', 'orders.pref_slab1')->where("ordSlot1.mas_cat_id", '=', '110');
		        	});

        	$query->leftJoin('master_lookup as ordSlot2', function ($join) {
		            $join->on('ordSlot2.value', '=', 'orders.pref_slab2')->where("ordSlot2.mas_cat_id", '=', '110');
	       	 		});
        	$query->where('orders.order_status_id', '17001');
			
		}

		if($filterType == 'picklist') {
			$fields[] = DB::raw("CONCAT(users.firstname, ' ', users.lastname) as created_by");
			$fields[] = DB::raw("CONCAT(pickuser.firstname, ' ', pickuser.lastname) as picker");
			$fields[] = 'track.scheduled_piceker_date';
			$fields[] = 'track.pick_code';
			$query->leftJoin('users', 'users.user_id', '=', 'orders.created_by');
            $query->leftJoin('gds_order_track as track', 'track.gds_order_id', '=', 'orders.gds_order_id');
			$query->leftJoin('users as pickuser', 'pickuser.user_id', '=', 'track.picker_id');
		
			$query->where('orders.order_status_id', '17020');
		}

		if($filterType == 'cancelbycust') {
			
			$fields[] = 'orders.scheduled_delivery_date';
            $fields[] = 'ordStatus.master_lookup_name as orderStatus';
            $fields[] = 'clookup.master_lookup_name as cancelReason';
                        
            $fields[] = DB::raw('SUM(canitem.total_price) as cancelledValue');
            $fields[] = DB::raw('SUM(canitem.qty) as cancelledQty');

            $fields[] = DB::raw("CONCAT(users.firstname, ' ', users.lastname) as created_by");
            
            $query->leftJoin('gds_cancel_grid as cangrid', 'cangrid.gds_order_id', '=', 'orders.gds_order_id');
            
           $query->leftJoin('gds_order_cancel as canitem', 'cangrid.cancel_grid_id', '=', 'canitem.cancel_grid_id');
            
            $query->leftJoin('master_lookup as clookup', 'clookup.value', '=', 'canitem.cancel_reason_id');

            $query->leftJoin('users', 'users.user_id', '=', 'orders.created_by');
            $query->whereIn('orders.order_status_id', array('17009'));
       	
		}

		if($filterType == 'cancelbyebutor') {
			
			$fields[] = 'orders.scheduled_delivery_date';
            $fields[] = 'ordStatus.master_lookup_name as orderStatus';
            $fields[] = 'clookup.master_lookup_name as cancelReason';
            $fields[] = DB::raw('SUM(canitem.total_price) as cancelledValue');
            $fields[] = DB::raw('SUM(canitem.qty) as cancelledQty');
            $fields[] = DB::raw("CONCAT(users.firstname, ' ', users.lastname) as created_by");
            
            $query->leftJoin('gds_cancel_grid as cangrid', 'cangrid.gds_order_id', '=', 'orders.gds_order_id');
            
            $query->leftJoin('gds_order_cancel as canitem', 'cangrid.cancel_grid_id', '=', 'canitem.cancel_grid_id');
            $query->leftJoin('master_lookup as clookup', 'clookup.value', '=', 'canitem.cancel_reason_id');

            $query->leftJoin('users', 'users.user_id', '=', 'orders.created_by');
            $query->whereIn('orders.order_status_id', array('17015'));
       	
		}

		if($filterType == 'partialcancel') {
			$fields[] = 'invgrid.gds_invoice_grid_id';
			$fields[] = 'orders.scheduled_delivery_date';
            $fields[] = 'ordStatus.master_lookup_name as orderStatus';
            $fields[] = 'clookup.master_lookup_name as cancelReason';
            $fields[] = 'cangrid.created_at as cancelDate';
            $fields[] = DB::raw('SUM(canitem.total_price) as cancelledValue');
            $fields[] = DB::raw('SUM(canitem.qty) as cancelledQty');
            $fields[] = DB::raw("CONCAT(users.firstname, ' ', users.lastname) as created_by");
            
            $query->leftJoin('gds_invoice_grid as invgrid', 'invgrid.gds_order_id', '=', 'orders.gds_order_id');
            $query->join('gds_cancel_grid as cangrid', 'cangrid.gds_order_id', '=', 'orders.gds_order_id');
            
            $query->leftJoin('gds_order_cancel as canitem', 'cangrid.cancel_grid_id', '=', 'canitem.cancel_grid_id');
            
            $query->leftJoin('master_lookup as clookup', 'clookup.value', '=', 'canitem.cancel_reason_id');

            $query->leftJoin('users', 'users.user_id', '=', 'orders.created_by');
            $query->whereNotIn('orders.order_status_id', array('17015','17009'));
       	
		}


		if($filterType == 'dispatch') {

			$fields[] = DB::raw("CONCAT(pickuser.firstname, ' ', pickuser.lastname) as pickedby");
			$fields[] = 'track.cfc_cnt';
			$fields[] = 'track.bags_cnt';
			$fields[] = 'track.crates_cnt';
			$fields[] = 'track.pick_code';
			$fields[] = DB::raw("CONCAT(verifieduser.firstname, ' ', verifieduser.lastname) as verifiedby");
			$fields[] = DB::raw("((gdsship.ship_qty*100) /orders.total_item_qty)  AS fillrate");
			
			$query->leftJoin('gds_ship_grid as gdsship', 'gdsship.gds_order_id', '=', 'orders.gds_order_id');
			$query->leftJoin('gds_order_track as track', 'track.gds_order_id', '=', 'orders.gds_order_id');
			$query->leftJoin('users as pickuser', 'pickuser.user_id', '=', 'track.picker_id');
			$query->leftJoin('users as verifieduser', 'verifieduser.user_id', '=', 'track.checker_id');
			$query->where('orders.order_status_id', '17005');
		}

		if($filterType == 'invoiced') {
                        $fields[] = DB::raw("CONCAT(pickuser.firstname, ' ', pickuser.lastname) as pickedby");
			$fields[] = DB::raw("CONCAT(deluser.firstname, ' ', deluser.lastname) as deliveredby");
			$fields[] = 'track.cfc_cnt';
			$fields[] = 'track.bags_cnt';
			$fields[] = 'track.crates_cnt';
			$fields[] = 'inv.grand_total as totInvoiceValue';
			$fields[] = 'inv.invoice_qty as totInvoiceQty';
			$fields[] = 'inv.invoice_code';
			$fields[] = 'inv.created_at as invoice_date';
			$fields[] = 'inv.gds_invoice_grid_id';
			$fields[] = 'track.delivery_date';
			
			$query->leftJoin('gds_order_track as track', 'track.gds_order_id', '=', 'orders.gds_order_id');
			$query->leftJoin('users as deluser', 'deluser.user_id', '=', 'track.delivered_by');
                        $query->leftJoin('users as pickuser', 'pickuser.user_id', '=', 'track.picker_id');
			$query->leftJoin('gds_invoice_grid as inv', 'inv.gds_order_id', '=', 'orders.gds_order_id');
			$query->where('orders.order_status_id', '17021');			
		}

		if($filterType == 'stocktransit') {

                        $fields[] = DB::raw("CONCAT(pickuser.firstname, ' ', pickuser.lastname) as pickedby");
			$fields[] = DB::raw("CONCAT(deluser.firstname, ' ', deluser.lastname) as deliveredby");
			$fields[] = 'track.cfc_cnt';
			$fields[] = 'track.bags_cnt';
			$fields[] = 'track.crates_cnt';
			$fields[] = 'inv.grand_total as totInvoiceValue';
			$fields[] = 'inv.invoice_qty as totInvoiceQty';
			$fields[] = 'inv.invoice_code';
			$fields[] = 'inv.created_at as invoice_date';
			$fields[] = 'inv.gds_invoice_grid_id';
			$fields[] = 'track.delivery_date';

			$fields[] = DB::raw("CONCAT(stdeluser.firstname, ' ', stdeluser.lastname) as st_de_name");
			$fields[] = 'track.st_del_date';
			$fields[] = 'track.st_vehicle_no';
			$fields[] = 'track.st_driver_name';
			$fields[] = 'track.st_driver_mobile';
			$fields[] = 'track.st_docket_no';

			$query->leftJoin('gds_order_track as track', 'track.gds_order_id', '=', 'orders.gds_order_id');
			$query->leftJoin('users as deluser', 'deluser.user_id', '=', 'track.delivered_by');
                        $query->leftJoin('users as pickuser', 'pickuser.user_id', '=', 'track.picker_id');
			$query->leftJoin('users as stdeluser', 'stdeluser.user_id', '=', 'track.st_del_ex_id');
			$query->leftJoin('gds_invoice_grid as inv', 'inv.gds_order_id', '=', 'orders.gds_order_id');
			
			$query->where('orders.order_status_id', '17024');			
		}
		if($filterType == 'stockhub') {
                        $fields[] = DB::raw("CONCAT(pickuser.firstname, ' ', pickuser.lastname) as pickedby");
			$fields[] = DB::raw("CONCAT(deluser.firstname, ' ', deluser.lastname) as deliveredby");
			$fields[] = 'track.cfc_cnt';
			$fields[] = 'track.bags_cnt';
			$fields[] = 'track.crates_cnt';
			$fields[] = 'inv.grand_total as totInvoiceValue';
			$fields[] = 'inv.invoice_qty as totInvoiceQty';
			$fields[] = 'inv.invoice_code';
			$fields[] = 'inv.created_at as invoice_date';
			$fields[] = 'inv.gds_invoice_grid_id';
			$fields[] = 'track.delivery_date';

			$fields[] = DB::raw("CONCAT(stdeluser.firstname, ' ', stdeluser.lastname) as st_de_name");
			$fields[] = DB::raw("CONCAT(strecuser.firstname, ' ', strecuser.lastname) as st_re_name");
			$fields[] = 'track.st_del_date';
			$fields[] = 'track.st_vehicle_no';
			$fields[] = 'track.st_driver_name';
			$fields[] = 'track.st_driver_mobile';
			$fields[] = 'track.st_docket_no';
			$fields[] = 'track.st_received_at';

			
			$query->leftJoin('gds_order_track as track', 'track.gds_order_id', '=', 'orders.gds_order_id');
			$query->leftJoin('users as deluser', 'deluser.user_id', '=', 'track.delivered_by');
                        $query->leftJoin('users as pickuser', 'pickuser.user_id', '=', 'track.picker_id');
			$query->leftJoin('users as stdeluser', 'stdeluser.user_id', '=', 'track.st_del_ex_id');
			$query->leftJoin('users as strecuser', 'strecuser.user_id', '=', 'track.st_received_by');
			$query->leftJoin('gds_invoice_grid as inv', 'inv.gds_order_id', '=', 'orders.gds_order_id');
			
			$query->where('orders.order_status_id', '17025');			
		}

		if($filterType == 'stockindc') {
            $fields[] = DB::raw("CONCAT(pickuser.firstname, ' ', pickuser.lastname) as pickedby");
			$fields[] = DB::raw("CONCAT(deluser.firstname, ' ', deluser.lastname) as deliveredby");
			$fields[] = 'track.cfc_cnt';
			$fields[] = 'track.bags_cnt';
			$fields[] = 'track.crates_cnt';
			$fields[] = 'inv.grand_total as totInvoiceValue';
			$fields[] = 'inv.invoice_qty as totInvoiceQty';
			$fields[] = 'inv.invoice_code';
			$fields[] = 'inv.created_at as invoice_date';
			$fields[] = 'inv.gds_invoice_grid_id';
			$fields[] = 'track.delivery_date';

			$fields[] = DB::raw("CONCAT(rtdeluser.firstname, ' ', rtdeluser.lastname) as rt_de_name");
			$fields[] = DB::raw("CONCAT(rtrecuser.firstname, ' ', rtrecuser.lastname) as rt_re_name");
			$fields[] = 'track.rt_del_date';
			$fields[] = 'track.rt_vehicle_no';
			$fields[] = 'track.rt_driver_name';
			$fields[] = 'track.rt_driver_mobile';
			$fields[] = 'track.rt_docket_no';
			$fields[] = 'track.rt_received_at';
			$fields[] = 'rgrid.return_grid_id';
			$fields[] = DB::raw('cast(rgrid.total_return_value as decimal(18,2)) as totReturnValue');
			$fields[] = 'gdsr.dit_qty as totDamagedValue';
			$fields[] = 'gdsr.dnd_qty as totMissingValue';
			$fields[] = 'gdsr.excess_qty as totExcessValue';

			$fields[] = 'ordStatus.master_lookup_name as orderStatus';
			
			$query->leftJoin('gds_order_track as track', 'track.gds_order_id', '=', 'orders.gds_order_id');
			$query->leftJoin('users as deluser', 'deluser.user_id', '=', 'track.delivered_by');
                        $query->leftJoin('users as pickuser', 'pickuser.user_id', '=', 'track.picker_id');
			$query->leftJoin('users as rtdeluser', 'rtdeluser.user_id', '=', 'track.rt_del_ex_id');
			$query->leftJoin('users as rtrecuser', 'rtrecuser.user_id', '=', 'track.rt_received_by');
			$query->leftJoin('gds_invoice_grid as inv', 'inv.gds_order_id', '=', 'orders.gds_order_id');
			$query->leftJoin('gds_return_grid as rgrid', 'rgrid.gds_order_id', '=', 'orders.gds_order_id');

			$query->leftJoin('gds_returns as gdsr','orders.gds_order_id','=','gdsr.gds_order_id');
			
			$query->whereIn('orders.order_status_id', array('17022','17023'));	
			$query->where('orders.order_transit_status', '17028');
			//$query->where('gdsr.return_status_id', array('57067'));		
			$query->where('rgrid.return_status_id', array('57067'));		
		}
		
		if($filterType == 'ofd') {
            $fields[] = DB::raw("CONCAT(pickuser.firstname, ' ', pickuser.lastname) as pickedby");
			$fields[] = DB::raw("CONCAT(deluser.firstname, ' ', deluser.lastname) as deliveredby");
			$fields[] = 'track.cfc_cnt';
			$fields[] = 'track.bags_cnt';
			$fields[] = 'track.crates_cnt';
			$fields[] = 'inv.grand_total as totInvoiceValue';
			$fields[] = 'inv.invoice_qty as totInvoiceQty';
			$fields[] = 'inv.invoice_code';
			$fields[] = 'inv.created_at as invoice_date';
			$fields[] = 'inv.gds_invoice_grid_id';
			$fields[] = 'track.delivery_date';

			$fields[] = DB::raw("CONCAT(stdeluser.firstname, ' ', stdeluser.lastname) as st_de_name");
			$fields[] = DB::raw("CONCAT(strecuser.firstname, ' ', strecuser.lastname) as st_re_name");
			$fields[] = 'track.st_del_date';
			$fields[] = 'track.st_vehicle_no';
			$fields[] = 'track.st_driver_name';
			$fields[] = 'track.st_driver_mobile';
			$fields[] = 'track.st_docket_no';
			$fields[] = 'track.st_received_at';

			
			$query->leftJoin('gds_order_track as track', 'track.gds_order_id', '=', 'orders.gds_order_id');
			$query->leftJoin('users as deluser', 'deluser.user_id', '=', 'track.delivered_by');
            $query->leftJoin('users as pickuser', 'pickuser.user_id', '=', 'track.picker_id');
			$query->leftJoin('users as stdeluser', 'stdeluser.user_id', '=', 'track.st_del_ex_id');
			$query->leftJoin('users as strecuser', 'strecuser.user_id', '=', 'track.st_received_by');
			$query->leftJoin('gds_invoice_grid as inv', 'inv.gds_order_id', '=', 'orders.gds_order_id');
			
			$query->where('orders.order_status_id', '17026');			
		}
		
		if($filterType == 'delivered') {
                        $fields[] = DB::raw("CONCAT(pickuser.firstname, ' ', pickuser.lastname) as pickedby");
			$fields[] = DB::raw("CONCAT(deluser.firstname, ' ', deluser.lastname) as deliveredby");
			$fields[] = 'track.delivery_date';
			$fields[] = 'inv.created_at as invoice_date';
			$fields[] = 'track.crates_cnt';
			$fields[] = 'inv.grand_total as totInvoiceValue';
			$fields[] = 'inv.invoice_qty as totInvoiceQty';
			$fields[] = 'inv.invoice_code';
			$fields[] = 'ordStatus.master_lookup_name as orderStatus';

			$fields[] = 'inv.gds_invoice_grid_id';
			$query->leftJoin('gds_order_track as track', 'track.gds_order_id', '=', 'orders.gds_order_id');
			$query->leftJoin('users as deluser', 'deluser.user_id', '=', 'track.delivered_by');
                        $query->leftJoin('users as pickuser', 'pickuser.user_id', '=', 'track.picker_id');
			$query->leftJoin('gds_invoice_grid as inv', 'inv.gds_order_id', '=', 'orders.gds_order_id');

			$query->whereIn('orders.order_status_id', array('17007'));			
		}

		if($filterType == 'partialdelivered') {
                        $fields[] = DB::raw("CONCAT(pickuser.firstname, ' ', pickuser.lastname) as pickedby");
			$fields[] = DB::raw("CONCAT(deluser.firstname, ' ', deluser.lastname) as deliveredby");
			$fields[] = 'track.delivery_date';
			$fields[] = 'inv.created_at as invoice_date';
			$fields[] = 'track.crates_cnt';
			$fields[] = 'inv.grand_total as totInvoiceValue';
			$fields[] = 'inv.invoice_qty as totInvoiceQty';
			$fields[] = 'inv.invoice_code';
			$fields[] = 'ordStatus.master_lookup_name as orderStatus';
			
			$fields[] = DB::raw('cast(rgrid.total_return_value as decimal(18,2)) as totReturnValue');
			$fields[] = 'rgrid.total_return_item_qty as totReturnQty';

			$fields[] = 'inv.gds_invoice_grid_id';
			$fields[] = 'rgrid.return_grid_id';
			$query->leftJoin('gds_order_track as track', 'track.gds_order_id', '=', 'orders.gds_order_id');
			$query->leftJoin('users as deluser', 'deluser.user_id', '=', 'track.delivered_by');
                        $query->leftJoin('users as pickuser', 'pickuser.user_id', '=', 'track.picker_id');
			$query->leftJoin('gds_invoice_grid as inv', 'inv.gds_order_id', '=', 'orders.gds_order_id');

			$query->leftJoin('gds_return_grid as rgrid', 'rgrid.gds_order_id', '=', 'orders.gds_order_id');
			$query->whereIn('orders.order_status_id', array('17023'));
			//$query->join('gds_returns as gdsr','orders.gds_order_id','=','gdsr.gds_order_id');
			//$query->where('gdsr.approval_status', 1);
			//$query->where('rgrid.approval_status', 1);
			$query->where('rgrid.return_status_id', 57066);
		}

		if($filterType == 'completed') {
            $fields[] = DB::raw("CONCAT(pickuser.firstname, ' ', pickuser.lastname) as pickedby");
			$fields[] = DB::raw("CONCAT(deluser.firstname, ' ', deluser.lastname) as deliveredby");
			$fields[] = 'track.scheduled_piceker_date as picked_date';
			$fields[] = 'track.delivery_date';
			$fields[] = 'gdsship.created_at as ship_date';
			$fields[] = 'inv.created_at as invoice_date';
			$fields[] = 'track.pick_code';
			$fields[] = 'inv.grand_total as totInvoiceValue';
			$fields[] = 'inv.invoice_qty as totInvoiceQty';
			$fields[] = 'inv.invoice_code';
			$fields[] = 'ordStatus.master_lookup_name as orderStatus';
			$fields[] = DB::raw('cast(rgrid.total_return_value as decimal(18,2)) as totReturnValue');
			
			$fields[] = 'inv.gds_invoice_grid_id';

			$query->leftJoin('gds_order_track as track', 'track.gds_order_id', '=', 'orders.gds_order_id');
			$query->leftJoin('gds_ship_grid as gdsship', 'gdsship.gds_order_id', '=', 'orders.gds_order_id');
			$query->leftJoin('users as deluser', 'deluser.user_id', '=', 'track.delivered_by');
                        $query->leftJoin('users as pickuser', 'pickuser.user_id', '=', 'track.picker_id');
			$query->leftJoin('gds_invoice_grid as inv', 'inv.gds_order_id', '=', 'orders.gds_order_id');
			$query->leftJoin('gds_return_grid as rgrid', 'rgrid.gds_order_id', '=', 'orders.gds_order_id');
			$query->whereIn('orders.order_status_id', array('17008'));
		}


		if($filterType == 'unpaid') {
			$fields[] = DB::raw("CONCAT(deluser.firstname, ' ', deluser.lastname) as deliveredby");
			$fields[] = DB::raw("CONCAT(collecteduser.firstname, ' ', collecteduser.lastname) as collected_by");
			$fields[] = 'track.delivery_date';
			$fields[] = 'gdsship.created_at as ship_date';
			$fields[] = 'inv.created_at as invoice_date';
			$fields[] = 'inv.grand_total as totInvoiceValue';
			$fields[] = 'inv.invoice_qty as totInvoiceQty';
			$fields[] = 'inv.invoice_code';
			$fields[] = 'ordStatus.master_lookup_name as orderStatus';
			$fields[] = DB::raw('cast(rgrid.total_return_value as decimal(18,2)) as totReturnValue');
			$fields[] = 'inv.gds_invoice_grid_id';
                        $fields[] = 'remappr.master_lookup_name as remStat';
			$fields[] = 'collections.collection_code';
			$fields[] = 'collections.created_on as collection_date';
			$fields[] = 'collections.collected_amount';
			$fields[] = 'remittance.remittance_code';
			$fields[] = 'remittance.created_at as remittance_date';
			$fields[] = DB::raw("CONCAT(hub_awh_user.firstname, ' ', hub_awh_user.lastname) as hub_appr_by");
			$fields[] = 'hub_awh_history.created_at as hub_appr_date';
			$fields[] = DB::raw("CONCAT(fin_awh_user.firstname, ' ', fin_awh_user.lastname) as fin_appr_by");
			$fields[] = 'fin_awh_history.created_at as fin_appr_date';

			$query->leftJoin('gds_order_track as track', 'track.gds_order_id', '=', 'orders.gds_order_id');
			$query->leftJoin('gds_ship_grid as gdsship', 'gdsship.gds_order_id', '=', 'orders.gds_order_id');
			$query->leftJoin('users as deluser', 'deluser.user_id', '=', 'track.delivered_by');
                        $query->leftJoin('users as pickuser', 'pickuser.user_id', '=', 'track.picker_id');
			$query->leftJoin('gds_invoice_grid as inv', 'inv.gds_order_id', '=', 'orders.gds_order_id');

			$query->leftJoin('collections', 'collections.gds_order_id', '=', 'orders.gds_order_id');
			$query->leftJoin('collection_history', 'collection_history.collection_id', '=', 'collections.collection_id');
            $query->leftJoin('users as collecteduser', 'collecteduser.user_id', '=', 'collection_history.collected_by');
            $query->leftJoin('remittance_mapping as mapping', 'mapping.collection_id', '=', 'collections.collection_id');
            $query->leftJoin('collection_remittance_history as remittance', 'remittance.remittance_id', '=', 'mapping.remittance_id');
            $query->leftJoin('master_lookup as remappr', 'remittance.approval_status', '=', 'remappr.value');
			

			$query->leftJoin('appr_workflow_history as hub_awh_history', function ($join) {
		            $join->on('hub_awh_history.awf_for_id', '=', 'remittance.remittance_id')->on("hub_awh_history.awf_for_type_id", '=', DB::raw(56018))->on("hub_awh_history.status_to_id", '=', DB::raw(57052));
		        	});
            $query->leftJoin('users as hub_awh_user', 'hub_awh_user.user_id', '=', 'hub_awh_history.user_id');

			$query->leftJoin('appr_workflow_history as fin_awh_history', function ($join) {
		            $join->on('fin_awh_history.awf_for_id', '=', 'remittance.remittance_id')->on("fin_awh_history.awf_for_type_id", '=', DB::raw(56018))->on("fin_awh_history.status_to_id", '=', DB::raw(57053));
		        	});
            $query->leftJoin('users as fin_awh_user', 'fin_awh_user.user_id', '=', 'fin_awh_history.user_id');

			$query->leftJoin('gds_orders_payment as payment', 'payment.gds_order_id', '=', 'orders.gds_order_id');

			$query->leftJoin('gds_return_grid as rgrid', 'rgrid.gds_order_id', '=', 'orders.gds_order_id');

			$query->where('payment.payment_status_id',32003);
			$query->whereIn('orders.order_status_id', array('17007','17023'));
		}


		if($filterType == 'nct') {
            
			$fields[] = DB::raw("CONCAT(pickuser.firstname, ' ', pickuser.lastname) as pickedby");
			$fields[] = DB::raw("CONCAT(deluser.firstname, ' ', deluser.lastname) as deliveredby");
			$fields[] = 'track.delivery_date';
			$fields[] = 'inv.created_at as invoice_date';
			$fields[] = 'track.crates_cnt';
			$fields[] = 'inv.grand_total as totInvoiceValue';
			$fields[] = 'inv.invoice_qty as totInvoiceQty';
			$fields[] = 'inv.invoice_code';
			$fields[] = 'ordStatus.master_lookup_name as orderStatus';
			$fields[] = DB::raw('cast(rgrid.total_return_value as decimal(18,2)) as totReturnValue');
			$fields[] = 'rgrid.total_return_item_qty as totReturnQty';
			$fields[] = 'inv.gds_invoice_grid_id';
			$fields[] = 'rgrid.return_grid_id';
			$fields[] = DB::raw("IFNULL(nctmlookup.master_lookup_name, 'Initiated') as nctStatus");


			$query->leftJoin('gds_order_track as track', 'track.gds_order_id', '=', 'orders.gds_order_id');
			$query->leftJoin('users as deluser', 'deluser.user_id', '=', 'track.delivered_by');
                        $query->leftJoin('users as pickuser', 'pickuser.user_id', '=', 'track.picker_id');
			$query->leftJoin('gds_invoice_grid as inv', 'inv.gds_order_id', '=', 'orders.gds_order_id');
			$query->leftJoin('gds_return_grid as rgrid', 'rgrid.gds_order_id', '=', 'orders.gds_order_id');

			$query->leftJoin('collections', 'collections.gds_order_id', '=', 'orders.gds_order_id');
			$query->leftJoin('collection_history', 'collection_history.collection_id', '=', 'collections.collection_id');
			$query->leftJoin('nct_transcation_tracking as nct', 'nct.nct_history_id', '=', 'collection_history.history_id');
			$query->leftJoin('master_lookup as nctmlookup', 'nctmlookup.value', '=', 'nct.nct_status');
			
			//$query->whereIn('orders.order_status_id', array('17007', '17023'));
			$query->whereNotIn('collection_history.payment_mode', array('22010', '22005', '0'));
			
			$query->where(function ($query) {
                    $query->whereNotIn('nct.nct_status', array('11904'));
                    $query->orWhereNull('nct.nct_status');
                });
		}
		

		if($filterType == 'hold') {
                        $fields[] = DB::raw("CONCAT(pickuser.firstname, ' ', pickuser.lastname) as pickedby");
			$fields[] = DB::raw("CONCAT(deluser.firstname, ' ', deluser.lastname) as deliveredby");
			$fields[] = 'inv.created_at as invoice_date';
			$fields[] = 'inv.invoice_code';
			$fields[] = 'inv.grand_total as totInvoiceValue';
			$fields[] = 'track.hold_count';
			$fields[] = 'gdscomment.comment as hold_reason';
			$fields[] = 'track.delivery_date';
			$fields[] = 'inv.gds_invoice_grid_id';

			$query->leftJoin('gds_invoice_grid as inv', 'inv.gds_order_id', '=', 'orders.gds_order_id');
			$query->leftJoin('gds_order_track as track', 'track.gds_order_id', '=', 'orders.gds_order_id');
			$query->leftJoin('users as deluser', 'deluser.user_id', '=', 'track.delivered_by');
			$query->leftJoin('users as pickuser', 'pickuser.user_id', '=', 'track.picker_id');
			$query->leftJoin('gds_orders_comments as gdscomment', function ($join) {
		            $join->on('gdscomment.entity_id', '=', 'orders.gds_order_id')->where("gdscomment.comment_type", '=', '17')
		            ->where("gdscomment.order_status_id", '=', '17014');
	       	 		});

			$query->whereIn('orders.order_status_id', array('17014'));
		}

		if($filterType == 'returnapproval') {
                        $fields[] = DB::raw("CONCAT(pickuser.firstname, ' ', pickuser.lastname) as pickedby");
			$fields[] = DB::raw("CONCAT(deluser.firstname, ' ', deluser.lastname) as deliveredby");
			$fields[] = 'track.picked_date';
            		$fields[] = 'track.delivery_date';
			$fields[] = 'inv.created_at as invoice_date';
			$fields[] = 'track.pick_code';
			$fields[] = 'inv.grand_total as totInvoiceValue';
			$fields[] = 'inv.invoice_qty as totInvoiceQty';			
			$fields[] = 'inv.invoice_code';                        
			$fields[] = 'ordStatus.master_lookup_name as orderStatus';
			$fields[] = DB::raw('cast(rgrid.total_return_value as decimal(18,2)) as totReturnValue');
			$fields[] = 'rgrid.total_return_item_qty as totReturnQty';
			$fields[] = 'inv.gds_invoice_grid_id';
			$fields[] = 'rgrid.return_grid_id';

			$query->leftJoin('gds_order_track as track', 'track.gds_order_id', '=', 'orders.gds_order_id');
			$query->leftJoin('users as deluser', 'deluser.user_id', '=', 'track.delivered_by');
                        $query->leftJoin('users as pickuser', 'pickuser.user_id', '=', 'track.picker_id');
			$query->leftJoin('gds_invoice_grid as inv', 'inv.gds_order_id', '=', 'orders.gds_order_id');
			
			$query->leftJoin('gds_return_grid as rgrid', 'rgrid.gds_order_id', '=', 'orders.gds_order_id');
			$query->join('gds_returns as gdsr','orders.gds_order_id','=','gdsr.gds_order_id');
			$query->whereIn('orders.order_status_id', array('17022','17023'));
			//$query->where('gdsr.return_status_id', array('67002'));
			$query->where('rgrid.return_status_id', array('67002'));
		}
        if($filterType == 'missingquantities'){
        	$fields[] = DB::raw("CONCAT(pickuser.firstname, ' ', pickuser.lastname) as pickedby");
			$fields[] = DB::raw("CONCAT(deluser.firstname, ' ', deluser.lastname) as deliveredby");
			$fields[] = 'track.picked_date';
            $fields[] = 'track.delivery_date';
			$fields[] = 'inv.created_at as invoice_date';
			$fields[] = 'track.pick_code';
			$fields[] = 'inv.grand_total as totInvoiceValue';
			$fields[] = 'inv.invoice_qty as totInvoiceQty';			
			$fields[] = 'inv.invoice_code';                        
			$fields[] = 'ordStatus.master_lookup_name as orderStatus';
			$fields[] = 'rgrid.total_return_value as totReturnValue';
			$fields[] = 'rgrid.total_return_item_qty as totReturnQty';
			$fields[] = 'inv.gds_invoice_grid_id';
			$fields[] = 'rgrid.return_grid_id';
			$fields[] = DB::raw('sum(gdsr.dit_qty) as totDamagedQty');
			$fields[] = DB::raw('sum(gdsr.dit_qty*gdsr.unit_price) as totDamagedValue');
			$fields[] = DB::raw('sum(gdsr.dnd_qty) as totMissingQty');
			$fields[] = DB::raw('sum(gdsr.dnd_qty*gdsr.unit_price) as totMissingValue');
			$fields[] = DB::raw('sum(gdsr.excess_qty) as totExcessQty');
			$fields[] = DB::raw('sum(gdsr.excess_qty*gdsr.unit_price) as totExcessValue');
            $query->leftJoin('gds_order_track as track', 'track.gds_order_id', '=', 'orders.gds_order_id');
			$query->leftJoin('users as deluser', 'deluser.user_id', '=', 'track.delivered_by');
            $query->leftJoin('users as pickuser', 'pickuser.user_id', '=', 'track.picker_id');
			$query->leftJoin('gds_invoice_grid as inv', 'inv.gds_order_id', '=', 'orders.gds_order_id');
			$query->leftJoin('gds_return_grid as rgrid', 'rgrid.gds_order_id', '=', 'orders.gds_order_id');
		    $query->leftJoin('gds_returns as gdsr','orders.gds_order_id','=','gdsr.gds_order_id');

			$query->whereIn('orders.order_status_id', array('17022','17023','17008'));
			$query->having(DB::raw('sum(gdsr.dnd_qty)'),'>',0);
		}       
        if($filterType == 'damagedquantities'){
        	$fields[] = DB::raw("CONCAT(pickuser.firstname, ' ', pickuser.lastname) as pickedby");
			$fields[] = DB::raw("CONCAT(deluser.firstname, ' ', deluser.lastname) as deliveredby");
			$fields[] = 'track.picked_date';
            $fields[] = 'track.delivery_date';
			$fields[] = 'inv.created_at as invoice_date';
			$fields[] = 'track.pick_code';
			$fields[] = 'inv.grand_total as totInvoiceValue';
			$fields[] = 'inv.invoice_qty as totInvoiceQty';			
			$fields[] = 'inv.invoice_code';                        
			$fields[] = 'ordStatus.master_lookup_name as orderStatus';
			$fields[] = 'rgrid.total_return_value as totReturnValue';
			$fields[] = 'rgrid.total_return_item_qty as totReturnQty';
			$fields[] = 'inv.gds_invoice_grid_id';
			$fields[] = 'rgrid.return_grid_id';
			$fields[] = DB::raw('sum(gdsr.dit_qty) as totDamagedQty');
			$fields[] = DB::raw('sum(gdsr.dit_qty*gdsr.unit_price) as totDamagedValue');
			$fields[] = DB::raw('sum(gdsr.dnd_qty) as totMissingQty');
			$fields[] = DB::raw('sum(gdsr.dnd_qty*gdsr.unit_price) as totMissingValue');
			$fields[] = DB::raw('sum(gdsr.excess_qty) as totExcessQty');
			$fields[] = DB::raw('sum(gdsr.excess_qty*gdsr.unit_price) as totExcessValue');
			$query->leftJoin('gds_order_track as track', 'track.gds_order_id', '=', 'orders.gds_order_id');
			$query->leftJoin('users as deluser', 'deluser.user_id', '=', 'track.delivered_by');
            $query->leftJoin('users as pickuser', 'pickuser.user_id', '=', 'track.picker_id');
			$query->leftJoin('gds_invoice_grid as inv', 'inv.gds_order_id', '=', 'orders.gds_order_id');
			$query->leftJoin('gds_return_grid as rgrid', 'rgrid.gds_order_id', '=', 'orders.gds_order_id');
			$query->leftJoin('gds_returns as gdsr','orders.gds_order_id','=','gdsr.gds_order_id');
			$query->whereIn('orders.order_status_id', array('17022','17023','17008'));
			$query->having(DB::raw('sum(gdsr.dit_qty)'),'>',0);
		}
		 if($filterType == 'approvedMissingquantities'){
           $fields[] = DB::raw("CONCAT(pickuser.firstname, ' ', pickuser.lastname) as pickedby");
		   $fields[] = DB::raw("CONCAT(deluser.firstname, ' ', deluser.lastname) as deliveredby");
			$fields[] = 'track.picked_date';
            $fields[] = 'track.delivery_date';
			$fields[] = 'inv.created_at as invoice_date';
			$fields[] = 'track.pick_code';
			$fields[] = 'inv.grand_total as totInvoiceValue';
			$fields[] = 'inv.invoice_qty as totInvoiceQty';			
			$fields[] = 'inv.invoice_code';                        
			$fields[] = 'ordStatus.master_lookup_name as orderStatus';
			$fields[] = 'rgrid.total_return_value as totReturnValue';
			$fields[] = 'rgrid.total_return_item_qty as totReturnQty';
			$fields[] = DB::raw('sum(gdsr.dnd_qty) as totMissingQty');
			$fields[] = DB::raw('sum(gdsr.dnd_qty*gdsr.unit_price) as totMissingValue');
			$fields[] = 'inv.gds_invoice_grid_id';
			$fields[] = 'rgrid.return_grid_id';
			$query->leftJoin('gds_order_track as track', 'track.gds_order_id', '=', 'orders.gds_order_id');
			$query->leftJoin('users as deluser', 'deluser.user_id', '=', 'track.delivered_by');
            $query->leftJoin('users as pickuser', 'pickuser.user_id', '=', 'track.picker_id');
			$query->leftJoin('gds_invoice_grid as inv', 'inv.gds_order_id', '=', 'orders.gds_order_id');
			$query->leftJoin('gds_return_grid as rgrid', 'rgrid.gds_order_id', '=', 'orders.gds_order_id');
			$query->join('gds_returns as gdsr','orders.gds_order_id','=','gdsr.gds_order_id');
			$query->whereIn('orders.order_status_id', array('17022','17023','17008'));
			$query->having(DB::raw('sum(gdsr.dnd_qty)'),'>',0);
		    $query->where('gdsr.return_status_id','=',57066);
		}       
        if($filterType == 'approvedDamagedquantities'){
			$fields[] = DB::raw("CONCAT(pickuser.firstname, ' ', pickuser.lastname) as pickedby");
			$fields[] = DB::raw("CONCAT(deluser.firstname, ' ', deluser.lastname) as deliveredby");
			$fields[] = 'track.picked_date';
            $fields[] = 'track.delivery_date';
			$fields[] = 'inv.created_at as invoice_date';
			$fields[] = 'track.pick_code';
			$fields[] = 'inv.grand_total as totInvoiceValue';
			$fields[] = 'inv.invoice_qty as totInvoiceQty';			
			$fields[] = 'inv.invoice_code';                        
			$fields[] = 'ordStatus.master_lookup_name as orderStatus';
			$fields[] = 'rgrid.total_return_value as totReturnValue';
			$fields[] = 'rgrid.total_return_item_qty as totReturnQty';
		    $fields[] = DB::raw('sum(gdsr.dit_qty) as totDamagedQty');
			$fields[] = DB::raw('sum(gdsr.dit_qty*gdsr.unit_price) as totDamagedValue');
			$fields[] = 'inv.gds_invoice_grid_id';
			$fields[] = 'rgrid.return_grid_id';
			$query->leftJoin('gds_order_track as track', 'track.gds_order_id', '=', 'orders.gds_order_id');
			$query->leftJoin('users as deluser', 'deluser.user_id', '=', 'track.delivered_by');
            $query->leftJoin('users as pickuser', 'pickuser.user_id', '=', 'track.picker_id');
			$query->leftJoin('gds_invoice_grid as inv', 'inv.gds_order_id', '=', 'orders.gds_order_id');
			$query->leftJoin('gds_return_grid as rgrid', 'rgrid.gds_order_id', '=', 'orders.gds_order_id');
   			$query->join('gds_returns as gdsr','orders.gds_order_id','=','gdsr.gds_order_id');
			$query->whereIn('orders.order_status_id', array('17022','17023','17008'));
			$query->having(DB::raw('sum(gdsr.dit_qty)'),'>',0);
			$query->where('gdsr.return_status_id','=',57066);
		}
	    if($filterType == 'shortcollections'){
            $fields = array(
                            'orders.gds_order_id',
                            'orders.platform_id',
                            'orders.order_code',
                            'orders.shop_name', 
                            'orders.order_date', 
                            'orders.total as order_value',
                            'orders.hub_id',
                            'city.officename as areaname',
                            'pjp.pjp_name as beat',
            				'orders.total_items as totSku',
            				'orders.total_item_qty as orderedQty',
            				'orders.order_status_id',
            				'le.le_code',
            				'lw.lp_wh_name',
            				'spokes.spoke_name as spokeName',
                        );
            $fields[] = DB::raw("CONCAT(pickuser.firstname, ' ', pickuser.lastname) as pickedby");
			$fields[] = DB::raw("CONCAT(deluser.firstname, ' ', deluser.lastname) as deliveredby");
			$fields[] = 'track.picked_date';
            $fields[] = 'track.delivery_date';
			$fields[] = 'inv.created_at as invoice_date';
			$fields[] = 'track.pick_code';
			$fields[] = 'inv.grand_total as totInvoiceValue';
			$fields[] = 'inv.invoice_qty as totInvoiceQty';			
			$fields[] = 'inv.invoice_code';                        
			$fields[] = 'ordStatus.master_lookup_name as orderStatus';
			$fields[] = 'rgrid.total_return_value as totReturnValue';
			$fields[] = 'rgrid.total_return_item_qty as totReturnQty';
			$fields[] = 'inv.gds_invoice_grid_id';
			$fields[] = 'rgrid.return_grid_id';
            $fields[]=DB::raw("ROUND(SUM(IFNULL(c.invoice_amount,0)-IFNULL(c.return_total,0)-IFNULL(c.collected_amount,0)-IFNULL(c.discount_amt,0)),2) AS due");		
			$query = DB::table('collections as c');
			$query->join('gds_orders as orders','c.gds_order_id','=','orders.gds_order_id');
			$query->join('collection_history as ch','c.collection_id','=','ch.collection_id');
	        $query->leftJoin('customers as cust', 'cust.le_id', '=', 'orders.cust_le_id');
	       	$query->leftJoin('cities_pincodes as city', 'city.city_id', '=', 'cust.area_id');
			$query->leftJoin('pjp_pincode_area as pjp', 'pjp.pjp_pincode_area_id', '=', 'orders.beat');
			$query->leftJoin('spokes','pjp.spoke_id','=','spokes.spoke_id');
		    $query->leftJoin('master_lookup as ordStatus',function ($join) {
			$join->on('ordStatus.value','=','orders.order_status_id')->where("ordStatus.mas_cat_id",'=','17');		   
		     });   	
			$query->leftJoin('legal_entities as le', 'le.legal_entity_id', '=', 'orders.cust_le_id');
			$query->leftJoin('legalentity_warehouses as lw', 'lw.le_wh_id', '=', 'orders.hub_id');
			$query->leftjoin('gds_order_track as track','track.gds_order_id','=','orders.gds_order_id');
			$query->leftjoin('users as deluser','deluser.user_id','=','track.delivered_by');
			$query->leftjoin('users as pickuser','pickuser.user_id','=','track.picker_id');
			$query->leftjoin('gds_invoice_grid as inv','inv.gds_order_id','=','orders.gds_order_id');
			$query->leftjoin('gds_return_grid as rgrid','rgrid.gds_order_id','=', 'orders.gds_order_id'); 
			//$query->where('c.gds_order_id','=','orders.gds_order_id');
			//$query->where('c.collection_id','=','ch.collection_id');
			$query->having(DB::raw("ROUND(SUM(IFNULL(c.invoice_amount,0)-IFNULL(c.return_total,0)-IFNULL(c.collected_amount,0)-IFNULL(c.discount_amt,0)),2)"),'>=',1);
		}
		if($filterType == 'rah') {
            $fields[] = DB::raw("CONCAT(pickuser.firstname, ' ', pickuser.lastname) as pickedby");
			$fields[] = DB::raw("CONCAT(deluser.firstname, ' ', deluser.lastname) as deliveredby");
			$fields[] = 'track.picked_date';
    		$fields[] = 'track.delivery_date';
			$fields[] = 'inv.created_at as invoice_date';
			$fields[] = 'track.pick_code';
			$fields[] = 'inv.grand_total as totInvoiceValue';
			$fields[] = 'inv.invoice_qty as totInvoiceQty';
			$fields[] = 'inv.invoice_code';                        
			$fields[] = 'ordStatus.master_lookup_name as orderStatus';
			
			$fields[] = DB::raw('cast(rgrid.total_return_value as decimal(18,2)) as totReturnValue');
			$fields[] = 'rgrid.total_return_item_qty as totReturnQty';
			$fields[] = 'gdsr.dit_qty as totDamagedValue';
			$fields[] = 'gdsr.dnd_qty as totMissingValue';
			$fields[] = 'gdsr.excess_qty as totExcessValue';

			$fields[] = 'inv.gds_invoice_grid_id';
			$fields[] = 'rgrid.return_grid_id';

			$query->leftJoin('gds_order_track as track', 'track.gds_order_id', '=', 'orders.gds_order_id');
			$query->leftJoin('users as deluser', 'deluser.user_id', '=', 'track.delivered_by');
                        $query->leftJoin('users as pickuser', 'pickuser.user_id', '=', 'track.picker_id');
			$query->leftJoin('gds_invoice_grid as inv', 'inv.gds_order_id', '=', 'orders.gds_order_id');
			$query->leftJoin('gds_return_grid as rgrid', 'rgrid.gds_order_id', '=', 'orders.gds_order_id');

			$query->leftJoin('gds_returns as gdsr','orders.gds_order_id','=','gdsr.gds_order_id');
			$query->whereIn('orders.order_status_id', array('17022','17023'));
			//$query->where('gdsr.return_status_id', '57067');
			$query->where('rgrid.return_status_id', '57067');
			$query->whereNull('orders.order_transit_status');
		}

        if($filterType == 'stocktransitdc') {
            $fields[] = DB::raw("CONCAT(pickuser.firstname, ' ', pickuser.lastname) as pickedby");
			$fields[] = DB::raw("CONCAT(deluser.firstname, ' ', deluser.lastname) as deliveredby");
			$fields[] = 'track.cfc_cnt';
			$fields[] = 'track.bags_cnt';
			$fields[] = 'track.crates_cnt';
			$fields[] = 'inv.grand_total as totInvoiceValue';
			$fields[] = 'inv.invoice_qty as totInvoiceQty';
			$fields[] = 'inv.invoice_code';
			$fields[] = 'inv.created_at as invoice_date';
			$fields[] = 'inv.gds_invoice_grid_id';
			$fields[] = 'track.delivery_date';
			$fields[] = DB::raw("CONCAT(stdeluser.firstname, ' ', stdeluser.lastname) as st_de_name");
			$fields[] = 'track.st_del_date';
			$fields[] = 'track.st_vehicle_no';
			$fields[] = 'track.st_driver_name';
			$fields[] = 'track.st_driver_mobile';
			$fields[] = 'track.st_docket_no';
			$fields[] = DB::raw("CONCAT(rtdeluser.firstname, ' ', rtdeluser.lastname) as rt_de_name");
			$fields[] = 'track.rt_del_date';
			$fields[] = 'track.rt_vehicle_no';
			$fields[] = 'track.rt_driver_name';
			$fields[] = 'track.rt_driver_mobile';
			$fields[] = 'track.rt_docket_no';
			$fields[] = 'rgrid.return_grid_id';
			$fields[] = DB::raw('cast(rgrid.total_return_value as decimal(18,2)) as totReturnValue');
			$fields[] = 'ordStatus.master_lookup_name as orderStatus';
			
			$fields[] = 'gdsr.dit_qty as totDamagedValue';
			$fields[] = 'gdsr.dnd_qty as totMissingValue';
			$fields[] = 'gdsr.excess_qty as totExcessValue';

			
			$query->leftJoin('gds_order_track as track', 'track.gds_order_id', '=', 'orders.gds_order_id');
			$query->leftJoin('users as deluser', 'deluser.user_id', '=', 'track.delivered_by');
                        $query->leftJoin('users as pickuser', 'pickuser.user_id', '=', 'track.picker_id');
			$query->leftJoin('users as stdeluser', 'stdeluser.user_id', '=', 'track.st_del_ex_id');
			$query->leftJoin('users as rtdeluser', 'rtdeluser.user_id', '=', 'track.rt_del_ex_id');
			$query->leftJoin('gds_invoice_grid as inv', 'inv.gds_order_id', '=', 'orders.gds_order_id');
            
            $query->leftJoin('gds_return_grid as rgrid', 'rgrid.gds_order_id', '=', 'orders.gds_order_id');  
            $query->leftJoin('gds_returns as gdsr','orders.gds_order_id','=','gdsr.gds_order_id');

			$query->whereIn('orders.order_status_id', array('17022','17023'));	
			$query->where('orders.order_transit_status', '17027');	
		}

		if($filterType == 'returnst') {

                        $fields[] = DB::raw("CONCAT(pickuser.firstname, ' ', pickuser.lastname) as pickedby");
			$fields[] = DB::raw("CONCAT(deluser.firstname, ' ', deluser.lastname) as deliveredby");
			$fields[] = 'track.picked_date';
            		$fields[] = 'track.delivery_date';
			$fields[] = 'inv.created_at as invoice_date';
			$fields[] = 'track.pick_code';
			$fields[] = 'inv.grand_total as totInvoiceValue';
			$fields[] = 'inv.invoice_qty as totInvoiceQty';
			$fields[] = 'inv.invoice_code';                        
			$fields[] = 'ordStatus.master_lookup_name as orderStatus';
			
			$fields[] = DB::raw('cast(rgrid.total_return_value as decimal(18,2)) as totReturnValue');
			$fields[] = 'rgrid.total_return_item_qty as totReturnQty';

			$fields[] = 'inv.gds_invoice_grid_id';
			$fields[] = 'rgrid.return_grid_id';

			$query->leftJoin('gds_order_track as track', 'track.gds_order_id', '=', 'orders.gds_order_id');
			$query->leftJoin('users as deluser', 'deluser.user_id', '=', 'track.delivered_by');
                        $query->leftJoin('users as pickuser', 'pickuser.user_id', '=', 'track.picker_id');
			$query->leftJoin('gds_invoice_grid as inv', 'inv.gds_order_id', '=', 'orders.gds_order_id');
			
			$query->leftJoin('gds_return_grid as rgrid', 'rgrid.gds_order_id', '=', 'orders.gds_order_id');
			//$query->join('gds_returns as gdsr','orders.gds_order_id','=','gdsr.gds_order_id');
			$query->whereIn('orders.order_status_id', array('17022','17023'));
			//$query->where('gdsr.return_status_id', array('67002'));
			$query->where('rgrid.return_status_id', array('67002'));
		}
             
		if($filterType == 'return') {
                        $fields[] = DB::raw("CONCAT(pickuser.firstname, ' ', pickuser.lastname) as pickedby");
			$fields[] = DB::raw("CONCAT(deluser.firstname, ' ', deluser.lastname) as deliveredby");
			$fields[] = 'track.picked_date';
            		$fields[] = 'track.delivery_date';
			$fields[] = 'inv.created_at as invoice_date';
			$fields[] = 'track.pick_code';
			$fields[] = 'inv.grand_total as totInvoiceValue';
			//$fields[] = DB::raw("SUM(invitem.qty) as totInvoiceQty");
			$fields[] = 'inv.invoice_qty as totInvoiceQty';
			$fields[] = 'inv.invoice_code';                        
			$fields[] = 'ordStatus.master_lookup_name as orderStatus';

			$fields[] = DB::raw('cast(rgrid.total_return_value as decimal(18,2)) as totReturnValue');
			$fields[] = 'rgrid.total_return_item_qty as totReturnQty';

			$fields[] = 'inv.gds_invoice_grid_id';
			$fields[] = 'rgrid.return_grid_id';
			$fields[] = 'rgrid.created_at as returnDate';

			$query->leftJoin('gds_order_track as track', 'track.gds_order_id', '=', 'orders.gds_order_id');
			$query->leftJoin('users as deluser', 'deluser.user_id', '=', 'track.delivered_by');
                        $query->leftJoin('users as pickuser', 'pickuser.user_id', '=', 'track.picker_id');
			$query->leftJoin('gds_invoice_grid as inv', 'inv.gds_order_id', '=', 'orders.gds_order_id');
			
			$query->leftJoin('gds_return_grid as rgrid', 'rgrid.gds_order_id', '=', 'orders.gds_order_id');
			//$query->join('gds_returns as gdsr','orders.gds_order_id','=','gdsr.gds_order_id');
			$query->whereIn('orders.order_status_id', array('17022'));
			//$query->where('gdsr.approval_status', 1);
			$query->where('rgrid.return_status_id', 57066);
		}
		
		if($salestype==1){
			//for intermediate sales legal_entity_type_id in 1016
			$query->whereIn('orders.is_primary_sale',[1,3]);
		}elseif($salestype==2){
			
			$query->whereIn('orders.is_primary_sale',[0]);
		}

		// code end for tabs

		// iggrid filter code start from here
		//echo '<pre>';print_r($filter);die;
		if(isset($filter['Area']) && !empty($filter['Area']['value'])) {
			$query->where('city.officename', 'LIKE', '%'.$filter['Area']['value'].'%');
		}
		if(isset($filter['remStat'])) {
			$query->where('remappr.master_lookup_name', 'LIKE', '%'.$filter['remStat']['value'].'%');
		}

		if(isset($filter['custRating']) && !empty($filter['custRating'])) {
			$query->where(DB::raw('getRetailerRatingName(le.legal_entity_id) '), 'LIKE', '%'.$filter['custRating']['value'].'%');
		}


		if(isset($filter['beat']) && !empty($filter['beat']['value'])) {
			$query->where('pjp.pjp_name', 'LIKE', '%'.$filter['beat']['value'].'%');
		}

		if(isset($filter['Customer'])) {
			$query->where('orders.shop_name', 'LIKE', '%'.$filter['Customer']['value'].'%');
		}

		if(isset($filter['Hub'])) {
			$query->where('lw.lp_wh_name', 'LIKE', '%'.$filter['Hub']['value'].'%');
		}

		if(isset($filter['User'])) {
			$query->where(DB::raw("CONCAT(users.firstname, ' ', users.lastname)"), 'LIKE', '%'.$filter['User']['value'].'%');
		}

		if(isset($filter['del_name'])) {
			$query->where(DB::raw("GetUserName(track.delivered_by, 2)"), 'LIKE', '%'.$filter['del_name']['value'].'%');
		}
        if(isset($filter['picker'])) {
			$query->where(DB::raw("CONCAT(pickuser.firstname, ' ', pickuser.lastname)"), 'LIKE', '%'.$filter['picker']['value'].'%');
		}
		if(isset($filter['canReason'])) {
			$query->where('clookup.master_lookup_name', 'LIKE', '%'.$filter['canReason']['value'].'%');
		}

		if(isset($filter['OrderID'])) {
			$orderIds = explode(',',trim($filter['OrderID']['value'],','));
			if(is_array($orderIds) && count($orderIds)>0) {

					$query->where(function($query) use ($orderIds)
					 {
					    foreach ($orderIds as $orderId) {
					       $query->orWhere('orders.order_code', 'LIKE', '%'.trim($orderId).'%' );
					    }
					 });
			}	
		}
	   if(!empty(Session::get('business_unitid')) && Session::get('business_unitid')!=0){
			$bu_id=Session::get('business_unitid');
			$userID = Session('userId');
			$roleRepo = new RoleRepo();
            $globalAccess = $roleRepo->checkPermissionByFeatureCode("GLBWH0001",$userID);
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
		if(isset($filter['gds_order_id'])) {
			$filter['gds_order_id'] = explode(',',$filter['gds_order_id']);
			$query->whereIn("orders.gds_order_id" , $filter['gds_order_id']);
		}

		if(isset($filter['SDS1'])) {
			$query->where("ordSlot1.master_lookup_name", 'LIKE', '%'.$filter['SDS1']['value'].'%');
		}

		if(isset($filter['SDS2'])) {
			$query->where("ordSlot2.master_lookup_name", 'LIKE', '%'.$filter['SDS2']['value'].'%');
		}

		if(isset($filter['ChannelName']) && !empty($filter['ChannelName'])) {
			$query->where(DB::raw('LOWER(mlookup.master_lookup_name)'), 'LIKE', '%'.$filter['ChannelName']['value'].'%');
		}

		if(isset($filter['Status'])) {
			$query->where(DB::raw('LOWER(ordStatus.master_lookup_name)'), 'LIKE', '%'.$filter['Status']['value'].'%');
		}

		if(isset($filter['nctTracker'])) {
			$query->where(DB::raw("IFNULL(nctmlookup.master_lookup_name, 'Initiated')"), 'LIKE', '%'.$filter['nctTracker']['value'].'%');
		}

		if(isset($filter['custcode'])) {
			$query->where("le.le_code", 'LIKE', '%'.$filter['custcode']['value'].'%');
		}                                
		if(isset($filter['ReturnValue'])){
			$query->where(DB::raw("CAST(rgrid.total_return_value as decimal(18,2))"), $filter['ReturnValue']['operator'], $filter['ReturnValue']['value']);
		} 

        if(isset($filter['DamagedValue'])){
			$query->having(DB::raw("CAST(sum(gdsr.dit_qty*gdsr.unit_price) as decimal(5,2))"),$filter['DamagedValue']['operator'],$filter['DamagedValue']['value']);
		}  

		
		if(isset($filter['MissingValue'])){
			$query->having(DB::raw("CAST(sum(gdsr.dnd_qty*gdsr.unit_price) as decimal(5,2))"),$filter['MissingValue']['operator'],$filter['MissingValue']['value']);
		}
		
		 if(isset($filter['DamagedQty'])){
			$query->having( DB::raw('sum(gdsr.dit_qty)'),$filter['DamagedQty']['operator'],$filter['DamagedQty']['value']);
		}  

		if(isset($filter['MissingQty'])){
			$query->having(DB::raw('sum(gdsr.dnd_qty)'),$filter['MissingQty']['operator'],$filter['MissingQty']['value']);
		}                   
         
		if(isset($filter['ExcessQty'])){
			$query->having(DB::raw('sum(gdsr.excess_qty)'),$filter['ExcessQty']['operator'],$filter['ExcessQty']['value']);
		}
		if(isset($filter['ExcessValue'])){
			$query->having(DB::raw("CAST(sum(gdsr.excess_qty*gdsr.unit_price) as decimal(5,2))"),$filter['ExcessValue']['operator'],$filter['ExcessValue']['value']);
		}
		               	
        if(isset ($filter['orderedQty'])) {
            $query->where('orders.total_item_qty', $filter['orderedQty']['operator'], $filter['orderedQty']['value']);                    
        }                
        if(isset ($filter['ReturnQty'])) {
        	$query->where('rgrid.total_return_item_qty', $filter['ReturnQty']['operator'], $filter['ReturnQty']['value']);                    
        }
        if(isset ($filter['InvoiceQty'])) {
            $query->where('inv.invoice_qty', $filter['InvoiceQty']['operator'], $filter['InvoiceQty']['value']);                    
        }

        if(isset ($filter['hold_count'])) {
            $query->where('track.hold_count', $filter['hold_count']['operator'], $filter['hold_count']['value']);                    
        }

        if(isset ($filter['FillRate'])) {
            $query->where(DB::raw("ROUND(IFNULL(((invgrid.invoice_qty*100)/orders.total_item_qty),0))"), $filter['FillRate']['operator'], $filter['FillRate']['value']);                    
        }

        if(isset ($filter['DisFRate'])) {
            $query->where(DB::raw("ROUND(IFNULL(((gdsship.ship_qty*100) /orders.total_item_qty),0))"), $filter['DisFRate']['operator'], $filter['DisFRate']['value']);                    
        }
        
        if(isset ($filter['skuCount'])) {                    
            $query->where('orders.total_items', $filter['skuCount']['operator'], $filter['skuCount']['value']);                    
        }
        if(isset ($filter['pickedby'])) {                       
            $query->where(DB::raw("CONCAT(pickuser.firstname, ' ', pickuser.lastname)"), 'LIKE', '%'.$filter['pickedby']['value'].'%');                    
        }

        if(isset ($filter['verifiedby'])) {
            $query->where(DB::raw("CONCAT(verifieduser.firstname, ' ', verifieduser.lastname)"), 'LIKE', '%'.$filter['verifiedby']['value'].'%');                    
        }

		if(isset($filter['OrderValue'])) {
		$query->where(DB::raw('ROUND(orders.total, 2)'), $filter['OrderValue']['operator'], $filter['OrderValue']['value']);
		}

        if(isset($filter['CancelledValue'])) {
			$query->having(DB::raw('ROUND(sum(canitem.total_price), 2)'), $filter['CancelledValue']['operator'], $filter['CancelledValue']['value']);
		}
		if(isset($filter['CancelledQty'])) {
			$query->having(DB::raw('sum(canitem.qty)'), $filter['CancelledQty']['operator'], $filter['CancelledQty']['value']);
		}
		if(isset($filter['cartons'])) {
			$query->where('track.cfc_cnt', 'LIKE', '%' .$filter['cartons']['value'] .'%');
		}
              
       	if(isset($filter['invoice_code']) && !empty($filter['invoice_code']['value'])) {
       		// print_r($filter);die();
		   $query->where('inv.invoice_code', 'LIKE', '%'.$filter['invoice_code']['value'].'%');
		}

	  	
                
		if(isset($filter['bags'])) {
			$query->where('track.bags_cnt', 'LIKE', '%' .$filter['bags']['value'] .'%');
		}
		if(isset($filter['crates'])) {
			$query->where('track.crates_cnt', 'LIKE', '%' .$filter['crates']['value'] .'%');
		}

		if(isset($filter['spoke'])) {
			$query->where('spokes.spoke_name', 'LIKE', '%'.$filter['spoke']['value'].'%');
		}        
		if(isset($filter['pickno'])) {
			$plIds = explode(',',trim($filter['pickno']['value'],','));
			if(is_array($plIds) && count($plIds)>0) {

					$query->where(function($query) use ($plIds)
					 {
					    foreach ($plIds as $plId) {
					       $query->orWhere('track.pick_code', 'LIKE', '%'.trim($plId).'%' );
					    }
					 });
			}	
		}
		if(isset($filter['hold_reason'])) {
			$query->where('gdscomment.comment', 'LIKE', '%' .$filter['hold_reason']['value'] .'%');
		}		

		if(isset($filter['InvoiceValue'])) {
			$query->where(DB::raw('ROUND(inv.grand_total, 2)'), $filter['InvoiceValue']['operator'], $filter['InvoiceValue']['value']);
		}


		if(isset($filter['st_de_name'])) {

			$query->where(DB::raw("CONCAT(stdeluser.firstname, ' ', stdeluser.lastname)"), 'LIKE', '%' .$filter['st_de_name']['value'] .'%');
		}

		if(isset($filter['st_re_name'])) {

			$query->where(DB::raw("CONCAT(strecuser.firstname, ' ', strecuser.lastname)"), 'LIKE', '%' .$filter['st_re_name']['value'] .'%');
		}


		if(isset($filter['rt_de_name'])) {
                    $query->where(DB::raw("CONCAT(rtdeluser.firstname, ' ', rtdeluser.lastname)"), 'LIKE', '%' .$filter['rt_de_name']['value'] .'%');
		}

		if(isset($filter['rt_re_name'])) {
                    $query->where(DB::raw("CONCAT(rtrecuser.firstname, ' ', rtrecuser.lastname)"), 'LIKE', '%' .$filter['rt_re_name']['value'] .'%');
		}
                
                if(isset($filter['rt_del_date']) && is_array($filter['rt_del_date']) && count($filter['rt_del_date']) > 0) {
                    $fdate = $filter['rt_del_date'][2].'-'.$filter['rt_del_date'][1].'-'.$filter['rt_del_date'][0];
                    $operator = $filter['rt_del_date']['operator'];
                    if($operator == '=') {
                        $query->whereBetween('track.rt_del_date', [$fdate.' 00:00:00', $fdate.' 23:59:59']);
                    } else if($operator == '<') {
                        $query->where('track.rt_del_date', $operator, $fdate.' 00:00:00');
                    } else if($operator == '<=') {
                        $query->where('track.rt_del_date', $operator, $fdate.' 23:59:59');
                    } else {
                        $query->where('track.rt_del_date', $operator, $fdate.' 00:00:00');
                    }
		}

		if(isset($filter['rt_received_at']) && is_array($filter['rt_received_at']) && count($filter['rt_received_at']) > 0) {
                    $fdate = $filter['rt_received_at'][2].'-'.$filter['rt_received_at'][1].'-'.$filter['rt_received_at'][0];
                    $operator = $filter['rt_received_at']['operator'];
                    if($operator == '=') {
                        $query->whereBetween('track.rt_received_at', [$fdate.' 00:00:00', $fdate.' 23:59:59']);
                    } else if($operator == '<') {
                        $query->where('track.rt_received_at', $operator, $fdate.' 00:00:00');
                    } else if($operator == '<=') {
                        $query->where('track.rt_received_at', $operator, $fdate.' 23:59:59');
                    } else {
                        $query->where('track.rt_received_at', $operator, $fdate.' 00:00:00');
                    }
		}
                
		if(isset($filter['rt_vehicle_no'])) {
                    $query->where('track.rt_vehicle_no', 'LIKE', '%' .$filter['rt_vehicle_no']['value'] .'%');
		}
		if(isset($filter['rt_driver_name'])) {
                    $query->where('track.rt_driver_name', 'LIKE', '%' .$filter['rt_driver_name']['value'] .'%');
		}		

		if(isset($filter['rt_driver_mobile'])) {
                    $query->where('track.rt_driver_mobile', 'LIKE', '%' .$filter['rt_driver_mobile']['value'] .'%');
		}

		if(isset($filter['rt_docket_no'])) {
                    $query->where('track.rt_docket_no', 'LIKE', '%' .$filter['rt_docket_no']['value'] .'%');
		}

		if(isset($filter['st_del_date']) && is_array($filter['st_del_date']) && count($filter['st_del_date']) > 0) {
    		$fdate = $filter['st_del_date'][2].'-'.$filter['st_del_date'][1].'-'.$filter['st_del_date'][0];
    		$operator = $filter['st_del_date']['operator'];
    		if($operator == '=') {
    			$query->whereBetween('track.st_del_date', [$fdate.' 00:00:00', $fdate.' 23:59:59']);
    		}
    		else if($operator == '<') {
    			$query->where('track.st_del_date', $operator, $fdate.' 00:00:00');
    		}
    		else if($operator == '<=') {
    			$query->where('track.st_del_date', $operator, $fdate.' 23:59:59');
    		}
    		else {
    			$query->where('track.st_del_date', $operator, $fdate.' 00:00:00');
    		}
		}

		if(isset($filter['st_received_at']) && is_array($filter['st_received_at']) && count($filter['st_received_at']) > 0) {
    		$fdate = $filter['st_received_at'][2].'-'.$filter['st_received_at'][1].'-'.$filter['st_received_at'][0];
    		$operator = $filter['st_received_at']['operator'];
    		if($operator == '=') {
    			$query->whereBetween('track.st_received_at', [$fdate.' 00:00:00', $fdate.' 23:59:59']);
    		}
    		else if($operator == '<') {
    			$query->where('track.st_received_at', $operator, $fdate.' 00:00:00');
    		}
    		else if($operator == '<=') {
    			$query->where('track.st_received_at', $operator, $fdate.' 23:59:59');
    		}
    		else {
    			$query->where('track.st_received_at', $operator, $fdate.' 00:00:00');
    		}
		}

		if(isset($filter['ReturnDate']) && is_array($filter['ReturnDate']) && count($filter['ReturnDate']) > 0) {
    		$fdate = $filter['ReturnDate'][2].'-'.$filter['ReturnDate'][1].'-'.$filter['ReturnDate'][0];
    		$operator = $filter['ReturnDate']['operator'];
    		if($operator == '=') {
    			$query->whereBetween('rgrid.created_at', [$fdate.' 00:00:00', $fdate.' 23:59:59']);
    		}
    		else if($operator == '<') {
    			$query->where('rgrid.created_at', $operator, $fdate.' 00:00:00');
    		}
    		else if($operator == '<=') {
    			$query->where('rgrid.created_at', $operator, $fdate.' 23:59:59');
    		}
    		else {
    			$query->where('rgrid.created_at', $operator, $fdate.' 00:00:00');
    		}
		}

		if(isset($filter['st_vehicle_no'])) {
			$query->where('track.st_vehicle_no', 'LIKE', '%' .$filter['st_vehicle_no']['value'] .'%');
		}
		if(isset($filter['st_driver_name'])) {
			$query->where('track.st_driver_name', 'LIKE', '%' .$filter['st_driver_name']['value'] .'%');
		}		

		if(isset($filter['st_driver_mobile'])) {
			$query->where('track.st_driver_mobile', 'LIKE', '%' .$filter['st_driver_mobile']['value'] .'%');
		}

		if(isset($filter['st_docket_no'])) {
			$query->where('track.st_docket_no', 'LIKE', '%' .$filter['st_docket_no']['value'] .'%');
		}



		if(isset($filter['OrderDate']) && is_array($filter['OrderDate']) && count($filter['OrderDate']) > 0) {
    		$fdate = $filter['OrderDate'][2].'-'.$filter['OrderDate'][1].'-'.$filter['OrderDate'][0];
    		$operator = $filter['OrderDate']['operator'];

    		if($operator == '=') {
    			$query->whereBetween('orders.order_date', [$fdate.' 00:00:00', $fdate.' 23:59:59']);
    		}
    		else if($operator == '<') {
    			$query->where('orders.order_date', $operator, $fdate.' 00:00:0');
    		}
    		else if($operator == '<=') {
    			$query->where('orders.order_date', $operator, $fdate.' 23:59:59');
    		}
    		else {
    			$query->where('orders.order_date', $operator, $fdate.' 00:00:00');
    		}
		}
                
		if(isset($filter['shipmentDate']) && is_array($filter['shipmentDate']) && count($filter['shipmentDate']) > 0) {
    		$fdate = $filter['shipmentDate'][2].'-'.$filter['shipmentDate'][1].'-'.$filter['shipmentDate'][0];
    		$operator = $filter['shipmentDate']['operator'];

    		if($operator == '=') {
    			$query->whereBetween('gdsship.created_at', [$fdate.' 00:00:00', $fdate.' 23:59:59']);
    		}
    		else if($operator == '<') {
    			$query->where('gdsship.created_at', $operator, $fdate.' 00:00:00');
    		}
    		else if($operator == '<=') {
    			$query->where('gdsship.created_at', $operator, $fdate.' 23:59:59');
    		}
    		else {
    			$query->where('gdsship.created_at', $operator, $fdate.' 00:00:00');
    		}
		}   
                
                
		if(isset($filter['pickerdate']) && is_array($filter['pickerdate']) && count($filter['pickerdate']) > 0) {
    		$fdate = $filter['pickerdate'][2].'-'.$filter['pickerdate'][1].'-'.$filter['pickerdate'][0];
    		$operator = $filter['pickerdate']['operator'];

    		if($operator == '=') {
    			$query->whereBetween('track.scheduled_piceker_date', [$fdate.' 00:00:00', $fdate.' 23:59:59']);
    		}
    		else if($operator == '<') {
    			$query->where('track.scheduled_piceker_date', $operator, $fdate.' 00:00:00');
    		}
    		else if($operator == '<=') {
    			$query->where('track.scheduled_piceker_date', $operator, $fdate.' 23:59:59');
    		}
    		else {
    			$query->where('track.scheduled_piceker_date', $operator, $fdate.' 00:00:00');
    		}
		}

		if(isset($filter['InvoiceDate']) && is_array($filter['InvoiceDate']) && count($filter['InvoiceDate']) > 0) {
    		$fdate = $filter['InvoiceDate'][2].'-'.$filter['InvoiceDate'][1].'-'.$filter['InvoiceDate'][0];
    		$operator = $filter['InvoiceDate']['operator'];
    		if($operator == '=') {
    			$query->whereBetween('inv.created_at', [$fdate.' 00:00:00', $fdate.' 23:59:59']);
    		}
    		else if($operator == '<') {
    			$query->where('inv.created_at', $operator, $fdate.' 00:00:00');
    		}
    		else if($operator == '<=') {
    			$query->where('inv.created_at', $operator, $fdate.' 23:59:59');
    		}
    		else {
    			$query->where('inv.created_at', $operator, $fdate.' 00:00:00');
    		}
		}
		if(isset($filter['pickedDate']) && is_array($filter['pickedDate']) && count($filter['pickedDate']) > 0) {
    		$fdate = $filter['pickedDate'][2].'-'.$filter['pickedDate'][1].'-'.$filter['pickedDate'][0];
    		$operator = $filter['pickedDate']['operator'];
    		if($operator == '=') {
    			$query->whereBetween('track.scheduled_piceker_date', [$fdate.' 00:00:00', $fdate.' 23:59:59']);
    		}
    		else if($operator == '<') {
    			$query->where('track.scheduled_piceker_date', $operator, $fdate.' 00:00:00');
    		}
    		else if($operator == '<=') {
    			$query->where('track.scheduled_piceker_date', $operator, $fdate.' 23:59:59');
    		}
    		else {
    			$query->where('track.scheduled_piceker_date', $operator, $fdate.' 00:00:00');
    		}
		}

		if(isset($filter['nextschdate']) && is_array($filter['nextschdate']) && count($filter['nextschdate']) > 0) {
    		$fdate = $filter['nextschdate'][2].'-'.$filter['nextschdate'][1].'-'.$filter['nextschdate'][0];
    		$operator = $filter['nextschdate']['operator'];
    		if($operator == '=') {
    			$query->whereBetween('track.delivery_date', [$fdate.' 00:00:00', $fdate.' 23:59:59']);
    		}
    		else if($operator == '<') {
    			$query->where('track.delivery_date', $operator, $fdate.' 00:00:00');
    		}
    		else if($operator == '<=') {
    			$query->where('track.delivery_date', $operator, $fdate.' 23:59:59');
    		}
    		else {
    			$query->where('track.delivery_date', $operator, $fdate.' 00:00:00');
    		}
		}
                
		if(isset($filter['ADT']) && is_array($filter['ADT']) && count($filter['ADT']) > 0) {
    		$fdate = $filter['ADT'][2].'-'.$filter['ADT'][1].'-'.$filter['ADT'][0];
    		$operator = $filter['ADT']['operator'];
    		if($operator == '=') {
    			$query->whereBetween('track.delivery_date', [$fdate.' 00:00:00', $fdate.' 23:59:59']);
    		}
    		else if($operator == '<') {
    			$query->where('track.delivery_date', $operator, $fdate.' 00:00:00');
    		}
    		else if($operator == '<=') {
    			$query->where('track.delivery_date', $operator, $fdate.' 23:59:59');
    		}
    		else {
    			$query->where('track.delivery_date', $operator, $fdate.' 00:00:00');
    		}
		}

		if(isset($filter['SDT']) && is_array($filter['SDT']) && count($filter['SDT']) > 0) {
    		$fdate = $filter['SDT'][2].'-'.$filter['SDT'][1].'-'.$filter['SDT'][0];
    		$operator = $filter['SDT']['operator'];
    		if($operator == '=') {
    			$query->whereBetween('orders.scheduled_delivery_date', [$fdate.' 00:00:00', $fdate.' 23:59:59']);
    		}
    		else if($operator == '<') {
    			$query->where('orders.scheduled_delivery_date', $operator, $fdate.' 00:00:00');
    		}
    		else if($operator == '<=') {
    			$query->where('orders.scheduled_delivery_date', $operator, $fdate.' 23:59:59');
    		}
    		else {
    			$query->where('orders.scheduled_delivery_date', $operator, $fdate.' 00:00:00');
    		}
		}
		if(isset($le_wh_ids)){
			$le_wh_ids=explode(',', $le_wh_ids);
	    }else{
			$le_wh_ids=array();
	    }
		if(Session::get('business_unitid')!=0 && !empty(Session::get('business_unitid')) && is_array($le_wh_ids) && count($le_wh_ids)>0){
            $query->whereIn("orders.le_wh_id" , $le_wh_ids);
		}else if(isset($filter['Dcs_Assigned']) && !empty($filter['Dcs_Assigned'])) {
			$Dcs_Assigned = explode(',',$filter['Dcs_Assigned']);
			$query->whereIn("orders.le_wh_id" , $Dcs_Assigned);
		}else{
			$query->whereRaw("orders.le_wh_id IN (0)");
		}	
		if(Session::get('business_unitid')!=0 && !empty(Session::get('business_unitid')) && !empty($hubdata)){
			$hubdata=explode(',', $hubdata);
			$query->whereIn("orders.hub_id", $hubdata);
		}else if(isset($filter['Hubs_Assigned']) && !empty($filter['Hubs_Assigned'])) {
			$Hubs_Assigned = explode(',',$filter['Hubs_Assigned']);
			$query->whereIn("orders.hub_id", $Hubs_Assigned);
		}
		if(isset($filter['collection_code'])) {
			$query->where('collections.collection_code', 'LIKE', '%' .$filter['collection_code']['value'] .'%');
		}
		if(isset($filter['collection_date']) && is_array($filter['collection_date']) && count($filter['collection_date']) > 0) {
    		$fdate = $filter['collection_date'][2].'-'.$filter['collection_date'][1].'-'.$filter['collection_date'][0];
    		$operator = $filter['collection_date']['operator'];
    		if($operator == '=') {
    			$query->whereBetween('collections.created_on', [$fdate.' 00:00:00', $fdate.' 23:59:59']);
    		}
    		else if($operator == '<') {
    			$query->where('collections.created_on', $operator, $fdate.' 00:00:00');
    		}
    		else if($operator == '<=') {
    			$query->where('collections.created_on', $operator, $fdate.' 23:59:59');
    		}
    		else {
    			$query->where('collections.created_on', $operator, $fdate.' 00:00:00');
    		}
		}

		if(isset($filter['collected_amount'])) {
			$query->where('collections.collected_amount', $filter['collected_amount']['operator'], $filter['collected_amount']['value']);
		}

        if(isset($filter['collected_by'])) {
			$query->where(DB::raw("CONCAT(collecteduser.firstname, ' ', collecteduser.lastname)"), 'LIKE', '%'.$filter['collected_by']['value'].'%');
		}

		if(isset($filter['remittance_code'])) {
			$query->where('remittance.remittance_code', 'LIKE', '%' .$filter['remittance_code']['value'] .'%');
		}

		if(isset($filter['remittance_date']) && is_array($filter['remittance_date']) && count($filter['remittance_date']) > 0) {
    		$fdate = $filter['remittance_date'][2].'-'.$filter['remittance_date'][1].'-'.$filter['remittance_date'][0];
    		$operator = $filter['remittance_date']['operator'];
    		if($operator == '=') {
    			$query->whereBetween('remittance.created_at', [$fdate.' 00:00:00', $fdate.' 23:59:59']);
    		}
    		else if($operator == '<') {
    			$query->where('remittance.created_at', $operator, $fdate.' 00:00:00');
    		}
    		else if($operator == '<=') {
    			$query->where('remittance.created_at', $operator, $fdate.' 23:59:59');
    		}
    		else {
    			$query->where('remittance.created_at', $operator, $fdate.' 00:00:00');
    		}
		}


		if(isset($filter['hub_appr_date']) && is_array($filter['hub_appr_date']) && count($filter['hub_appr_date']) > 0) {
    		$fdate = $filter['hub_appr_date'][2].'-'.$filter['hub_appr_date'][1].'-'.$filter['hub_appr_date'][0];
    		$operator = $filter['hub_appr_date']['operator'];
    		if($operator == '=') {
    			$query->whereBetween('hub_awh_history.created_at', [$fdate.' 00:00:00', $fdate.' 23:59:59']);
    		}
    		else if($operator == '<') {
    			$query->where('hub_awh_history.created_at', $operator, $fdate.' 00:00:00');
    		}
    		else if($operator == '<=') {
    			$query->where('hub_awh_history.created_at', $operator, $fdate.' 23:59:59');
    		}
    		else {
    			$query->where('hub_awh_history.created_at', $operator, $fdate.' 00:00:00');
    		}
		}

        if(isset($filter['hub_appr_by'])) {
			$query->where(DB::raw("CONCAT(hub_awh_user.firstname, ' ', hub_awh_user.lastname)"), 'LIKE', '%'.$filter['hub_appr_by']['value'].'%');
		}

		if(isset($filter['fin_appr_date']) && is_array($filter['fin_appr_date']) && count($filter['fin_appr_date']) > 0) {
    		$fdate = $filter['fin_appr_date'][2].'-'.$filter['fin_appr_date'][1].'-'.$filter['fin_appr_date'][0];
    		$operator = $filter['fin_appr_date']['operator'];
    		if($operator == '=') {
    			$query->whereBetween('fin_awh_history.created_at', [$fdate.' 00:00:00', $fdate.' 23:59:59']);
    		}
    		else if($operator == '<') {
    			$query->where('fin_awh_history.created_at', $operator, $fdate.' 00:00:00');
    		}
    		else if($operator == '<=') {
    			$query->where('fin_awh_history.created_at', $operator, $fdate.' 23:59:59');
    		}
    		else {
    			$query->where('fin_awh_history.created_at', $operator, $fdate.' 00:00:00');
    		}
		}

        if(isset($filter['fin_appr_by'])) {
			$query->where(DB::raw("CONCAT(fin_awh_user.firstname, ' ', fin_awh_user.lastname)"), 'LIKE', '%'.$filter['fin_appr_by']['value'].'%');
		}
		
		// iggrid filter code end here

		if($count) {
			/*if($filterType=='shortcollections'){
				$querydc='';				
				if(isset($filter['Dcs_Assigned']) && !empty($filter['Dcs_Assigned']) && isset($filter['Hubs_Assigned']) && !empty($filter['Hubs_Assigned'])) {
					if(Session::get('business_unitid')!=0 && !empty(Session::get('business_unitid')) && count($le_wh_ids)>0){
					if($le_wh_ids == ""){
                        $le_wh_ids = 0;
                    }    
                    $le_wh_ids=implode(',', $le_wh_ids);
                    $querydc = ' WHERE `orders`.`le_wh_id` IN ('.$le_wh_ids.')';
                    }else{
						$querydc = ' WHERE `orders`.`le_wh_id` IN ('.$filter['Dcs_Assigned'].')';
					}
					$querydc .= ' AND `orders`.`hub_id` IN ('.$filter['Hubs_Assigned'].')';
				}
				if($salestype==1){
					//for intermediate sales legal_entity_type_id in 1016
					$querydc.=' and orders.is_primary_sale =1';
				}elseif($salestype==2){
					
					$querydc.=' and orders.is_primary_sale =0';
				}
				$querycount = DB::select(DB::raw('SELECT SUM(innr.cnt) as totOrders FROM ( 
				SELECT COUNT(DISTINCT(orders.gds_order_id)) AS cnt
				FROM `collections` AS `c` 
				INNER JOIN `gds_orders` AS `orders` ON `c`.`gds_order_id` = `orders`.`gds_order_id` 
				INNER JOIN `collection_history` as `ch` ON `c`.`collection_id`=`ch`.`collection_id`' 
				.$querydc.'
				GROUP BY `orders`.`gds_order_id` 
				HAVING ROUND(SUM(IFNULL(c.invoice_amount,0)-IFNULL(c.return_total,0)-IFNULL(c.collected_amount,0)-IFNULL(c.discount_amt,0)),2)>=1) innr'));
				return isset($querycount[0]->totOrders) ? (int)$querycount[0]->totOrders : 0;
			}else{*/
			$query->groupBy('orders.gds_order_id');
			$fields = array(DB::raw("COUNT(DISTINCT orders.gds_order_id) as totOrders"));
			$data = $query->select($fields)->get()->all();
			$count = count($data);
			return $count;

		//}
		}
		
		else {
			$query->groupBy('orders.gds_order_id');
			$orderBy = !empty($sort['orderBy']) ? $sort['orderBy'] : 'orders.gds_order_id';
			$sortBy = !empty($sort['sortBy']) ? $sort['sortBy'] : 'desc';
			$query->orderby($orderBy, $sortBy);
			$offset = ($offset * $perpage);
			if(!isset($filter['gds_order_id']))
			$query->skip($offset)->take($perpage);
            //echo $query->toSql();die;
			$data = $query->select($fields)->get()->all();
      return $data;
		}
		
	}

	public function insertMapping($remittance){
		DB::table('remittance_mapping')
                    ->insert($remittance);
	}

}
