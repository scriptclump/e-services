<?php
/*
FileName : approvalTicketModel.php
Author   : eButor
Description : Function needed for Index page.
CreatedDate : 28/Jul/2016
*/

//defining namespace
namespace App\Modules\ApprovalEngine\Models;
use DB;
use Session;
use Log;

class approvalTicketModel{

	public function viewAprovalTicketdata( $makeFinalSqlOuter, $orderBy, $page, $pageSize, $countBy='openTicketsTab' ){
		if($orderBy!=''){
            $orderBy = ' ORDER BY ' . $orderBy;
        }
        else{
            $orderBy = ' ORDER BY AssignedON desc';
        }
        $sqlWhrCls = '';
        $countLoop = 0;
		
		foreach ($makeFinalSqlOuter as $value) {
			if( $countLoop==0 ){
				$sqlWhrCls .= ' WHERE ' . $value;
			}elseif( count($makeFinalSqlOuter)==$countLoop ){
				$sqlWhrCls .= $value;
			}else{
				$sqlWhrCls .= ' AND ' .$value;
			}
			$countLoop++;
		}
        $role = str_replace(",", "|", Session::get('roles'));
		$userID = Session::get('userId');

		$statusFilter = "";
		if ($countBy=='openTicketsTab') {
			$statusFilter = " AND appr_workflow_history.`is_final` = 0";
		}elseif ($countBy=='closedTicketsTab') {
			$statusFilter = " AND appr_workflow_history.`is_final` = 1";
		}
		$sqlGeneralPart = $sqlQuery = $pageLimit = $sqlCountPart = "";

		$sqlGeneralPart ="select * from ( ";
		$CustomActionQuery = "CONCAT('<center>
					    <code>
					    <a href=  \"#\" onclick=\"viewhistory(\'',appr_workflow_history.awf_for_type,'\',\'',appr_workflow_history.awf_for_id,'\')\">
					    <i class=\"fa fa-eye\"></i>
					    
					    </code>
					    </center>') AS CustomAction ";
        
		$sqlQuery = "select  
							".$CustomActionQuery.",
							CONCAT('TKT-', appr_workflow_history.awf_for_id) AS 'TicketNumber',
							getMastLookupValue(appr_workflow_history.status_from_id) AS 'PreviousStatus'
							,getMastLookupValue(appr_workflow_history.condition_id) AS 'TickCondition'
							,getMastLookupValue(appr_workflow_history.status_to_id) AS 'CurrentStatus',
							IF( appr_workflow_history.`is_final`=0, 'Open', 'Closed' ) AS 'TicketStatus',
							GetUserName(appr_workflow_history.user_id,2) AS 'PreviouslyApprovedBy',
							getRolesNameById(appr_workflow_history.next_lbl_role) AS 'TicketPendingOn',
							appr_workflow_history.`created_at` AS 'created_at',
							appr_workflow_history.`awf_comment` AS 'awf_comment',
							appr_workflow_history.`awf_for_type_id` AS 'awf_for_type_id',
							getMastLookupValue(appr_workflow_history.`awf_for_type_id`) AS 'TicketType',
							appr_workflow_history.`awf_for_id` AS 'awf_for_id',
							appr_workflow_history.`created_at` as 'AssignedON',
							date_format(appr_workflow_history.`created_at`, '%d-%m-%Y') as 'AssignDate'
							FROM appr_workflow_history
							WHERE appr_workflow_history.`awf_history_id` IN(
								SELECT MAX(awf_history_id) 
									FROM appr_workflow_history AS apprinner 
										WHERE(
												CONCAT(\",\", apprinner.`next_lbl_role`, \",\") REGEXP \",(".$role."),\" 
								          		OR(
								          			apprinner.`created_by_manager` = '".$userID."' 
								          			AND apprinner.`next_lbl_role` = '75'
								        		)
								        		OR (
								          			apprinner.`ticket_created_by` = '".$userID."' 
								          			AND apprinner.`next_lbl_role` = '76'
								        		)
								    		)
									GROUP BY apprinner.`awf_for_type_id` ,apprinner.`awf_for_id`
								)".$statusFilter."
							) AS innersecond".$sqlWhrCls. $orderBy;
                DB::enableQueryLog();
        if($page!='' && $pageSize!=''){
        	$pageLimit = " LIMIT " . (int)($page*$pageSize) . ", " . $pageSize;
        }
		$allTicketData = DB::select(DB::raw($sqlGeneralPart. $sqlQuery . $pageLimit ));
		$i=0;
		foreach ($allTicketData as $key => $value) {
			$redirect_url = DB::table('appr_workflow_status_new')->where('awf_for_id',$value->awf_for_type_id)->select('redirect_url')->first();
			$redirect_url = str_replace('##',$value->awf_for_id,$redirect_url->redirect_url);
			if($value->TicketStatus == 'Open')
				$color = 'red';
			else
				$color = 'blue';
			$allTicketData[$i]->TicketDetails = '<span style="color:#3598dc; font-weight: bold;">Approval For - <a style="color:'.$color.';" href="' . $redirect_url.'" target="_blank">'.$value->TicketType .'('.$value->awf_for_id. ')'.'</a></span> <br><span style="color:#3598dc; font-style: italic;">Previously '.$value->TickCondition.' By '.$value->PreviouslyApprovedBy.' </span> ';
			$i++;
		}
		$TotalRecordsCount = count(DB::select(DB::raw($sqlGeneralPart.$sqlQuery )));
		return json_encode(array('results'=>$allTicketData, 'TotalRecordsCount'=>(int)($TotalRecordsCount)));
	}

	// Get Total Ticket Count as per Param
	public function getTicketCount($countBy='Open'){
		
		// get User Roles from Session
		$role = str_replace(",", "|", Session::get('roles'));
		$userID = Session::get('userId');

		// get User Roles from Session
	    $sqlQuery ="select COUNT(awf_history_id) AS TotalCount, SUM(CASE WHEN is_final=0 THEN 1 ELSE 0 END) AS opentickets,SUM(CASE WHEN is_final=1 THEN 1 ELSE 0 END) AS closed
					FROM appr_workflow_history WHERE appr_workflow_history.`awf_history_id` IN ( 

							SELECT MAX(awf_history_id) 
							FROM appr_workflow_history AS apprinner
							WHERE (
								CONCAT(\",\", apprinner.`next_lbl_role`, \",\") REGEXP \",(".$role."),\" 
								OR (
						          apprinner.`created_by_manager` = '".$userID."'
						          AND apprinner.`next_lbl_role` = '75'
						        ) 
						        OR (
						          apprinner.`ticket_created_by` = '".$userID."'
						          AND apprinner.`next_lbl_role` = '76'
						        )
						    )
						    
							GROUP BY apprinner.`awf_for_id`,apprinner.`awf_for_type_id`
						)";

		$allTicketData = DB::select(DB::raw($sqlQuery));
		if( isset($allTicketData[0]) ){
			$allTicketData = $allTicketData[0];
			$allTicketData = json_decode(json_encode($allTicketData),1);
		}
		return $allTicketData;
	}

	public function historyData($approvalType, $approvalID){
		
		$queryData = "select *
							,(SELECT master_lookup_name FROM master_lookup AS ml WHERE ml.`value`=approuter.status_from_id ) AS 'PreviousStatus'
							,(SELECT master_lookup_name FROM master_lookup AS ml WHERE ml.`value`=approuter.condition_id ) AS 'Condition'
							,(SELECT master_lookup_name FROM master_lookup AS ml WHERE ml.`value`=approuter.status_to_id ) AS 'CurrentStatus'
							,(SELECT rls.name FROM roles rls WHERE rls.role_id=approuter.`next_lbl_role`) AS 'PendingOn'
							,(SELECT CONCAT(firstname,lastname) FROM users AS us WHERE us.user_id = approuter.`created_by`)AS 'UserNameLstAction'

							FROM appr_workflow_history AS approuter  
							WHERE approuter.awf_for_id = '".$approvalID."'
							AND approuter.`awf_for_type` = '".$approvalType."'";
												
		$historyDataTicket = DB::select(DB::raw($queryData));

		return $historyDataTicket;	
	}

}