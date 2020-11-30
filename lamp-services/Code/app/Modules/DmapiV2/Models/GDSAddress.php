<?php

namespace App\Modules\DmapiV2\Models;
use App\Modules\DmapiV2\Models\Dmapiv2Model;
use App\Modules\Orders\Models\OrderModel;
use DB;
use Illuminate\Database\Eloquent\Model;
use Cache;

class GDSAddress extends Model {

	/**
	 * [insertCustomerAddress description]
	 * @param  [type] $data       [description]
	 * @param  [type] $gds_orders [object of gds orders]
	 * @return [type]             [description]
	 */
	public function insertCustomerAddress($data,$gds_orders) {
	    
	    try {
	           	$dmapiv2Model = new Dmapiv2Model;
	           	$data = json_decode($data['orderdata']);
	           	$addressInfo = $data->address_info;
	           	$custAddressArray = array();
	           	foreach ($addressInfo as $address) {
	           		
	           		$custAddressArray[] = [
	                    'fname' => property_exists($address, 'first_name') ? $address->first_name : '',
	                    'mname' => property_exists($address, 'middle_name') ? $address->middle_name : '',
	                    'lname' => property_exists($address, 'last_name') ? $address->last_name : '',
	                    'address_type' => property_exists($address, 'address_type') ? $address->address_type : '',
	                    'company' => property_exists($address, 'company') ? $address->company : '',
	                    'addr1' => property_exists($address, 'address1') ? $address->address1 : '',
	                    'addr2' => property_exists($address, 'address2') ? $address->address2 : '',
	                    'city' => property_exists($address, 'city') ? $address->city : '',
	                    'state_id' => $gds_orders->getOrderStateId(),
	                    'country_id' => $dmapiv2Model->getCountryIdFromCountryName($address->country),//set country here
	                    'postcode' => property_exists($address, 'pincode') ? $address->pincode : '',
	                    'telephone' => property_exists($address, 'phone') ? $address->phone : '',
	                    'mobile' => property_exists($address, 'mobile_no') ? $address->mobile_no : '',
	                    'gds_order_id' => $gds_orders->getGdsOrderId(),
	                    'area' => property_exists($address, 'area_id') ? $address->area_id : '',
	                    'landmark' => property_exists($address, 'landmark') ? $address->landmark : '0',
	                    'locality' => property_exists($address, 'locality') ? $address->locality : '0'
	                ];

	           		
	           	}
 	             
	            DB::table('gds_orders_addresses')->insert($custAddressArray);
	                
	        }catch (ErrorException $ex) {
	            return $e;
	        }
	    }
	}