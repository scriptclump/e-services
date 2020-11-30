<?php

namespace App\Modules\Communication\Models;
use App\Central\Repositories\CustomerRepo;
use App\Central\Repositories\ProductRepo;
use App\Lib\Queue;
use App\Modules\Communication\Models\CommunicationMongoModel;
use DB;
use Illuminate\Database\Eloquent\Model;
use Log;
use Session;
use Config;

class Communication extends Model {
	public function getDcData() {
		try
		{
			return DB::table('legalentity_warehouses')
				->where(['dc_type' => 118001, 'status' => 1])
				->select('le_wh_id', 'lp_wh_name')
				->get()->all();
		} catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
		}
	}

	public function getRolesList() {
		try
		{
			$legalEntityId = Session::get('legal_entity_id');
			return DB::table('roles')
				// ->where(['is_active' => 1, 'legal_entity_id' => $legalEntityId])
			    ->where('is_active' , 1)
				->select('role_id', 'name')
				->get()->all();
		} catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
		}
	}

	public function getHubData($data) {
		try
		{
			$dcId = isset($data['dc_id']) ? explode(',', $data['dc_id']) : 0;
			$status = 0;
			$message = '';
			$response = [];
			if ($dcId != '') {
				if ($dcId == 0) {
					$response = DB::table('dc_hub_mapping')
						->leftJoin('legalentity_warehouses', 'legalentity_warehouses.le_wh_id', '=', 'dc_hub_mapping.hub_id')
						->where(['legalentity_warehouses.dc_type' => 118002,
							'legalentity_warehouses.status' => 1])
//                        ->whereIn('dc_hub_mapping.dc_id', $dcId)
						->select('legalentity_warehouses.le_wh_id', 'legalentity_warehouses.lp_wh_name')
						->get()->all();
				} else {
					$response = DB::table('dc_hub_mapping')
						->leftJoin('legalentity_warehouses', 'legalentity_warehouses.le_wh_id', '=', 'dc_hub_mapping.hub_id')
						->where(['legalentity_warehouses.dc_type' => 118002,
							'legalentity_warehouses.status' => 1])
						->whereIn('dc_hub_mapping.dc_id', $dcId)
						->select('legalentity_warehouses.le_wh_id', 'legalentity_warehouses.lp_wh_name')
						->get()->all();
				}

				if (!empty($response)) {
					$status = 1;
					$message = 'Sucess';
					$response = json_decode(json_encode($response), true);
				}
			}
//            $temp[] = ['le_wh_id' => 0, 'lp_wh_name' => 'ALL'];
			//            $response = array_merge($temp, $response);
			$temp2 = json_encode(['status' => 1, 'message' => $message, 'response' => $response]);
			return $temp2;
		} catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
			$message = $ex->getMessage();
			return json_encode(['status' => 0, 'message' => $message, 'response' => $response]);
		}
	}

	public function getBeatData($data) {
		try
		{
			$hubId = isset($data['hub_id']) ? explode(',', $data['hub_id']) : 0;
			$status = 0;
			$message = '';
			$response = [];
			if ($hubId != '') {
				if ($hubId == 0) {
					$response = DB::table('pjp_pincode_area')
						->leftJoin('legalentity_warehouses', 'legalentity_warehouses.le_wh_id', '=', 'pjp_pincode_area.le_wh_id')
						->where(['legalentity_warehouses.dc_type' => 118002,
							'legalentity_warehouses.status' => 1])
//                        ->whereIn('pjp_pincode_area.le_wh_id', $hubId)
						->select('pjp_pincode_area.pjp_pincode_area_id', 'pjp_pincode_area.pjp_name')
						->get()->all();
				} else {
					$response = DB::table('pjp_pincode_area')
						->leftJoin('legalentity_warehouses', 'legalentity_warehouses.le_wh_id', '=', 'pjp_pincode_area.le_wh_id')
						->where(['legalentity_warehouses.dc_type' => 118002,
							'legalentity_warehouses.status' => 1])
						->whereIn('pjp_pincode_area.le_wh_id', $hubId)
						->select('pjp_pincode_area.pjp_pincode_area_id', 'pjp_pincode_area.pjp_name')
						->get()->all();
				}

				if (!empty($response)) {
					$status = 1;
					$message = 'Sucess';
					$response = json_decode(json_encode($response), true);
				}
//                $temp[] = ['pjp_pincode_area_id' => 0, 'pjp_name' => 'ALL'];
				//                $response = array_merge($temp, $response);
			}
			$temp = json_encode(['status' => 1, 'message' => $message, 'response' => $response]);
			return $temp;
		} catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
			$message = $ex->getMessage();
			return json_encode(['status' => 0, 'message' => $message, 'response' => $response]);
		}
	}

	public function sendMessage($data) {
		try
		{
			$message = "No records found to send message.";
			$response = '';
			$status = 0;
			$customerMobileList = [];
			$messageType = isset($data['message_type']) ? $data['message_type'] : [];
			$smsMessage = isset($data['message']) ? $data['message'] : '';
			if (!empty($messageType) && $smsMessage != '') {
				$dcNames = isset($data['dc_name']) ? $data['dc_name'] : [];
				$hubs = isset($data['hubs']) ? $data['hubs'] : [];
				$beats = isset($data['beats']) ? $data['beats'] : [];
				$roles = isset($data['roles']) ? $data['roles'] : [];

				if (empty($beats)) {
					if (!empty($dcNames) && empty($hubs)) {
						$hubs = DB::table('dc_hub_mapping')
							->whereIn('dc_id', $dcNames)
							->pluck('hub_id')->all();
					}
					if (!empty($hubs) && empty($beats)) {
						$beats = DB::table('pjp_pincode_area')
							->whereIn('le_wh_id', $hubs)
							->pluck('pjp_pincode_area_id')->all();
					}
				}
				$customerRoleId = 0;
				$customerRole = DB::table('legal_entity_roles')
					->where('le_type_id', 'like', '3%')
					->first(['role_id']);
				if (!empty($customerRole)) {
					$customerRoleId = property_exists($customerRole, 'role_id') ? $customerRole->role_id : 0;
				}
//                echo "<pre>";print_R($beats);die;
				//DB::enableQueryLog();
				//Log::info('Beats');
				//Log::info($beats);
				//Log::info('customerRoleId');
				//Log::info($customerRoleId);
				//Log::info('roles');
				//Log::info($roles);
				//Log::info('dcNames');
				//Log::info($dcNames);
				//Log::info('hubs');
				//Log::info($hubs);
				if (in_array($customerRoleId, $roles)) {
					if (!empty($beats)) {
						$customerMobileList = DB::table('users')
							->leftJoin('customers', 'customers.le_id', '=', 'users.legal_entity_id')
							->leftJoin('user_roles', 'user_roles.user_id', '=', 'users.user_id')
							->whereIn('customers.beat_id', $beats)
							->where(DB::raw('CHAR_LENGTH(users.mobile_no)'), '>', 9)
							->where('users.is_active', 1)
							->whereIn('user_roles.role_id', $roles)
							->groupBy('users.mobile_no')
							->pluck('users.mobile_no')->all();
					} else {
						$customerMobileList = DB::table('users')
//                      ->leftJoin('customers', 'customers.le_id', '=', 'users.legal_entity_id')
						//                            ->whereIn('customers.beat_id', $beats)
							->where(DB::raw('CHAR_LENGTH(users.mobile_no)'), '>', 9)
							->where('users.is_active', 1)
							->groupBy('users.mobile_no')
							->pluck('users.mobile_no')->all();
					}

				} elseif (!empty($beats) && !empty($dcNames) || !empty($hubs)) {
					$dcNames[] = 0;
					if (!empty($roles)) {
						$customerMobileList = DB::table('users')
							->leftJoin('user_roles', 'user_roles.user_id', '=', 'users.user_id')
							->leftJoin('user_permssion', 'user_permssion.user_id', '=', 'users.user_id')
							->leftJoin('legalentity_warehouses', 'legalentity_warehouses.bu_id', '=', 'user_permssion.object_id')
							->whereIn('user_roles.role_id', $roles)
							->where('users.is_active', 1)
							->whereIn('legalentity_warehouses.le_wh_id', array_merge($dcNames, $hubs))
							->where(DB::raw('CHAR_LENGTH(users.mobile_no)'), '>', 9)
							->groupBy('users.mobile_no')
							->pluck('users.mobile_no')->all();
					} else {
						$customerMobileList = DB::table('users')
							->leftJoin('user_roles', 'user_roles.user_id', '=', 'users.user_id')
							->leftJoin('user_permssion', 'user_permssion.user_id', '=', 'users.user_id')
							->leftJoin('legalentity_warehouses', 'legalentity_warehouses.bu_id', '=', 'user_permssion.object_id')
//                            ->whereIn('user_roles.role_id', $roles)
							->where('users.is_active', 1)
							->whereIn('legalentity_warehouses.le_wh_id', array_merge($dcNames, $hubs))
							->where(DB::raw('CHAR_LENGTH(users.mobile_no)'), '>', 9)
							->groupBy('users.mobile_no')
							->pluck('users.mobile_no')->all();
					}

				} elseif (!empty($dcNames) && empty($beats) && empty($hubs)) {
					if (!empty($roles)) {
						$customerMobileList = DB::table('users')
							->leftJoin('user_roles', 'user_roles.user_id', '=', 'users.user_id')
							->leftJoin('user_permssion', 'user_permssion.user_id', '=', 'users.user_id')
							->leftJoin('legalentity_warehouses', 'legalentity_warehouses.bu_id', '=', 'user_permssion.object_id')
							->whereIn('user_roles.role_id', $roles)
							->where('users.is_active', 1)
							->whereIn('legalentity_warehouses.le_wh_id', array_merge($dcNames, [0]))
							->where(DB::raw('CHAR_LENGTH(users.mobile_no)'), '>', 9)
							->groupBy('users.mobile_no')
							->pluck('users.mobile_no')->all();
					} else {
						$customerMobileList = DB::table('users')
//                            ->leftJoin('user_roles', 'user_roles.user_id', '=', 'users.user_id')
							->leftJoin('user_permssion', 'user_permssion.user_id', '=', 'users.user_id')
							->leftJoin('legalentity_warehouses', 'legalentity_warehouses.bu_id', '=', 'user_permssion.object_id')
//                            ->whereIn('user_roles.role_id', $roles)
							->where('users.is_active', 1)
							->whereIn('legalentity_warehouses.le_wh_id', array_merge($dcNames, [0]))
							->where(DB::raw('CHAR_LENGTH(users.mobile_no)'), '>', 9)
							->groupBy('users.mobile_no')
							->pluck('users.mobile_no')->all();
					}
				} elseif (!empty($dcNames) && empty($beats) && !empty($hubs)) {
					if (!empty($roles)) {
						$customerMobileList = DB::table('users')
							->leftJoin('user_roles', 'user_roles.user_id', '=', 'users.user_id')
							->leftJoin('user_permssion', 'user_permssion.user_id', '=', 'users.user_id')
							->leftJoin('legalentity_warehouses', 'legalentity_warehouses.bu_id', '=', 'user_permssion.object_id')
							->whereIn('user_roles.role_id', $roles)
							->where('users.is_active', 1)
							->whereIn('legalentity_warehouses.le_wh_id', array_merge($dcNames, [0], $hubs))
							->where(DB::raw('CHAR_LENGTH(users.mobile_no)'), '>', 9)
							->groupBy('users.mobile_no')
							->pluck('users.mobile_no')->all();
					} else {
						$customerMobileList = DB::table('users')
//                            ->leftJoin('user_roles', 'user_roles.user_id', '=', 'users.user_id')
							->leftJoin('user_permssion', 'user_permssion.user_id', '=', 'users.user_id')
							->leftJoin('legalentity_warehouses', 'legalentity_warehouses.bu_id', '=', 'user_permssion.object_id')
//                            ->whereIn('user_roles.role_id', $roles)
							->where('users.is_active', 1)
							->whereIn('legalentity_warehouses.le_wh_id', array_merge($dcNames, [0], $hubs))
							->where(DB::raw('CHAR_LENGTH(users.mobile_no)'), '>', 9)
							->groupBy('users.mobile_no')
							->pluck('users.mobile_no')->all();
					}
				} else {
					if (!empty($roles)) {
						$customerMobileList = DB::table('users')
							->leftJoin('user_roles', 'user_roles.user_id', '=', 'users.user_id')
							->whereIn('user_roles.role_id', $roles)
							->where('users.is_active', 1)
							->where(DB::raw('CHAR_LENGTH(users.mobile_no)'), '>', 9)
							->groupBy('users.mobile_no')
							->pluck('users.mobile_no')->all();
					}
				}
				
				//Log::info(DB::getQueryLog());
				//                echo "<pre>";print_r($customerMobileList);die;
				if (!empty($customerMobileList)) {
					//Log::info(DB::getQueryLog());
					$data['status'] = 0;
					$data['sms_status'] = 0;
					$data['push_status'] = 0;
					$data['sms_sent_count'] = 0;
					$data['push_sent_count'] = 0;
					$data['mobile_numbers'] = implode(',', $customerMobileList);
					//$data['mobile_numbers'] = $customerMobileList;
					$data['count_mobile_numbers'] = count($customerMobileList);
					$data['message'] = (trim($data['message']));
					date_default_timezone_set('Asia/Kolkata');
					$data['created_at'] = date('Y-m-d H:i:s');
					$data['created_by'] = Session::get('userId');
					$data['created_by_name'] = Session::get('userName');
					

					$response = DB::table('communication')->insertGetId(array('status' => $data['status'], 'sms_status' => $data['sms_status'], 'push_status' => $data['push_status'],
						'sms_sent_count' => $data['sms_sent_count'],
						'push_sent_count' => $data['push_sent_count'],
						//'mobile_numbers' => $data['mobile_numbers'],
						'count_mobile_numbers' => $data['count_mobile_numbers'],
						'dc_name' => ($dcNames != '')?implode(',',$dcNames):'',
						'hubs' => ($hubs != '')?implode(',', $hubs):'',
						'beats' => ($beats != '')?implode(',', $beats):'',
						'roles' => ($beats != '')?implode(',', $roles):'',
						'message_type' => implode(',', $data['message_type']),
						'message' => $data['message'],
						'created_at' => $data['created_at'],
						'created_by' => $data['created_by'],
						'created_by_name' => $data['created_by_name'],
					));


 $nums = array_chunk($customerMobileList,10000);

 foreach ($nums as  $num) {

 	$data1[] = [
 		'communication_id'=> $response,
 		'mobile_numbers'=> implode(',', $num),

 	];

 	
 
 }


DB::table('communication_details')->insert($data1);








				 	//print_r($data);
					//$communicationMongoModel = new CommunicationMongoModel();
					//$response = $communicationMongoModel->storeData($data);

				}

				if ($response != '') {
					//$response = json_decode(json_encode($response), true);
					//print_r($response);exit;

					$this->queue = new Queue();
				
                	$args = array("ConsoleClass" => 'MessageQueues',
						'arguments' => array($response));
				
                	$token = $this->queue->enqueue('default', 'ResqueJobRiver', $args);
					$status = 1;
					$message = "Your request is in queue, please keep monitoring list further update.";
				}
			} else {
				$message = "Please provide proper data.";
			}
			return json_encode(['status' => $status, 'message' => $message, 'response' => $response]);
		} catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
			$message = $ex->getMessage();
			return json_encode(['status' => 0, 'message' => $message, 'response' => $response]);
		}
	}

	public function processPendingMessages($id = null) {
		try
		{
			$status = 0;
			$message = 'No Data Found.';

			$response = $this->getPendingMessageList($id);

			if (!empty($response)) {
				foreach ($response as $messageData) {


//                    echo "<prE>";print_R($messageData->toArray());die;

					$messageInfo = $messageData;

					//echo "<pre>";
					//print_r(count($messageInfo));

					
					if ($messageInfo != '') {
						$pushMessage = isset($messageInfo->message) ? ($messageInfo->message) : '';


						if ($pushMessage != '') {


							$pushMessageId = isset($messageInfo->id) ? $messageInfo->id : 0;

							$comm_det_id = isset($messageInfo->comm_det_id) ? $messageInfo->comm_det_id : 0;

							$pushMessageCreatedBy = isset($messageInfo->created_by) ? $messageInfo->created_by : 0;
							$pushMessageType = isset($messageInfo->message_type) ? explode(',', $messageInfo->message_type) : [];
							$pushMessageNumbers = isset($messageInfo->mobile_numbers) ? explode(',', $messageInfo->mobile_numbers) : [];
							$pushMessageSmsStatus = isset($messageInfo->sms_status) ? $messageInfo->sms_status : 0;
							$pushMessagePushStatus = isset($messageInfo->push_status) ? $messageInfo->push_status : 0;
							if (!empty($pushMessageNumbers)) {
								if ($pushMessageSmsStatus == 0) {
									if (in_array('sms', $pushMessageType) && in_array('push', $pushMessageType)) {


										$message = $this->sendSMS($pushMessageId, $pushMessageCreatedBy, $pushMessage, $pushMessageNumbers,$comm_det_id);

										// $this->sendPushNotification($pushMessageId, $pushMessageCreatedBy, $pushMessage, $pushMessageNumbers);

									} elseif (in_array('sms', $pushMessageType)) {
										$message = $this->sendSMS($pushMessageId, $pushMessageCreatedBy, $pushMessage, $pushMessageNumbers,$comm_det_id);
									} else {
										$message = "This message queue is not for SMS";
									}
								} else {
									$message = "This message queue has already sent all SMS's";
								}


								if ($pushMessagePushStatus == 0) {
									if (in_array('sms', $pushMessageType) && in_array('push', $pushMessageType)) {

									
										// $message = $this->sendSMS($pushMessageId, $pushMessageCreatedBy, $pushMessage, $pushMessageNumbers);
										$this->sendPushNotification($pushMessageId, $pushMessageCreatedBy, $pushMessage, $pushMessageNumbers,$comm_det_id);
									} elseif (in_array('push', $pushMessageType)) {



										$this->sendPushNotification($pushMessageId, $pushMessageCreatedBy, $pushMessage, $pushMessageNumbers,$comm_det_id);
									} else {
										$message = "This message queue is not for Push Notifications";
									}
								} else {
									$message = "This message queue has already sent all Push Notifications";
								}
							} else {
								$message = "No Numbers to send";
							}
						} else {
							$message = "Improper Message";
						}
					} else {
						$message = "Improper data from Mongo";
					}
				}
			}
			return json_encode(['status' => $status, 'message' => $message]);


		} catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
			$message = $ex->getMessage();
			return json_encode(['status' => 0, 'message' => $message, 'response' => $response]);
		}
	}

	public function sendSMS($pushMessageId, $pushMessageCreatedBy, $pushMessage, $pushMessageNumbers,$comm_det_id) {
		try
		{
			$resonse = [];
			$message[] = 'No data found';
			if ($pushMessage != '' && !empty($pushMessageNumbers)) {
				$state = 1;
				$ishex = 0;
				$dcs = 0;
				if (ctype_xdigit($pushMessage)) {
					$state = 4;
					$ishex = 1;
					$dcs = 245;
				}

//                $retailersData = array_chunk($pushMessageNumbers, 10);
				$retailersData = array_unique($pushMessageNumbers);

		

				//$communicationMongoModel = new CommunicationMongoModel();




	$checkIfSent = $this->validateNumbers($pushMessageId, $comm_det_id);

		if ($checkIfSent) {


						//$customerRepo = new CustomerRepo();
						$temp = $this->sendSMSGetway($pushMessageId, $pushMessageCreatedBy, $retailersData, $pushMessage, $state, $ishex, $dcs,$comm_det_id);
						if ($temp != '') {
							$resonse[] = $temp;
						}
					} else {
						$message[] = "This number message already sent";
					}



				// foreach ($retailersData as $numbers) {

					

				// 	$checkIfSent = $this->validateNumbers($pushMessageId, $comm_det_id);




				// 	if ($checkIfSent) {
				// 		//$customerRepo = new CustomerRepo();
				// 		$temp = $this->sendSMSGetway($pushMessageId, $pushMessageCreatedBy, $numbers, $pushMessage, $state, $ishex, $dcs);
				// 		if ($temp != '') {
				// 			$resonse[] = $temp;
				// 		}
				// 	} else {
				// 		$message[] = "This number(" . $numbers . ") message already sent";
				// 	}
				// }


				$this->updateStatusDetails($pushMessageId,$comm_det_id);

				$this->updateStatus($pushMessageId, 'sms', 'sms_status');


				$this->updateCount($pushMessageId, 'sms', count($retailersData));

				$this->updateMessageQueue($pushMessageId, 'sms');

				//Log::info(count($retailersData) . ' == ' . count($resonse));

				//print_r(count($retailersData));

				// if (count($retailersData) == count($resonse)) {
				// 	$this->updateMessageQueue($pushMessageId, 'sms');
				// 	$message[] = "All the message are sent to the respective numbers";
				// } else {
				// 	$message[] = "Some of the customer recieved the messages and some are missing due to some issue, please check the report.";
				// }
			}
			return $message;
		} catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
			return $ex->getMessage();
		}
	}

	public function sendPushNotification($pushMessageId, $pushMessageCreatedBy, $pushMessage, $pushMessageNumbers,$comm_det_id) {
		try {
			//$communicationMongoModel = new CommunicationMongoModel();

			$retailersData = array_unique($pushMessageNumbers);
			// $RegId = DB::table('device_details')
			// 	->join('users', 'users.user_id', '=', 'device_details.user_id')
			// 	// ->select('max(registration_id) as registration_id', 'platform_id','device_details.user_id')	
			// 	 ->select(DB::raw('registration_id,platform_id,device_details.user_id,users.mobile_no as mobile_no,max(device_details.created_at) as created_at'))
			// 	 ->orderBy('device_details.created_at', 'DESC')		
			// 	->groupBy('device_details.user_id')
			// 	//->orderBy('device_details.updated_at','DESC')
						
			// 	->whereIn('users.mobile_no', $pushMessageNumbers)->get()->all();





	$RegId1 = DB::table('users')
	 ->select(DB::raw('user_id'))
	 ->whereIn('mobile_no', $pushMessageNumbers)->get()->all();

	 foreach ($RegId1 as  $RegId2) {

	 	$RegId[] =  DB::table('device_details') ->select(DB::raw('registration_id,platform_id,users.mobile_no as mobile_no'))
	 	->join('users', 'users.user_id', '=', 'device_details.user_id')
	 	 ->orderBy('device_details.updated_at','DESC')
	 	 ->where('device_details.user_id',$RegId2->user_id)
	 	 	 	 ->first();
	 }

$RegId = array_filter($RegId);
$tokenDetails = json_decode((json_encode($RegId)), true);
			//$productRepo = new ProductRepo();


// print_r($tokenDetails);
	foreach ($RegId as  $mobile) {

              $mobiles[] = $mobile->mobile_no;
      
			}


	




			$this->pushNotifications($pushMessage, $tokenDetails, $type = 'push', $sentBy = 'Ebutor', $link = '',$pushMessageId,$pushMessageCreatedBy,$data='', $start  = 0 ,$end = 0,$comm_det_id,$mobiles);

			$this->updateStatusDetails($pushMessageId,$comm_det_id);
			$this->updateStatus($pushMessageId, 'push', 'push_status');
			$this->updateCount($pushMessageId, 'push', count($RegId));

     		//Log::info(count($retailersData).' == '.count($resonse));
			//if(count($retailersData) == count($resonse))
			//{
			$this->updateMessageQueue($pushMessageId, 'push');
			$message[] = "All the message are sent to the respective numbers";
			//}else{
			//	$message[] = "Some of the customer recieved the messages and some are missing due to some issue, please check the report.";
			//}
			return $message;
		} catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
		}
	}

//get data database

	public function getAllMessages($request) {
		try
		{
			$grid_fields = [
				'message_type' => 'message_type',
				'message' => 'message',
				'count_mobile_numbers' => 'count_mobile_numbers',
				'created_by' => 'created_by',
				'created_by_name' => 'created_by_name',
				'created_at' => 'created_at',
				'sms_sent_count' => 'sms_sent_count',
				'push_sent_count' => 'push_sent_count',
			];
			$results = DB::table('communication')->whereIn('status', [0, 1])
//                    ->leftJoin('message_history', 'message_history.reference_id', '=', 'message_queue._id')
				->select('id', 'message', 'message_type', 'count_mobile_numbers',
					'sms_sent_count', 'push_sent_count',
					'created_by', 'created_by_name', 'created_at');
//                    ->selectRaw('count(mobile_numbers) as count2');
			//                ->orderBy('created_at', 'DESC')
			//                ->skip(0)->take(20)->get();
			$page = (int) $request->input('page'); //Page number
			$pageSize = (int) $request->input('pageSize'); //Page size for ajax
			//            $notificationsModel = new NotificationsModel();
			//$filter_by = $this->filterData($request, $grid_fields);
			//          Log::info('filter_by');
			//            Log::info($filter_by);
			//            echo "<prE>";print_R($filter_by);die;
			//        Log::info($filter_by);

			$filter_by = $this->filterData($request, $grid_fields);

			//          Log::info('filter_by');
			//            Log::info($filter_by);
			//            echo "<prE>";print_R($filter_by);die;
			//        Log::info($filter_by);
			$order_by = '';
			$orderby_array = [];
			$totalCount1 = 0;
			if (!empty($filter_by)) {
				foreach ($filter_by as $filterByEach) {
					$filterByEachExplode = explode(' ', $filterByEach);

					$length = count($filterByEachExplode);
					$filter_query_value = '';
					if ($length > 3) {
						$filter_query_field = $filterByEachExplode[0];
						$filter_query_operator = $filterByEachExplode[1];
						for ($i = 2; $i < $length; $i++) {
							$filter_query_value .= $filterByEachExplode[$i] . " ";
						}

					} else {
						$filter_query_field = $filterByEachExplode[0];
						$filter_query_operator = $filterByEachExplode[1];
						$filter_query_value = $filterByEachExplode[2];
					}

					$operator_array = array('=', '!=', '>', '<', '>=', '<=');
					if (in_array(trim($filter_query_operator), $operator_array)) {
						$results = $results->where($filter_query_field, $filter_query_operator, trim($filter_query_value));
					} elseif ($filter_query_operator == 'like' && $filter_query_field == 'created_at') {
						$results = $results->where($filter_query_field, $filter_query_operator, trim($filter_query_value));
					} else {
						$results = $results->where($filter_query_field, $filter_query_operator, trim($filter_query_value));
					}
				}
			}
			if ($request->input('$orderby')) {
				//checking for sorting
				$order = explode(' ', $request->input('$orderby'));
				$order_query_field = $order[0]; //on which field sorting need to be done
				$order_query_type = $order[1]; //sort type asc or desc
				$order_by_type = 'desc';

				if ($order_query_type == 'asc') {
					$order_by_type = 'asc';
				}

				if (isset($grid_fields[$order_query_field])) {
					//getting appropriate table field based on grid field
					$order_by = $grid_fields[$order_query_field];
				}
				$orderby_array = $order_by . " " . $order_by_type;
			}
			//   Log::info('orderby_array');
			// Log::info($orderby_array);
			//echo "<pre>";

			if (!empty($orderby_array)) {
				$orderClause = explode(" ", $orderby_array);
				$results = $results->orderby($orderClause[0], $orderClause[1]); //order by query
			} else {
				$results = $results->orderBy('created_at', 'DESC');
			}
			if ($page == '' || $page < 0) {
				$page = 0;
			}
			if ($pageSize == '' || $pageSize < 0) {
				$pageSize = 10;
			}
			$countResult = clone $results;
			\DB::connection($this->connection)->enableQueryLog();
//            $results = $results->groupBy('message_history.reference_id');
			$results = $results->skip($page * $pageSize)->take($pageSize)->get();
//            echo "<pre>";print_R($results);die;
			//            $results = $results->get();
			//   Log::info(\DB::connection($this->connection)->getQueryLog());
			$totalCount1 = $countResult->get();
			$totalCount = count($totalCount1);

			return ['Records' => $results, 'totalUserCount' => $totalCount];
		} catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
		}
	}

//db filterData

	public function filterData($request, $grid_fields) {
		try
		{
			$request_input = $request->input();
			$filter_by = '';
			$filterBy = '';
			$orderby_array = [];
			$order_by = '';
			$date = array();
			if (isset($request_input['$filter'])) {
				$filterBy = $request_input['$filter'];
			} elseif (isset($request_input['%24filter'])) {
				$filterBy = urldecode($request_input['%24filter']);
			}
			//   \Log::info($request_input);

			if ($request->input('$orderby')) {
				//checking for sorting
				$order = explode(' ', $request->input('$orderby'));
				$order_query_field = $order[0]; //on which field sorting need to be done
				$order_query_type = $order[1]; //sort type asc or desc
				$order_by_type = 'desc';

				if ($order_query_type == 'asc') {
					$order_by_type = 'asc';
				}

				if (isset($grid_fields[$order_query_field])) {
					//getting appropriate table field based on grid field
					$order_by = $grid_fields[$order_query_field];
				}
				$orderby_array = $order_by . " " . $order_by_type;
			}

			if (isset($filterBy) && $filterBy != '') {
				$filter_explode = explode(' and ', $filterBy);

				foreach ($filter_explode as $filter_each) {
					$filter_each_explode = explode(' ', $filter_each);
					$length = count($filter_each_explode);
					$filter_query_field = '';
					if ($length > 3) {
						for ($i = 0; $i < $length - 2; $i++) {
							$filter_query_field .= $filter_each_explode[$i] . " ";
						}

						$filter_query_field = trim($filter_query_field);
						$filter_query_operator = $filter_each_explode[$length - 2];
						$filter_query_value = $filter_each_explode[$length - 1];
					} else {
						$filter_query_field = $filter_each_explode[0];
						$filter_query_operator = $filter_each_explode[1];
						$filter_query_value = $filter_each_explode[2];
					}
					if (strpos($filter_each, ' or ') !== false) {
						$query_field_arr = explode(' or ', $filter_each);
						foreach ($query_field_arr as $query_field_data) {
							$filter = explode(' ', $query_field_data);
							$filter_query_field = $filter[0];
							$filter_query_operator = $filter[1];
							$filter_query_value = $filter[2];

							if (strpos($filter_query_field, 'day(') !== false) {
								$start = strpos($filter_query_field, '(');
								$end = strpos($filter_query_field, ')');
								$filter_query_field = substr($filter_query_field, $start + 1, $end - $start - 1);
								$date[$filter_query_field]["value"]['day'] = ($filter_query_value < 10) ? '0' . $filter_query_value : $filter_query_value;
								continue;
							} elseif (strpos($filter_query_field, 'month(') !== false) {
								$start = strpos($filter_query_field, '(');
								$end = strpos($filter_query_field, ')');
								$filter_query_field = substr($filter_query_field, $start + 1, $end - $start - 1);
								$date[$filter_query_field]["value"]['month'] = ($filter_query_value < 10) ? '0' . $filter_query_value : $filter_query_value;
								continue;
							} elseif (strpos($filter_query_field, 'year(') !== false) {
								$start = strpos($filter_query_field, '(');
								$end = strpos($filter_query_field, ')');
								$filter_query_field = substr($filter_query_field, $start + 1, $end - $start - 1);
								$date[$filter_query_field]["value"]['year'] = $filter_query_value;
								$date[$filter_query_field]["operator"] = $filter_query_operator;
								$filter_query_operator = $date[$filter_query_field]['operator'];
								$filter_query_value = implode('-', array_reverse($date[$filter_query_field]['value']));
							}
						}
					} else {
						if (strpos($filter_query_field, 'day(') !== false) {
							$start = strpos($filter_query_field, '(');
							$end = strpos($filter_query_field, ')');
							$filter_query_field = substr($filter_query_field, $start + 1, $end - $start - 1);
							$date[$filter_query_field]["value"]['day'] = ($filter_query_value < 10) ? '0' . $filter_query_value : $filter_query_value;
							continue;
						} elseif (strpos($filter_query_field, 'month(') !== false) {
							$start = strpos($filter_query_field, '(');
							$end = strpos($filter_query_field, ')');
							$filter_query_field = substr($filter_query_field, $start + 1, $end - $start - 1);
							$date[$filter_query_field]["value"]['month'] = ($filter_query_value < 10) ? '0' . $filter_query_value : $filter_query_value;
							continue;
						} elseif (strpos($filter_query_field, 'year(') !== false) {
							$start = strpos($filter_query_field, '(');
							$end = strpos($filter_query_field, ')');
							$filter_query_field = substr($filter_query_field, $start + 1, $end - $start - 1);
							$date[$filter_query_field]["value"]['year'] = $filter_query_value;
							$date[$filter_query_field]["operator"] = $filter_query_operator;
							$filter_query_operator = $date[$filter_query_field]['operator'];
							$filter_query_value = implode('-', array_reverse($date[$filter_query_field]['value']));
						}
						reset($date);
					}

					$filter_query_field_substr = substr($filter_query_field, 0, 7);
					if ($filter_query_field_substr == 'startsw' || $filter_query_field_substr == 'endswit' || $filter_query_field_substr == 'indexof' || $filter_query_field_substr == 'tolower') {
						//Here we are checking the filter is of which type startwith, endswith, contains, doesn't contain, equals, doesn't equal

						if ($filter_query_field_substr == 'startsw') {
							$filter_query_field_value_array = explode("'", $filter_query_field);
							//extracting the input filter value between single quotes, example: 'value'

							$filter_value = $filter_query_field_value_array[1] . '%';
//                            $filter_by[] =  $filter_query_field . ' like ' . $filter_value;
							foreach ($grid_fields as $key => $value) {
								if (strpos($filter_query_field, '(' . $key . ')') != 0) {
									//getting the filter field name
									$starts_with_value = $grid_fields[$key] . ' like ' . $filter_value;
									$filter_by[] = $starts_with_value;
								} else {
									$starts_with_value = "";
								}
							}
						}

						if ($filter_query_field_substr == 'endswit') {
							$filter_query_field_value_array = explode("'", $filter_query_field);
							//extracting the input filter value between single quotes, example: 'value'

							$filter_value = '%' . $filter_query_field_value_array[1];
//                            $filter_by[] =  $filter_query_field . ' like ' . $filter_value;
							foreach ($grid_fields as $key => $value) {
								if (strpos($filter_query_field, '(' . $key . ')') != 0) {
									//getting the filter field name
									$ends_with_value = $grid_fields[$key] . ' like ' . $filter_value;
									$filter_by[] = $ends_with_value;
								} else {
									$ends_with_value = "";
								}
							}
						}

						if ($filter_query_field_substr == 'tolower') {
							$filter_query_value_array = explode("'", $filter_query_value);
							//extracting the input filter value between single quotes, example: 'value'

							$filter_value = $filter_query_value_array[1];
							if ($filter_query_operator == 'eq') {
								$like = ' = ';
							} else {
								$like = ' != ';
							}
//                            $filter_by[] = $filter_query_field . $like . $filter_value;
							foreach ($grid_fields as $key => $value) {
								if (strpos($filter_query_field, '(' . $key . ')') != 0) {
									//getting the filter field name
									$to_lower_value = $grid_fields[$key] . $like . $filter_value;
									$filter_by[] = $to_lower_value;
								} else {
									$to_lower_value = "";
								}
							}
						}

						if ($filter_query_field_substr == 'indexof') {
							$filter_query_value_array = explode("'", $filter_query_field);
							//extracting the input filter value between single quotes ex 'value'

							$filter_value = '%' . $filter_query_value_array[1] . '%';

							if ($filter_query_operator == 'ge') {
								$like = ' like ';
							} else {
								$like = ' not like ';
							}
//                            $filter_by[] = $filter_query_field . $like . $filter_value;
							foreach ($grid_fields as $key => $value) {
								if (strpos($filter_query_field, '(' . $key . ')') != 0) {
									//getting the filter field name
									$indexof_value = $grid_fields[$key] . $like . $filter_value;
									$filter_by[] = $indexof_value;
								} else {
									$indexof_value = "";
								}
							}
						}
					} else {
						if ($filter_query_field == 'created_at') {
							$filter_operator = ' like ';
//                            $filter_query_value = $filter_query_value.'';
						} else {
							switch ($filter_query_operator) {
							case 'eq':
								$filter_operator = ' = ';
								break;

							case 'ne':
								$filter_operator = ' != ';
								break;

							case 'gt':
								$filter_operator = ' > ';
								break;

							case 'lt':
								$filter_operator = ' < ';
								break;

							case 'ge':
								$filter_operator = ' >= ';
								break;

							case 'le':
								$filter_operator = ' <= ';
								break;
							}
						}

						if (isset($grid_fields[$filter_query_field])) {
							//getting appropriate table field based on grid field
							$filter_field = $grid_fields[$filter_query_field];
						}
						if (strpos($filter_query_value, 'DateTime') !== false && $filter_field == 'last_order_date') {
							$temp = str_replace("DateTime'", '', $filter_query_value);
							$tempArray = explode('T', $temp);
							$filter_query_value = isset($tempArray[0]) ? $tempArray[0] : $filter_query_value;
						}
						$filter_by[] = $filter_field . $filter_operator . '"' . $filter_query_value . '"';
					}
				}
			}
//            Log::info(DB::getQueryLog());

			return $filter_by;
		} catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
		}
	}

//new db getPendingMessageList

	public function getPendingMessageList($id = null) {
		try
		{
			if ($id) {

	$pendingMessagesCollection = DB::table('communication')
				->join('communication_details', 'communication.id', '=', 'communication_details.communication_id')				
				->where('communication.id',$id)
				->select('communication_details.mobile_numbers as mobile_numbers',
					'communication_details.status as communication_details_status',
					'communication_details.comm_det_id as comm_det_id',
					'communication.message_type as message_type',
					'communication.status as status',
					'communication.id as id',
					'communication.sms_status as sms_status',
					'communication.push_status as push_status',
					'communication.sms_sent_count as sms_sent_count',
					'communication.push_sent_count as push_sent_count',
					'communication.count_mobile_numbers as count_mobile_numbers',
				  'communication.message as message',
			      'communication.created_at as created_at',
		          'communication.created_by as created_by',
		      'communication.created_by_name as created_by_name')
				->where('communication_details.status',0)->get()->all();


			
				log::info($pendingMessagesCollection);
			} else {
				$pendingMessagesCollection = DB::table('communication')
				->join('communication_details', 'communication.id', '=', 'communication_details.communication_id')
					->select('communication_details.mobile_numbers as mobile_numbers',
					'communication_details.status as communication_details_status',
						'communication_details.comm_det_id as comm_det_id',
							'communication.id as id',
					'communication.message_type as message_type',
					'communication.status as status',
					'communication.sms_status as sms_status',
					'communication.push_status as push_status',
					'communication.sms_sent_count as sms_sent_count',
					'communication.push_sent_count as push_sent_count',
					'communication.count_mobile_numbers as count_mobile_numbers',
				  'communication.message as message',
			      'communication.created_at as created_at',
		          'communication.created_by as created_by',
		          'communication.created_by_name as created_by_name')			
				//->where('communication.id',$id)
				->where('communication_details.status',0)->get()->all();
			}

//            echo "<pre>";print_R($pendingMessagesCollection);die;
			return $pendingMessagesCollection;
		} catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
		}
	}

//db updateStatus

	public function updateStatus($pushMessageId, $messageType, $messageTypeStatus) {
		try
		{

			$pendingMessagesCollection = DB::table('communication')->where(['id' => $pushMessageId, 'status' => 0])
				->select('sms_status', 'push_status', 'message_type')->first();
			//   Log::info($pendingMessagesCollection);
			if (!empty($pendingMessagesCollection)) {
				$response = $pendingMessagesCollection;

				$messageTypeInfo = property_exists($response, 'message_type') ? explode(",", $response->message_type) :'';

				if (!empty($messageTypeInfo) && in_array($messageType, $messageTypeInfo)) {

					if (property_exists($response, $messageTypeStatus)) {

						//print_r($pushMessageId);

						DB::table('communication')->where('id', $pushMessageId)
							->update(['status' => 1]);
					}
				} else {

					DB::table('communication')->where('id', $pushMessageId)
						->update(['status' => 1]);
				}
			}
		} catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
		}
	}


//db updateStatusDetails

	public function updateStatusDetails($pushMessageId, $comm_det_id) {
		try
		{


		

					DB::table('communication_details')->where('communication_id', $pushMessageId)
					->where('comm_det_id', $comm_det_id)
						->update(['status' => 1]);
			
			
		} catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
		}
	}





	//db updateCount
	public function updateCount($Id, $messageType, $count) {
			
		try
		{
			if ($Id != '') {
				if ($messageType == 'sms') {
					$sendCount = DB::table('communication')->where(['id' => $Id])
						->select('sms_sent_count')->first();
					$response = $sendCount->sms_sent_count;

			
					if (!empty($sendCount)) {
//                        $smsCount = property_exists($response, 'sms_sent_count') ? $response->sms_sent_count : 0;
						$smsCount = isset($response) ? $response : 0;

					

					
						DB::table('communication')->where('id', $Id)
							->update(['sms_sent_count' => ($smsCount + $count)]);
					}
				} else {
					$sendCount = DB::table('communication')->where(['id' => $Id])
						->select('push_sent_count')->first();
					$response = $sendCount->push_sent_count;
					if (!empty($sendCount)) {
//                        $pushCount = property_exists($response, 'push_sent_count') ? $response->push_sent_count : 0;
						$smsCount = isset($response) ? $response : 0;

			
						DB::table('communication')->where('id', $Id)
							->update(['push_sent_count' => ($smsCount + $count)]);
					}
				}
			}
		} catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
		}
	}

//db updateMessageQueue
	public function updateMessageQueue($pushMessageId, $messageType) {
		try
		{
			if ($pushMessageId != '') {
				if ($messageType == 'sms') {
					DB::table('communication')->where('id', $pushMessageId)
						->update(['sms_status' => 1]);
					$this->updateStatus($pushMessageId, 'push', 'push_status');
				} elseif ($messageType == 'push') {
					DB::table('communication')->where('id', $pushMessageId)
						->update(['push_status' => 1]);
					$this->updateStatus($pushMessageId, 'sms', 'sms_status');
				}
			}
		} catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
		}
	}

  public function sendSMSGetway($referenceId, $userId, $number, $message, $state = null, $ishex = null, $dcs = null,$comm_det_id)
    {
        $postfields['user'] = Config::get('dmapi.SMS_USER');
        $postfields['senderID'] = Config::get('dmapi.SMS_SENDER_ID');
        $postfields['msgtxt'] = $message;
        $postfields['ishex'] = ($ishex == null) ? 0 : $ishex;
        $postfields['dcs'] = ($dcs == null) ? 0 : $dcs;
        $postfields['receipientno'] = (is_array($number)) ? implode(',', $number) : $number;
        $postfields['state'] = ($state == null) ? 4 : $state;
        $postfields = http_build_query($postfields);
       $response = $this->curlRequest(Config::get('dmapi.SMS_URL'), $postfields);
 //$response = 1;
         date_default_timezone_set('Asia/Kolkata');
			$data['created_at'] = date('Y-m-d H:i:s');	

			$request_type = "sms";
			$response = DB::table('message_history')->insert(array('reference_id' =>$referenceId, 'requested_by' => $userId, 'request_type' => $request_type,
				'comm_det_id' =>$comm_det_id,
				'number' =>  (is_array($number)) ? implode(',', $number) : $number,
				'message' =>$message,
				'response' => $response,
				'created_on' =>$data['created_at'],
			
			));


      //   $tableName = 'message_history';
      //   $insertData['reference_id'] = $referenceId;
      //   $insertData['requested_by'] = $userId;
      //   $insertData['request_type'] = 'sms';
      //   $insertData['number'] = $number;
      //   $insertData['message'] = json_encode($message);
      //   $insertData['response'] = json_encode($response);
      //   date_default_timezone_set('Asia/Kolkata');
      // //  $insertData['created_on'] = new \MongoDate();

      //  $insertData['created_on'] = new \MongoDB\BSON\UTCDateTime(strtotime('yesterday') * 1000);

      //   $mongoRepo = new MongoRepo();
      //   $mongoRepo->insert($tableName, $insertData);
        return $response;
    }

    private function curlRequest($url, $postfields)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
        $buffer = curl_exec($ch);
        if (empty($buffer))
        {
            return false;
        } else
        {
            return $buffer;
        }
    }


    	public function validateNumbers($Id, $comm_det_id) {
		try
		{

			
			if ($Id != '' && $comm_det_id != '') {
				$response = DB::table('message_history')
					->where('reference_id', $Id)
				    ->where('comm_det_id' , $comm_det_id)
					->first();
				if (!empty($response)) {
					return false;
				}
			}
			return true;
		} catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
		}
	}

	  public function pushNotifications($message, $tokenDetails,$type ='default',$sentBy = 'Ebutor',$link ='',$pushMessageId='',$pushMessageCreatedBy='',$data='' , $start  = 0 , $end = 0,$comm_det_id,$mobiles)
    {    








        $queue = new Queue();
        $tokenDetails1  = $tokenDetails;
        $tokenDetails = json_encode($tokenDetails);
        $tokenDetails = base64_encode($tokenDetails);
        $args = array(  'ConsoleClass' => 'notification', 
                        'arguments' => array($message,$tokenDetails,$type,$sentBy,$link,$pushMessageId,$pushMessageCreatedBy,$data));
        $token_job = $queue->enqueue('default', 'ResqueJobRiver', $args);


            date_default_timezone_set('Asia/Kolkata');
			$created_at = date('Y-m-d H:i:s');	

			$request_type = "push";
			$response = DB::table('message_history')->insert(array('reference_id' =>$pushMessageId, 'requested_by' => $pushMessageId, 'request_type' => $request_type,
				'comm_det_id' =>$comm_det_id,
				'number' =>  (is_array($mobiles)) ? implode(',', $mobiles) : $mobiles,
				'message' =>$message,
				'response' => 1,
				'created_on' =>$created_at,
			
			));




        
    }  
	

		public function getSentMessageList($id) {
		try
		{
			$response = [];
			if ($id != '') {
//                \DB::connection($this->connection)->enableQueryLog();
				$response =  DB::table('message_history')					
					->where(['reference_id' => $id])
					->select('request_type', 'number', 'message', 'response')
					->get();
//                Log::info(\DB::connection($this->connection)->getQueryLog());
			}
			return $response;
		} catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
		}
	}
}