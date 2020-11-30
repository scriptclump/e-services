<?php
/*
FileName : expensesTrackModel
Author   : eButor
Description : Function Written for all Model function related to Expenses Tracker.
CreatedDate : 26/Dec/2016
*/

//defining namespace
namespace App\Modules\ExpensesTracker\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\ExpensesTracker\Models\expensesTrackModel;
use App\Modules\ExpensesTracker\Models\expensesAPIModel;
use DB;
use Session;
use UserActivity;

class expensesTrackModel extends Model
{
    protected $table = 'expenses_details';
    protected $primaryKey = "exp_det_id";
    private $objAPIModel = '';
    public function __construct(){
        $this->objAPIModel    = new expensesAPIModel();
    }

    public function showexpensesDetails($makeFinalSql, $orderBy, $page, $pageSize){

        if($orderBy!=''){
            $orderBy = ' ORDER BY ' . $orderBy;
        }else{
            $orderBy = ' ORDER BY ExpSumitDate desc, exp_code DESC';
        }

        $sqlWhrCls = '';
        $countLoop = 0;
        $legal_entity=$this->objAPIModel->getLegalEntity(Session::get('userId'));

        foreach ($makeFinalSql as $value) {
            if( $countLoop==0 ){
                $sqlWhrCls .= ' WHERE ' . $value;
            }elseif( count($makeFinalSql)==$countLoop ){
                $sqlWhrCls .= $value;
            }else{
                $sqlWhrCls .= ' AND ' .$value;
            }
            $countLoop++;
        }
        
        $expensesDetails = "select count(*) as cnt from (
                            select main.exp_code, 
                            (select ml.master_lookup_name from master_lookup as ml where ml.value = main.exp_req_type) as 'ExpensesType',
                             (select b.bu_name FROM business_units b WHERE b.bu_id= main.business_unit_id)AS business_name, 
                            main.exp_subject, main.exp_actual_amount, main.business_unit_id,main.exp_approved_amount, date(main.submit_date) as 'ExpSumitDate', 
                            (select concat(usr.firstname, ' ', usr.lastname) from users as usr where usr.user_id=main.submited_by_id) as 'SubmittedBy',
                            case main.exp_appr_status
                                when 1 then 'Approved'
                                else (select ml.master_lookup_name from master_lookup as ml where ml.value = main.exp_appr_status) 
                            end 'ApprovalStatus',
                            CONCAT('<center>
                            <code>
                            
                            </code>
                            </center>') AS 'actions'
                            from expenses_main as main where main.legal_entity_id=".$legal_entity  .") as innertbl ". $sqlWhrCls;

        $allData = DB::select(DB::raw($expensesDetails));
        $TotalRecordsCount = $allData[0]->cnt;


        $expensesDetails = "select * from (
                            select main.exp_code, 
                            (select ml.master_lookup_name from master_lookup as ml where ml.value = main.exp_req_type) as 'ExpensesType', 
                            (select b.bu_name FROM business_units b WHERE b.bu_id= main.business_unit_id)AS business_name,
                            main.exp_subject, main.exp_actual_amount,main.business_unit_id, main.exp_approved_amount,main.is_direct_advance, 
                            date(main.submit_date) as 'ExpSumitDate', date_format(main.submit_date, '%d-%m-%Y') as 'ExpSumitDate_grid',
                            (select concat(usr.firstname, ' ', usr.lastname) from users as usr where usr.user_id=main.submited_by_id) as 'SubmittedBy',
                            case main.exp_appr_status
                                when 1 then 'Approved'
                                else (select ml.master_lookup_name from master_lookup as ml where ml.value = main.exp_appr_status) 
                            end 'ApprovalStatus',
                           CASE main.is_direct_advance WHEN 0 THEN CONCAT('<center>
                          <code>                            
                          <a href=\"javascript:void(0)\" onclick=\"viewexpensesdata(',exp_id,')\">
                          <i class=\"fa fa-balance-scale\"></i>
                          </a> &nbsp&nbsp
                          <a href=\"javascript:void(0)\" onclick=\"historyexpensesdata(',exp_id,')\">
                          <i class=\"fa fa-eye\"></i>
                          </a>
                          </code>
                          </center>') END AS 'actions'
                            from expenses_main as main where main.legal_entity_id=".$legal_entity .") as innertbl ". $sqlWhrCls . $orderBy;

        
        $pageLimit = '';
        if($page!='' && $pageSize!=''){
            $pageLimit = " LIMIT " . (int)($page*$pageSize) . ", " . $pageSize;
        }

        $expensesDetails = DB::select(DB::raw($expensesDetails . $pageLimit));

        return json_encode(array('results'=>$expensesDetails, 'TotalRecordsCount'=>(int)($TotalRecordsCount))); 
    }
    public function updateMainBusinessUnit($expe_id,$bus_id){
        $data = DB::table('expenses_main')
                ->where('exp_id', '=',$expe_id)
                ->update(['business_unit_id' => $bus_id]);
        return $data;
    }

    public function getExpensesData($expid){

        $expensesdataNew = "select epm.*, date_format(epm.submit_date, '%d-%m-%Y') as 'ExpSubmittedDate', expd.exp_det_id, expd.exp_det_actual_amount,expd.exp_det_approved_amount,
                        expd.exp_det_date,expd.exp_type,expd.exp_det_description,expd.exp_det_proof,expd.exp_det_type,expd.appr_det_status, ml.master_lookup_name,
                        (select master_lookup_name from master_lookup as m where m.value=epm.exp_req_type) as 'RequestFor',
                        (SELECT CONCAT( u.firstname, ' ', u.lastname ) FROM users AS u WHERE u.user_id=epm.`submited_by_id` ) AS 'SubmittedByName'
                        from expenses_main as epm 
                        left join expenses_details as expd on epm.exp_id=expd.exp_id
                        left join master_lookup as ml on expd.exp_type=ml.value
                        where epm.exp_id=".$expid;                       
        $allDataNew = DB::select(DB::raw($expensesdataNew));
        
        return $allDataNew;

    }
    public function getBusinesID($SubmittedID, $expensesMainId){

        $userIDQuery = "select CONCAT(usr.business_unit_id, ';', mn.`business_unit_id`) AS 'BusinesUnitIDs'
                        FROM users AS usr
                        INNER JOIN expenses_main AS mn ON mn.`submited_by_id`=usr.`user_id`
                        WHERE mn.`exp_id`=".$expensesMainId." 
                        AND usr.`user_id`=".$SubmittedID;
        $actualId = DB::select(DB::raw($userIDQuery));

        return isset($actualId[0]) ? $actualId[0]->BusinesUnitIDs : 0;
    }
    public function updateApporeData($approve_data){

        // Update the table without effective date
        $approveddata = DB::table('expenses_details')
                        ->where('exp_det_id', '=', $approve_data['exp_det_id'])
                        ->update(['exp_det_approved_amount' => $approve_data['exp_det_approved_amount']]);
        return $approveddata;
    }
    public function getBusinessUnitWithID(){
        $buData = "select bu_id, CONCAT( bu_name,' - ', cost_center ) AS 'BuName' 
                        FROM business_units ORDER BY bu_name";
        $businessData = DB::select(DB::raw($buData));
        return $businessData;
    }
    

    //get the expenses type from expenses_main table
    public function getExpensesTypeFromTable($expid){
        $approveddata = DB::table('expenses_main')
                        ->where('exp_id', '=', $expid)
                        ->get()->all();

        if( count($approveddata)==0 ){
            return 0;
        }else{
            return $approveddata[0]->exp_req_type;
        }

    }
    public function insertLinesIntoExpHistDetails($update_exp_hist_data){

        //Save data into expenses history table
        $save = DB::table("expenses_history_details")->insert($update_exp_hist_data);
        return true;
    }
    public function updateMaintableData($expenseid,$finalStatus,$totalAmount){

        //Update the expense main table with total approved amount and approval status
        $updatedata = DB::table('expenses_main')
                        ->where('exp_id', '=', $expenseid)
                        ->update(['exp_appr_status' => $finalStatus, 'exp_approved_amount' => $totalAmount]);
        return $updatedata;
    }

    public function updateDetailsTableWithId($exp_id,$exp_type){

        //Update the expense main table with total approved amount and approval status
        $updatedata = DB::table('expenses_details')
                        ->where('exp_det_id', '=', $exp_id)
                        ->update(['exp_type' => $exp_type]);
        return $updatedata;

    }
    public function updateDetailsTableAmountWithId($detailsID,$expenseId,$exp_det_approved_amount){
        
        // Update details approved amount
        $data = DB::table('expenses_details')
                ->where('exp_det_id', '=',$detailsID)
                ->update(['exp_det_approved_amount' => $exp_det_approved_amount]);

        // Get the total approved amount from details table
        $amount="select SUM(det.exp_det_approved_amount) AS 'TotalApprAmt' FROM  expenses_details AS det WHERE det.`exp_id` ='".$expenseId."'";
        $actualAmount = DB::select(DB::raw($amount));

        // Finally Update main table with new Approved amount
        $tabledata = DB::table('expenses_main')
                ->where('exp_id','=',$expenseId)
                ->update(['exp_approved_amount' =>$actualAmount[0]->TotalApprAmt ]);

        return $actualAmount[0]->TotalApprAmt;
    }

    public function downloadAsPerData($start_date,$end_date){

        $start_date=date("Y-m-d", strtotime($start_date));
        $end_date=date("Y-m-d", strtotime($end_date));
        $expensesDetails = "select * FROM (SELECT main.exp_code, 
                            (SELECT ml.master_lookup_name FROM master_lookup AS ml WHERE ml.value = main.exp_req_type) AS 'ExpensesType', 
                            main.exp_subject, main.exp_actual_amount, main.exp_approved_amount,main.exp_id, 
                            DATE(main.submit_date) AS 'ExpSumitDate', DATE_FORMAT(main.submit_date, '%d-%m-%Y') AS 'ExpSumitDate_grid',
                            (SELECT CONCAT(usr.firstname, ' ', usr.lastname) FROM users AS usr  WHERE usr.user_id=main.submited_by_id) AS 'SubmittedBy',
                            CASE main.exp_appr_status
                            WHEN 1 THEN 'Approved'
                            ELSE (SELECT ml.master_lookup_name FROM master_lookup AS ml WHERE ml.value = main.exp_appr_status) 
                            END 'ApprovalStatus'
                            FROM expenses_main AS main) AS innertbl WHERE ExpSumitDate BETWEEN '".$start_date."' AND '".$end_date."'";


        $allData = DB::select(DB::raw($expensesDetails));
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

    //get history details of expenses
    public function getHistoryExpensesData($expid){

    }

    public function getExpensesDrData(){

        $getData = DB::table("tally_ledger_master")
                            ->select("tlm_name")
                            ->where("tlm_name","LIKE", "125%")
                            ->get()->all();
        return $getData;
    }

    public function getExpensesCrData(){
        $getData = DB::table("tally_ledger_master")
                            ->select("tlm_name")
                            ->where("tlm_name","LIKE", "101%")
                            ->get()->all();
        return $getData;
    }

    public function getExpensesDetailsWithId($id){
        $expensesDetailsData = "select *,
                                (SELECT CONCAT( u.firstname, ' ', u.lastname ) FROM users AS u WHERE u.user_id=det.`submited_by_id` ) AS 'SubmittedByName'
                                FROM expenses_main AS det
                                WHERE det.`exp_id`='" . $id . "'";
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

    // ===================================================//
    // Tally Section //
    public function getExpMainDataForTally($expMainID){
        $mainData = "select *, 
                        CASE 
                        WHEN TotoBu=1 AND BuIds<>0 THEN (SELECT CONCAT(cost_center, '-', bu_name, ' - Advance') AS 'TallyLedgerDr' FROM business_units WHERE business_units.`bu_id`=BuIds)
                        WHEN BuIds=0 OR TotoBu=0 OR TotoBu>1 THEN (SELECT CONCAT(cost_center, '-', bu_name, ' - Advance') AS 'TallyLedgerDr' FROM business_units WHERE business_units.`bu_id`=1)
                        END 'BusinessUnitName'
                        FROM
                        (
                            SELECT submited_by_id, tally_ledger_name, exp_code, submit_date,exp_actual_amount, 
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
                        SELECT
                        (SELECT CONCAT(bu.cost_center, ' - ', bu.bu_name)  FROM business_units AS bu WHERE bu.bu_id=exp.`business_unit_id`) AS 'TallyLedgerForAdvance'
                        FROM expenses_main AS exp
                        WHERE exp.exp_id=".$ExpensesMainID."
                    ) AS innertbl";



        $mainData = DB::select(DB::raw($mainData));
        return $mainData;
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

    public function getMasterlookupdata(){
        $getData = DB::table("master_lookup")
                            ->where("mas_cat_id","=", "123")
                            ->get()->all();
        return $getData;
    }

    public function getMasterLookupNameByVal($masValue){
        $getData = DB::table("master_lookup")
                            ->where("mas_cat_id","=", "123")
                            ->where("value", "=", $masValue)
                            ->first();
        return $getData;
    }

    public function getApprovalStatusByExpID($expensesMainId){

        $getData = DB::table("expenses_main")
                    ->where ("exp_id","=",$expensesMainId)
                    ->first();

        if($getData){
            return $getData->exp_appr_status;
        }else{
            return 1;
        }
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

}