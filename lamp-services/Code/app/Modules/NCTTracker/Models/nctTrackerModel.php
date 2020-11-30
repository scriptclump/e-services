<?php
//defining namespace
namespace App\Modules\NCTTracker\Models;
use Illuminate\Database\Eloquent\Model;
use App\Modules\NCTTracker\Models\nctTrackerModel;
use DB;
use Session;
use UserActivity;
use Input;


class nctTrackerModel extends Model
{
    protected $table = 'nct_transcation_tracking';
    protected $primaryKey = "nct_id";

    public function viewNctTrackerData($makeFinalSql, $orderBy, $page, $pageSize){

        $orderByStatis = '';
        if($orderBy!=''){
            $orderBy = ' ORDER BY  '. $orderBy;
        }else{
        $orderBy = ' ORDER BY collected_on desc';
        }
        $sqlWhrCls = '';
        $countLoop = 0;

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

        $nctTrackerDetails = "select * FROM (
                                SELECT * FROM (
                                SELECT *,
                                CASE
                                WHEN (amount-TotalCollected) IS NULL THEN ROUND(amount, 2) 
                                ELSE ROUND((amount-TotalCollected), 2)
                                END 'balance',
                                CASE
                                WHEN TotalCollected IS NULL AND TotalCollected_Partial IS NULL THEN '<div style=\"color:orange; font-weight:bold;\">Initiated</div>'
                                WHEN TotalCollected_Partial>0 THEN '<div style=\"color:green; font-weight:bold;\">Collected/In Progress</div>'
                                WHEN FORMAT(TotalCollected, 2) = FORMAT(amount, 2) THEN '<div style=\"color:blue; font-weight:bold;\">Fully Collected</div>'
                                WHEN TotalCollected>0 AND TotalCollected < amount THEN CONCAT('<div style=\"color:red; font-weight:bold;\">Partially Collected (',(TotalCollected),')<div>')
                                WHEN TotalCollected>0 AND TotalCollected > amount THEN CONCAT('<div style=\"color:red; font-weight:bold;\">Over Collected (', (TotalCollected), ')</div>')
                                END 'CollectionStatus',
                                CASE
                                WHEN TotalCollected IS NULL AND TotalCollected_Partial IS NULL THEN 'Initiated'
                                WHEN TotalCollected_Partial>0 THEN 'Collected/In Progress'
                                WHEN FORMAT(TotalCollected, 2) = FORMAT(amount, 2) THEN 'Fully Collected'
                                WHEN TotalCollected>0 AND TotalCollected < amount THEN 'Partially Collected'
                                WHEN TotalCollected>0 AND TotalCollected > amount THEN 'Over Collected'
                                ELSE 'Collected/No Status'
                                END 'CollectionStatus_Srch' 
                                FROM (
                                SELECT 
                                hist.*,
                                ml.master_lookup_name,
                                CONCAT(usr.firstname, ' ', usr.lastname) AS 'FullName',
                                (SELECT SUM(nct_amount) FROM nct_transcation_tracking AS nct WHERE nct.nct_history_id=hist.history_id AND nct_status  NOT IN (11903, 11906, 11908) ) AS 'TotalCollected', 
                                (SELECT SUM(nct_amount) FROM nct_transcation_tracking AS nct WHERE nct.nct_history_id=hist.history_id AND nct_status  IN(11905, 11902, 11907) ) AS 'TotalCollected_Partial',
                                (SELECT GROUP_CONCAT(CONCAT('<a href=\"salesorders/detail/',gds_order_id,'#payments\" target=\"_blank\">',order_code, '</a>') ) FROM collections AS coll WHERE coll.collection_id=hist.collection_id) AS 'OrderCode',
                                (SELECT bu_name 
                                FROM business_units AS bu 
                                INNER JOIN legalentity_warehouses AS lw ON lw.bu_id=bu.bu_id
                                INNER JOIN collections AS col ON col.le_wh_id=lw.le_wh_id
                                WHERE col.collection_id=hist.collection_id LIMIT 1) AS 'BuName'
                                FROM collection_history AS hist 
                                INNER JOIN master_lookup AS ml ON ml.value=hist.payment_mode
                                INNER JOIN users AS usr ON usr.user_id=hist.collected_by
                                WHERE hist.payment_mode!=22010 AND hist.payment_mode!=22005
                                ) AS innertbl1
                                ) AS innertbl2 
                            ) AS innertbl3". $sqlWhrCls . $orderBy;

        $allData = DB::select(DB::raw($nctTrackerDetails));
		return $allData;
    }

    public function totalNctTrackerDetails($historyid,$editNCTAcess){

        $concatQuery = "";
        if($editNCTAcess== 1){
            $concatQuery = " CONCAT('<center><code>',
                            '<a href=\"javascript:void(0)\" onclick=\"updateNCTData(',ntt.nct_id,')\">
                            <i class=\"fa fa-pencil\"></i></a>&nbsp&nbsp
                            <a href=\"javascript:void(0)\" onclick=\"viewNctData(',ntt.nct_id,')\">
                            <i class=\"fa fa-eye\"></i></a>&nbsp&nbsp

                            </code></center>') 
           AS 'CustomAction' ";
        }

        $nctdetails = "select *, ".$concatQuery." from nct_transcation_tracking as ntt 
            LEFT JOIN collection_history as ch ON ch.history_id=ntt.nct_history_id
            inner join master_lookup as ml ON ml.value=ntt.nct_status
            where history_id=$historyid";

    	$allData = DB::select(DB::raw($nctdetails));
    	return $allData;

    } 

    public function saveNctDetailsIntoDB($nctdata){

        $originalDate = $nctdata['issued_date'];
        $originalDate = str_replace('/', '-', $originalDate);
        $availableDate = date("Y-m-d", strtotime($originalDate) ); 
        
    	$this->nct_history_id = $nctdata['MaintableId'];
    	$this->nct_ref_no = $nctdata['reference_no'];
    	$this->nct_bank = $nctdata['bank_name'];
    	$this->nct_status = $nctdata['status'];
    	$this->nct_branch = $nctdata['branch_name'];
    	$this->nct_holdername = $nctdata['holder_name'];
    	$this->nct_issue_date = $availableDate;
        $this->transcation_type = 1;
        $this->proof_image = $nctdata['proof_image'];
        
    	//$this->nct_collected_by = $nctdata['user_id'];
        $this->nct_collected_by = $nctdata['collected_by'];
        $this->nct_comment = $nctdata['comment'];
    	$this->nct_amount = $nctdata['amount'];
        $this->nct_deposited_to = $nctdata['deposited_to'];
        $this->created_by = Session::get('userId');
        $this->updated_by = Session::get('userId');
    	if($this->save())
    	{
    		return $this->nct_id;
    	}else{
            return false;
        }
    }

    // Get the Ledger group as per Holder
    public function getNameAsperHoldername($tlmname){
        $group =  DB::table("tally_ledger_master")
                        ->where('tlm_name', '=', $tlmname)
                        ->first();

        if( !empty($group) ){
            return $group->tlm_group;
        }else{
            return "";
        }
    }
    //save into vouchers table
    public function saveIntoVouchersTable($historyid,$nctdata,$ledgerNameGroup, $DrCrFlag,$cost_center,$invoice_code){

        $date = str_replace('/', '-',$nctdata['issued_date']);
        $voucherdate = date('Y-m-d', strtotime($date));
        $cost_centre_group=$cost_center;

        $saveData[0] = array(
            "voucher_code"                      =>  "NCT-".$historyid,
            "voucher_type"                      =>  "Receipt",
            "voucher_date"                      =>  $voucherdate,
            "ledger_group"                      =>  "Bank Accounts",
            "ledger_account"                    =>  $nctdata['deposited_to'],
            "tran_type"                         =>  $DrCrFlag==0 ? 'Dr' : 'Cr',
            "amount"                            =>  $nctdata['amount'],
            "naration"                         =>  "Being Cheque received from " . $nctdata['holder_name'] ." as Cheque Refrence No ". $nctdata['reference_no'],
            "cost_centre"                       =>  $cost_centre_group,
            "cost_centre_group"                 =>  "Z1R1",
            "reference_no"                      =>  $invoice_code,
            "is_posted"                         =>  0
        );

        $saveData[1] = array(
            "voucher_code"                      =>  "NCT-".$historyid,
            "voucher_type"                      =>  "Receipt",
            "voucher_date"                      =>  $voucherdate,
            "ledger_group"                      =>  $ledgerNameGroup,
            "ledger_account"                    =>  $nctdata['holder_name'],
            "tran_type"                         =>  $DrCrFlag==1 ? 'Dr' : 'Cr',
            "amount"                            =>  $nctdata['amount'],
            "naration"                          =>  "Being Cheque received from " . $nctdata['holder_name'] ." as Cheque Refrence No ". $nctdata['reference_no'],
            "cost_centre"                       =>  $cost_centre_group,
            "cost_centre_group"                 =>  "Z1R1",
            "reference_no"                      =>  $invoice_code,
            "is_posted"                         =>  0
        );
        $save = DB::table("vouchers") 
            ->insert($saveData);

        return 1;   
    }

    public function getBankNameFromTalleyLedgerMaster(){

           $bankName = "select * FROM tally_ledger_master WHERE tlm_name LIKE '101%'";
           $allData = DB::select(DB::raw($bankName));
           return $allData;
    }

    public function getStatusFromMasterlookup(){
    	$groupName = DB::table("master_lookup")
         				->where('mas_cat_id','=', "119")
         				->orderby("sort_order")
         				->get()->all();
        return $groupName;
    }

    public function getUserName($term){

        $getlist = "select * 
                    from users as usr
                    inner join user_roles as ur on ur.user_id=usr.user_id
                    inner join roles as rls on rls.role_id=ur.role_id 
                    where rls.name='Delivery Executive'
                    and concat(usr.firstname, usr.lastname) like '%".$term."%'";
        $allData = DB::select(DB::raw($getlist));

    	$users_arr = array();

        foreach($allData  as $getnames) {
            $users = array("label" => $getnames->firstname,"lastname" => $getnames->lastname,"user_id" => $getnames->user_id);
            array_push($users_arr, $users); 
        }

        return $users_arr;
    }

    // ifsc codes
    public function getIfsclist($term){

        $getifsclist = "select * from bank_info where  ifsc like '%$term%' limit 20";
        $allData = DB::select(DB::raw($getifsclist));

        $ifsc_arr = array();
        foreach($allData  as $getnames) {
            $ifscs = array("label" => $getnames->ifsc,"ifsc" => $getnames->ifsc,"branch" => $getnames->branch,"bank_name" => $getnames->bank_name);
            array_push($ifsc_arr, $ifscs); 
        }
      
        return $ifsc_arr;
    }

    // get deposited to
    public function getDepositeTypes($option){
        $likeQuery = "";
        if($option == 11907){
            $likeQuery = "AND tlm_name like '%Cash%'";
        }
        $getDepositeTypes = "select * FROM tally_ledger_master WHERE tlm_name LIKE '101%' ".$likeQuery;
        $allData = DB::select(DB::raw($getDepositeTypes));
        $options="";
        $options .= "<option value=''></option>";
        foreach($allData  as $getoptions) {
            $options .= "<option value='".$getoptions->tlm_name."'>$getoptions->tlm_name</option>";
        }
        return $options;
    }
    
    // this code is not valiod after some time will remove 
    public function getHolderName($term){

        $getlist = "select * from tally_ledger_master 
                    WHERE tlm_group IN ('Sundry Debtors','Sundry Creditors')
                    AND tlm_name like '%".$term."%' ORDER BY tlm_name";

        $allData = DB::select(DB::raw($getlist));

        $users_arr = array();

        foreach($allData  as $getnames) {
            $users = array("label" => $getnames->tlm_name,"tlm_name" => $getnames->tlm_name);
            array_push($users_arr, $users); 
        }
        return $users_arr;
    }

    public function getNameLedgerNames(){

        $getlist = "select * from tally_ledger_master 
                    WHERE tlm_group IN ('Sundry Debtors','Sundry Creditors')
                    ORDER BY tlm_name";

        $allData = DB::select(DB::raw($getlist));

        return $allData;

    }
    

    public function getNctDetails($nctid){
    	$getdetails = DB::table("nct_transcation_tracking")
                        ->select("*", DB::raw("date_format(nct_issue_date, '%d/%m/%Y') as 'nct_issue_date_format' "), DB::raw("date_format(nct_issue_date, '%Y-%m-%d') as 'nct_issue_date_format_fordate' "))
         				->where('nct_id','=', $nctid)
         				->get()->all();	
		
        return $getdetails;
    }

    public function getNctTrackerHistoryDetails($nctid){
        $history = "select DATE_FORMAT(ncthist.hist_date, '%d/%m/%Y') AS `hist_date`, ncttrack.nct_history_id,
                    (select ml.master_lookup_name from master_lookup ml where ml.value=ncthist.`prev_status`) as 'PreviousStatus',
                    (select ml.master_lookup_name from master_lookup ml where ml.value=ncthist.`current_status`) as 'CurrentStaus',
                    ncthist.comment,ncthist.extra_charges,
                    ncttrack.`nct_amount`, ncttrack.`nct_collected_by`,
                    (SELECT CONCAT( u.firstname, ' ', u.lastname ) FROM users AS u WHERE u.user_id=ncttrack.`nct_collected_by` ) AS 'CollectedByID',
                    ncttrack.`nct_holdername`, ncttrack.`nct_bank`, ncthist.`nct_ref_no`,
                    ncthist.`changed_by`, ncthist.`changed_by` AS 'ChangedByName', ncttrack.`proof_image` AS 'NctHistProof',
                    ncthist.`created_by`, (SELECT CONCAT( u.firstname, ' ', u.lastname ) FROM users AS u WHERE u.user_id=ncthist.`created_by` ) AS 'RecordAddedByName'
                    FROM nct_transcation_history AS ncthist
                    INNER JOIN nct_transcation_tracking AS ncttrack ON ncttrack.`nct_id`=ncthist.`nct_id`
                    WHERE ncthist.nct_id=$nctid";

        $allData = DB::select(DB::raw($history));

        return $allData;
    }
    //
    public function updateMainTableNctData($nctid,$nctdata){

        $originalDate = isset($nctdata['issued_date']) ? $nctdata['issued_date'] : $nctdata['nct_issue_date'];
        $originalDate = str_replace('/', '-', $originalDate);
        $availableDate = date("Y-m-d", strtotime($originalDate) );
    
        $update = DB::table('nct_transcation_tracking')
                    ->where('nct_id', '=', $nctid )
                    ->update(['nct_ref_no' => $nctdata['reference_no'], 
                            'nct_bank' => $nctdata['bank_name'], 
                            'nct_status' => $nctdata['status'], 
                            'nct_branch' => $nctdata['branch_name'], 
                            'nct_holdername' => $nctdata['holder_name'],
                            'nct_issue_date' => $availableDate,
                            'nct_collected_by' => $nctdata['collected_by'],
                            'updated_by' => $nctdata['updated_by'],
                            /*'nct_collected_by'  =>$nctdata['collected_by_id'],*/
                            'nct_amount' => $nctdata['amount']]);

        return $update;
    }

    public function getMainTableNctId($nctid){

        $sqlData = DB::table("nct_transcation_tracking")
                        ->where("nct_history_id","=",$nctid)
                        ->get()->all();

       return $sqlData[0]->nct_id;

    }

    public function getNCTDataFromDB($tblFlag, $nctID){

        $sqlData = "";
        if($tblFlag==0){  
            $sqlData = DB::table("collection_history as hist")
                        ->select("hist.*", DB::raw("concat(usr.firstname, ' ', usr.lastname) as UserName"))
                        ->join("users as usr", "usr.user_id", "=", "hist.collected_by" )
                        ->where("hist.history_id","=",$nctID)
                        ->first();
        }else{

            $sqlData = DB::table("nct_transcation_tracking as ntt")
                        ->select("ntt.*", DB::raw("nct_collected_by as UserName"))
                        //->join("users as usr", "usr.user_id", "=", "ntt.nct_collected_by" )
                        ->where("ntt.nct_history_id","=",$nctID)
                        ->first();
        }
          
       return $sqlData;
    }

    public function getDatafromCollectionHistory($historyID){
        $collectionData = DB::table("collection_history")
                        ->where("history_id", "=", $historyID)
                        ->get()->all();
        return $collectionData;
    }

    public function getDatafromNCTByID($historyID){
        $collectionData = DB::table("nct_transcation_tracking")
                        ->where("nct_history_id", "=", $historyID)
                        ->get()->all();
        return $collectionData;
    }
    public function CheckRefNoExist($refno,$MaintableId){
        $refnoCount = "select count(*) AS re_count FROM nct_transcation_tracking WHERE nct_ref_no='$refno' AND nct_history_id = '$MaintableId'";
        $Count = DB::select(DB::raw($refnoCount));
        return $Count[0]->re_count;
    }
    public function getCollInvoiceCode($historyid){
        $invcode = "select  
                    cl.invoice_code ,
                    (SELECT CONCAT(le.`business_legal_name`, '-', le.`le_code`) FROM legal_entities AS le WHERE le.`legal_entity_id`=cl.`customer_id`) AS 'TallyLedger', 
                    bu.`cost_center`
                    FROM collection_history AS ch 
                    INNER JOIN collections AS cl ON ch.`collection_id` = cl.`collection_id`
                    INNER JOIN gds_orders AS od ON od.`gds_order_id`=cl.`gds_order_id`
                    INNER JOIN legalentity_warehouses AS lw ON lw.`le_wh_id`=od.`hub_id`
                    INNER JOIN business_units AS bu ON bu.`bu_id`=lw.`bu_id`
                    AND ch.`history_id`=".$historyid;
        $invdata = DB::select(DB::raw($invcode));

        return $invdata;
    }
}