<?php
//defining namespace

namespace App\Modules\NCTTracker\Models;
use Illuminate\Database\Eloquent\Model;
use App\Modules\NCTTracker\Models\nctTrackerHistoryModel;
use DB;
use Session;
use UserActivity;

class nctTrackerHistoryModel extends Model
{
    protected $table = 'nct_transcation_history';
    protected $primaryKey = "hist_id";

    public function saveIntoHistoryTable($trackingData,$nctdata){

        $this->nct_id = $trackingData;
        $originalDate = $nctdata['issued_date'];
        $originalDate = str_replace('/', '-', $originalDate);
        $availableDate = date("Y-m-d", strtotime($originalDate) ); 
        $this->hist_date = $availableDate;
        $this->nct_ref_no = $nctdata['reference_no'];
        $this->current_status = $nctdata['status'];
        $this->prev_status = "none";
        $this->nct_bank = $nctdata['bank_name'];
        $this->nct_branch = $nctdata['branch_name'];
        $this->created_by=Session::get('userId');
        $this->changed_by = $nctdata['collected_by'];
        $checkcomment = $this->checkCommentExistOrNot($trackingData);
        if($checkcomment>0){
            $this->comment = $nctdata['comment'];
        }else{
            $this->comment = $nctdata['collected_by'] . " Collected : " . $nctdata['amount'] . " on " . $nctdata['issued_date'] . " <br>" . $nctdata['bank_name'] . " : " . $nctdata['branch_name'] ."<br> Holder : " . $nctdata['holder_name'] ."<br>IFSC code :".$nctdata['ifsc_code']."<br> Comment : " . $nctdata['comment'];
        }

       if($this->save())
        {
            return $this->hist_id;
        }else{
            return false;
        }
    }

    public function checkCommentExistOrNot($trackingData){
        $checkData = DB::table("nct_transcation_history")
                    ->where("nct_id", "=", $trackingData)
                    ->count();
        return $checkData;
    }

    public function saveAndUpdateDetails($updatestatusdetails,$updatestatus,$userId){
        $changed_by = Session::get('userName');
        $originalDate = $updatestatus['date'];
        $originalDate = str_replace('/', '-', $originalDate);
        $availableDate = date("Y-m-d", strtotime($originalDate) );
        $this->nct_id = $updatestatusdetails;
        $this->nct_ref_no = $updatestatus['nct_reference_no'];
        $this->hist_date = $availableDate;
        $this->current_status = $updatestatus['update_status'];
        $this->comment = $updatestatus['comment'];
        $this->prev_status = $updatestatus['prev_status_id'];
        $this->updated_by = $userId;
        $this->created_by = $userId;
        $this->changed_by = $updatestatus['changes_by'];
        if($this->save())
        {
            $historydata=2;
        }
        return $historydata;
    }

    public function getNctDataByRowDB($ncit){
        $sqlData = "";

            $sqlData = DB::table("nct_transcation_tracking as ntt")
                        ->select("ntt.*", DB::raw("nct_collected_by as UserName"))
                        //->join("users as usr", "usr.user_id", "=", "ntt.nct_collected_by" )
                        ->where("ntt.nct_id","=",$ncit)
                        ->first();
        
          
       return $sqlData;
    }
    public function UpdateEachDetailsNct($nctdata){

        $userId = Session::get('userId');
        $originalDate = $nctdata['issued_date_view'];
        $Nnct_id_view = $nctdata['nct_id_view'];
        $originalDate = str_replace('/', '-', $originalDate);
        $availableDate = date("Y-m-d", strtotime($originalDate) ); 
        $changed_by = Session::get('userName');
        $availableDate = date("Y-m-d", strtotime($originalDate) );
        //extra charges
        $extracharges=0;
        if($nctdata['status_view'] == 11908){
            $extracharges = $nctdata['extra_charge_view'];
        }

        //update in nct tracking table
        $update = DB::table('nct_transcation_tracking')
        ->where('nct_id', '=', $Nnct_id_view )
        ->update([
        'nct_ref_no' => $nctdata['reference_no_view'], 
        'nct_bank' => $nctdata['bank_name_view'],
        'nct_branch' => $nctdata['branch_name_view'], 
        'nct_holdername' => $nctdata['holder_name_view'],
        'nct_issue_date' => $availableDate,
        'nct_collected_by' => $nctdata['collected_by_view'],
        'nct_comment' => $nctdata['comment_view'],
        // 'nct_amount' => $nctdata['amount_view'],
        'nct_deposited_to' => $nctdata['deposited_to_view'],
        'nct_status' => $nctdata['status_view'],
        'updated_at' => DB::raw('TIMESTAMP(NOW())'),
        'extra_charges' => $extracharges]);
        
        $checkcomment = $this->checkCommentExistOrNot($Nnct_id_view);

        if($checkcomment==0){
            $this->comment = $nctdata['comment_view'];
        }else{
            $this->comment = $nctdata['collected_by_view'] . " Collected : " . $nctdata['amount_view'] . " on " . $nctdata['issued_date_view'] . " <br>" . $nctdata['bank_name_view'] . " : " . $nctdata['branch_name_view'] ."<br> Holder : " . $nctdata['holder_name_view'] . "<br> Comment : " . $nctdata['comment_view'];
        }
        // save history in history table
        $this->nct_id = $Nnct_id_view;
        $this->nct_ref_no = $nctdata['reference_no_view'];
        $this->nct_bank = $nctdata['bank_name_view'];
        $this->nct_branch = $nctdata['branch_name_view'];
        $this->hist_date = $availableDate;
        $this->current_status = $nctdata['status_view'];
        $this->prev_status = '';
        $this->updated_by = $userId;
        $this->created_by = $userId;
        $this->changed_by = $nctdata['collected_by_view'];
        if($this->save())
        {
            return $this->nct_id;
        }else{
            return false;
        }  
    }
    public function CheckBalanceAmount($ncthistoryid){
        $showbalamount= "select ROUND((SUM(ntt.nct_amount)),2) AS balance
            FROM nct_transcation_tracking AS ntt 
            INNER JOIN collection_history AS col ON ntt.`nct_history_id`=col.`history_id`
            WHERE ntt.nct_history_id='".$ncthistoryid."'
            AND ntt.nct_status IN (11903,11906,11908)";

        $allData = DB::select(DB::raw($showbalamount));

        if( $allData[0]->balance == '') {
            return 0;
        }else{
            return $allData[0]->balance;
        }

    }
    public function gethistoryid($nctid){

        $sqlData = DB::table("nct_transcation_tracking")
                        ->where("nct_id","=",$nctid)
                        ->get()->all();

       return $sqlData[0]->nct_history_id;

    }
    
    public function saveIntoVouchersTableOnUpdate($historyid,$nctdata,$ledgerNameGroup, $ledgerNameForCustomer, $DrCrFlag,$cost_center,$invoicecode ){
        $date = str_replace('/', '-',$nctdata['issued_date_view']);
        $voucherdate = date('Y-m-d', strtotime($date));
        $cost_centre_group=$cost_center;
        $ledger_group = "Bank Accounts";
        $amount = $nctdata['amount_view'];
        $recivetype = "Cheque";
        
        $i=1;
        if($nctdata['status_view'] == 11908){
            // $i=2;
            //if($i == 2)
            {
                $amount = $nctdata['extra_charge_view'];
                $ledgerNameGroup = "Bank Charges";
                $recivetype = "Cash";
                $ledgerNameForCustomer = "711500 : Bank Charges";
            }
        }

        if($nctdata['status_view'] == 11907 ){
                $amount = $nctdata['amount_view'];
                $ledger_group = "Cash-in-hand";
                $recivetype = "Cash";
                $nctdata['deposited_to_view'] = "10100 : Cash";
        }

        while($i)
        {
            $saveData[0] = array(
                "voucher_code"                      =>  "NCT-".$historyid,
                "voucher_type"                      =>  "Receipt",
                "voucher_date"                      =>  $voucherdate,
                "ledger_group"                      =>  $ledger_group,
                "ledger_account"                    =>  $nctdata['deposited_to_view'],
                "tran_type"                         =>  $DrCrFlag==0 ? 'Dr' : 'Cr',
                "amount"                            =>  $amount,
                "naration"                          =>  "Being $recivetype received from " . $nctdata['holder_name_view'] ." as Cheque Refrence No ". $nctdata['reference_no_view'],
                "cost_centre"                       =>  $cost_centre_group,
                "cost_centre_group"                 =>  "Z1R1",
                "reference_no"                      =>  $invoicecode,
                "is_posted"                         =>  0
            );

            $saveData[1] = array(
                "voucher_code"                      =>  "NCT-".$historyid,
                "voucher_type"                      =>  "Receipt",
                "voucher_date"                      =>  $voucherdate,
                "ledger_group"                      =>  $ledgerNameGroup,
                "ledger_account"                    =>  $ledgerNameForCustomer,
                "tran_type"                         =>  $DrCrFlag==1 ? 'Dr' : 'Cr',
                "amount"                            =>  $amount,
                "naration"                          =>  "Being $recivetype received from " . $nctdata['holder_name_view'] ." as Cheque Refrence No ". $nctdata['reference_no_view'],
                "cost_centre"                       =>  $cost_centre_group,
                "cost_centre_group"                 =>  "Z1R1",
                "reference_no"                      =>  $invoicecode,
                "is_posted"                         =>  0
            );

            

            $save = DB::table("vouchers") 
            ->insert($saveData);
            $i--;    

        }
        return 1;   
    }
    public function saveIntoHistoryTableOnUpdate($trackingData,$nctdata){
        $extra_charges = 0;
        if($nctdata['status_view'] == 11908){
            $extra_charges = $nctdata['extra_charge_view'];
        }

        $this->nct_id = $trackingData;
        $originalDate = $nctdata['issued_date_view'];
        $originalDate = str_replace('/', '-', $originalDate);
        $availableDate = date("Y-m-d", strtotime($originalDate) ); 
        $this->hist_date = $availableDate;
        $this->nct_ref_no = $nctdata['reference_no_view'];
        $this->current_status = $nctdata['status_view'];
        $this->prev_status = "none";
        $this->nct_bank = $nctdata['bank_name_view'];
        $this->nct_branch = $nctdata['branch_name_view'];
        $this->extra_charges = $extra_charges;
        $this->created_by=Session::get('userId');
        $this->changed_by = $nctdata['collected_by_view'];
        $checkcomment = $this->checkCommentExistOrNot($trackingData);
        if($checkcomment>0){
            $this->comment = $nctdata['comment_view'];
        }else{
            $this->comment = $nctdata['collected_by_view'] . " Collected : " . $nctdata['amount_view'] . " on " . $nctdata['issued_date_view'] . " <br>" . $nctdata['bank_name_view'] . " : " . $nctdata['branch_name_view'] ."<br> Holder : " . $nctdata['holder_name_view'] . "<br> Comment : " . $nctdata['comment_view'] ;
        }
       if($this->save()){
            return $this->hist_id;
        }else{
            return false;
        }
    }
    public function checkCurrStatusById($nctID){
        $checkData = DB::table("nct_transcation_tracking")
                    ->where("nct_id", "=", $nctID)
                    ->get()->all();
        return $checkData[0]->nct_status;
    }
    public function getDepositedByName($user_id){

        $userName = "select  CONCAT(firstname,' ',lastname) AS Fullname FROM users WHERE user_id=$user_id";
        $deposit_name = DB::select(DB::raw($userName));
        return $deposit_name[0]->Fullname;
    }

    public function countOfTheReferenceNumber($refNumber,$collectionId){
        $checkData = DB::table("nct_transcation_tracking")
                    ->where("nct_history_id", "=", $collectionId)
                    ->orwhere("nct_ref_no", "=", $refNumber)
                    ->first();
        if( !empty($checkData) ){
                        return $checkData->nct_ref_no;
                    }else{
                        return 0;
                    }
    }

    public function getThePreviousStatus($collectionId,$chequeAmount,$refNumber){

        $previousStatus = DB::table("nct_transcation_tracking")
                    ->where('nct_history_id', '=', $collectionId)
                    ->where('nct_amount', '=', $chequeAmount)
                    ->where('nct_ref_no', '=', $refNumber )
                    ->first();
                    if( !empty($previousStatus) ){
                        return $previousStatus->nct_id . ';'.$previousStatus->nct_status;
                    }else{
                        return 0;
                    }
    }

    public function getNctId($collectionId){
        $nctid = DB::table("nct_transcation_tracking")
                    ->where("nct_history_id", "=", $collectionId)
                    ->first();
                    
                    if( !empty($nctid) ){
                        return $nctid->nct_id;
                    }else{
                        return 0;
                    }
    }

    public function getChequeDataOnCurrentDate(){
        $query = "select nct.`nct_ref_no`,nct.`nct_amount`,nct.`nct_issue_date`,cht.`collection_code`,
        ctt.`order_code`,ctt.`customer_name`,DATEDIFF(DATE(NOW()),nct.nct_issue_date) AS 'daysdiff' FROM nct_transcation_tracking AS nct INNER JOIN collection_history AS cht ON cht.`history_id` = nct.`nct_history_id` INNER JOIN collections AS ctt ON ctt.`collection_id` = cht.`collection_id` WHERE DATEDIFF(DATE(NOW()),nct.nct_issue_date) > 1 AND nct.`nct_status`=11905";

        $allData = DB::select(DB::raw($query));
        return $allData;

    }

    public function getChequeBounceDate(){
        $query = "select nct.`nct_ref_no`,nct.`nct_amount`,nct.`nct_issue_date`,cht.`collection_code`,
        ctt.`order_code`,ctt.`customer_name`,DATEDIFF(DATE(NOW()),nct.updated_at) AS 'daysdiff' FROM nct_transcation_tracking AS nct INNER JOIN collection_history AS cht ON cht.`history_id` = nct.`nct_history_id` INNER JOIN collections AS ctt ON ctt.`collection_id` = cht.`collection_id` WHERE DATEDIFF(DATE(NOW()),nct.updated_at) > 4 AND nct.`nct_status`=11906";

        $allData = DB::select(DB::raw($query));
        return $allData;
    }

    public function checkDepositedDate(){
        $query = "select nct.`nct_ref_no`,nct.`nct_amount`,nct.`nct_issue_date`,cht.`collection_code`,
        ctt.`order_code`,ctt.`customer_name`,DATEDIFF(DATE(NOW()),nct.updated_at) AS 'daysdiff' FROM nct_transcation_tracking AS nct INNER JOIN collection_history AS cht ON cht.`history_id` = nct.`nct_history_id` INNER JOIN collections AS ctt ON ctt.`collection_id` = cht.`collection_id` WHERE DATEDIFF(DATE(NOW()),nct.updated_at) > 4 AND nct.`nct_status`=11902";

        $allData = DB::select(DB::raw($query));
        return $allData;
    }

}