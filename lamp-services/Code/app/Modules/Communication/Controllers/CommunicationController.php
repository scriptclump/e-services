<?php

/*
 * Filename: CommunicationController.php
 * Description: This file is used for sms and push notifications
 * Author: Ebutor <info@ebutor.com>
 * Copyright: ebutor@2016
 * Version: v1.0
 * Created date: 02 Feb 2017
 * Modified date: 02 Feb 2017
 */

namespace App\Modules\Communication\Controllers;

use App\Central\Repositories\RoleRepo;
use App\Http\Controllers\BaseController;
use App\Modules\Communication\Models\Communication;
use App\Modules\Communication\Models\CommunicationMongoModel;
use Illuminate\Http\Request;
use Input;
use Log;
use View;

class CommunicationController extends BaseController {

	public function __construct() {
		parent::__construct();
	}

	public function index() {
		try
		{
			parent::Title('Communications');
			$communicationModel = new Communication();
			$dcDetails = $communicationModel->getDcData();
			$rolesList = $communicationModel->getRolesList();
			$roleRepo = new RoleRepo();
			$addNewMessageAccess = $roleRepo->checkPermissionByFeatureCode('MES002');
			return View('Communication::list')->with(['dc_data' => $dcDetails,
				'add_button_access' => $addNewMessageAccess,
				'role_list' => $rolesList]);
		} catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
		}
	}

	public function getAllMessages(Request $request) {
		try
		{
			$status = false;
			$message = '';
			$data = [];
			$count = 0;
			$messages = [];

			$communicationMongoModel = new Communication();
			$communicationMongoModelDetails = $communicationMongoModel->getAllMessages($request);
			if (!empty($communicationMongoModelDetails)) {
				$messageDetails = isset($communicationMongoModelDetails['Records']) ? $communicationMongoModelDetails['Records'] : [];
				$messageCount = isset($communicationMongoModelDetails['totalUserCount']) ? $communicationMongoModelDetails['totalUserCount'] : 0;
			}
			if (!empty($messageDetails)) {
				$status = true;
				$message = 'Recieved data';
				$i = 0;
				foreach ($messageDetails as $messageData) {
					//$messageData = $messageData->toArray();
					$id = isset($messageData->id) ? $messageData->id : '';
					$sms_sent_count = isset($messageData->sms_sent_count) ? $messageData->sms_sent_count : 0;
					$push_sent_count = isset($messageData->push_sent_count) ? $messageData->push_sent_count : 0;
					$messageData->message_type = isset($messageData->message_type) ? strtoupper($messageData->message_type) : '';
					$messageData->message = isset($messageData->message) ? $messageData->message : '';
//                    $messageData['mobile_numbers'] = isset($messageData['mobile_numbers']) ? count($messageData['mobile_numbers']) : $messageData['mobile_numbers'];
					if (strlen($messageData->message) > 100) {
						$messageData->message = substr($messageData->message, 0, 100) . "...";
					}
					if ($id != '' && ($sms_sent_count > 0 || $push_sent_count > 0)) {
						$messageData->actions = '<span style="padding-left:20px;" >'
							. '<a href="/communication/download/' . $id . '"><i class="fa fa-download"></i></a></span>';
					} else {
						$messageData->actions = '';
					}
					$messages[] = $messageData;
				}
				$count = $messageCount;
			}
			return json_encode(['status' => $status, 'message' => $message, 'Records' => $messages, 'totalMessageCount' => $count]);
		} catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
		}
	}

	public function addAction() {
		try
		{
			parent::Title('New Message');
			$communicationModel = new Communication();
			$dcDetails = $communicationModel->getDcData();
			$rolesList = $communicationModel->getRolesList();
			return View('Communication::add')->with(['dc_data' => $dcDetails, 'role_list' => $rolesList]);
		} catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
		}
	}

	public function getHubs() {
		try
		{
			$data = Input::all();
			$communicationModel = new Communication();
			return $communicationModel->getHubData($data);
		} catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
		}
	}

	public function getBeats() {
		try
		{
			$data = Input::all();
			$communicationModel = new Communication();
			return $communicationModel->getBeatData($data);
		} catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
		}
	}

	public function sendData() {
		try
		{
			$data = Input::all();
			if (!empty($data)) {
				$communicationModel = new Communication();
				return $communicationModel->sendMessage($data);
			} else {
				return '';
			}
		} catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
		}
	}

	public function processPendingMessages() {
		try
		{
			$communicationModel = new Communication();
			return $communicationModel->processPendingMessages();
		} catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
		}
	}

	public function downloadAction($id) {
		try
		{
			if ($id != '') {
				$communicationModel = new Communication();
				$messageCollection = $communicationModel->getSentMessageList($id);
//                echo "<prE>";print_R($messageList);die;
				$messageList['headers'] = ['Message Type', 'Mobile Number', 'Message', 'Response'];
				$myArrayInit = json_decode(json_encode($messageCollection), true);
				$i = 0;
				foreach ($myArrayInit as $messagesData) {
					strtoupper($myArrayInit[$i]['request_type']);
					$myArrayInit[$i]['response'] = "Sent";
					if (isset($messagesData['_id'])) {
						unset($myArrayInit[$i]['_id']);
					}
					$i++;
				}
				date_default_timezone_set('Asia/Kolkata');
				$messageList = array_merge($messageList, $myArrayInit);
				\Excel::create('sent_message_list_' . date('Y-m-d_H_i_s'), function ($excel) use ($messageList) {
					$excel->setTitle('Sent Message List');
					$excel->sheet('Sent Message List', function ($sheet) use ($messageList) {
						$sheet->fromArray($messageList, null, 'A1', false, false);
					});

				})->download('xls');
			}
		} catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
		}
	}
}