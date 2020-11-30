<?php
namespace App\Modules\Communication\Models;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use \Log;

class CommunicationMongoModel extends Eloquent {

	protected $connection = 'mongo';
	protected $table = 'message_queues';
	protected $primaryKey = '_id';
//    protected $dates = ['created_at'];

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
			$results = $this->whereIn('status', [0, 1])
//                    ->leftJoin('message_history', 'message_history.reference_id', '=', 'message_queue._id')
				->select('_id', 'message', 'message_type', 'count_mobile_numbers',
					'sms_sent_count', 'push_sent_count',
					'created_by', 'created_by_name', 'created_at');
//                    ->selectRaw('count(mobile_numbers) as count2');
			//                ->orderBy('created_at', 'DESC')
			//                ->skip(0)->take(20)->get();
			$page = (int) $request->input('page'); //Page number
			$pageSize = (int) $request->input('pageSize'); //Page size for ajax
			//            $notificationsModel = new NotificationsModel();
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
			//        echo "<pre>";print_R($orderby_array);die;
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

	public function storeData($data) {
		try
		{
			$inserID = '';
			if (!empty($data)) {
				$inserID = $this->insertGetId($data);
			}
			return $inserID;
		} catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
		}
	}
	public function getLogData() {
		try
		{

		} catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
		}
	}

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

	public function getPendingMessageList($id = null) {
		try
		{
			if ($id) {
				$pendingMessagesCollection = $this
					->where(['_id' => $id, 'status' => 0])
					->select('_id', 'sms_status', 'push_status', 'message', 'message_type', 'mobile_numbers', 'created_by', 'created_by_name')
					->get();
			} else {
				$pendingMessagesCollection = $this->where(['status' => 0])
					->select('_id', 'sms_status', 'push_status', 'message', 'message_type', 'mobile_numbers', 'created_by', 'created_by_name')
					->get();
			}

//            echo "<pre>";print_R($pendingMessagesCollection);die;
			return $pendingMessagesCollection;
		} catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
		}
	}

	public function updateMessageQueue($pushMessageId, $messageType) {
		try
		{
			if ($pushMessageId != '') {
				if ($messageType == 'sms') {
					$this->where('_id', $pushMessageId)
						->update(['sms_status' => 1]);
					$this->updateStatus($pushMessageId, 'push', 'push_status');
				} elseif ($messageType == 'push') {
					$this->where('_id', $pushMessageId)
						->update(['push_status' => 1]);
					$this->updateStatus($pushMessageId, 'sms', 'sms_status');
				}
			}
		} catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
		}
	}

	public function updateStatus($pushMessageId, $messageType, $messageTypeStatus) {
		try
		{
			$pendingMessagesCollection = $this->where(['_id' => $pushMessageId, 'status' => 0])
				->select('sms_status', 'push_status', 'message_type')->first();
			//   Log::info($pendingMessagesCollection);
			if (!empty($pendingMessagesCollection)) {
				$response = json_decode($pendingMessagesCollection);
				$messageTypeInfo = property_exists($response, 'message_type') ? $response->message_type : [];
				if (!empty($messageTypeInfo) && in_array($messageType, $messageTypeInfo)) {
					if (property_exists($response, $messageTypeStatus) && $response->$messageTypeStatus) {
						$this->where('_id', $pushMessageId)
							->update(['status' => 1]);
					}
				} else {
					$this->where('_id', $pushMessageId)
						->update(['status' => 1]);
				}
			}
		} catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
		}
	}

	public function updateCount($Id, $messageType, $count) {
		try
		{
			if ($Id != '') {
				if ($messageType == 'sms') {
					$sendCount = $this->where(['_id' => $Id])
						->select('sms_sent_count')->get();
					$response = json_decode($sendCount);
					if (!empty($response)) {
//                        $smsCount = property_exists($response, 'sms_sent_count') ? $response->sms_sent_count : 0;
						$smsCount = isset($response['sms_sent_count']) ? $response['sms_sent_count'] : 0;
						$this->where('_id', $Id)
							->update(['sms_sent_count' => ($smsCount + $count)]);
					}
				} else {
					$sendCount = $this->where(['_id' => $Id])
						->select('push_sent_count')->get();
					$response = json_decode($sendCount);
					if (!empty($response)) {
//                        $pushCount = property_exists($response, 'push_sent_count') ? $response->push_sent_count : 0;
						$smsCount = isset($response['push_sent_count']) ? $response['push_sent_count'] : 0;
						$this->where('_id', $Id)
							->update(['push_sent_count' => ($smsCount + $count)]);
					}
				}
			}
		} catch (\ErrorException $ex) {
			Log::error($ex->getMessage());
			Log::error($ex->getTraceAsString());
		}
	}

	public function validateNumbers($Id, $numbers) {
		try
		{
			if ($Id != '' && (int) $numbers > 0) {
				$response = \DB::connection($this->connection)->collection('message_history')
					->where(['reference_id' => $Id, 'number' => $numbers])
					->first(['response']);
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

	public function getSentMessageList($id) {
		try
		{
			$response = [];
			if ($id != '') {
//                \DB::connection($this->connection)->enableQueryLog();
				$response = \DB::connection($this->connection)
					->collection('message_history')
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
