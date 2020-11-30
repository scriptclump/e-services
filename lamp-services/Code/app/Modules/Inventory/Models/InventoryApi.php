<?php

namespace App\Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Log;

class InventoryApi extends Model {

    public function authenticatToken($lpToken) {
        try {
            $user_id = json_decode(json_encode(DB::table("users")->where("lp_token", $lpToken)->orWhere("password_token", $lpToken)->pluck("user_id")->all()), true);
            if (!empty($user_id)) {
                return $user_id[0];
            } else {
                return "Token not found";
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getRolesByUserId($userId) {
        try {
            $role_ids = DB::table("user_roles")->where("user_id", $userId)->pluck("role_id")->all();
            if (!empty($role_ids)) {
                return $role_ids;
            } else {
                return "Role does not exist";
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getTicketsByRoleId($roleId) {
        try {
            $role_ids = implode("|", $roleId);
            $raw_query = "SELECT * FROM
                (SELECT innertbl.*
                 ,(SELECT master_lookup_name FROM master_lookup AS ml WHERE ml.`value`=approuter.status_from_id ) AS 'PreviousStatus'
                 ,(SELECT master_lookup_name FROM master_lookup AS ml WHERE ml.`value`=approuter.condition_id ) AS 'Condition'
                 ,(SELECT master_lookup_name FROM master_lookup AS ml WHERE ml.`value`=approuter.status_to_id ) AS 'CurrentStatus'
                 ,(SELECT rls.name FROM roles rls WHERE rls.role_id=approuter.`next_lbl_role`) AS 'PendingOn',
                 IF( approuter.`is_final`=0, 'Open', 'Closed' ) AS 'TicketFinalStatus',
                 (SELECT CONCAT(firstname,' ', lastname) FROM users AS usr WHERE usr.user_id=approuter.user_id) AS 'PreviouslyApprovedBy',
                 approuter.`created_at`, approuter.`awf_comment` 
                 FROM (
                  SELECT appr.`awf_history_id`, appr.`awf_for_type`, appr.`awf_for_type_id`, appr.`awf_for_id`, 
                  CONCAT('TKT-', appr.awf_for_id) AS TicketNumber,
                  IF( appr.`is_final`=0, 'Open', 'Closed' ) AS 'TicketStatusAsPerRole' 
                  FROM appr_workflow_history AS appr
                  INNER JOIN appr_workflow_status_new AS awh ON awh.awf_for_id = appr.`awf_for_type_id`
                  WHERE appr.`awf_for_type` in ('Inventory Bluk Upload', 'Inventory Bulk Upload')
                  AND appr.`awf_history_id` IN (
                   SELECT MAX(awf_history_id) 
                   FROM appr_workflow_history AS apprinner 
                   WHERE apprinner.`awf_for_id` = appr.`awf_for_id` 
                   AND apprinner.`awf_for_type`= appr.`awf_for_type` 
                   AND CONCAT(', ', apprinner.`next_lbl_role`, ', ') REGEXP ' [[:<:]](" . $role_ids . ")[[:>:]]' 
                   GROUP BY apprinner.`awf_for_id`
                  )
                 ) AS innertbl 
                 INNER JOIN appr_workflow_history AS approuter 
                 ON approuter.awf_for_type_id=innertbl.awf_for_type_id
                 AND approuter.awf_for_id=innertbl.awf_for_id 
                 AND approuter.awf_history_id = ( 
                  SELECT MAX(apprinner1.awf_history_id) 
                  FROM appr_workflow_history AS apprinner1 
                  WHERE apprinner1.`awf_for_id` = approuter.`awf_for_id` 
                  AND apprinner1.`awf_for_type`= approuter.`awf_for_type` 
                  GROUP BY apprinner1.`awf_for_id`
                 )
                ) AS fullquery ORDER BY created_at DESC, TicketStatusAsPerRole DESC";
            $all_tickets = DB::select(DB::raw($raw_query));
            return $all_tickets;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getTicketsByRoleIdForCycleCount($roleId,$legalid) {
        try {
            $role_ids = implode("|", $roleId);

            $raw_query = "SELECT * FROM
                (SELECT innertbl.*
                 ,(SELECT master_lookup_name FROM master_lookup AS ml WHERE ml.`value`=approuter.status_from_id ) AS 'PreviousStatus'
                 ,(SELECT master_lookup_name FROM master_lookup AS ml WHERE ml.`value`=approuter.condition_id ) AS 'Condition'
                 ,(SELECT master_lookup_name FROM master_lookup AS ml WHERE ml.`value`=approuter.status_to_id ) AS 'CurrentStatus'
                 ,(SELECT rls.name FROM roles rls WHERE rls.role_id=approuter.`next_lbl_role`) AS 'PendingOn',
                 IF( approuter.`is_final`=0, 'Open', 'Closed' ) AS 'TicketFinalStatus',
                 (SELECT CONCAT(firstname,' ', lastname) FROM users AS usr WHERE usr.user_id=approuter.user_id) AS 'PreviouslyApprovedBy',
                 approuter.`created_at`, approuter.`awf_comment` 
                 FROM (
                  SELECT appr.`awf_history_id`, appr.`awf_for_type`, appr.`awf_for_type_id`, appr.`awf_for_id`, 
                  CONCAT('TKT-', appr.awf_for_id) AS TicketNumber,
                  IF( appr.`is_final`=0, 'Open', 'Closed' ) AS 'TicketStatusAsPerRole' 
                  FROM appr_workflow_history AS appr
                  INNER JOIN appr_workflow_status_new AS awh ON awh.awf_for_id = appr.`awf_for_type_id`
                  WHERE appr.`awf_for_type` in ('Inventory Bluk Upload', 'Inventory Bulk Upload')
                  AND awh.`legal_entity_id` = ".$legalid."
                  AND appr.`awf_history_id` IN (
                   SELECT MAX(awf_history_id) 
                   FROM appr_workflow_history AS apprinner 
                   WHERE apprinner.`awf_for_id` = appr.`awf_for_id` 
                   AND apprinner.`awf_for_type`= appr.`awf_for_type` 
                   AND CONCAT(', ', apprinner.`next_lbl_role`, ', ') REGEXP ' [[:<:]](" . $role_ids . ")[[:>:]]' 
                   GROUP BY apprinner.`awf_for_id`
                  )
                 ) AS innertbl 
                 INNER JOIN appr_workflow_history AS approuter 
                 ON approuter.awf_for_type_id=innertbl.awf_for_type_id
                 AND approuter.awf_for_id=innertbl.awf_for_id 
                 AND approuter.awf_history_id = ( 
                  SELECT MAX(apprinner1.awf_history_id) 
                  FROM appr_workflow_history AS apprinner1 
                  WHERE apprinner1.`awf_for_id` = approuter.`awf_for_id` 
                  AND apprinner1.`awf_for_type`= approuter.`awf_for_type` 
                  GROUP BY apprinner1.`awf_for_id`
                 )
                ) AS fullquery ORDER BY created_at DESC, TicketStatusAsPerRole DESC";
            $all_tickets = DB::select(DB::raw($raw_query));
            return $all_tickets;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

}
