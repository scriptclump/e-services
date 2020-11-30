<?php 
namespace App\Modules\RoutingAdmin\Models;

use App\Modules\LegalEntities\Models\Legalentity;
use Illuminate\Database\Eloquent\Model;
use Config;
use DB;
use Log;
use  \Exception;


class UserGeoTrackerModel extends Model{

	public function __construct(){

	}

	/**
	 * Begin the used up scene
	 * [getAllFeildForce get all users with the data for the game]
	 * @return [type] [description]
	 */
	public function getAllFeildForce($usertype){

		$query = "select user_id, concat(firstname,' ',lastname) as name from users where is_active = 1 and user_id in (select user_id from user_roles where role_id in ($usertype))";
		$data = DB::select($query);
		if(count($data) > 0){

			$data = json_decode(json_encode($data),true);
			return $data;

		}else{
			return false;
		} 

	}

	/**
	 * [getLastKnownLocation description]
	 * @param  [type] $user_id  [description]
	 * @param  [type] $usertype [description]
	 * @return [type]           [description]
	 */
	public function getLastKnownLocation($user_id = null,$usertype=null){

		if(is_null($usertype)){
			return false;
		}
		$current_date = date('Y-m-d');
		//usertype = 52,53
		if(is_null($user_id)){

			$query = "	SELECT 
							geo_track.user_id, CONCAT(users.firstname,' ',users.lastname) AS name,
							geo_track.latitude,
							geo_track.longitude,
							geo_track.heading,
							geo_track.created_at AS last_seen
							FROM geo_track
							LEFT JOIN users ON users.user_id = geo_track.user_id
							INNER JOIN 
							(
								SELECT MAX(gt_id) AS id
								FROM geo_track
								GROUP BY user_id
							) last_updates ON last_updates.id = geo_track.gt_id
							WHERE 
							geo_track.user_id IN (
											SELECT user_id
											FROM user_roles
											WHERE role_id IN ($usertype)
									)
							AND date(geo_track.created_at) = '$current_date'
							GROUP BY geo_track.user_id
							ORDER BY geo_track.gt_id DESC";

		}else{

			if(count(explode(',',$user_id) > 0)){

				$query = "	SELECT 
							geo_track.gt_id,
							geo_track.user_id, CONCAT(users.firstname,' ',users.lastname) AS name,
							geo_track.latitude,
							geo_track.longitude,
							geo_track.heading,
							geo_track.created_at AS last_seen
						FROM geo_track
						LEFT JOIN users ON users.user_id = geo_track.user_id
						WHERE 
							geo_track.user_id in ( $user_id )
						AND date(geo_track.created_at) = '$current_date'
						GROUP BY geo_track.user_id
						ORDER BY geo_track.gt_id DESC";

			}else{

				$query = "	SELECT 
							geo_track.gt_id,
							geo_track.user_id, CONCAT(users.firstname,' ',users.lastname) AS name,
							geo_track.latitude,
							geo_track.longitude,
							geo_track.heading,
							geo_track.created_at AS last_seen
						FROM geo_track
						LEFT JOIN users ON users.user_id = geo_track.user_id
						WHERE 
							geo_track.user_id = $user_id
						AND date(geo_track.created_at) = '$current_date'
						ORDER BY geo_track.gt_id DESC
						LIMIT 1";
			}
			
		}

		$data = DB::select($query);

		if(count($data) > 0){

			$data = json_decode(json_encode($data),true);
			$query = "select bu.bu_name, bu.description, lw.dc_type, lw.longitude, lw.latitude, concat(lw.address1, lw.address2) as address 
						from legalentity_warehouses lw, business_units bu
						where lw.bu_id = bu.bu_id and lw.status=1
						group by lw.bu_id";
			$res = json_decode(json_encode(DB::select($query)),true);
			$data['hubs'] = $res;
			return $data;

		}else{
			return false;
		}
		
	}

	public function historyTimeRoute($user_id,$date){


		$query = "SELECT 
						latitude,
						longitude, 
						MAX(key_data.created_at) AS created_at, 
						TIMESTAMPDIFF(MINUTE, 
						MIN(key_data.created_at), 
						MAX(key_data.created_at)) time_spent, CONCAT(users.firstname,' ',users.lastname) AS name
					FROM (
								SELECT 	*, CONCAT(user_id,latitude,longitude, DATE(geo_track.created_at)) AS key_adjuster
								FROM geo_track
								WHERE 
									user_id = $user_id AND 
									accuracy <= 10 AND DATE(geo_track.created_at) = '$date'			
							) AS key_data
					LEFT JOIN users ON users.user_id = key_data.user_id
					GROUP BY key_adjuster
					ORDER BY key_data.gt_id ASC";
		$data = DB::select($query);
		if(count($data) > 0){
			$data = json_decode(json_encode($data),true);
			return $data;			
		}else{
			return false;
		}
	}

	/**
	 * [setUpdateDeOrderlist WILL UPDATE WITH CURRENT DATA OF THE ORDERS FOR EACH DATA]
	 * @param [type] $de_user_id [int]
	 * @param [type] $date       [date]
	 */
	public function setUpdateDeOrderlist($de_user_id,$date){

		$cur_date = date('Y-m-d H:i:s');

		if($date == $cur_date){

			$query = "select * from delivery_track_order where created_date = $date and $user_id = $de_user_id";
			$data = DB::select($query);
			if(count($data) > 0){
				
				$data = json_decode(json_encode($data),true);
				$update_order_status = $this->getOrdersOnDelivery($de_user_id,$date);
				
				if($update_order_status){

					$this->updateDeliveryTrackOrderInfo($user_id,json_encode($update_order_status),$date);
					return $update_order_status;
				
				}else{
					return false;
				}
				
			}else{

				$update_order_status = $this->getOrdersOnDelivery($de_user_id,$date);

				if($update_order_status){
					
					$this->setDeliveryTrackOrderInfo($user_id,json_encode($update_order_status));
					return $update_order_status;
				
				}else{
					return false;
				}

			}

		}else{

			$query = "select * from delivery_track_order where created_date = $date and user_id = $de_user_id";
			$data = DB::select($query);

			if(count($data) > 0){
				$data = json_decode(json_encode($data),true);
				return $data[0]['order_data'];			
			}else{
				$d = $this->getOrdersOnDelivery($de_user_id,$date);

				return $d; //else return false
			}

		}

	}

	/**
	 * [getOrdersOnDelivery description]
	 * @param  [type] $de_user_id [description]
	 * @param  [type] $date       [description]
	 * @return [type]             [description]
	 */
	public function getOrdersOnDelivery($de_user_id,$date){


		$query = "	SELECT 
						od.gds_order_id,
						od.order_code,
						od.created_at AS order_date,
						od.cust_le_id,
						getMastLookupValue(od.order_status_id) AS order_status,
						lw.le_wh_id AS hub_id,
						lw.lp_wh_name AS hub_name,
						le.latitude AS `latitude`,
						le.longitude AS `longitude`,
						le.business_legal_name,
						ot.delivery_date,
						ot.st_vehicle_no AS vehicle_no,
						IFNULL(co.invoice_amount, 0) AS invoice_amount,
						IFNULL(co.collected_amount, 0) AS collected_amount,
						IFNULL(co.return_total, 0) AS return_total
						FROM gds_order_track ot
						LEFT JOIN gds_orders od ON od.gds_order_id = ot.gds_order_id
						LEFT JOIN legal_entities le ON od.cust_le_id = le.legal_entity_id
						LEFT JOIN legalentity_warehouses lw ON lw.le_wh_id = od.hub_id
						LEFT JOIN users u ON u.user_id = ot.delivered_by
						LEFT JOIN collections co ON co.gds_order_id = od.gds_order_id
						WHERE DATE(ot.delivery_date) = '$date' 
						AND ot.delivered_by = $de_user_id";
		$data = DB::select($query);
		if(count($data) > 0){
			$data = json_decode(json_encode($data),true);
			//$data = json_encode($data);
			return $data;			
		}else{
			return false;
		}
	}

	/**
	 * [setDeliveryTrackOrderInfo description]
	 * @param [type] $user_id [description]
	 * @param [type] $data    [description]
	 */
	public function setDeliveryTrackOrderInfo($user_id,$data){

		$insert_data = array(
								'user_id' => $user_id,
								'order_data' => $data,
								'trip_status' => 1

			);

		DB::table('delivery_track_order')->insert($insert_data); 

	}

	/**
	 * [updateDeliveryTrackOrderInfo description]
	 * @param  [type] $user_id [description]
	 * @param  [type] $data    [description]
	 * @param  [type] $date    [description]
	 * @return [type]          [description]
	 */
	public function updateDeliveryTrackOrderInfo($user_id,$data,$date){

		DB::statement("UPDATE delivery_track_order SET order_data = $data WHERE user_id = $user_id and created_date = $date");
	}
	
}