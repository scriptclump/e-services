<?php

/*
 * Filename: InvMobileApprController.php
 * Description: This file is used for inventory approval from mobile
 * Author: Ebutor <info@ebutor.com>
 * Copyright: ebutor@2016
 * Version: v1.0
 * Created date: 7 November 2017
 * Modified date: 7 November 2017
 */

namespace App\Modules\Inventory\Controllers;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Log;
use App\Modules\Inventory\Models\Inventory;
use App\Modules\Inventory\Models\InventoryApi;
use UserActivity;
use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;
use App\Modules\Notifications\Models\NotificationsModel;
use App\Lib\Queue;
use DB;

class InvMobileApprController extends BaseController {

    public function __construct() {
        try {
            $this->_inventory = new Inventory();
            $this->_inventoryApi = new InventoryApi();
            $this->_approvalFlowMethod = new CommonApprovalFlowFunctionModel();
            $this->_notificationsMethod = new NotificationsModel();
            $this->_queue = new Queue();
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getInvBulkUploadTkts(Request $request) {
        try {
            $gettkts_params = json_decode($request->input("data"), true);
            $last_id = UserActivity::apiActivityLog("Inventory Bulk Upload Mobile", "getinvbulkuploadtkts", $request->input("data"), "getinvbulkuploadtkts api was requested", "");
            if ($gettkts_params["lp_token"] == "") {
                UserActivity::apiUpdateActivityLog($last_id, "Token is missing");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token is missing", "data" => array()));
            }

            $token_check = $this->_inventoryApi->authenticatToken($gettkts_params["lp_token"]);
            if ($token_check !== "Token not found") {
                $role_ids = $this->_inventoryApi->getRolesByUserId($token_check);
                if ($role_ids !== "Role does not exist") {
                    $tickets_list = $this->_inventoryApi->getTicketsByRoleId($role_ids);
                    if (!empty($tickets_list)) {
                        UserActivity::apiUpdateActivityLog($last_id, $tickets_list);
                        return json_encode(array("status_code" => 200, "status" => "Success", "message" => "All tickets for the given token", "data" => $tickets_list));
                    } else {
                        UserActivity::apiUpdateActivityLog($last_id, $tickets_list);
                        return json_encode(array("status_code" => 200, "status" => "Success", "message" => "There are no tickets for the given token", "data" => $tickets_list));
                    }
                } else {
                    UserActivity::apiUpdateActivityLog($last_id, "Role does not exit for the given token");
                    return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Role does not exit for the given token", "data" => array()));
                }
            } else {
                UserActivity::apiUpdateActivityLog($last_id, "You have already logged into the Ebutor System");
                return json_encode(array("status_code" => 401, "status" => "session", "message" => "You have already logged into the Ebutor System", "data" => array()));
            }
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Something went wrong from API side", "data" => array("error" => $ex->getMessage())));
        }
    }

    public function awfBulkUploadDetails(Request $request) {
        try {
            $bulkupdate_params = json_decode($request->input("data"), true);
            $last_id = UserActivity::apiActivityLog("Inventory Bulk Upload Mobile", "awfbulkuploaddetails", $request->input("data"), "awfbulkuploaddetails api was requested", "");
            if ($bulkupdate_params["lp_token"] == "") {
                UserActivity::apiUpdateActivityLog($last_id, "Token is missing");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token is missing", "data" => array()));
            }
            if ($bulkupdate_params["bulk_upload_id"] == "") {
                UserActivity::apiUpdateActivityLog($last_id, "Bulk upload id is missing");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Bulk upload id is missing", "data" => array()));
            }

            $token_check = $this->_inventoryApi->authenticatToken($bulkupdate_params["lp_token"]);
            if ($token_check !== "Token not found") {
                $bulkDetails = $this->_inventory->getBulkUploadDetails($bulkupdate_params["bulk_upload_id"]);
                if (!empty($bulkDetails)) {
                    $allprod_ids = array_column($bulkDetails, 'product_id');
                    $warehouse = array_unique(array_column($bulkDetails, 'le_wh_id'));
                    $all_current_inventory = $this->_inventory->getAllCurrentInventory($allprod_ids, $warehouse);
                    if (!empty($all_current_inventory)) {
                        $main_Arr = array();
                        foreach ($all_current_inventory as $key => $value) {
                            $main_Arr[$value['product_id']] = array("curr_dit_qty" => $value['dit_qty'], "curr_dnd_qty" => $value['dnd_qty']);
                        }

                        foreach ($bulkDetails as $key => $value) {
                            $bulkDetails[$key]['curr_dit_qty'] = $main_Arr[$value['product_id']]['curr_dit_qty'];
                            $bulkDetails[$key]['resulted_dit'] = ($main_Arr[$value['product_id']]['curr_dit_qty'] + $value['dit_diff']);
                            $bulkDetails[$key]['curr_dnd_qty'] = $main_Arr[$value['product_id']]['curr_dnd_qty'];
                            $bulkDetails[$key]['resulted_dnd'] = ($main_Arr[$value['product_id']]['curr_dnd_qty'] + $value['dnd_diff']);
                            $bulkDetails[$key]['new_dit_qty'] = ($value['old_dit_qty'] + $value['new_dit_qty']);
                            $bulkDetails[$key]['new_dnd_qty'] = ($value['old_dnd_qty'] + $value['new_dnd_qty']);
                        }

                        $approvalStatus = $bulkDetails[0]['approval_status'];
                        $workflowData = $this->_approvalFlowMethod->getApprovalFlowDetails('Inventory Bulk Upload', $approvalStatus, $token_check);
                        $approvalData = isset($workflowData['data']) ? $workflowData['data'] : array();
                        $currentStatusId = isset($workflowData['currentStatusId']) ? $workflowData['currentStatusId'] : "";

                        $finalData = array("bulkdetails" => $bulkDetails, "bulk_upload_id" => $bulkupdate_params["bulk_upload_id"], "approvalStatus" => $approvalData, "curr_status_id" => $currentStatusId);

                        UserActivity::apiUpdateActivityLog($last_id, $finalData);
                        return json_encode(array("status_code" => 200, "status" => "Success", "message" => "Details for the given bulk upload id", "data" => $finalData));
                    } else {
                        UserActivity::apiUpdateActivityLog($last_id, "Current inventory values didnt found for the given product ids and warehouse");
                        return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Current inventory values did not found for the given product ids and warehouse", "data" => array()));
                    }
                } else {
                    UserActivity::apiUpdateActivityLog($last_id, "No details found for the bulk upload id given");
                    return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "No details found for the bulk upload id given", "data" => array()));
                }
            } else {
                UserActivity::apiUpdateActivityLog($last_id, "You have already logged into the Ebutor System");
                return json_encode(array("status_code" => 401, "status" => "session", "message" => "You have already logged into the Ebutor System", "data" => array()));
            }
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Something went wrong from API side", "data" => array("error" => $ex->getMessage())));
        }
    }

    public function approveInvBulkUploadTkt(Request $request) {
        try {
            $apprtkt_params = json_decode($request->input("data"), true);
            $last_id = UserActivity::apiActivityLog("Inventory Bulk Upload Mobile", "approveinvbulkuploadtkt", $request->input("data"), "approveinvbulkuploadtkt api was requested", "");
            if ($apprtkt_params["lp_token"] == "") {
                UserActivity::apiUpdateActivityLog($last_id, "Token is missing");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token is missing", "data" => array()));
            }
            if ($apprtkt_params["next_status"] == "") {
                UserActivity::apiUpdateActivityLog($last_id, "Next status is missing");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Next status is missing", "data" => array()));
            }
            if ($apprtkt_params["approval_comment"] == "") {
                UserActivity::apiUpdateActivityLog($last_id, "Approval comment is missing");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Approval comment is missing", "data" => array()));
            }
            if ($apprtkt_params["current_status_id"] == "") {
                UserActivity::apiUpdateActivityLog($last_id, "Current status id is missing");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Current status id is missing", "data" => array()));
            }
            if ($apprtkt_params["bulk_upload_id"] == "") {
                UserActivity::apiUpdateActivityLog($last_id, "Bulk upload id is missing");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Bulk upload id is missing", "data" => array()));
            }

            $token_check = $this->_inventoryApi->authenticatToken($apprtkt_params["lp_token"]);
            if ($token_check !== "Token not found") {
                $bulk_upload_details = $this->_inventory->getBulkUploadDetails($apprtkt_params["bulk_upload_id"]);
                $message = "";
                $data_array = array();
                $message = "Your request was submitted.";
                if (!empty($bulk_upload_details)) {
                    if ($bulk_upload_details[0]["approval_status"] == 1) {
                        UserActivity::apiUpdateActivityLog($last_id, "This ticket was already approved");
                        return json_encode(array("status_code" => 200, "status" => "Success", "message" => "This ticket was already approved", "data" => array()));
                    } else {
                        $next_status_explode = explode(",", $apprtkt_params["next_status"]);
                        if ($next_status_explode[1] == 1) {
                            $args = array();
                            $user_ids = $this->_notificationsMethod->getUsersByCode("INVAPPR");
                            $args["email"] = $this->_inventory->userEmailsByIds($user_ids);
                            $args["file"] = $this->_inventory->getUploadedFile($apprtkt_params["bulk_upload_id"])[0]['filepath'];
                            $args["user"] = $token_check;
                            $encoded_args = base64_encode(json_encode($args));

                            $queue_args = array("ConsoleClass" => 'InventoryFinalApprovalMail', 'arguments' => array('emails' => $encoded_args));
                            $this->_queue->enqueue('default', 'ResqueJobRiver', $queue_args);

                            $invNegativeValuesArray = $this->_inventory->updateInventoryTableforBulk($apprtkt_params["bulk_upload_id"]);
                            $message = "Your request was submitted.";
                            if(!empty($invNegativeValuesArray))
                            {
                                $next_status_explode[1] = 57089;
                                $next_status_explode[0] = 57089;
//                                Log::info("SOH transter failed.....");
                                $data_array = $invNegativeValuesArray;
                                $this->_inventory->revertInventoryTableforBulk($apprtkt_params["bulk_upload_id"]);
                            }
  //                          Log::info("Inventory bulk upload is done.....");
                        } elseif ($next_status_explode[0] == 57089) {
                            $this->_inventory->revertInventoryTableforBulk($apprtkt_params["bulk_upload_id"]);
                        }
                        if($next_status_explode[1] == 0)
                        {
                            $next_status_explode[1] = $next_status_explode[0];
                        }
                        $this->_inventory->updateTrackingTableWithStatusforBulk($next_status_explode[1], $apprtkt_params["bulk_upload_id"]);
                      //  Log::info("going to work flow----");
                        //Log::info( $apprtkt_params["bulk_upload_id"].'__bulkupload id__'.$apprtkt_params["current_status_id"].'________current status______'.$next_status_explode[0].'_____next sid____'.$apprtkt_params["approval_comment"].'____user token_______'.$token_check);
                        $this->_approvalFlowMethod->storeWorkFlowHistory('Inventory Bulk Upload', $apprtkt_params["bulk_upload_id"], $apprtkt_params["current_status_id"], $next_status_explode[0], $apprtkt_params["approval_comment"], $token_check);

                        UserActivity::apiUpdateActivityLog($last_id, $message);
                        return json_encode(array("status_code" => 200, "status" => "Success", "message" => $message, "data" => $data_array));
                    }
                } else {
                    UserActivity::apiUpdateActivityLog($last_id, "No details found for the given bulk upload id");
                    return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "No details found for the given bulk upload id", "data" => array()));
                }
            } else {
                UserActivity::apiUpdateActivityLog($last_id, "You have already logged into the Ebutor System");
                return json_encode(array("status_code" => 401, "status" => "session", "message" => "You have already logged into the Ebutor System", "data" => array()));
            }
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Something went wrong from API side", "data" => array("error" => $ex->getMessage())));
        }
    }
    public function approvalHistoryDetails(Request $request){
        try {
            $apprtkt_params = json_decode($request->input("data"), true);
            if ($apprtkt_params["lp_token"] == "") {
                UserActivity::apiUpdateActivityLog($last_id, "Token is missing");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token is missing", "data" => array()));
            }
            if ($apprtkt_params["appoval_id"] == "") {
                
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Approval id is missing", "data" => array()));
            }
            $token_check = $this->_inventoryApi->authenticatToken($apprtkt_params["lp_token"]);
           if ($token_check !== "Token not found") {
                $approvalID = $apprtkt_params["appoval_id"];
                    $queryData = "select *
                            ,(SELECT master_lookup_name FROM master_lookup AS ml WHERE ml.`value`=approuter.status_from_id ) AS 'PreviousStatus'
                            ,(SELECT master_lookup_name FROM master_lookup AS ml WHERE ml.`value`=approuter.condition_id ) AS 'Condition'
                            ,(SELECT master_lookup_name FROM master_lookup AS ml WHERE ml.`value`=approuter.status_to_id ) AS 'CurrentStatus'
                            ,(SELECT rls.name FROM roles rls WHERE rls.role_id=approuter.`next_lbl_role`) AS 'PendingOn'
                            ,(SELECT CONCAT(firstname,lastname) FROM users AS us WHERE us.user_id = approuter.`created_by`)AS 'UserNameLstAction'

                            FROM appr_workflow_history AS approuter  
                            WHERE approuter.awf_for_id = '".$approvalID."'
                            AND approuter.`awf_for_type` ='Inventory Bulk Upload'";
                    $historyDataTicket = DB::select(DB::raw($queryData));
                   
                    return json_encode(array("status_code" => 200, "status" => "Success", "message" => "Approval History Details.", "data" => $historyDataTicket));
                }else
                {
                    UserActivity::apiUpdateActivityLog($last_id, "You have already logged into the Ebutor System");
                    return json_encode(array("status_code" => 401, "status" => "session", "message" => "You have already logged into the Ebutor System", "data" => array()));
                }

        }
        catch (\Exception $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Something went wrong from API side", "data" => array("error" => $ex->getMessage())));
        }
    }

        public function getInvBulkUploadTktsForCycleCount(Request $request) {
        try {
            $gettkts_params = json_decode($request->input("data"), true);
            $last_id = UserActivity::apiActivityLog("Inventory Bulk Upload Mobile", "getinvbulkuploadtkts", $request->input("data"), "getinvbulkuploadtkts api was requested", "");
            if ($gettkts_params["lp_token"] == "") {
                UserActivity::apiUpdateActivityLog($last_id, "Token is missing");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Token is missing", "data" => array()));
            }

            if ($gettkts_params["legal_entity_id"] == "") {
                UserActivity::apiUpdateActivityLog($last_id, "Legal Entity missing");
                return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Legal Entity missing", "data" => array()));
            }

            $token_check = $this->_inventoryApi->authenticatToken($gettkts_params["lp_token"]);
            if ($token_check !== "Token not found") {
                $role_ids = $this->_inventoryApi->getRolesByUserId($token_check);
                //echo "<pre/>";print_r($role_ids);exit;
                if ($role_ids !== "Role does not exist") {
                    $tickets_list = $this->_inventoryApi->getTicketsByRoleIdForCycleCount($role_ids,$gettkts_params["legal_entity_id"]);
                    if (!empty($tickets_list)) {
                        UserActivity::apiUpdateActivityLog($last_id, $tickets_list);
                        return json_encode(array("status_code" => 200, "status" => "Success", "message" => "All tickets for the given token", "data" => $tickets_list));
                    } else {
                        UserActivity::apiUpdateActivityLog($last_id, $tickets_list);
                        return json_encode(array("status_code" => 200, "status" => "Success", "message" => "There are no tickets for the given token", "data" => $tickets_list));
                    }
                } else {
                    UserActivity::apiUpdateActivityLog($last_id, "Role does not exit for the given token");
                    return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Role does not exit for the given token", "data" => array()));
                }
            } else {
                UserActivity::apiUpdateActivityLog($last_id, "You have already logged into the Ebutor System");
                return json_encode(array("status_code" => 401, "status" => "session", "message" => "You have already logged into the Ebutor System", "data" => array()));
            }
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return json_encode(array("status_code" => 401, "status" => "Failed", "message" => "Something went wrong from API side", "data" => array("error" => $ex->getMessage())));
        }
    }
}
