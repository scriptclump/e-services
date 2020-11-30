<?php
/*
FileName : expensesAPIModel
Author   : eButor
Description :
CreatedDate : 26/Dec/2016
*/

//defining namespace
namespace App\Modules\ExpensesTracker\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use UserActivity;
use Utility;

class expensesAPIModel extends Model
{
    protected $table = 'expenses_main';
    protected $primaryKey = "exp_id";

    // Fetch all the expenses from the DB as per the filter inputs
    public function getAllExpensesFromDB($userID, $expType,  $withAdjustFlag=0){

        $userQuery = "";
        if($userID!=0){
            $userQuery = "where expmn.submited_by_id='" . $userID . "' AND";
        }

        if($expType=='ADV'){
            $expType = " WHERE RequestFor='Advance'";
        }else if($expType=='REM'){
            $expType = " WHERE RequestFor='Reimbursement'";
        }else if($expType=='ALL'){
            $expType = "";
        }

        if($withAdjustFlag == 1){
            $withAdjustFlag = $expType=="" ? " WHERE " : " AND ";
            $withAdjustFlag .= " (ActualAmount>AdjustedAmount OR AdjustedAmount IS NULL)";
        }else{
            $withAdjustFlag="";
        }

    	$sql_expenses = "select * from
        (
            select exp_id, exp_code AS 'ExpCdoe', 
        	(select master_lookup_name from master_lookup where value=expmn.exp_req_type) AS 'RequestFor', exp_subject AS 'ExpSubject', 
        	exp_actual_amount AS 'ActualAmount', exp_approved_amount AS 'ApprovedAmount', submit_date AS 'ExpDate',
            (select master_lookup_name from master_lookup where value=expmn.exp_appr_status) AS 'CurrentStatus', exp_appr_status AS 'CurrentStatusID',
            (SELECT SUM(det.exp_det_actual_amount) expenses_details FROM expenses_details AS det WHERE det.exp_id=expmn.exp_id GROUP BY det.exp_id) AS 'AdjustedAmount' 
        	FROM expenses_main as expmn " . $userQuery . " is_direct_advance=0 ORDER BY submit_date DESC, exp_id DESC
        ) as innertbl " . $expType . $withAdjustFlag;

    	$allData = DB::select(DB::raw($sql_expenses));

		return $allData;
    }

    public function getExpensesAsPerApprovalRoleFromDB($userID, $expType){

        //Get the roles of the User
        $dataQuery = "select group_concat(role_id) as 'Roles'
            FROM user_roles AS rls
            WHERE rls.`user_id`=".$userID;

        $dataQuery = DB::select(DB::raw($dataQuery));
        $userRoles = 0;
        if(count($dataQuery)>0){
            $userRoles = $dataQuery[0]->Roles;
        }

        // Filtaretion query for Expenses type
        if($expType=='ADV'){
            $expType = " and RequestFor='Advance'";
        }else if($expType=='REM'){
            $expType = " and RequestFor='Reimbursement'";
        }else if($expType=='ALL'){
            $expType = "";
        }

        // Get the individuals roleID
        $staticRoles = DB::table('roles')
                        ->select("role_id")
                        ->where("name", "=", "Initiator")
                        ->get()->all();
        $InitiatorID = $staticRoles[0]->role_id;
        // ===========================
        $staticRoles = DB::table('roles')
                        ->select("role_id")
                        ->where("name", "=", "ImmediateReporter")
                        ->get()->all();
        $reporterID = $staticRoles[0]->role_id;

        $staticRoles = $staticRoles=="" ? " "  : "OR ( Roles IN(".$reporterID.") AND ReportingID='".$userID."' ) OR ( Roles IN(".$InitiatorID.") AND submited_by_id='".$userID."' ) ";

        $sql_expenses = "select * FROM
                (
                    SELECT exp_id, exp_code AS 'ExpCdoe', 
                    (SELECT master_lookup_name FROM master_lookup WHERE VALUE=expmn.exp_req_type) AS 'RequestFor', exp_subject AS 'ExpSubject', 
                    exp_actual_amount AS 'ActualAmount', exp_approved_amount AS 'ApprovedAmount', submit_date AS 'ExpDate',
                    (SELECT master_lookup_name FROM master_lookup WHERE VALUE=expmn.exp_appr_status) AS 'CurrentStatus', exp_appr_status AS 'CurrentStatusID',
                    (SELECT GROUP_CONCAT(applied_role_id) FROM  appr_workflow_status_details AS apdet WHERE apdet.awf_status_id=expmn.exp_appr_status) AS 'Roles',
                    (SELECT CONCAT(firstname, ' ', lastname) FROM users AS usr WHERE usr.user_id=expmn.submited_by_id) AS 'SubmittedByName',

                    (SELECT reporting_manager_id FROM users AS usr WHERE usr.user_id=expmn.submited_by_id) AS 'ReportingID',expmn.submited_by_id

                    FROM expenses_main AS expmn ORDER BY submit_date DESC, exp_id DESC
                    
                ) AS innertbl WHERE Roles IN (".$userRoles.") " . $staticRoles . $expType;

        $allData = DB::select(DB::raw($sql_expenses));

        return $allData;
    }

    // Get taltal Approved Balance
    public function getTotalWalletAmount($userID){

        $sql_expenses = "select SUM(BalanceAmt) AS 'WalletTotal'
                        FROM
                        (
                            SELECT *, (exp_approved_amount-AdjustedAmt) AS 'BalanceAmt'
                            FROM
                            (
                                SELECT main.`exp_id`, main.`exp_approved_amount`, SUM(det.`exp_det_actual_amount`) AS 'AdjustedAmt'
                                FROM expenses_main main
                                INNER JOIN expenses_details det ON det.exp_id=main.`exp_id`

                                WHERE main.`exp_appr_status`=1
                                AND main.`submited_by_id`='".$userID."'
                                GROUP BY main.`exp_id`
                            ) AS innertbl
                        ) AS innertbl2";

        $allData = DB::select(DB::raw($sql_expenses));
        return $allData;
    }

    // Fetch expenses from DB By ID
    public function getAllExpensesByIDFromDB($expID){
    	$sql_expenses = "select exp_det_id, exp_det_actual_amount AS 'DetActualAmount', exp_det_approved_amount AS 'DetApprovedAmount', 
		(SELECT master_lookup_name FROM master_lookup WHERE VALUE=exp_det.exp_type) AS 'DetExpType', 
		exp_det_description AS 'DetDescription', exp_det.exp_det_proof as 'AttachedFiles' 
		FROM expenses_details AS exp_det
		WHERE exp_det.exp_id=" . $expID;

    	$allData = DB::select(DB::raw($sql_expenses));

		return $allData;
    }

    // Get Data from Master lookup as per the type
    public function getMasterLookupValueForExpFromDB(){


        $getMasterValueForRequest = DB::Table('master_lookup_categories as ms')
                        ->join('master_lookup as ml','ml.mas_cat_id', '=', 'ms.mas_cat_id')
                        ->select("ml.master_lookup_name", "ml.value")
                        ->where('mas_cat_name', '=', 'Expences Request For')
                        ->where('ml.is_active','=','1')
                        ->get()->all();

        $getMasterValueForRequestType = DB::Table('master_lookup_categories as ms')
                        ->join('master_lookup as ml','ml.mas_cat_id', '=', 'ms.mas_cat_id')
                        ->select("ml.master_lookup_name", "ml.value")
                        ->where('mas_cat_name', '=', 'Expenses Request For Type')
                        ->get()->all();

        $getMasterValueForExpType = DB::Table('master_lookup_categories as ms')
                        ->join('master_lookup as ml','ml.mas_cat_id', '=', 'ms.mas_cat_id')
                        ->select("ml.master_lookup_name", "ml.value")
                        ->where('mas_cat_name', '=', 'Expences Type')
                        ->get()->all();
                        

        $allData = array(
                'RequestType'       =>  $getMasterValueForRequest,
                'RequestTypeFor'    =>  $getMasterValueForRequestType,    
                'ExpType'           =>  $getMasterValueForExpType
            );

        return $allData;
    }

    // Add expenses in Main Table
    public function addExpensesDataIntoDB($mainData, $MainTableID=0, $directExp=0){

        $defaultApprID = '57078';
        if($mainData['exp_req_type']=='122001'){
            $defaultApprID = "57078";
        }elseif($mainData['exp_req_type']=='122002'){
            $defaultApprID = "57078";
        }elseif($mainData['exp_req_type']=='122003'){
            $defaultApprID = "57099";
        }

        // Again checking for Direct Expenses if so then Approcal status should be 0
        $is_direct_advance=0;
        if($directExp==1 && $mainData['exp_req_type']=='122004'){
            $defaultApprID = "0";
            $is_direct_advance = 1;
        }

        if( $MainTableID==0 ){

            //get the business_unit_id from user table as per submited by id

            $business_unit_id = DB::table("users")
                        ->where('user_id', '=', $mainData['submited_by_id'])
                        ->first(); 
            $refNoArr = Utility::getReferenceCode('EX','TS');
            
            $legal_entity=$this->getLegalEntity($mainData['submited_by_id']);


            $this->exp_code                 = $refNoArr;
            $this->exp_req_type             = $mainData['exp_req_type'];
            $this->exp_req_type_for_id      = $mainData['exp_req_type_for_id'];
            $this->exp_subject              = $mainData['exp_subject'];
            $this->exp_actual_amount        = $mainData['exp_actual_amount'];
            $this->tally_ledger_name        = $mainData['tally_ledger_name'];
            $this->exp_approved_amount      = $mainData['exp_actual_amount'];
            $this->submit_date              = $mainData['submit_date'];
            $this->submited_by_id           = $mainData['submited_by_id'];
            $this->exp_reff_id              = $mainData['exp_reff_id'];
            $this->exp_appr_status          = $defaultApprID;
            $this->created_by               = $mainData['submited_by_id'];
            $this->business_unit_id         = $business_unit_id->business_unit_id;
            $this->is_direct_advance        = $is_direct_advance;
            $this->legal_entity_id          = $legal_entity;
            if($this->save() ){
                return $this->exp_id;
            }else{
                return 0;
            }
        }else{
            return $MainTableID;
        }
    }

    // Add expenses Details
    public function addExpensesDataIntoDetailsTable($mainTableID, $detailsData, $userID){

        $recordType = $mainTableID==0 || $mainTableID=='' ? 0 : 1;

        foreach( $detailsData as $data ){

            $finalData = array(
                "exp_id"                        =>  $mainTableID,
                "exp_det_actual_amount"         =>  $data['ExpActualAmount'],
                "exp_det_approved_amount"       =>  $data['ExpActualAmount'],
                "exp_det_date"                  =>  $data['ExpDetailsDate'],
                "exp_type"                      =>  $data['ExpType'],
                "exp_det_description"           =>  $data['ExpDetailsDesc'],
                "exp_det_type"                  =>  $recordType,
                "created_by"                    =>  $userID,
                "created_at"                    =>  date("Y-m-d H:i:s")
            );

            $saveDetailsData = DB::Table("expenses_details")
                            ->insert($finalData);

        }

        return $saveDetailsData;
    }

    // Save record in Details Table for Single Record
    public function addExpensesDataIntoDetailsTableSingleRecord($detailsData){
        $saveDetailsData = DB::Table("expenses_details")
                            ->insert($detailsData);

        return 1;
    }

    public function getExpensesLineItemFromDB($userID, $recordType){

        $sql_expenses = "select det.`exp_det_id`, det.`exp_det_actual_amount` as 'ExpDetAmount', det.`exp_det_date` as 'ExpDetDate', 
                    (SELECT ml.master_lookup_name FROM master_lookup AS ml WHERE ml.`value` = det.`exp_type`) AS 'ExpDetType',
                    det.`exp_det_description` as 'Description', det.exp_det_proof as 'ProofImageKey'
                    FROM expenses_details AS det
                    WHERE det.created_by='".$userID."'
                    AND det.exp_det_type='". $recordType . "' ORDER BY exp_det_date DESC, exp_det_id DESC";

        $allData = DB::select(DB::raw($sql_expenses));

        return $allData;
    }

    public function updateExpensesLineItemWithMainTableID($mainTableID, $MapIDs){

        $updateQuery = "update expenses_details 
                        SET exp_id=".$mainTableID.", exp_det_type=1
                        WHERE exp_det_id IN (". $MapIDs.")";

        $allData = DB::statement(DB::raw($updateQuery));
        return 1;
    }

    public function updateExpensesMainTableforApproval($mainTableID, $ApprovalAmount, $isFinalStep,$tallyLedgerName){

        if( trim($tallyLedgerName)=='' ){
                $update = DB::table('expenses_main')
                    ->where('exp_id', '=', $mainTableID )
                    ->update(['exp_appr_status' => $isFinalStep, 
                            'exp_approved_amount' => $ApprovalAmount
                            ]);
            }else{
                $update = DB::table('expenses_main')
                    ->where('exp_id', '=', $mainTableID )
                    ->update(['exp_appr_status' => $isFinalStep, 
                            'exp_approved_amount' => $ApprovalAmount, 
                            'tally_ledger_name' => $tallyLedgerName
                            ]);
            }

        return 1;
    }

    public function getApprovalHistorByID($mainExpensesID){

        $Query = "select 
        '' AS 'awf_history_id',
        '' AS 'profile_picture',
        urs.`firstname` AS 'firstname',
        urs.`lastname` AS 'lastname',
        CONCAT(urs.`firstname`, ' ', urs.`lastname`) AS 'name',
        ex.created_at AS 'created_at',
        '' AS 'status_to_id',
        '' AS 'status_from_id',
        ex.exp_subject AS 'awf_comment',
        'Drafted' AS 'master_lookup_name'
        FROM expenses_main AS ex
        INNER JOIN users AS urs ON urs.user_id=ex.submited_by_id
        WHERE ex.`exp_id`=$mainExpensesID ";

        $allData = DB::select(DB::raw($Query));
        
        return $allData;
    }

    public function getApprovalHistorByIDFromDB($mainExpensesID, $expensesType){

        $history=DB::table('appr_workflow_history as hs')
                        ->join('users as us','us.user_id','=','hs.user_id')
                        ->join('user_roles as ur','ur.user_id','=','hs.user_id')
                        ->join('roles as rl','rl.role_id','=','ur.role_id')
                        ->join('master_lookup as ml','ml.value','=','hs.status_to_id')
                        ->select('hs.awf_history_id', 'us.profile_picture','us.firstname','us.lastname',DB::raw('group_concat(rl.name) as name'),DB::raw("date_format(hs.created_at, '%Y-%m-%d') as 'created_at' "),'hs.status_to_id','hs.status_from_id','hs.awf_comment','ml.master_lookup_name')
                        ->where('hs.awf_for_id',$mainExpensesID)
                        ->where('hs.awf_for_type',$expensesType)
                        ->groupBy('hs.created_at')
                        ->orderBy('hs.awf_history_id')
                        ->get()->all();

        return $history;

    }
    public function getAPIcallsdetails($callName, $hostIP, $paramData, $finalResponse){

        $finalData = array(
            "exp_call_name"                     =>  $callName,
            "exp_call_from"                     =>  $hostIP,
            "exp_call_params"                   =>  json_encode($paramData),
            "exp_call_response"                 =>  json_encode($finalResponse),
            "created_at"                        =>  date("Y-m-d H:i:s")
        );

        $save = DB::table("expenses_api_call_details") 
            ->insert($finalData);

        return 1;    
    }

    //Get UserIds as per the Approcal Role
    public function getUserIds($MainTableID){

        $userIDs = array();

        $getRoleName = "select DISTINCT rls.`name`, exmp.submited_by_id 
                FROM appr_workflow_status_details AS ad 
                INNER JOIN expenses_main AS exmp ON exmp.`exp_appr_status`=ad.`awf_status_id`
                INNER JOIN roles AS rls ON rls.`role_id`=ad.`applied_role_id`
                WHERE exmp.`exp_id`=$MainTableID";

        $roleNames = DB::select(DB::raw($getRoleName));

        foreach ($roleNames as $value) {
            if($value->name == 'ImmediateReporter'){

                $ImmediateReporterDetails = "
                select usr.`user_id` FROM users AS usr WHERE usr.`user_id` = (
                    SELECT usr.`reporting_manager_id`
                    FROM users AS usr 
                    WHERE usr.`user_id`=$value->submited_by_id
                )";

                $ImmediateReporterDetails = DB::select(DB::raw($ImmediateReporterDetails));

                $userIDs[]=$ImmediateReporterDetails[0]->user_id;

            }elseif($value->name == 'Initiator'){

                $InitiatorDetails = "select usr.`user_id` FROM users AS usr where usr.`user_id`=$value->submited_by_id";

                $InitiatorDetails = DB::select(DB::raw($InitiatorDetails));

                $userIDs[]=$InitiatorDetails[0]->user_id;
            }
        }

        $sqlUser = "select group_concat(usr.`user_id`) as 'UserIDs' 
                FROM users AS usr 
                INNER JOIN user_roles AS rls ON usr.`user_id`=rls.`user_id`
                WHERE rls.`role_id` IN (
                    SELECT DISTINCT ad.`applied_role_id` 
                    FROM appr_workflow_status_details AS ad 
                    INNER JOIN expenses_main AS exmp ON exmp.`exp_appr_status`=ad.`awf_status_id`
                    WHERE exmp.`exp_id`=$MainTableID
                )";
        $allData = DB::select(DB::raw($sqlUser));

        foreach ($allData as $value) {
            if($value->UserIDs!=""){
                $userIDs[]= $value->UserIDs;
            }
        }

        return $userIDs;
    }

    //Get Registration Id 
    public function getRegId($userIds){

        $sqlUser = "select registration_id, platform_id FROM device_details WHERE user_id IN (".$userIds.")";
        $allData = DB::select(DB::raw($sqlUser));
        return $allData;
    }

    // Get data from a SindleTable
    public function getDataFromTable($tableName, $whereFld, $value){
        $allData = DB::table($tableName)
                    ->where($whereFld, "=", $value)
                    ->get()->all();

        return $allData;
    }

    // Function to get the Workflow details for the Message
    public function getFlowDetailsFromDB($currentStatusID, $NextStatusID){

        $sqlUser = "
        select 
        (SELECT NAME FROM roles WHERE role_id=det.applied_role_id) AS 'PreviousRole',
        (SELECT master_lookup_name FROM master_lookup WHERE VALUE=det.`awf_condition_id`) AS 'ConditionName'
        FROM appr_workflow_status_details AS det
        WHERE det.`awf_status_id`=".$currentStatusID."
        AND det.`awf_status_to_go_id`=".$NextStatusID."
        LIMIT 1
        ";

        $allData = DB::select(DB::raw($sqlUser));
        return $allData;

    }


    // Function to get unClaimed Mapping check
    public function checkUnclaimedInDB($unclaimedID){

        $unclaimedID = explode(",",$unclaimedID);
        $dbData = DB::table("expenses_details")
                    ->whereIn("exp_det_id",$unclaimedID)
                    ->where("exp_id","!=",0)
                    ->count();

        return $dbData;
    }

    // Function to Delete the Expenses
    public function deleteUnclaimedExpFromDB($unclaimedID){
        $unclaimedID = explode(",",$unclaimedID);
        DB::table('expenses_details')
            ->whereIn('exp_det_id', $unclaimedID)
            ->delete();

        return 1;
    }
  
    // Function to get Approval Activity Details
    public function getApprovalActivityDetailsFromDB($mainExpensesID, $userID){

        /* OLD QUERY COMBINATION
        select main.`exp_code` AS 'ExpCode', main.`exp_approved_amount` AS 'ExpAmount',
        hist.`awf_comment` AS 'ActionComment',
        hist.`status_from_id`, (SELECT master_lookup_name FROM master_lookup WHERE VALUE=hist.`status_from_id`) AS 'BeforeStatus',
        hist.`condition_id`, (SELECT master_lookup_name FROM master_lookup WHERE VALUE=hist.`condition_id`) AS 'OnAction',
        hist.`status_to_id`, (SELECT master_lookup_name FROM master_lookup WHERE VALUE=hist.`status_to_id`) AS 'AfterStatus',
        DATE_FORMAT(hist.`created_at`, '%d-%m-%Y') AS 'ActionDate',
        hist.`is_final`
        */

        if(trim($mainExpensesID)!=""){
            $mainExpensesID = " AND hist.`awf_for_id`=".$mainExpensesID;
        }

        $sqlQuery = "
        select main.exp_id, main.`exp_code` AS 'ExpCdoe',    
        (SELECT master_lookup_name FROM master_lookup WHERE VALUE=main.exp_req_type) AS 'RequestFor',
        main.exp_subject as 'ExpSubject', main.`exp_actual_amount` AS 'ActualAmount', main.`exp_approved_amount` AS 'ApprovedAmount',
        DATE_FORMAT(hist.`created_at`, '%d-%m-%Y') AS 'ExpDate',
        
        (SELECT master_lookup_name FROM master_lookup WHERE VALUE=hist.`status_to_id`) AS 'CurrentStatus',
        0 as 'Roles',
        (SELECT CONCAT(firstname, ' ', lastname) FROM users AS usr WHERE usr.user_id=main.submited_by_id) AS 'SubmittedByName', hist.awf_history_id

        FROM appr_workflow_history AS hist
        INNER JOIN expenses_main AS main ON main.`exp_id`=hist.`awf_for_id`
        WHERE hist.user_id=".$userID.$mainExpensesID."  ORDER BY hist.created_at desc";

        $allData = DB::select(DB::raw($sqlQuery));
        return $allData;
    }

    public function saveIntoVouchersTable($getvoucherDetails){

        $dcData=$this->getCostCentre($getvoucherDetails[0]->business_unit_id);


        $saveData[0] = array(
            "voucher_code"                      =>  $getvoucherDetails[0]->exp_code,
            "voucher_type"                      =>  "Payment",
            "voucher_date"                      =>  $getvoucherDetails[0]->submit_date,
            "ledger_group"                      =>  $getvoucherDetails[0]->description,
            "ledger_account"                    =>  $getvoucherDetails[0]->master_lookup_name,
            "tran_type"                         =>  'Dr',
            "amount"                            =>  $getvoucherDetails[0]->exp_actual_amount,
            "naration"                         =>  "Being the payment made to " . $getvoucherDetails[0]->UserName ." on ". $getvoucherDetails[0]->exp_code . " dated " . $getvoucherDetails[0]->created_at,
            "cost_centre"                       =>  $dcData[0]->cost_center,
            "cost_centre_group"                 =>  $dcData[0]->csGroup,
            "reference_no"                      =>  $getvoucherDetails[0]->exp_code ,
            "is_posted"                         =>  0
        );
        $saveData[1] = array(
            "voucher_code"                      =>  $getvoucherDetails[0]->exp_code,
            "voucher_type"                      =>  "Payment",
            "voucher_date"                      =>  $getvoucherDetails[0]->submit_date,
            "ledger_group"                      =>  $getvoucherDetails[0]->TallyGroup,
            "ledger_account"                    =>  $getvoucherDetails[0]->tally_ledger_name,
            "tran_type"                         =>  'Cr',
            "amount"                            =>  $getvoucherDetails[0]->exp_actual_amount,
            "naration"                         =>  "Being the payment made to " . $getvoucherDetails[0]->UserName ." on ". $getvoucherDetails[0]->exp_code . " dated " . $getvoucherDetails[0]->created_at,
            "cost_centre"                       =>  $dcData[0]->cost_center,
            "cost_centre_group"                 =>  $dcData[0]->csGroup,
            "reference_no"                      =>  $getvoucherDetails[0]->exp_code ,
            "is_posted"                         =>  0
        );

        $save = DB::table("vouchers") 
            ->insert($saveData);

        return 1;
    }


    public function getAllTheTablesData($ExpensesMainID){
        $sqlUser = " select exp.*, 
                    (select tlm.tlm_group from tally_ledger_master as tlm where tlm.tlm_name=exp.tally_ledger_name limit 1) as 'TallyGroup',
                    concat(usr.firstname, ' ', usr.lastname) as 'UserName', ml.master_lookup_name, ml.description 
                    from expenses_main as exp
                    inner join users as usr on usr.user_id=exp.submited_by_id
                    inner join master_lookup as ml on ml.value=exp.exp_req_type_for_id
                    and ml.mas_cat_id=124
                    and exp.exp_id=$ExpensesMainID";

        $allData = DB::select(DB::raw($sqlUser));
        return $allData;
    }


    public function getTallyLedgerDetails(){

        $saveDetailsData = DB::table("tally_ledger_master")
                            ->select("tlm_name","tlm_group","tlm_id")
                            ->where("tlm_name","LIKE", "101%")
                            ->get()->all();
        return $saveDetailsData;

    }

    public function getAllUsersExpensesFromDB($userID){

        $allUserExpenses = " select expenses_details.exp_det_id,expenses_details.exp_id, expenses_details.exp_det_actual_amount, expenses_details.exp_det_date ,expenses_details.created_by,expenses_details.exp_det_description,expenses_details.exp_type,expenses_details.exp_det_approved_amount,expenses_details.appr_det_status,expenses_details.exp_det_proof FROM expenses_details
                        LEFT JOIN users ON expenses_details.created_by = users.user_id
                        WHERE users.reporting_manager_id =$userID";

        $allData = DB::select(DB::raw($allUserExpenses));
        return $allData;

    }

    public function getExpMainDataForTally($expMainID){
        $mainData = "select *, 
                        CASE 
                        WHEN TotoBu=1 AND BuIds<>0 THEN (SELECT CONCAT(cost_center, '-', bu_name, ' - Advance') AS 'TallyLedgerDr' FROM business_units WHERE business_units.`bu_id`=BuIds)
                        WHEN BuIds=0 OR TotoBu=0 OR TotoBu>1 THEN (SELECT CONCAT(cost_center, '-', bu_name, ' - Advance') AS 'TallyLedgerDr' FROM business_units WHERE business_units.`bu_id`=1)
                        END 'BusinessUnitName'
                        FROM
                        (
                            SELECT submited_by_id, tally_ledger_name, exp_code, submit_date, 
                            (SELECT CONCAT(firstname, ' ', lastname) FROM users WHERE user_id=expenses_main.`submited_by_id`) AS 'SubmittedByName',
                            (SELECT GROUP_CONCAT(object_id) FROM `user_permssion` WHERE permission_level_id=6 AND user_id=expenses_main.`submited_by_id`) AS 'BuIds',
                            (SELECT COUNT(object_id) FROM `user_permssion` WHERE permission_level_id=6 AND user_id=expenses_main.`submited_by_id`) AS 'TotoBu'
                            FROM expenses_main WHERE exp_id='".$expMainID."' limit 1
                        ) AS innertbl";

        $mainData = DB::select(DB::raw($mainData));
        return $mainData;
    }


    // Get Tally Ledger Name for Advace Dr [ Basically then BU name is needed as per the submited_id ]
    public function getTallyLedgerNameForAdvanceDr($ExpensesMainID){

        $mainData = "select *,
                    CASE 
                        WHEN TallyLedgerForAdvance IS NULL THEN (SELECT CONCAT(bu.cost_center, ' - ', bu.bu_name)  FROM business_units AS bu WHERE bu.bu_id=1)
                        ELSE TallyLedgerForAdvance
                    END AS 'TallyLedgerForAdvanceDr'
                    FROM 
                    (
                        SELECT exp.`submited_by_id`,
                        (SELECT CONCAT(bu.cost_center, ' - ', bu.bu_name)  FROM business_units AS bu WHERE bu.bu_id=exp.`business_unit_id`) AS 'TallyLedgerForAdvance'
                        FROM expenses_main AS exp
                        WHERE exp.exp_id=".$ExpensesMainID."
                    ) AS innertbl";

        $mainData = DB::select(DB::raw($mainData));
        return $mainData;
    }

    //get the expenses Details with Id
    public function getExpensesDetailsWithId($id){
        $expensesDetailsData = "select *,
                                (SELECT CONCAT( u.firstname, ' ', u.lastname ) FROM users AS u WHERE u.user_id=det.`submited_by_id` ) AS 'SubmittedByName'
                                FROM expenses_main AS det
                                WHERE det.`exp_id`='" . $id . "'";
        $allData = DB::select(DB::raw($expensesDetailsData));
        return $allData;
    }


    public function downloadExpenseDetailsData($exp_id){
        $expensesDetailsData = "select *,
                                date(det.exp_det_date) as 'details_date',
                                (SELECT ml.master_lookup_name FROM master_lookup AS ml WHERE ml.value = det.exp_type) AS 'ExpensesType'
                                FROM expenses_details AS det
                                WHERE det.`exp_id`='" . $exp_id . "'";

        $allData = DB::select(DB::raw($expensesDetailsData));
        return $allData;
    }


    public function getExpensesLedgerGroup($tlmname){
        $group =  DB::table("tally_ledger_master")
                        ->where('tlm_name', '=', $tlmname)
                        ->first();

        if( !empty($group) ){
            return $group->tlm_group;
        }else{
            return "";
        }
    }



    public function saveIntoVouchersTableAdvance( $data, $exptallydr, $exptallycr){

        // Get Ledger Group Information for Tally Dr
        $tallyLedgerGroupDr = $this->getExpensesLedgerGroup($exptallydr);
        // Get Ledger Group Information for Tally Cr
        $tallyLedgerGroupCr = $this->getExpensesLedgerGroup($exptallycr);

        $dcData=$this->getCostCentre($data[0]->business_unit_id);

        $saveData[0] = array(
            "voucher_code"                      =>  $data[0]->exp_code,
            "voucher_type"                      =>  "Payment",
            "voucher_date"                      =>  $data[0]->submit_date,
            "ledger_group"                      =>  $tallyLedgerGroupDr,
            "ledger_account"                    =>  $exptallydr,
            "tran_type"                         =>  'Dr',
            "amount"                            =>  $data[0]->exp_actual_amount,
            "naration"                          =>  "Being Expenses given to" .$data[0]->SubmittedByName . ", as an Advance of Rs. " .$data[0]->exp_actual_amount,
            "cost_centre"                       =>  $exptallydr,
            "cost_centre_group"                 =>  $dcData[0]->cost_center,
            "reference_no"                      =>  $data[0]->exp_code,
            "is_posted"                         =>  0,
            "Remarks"                           => "exp_advance"
        );

        $saveData[1] = array(
            "voucher_code"                      =>  $data[0]->exp_code,
            "voucher_type"                      =>  "Payment",
            "voucher_date"                      =>  $data[0]->submit_date,
            "ledger_group"                      =>  $tallyLedgerGroupCr,
            "ledger_account"                    =>  $exptallycr,
            "tran_type"                         =>  'Cr',
            "amount"                            =>  $data[0]->exp_actual_amount,
            "naration"                          => "Being Expenses given to" .$data[0]->SubmittedByName . ", as an Advance of Rs. " .$data[0]->exp_actual_amount,
            "cost_centre"                       =>  $exptallydr,
            "cost_centre_group"                 =>  $dcData[0]->cost_center,
            "reference_no"                      =>  $data[0]->exp_code,
            "is_posted"                         =>  0,
            "Remarks"                           => "exp_advance"
        );

        $save = DB::table("vouchers") 
            ->insert($saveData);

        return 1;   
    }


    public function saveIntoVouchersTableForReimbursment( $data, $expDetailsData, $exptallycr){
        //Prepar DR data
        $loopCount =0;
        $saveData = array();
        $dcData=$this->getCostCentre($data[0]->business_unit_id);

        foreach ($expDetailsData as $value) {

            // get Tally Ledger Name
            $tallyLedgerName = $this->getMasterLookupNameByVal($value->exp_type);
            $tallyLedgerName = is_object($tallyLedgerName) ? $tallyLedgerName->description : "";
           // $dcData=$this->getCostCentre($data['business_unit_id']);


            if($tallyLedgerName!=""){

                // Get Ledger Group Information for Tally Dr
                $tallyLedgerGroupDr = $this->getExpensesLedgerGroup($tallyLedgerName);
                $saveData[$loopCount] = array(
                    "voucher_code"                      =>  $data[0]->exp_code,
                    "voucher_type"                      =>  "Journal",
                    "voucher_date"                      =>  $data[0]->submit_date,
                    "ledger_group"                      =>  $tallyLedgerGroupDr,
                    "ledger_account"                    =>  $tallyLedgerName,
                    "tran_type"                         =>  'Dr',
                    "amount"                            =>  $value->exp_det_approved_amount,
                    "naration"                          =>  "Being Rimbursement given to" .$data[0]->SubmittedByName . ", as an Reimbursment of Rs. " .$value->exp_det_actual_amount,
                    "cost_centre"                       =>  $exptallycr,
                    "cost_centre_group"                 =>  $dcData[0]->cost_center,
                    "reference_no"                      =>  $data[0]->exp_code,
                    "is_posted"                         =>  0,
                    "Remarks"                           => "exp_reimbursment"
                );

                $loopCount++;
            }
        }

        // Get Ledger Group Information for Tally Cr
        $tallyLedgerGroupCr = $this->getExpensesLedgerGroup($exptallycr);
        $saveData[$loopCount] = array(
            "voucher_code"                      =>  $data[0]->exp_code,
            "voucher_type"                      =>  "Journal",
            "voucher_date"                      =>  $data[0]->submit_date,
            "ledger_group"                      =>  $tallyLedgerGroupCr,
            "ledger_account"                    =>  $exptallycr,
            "tran_type"                         =>  'Cr',
            "amount"                            =>  $data[0]->exp_approved_amount,
            "naration"                          => "Being Reimbursment given to" .$data[0]->SubmittedByName . ", as an Reimbursment of Rs. " .$data[0]->exp_actual_amount,
            "cost_centre"                       =>  $exptallycr,
            "cost_centre_group"                 =>  $dcData[0]->cost_center,
            "reference_no"                      =>  $data[0]->exp_code,
            "is_posted"                         =>  0,
            "Remarks"                           => "exp_reimbursment"
        );

        $save = DB::table("vouchers") 
            ->insert($saveData);

        return 1;   
    }

    //get master lookup name by value
    public function getMasterLookupNameByVal($masValue){
        $getData = DB::table("master_lookup")
                            ->where("mas_cat_id","=", "123")
                            ->where("value", "=", $masValue)
                            ->first();
        return $getData;
    }

    public function getCostCentre($bu_id){
        $getData="select *,
                      (SELECT 
                        bb.cost_center
                      FROM
                        business_units bb
                      WHERE b.`parent_bu_id`=bb.bu_id)  AS csGroup 
                    FROM
                      business_units b 
                    WHERE b.bu_id = '$bu_id'";
        $getData=DB::select(DB::raw($getData));

        return $getData;
    }

    public function getLegalEntity($id){ 
        $lid = "select legal_entity_id from users where user_id= $id";
        $lid=DB::select(DB::raw($lid));
        $lid=json_decode(json_encode($lid),true);
        return $lid['0']['legal_entity_id'];
    }

}