<?php
//defining namespace
namespace App\Modules\ExpensesTracker\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use UserActivity;

class directexpensesModel extends Model
{
    protected $table = 'expenses_main';
    protected $primaryKey = "exp_id";

    // get direct expenses data
    public function showdirectexpensesDetails($makeFinalSql, $orderBy, $page, $pageSize){
        $legalid = Session::get('legal_entity_id');
        if($orderBy!=''){
            $orderBy = ' ORDER BY ' . $orderBy;
        }else{
            $orderBy = ' ORDER BY SubmittedBy DESC';
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

        $sqlWhrCls = str_replace("BuName", "CONVERT(BuName USING utf8)", $sqlWhrCls);

        $expensesDetails = "select COUNT(*) AS 'cnt' FROM ( SELECT *,AdvanceTotalAmount-RemTotalAmount AS 'balance' FROM (SELECT *, SUM(AdvanceAmount) AS 'AdvanceTotalAmount', SUM(RemAmount) AS 'RemTotalAmount' FROM (
        SELECT IF(main.exp_req_type = 122001,main.exp_actual_amount,0) AS 'AdvanceAmount',
        IF(main.exp_req_type = 122002,main.exp_actual_amount,0) AS 'RemAmount',
        CONCAT(usr.firstname, ' ', usr.lastname) AS 'SubmittedBy',
        (SELECT CONCAT(bu.bu_name, ' (', bu.cost_center, ')') FROM business_units AS bu WHERE bu.bu_id=usr.`business_unit_id` ) AS 'BuName',
        usr.`user_id`
        FROM expenses_main AS main 
        INNER JOIN users AS usr ON usr.user_id=main.submited_by_id 
        WHERE main.exp_appr_status IN (0,1) and main.legal_entity_id='".$legalid."'
        ) AS innertbl GROUP BY user_id) AS innertbl2 ) AS innrtbl3 ". $sqlWhrCls;
        $allData = DB::select(DB::raw($expensesDetails));
        $TotalRecordsCount = $allData[0]->cnt;


        $expensesDetails = "select * FROM (SELECT *,AdvanceTotalAmount-RemTotalAmount AS 'balance' FROM (SELECT *, SUM(AdvanceAmount) AS 'AdvanceTotalAmount',SUM(RemAmount) AS 'RemTotalAmount' FROM (
    SELECT IF(main.exp_req_type = 122001,main.exp_actual_amount,0) AS 'AdvanceAmount',
    IF(main.exp_req_type = 122002,main.exp_actual_amount,0) AS 'RemAmount',
    CONCAT(usr.firstname, ' ', usr.lastname) AS 'SubmittedBy',
    (SELECT CONCAT(bu.bu_name, ' (', bu.cost_center, ')') FROM business_units AS bu WHERE bu.bu_id=usr.`business_unit_id` ) AS 'BuName',
    usr.`user_id`,CASE main.exp_appr_status WHEN 1 THEN 'Approved'ELSE (SELECT ml.master_lookup_name FROM master_lookup AS ml WHERE ml.value = main.exp_appr_status) END 'ApprovalStatus',CONCAT('<center><code>
    <a href=\"javascript:void(0)\" onclick=\"directexpensesdata(',submited_by_id,')\">
    <i class=\"fa fa-eye\"></i></a></code></center>') AS 'actions'
    FROM expenses_main AS main 
    INNER JOIN users AS usr ON usr.user_id=main.submited_by_id 
    WHERE main.exp_appr_status IN (0,1) and main.legal_entity_id='".$legalid."'
) AS innertbl GROUP BY user_id) AS innertbl2 ) AS innertbl3". $sqlWhrCls . $orderBy;

        $pageLimit = '';
        if($page!='' && $pageSize!=''){
            $pageLimit = " LIMIT " . (int)($page*$pageSize) . ", " . $pageSize;
        }

        $expensesDetails = DB::select(DB::raw($expensesDetails . $pageLimit));
        return json_encode(array('results'=>$expensesDetails, 'TotalRecordsCount'=>(int)($TotalRecordsCount))); 
    }
    public function getDirectExpenses($submited_by_id){

        $expensesdataNew = "select IF(main.exp_req_type = 122001,main.exp_actual_amount,0) AS 'AdvanceAmount',DATE_FORMAT(main.submit_date, '%d-%m-%Y') AS 'ExpSubmittedDate',
                            IF(main.exp_req_type = 122002,main.exp_actual_amount,0) AS 'RemAmount',main.submited_by_id ,main.`exp_subject`,main.`exp_code`,main.`is_direct_advance`,main.`exp_req_type`,
                            CONCAT(usr.firstname, ' ', usr.lastname) AS 'SubmittedByName',
                            (SELECT 
                            master_lookup_name 
                          FROM
                            master_lookup AS m 
                          WHERE m.value = main.exp_req_type) AS 'RequestFor',
                            (SELECT CONCAT(bu.bu_name, ' (', bu.cost_center, ')') FROM business_units AS bu WHERE bu.bu_id=usr.`business_unit_id` ) AS 'BuName',
                            usr.`user_id`
                            FROM expenses_main AS main 
                            INNER JOIN users AS usr ON usr.user_id=main.submited_by_id 
                            WHERE main.exp_appr_status IN (0,1) AND  submited_by_id = ".$submited_by_id."  ORDER BY ExpSubmittedDate";                       
        $allDataNew = DB::select(DB::raw($expensesdataNew));
        return $allDataNew;

    }
    
    public function getTotalsDb(){
         $legalid = Session::get('legal_entity_id');
        $query = "select ifnull(SUM(AdvanceAmount),0) AS 'AdvanceTotalAmount', ifnull(SUM(RemAmount),0) AS 'RemTotalAmount' FROM (
                    SELECT IF(main.exp_req_type = 122001,main.exp_actual_amount,0) AS 'AdvanceAmount',
                    IF(main.exp_req_type = 122002,main.exp_actual_amount,0) AS 'RemAmount'
                    FROM expenses_main AS main 
                    WHERE main.exp_appr_status IN (0,1) and main.legal_entity_id='".$legalid."'
                ) AS innertbl";
                       
        $allData = DB::select(DB::raw($query));
        
        return $allData;
    }

 

}